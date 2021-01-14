<?php

namespace Drupal\auto_paragraphs;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;

class AutoParagraphs
{

  public static function addMoreSubmit(array $form, FormStateInterface $form_state)
  {
    $state = $form_state->getStorage();
    $configs = $state['auto_paragraphs_configs'];

    $paragraphsToAdd = [];

    foreach ($configs as $configId) {
      $autoParagraphConfig = \Drupal::config('auto_paragraphs.auto_paragraph.' . $configId)->get();

      $button = $form_state->getTriggeringElement();
      if ($button['#name'] == $autoParagraphConfig['field'] . '_auto_paragraph') {
        $paragraphFieldName = $autoParagraphConfig['paragraph_field'];
        $autoParagraphFieldName = $autoParagraphConfig['field'];

        $paragraphFields[] = $paragraphFieldName;

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

  public static function addMoreAjax(array $form, FormStateInterface $form_state)
  {
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

  public static function getWidgetState(array $parents, $field_name, FormStateInterface $form_state)
  {
    return NestedArray::getValue($form_state->getStorage(), array_merge(['field_storage', '#parents'], $parents, ['#fields', $field_name]));
  }

  public static function setWidgetState(array $parents, $field_name, FormStateInterface $form_state, array $field_state)
  {
    NestedArray::setValue($form_state->getStorage(), array_merge(['field_storage', '#parents'], $parents, ['#fields', $field_name]), $field_state);
  }

  public static function prepareDeltaPosition(array &$widget_state, FormStateInterface $form_state, array $field_path, $new_delta)
  {
    // Increase number of items to create place for new paragraph.
    $widget_state['items_count']++;

    // Default behavior is adding to end of list and in case delta is not
    // provided or already at end, we can skip all other steps.
    if (!is_numeric($new_delta) || intval($new_delta) >= $widget_state['real_item_count']) {
      return;
    }

    $widget_state['real_item_count']++;

    // Limit delta between 0 and "number of items" in paragraphs widget.
    $new_delta = max(intval($new_delta), 0);

    // Change user input in order to create new delta position.
    $user_input = NestedArray::getValue($form_state->getUserInput(), $field_path);

    // Rearrange all original deltas to make one place for the new element.
    $new_original_deltas = [];
    foreach ($widget_state['original_deltas'] as $current_delta => $original_delta) {
      $new_current_delta = $current_delta >= $new_delta ? $current_delta + 1 : $current_delta;

      $new_original_deltas[$new_current_delta] = $original_delta;
      $user_input[$original_delta]['_weight'] = $new_current_delta;
    }

    // Add information into delta mapping for the new element.
    $original_deltas_size = count($widget_state['original_deltas']);
    $new_original_deltas[$new_delta] = $original_deltas_size;
    $user_input[$original_deltas_size]['_weight'] = $new_delta;

    $widget_state['original_deltas'] = $new_original_deltas;
    NestedArray::setValue($form_state->getUserInput(), $field_path, $user_input);
  }
}
