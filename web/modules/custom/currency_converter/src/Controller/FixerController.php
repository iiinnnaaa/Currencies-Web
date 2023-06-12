<?php

namespace Drupal\currency_converter\Controller;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\currency_converter\Services\FixerServiceInterface;

class FixerController extends ControllerBase{

  protected $fixerService;

  public function __construct(FixerServiceInterface $fixerService){
    $this->fixerService = $fixerService;
  }

  public static function create(ContainerInterface|\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('currency_converter.fixer_service')
    );
  }

  public function getExchangeRates(){
    $exchangeRates = $this->fixerService->getExchangeRates();

    return [
      '#theme' => 'fixer-exchange-theme',
      '#exchange_rates' => $exchangeRates,
    ];
  }

  public function convert(){
    $convert = $this->fixerService->convert();


  }
}
