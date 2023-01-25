<?php

namespace Richardds\ECBAPI;

class ECBConverter
{
    private ECB $ecb;

    private ?string $cache_file;

    private int $cache_timeout;

    /**
     * @var Currency[]
     */
    private array $exchange_data = [];

    /**
     * @param ECB $ecb
     * @param ?string $cache_file path to exchange reference cache; disables the cache on null
     * @param int $cache_timeout number of seconds after the cache is renewed
     */
    public function __construct(ECB $ecb, ?string $cache_file = '.ecb_cache', int $cache_timeout = 3600)
    {
        $this->ecb = $ecb;
        $this->cache_file = $cache_file;
        $this->cache_timeout = $cache_timeout;
    }

    /**
     * Triggers cache timeout check.
     * The cache is renewed only if the current cache file reached its timeout.
     *
     * @throws ECBException
     */
    public function check(): void
    {
        if (!empty($this->cache_file)) {
            if (!file_exists($this->cache_file) || time() - filemtime($this->cache_file) > $this->cache_timeout) {
                $this->exchange_data = $this->ecb->getExchangeReferences();
                file_put_contents($this->cache_file, serialize($this->exchange_data), LOCK_EX);
            } else if (empty($this->exchange_data)) {
                $this->exchange_data = unserialize(file_get_contents($this->cache_file));
            }
        } else if (empty($this->exchange_data)) {
            $this->exchange_data = $this->ecb->getExchangeReferences();
        }
    }

    /**
     * List all currency exchange references.
     *
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
     * @return float[]|float
     * @throws ECBException
     */
    private function convert(float $amount, $currencies, callable $callback)
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
     * Converts foreign currency amount to euro amount.
     *
     * @param string[]|string $currencies
     * @return float[]|float
     * @throws ECBException
     */
    public function toEuro(float $amount, $currencies, ?int $precision = null)
    {
        return $this->convert($amount, $currencies, function ($amount, $rate) use ($precision) {
            $value = $amount / $rate;

            if (!is_null($precision)) {
                return round($value, $precision);
            } else {
                return $value;
            }
        });
    }

    /**
     * Converts euro amount to foreign currency amount.
     *
     * @param string[]|string $currencies
     * @return float[]|float
     * @throws ECBException
     */
    public function toForeign(float $amount, $currencies, ?int $precision = null)
    {
        return $this->convert($amount, $currencies, function ($amount, $rate) use ($precision) {
            $value = $amount * $rate;

            if (!is_null($precision)) {
                return round($value, $precision);
            } else {
                return $value;
            }
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

    public function getCacheFile(): ?string
    {
        return $this->cache_file;
    }

    public function setCacheFile(?string $cache_file): void
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
