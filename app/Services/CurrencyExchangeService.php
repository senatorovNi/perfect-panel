<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyExchangeService {
    private const  COMMISSION_RATE = 1 - 0.02;
    private const  MIN_EXCHANGE    = 0.01;

    private const  API_URL      = 'https://api.coincap.io/v2/';
    private const  METHOD_RATES = 'rates';

    private const CURRENCY_USD = 'USD';

    public function getRates(?array $neededCurrencies = null): array
    {
        $ratesData = $this->getRatesData(true);
        $rates     = $neededCurrencies !== null
            ? array_filter($ratesData, fn (string $currencyCode) => in_array($currencyCode, $neededCurrencies), ARRAY_FILTER_USE_KEY)
            : $ratesData;

        // Сортировка от меньшего курса к большому
        asort($rates);

        return $rates;
    }

    public function convertCurrency(string $currencyFrom, string $currencyTo, float $value): array
    {
        if ($value < self::MIN_EXCHANGE) {
            throw new \InvalidArgumentException('Minimum exchange amount is 0.01');
        }
        $ratesData = $this->getRatesData();

        if (!array_key_exists($currencyFrom, $ratesData) || !array_key_exists($currencyTo, $ratesData)) {
            throw new \InvalidArgumentException('Invalid currencies');
        }

        $rateValue      = round($ratesData[$currencyTo] / $ratesData[$currencyFrom], 10);
        $precision      = $currencyTo === self::CURRENCY_USD ? 2 : 10;
        $convertedValue = round($value * $rateValue * self::COMMISSION_RATE, $precision);

        return [
            'currency_from'   => $currencyFrom,
            'currency_to'     => $currencyTo,
            'value'           => $value,
            'converted_value' => number_format($convertedValue, $precision, '.', ''),
            'rate'            => number_format($rateValue, 10, '.', ''),
        ];
    }

    /**
     * @param  bool  $hasCommission
     * @return array [name_currency => value]
     */
    private function getRatesData(bool $hasCommission = false): array
    {
        $commission = $hasCommission ? self::COMMISSION_RATE : 1;
        $response   = Http::get(self::API_URL . self::METHOD_RATES);
        $data       = $response->json()['data'] ?? [];
        $rates      = [];
        foreach ($data as $rate) {
            $currencyCode         = $rate['symbol'];
            $currencyValue        = $rate['rateUsd'] * $commission;
            $rates[$currencyCode] = $currencyValue;
        }

        return $rates;
    }
}
