#!/bin/bash

postcss --verbose -o ./build/app.css ./css/app.css
node scripts/split-tailwind-drupal.js
