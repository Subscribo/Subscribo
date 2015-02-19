# Model Core

Subscribo Model Core contain core models to be used by API servers

## 1. Installation

Note: If another package is adding dependency and registering service providers, steps 1.1-1.3 are not necessary

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
    "subscribo/modelcore": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ModelCoreServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ModelCore\\Integration\\Laravel\\ModelCoreServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ModelCore\\Integration\\Laravel\\ModelCoreServiceProvider')) {
        $app->register('\\Subscribo\\ModelCore\\Integration\\Laravel\\ModelCoreServiceProvider');
    }
```

## 2. Building models

### 2.1 [Install](../schemabuilder/README.md) Package Subscribo SchemaBuilder

### 2.2 To copy [schema.yaml](/src/modelschema/schema.yaml) (and possibly other model schema marked files from other packages) to subscribo/config/packages/schemabuilder so that Subscribo SchemaBuilder can read it, run

```sh
    php artisan vendor:publish --tag modelschema
```

or (to force overwrite and for only this package)

```sh
    php artisan vendor:publish --tag="modelschema" --provider="\\Subscribo\\ModelCore\\Integration\\Laravel\\ModelCoreServiceProvider" --force
```



### 2.3 To make Subcribo SchemaBuilder rebuild models, run

```sh
   php artisan build
```

or (to automatically force all files tagged with modelschema to be published) run


```sh
   php artisan build --publish
```


