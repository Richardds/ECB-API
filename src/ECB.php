<?php

namespace Richardds\ECBAPI;
use Illuminate\Support\Facades\Cache;

/**
 * Class ECB
 * @package Richardds\ECBAPI
 */
class ECB
{
    /**
     * Default ECB url for daily exchange reference update
     *
     * @var string
     */
    protected const EXCHANGE_REFERENCE_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    protected const EXCHANGE_CACHE_HOURS = 1; // may be false to disable caching
    //another logic my be required for next day (for example if they publish rates at midnight and you set cache for 3 hours your system may be using till 3am old rates) - thats why Im going to use 1 hour.
   
    /**
     * @return Currency[]
     * @throws ECBException
     */
    public static function getExchangeReferences(): array
    {
    // Attempt to retrieve the data from cache
        $cacheKey = 'exchange_references';
        $cachedReferences = Cache::get($cacheKey);

        if (self::EXCHANGE_REFERENCE_URL && $cachedReferences !== null) {
            return $cachedReferences;
        }

    // Fetch data from ECB if not in cache
        $raw_xml_data = self::fetch(self::EXCHANGE_REFERENCE_URL);

        if (($xml = simplexml_load_string($raw_xml_data)) !== false) {
            $exchange_references = [];
            $exchange_references['EUR'] = new Currency('EUR', 1.0000);

            foreach ($xml->Cube->Cube->Cube as $row) {
                $code = (string)($row['currency'] ?? '');
                $rate = (double)($row['rate'] ?? 0);

                if (empty($code) || strlen($code) !== 3) {
                    throw new ECBException('Currency code is invalid', ECBException::DATA_PARSE_FAILED);
                }

                if ($rate <= 0) {
                    throw new ECBException('Currency rate is invalid', ECBException::DATA_PARSE_FAILED);
                }

                $exchange_references[$code] = new Currency($code, $rate);
            }

            Cache::put($cacheKey, $exchange_references, now()->addHours(self::EXCHANGE_REFERENCE_URL));

            return $exchange_references;
        }

        throw new ECBException('', ECBException::DATA_PARSE_FAILED);
    }

    /**
     * @param string $url
     * @return string
     * @throws ECBException
     */
    private static function fetch(string $url): string
    {
        $ch = curl_init($url . '?' . uniqid('', true));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (($data = @curl_exec($ch)) !== false) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code != 200) {
                curl_close($ch);
                throw new ECBException('HTTP_CODE != 200', ECBException::DATA_DOWNLOAD_FAILED);
            }

            curl_close($ch);

            return $data;
        }

        $curl_error = curl_error($ch);
        curl_close($ch);

        throw new ECBException($curl_error, ECBException::DATA_DOWNLOAD_FAILED);
    }
}
