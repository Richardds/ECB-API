<?php

namespace Richardds\ECBAPI;

/**
 * Class Currency
 * @package Richardds\ECBAPI
 */
class Currency
{
    /**
     * Available ECB currencies
     */
    public const CURRENCY_NAME_TABLE = [
        'EUR' => '',
        'USD' => '',
        'JPY' => '',
        'BGN' => '',
        'CZK' => '',
        'DKK' => '',
        'GBP' => '',
        'HUF' => '',
        'PLN' => '',
        'RON' => '',
        'SEK' => '',
        'CHF' => '',
        'NOK' => '',
        'HRK' => '',
        'RUB' => '',
        'TRY' => '',
        'AUD' => '',
        'BRL' => '',
        'CAD' => '',
        'CNY' => '',
        'HKD' => '',
        'IDR' => '',
        'ILS' => '',
        'INR' => '',
        'KRW' => '',
        'MXN' => '',
        'MYR' => '',
        'NZD' => '',
        'PHP' => '',
        'SGD' => '',
        'THB' => '',
        'ZAR' => ''
    ];

    /**
     * @var string
     */
    private $code;

    /**
     * @var float
     */
    private $rate;

    public function __construct(string $code, float $rate)
    {
        $this->code = $code;
        $this->rate = $rate;
    }

    public function getName(): string
    {
        return self::CURRENCY_NAME_TABLE[$this->code] ?? '';
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
}
