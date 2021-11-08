const fs = require("fs")
const {
  promisify
} = require("util")

const asyncFs = {
  access: promisify(fs.access),
  readFile: promisify(fs.readFile),
  writeFile: promisify(fs.writeFile),
};

async function splitFile() {
  let content = await asyncFs.readFile("./build/app.css", "utf-8")

  let data = content.split('/* --- TAILWIND-UTILITIES-DRUPAL-SPLIT */');
  await asyncFs.writeFile('./build/app--pre-utilities.css', data[0]);
  await asyncFs.writeFile('./build/app--tailwind-utilities.css', data[1]);
  await asyncFs.writeFile('./build/app--post-utilities.css', data[2]);
}

splitFile()
