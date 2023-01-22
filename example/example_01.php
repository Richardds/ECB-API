<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;
use Richardds\ECBAPI\ECBException;

$ecb = new ECB();

$converter = new ECBConverter($ecb);

try {
    $a = $converter->toEuro(150, 'USD', 1);
    $b = $converter->toForeign(150, 'GBP');
    $c = $converter->toForeign(150, 'CZK');
    $d = $converter->toEuro(150, ['EUR', 'USD', 'GBP', 'CZK']);

    echo var_export($a, true) . PHP_EOL;
    echo var_export($b, true) . PHP_EOL;
    echo var_export($c, true) . PHP_EOL;
    echo var_export($d, true) . PHP_EOL;
} catch (ECBException $e) {
    echo $e->getMessage();
}
