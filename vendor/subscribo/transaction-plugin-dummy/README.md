# Dummy Transaction Plugin

For development and testing

## Installation

### Require this package in your composer.json:

```json
{
    "require-dev": {
        "subscribo/transaction-plugin-dummy": "@dev"
    }
}
```

### Register `TransactionPluginDummyServiceProvider` e.g. by adding into your `bootstrap/app.php`:

```php
    if (class_exists('\\Subscribo\\TransactionPluginDummy\\Integration\\Laravel\\TransactionPluginDummyServiceProvider')) {
        $app->register('\\Subscribo\\TransactionPluginDummy\\Integration\\Laravel\\TransactionPluginDummyServiceProvider');
    }

```

### Add Dummy plugin into your database, e.g. by adding into `TransactionGatewaySeeder`:

```php
    use Subscribo\TransactionPluginDummy\Drivers\SuccessForAllDriver;

    //... in function run()
        $dummySuccess = TransactionGateway::firstOrCreate(['identifier' => 'DUMMY-SUCCESS_FOR_ALL']);
        $dummySuccess->driver = SuccessForAllDriver::getDriverIdentifier();
        $dummySuccess->translateOrNew('en')->name = 'Development Dummy';
        $dummySuccess->translateOrNew('en')->description = 'Dummy - all transaction passed';
        $dummySuccess->save();
```

### Add Dummy plugin configuration for service into your database, e.g. by adding into `TransactionGatewayConfigurationSeeder::run()`:

```php
    $dummySuccess = TransactionGateway::query()->where(['identifier' => 'DUMMY-SUCCESS_FOR_ALL'])->first();

    //...

    $dummyConfigForMain = TransactionGatewayConfiguration::firstOrCreate([
        'service_id' => $mainService->id,
        'transaction_gateway_id' => $dummySuccess->id,
    ]);
    $dummyConfigForMain->configuration = [];
    $dummyConfigForMain->ordering = 100;
    $dummyConfigForMain->save();

```
