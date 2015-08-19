# Subscribo transaction plugin using subscribo/omnipay-payunity Omnipay gateway

## Installation in project

Project using this package should include it under `require` key of its composer.json:

```json
    "require": {
        "subscribo/transaction-plugin-payunity": "@dev"
    }
```

You may add `Subscribo\TransactionPluginPayUnity\Integration\Laravel\TransactionPluginPayUnityServiceProvider` to your
project's config/app.php or conditionally to your project's bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\TransactionPluginPayUnity\\Integration\\Laravel\\TransactionPluginPayUnityServiceProvider')) {
        $app->register('\\Subscribo\\TransactionPluginPayUnity\\Integration\\Laravel\\TransactionPluginPayUnityServiceProvider');
    }
```

## Configuration

Your TransactionGatewayConfiguration model pointing to TransactionGateway using driver from this package should contain
configuration of this type (you can set it as array in php):

```json
{
    "initialize": {
        "securitySender": "Your security sender value",
        "transactionChannel": "Your transaction channel value",
        "userLogin": "Your user login value",
        "userPwd": "Your user password",
        "testMode": true/false,
        "registrationMode": true/false,
    },
    "purchase": {
        "brands":"VISA MASTER MAESTRO SOFORTUEBERWEISUNG"
    }
}
```

You may see [`subscribo/omnipay-payunity` documentation](https://github.com/Subscribo/omnipay-payunity) for more options.
