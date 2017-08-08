<?php

namespace Richardds\ECBAPI;

class ECBConverter
{
    /**
     * TODO: Collection to fix get on null exception
     *
     * @var Currency[]
     */
    private static $exchange_data;

    /**
     * @var boolean
     */
    private static $exchange_data_cached = false;

    /**
     * Converts foreign currency to euro
     *
     * @param string|string[] $currency_code
     * @param integer $amount
     * @param boolean|null $round
     * @return integer|integer[]
     */
    public static function toEuro($amount, $currency_code, $round = null)
    {
        return self::convert($amount, $currency_code, function ($amount, $rate) use ($round) {
            $val = $amount / $rate;
            return !is_null($round) ? round($val, $round) : $val;
        });
    }

    /**
     * @param $amount
     * @param $currency_code
     * @param callable $callback
     * @return array
     */
    private static function convert($amount, $currency_code, callable $callback)
    {
        if (!self::$exchange_data_cached) {
            self::reloadExchangeReferences();
        }

        if (is_array($currency_code)) {
            $results = [];
            foreach ($currency_code as $currency_c) {
                $results[$currency_c] = $callback($amount, self::$exchange_data[$currency_c]->getRate());
            }
            return $results;
        } else {
            if ($currency_code == '*') {
                $results = [];
                foreach (self::$exchange_data as $currency) {
                    $results[$currency->getCode()] = $callback($amount, $currency->getRate());
                }
                return $results;
            } else {
                return $callback($amount, self::$exchange_data[$currency_code]->getRate());
            }
        }
    }

    /**
     * Reloads ECB exchange references
     *
     * @throws ECBException
     */
    public static function reloadExchangeReferences()
    {
        try {
            self::$exchange_data = ECB::getExchangeReferences();
            self::$exchange_data_cached = true;
        } catch (ECBException $e) {
            throw new ECBException('Failed to update ESB exchange references',
                ECBException::CONVERT_FAILED, $e);
        }
    }

    /**
     * Converts euro to foreign currency
     *
     * @param string|string[] $currency_code
     * @param integer $amount
     * @param boolean|null $round
     * @return integer|integer[]
     */
    public static function toForeign($amount, $currency_code, $round = null)
    {
        return self::convert($amount, $currency_code, function ($amount, $rate) use ($round) {
            $val = $amount * $rate;
            return !is_null($round) ? round($val, $round) : $val;
        });
    }
}
