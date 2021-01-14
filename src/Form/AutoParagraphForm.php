<?php
namespace Drupal\auto_paragraphs\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\field\FieldStorageConfigInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Example add and edit forms.
 */
class AutoParagraphForm extends EntityForm {

  /**
   * The field type plugin manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypePluginManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Turns a render array into a HTML string.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs an AutoParagraphForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_plugin_manager
   *   The field type plugin manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface|null $entity_field_manager
   *   (optional) The entity field manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The render object.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, FieldTypePluginManagerInterface $field_type_plugin_manager, EntityFieldManagerInterface $entity_field_manager, RendererInterface $renderer) {
    $this->entityTypeManager = $entityTypeManager;
    $this->fieldTypePluginManager = $field_type_plugin_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('entity_field.manager'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $autoParagraphEntity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $autoParagraphEntity->label(),
      '#description' => $this->t("Label for the Example."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $autoParagraphEntity->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$autoParagraphEntity->isNew(),
    ];

    $existingContentTypeOptions = $this->getExistingContentTypes();

    $form['content_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Content Type'),
      '#options' => $existingContentTypeOptions,
      '#empty_option' => $this->t('- Select an existing content type -'),
      '#default_value' => $autoParagraphEntity->getContentType(),
      '#required' => TRUE,
      '#ajax' => [
        'event' => 'change',
        'callback' => '::formDropdownCallback',
        'wrapper' => 'field-replace',
      ],
    ];

    $fields = [];
    if (!empty($form_state->getValue('content_type'))) {
      $contentType = $form_state->getValue('content_type');
      $fields = $this->getExistingFieldStorageOptions($contentType);
    }
    else {
      $fields = $this->getExistingFieldStorageOptions($autoParagraphEntity->getContentType());
    }

    $form['field'] = [
      '#type' => 'select',
      '#title' => $this->t('Field'),
      '#options' => $fields,
      '#empty_option' => $this->t('- Select an existing field -'),
      '#default_value' => $autoParagraphEntity->getField(),
      '#required' => TRUE,
      '#states' => [
        '!visible' => [
          ':input[name="content_type"]' => ['value' => ''],
        ],
      ],
      '#ajax' => [
        'event' => 'change',
        'callback' => '::formDropdownCallback',
        'wrapper' => 'options-replace',
      ],
      '#prefix' => '<div id="field-replace">',
      '#suffix' => '</div>',
    ];

    $options = [];
    if (!empty($form_state->getValue('field'))
      && !empty($form_state->getValue('content_type'))
    ) {
      // Use a default value.
      $selectedField = $form_state->getValue('field');
      $selectedContentType = $form_state->getValue('content_type');
      $options = $this->getOptionsForField($selectedField, $selectedContentType);
    }
    else {
      $options = $this->getOptionsForField($autoParagraphEntity->getField(), $autoParagraphEntity->getContentType());
    }

    $form['options'] = [
      '#type' => 'select',
      '#title' => $this->t('Options'),
      '#multiple' => TRUE,
      '#options' => $options,
      '#empty_option' => $this->t('- Select an option -'),
      '#default_value' => $autoParagraphEntity->getOptions(),
      '#states' => [
        '!visible' => [
          ':input[name="field"]' => ['value' => ''],
        ],
      ],
      '#prefix' => '<div id="options-replace">',
      '#suffix' => '</div>',
    ];

    $paragraphFields = [];
    if (!empty($form_state->getValue('content_type'))) {
      $contentType = $form_state->getValue('content_type');
      $paragraphFields = $this->getParargraphFieldOptions($contentType);
    }
    else {
      $paragraphFields = $this->getParargraphFieldOptions($autoParagraphEntity->getContentType());
    }

    $form['paragraph_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Paragraph Field'),
      '#options' => $paragraphFields,
      '#empty_option' => $this->t('- Select an existing paragraph field -'),
      '#default_value' => $autoParagraphEntity->getParagraphField(),
      '#states' => [
        '!visible' => [
          ':input[name="options"]' => ['value' => ''],
        ],
      ],
      '#ajax' => [
        'event' => 'change',
        'callback' => '::formDropdownCallback',
        'wrapper' => 'paragraphs-replace',
      ],
      '#prefix' => '<div id="paragraph-field-replace">',
      '#suffix' => '</div>',
    ];

    $paragraphs = [];
    if (!empty($form_state->getValue('paragraph_field'))
      && !empty($form_state->getValue('content_type'))
    ) {
      // Use a default value.
      $paragraphField = $form_state->getValue('paragraph_field');
      $selectedContentType = $form_state->getValue('content_type');
      $paragraphs = $this->getParagraphBundlesForField($paragraphField, $selectedContentType);
    }
    else {
      $paragraphs = $this->getParagraphBundlesForField($autoParagraphEntity->getParagraphField(), $autoParagraphEntity->getContentType());
    }

    $form['paragraphs'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Paragraphs'),
      '#options' => $paragraphs,
      '#empty_option' => $this->t('- Select an option -'),
      '#default_value' => $autoParagraphEntity->getParagraphs(),
      '#required' => TRUE,
      '#states' => [
        '!visible' => [
          ':input[name="paragraph_field"]' => ['value' => ''],
        ],
      ],
      '#prefix' => '<div id="paragraphs-replace">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * Ajax callback for the auto_paragraph creation form.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function formDropdownCallback(array $form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);

    $response = new AjaxResponse();
    //$fieldField = $this->renderer->renderRoot($form['field']);
    $response->addCommand(new ReplaceCommand('#field-replace', $form['field']));

    //$optionsField = $this->renderer->renderRoot($form['options']);
    $response->addCommand(new ReplaceCommand('#options-replace', $form['options']));

    //$paragraphFieldField = $this->renderer->renderRoot($form['paragraph_field']);
    $response->addCommand(new ReplaceCommand('#paragraph-field-replace', $form['paragraph_field']));

    //$paragraphsField = $this->renderer->renderRoot($form['paragraphs']);
    $response->addCommand(new ReplaceCommand('#paragraphs-replace', $form['paragraphs']));

    return $response;
  }

  /**
   * Get options for field.
   *
   * @param $selectedField
   * @param $contentType
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getOptionsForField($selectedField, $contentType) {
    if (empty($selectedField)) {
      return [];
    }
    // Load the field storage.
    $fieldStorage = $this->entityTypeManager
      ->getStorage('field_storage_config')
      ->load('node.' . $selectedField);

    // We need to create a blank content type in order to be able to fetch the needed widget configurations for that
    // entity type.
    $node = $this->entityTypeManager
      ->getStorage('node')
      ->create(['type' => $contentType]);

    $options = $fieldStorage
      ->getOptionsProvider('target_id', $node)
      ->getSettableOptions();

    return $options;
  }

  /**
   * Get paragraph bundles for field.
   *
   * @param $paragraphField
   * @param $contentType
   * @return array|mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getParagraphBundlesForField($paragraphField, $contentType) {
    if (empty($paragraphField)) {
      return [];
    }

    $paragraphFieldConfiguration = $this->entityTypeManager
      ->getStorage('field_config')
      ->load('node.' . $contentType . '.' . $paragraphField);

    $paragraphBundles = $paragraphFieldConfiguration->getSetting('handler_settings')["target_bundles"];

    return $paragraphBundles;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $example = $this->entity;
    $status = $example->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addMessage($this->t('The %label Example created.', [
        '%label' => $example->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label Example updated.', [
        '%label' => $example->label(),
      ]));
    }

    $form_state->setRedirect('entity.auto_paragraph.collection');
  }

  /**
   * Helper function to check whether an Example configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('auto_paragraph')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

  public function getExistingContentTypes() {
    $types = [];
    $loadedTypes = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($loadedTypes as $contentType) {
      $types[$contentType->id()] = $contentType->label();
    }
    return $types;
  }

  protected function getExistingFieldStorageOptions($contentType) {
    $bundleFields = [];
    foreach ($this->entityFieldManager->getFieldDefinitions('node', $contentType) as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        if ($field_definition->getType() == 'entity_reference') {
          if ($field_definition->getSetting('target_type') == 'taxonomy_term') {
            $handlerSettings = $field_definition->getSetting('handler_settings');
            if ($handlerSettings['auto_create'] == FALSE) {
              $bundleFields[$field_name] = $this->t('@type: @field', [
                '@type' => $field_definition->getType(),
                '@field' => $field_definition->getLabel(),
              ]);
            }
          }
        }
      }
    }
    asort($bundleFields);
    return $bundleFields;
  }

  protected function getParargraphFieldOptions($contentType) {
    $bundleParagraphfields = [];
    foreach ($this->entityFieldManager->getFieldDefinitions('node', $contentType) as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        if ($field_definition->getType() == 'entity_reference_revisions') {
          if ($field_definition->getSetting('target_type') == 'paragraph') {
            $bundleParagraphfields[$field_name] = $this->t('@type: @field', [
              '@type' => $field_name,
              '@field' => $field_definition->getLabel(),
            ]);
          }
        }
      }
    }
    asort($bundleParagraphfields);
    return $bundleParagraphfields;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);

    // @todo : validate the conditions we are generating and ensure we aren't dubling up.
    // ie, no two rules can act on different paragraph fields.
    // no two rules can contradict their selected options.
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $trigger = (string) $form_state->getTriggeringElement()['#value'];
    if ($trigger == 'Save') {
      // Process submitted form data.
      parent::submitForm($form, $form_state);
      $form_state->setRedirect('entity.auto_paragraph.collection');
    }
    else {
      // Rebuild the form. This causes buildForm() to be called again before the
      // associated Ajax callback. Allowing the logic in buildForm() to execute
      // and update the $form array so that it reflects the current state of
      // the instrument family select list.
      $form_state->setRebuild(TRUE);
    }
  }
}
