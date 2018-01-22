<?php

namespace Richardds\ECBAPI;

use Exception;
use InvalidArgumentException;
use Throwable;

class ECBException extends Exception
{
    const UNDEFINED = 0;
    const DATA_DOWNLOAD_FAILED = 1;
    const DATA_PARSE_FAILED = 2;
    const INVALID_DATA = 3;
    const CONVERT_FAILED = 4;

    /**
     * ECBException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = self::UNDEFINED, Throwable $previous = null)
    {
        $msg = '';
        switch ($code) {
            case self::UNDEFINED:
                break;
            case self::DATA_DOWNLOAD_FAILED:
                $msg = 'Failed to download data from ECB';
                break;
            case self::DATA_PARSE_FAILED:
                $msg = 'Failed to parse ECB data';
                break;
            case self::INVALID_DATA:
                $msg = 'ECB data are invalid';
                break;
            case self::CONVERT_FAILED:
                $msg = 'Failed to convert amount to target currency';
                break;
            default:
                throw new InvalidArgumentException('Invalid error code');
        }

        if (!empty($message)) {
            $msg .= (!empty($msg) ? ' <= ' : '') . $message;
        }

        parent::__construct($msg, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
