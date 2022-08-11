const fs = require("fs")
const postcss = require("postcss")
const selectorParser = require("postcss-selector-parser")
const {
  promisify
} = require("util")
const {
  createHash,
} = require('crypto')

const asyncFs = {
  access: promisify(fs.access),
  readFile: promisify(fs.readFile),
  writeFile: promisify(fs.writeFile),
};

const ALWAYS = '.keep\\:always';

function isInPseudoClass(selector) {
  return (
    (selector.parent &&
      selector.parent.type === "pseudo" &&
      selector.parent.value.startsWith(":")) ||
    false
  );
}

function findSelectors(selector) {
  // ignore the selector if it is inside a pseudo class
  if (isInPseudoClass(selector)) {
    return [];
  }

  let matches = [];

  for (const selectorNode of selector.nodes) {
    switch (selectorNode.type) {
      case "attribute":
        break;
      case "class":
        let className = selectorNode.toString();
        // Ignore .group catch all class.
        if (className != '.group') {
          matches.push(className);
        }
        break;
      case "id":
      case "tag":
        break;
      default:
        continue;
    }
  }

  return matches;
}

async function process() {
  let rules = {};
  let selectors = {};

  let content = await asyncFs.readFile("./build/app--tailwind-utilities.css", "utf-8")
  let root = postcss.parse(content)
  let atrule = '';

  root.walk((node) => {
    // Store atrule for later use.
    if (node.type == 'atrule') {
      let nodes = node.nodes;
      node.nodes = [];
      atrule = node.toString();
      node.nodes = nodes;
    }

    // exit if it is not a rule
    if (node.type != "rule" || !node.selector) {
      return;
    }

    let rule = node.toString();
    let ruleHash = createHash('sha1').update(rule).digest('hex');

    let item = {};
    item['rule'] = rule;

    // Check again that this has an atrule.
    if (node.parent && node.parent.type == 'atrule') {
      // @todo Find something more efficient.
      let nodes = node.parent.nodes;
      node.parent.nodes = [];
      atrule = node.parent.toString();
      node.parent.nodes = nodes;

      item['atrule'] = atrule;
    }

    rules[ruleHash] = item;

    selectorParser((selectorsParsed) => {
      selectorsParsed.walk((selector) => {
        if (selector.type !== "selector") {
          return;
        }

        if (selector.toString() == '[hidden]') {
          return;
        }

        // Find selector matches
        let matches = findSelectors(selector);
        if (matches.length == 0) {
          matches.push(ALWAYS);
        }

        // Should not happen anymore now that .group is handled.
        delete item['and'];
        if (matches.length > 1) {
          item['and'] = matches;
          matches = [matches[0]];
        }

        // @todo matches.length == 1 here.
        for (key in matches) {
          let match = matches[key]

          if (!(match in selectors)) {
            selectors[match] = [];
          }

          selectors[match].push(item);
        }
      });
    }).processSync(node.selector);
  });

  let keys = [];
  for (key in selectors) {
    keys.push(key);
  }

  await asyncFs.writeFile("./build/app--tailwind-utilities.json", JSON.stringify(selectors));
  await asyncFs.writeFile("./build/app--tailwind-utilities--keys.json", JSON.stringify(keys));
}

process();
