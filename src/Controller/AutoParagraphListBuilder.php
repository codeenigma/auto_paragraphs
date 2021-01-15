<?php

namespace Drupal\auto_paragraphs\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Example.
 */
class AutoParagraphListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['status'] = $this->t('Status');
    $header['label'] = $this->t('Auto Paragraph');
    $header['content_type'] = $this->t('Content Type');
    $header['field'] = $this->t('Field');
    $header['paragraph_field'] = $this->t('Paragraph');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['status'] = ($entity->status() ? $this->t('Enabled') : $this->t('Disabled'));
    $row['label'] = $entity->label();
    $row['content_type'] = $entity->getContentType();
    $row['field'] = $entity->getField();
    $row['paragraph_field'] = $entity->getParagraphField();
    return $row + parent::buildRow($entity);
  }

}
