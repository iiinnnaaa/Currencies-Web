<?php

namespace Drupal\currency_converter\Services;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

class FixerService implements FixerServiceInterface
{

  protected string $baseUrl;

  protected mixed $apiKey;

  protected ClientFactory $httpClientFactory;

  protected $logger;

  public function __construct(ConfigFactoryInterface $configFactory, ClientFactory $httpClientFactory, LoggerChannelFactoryInterface $loggerFactory)
  {
    $this->apiKey = $configFactory->get('fixer_integration.settings')->get('api_key');
    $this->httpClientFactory = $httpClientFactory;
    $this->logger = $loggerFactory->get('fixer_integration');
    $this->baseUrl = 'http://data.fixer.io/api';
  }

  public function storeExchangeRates()
  {
    try {
      $client = $this->httpClientFactory->fromOptions();
      $url = $this->baseUrl . '/latest?access_key=' . $this->apiKey;

      $response = $client->get($url);
      $data = Json::decode($response->getBody());

      $connection = \Drupal::service('database');
      foreach ($data['rates'] as $key => $value) {
        $connection->upsert('currency_rates')->fields([
          'country' => $key,
          'rate' => $value
        ])->key('country')->execute();
      }

    } catch (\Exception $exception) {
      $this->logger->error('Error retrieving exchange rates: @message', ['@message' => $exception->getMessage()]);
    }
  }

  public function getExchangeRates()
  {
    $database = \Drupal::database();
    $query = $database->select('currency_rates', 'cr');
    $exchangeRates = $query->fields('cr', ['country', 'rate'])->execute()->fetchAll();

    $convertedExchangeRates = [];
    foreach ($exchangeRates as $rate) {
      $convertedExchangeRates[] = (array)$rate;
    }

    return $convertedExchangeRates;
  }

  public function convert($amount, $fromRate, $toRate)
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

    $exchangeRates = $this->getExchangeRates();

    foreach ($exchangeRates as $rate) {
      if ($rate['country'] === strtoupper($toRate)) {
        $convertedValue = $amount * $rate['rate'];
        return round($convertedValue, 2);
      }
    }

    return null;
  }
}
