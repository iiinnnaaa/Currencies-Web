<?php

namespace Drupal\currency_converter\Controller;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\currency_converter\Services\FixerServiceInterface;

class FixerController extends ControllerBase
{

  protected $fixerService;

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

  public function getExchangeRates()
  {
    return [
      '#theme' => 'currency-converter-rates',
      '#exchangeRates' => $this->fixerService->getExchangeRates(),
    ];
  }

  public function convert($amount, $fromRate, $toRate)
  {
    $this->fixerService->convert($amount, $fromRate, $toRate);
  }


  public function convertedValuesPage()
  {
    $submitValue = \Drupal::service('session')->get('converted_value');

    \Drupal::service('session')->remove('converted_value');

    $content = [
      '#markup' => 'Form Submit Value: ' . $submitValue,
    ];

    return $content;
  }
}
