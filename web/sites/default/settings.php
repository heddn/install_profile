<?php

$settings['config_exclude_modules'] = [
  'devel_entity_updates',
  'devel_generate',
  'stage_file_proxy',
  'default_content',
  'hal',
  'default_content',
  'serialization',
  'config_inspector',
  'devel',
];

// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}

// Set config sync directory.
$settings['config_sync_directory'] = '../config/sync';

if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
