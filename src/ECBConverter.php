<?php

namespace Richardds\ECBAPI;

class ECBConverter
{
    private ECB $ecb;

    private string $cache_file;

    private int $cache_timeout;

    /**
     * @var Currency[]
     */
    private array $exchange_data = [];

    public function __construct(ECB $ecb, string $cache_file = '.ecb_cache', int $cache_timeout = 3600)
    {
        $this->ecb = $ecb;
        $this->cache_file = $cache_file;
        $this->cache_timeout = $cache_timeout;
    }

    /**
     * @throws ECBException
     */
    public function checkFileCache(): void
    {
        if (!file_exists($this->cache_file) || time() - filemtime($this->cache_file) > $this->cache_timeout) {
            $this->reloadExchangeReferences($this->ecb);
            file_put_contents($this->cache_file, serialize($this->exchange_data), LOCK_EX);
        } else if (empty($this->exchange_data)) {
            $this->exchange_data = unserialize(file_get_contents($this->cache_file));
        }
    }

    /**
     * Converts foreign currency to euro
     *
     * @throws ECBException
     */
    public function toEuro(float $amount, string $currency, ?int $precision = null)
    {
        return $this->convert($amount, $currency, function ($amount, $rate) use ($precision) {
            $value = $amount / $rate;

            if (!is_null($precision)) {
                return round($value, $precision);
            } else {
                return $value;
            }
        });
    }

    /**
     * @throws ECBException
     */
    private function check(): void
    {
        if (!empty($this->cache_file)) {
            $this->checkFileCache();
        } else if (empty($this->exchange_data)) {
            $this->reloadExchangeReferences($this->ecb);
        }
    }

    /**
     * @return Currency[]|array
     * @throws ECBException
     */
    public function list(bool $asArray = false): array
    {
        $this->check();

        if ($asArray) {
            $array = [];

            foreach ($this->exchange_data as $reference) {
                $array[$reference->getCode()] = $reference->getRate();
            }

            return $array;
        } else {
            return $this->exchange_data;
        }
    }

    /**
     * @param string[]|string $currencies
     * @return string[]|string
     * @throws ECBException
     */
    public function convert(float $amount, $currencies, callable $callback)
    {
        $this->check();

        // Selected currencies
        if (is_array($currencies)) {
            $results = [];

            foreach ($currencies as $currency) {
                $results[$currency] = $callback($amount, $this->exchange_data[$currency]->getRate());
            }

            return $results;
        }

        // All currencies
        if ($currencies === '*') {
            $results = [];

            foreach ($this->exchange_data as $currency) {
                $results[$currency->getCode()] = $callback($amount, $currency->getRate());
            }

            return $results;
        }

        // Single currency
        return $callback($amount, $this->exchange_data[$currencies]->getRate());
    }

    /**
     * Reloads ECB exchange references
     *
     * @throws ECBException
     */
    public function reloadExchangeReferences(ECB $ecb): void
    {
        $this->exchange_data = $ecb->getExchangeReferences();
    }

    /**
     * Converts euro to foreign currency
     *
     * @throws ECBException
     */
    public function toForeign(float $amount, string $currency_code, ?int $precision = null)
    {
        return $this->convert($amount, $currency_code, function ($amount, $rate) use ($precision) {
            $val = $amount * $rate;
            return !is_null($precision) ? round($val, $precision) : $val;
        });
    }

    public function getECB(): ECB
    {
        return $this->ecb;
    }

    public function setECB(ECB $ecb): void
    {
        $this->ecb = $ecb;
    }

    public function getCacheFile(): string
    {
        return $this->cache_file;
    }

    public function setCacheFile(string $cache_file): void
    {
        $this->cache_file = $cache_file;
    }

    public function getCacheTimeout(): int
    {
        return $this->cache_timeout;
    }

    public function setCacheTimeout(int $cache_timeout): void
    {
        $this->cache_timeout = $cache_timeout;
    }
}
