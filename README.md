# ECB-API
European Central Bank EURO exchange PHP API

## Examples
##### EURO to FOREIGN, FOREIGN to EURO
```php
require __DIR__ . '/vendor/autoload.php';

use Richardds\ECBAPI\ECBConverter;

echo ECBConverter::toEuro(150, 'USD', 3) . PHP_EOL;
echo ECBConverter::toForeign(150, 'USD') . PHP_EOL;
print_r(ECBConverter::toForeign(150, ['EUR', 'USD', 'CHF', 'RUB', 'CZK'])) . PHP_EOL;
```
```text
126.968
177.21
Array
(
    [EUR] => 150
    [USD] => 126.96800406298
    [CHF] => 130.68478829064
    [RUB] => 2.1159184778929
    [CZK] => 5.736137667304
)
```

##### Exchange rate list
```php
require __DIR__ . '/vendor/autoload.php';

use Richardds\ECBAPI\ECB;

foreach (ECB::getExchangeReferences() as $reference) {
    $foreign_code = $reference->getCode();
    $rate = $reference->getRate();

    if ($foreign_code == 'EUR') {
        continue;
    }

    printf("1.00 EUR = %.2f %s\n1.00 %s = %.2f EUR\n", $rate, $foreign_code, $foreign_code, (1 / $rate));
}
```
```text
1.00 EUR = 1.18 USD
1.00 USD = 0.85 EUR
1.00 EUR = 130.31 JPY
1.00 JPY = 0.01 EUR
1.00 EUR = 1.96 BGN
1.00 BGN = 0.51 EUR
1.00 EUR = 26.15 CZK
1.00 CZK = 0.04 EUR
...
```

## Composer
```bash
composer require richardds/ecb-api
```
