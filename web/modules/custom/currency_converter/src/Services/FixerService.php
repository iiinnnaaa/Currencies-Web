<?php

namespace Drupal\currency_converter\Services;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

class FixerService implements FixerServiceInterface
{

  protected string $baseUrl = 'http://data.fixer.io/api';

  protected mixed $apiKey;

  protected ClientFactory $httpClientFactory;

  protected $logger;

  public function __construct(ConfigFactoryInterface $configFactory, ClientFactory $httpClientFactory, LoggerChannelFactoryInterface $loggerFactory)
  {
    $this->apiKey = $configFactory->get('fixer_integration.settings')->get('api_key');
    $this->httpClientFactory = $httpClientFactory;
    $this->logger = $loggerFactory->get('fixer_integration');
  }

  public function getExchangeRates()
  {
    $client = $this->httpClientFactory->fromOptions();
    $url = $this->baseUrl . '/latest?access_key=' . $this->apiKey;
    try {
      $response = $client->get($url);
      $data = Json::decode($response->getBody());
      return $data['rates'];
    } catch (\Exception $exception) {
      $this->logger->error('Error retrieving exchange rates: @message', ['@message' => $exception->getMessage()]);
      return [];
    }
  }

  public function convert($value, $from, $to)
  {
//    // Use API of fixer.io (paid plan)
//    $client = $this->httpClientFactory->fromOptions();
//    $convertUrl = $this->baseUrl . '/convert?access_key=' . $this->apiKey . '&from=' . $from . '&to=' . $to . '&amount=' . $value;
//
//    try {
//      $response = $client->get($convertUrl);
//      $data = Json::decode($response->getBody());
//      return $data['converted'];
//    } catch (\Exception $exception) {
//      $this->logger->error('Error while converting values: @exception', ['@exception' => $exception->getMessage()]);
//    }
//  }

    $rates = $this->getExchangeRates();
    $to = strtoupper($to);

    $convertedValue = $value * $rates[$to];

    return round($convertedValue, 2);
  }
}
