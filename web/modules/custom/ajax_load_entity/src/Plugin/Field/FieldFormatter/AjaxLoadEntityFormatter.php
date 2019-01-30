<?php

namespace Drupal\ajax_load_entity\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'ajax load entity' formatter.
 *
 * @FieldFormatter(
 *   id = "ajax_load_entity",
 *   label = @Translation("Ajax load entity"),
 *   description = @Translation("Display the labels of the referenced entities that when clicked will AJAX inject the entity onto the page."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class AjaxLoadEntityFormatter extends EntityReferenceLabelFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'link' => TRUE,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['link'] = [
      '#title' => t('Link label to the referenced entity'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('link'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->getSetting('link') ? t('Link to the referenced entity') : t('No link');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // Get the view mode picked on the manage display page.
    $view_mode = $this->getSetting('view_mode');

    // Now we need to loop over each entity to be rendered and create a link element
    // for each one.
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      if (!$entity->isNew()) {
        // Set up the options for our route, we default the method to 'nojs' since
        // the drupal ajax library will replace that for us.
        $options = [
          'method' => 'nojs',
          'entity_type' => $entity->getEntityTypeId(),
          'entity' => $entity->id(),
          'view_mode' => $view_mode
        ];

        // Now we create the path from our route, passing the options it needs.
          $uri = Url::fromRoute('ajax_load_entity.load_entity', $options);

        // And create a link element. We need to add the 'use-ajax' class so that
        // Drupal's core AJAX library will detect this link and ajaxify it.
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $entity->uuid(),
          '#url' => $uri,
          '#options' => $uri->getOptions() + [
              'attributes' => [
                'class' => [
                  'use-ajax'
                ],
              ]
            ]
        ];

        if (!empty($items[$delta]->_attributes)) {
          $elements[$delta]['#options'] += ['attributes' => []];
          $elements[$delta]['#options']['attributes'] += $items[$delta]->_attributes;
          // Unset field item attributes since they have been included in the
          // formatter output and shouldn't be rendered in the field template.
          unset($items[$delta]->_attributes);
        }
      } else {
        continue;
      }
      $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
    }

    // Now we add the container to render after the links. This is where the AJAX
    // loaded content will be injected in to.
    $elements[] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'ajax-load-entity',
      ],
    ];

    // Make sure the AJAX library is attached.
    $elements['#attached']['library'][] = 'core/drupal.ajax';
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity) {
    return $entity->access('view label', NULL, TRUE);
  }

}
