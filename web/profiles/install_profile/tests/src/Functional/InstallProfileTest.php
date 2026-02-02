<?php

declare(strict_types=1);

namespace Drupal\Tests\install_profile\Functional;

use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Site\Settings;
use Drupal\dblog\Controller\DbLogController;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\install_profile\Traits\ConfigInstallTestTrait;
use Drupal\Tests\install_profile\Traits\ProfileTestTrait;
use Drupal\Tests\RequirementsPageTrait;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests Install Profile installation profile expectations.
 */
#[Group('install_profile')]
final class InstallProfileTest extends BrowserTestBase {
  use ConfigInstallTestTrait;
  use ProfileTestTrait;
  use RequirementsPageTrait;

  /**
   * {@inheritdoc}
   */
  protected $profile = 'install_profile';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'custom_tw';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'default_content',
    'custom_default_content',
  ];

  /**
   * {@inheritdoc}
   *
   * Strict schema validation is moved into assertInstalledConfig().
   */
  // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * Tests installing the site.
   */
  public function testInstall(): void {
    $this->container->get('module_installer')->uninstall(['custom_default_content', 'default_content']);
    $assert = $this->assertSession();

    // Ensure there are no errors on config import.
    $messages = [];
    $dblog_controller = DbLogController::create($this->container);
    $query = \Drupal::database()->select('watchdog')
      ->fields('watchdog')
      ->condition('type', 'config_sync')
      ->execute();
    \assert($query instanceof StatementInterface);
    while ($error = $query->fetchObject()) {
      $messages[] = (string) $dblog_controller->formatMessage($error);
    }
    self::assertSame([], \array_unique($messages), 'The config import during installation proceeded without error');

    $this->drupalGet('<front>');
    $assert->statusCodeEquals(200);

    $this->assertInstalledConfig($this->defaultSkippedConfig());

    // Disable override mode.
    \Drupal::configFactory()->getEditable('component_library.override_mode.settings')
      ->set('override_mode', FALSE)
      ->save();

    // Test that default content is created.
    $admin = $this->createUser([], NULL, TRUE);
    $admin->addRole('administrator');
    $admin->save();
    $this->drupalLogin($admin);
    $this->drupalGet('admin/content', [
      'query' => [
        'title' => 'Frontpage',
        'type' => 'article',
        'status' => 'All',
      ],
    ]);
    $assert->pageTextContains('Frontpage');
    $this->drupalGet('admin/content', [
      'query' => [
        'title' => 'Oops! Not found',
        'type' => 'article',
        'status' => 'All',
      ],
    ]);
    $assert->pageTextContains('Oops! Not found');
    $this->drupalGet('foo');
    $assert->statusCodeEquals(404);
    $assert->pageTextContains('Page not found');
  }

  /**
   * @return array<string, mixed>
   */
  protected function installParameters(): array {
    $parameters = parent::installParameters();
    $parameters['parameters']['existing_config'] = Settings::get('config_sync_directory', '../config/sync');
    return $parameters;
  }

}
