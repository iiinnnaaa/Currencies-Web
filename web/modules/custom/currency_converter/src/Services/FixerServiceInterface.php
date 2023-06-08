<?php

namespace Drupal\currency_converter\Services;

interface FixerServiceInterface
{
  public function getExchangeRates();

  public function convert($value, $from, $to);
}
