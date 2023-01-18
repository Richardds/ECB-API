<?php declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;
use Richardds\ECBAPI\ECBException;

final class CurrencyConversionTest extends TestCase
{
    /**
     * @throws ECBException
     */
    public function testCurrencyConversion(): void
    {
        $ecb = new ECB();
        $ecb->setExchangeReferenceUrl(__DIR__ . '/data/eurofxref-daily-2023-01-17.xml');

        $converter = new ECBConverter($ecb);

        $result = $converter->toEuro(0.89, 'USD', 3);

        $this->assertEquals(0.821, $result);
    }
}
