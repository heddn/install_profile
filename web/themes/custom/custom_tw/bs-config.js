
/*
 |--------------------------------------------------------------------------
 | Browser-sync config file
 |--------------------------------------------------------------------------
 |
 | For up-to-date information about the options:
 |   http://www.browsersync.io/docs/options/
 |
 | There are more options than you see here, these are just the ones that are
 | set internally. See the website for more info.
 |
 |
 */
module.exports = {
  files: [
    'css/**/*',
    'js/*',
    'custom_tw.info.yml',
    'custom_tw.theme',
    'templates/**/*',
    'src/**/*',
    'images/**/*',
    '../../../modules/custom/**/*',
  ],
  proxy: process.env.BS_PROXY ?? 'localhost'
};
