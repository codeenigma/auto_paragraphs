<?php

namespace Drupal\Tests\auto_paragraphs\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;
use Drupal\Tests\paragraphs\FunctionalJavascript\LoginAdminTrait;
use Drupal\Tests\paragraphs\FunctionalJavascript\ParagraphsTestBaseTrait;
use Drupal\Tests\paragraphs\Traits\ParagraphsCoreVersionUiTestTrait;

/**
 * Web tests for the auto_paragraphs module.
 *
 * @group auto_paragraphs
 */
class AutoParagraphsTest extends WebDriverTestBase {
  use LoginAdminTrait;
  use FieldUiTestTrait;
  use ParagraphsTestBaseTrait;
  use ParagraphsCoreVersionUiTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'paragraphs_test',
    'paragraphs',
    'field',
    'field_ui',
    'block',
    'link',
    'auto_paragraphs',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests the auto paragraphs button.
   */
  public function testAddWidgetButton() {
    $this->addParagraphedContentType('paragraphed_test');
    $this->loginAsAdmin([
      'edit any paragraphed_test content',
      'create paragraphed_test content',
    ]);

    $page = $this->getSession()->getPage();

    // Add a Paragraph type.
    $paragraph_type = 'detail';
    $this->addParagraphsType($paragraph_type);

    $this->drupalGet('node/add/paragraphed_test');

    // Inject paragraphs.
    $page->pressButton('Inject Paragraphs');

    $this->assertSession()->assertWaitOnAjaxRequest();
  }

}
