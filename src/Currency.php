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
        'EUR' => 'Euro',
        'USD' => 'United States dollar',
        'JPY' => 'Japanese yen',
        'BGN' => 'Bulgarian lev',
        'CZK' => 'Czech koruna',
        'DKK' => 'Danish krone',
        'GBP' => 'Pound sterling',
        'HUF' => 'Hungarian forint',
        'PLN' => 'Polish złoty',
        'RON' => 'Romanian leu',
        'SEK' => 'Swedish krona',
        'CHF' => 'Swiss franc',
        'ISK' => 'Icelandic króna',
        'NOK' => 'Norwegian krone',
        'HRK' => 'Croatian kuna',
        'TRY' => 'Turkish lira',
        'AUD' => 'Australian dollar',
        'BRL' => 'Brazilian real',
        'CAD' => 'Canadian dollar',
        'CNY' => 'Renminbi',
        'HKD' => 'Hong Kong dollar',
        'IDR' => 'Indonesian rupiah',
        'ILS' => 'Israeli new shekel',
        'INR' => 'Indian rupee',
        'KRW' => 'South Korean won',
        'MXN' => 'Mexican peso',
        'MYR' => 'Malaysian ringgit',
        'NZD' => 'New Zealand dollar',
        'PHP' => 'Philippine peso',
        'SGD' => 'Singapore dollar',
        'THB' => 'Thai baht',
        'ZAR' => 'South African rand'
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
