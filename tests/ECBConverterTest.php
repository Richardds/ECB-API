<?php declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;

final class ECBConverterTest extends TestCase
{
    private const LOCAL_EXCHANGE_REFERENCE_PATH = __DIR__ . '/data/eurofxref-daily-local.xml';

    private ECB $ecb;

    private ECBConverter $converter;

    public function setUp(): void
    {
        $this->ecb = new ECB();
        $this->ecb->setExchangeReferenceSource(self::LOCAL_EXCHANGE_REFERENCE_PATH);

        $this->converter = new ECBConverter($this->ecb);
    }

    /**
     * @throws \Richardds\ECBAPI\ECBException
     */
    public function testHasExchangeReference(): void
    {
        $references = $this->ecb->getExchangeReferences();
        $this->assertCount(31, $references);
    }

    /**
     * @return float[][]
     */
    public function provideConversionData(): array
    {
        return [
            [1.00, 0.92, 'USD'],
            [0.89, 0.82, 'USD'],
            [5.50, 5.08, 'USD'],

            [1.00, 1.14, 'GBP'],
            [0.89, 1.02, 'GBP'],
            [5.50, 6.28, 'GBP'],
        ];
    }

    /**
     * @dataProvider provideConversionData
     * @throws \Richardds\ECBAPI\ECBException
     */
    public function testCurrencyConversionResult(float $from, float $to, string $currency): void
    {
        $result = $this->converter->toEuro($from, $currency, 2);
        $this->assertEquals($to, $result);

        $result = $this->converter->toForeign($result, $currency, 2);
        $this->assertEquals($from, $result);
    }
}
