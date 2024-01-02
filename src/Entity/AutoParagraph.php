<?php

namespace Drupal\auto_paragraphs\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Example entity.
 *
 * @ConfigEntityType(
 *   id = "auto_paragraph",
 *   label = @Translation("Auto Paragraph"),
 *   handlers = {
 *     "list_builder" = "Drupal\auto_paragraphs\Controller\AutoParagraphListBuilder",
 *     "form" = {
 *       "add" = "Drupal\auto_paragraphs\Form\AutoParagraphForm",
 *       "edit" = "Drupal\auto_paragraphs\Form\AutoParagraphForm",
 *       "delete" = "Drupal\auto_paragraphs\Form\AutoParagraphDeleteForm",
 *     }
 *   },
 *   config_prefix = "auto_paragraph",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "content_type" = "content_type",
 *     "field" = "field",
 *     "options" = "options",
 *     "paragraph_field" = "paragraph_field",
 *     "paragraphs" = "paragraphs",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "content_type",
 *     "field",
 *     "options",
 *     "paragraph_field",
 *     "paragraphs",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/system/auto_paragraphs/{auto_paragraph}",
 *     "delete-form" = "/admin/config/system/auto_paragraphs/{auto_paragraph}/delete",
 *   }
 * )
 */
class AutoParagraph extends ConfigEntityBase implements AutoParagraphInterface {

  /**
   * The AutoParagraph ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The AutoParagraph label.
   *
   * @var string
   */
  protected $label;

  /**
   * The content type.
   *
   * @var string
   */
  protected $content_type;

  /**
   * The field type.
   *
   * @var string
   */
  protected $field;

  /**
   * The options.
   *
   * @var array
   */
  protected $options;

  /**
   * The paragraph field.
   *
   * @var string
   */
  protected $paragraph_field;

  /**
   * The paragraphs.
   *
   * @var array
   */
  protected $paragraphs;

  /**
   * Get the ID.
   *
   * @return string
   *   The ID.
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * Set the ID.
   *
   * @param string $id
   *   The ID.
   */
  public function setId(string $id): void {
    $this->id = $id;
  }

  /**
   * Get the label.
   *
   * @return string
   *   The label.
   */
  public function getLabel(): string {
    return $this->label;
  }

  /**
   * Set the lavel.
   *
   * @param string $label
   *   The label.
   */
  public function setLabel(string $label): void {
    $this->label = $label;
  }

  /**
   * Get the content type.
   *
   * @return string
   *   The content type.
   */
  public function getContentType() {
    return $this->content_type;
  }

  /**
   * Set the content type.
   *
   * @param string $content_type
   *   The content type.
   */
  public function setContentType($content_type): void {
    $this->content_type = $content_type;
  }

  /**
   * Get the field.
   *
   * @return mixed
   *   The field.
   */
  public function getField() {
    return $this->field;
  }

  /**
   * Set the field.
   *
   * @param mixed $field
   *   The field.
   */
  public function setField($field): void {
    $this->field = $field;
  }

  /**
   * Get the options.
   *
   * @return mixed
   *   The options.
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Set the options.
   *
   * @param mixed $options
   *   The options.
   */
  public function setOptions($options): void {
    $this->options = $options;
  }

  /**
   * Get the paragraph field.
   *
   * @return string
   *   The paragraph field
   */
  public function getParagraphField() {
    return $this->paragraph_field;
  }

  /**
   * Set the paragraph field.
   *
   * @param string $paragraph_field
   *   The paragraph field.
   */
  public function setParagraphField($paragraph_field): void {
    $this->paragraph_field = $paragraph_field;
  }

  /**
   * Get the paragraphs.
   *
   * @return array
   *   The paragraphs.
   */
  public function getParagraphs() {
    return $this->paragraphs;
  }

  /**
   * Set the paragraphs.
   *
   * @param array $paragraphs
   *   The paragraphs.
   */
  public function setParagraphs($paragraphs): void {
    $this->paragraphs = $paragraphs;
  }

}
