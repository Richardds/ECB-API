<?php

require __DIR__ . '/../vendor/autoload.php';

use Richardds\ECBAPI\ECBConverter;

$a = ECBConverter::toEuro(150, 'USD');
$b = ECBConverter::toEuro(150, ['EUR', 'USD', 'CHF', 'RUB', 'CZK']);
$c = ECBConverter::toForeign(150, 'USD');

print_r($a);
print_r($b);
print_r($c);
