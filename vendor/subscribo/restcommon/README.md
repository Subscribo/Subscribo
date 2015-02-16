# Subscribo REST Common Package

Auxiliary package containing files common for Subscribo REST client and server (Exception definitions and RestCommon class)

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json:

(note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add dependency to this package to your project's composer.json under "require" key

```json
    "subscribo/restcommon": "@dev"
```

### 1.3 If you want to use CommonSecretProvider and CommonSecretProviderInterface, it is advisable to add

```php
    '\\Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider',
```

under 'providers' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider')) {
        $app->register('\\Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider');
    }
```

in bootstrap/app.php for conditional registration


Note: If used with package adding this dependency for you, no installation is necessary.

## 2. Usage

### 2.1 Signing Request

- instantiate Subscribo\\RestCommon\\Signer class with your tokenRing (as string, array or TokenRing object) as a parameter
- before sending your request add signature header using modifyHeaders Signer method

### 2.2 Checking incoming request

- if your signatures are encrypted with common secret, instantiate an instance of CommonSecretProviderInterface
(if you have registered CommonSecretServiceProvider, App::make('Subscribo\\RestCommon\\Interfaces\\CommonSecretProviderInterface')
should to it for you (with common secret defined in proper .env file, under key SUBSCRIBO_COMMON_SECRET))
and invoke method getCommonSecretEncrypter() which will give you an instance of Encrypter contract to be used in next step

- use Signature::verifyRequest method with Request as a first parameter,
  as a second parameter callable converting tokens to instances of TokenRingProviderInterface
     (this is basically a function / method searching in UserToken model by token and optionally tokenType
      and returning a UserToken model implementing TokenRingProviderInterface very simply)
  third parameter is encrypter with common secret (see previous point - you will need this if you are using common secret)
  other parameters are optional:
  - you can enforce a specific signature type
  - add extra data and options (not used now)
  - and for throwing an exception instead of returning a false/null value set last parameter to true


