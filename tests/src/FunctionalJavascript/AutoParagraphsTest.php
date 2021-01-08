<?php

namespace Drupal\Tests\auto_paragraphs\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;
use Drupal\Tests\paragraphs\FunctionalJavascript\LoginAdminTrait;
use Drupal\Tests\paragraphs\FunctionalJavascript\ParagraphsTestBaseTrait;
use Drupal\Tests\paragraphs\Traits\ParagraphsCoreVersionUiTestTrait;

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
  public static $modules = [
    'node',
    'paragraphs_test',
    'paragraphs',
    'field',
    'field_ui',
    'block',
    'link',
    'auto_paragraphs'
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
      'administer content types',
      'administer node form display',
      'edit any paragraphed_test content',
      'create paragraphed_test content',
    ]);

    $page = $this->getSession()->getPage();

    // Add a Paragraph type.
    $paragraph_type = 'detail';
    $this->addParagraphsType($paragraph_type);

    // Add a text field to the detail type.
    $this->drupalGet('admin/structure/paragraphs_type/' . $paragraph_type . '/fields/add-field');
    $page->selectFieldOption('new_storage_type', 'text_long');
    $page->fillField('label', 'Text');
    $this->assertSession()->waitForElementVisible('css', '#edit-name-machine-name-suffix .link');
    $page->pressButton('Edit');
    $page->fillField('field_name', 'text');
    $page->pressButton('Save and continue');

    $this->drupalGet('node/add/paragraphed_test');

    // Inject paragraphs
    $page->pressButton('Inject Paragraphs');

    $this->assertSession()->assertWaitOnAjaxRequest();
  }
}
