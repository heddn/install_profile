DESCRIPTION
-----------
Custom TaiWind-based theme for the site.

USAGE
-----
This project uses tailwindcss.

It was tested on node v14.x.x.

INSTALLATION
------------
```
yarn install
```

BUILD COMMANDS
--------------
- Use `yarn start` to generate CSS for local development. The CSS will
  be re-compiled automatically when changes are made.
- Use `yarn build-prod` to generate CSS optimized for production site.

OPTIMISING FOR PRODUCTION
-------------------------

The theme's `tailwind.config.js` file contains a section on adding PurgeCSS to
the build to remove any unused classes and reduce the generated file size.

If you add any classes programmatically, such as within a preprocess function,
it can be whitelisted using `whitelistPatterns` or `whitelistPatternsChildren`.
