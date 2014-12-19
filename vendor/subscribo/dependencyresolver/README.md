# Dependency Resolver Package

Dependency Resolver for resolving dependencies

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" or "require-dev" keys

```json
    "subscribo/dependencyresolver": "@dev"
```

1.3 If you are using Laravel (4.2), you might want to add

```php
    '\\Subscribo\\DependencyResolver\\Support\\Laravel\\DependencyResolverServiceProvider',
```

under 'provider' key in app/config/app.php file.

and

```json
    "subscribo/serviceprovider": "@dev"
```

under "require" or "require-dev" keys in your composer.json

If used with package 'subscribo/schemabuilder', this step (1.3) is not necessary.

## Usage

```php
    DependencyResolver::resolve(array $dependencies)
```
