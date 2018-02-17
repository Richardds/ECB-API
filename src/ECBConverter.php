<?php

namespace Richardds\ECBAPI;

/**
 * Class ECBConverter
 * @package Richardds\ECBAPI
 */
class ECBConverter
{
    /**
     * @var null|Currency[]
     */
    private $exchange_data;

    /**
     * @var string
     */
    private $cache_file;

    /**
     * @var int
     */
    private $cache_timeout;

    /**
     * ECBConverter constructor.
     *
     * @param null|string $cache_file
     * @param int $cache_timeout
     */
    public function __construct(string $cache_file = '.ecb_cache', int $cache_timeout = 3600)
    {
        $this->cache_file = $cache_file;
        $this->cache_timeout = $cache_timeout;
    }

    /**
     * @throws ECBException
     */
    public function checkFileCache(): void
    {
        if (!file_exists($this->cache_file) ||  time() - filemtime($this->cache_file) > $this->cache_timeout) {
            $this->reloadExchangeReferences();
            file_put_contents($this->cache_file, serialize($this->exchange_data), LOCK_EX);
        } else {
            if (is_null($this->exchange_data)) {
                $this->exchange_data = unserialize(file_get_contents($this->cache_file));
            }
        }
    }

    /**
     * Converts foreign currency to euro.
     *
     * @param int $amount
     * @param string|array $currency_code
     * @param int|null $round
     * @return int|array
     * @throws ECBException
     */
    public function toEuro(int $amount, $currency_code, ?int $round = null)
    {
        return $this->convert($amount, $currency_code, function ($amount, $rate) use ($round) {
            $val = $amount / $rate;
            return !is_null($round) ? round($val, $round) : $val;
        });
    }

    /**
     * @throws ECBException
     */
    private function check()
    {
        if (!empty($this->cache_file)) {
            $this->checkFileCache();
        } else {
            if (is_null($this->exchange_data)) {
                $this->reloadExchangeReferences();
            }
        }
    }

    /**
     * @param bool $asArray
     * @return Currency[]|array
     * @throws ECBException
     */
    public function list(bool $asArray = false): array
    {
        $this->check();

        $references = $this->exchange_data;

        if ($asArray) {
            $array = [];

            foreach ($references as $reference) {
                $array[$reference->getCode()] = $reference->getRate();
            }

            return $array;
        }

        return $references;
    }

    /**
     * @param int $amount
     * @param string|array $currency_code
     * @param callable $callback
     * @return int|array
     * @throws ECBException
     */
    public function convert(int $amount, $currency_code, callable $callback)
    {
        $this->check();

        if (is_array($currency_code)) {
            $results = [];

            foreach ($currency_code as $currency_c) {
                $results[$currency_c] = $callback($amount, $this->exchange_data[$currency_c]->getRate());
            }

            return $results;
        } else {
            if ($currency_code == '*') {
                $results = [];

                foreach ($this->exchange_data as $currency) {
                    $results[$currency->getCode()] = $callback($amount, $currency->getRate());
                }

                return $results;
            } else {
                return $callback($amount, $this->exchange_data[$currency_code]->getRate());
            }
        }
    }

    /**
     * Reloads ECB exchange references.
     *
     * @throws ECBException
     */
    public function reloadExchangeReferences(): void
    {
        try {
            $this->exchange_data = ECB::getExchangeReferences();
        } catch (ECBException $e) {
            throw new ECBException('Failed to update ESB exchange references',
                ECBException::CONVERT_FAILED, $e);
        }
    }

    /**
     * Converts euro to foreign currency.
     *
     * @param int $amount
     * @param string|array $currency_code
     * @param int|null $precision
     * @return int|array
     * @throws ECBException
     */
    public function toForeign(int $amount, string $currency_code, ?int $precision = null)
    {
        return $this->convert($amount, $currency_code, function ($amount, $rate) use ($precision) {
            $val = $amount * $rate;
            return !is_null($precision) ? round($val, $precision) : $val;
        });
    }

    /**
     * @return string
     */
    public function getCacheFile(): string
    {
        return $this->cache_file;
    }

    /**
     * @param string $cache_file
     */
    public function setCacheFile(string $cache_file): void
    {
        $this->cache_file = $cache_file;
    }

    /**
     * @return int
     */
    public function getCacheTimeout(): int
    {
        return $this->cache_timeout;
    }

    /**
     * @param int $cache_timeout
     */
    public function setCacheTimeout(int $cache_timeout): void
    {
        $this->cache_timeout = $cache_timeout;
    }
}
