<?php

namespace Drupal\status_message\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
/**
 * Class StatusMessageSettings.
 */
class StatusMessageSettings extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'status_message_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // Return name config file.
    return [
      'status_message.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('status_message.settings');
    $form['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#description' => $this->t('The max-width of the pop-up window in pixels.'),
      '#default_value' => $config->get('width'),

    ];
    $form['height'] = [
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#description' => $this->t('The height of the pop-up window in pixels.'),
      '#default_value' => $config->get('height'),
    ];
    $form['background'] = [
      '#type' => 'color',
      '#title' => $this->t('Background'),
      '#description' => $this->t('The background of the pop-up window.'),
      '#default_value' => $config->get('background'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $width = $values['width'];
    $height = $values['height'];
    if(($width >= 100 || $width == '') && ($height >= 50 || $height == '')) {
      //echo 'weszlo'; die();
      $this->config('status_message.settings')
        ->set('width', $values['width'])
        ->set('height', $values['height'])
        ->set('background', $values['background'])
        ->save();
      drupal_set_message(t('Form Submitted Successfully'), 'status', TRUE);
    } else $form_state->setErrorByName('width or height', $this->t('Height can not be less than 50. Width can not be less than 100. Enter the correct value.'));


  }

}
