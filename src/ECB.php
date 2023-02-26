<?php

namespace Richardds\ECBAPI;

class ECB
{
    /**
     * Official ECB daily exchange reference URL
     */
    public const DEFAULT_ECB_REFERENCE_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * URL of daily exchange reference data
     */
    protected string $exchange_reference_source;

    /**
     * @param string $exchange_reference_source URL or file path of the daily exchange reference
     */
    public function __construct(string $exchange_reference_source = self::DEFAULT_ECB_REFERENCE_URL)
    {
        $this->exchange_reference_source = $exchange_reference_source;
    }

    /**
     * cURL wrapper to download remote resource
     *
     * @param string $url URL of the resource
     * @return string resource content
     * @throws ECBException
     */
    public static function fetch(string $url): string
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($handle);

        if ($data === false) {
            $curl_error = curl_error($handle);
            curl_close($handle);
            throw new ECBException(ECBException::DATA_FETCH_FAILED, 'cURL error: ' . $curl_error);
        }

        $http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($http_code !== 200) {
            curl_close($handle);
            throw new ECBException(ECBException::DATA_FETCH_FAILED, 'cURL status code != 200');
        }

        curl_close($handle);

        return $data;
    }

    /**
     * Retrieve latest exchange reference data
     *
     * @return Currency[]
     * @throws ECBException
     */
    public function getExchangeReferences(): array
    {
        $exchange_references = [];

        if (preg_match('/^https?:\/\//', $this->exchange_reference_source) === 1) {
            $raw_xml_data = self::fetch($this->exchange_reference_source);
        } else {
            $raw_xml_data = file_get_contents($this->exchange_reference_source);

            if ($raw_xml_data === false) {
                throw new ECBException(ECBException::DATA_FETCH_FAILED, 'Failed to get file contents');
            }
        }

        $xml = simplexml_load_string($raw_xml_data);

        if ($xml === false) {
            throw new ECBException(ECBException::DATA_PARSE_FAILED);
        }

        $exchange_references['EUR'] = new Currency('EUR', 1);

        foreach ($xml->Cube->Cube->Cube as $row) {
            $code = (string)($row['currency'] ?? '');
            $rate = (float)($row['rate'] ?? 0);

            if (empty($code) || strlen($code) !== 3) {
                throw new ECBException(ECBException::DATA_PARSE_FAILED, 'Currency code is invalid');
            }

            if ($rate <= 0) {
                throw new ECBException(ECBException::DATA_PARSE_FAILED, 'Currency rate is invalid');
            }

            $exchange_references[$code] = new Currency($code, $rate);
        }

        return $exchange_references;
    }

    public function getExchangeReferenceSource(): string
    {
        return $this->exchange_reference_source;
    }

    public function setExchangeReferenceSource(string $exchange_reference_source): void
    {
        $this->exchange_reference_source = $exchange_reference_source;
    }
}
