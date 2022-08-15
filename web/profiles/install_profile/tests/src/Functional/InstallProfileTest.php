<?php

declare(strict_types=1);

namespace Drupal\Tests\install_profile\Functional;

use Drupal\Core\Site\Settings;
use Drupal\dblog\Controller\DbLogController;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\install_profile\Traits\ConfigInstallTestTrait;
use Drupal\Tests\install_profile\Traits\ProfileTestTrait;
use Drupal\Tests\RequirementsPageTrait;

/**
 * Tests Install Profile installation profile expectations.
 *
 * @group minimal
 */
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
   *
   * Strict schema validation is moved into assertInstalledConfig().
   */
  protected $strictConfigSchema = FALSE;

  /**
   * Tests installing the site.
   */
  public function testInstall(): void {
    $assert = $this->assertSession();

    // Ensure there are no errors on config import.
    $messages = [];
    $dblog_controller = DbLogController::create($this->container);
    $query = \Drupal::database()->select('watchdog')
      ->fields('watchdog')
      ->condition('type', 'config_sync')
      ->execute();
    while ($error = $query->fetchObject()) {
      $messages[] = (string) $dblog_controller->formatMessage($error);
    }
    self::assertSame([], \array_unique($messages), 'The config import during installation proceeded without error');

    $this->drupalGet('');
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
