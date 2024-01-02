<?php

namespace Drupal\auto_paragraphs;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AutoParagraphsAjaxCallback.
 */
class AutoParagraphsAjaxCallback
{

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public static function addMoreSubmit(array $form, FormStateInterface $form_state)
  {
    $state = $form_state->getStorage();
    $configs = $state['auto_paragraphs_configs'];

    $paragraphsToAdd = [];

    foreach ($configs as $configId) {
      // @todo abstract this config lookup.
      $autoParagraphConfig = \Drupal::config('auto_paragraphs.auto_paragraph.' . $configId)->get();

      $button = $form_state->getTriggeringElement();
      if ($button['#name'] == $autoParagraphConfig['field'] . '_auto_paragraph') {
        $paragraphFieldName = $autoParagraphConfig['paragraph_field'];
        $autoParagraphFieldName = $autoParagraphConfig['field'];

        $inputs = $form_state->getUserInput();

        $autoParagraphFieldInput = $inputs[$autoParagraphFieldName];

        if (!is_array($autoParagraphFieldInput)) {
          $autoParagraphFieldInput = [$autoParagraphFieldInput];
        }

        foreach ($autoParagraphFieldInput as $fieldInput) {
          if (in_array($fieldInput, $autoParagraphConfig['options'])) {
            $paragraphsToAdd = array_merge($paragraphsToAdd, $autoParagraphConfig['paragraphs']);
          }
        }
      }
    }

    if (count($paragraphsToAdd) > 0) {
      $element = NestedArray::getValue($form, [$paragraphFieldName, 'widget']);
      $field_name = $element['#field_name'];
      $field_parents = $element['#field_parents'];

      $widget_state = static::getWidgetState($field_parents, $field_name, $form_state);

      foreach ($paragraphsToAdd as $paragraphToAdd) {
        $widget_state['auto_paragraphs'][] = $paragraphToAdd;

        // @todo: Add this code to prevent overloading paragraph fields.
        // if ($widget_state['real_item_count'] < $element['#cardinality'] || $element['#cardinality'] == FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED) {
        //   $widget_state['items_count'] += $howMany;
        // }
        $widget_state['items_count']++;
      }

      static::setWidgetState($field_parents, $field_name, $form_state, $widget_state);
    }

    $form_state->setRebuild();
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array|mixed|null
   */
  public static function addMoreAjax(array $form, FormStateInterface $form_state) {
    $state = $form_state->getStorage();
    $configs = $state['auto_paragraphs_configs'];

    foreach ($configs as $configId) {
      $autoParagraphConfig = \Drupal::config('auto_paragraphs.auto_paragraph.' . $configId)->get();

      $button = $form_state->getTriggeringElement();
      if ($button['#name'] == $autoParagraphConfig['field'] . '_auto_paragraph') {
        $paragraphFieldName = $autoParagraphConfig['paragraph_field'];
      }
    }

    $element = NestedArray::getValue($form, [$paragraphFieldName, 'widget']);
    $delta = $element['#max_delta'];
    $element[$delta]['#prefix'] = '<div class="ajax-new-content">' . (isset($element[$delta]['#prefix']) ? $element[$delta]['#prefix'] : '');
    $element[$delta]['#suffix'] = (isset($element[$delta]['#suffix']) ? $element[$delta]['#suffix'] : '') . '</div>';

    // Clear the Add more delta.
    NestedArray::setValue(
      $element,
      ['add_more', 'add_more_delta', '#value'],
      ''
    );

    return $element;
  }

  /**
   * @param array $parents
   * @param $field_name
   * @param FormStateInterface $form_state
   * @return array|mixed|null
   */
  public static function getWidgetState(array $parents, $field_name, FormStateInterface $form_state)
  {
    return NestedArray::getValue($form_state->getStorage(), array_merge(['field_storage', '#parents'], $parents, ['#fields', $field_name]));
  }

  /**
   * @param array $parents
   * @param $field_name
   * @param FormStateInterface $form_state
   * @param array $field_state
   */
  public static function setWidgetState(array $parents, $field_name, FormStateInterface $form_state, array $field_state)
  {
    NestedArray::setValue($form_state->getStorage(), array_merge(['field_storage', '#parents'], $parents, ['#fields', $field_name]), $field_state);
  }

}
