<?php

declare(strict_types=1);

namespace Drupal\Tests\install_profile\Traits;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Command\DbDumpCommand;
use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Setup for profile testing.
 */
trait ProfileTestTrait {

  /**
   * Installs profile from db dump if available.
   *
   * @see: https://gist.github.com/alexpott/8cd82bc75b88c01489d837cf8432451b
   * @see: https://www.drupal.org/project/drupal/issues/2900208
   */
  protected function doInstall(): void {
    $path_to_db = \array_key_exists('BROWSERTEST_DB_DUMP_PATH', $_ENV) ? $_ENV['BROWSERTEST_DB_DUMP_PATH'] : FALSE;

    if ($path_to_db && \strpos($path_to_db, 'COMMIT-HASH') !== FALSE) {
      // @phpstan-ignore-next-line
      $rev = @\exec('git rev-parse --short HEAD') ?: 'unknown-hash';
      $path_to_db = \str_replace('COMMIT-HASH', $rev, $path_to_db);
    }

    // Load from DB dump if available.
    if ($path_to_db && \file_exists($path_to_db)) {
      // Generate a hash salt.
      $settings = [];
      $settings['settings']['hash_salt'] = (object) [
        'value' => Crypt::randomBytesBase64(55),
        'required' => TRUE,
      ];

      // Since the installer isn't run, add the database settings here too.
      $settings['databases']['default'] = (object) [
        'value' => Database::getConnectionInfo(),
        'required' => TRUE,
      ];
      $this->writeSettings($settings);
      \mkdir($this->tempFilesDirectory);
      require $path_to_db;
    }
    else {
      parent::doInstall();
      if ($path_to_db) {
        // Create DB dump.
        $command = new DbDumpCommand();
        $command_tester = new CommandTester($command);
        $command_tester->execute([]);
        \file_put_contents($path_to_db, $command_tester->getDisplay());
      }
    }
  }

  /**
   * Resets and rebuilds the environment after setup.
   */
  protected function rebuildAll(): void {
    parent::rebuildAll();
    // Ensure the root user is valid.
    $password = $this->randomMachineName();
    $this->rootUser = User::load(1);
    $this->rootUser->setPassword($password);
    $this->rootUser->passRaw = $password;
    $this->rootUser->pass_raw = $password;
    $this->rootUser->save();
  }

}
