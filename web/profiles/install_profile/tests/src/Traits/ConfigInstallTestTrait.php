<?php

declare(strict_types=1);

namespace Drupal\Tests\install_profile\Traits;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\Schema\SchemaCheckTrait;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Site\Settings;
use Drupal\KernelTests\AssertConfigTrait;

/**
 * Configuration install test trait.
 */
trait ConfigInstallTestTrait {
  use AssertConfigTrait;
  use SchemaCheckTrait;

  /**
   * Ensures that all the installed config looks like the exported one.
   *
   * @param array $skipped_config
   *   An array of skipped config.
   */
  private function assertInstalledConfig(array $skipped_config): void {
    /** @var \Drupal\Core\Config\StorageInterface $active_config_storage */
    $active_config_storage = $this->container->get('config.storage');
    /** @var \Drupal\Core\Config\ConfigManagerInterface $config_manager */
    $config_manager = $this->container->get('config.manager');

    $default_install_path = Settings::get('config_sync_directory');
    $profile_config_storage = new FileStorage($default_install_path, StorageInterface::DEFAULT_COLLECTION);

    foreach ($profile_config_storage->listAll() as $config_name) {
      $result = $config_manager->diff($profile_config_storage, $active_config_storage, $config_name);
      try {
        $this->assertConfigDiff($result, $config_name, $skipped_config);
      }
      catch (\Exception $e) {
        self::fail($e->getMessage());
      }
    }

    // Moves schema validation later in test so all modules are enabled before
    // validating. Solves race conditions where sub-modules add config to parent
    // and schema is momentarily not valid because sub module is not enabled
    // until later in the install process.
    foreach ($active_config_storage->listAll() as $name) {
      $config_data = \Drupal::configFactory()->get($name)->get();
      $result = $this->checkConfigSchema(\Drupal::service('config.typed'), $name, $config_data);
      if (\is_array($result)) {
        self::fail(\print_r($result, TRUE));
      }
    }
  }

  /**
   * Get default skipped configuration.
   *
   * @return string[][]
   *   The config keys to skip.
   */
  private function defaultSkippedConfig(): array {
    return [
      // The system.date is changed configuration because the test system
      // changes it to test date edge cases.
      'system.date' => [
        'Australia/Sydney',
      ],
      // The system.logging is changed configuration because the test system
      // changes it to be verbose.
      'system.logging' => [
        'error_level: verbose',
      ],
      // The system.mail is changed configuration because the test system
      // changes it to ensure that mails are not sent.
      'system.mail' => [
        'test_mail_collector',
      ],
      // TODO: work out why this has changed but it is not particularly
      // important.
      'system.performance' => [
        'preprocess: false',
      ],
    ];
  }

}
