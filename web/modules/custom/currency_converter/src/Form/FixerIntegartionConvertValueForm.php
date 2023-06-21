<?php

namespace Drupal\currency_converter\Form;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\currency_converter\Services\FixerServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Implements an example form.
 */
class FixerIntegartionConvertValueForm extends FormBase
{
  protected FixerServiceInterface $fixerService;

  public function __construct(FixerServiceInterface $fixerService)
  {
    $this->fixerService = $fixerService;
  }

  public static function create(ContainerInterface|\Symfony\Component\DependencyInjection\ContainerInterface $container)
  {
    return new static(
      $container->get('currency_converter.fixer_service')
    );
  }

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
    $form['amount'] = [
      '#type' => 'textfield',
      '#placeholder' => '123',
      '#required' => true,
      '#title' => $this->t('Amount'),
    ];
    $form['fromRate'] = [
      '#type' => 'textfield',
      '#placeholder' => 'USD',
      '#required' => true,
      '#title' => $this->t('From'),
    ];
    $form['toRate'] = [
      '#type' => 'textfield',
      '#placeholder' => 'AMD',
      '#required' => true,
      '#title' => $this->t('To'),
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
    if (!$form_state->getValue('toRate')) {
      $form_state->setErrorByName('amount', $this->t('The field is required'));
      $form_state->setErrorByName('fromRate', $this->t('The field is required'));
      $form_state->setErrorByName('toRate', $this->t('The field is required'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    try {
      $amount = $form_state->getValue('amount');
      $fromRate = $form_state->getValue('fromRate');
      $toRate = $form_state->getValue('toRate');

      $converted = $this->fixerService->convert($amount, $fromRate, $toRate);

      $request = \Drupal::service('request_stack')->getCurrentRequest();
      $session = $request->getSession();
      $session->set('converted_value', $converted);

      // Redirect to the result page.
      $response = new RedirectResponse('/result-page');
      $response->send();

    } catch (\Exception $exception) {
      $this->logger('error')->error($exception->getMessage());
    }
  }
}
