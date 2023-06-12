<?php

namespace Drupal\currency_converter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class FixerIntegartionConvertValueForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'convert_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
//    dd($form_state);
    $form['amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Amount'),
    ];
//    $form['from'] = [
//      '#type' => 'select',
//      '#title' => $this->t('From'),
////      '#options' => $rates,
//    ];
    $form['from'] = [
      '#type' => 'textfield',
      '#default_value' => $this->t('From'),
    ];
    $form['to'] = [
      '#type' => 'textfield',
      '#default_value' => $this->t('To'),
      '#required' => true,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Convert'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (!$form_state->getValue('to')) {
      $form_state->setErrorByName('to', $this->t('The field is required'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

  }
}
