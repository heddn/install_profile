<?php // phpcs:ignore Drupal.Commenting.FileComment.Missing

declare(strict_types=1);

/**
 * @file
 * Profile containing customizations.
 */

use Drupal\Core\Site\Settings;
use Drupal\install_profile\InstallHelper;

/**
 * @return array<string, array>
 */
function install_profile_install_tasks(&$install_state): array {
  $tasks = [];
  $tasks['install_profile_finish_installation'] = [
    'display_name' => t('Install default content'),
  ];
  return $tasks;
}

/**
 * Finish installation process.
 *
 * @param array $install_state
 *   The install state.
 *
 * @throws \Drupal\Core\Entity\EntityStorageException
 */
function install_profile_finish_installation(array &$install_state): void {
  // Use \Drupal::service() because module install rebuilds the container.
  \Drupal::service('module_installer')->install(['default_content']);
  \Drupal::service('default_content.importer')->importContent('custom_default_content');
  \Drupal::service('module_installer')->uninstall(['serialization', 'hal', 'default_content']);
}
