// Generates a copy of our Tailwind config that can be used in the
// browser with the Tailwind CDN.

const fs = require("fs");
const prettier = require("prettier");
const path = require("path");
// Bring in the Tailwind config.
const { theme } = require("../tailwind.config.js");

const themeConfig = JSON.stringify(theme)

const js = `
tailwind.config  = {
  corePlugins: { preflight: false },
  safelist: [],
  theme: ${themeConfig}
}
`;

try {
  // write the file to src/theme.js after
  // having prettier format the string for us
  fs.writeFileSync(
    path.resolve(process.cwd(), "./build/tailwind-config-cdn.js"),
    prettier.format(js, { parser: "babel" }),
    "utf-8"
  );
} catch (err) {
  // uh-oh, something happened here!
  console.log(err.message);
}
