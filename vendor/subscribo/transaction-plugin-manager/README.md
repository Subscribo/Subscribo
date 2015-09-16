# Base package for Subscribo Transaction Plugins

## Installation

Projects and Transaction plugins depending on this package should require it in their composer.json:

```json
    "require": {
        "subscribo/transaction-plugin-manager": "@dev"
    }
```

You also might want to add TransactionPluginManagerServiceProvider in your bootstrap/app.php
(but generally it is not necessary, as any added driver should register it.
```php
    if (class_exists('\\Subscribo\\TransactionPluginManager\\Integration\\Laravel\\TransactionPluginManagerServiceProvider')) {
        $app->register('\\Subscribo\\TransactionPluginManager\\Integration\\Laravel\\TransactionPluginManagerServiceProvider');
    }
```

## Usage

### By transaction plugins

Transaction plugins should use interfaces provided by this package in Interface directory
and may use base classes, provided by this package in Base directory

### By projects

Controllers and other parts of projects may use interfaces and managers provided by this package.

Example code:

```php

use Subscribo\ModelCore\Models\Transaction;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Managers\TransactionProcessingManager;


class SomeController
{
    public method actionProcessChargeTransaction($id, PluginResourceManagerInterface $resourceManager)
    {
        $transaction = Transaction::find($id);
        $driverName = $transaction->transactionGatewayConfiguration->transactionGateway->driver;
        $processingManager = new TransactionProcessingManager($resourceManager, $driverName);

        return $processingManager->charge($transaction);
    }
}

```
