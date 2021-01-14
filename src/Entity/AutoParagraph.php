<?php

namespace Drupal\auto_paragraphs\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\auto_paragraphs\Entity\AutoParagraphInterface;

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
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * @param string $id
   */
  public function setId(string $id): void
  {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getLabel(): string
  {
    return $this->label;
  }

  /**
   * @param string $label
   */
  public function setLabel(string $label): void
  {
    $this->label = $label;
  }

  /**
   * @return mixed
   */
  public function getContentType()
  {
    return $this->content_type;
  }

  /**
   * @param mixed $content_type
   */
  public function setContentType($content_type): void
  {
    $this->content_type = $content_type;
  }

  /**
   * @return mixed
   */
  public function getField()
  {
    return $this->field;
  }

  /**
   * @param mixed $field
   */
  public function setField($field): void
  {
    $this->field = $field;
  }

  /**
   * @return mixed
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * @param mixed $options
   */
  public function setOptions($options): void
  {
    $this->options = $options;
  }

  /**
   * @return mixed
   */
  public function getParagraphField()
  {
    return $this->paragraph_field;
  }

  /**
   * @param mixed $paragraph_field
   */
  public function setParagraphField($paragraph_field): void
  {
    $this->paragraph_field = $paragraph_field;
  }

  /**
   * @return mixed
   */
  public function getParagraphs()
  {
    return $this->paragraphs;
  }

  /**
   * @param mixed $paragraphs
   */
  public function setParagraphs($paragraphs): void
  {
    $this->paragraphs = $paragraphs;
  }

}
