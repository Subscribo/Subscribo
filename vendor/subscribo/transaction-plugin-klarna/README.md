# Subscribo transaction plugin employing subscribo/omnipay-klarna Omnipay gateway

## Installation

Require this package in your project's composer.json:

```json
    "require": {
        "subscribo/transaction-plugin-klarna"
    }
```

Add `Subscribo\TransactionPluginKlarna\Integration\Laravel\TransactionPluginKlarnaServiceProvider` under providers key
in your project's config/app.php

or conditionally in your project's bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\TransactionPluginKlarna\\Integration\\Laravel\\TransactionPluginKlarnaServiceProvider')) {
        $app->register('\\Subscribo\\TransactionPluginKlarna\\Integration\\Laravel\\TransactionPluginKlarnaServiceProvider')
    }
```

## Configuration

Your TransactionGatewayConfiguration model pointing to TransactionGateway having drivers from this package should
contain this type of configuration (you may set it as array in php)

```json
    {
        "initialize": {
            "merchantId": "Your merchant ID",
            "sharedSecret: "Your shared secret",
            "locale": "language_country",
            "testMode": true/false
        }
    }
```

You may see [`subscribo/omnipay-klarna` documentation](https://github.com/Subscribo/omnipay-klarna) for more options.
