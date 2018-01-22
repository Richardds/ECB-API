<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECBConverter;

$converter = new ECBConverter();

try {
    $a = $converter->toEuro(150, 'USD', 1);
    $b = $converter->toEuro(150, ['EUR', 'USD', 'CHF', 'RUB', 'CZK']);
    $c = $converter->toForeign(150, 'USD');
} catch (\Richardds\ECBAPI\ECBException $e) {
    echo $e->getMessage();
}

echo var_export($a, true) . PHP_EOL;
echo var_export($b, true) . PHP_EOL;
echo var_export($c, true) . PHP_EOL;
