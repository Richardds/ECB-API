# ECB-API
European Central Bank EURO exchange PHP API

- Supports unofficial exchange reference source (remote or local file)
- Uses local exchange reference cache (can be disabled)

## Installation with Composer
```bash
composer require richardds/ecb-api
```

## Examples

### Currency conversion

```php
use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;

$ecb = new ECB();

$converter = new ECBConverter($ecb);

echo $converter->toEuro(150, 'USD', 1) . PHP_EOL;
echo $converter->toForeign(150, 'GBP') . PHP_EOL;
echo $converter->toForeign(150, 'CZK') . PHP_EOL;
$array = $converter->toEuro(150, ['EUR', 'USD', 'GBP', 'CZK']);
echo var_export($array, true) . PHP_EOL;
```
```text
138.6
131.4
3588.3
array (
  'EUR' => 150.0,
  'USD' => 138.55532976168485,
  'GBP' => 171.23287671232876,
  'CZK' => 6.270378730875345,
)
```

### Exchange rate list

```php
use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;

$ecb = new ECB();

$converter = new ECBConverter($ecb);

// Array or exchange rates
foreach ($converter->list(true) as $code => $rate) {
    printf("1.00 EUR = %.5f %s\n", $rate, $code);
}

// Iterate over array of \Richardds\ECBAPI\Currency objects
foreach ($converter->list() as $currency) {
    printf("1.00 EUR = %.5f %s\n", $currency->getRate(), $currency->getCode());
}
```
```text
1.00 EUR = 1.00000 EUR
1.00 EUR = 1.08260 USD
1.00 EUR = 140.86000 JPY
1.00 EUR = 1.95580 BGN
1.00 EUR = 23.92200 CZK
...
```

### Disable local cache
```php
use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;

$ecb = new ECB();
// Use different local/remote exchange reference
$ecb->setExchangeReferenceSource('../storage/local_exchange_reference.xml');

$converter = new ECBConverter($ecb);

echo $converter->...
```

### Disable local cache
```php
use Richardds\ECBAPI\ECB;
use Richardds\ECBAPI\ECBConverter;

$ecb = new ECB();
// Disable local exchange reference cache
$converter = new ECBConverter($ecb, null);

echo $converter->...
```
