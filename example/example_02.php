<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;
use Richardds\ECBAPI\ECBException;

$ecb = new ECB();

$converter = new ECBConverter($ecb);

try {
    // Iterate over array or exchange rates
    foreach ($converter->list(true) as $code => $rate) {
        printf("1.00 EUR = %.5f %s\n", $rate, $code);
    }

    // Iterate over array of \Richardds\ECBAPI\Currency objects
    foreach ($converter->list() as $currency) {
        printf("1.00 EUR = %.5f %s\n", $currency->getRate(), $currency->getCode());
    }
} catch (ECBException $e) {
    echo $e->getMessage();
}
