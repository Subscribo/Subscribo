# Translatable Model Base Package

Translatable Model Package helps internationalizing and localizing of Laravel Eloquent models

This package extends functionality of package dimsav/laravel, allowing multiple fallback locales per locale

## 1. Installation

Note: If another installed package is already dependent on this package (which is usually the case), installation is not necessary

### 1.1 Setup your project's composer.json

Add repository containing this package

```json
    "repositories": [{"type": "composer", "url": "http://your.resource.url"}],
```

(Note: you need to have access to this repository as well as to resources it points to)

Set minimum stability to 'dev':

```json
    "minimum-stability": "dev"
```

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/translatablemodel": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 Trait TranslatableModelTrait expects:
    1. function app() defined and 2. returning instance of  Subscribo\TranslatableModel\Interfaces\LocaleConfigurationInterface when calling
    app('Subscribo\\TranslatableModel\\Interfaces\\LocaleConfigurationInterface');

    1. should be fulfilled by laravel installation,
    for 2. you can create class implementing Subscribo\TranslatableModel\Interfaces\LocaleConfigurationInterface
and register it as implementing this interface in Service Container of Laravel (preferably in some ServiceProvider)


## 2. Usage

### 2.1 Properly configure your models and migrations (you can see https://github.com/dimsav/laravel-translatable for more details)

### 2.2 Use Subscribo\TranslatableModel\Traits\TranslatableModelTrait in your models

```php
use \Subscribo\TranslatableModel\Traits\TranslatableModelTrait;
```
