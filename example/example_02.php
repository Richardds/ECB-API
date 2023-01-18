<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;

$ecb = new ECB();
$converter = new ECBConverter($ecb);

$references = [];

try {
    $references = $converter->list(true);
} catch (\Richardds\ECBAPI\ECBException $e) {
    echo $e->getMessage();
}

foreach ($references as $code => $rate) {
    printf("%s => %.5f\n", $code, $rate);
}
