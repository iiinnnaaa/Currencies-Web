<?php

namespace Drupal\currency_converter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class FixerIntegrationSettingsForm extends ConfigFormBase
{

  protected function getEditableConfigNames()
  {
    return ['fixer_integration.settings'];
  }

  public function getFormId()
  {
    return 'fixer_integration_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('fixer_integration.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fixer API Key'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->config('fixer_integration.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
