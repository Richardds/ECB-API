<?php declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Richardds\ECBAPI\Currency;
use Richardds\ECBAPI\ECB;

class ECBTest extends TestCase
{
    /**
     * @var ECB
     */
    private $ecbMock;

    public function setUp(): void
    {
        $this->ecbMock = $this->getMockBuilder(ECB::class)
                              ->onlyMethods(['getExchangeReferences'])
                              ->getMock();

        // Return exchange reference from eurofxref-daily-mock.xml
        $this->ecbMock->method('getExchangeReferences')
                      ->willReturn([
                          new Currency('USD', 1.0826),
                          new Currency('GBP', 0.876),
                      ]);
    }

    public function testHasDefaultExchangeReferenceSource(): void
    {
        $source = $this->ecbMock->getExchangeReferenceSource();
        $this->assertEquals(ECB::DEFAULT_ECB_REFERENCE_URL, $source);
    }

    /**
     * @throws \Richardds\ECBAPI\ECBException
     */
    public function testHasExchangeReference(): void
    {
        $references = $this->ecbMock->getExchangeReferences();
        $this->assertCount(2, $references);
    }
}
