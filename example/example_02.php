<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECB;

$references = [];

try {
    $references = ECB::getExchangeReferences();
} catch (\Richardds\ECBAPI\ECBException $e) {
    echo $e->getMessage();
}

foreach ($references as $reference) {
    $code = $reference->getCode();
    $rate = $reference->getRate();

    if ($code == 'EUR') {
        continue;
    }

    printf("1.00 EUR = %.2f %s\n1.00 %s = %.2f EUR\n", $rate, $code, $code, (1 / $rate));
}
