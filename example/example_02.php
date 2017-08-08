<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECB;

foreach (ECB::getExchangeReferences() as $reference) {
    $foreign_code = $reference->getCode();
    $rate = $reference->getRate();

    if ($foreign_code == 'EUR') {
        continue;
    }

    printf("1.00 EUR = %.2f %s\n1.00 %s = %.2f EUR\n", $rate, $foreign_code, $foreign_code, (1 / $rate));
}
