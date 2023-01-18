<?php

namespace Richardds\ECBAPI;

use Exception;
use InvalidArgumentException;
use Throwable;

class ECBException extends Exception
{
    public const UNDEFINED = 0;
    public const DATA_DOWNLOAD_FAILED = 1;
    public const DATA_PARSE_FAILED = 2;
    public const INVALID_DATA = 3;
    public const CONVERT_FAILED = 4;
    public const INVALID_URL = 5;

    /**
     * ECBException constructor.
     *
     * @param int $code
     * @param string $details
     * @param Throwable|null $previous
     */
    public function __construct(int $code = self::UNDEFINED, string $details = '', Throwable $previous = null)
    {
        $message = '';
        switch ($code) {
            case self::UNDEFINED:
                break;
            case self::DATA_DOWNLOAD_FAILED:
                $message = 'Failed to download exchange reference data from ECB';
                break;
            case self::DATA_PARSE_FAILED:
                $message = 'Failed to parse exchange reference data';
                break;
            case self::INVALID_DATA:
                $message = 'Invalid exchange reference data';
                break;
            case self::CONVERT_FAILED:
                $message = 'Failed to convert the given amount to the target currency';
                break;
            case self::INVALID_URL:
                $message = 'Invalid exchange reference URL';
                break;
            default:
                throw new InvalidArgumentException('Invalid error code');
        }

        if (!empty($details)) {
            $message .= ' (' . $details . ')';
        }

        parent::__construct($message, $code, $previous);
    }
}
