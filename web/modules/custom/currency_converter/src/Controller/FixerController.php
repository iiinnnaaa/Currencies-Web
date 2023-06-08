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
    $usd = $exchangeRates['USD'];

    dd($this->fixerService->convert(11, 'usd', 'amd'));

    $content = [
      '#theme' => 'fixer_exchange_theme',
      '#exchange_rates' => $exchangeRates,
    ];

    return $content;
  }
}
