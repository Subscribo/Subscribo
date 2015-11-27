# Package to make it easier to integrate Subscribo Thick Client into Laravel Spark

Note: Package now contains only work-in-progress, very basic integration functionality


### Installation

#### Install Laravel Spark

#### Require this package in your project's `composer.json`

```json
    "require": {
        "subscribo/integration-to-spark": "@dev"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
```

```sh
    $ composer update
```

#### Register `IntegrationToSparkServiceProvider`

e.g. in  `providers` of `config/app.php`

or in your project's `bootstrap/app.php`:

```php
    if (class_exists('\\Subscribo\\IntegrationToSpark\\Integration\\Laravel\\IntegrationToSparkServiceProvider')) {
        $app->register('\\Subscribo\\IntegrationToSpark\\Integration\\Laravel\\IntegrationToSparkServiceProvider');
    }
```

#### Publish and run migrations:

```sh
    php artisan vendor:publish --tag=migrations
    php artisan migrate
```

#### Optionally publish views

Note: you might want to specify only some ServiceProviders, following way you publish also views from other packages:

```sh
    php artisan vendor:publish --tag=view
```

#### Specify environment variables needed to connect to Subscribo API

e.g. by specifying in `.env` file of your project values for keys listed in [`docs/.env.example`](docs/.env.example)

#### Add Plugin View into some Spark tab:

In `app/Providers/SparkServiceProvider.php` add following e.g. to method `customizeSettingsTab()`
(as one of items in returned array of anonymous function there):

```php
    $tabs->make('Subscribo', 'subscribo::integration-to-spark.plugin', ''),
```
