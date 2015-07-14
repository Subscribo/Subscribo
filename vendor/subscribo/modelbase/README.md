# Model Base Package

Model Base provides Eloquent based abstract class to make models extending it

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
    "subscribo/modelbase": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ModelBaseServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider')) {
        $app->register('\\Subscribo\\ModelBase\\Integration\\Laravel\\ModelBaseServiceProvider');
    }
```
