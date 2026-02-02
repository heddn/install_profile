<?php

declare(strict_types=1);

namespace Drupal\Tests\custom_default_content\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\node\Kernel\NodeAccessTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Test description.
 */
#[Group('custom_default_content')]
final class BasicTest extends NodeAccessTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'default_content',
    'file',
    'serialization',
    'taxonomy',
  ];

  public function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('taxonomy_term');
    $this->installEntitySchema('file');
    $this->installSchema('file', ['file_usage']);
  }

  /**
   * Test default creates successfully.
   */
  public function testContentCreation(): void {
    $this->container->get('default_content.importer')->importContent('custom_default_content');
    $not_found_node = Node::load(1);
    self::assertEquals('Oops! Not found', $not_found_node->label());
    $frontpage_node = Node::load(2);
    self::assertEquals('Frontpage', $frontpage_node->label());
  }

}
