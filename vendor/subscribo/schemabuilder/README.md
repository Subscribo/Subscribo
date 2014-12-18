# Schema Builder Package

Schema builder for building models, migrations, etc from schema

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" or "require-dev" keys

```json
    "subscribo/schemabuilder": "@dev"
```

1.3 To use with Laravel (4.2) add

```php
    '\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider',
```

under 'provider' key in app/config/app.php file

or

```php
if (class_exists('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider')) {
    App::register('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider');
}
```

in app/start/artisan.php for conditional registration

## Usage

1. Put your schema.yml into root directory of your project

2. Run from command line

```bash
php artisan build
```

