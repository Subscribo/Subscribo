# Api Client OAuth Package

Package to connect to 3rd party OAuth services for API Client to be used in Frontend Servers

## 1. Installation

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
    "subscribo/apiclientoauth": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ApiClientOAuthServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ApiClientOAuth\\Integration\\Laravel\\ApiClientOAuthServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ApiClientOAuth\\Integration\\Laravel\\ApiClientOAuthServiceProvider')) {
        $app->register('\\Subscribo\\ApiClientOAuth\\Integration\\Laravel\\ApiClientOAuthServiceProvider');
    }
```

### 1.4 To register package routes call method registerRoutes() of ApiClientOAuthServiceProvider

Note: If other package is doing previous steps for you (which is usually the case), they are not necessary.

## 2. Usage

### 2.1 Adding "Login with..." buttons to a blade template

Add following to blade template(s), where you want "Login with..." buttons to be displayed,
(e.g. to resources/views/auth/login.blade.php)
```php
    @include('subscribo::apiclientoauth.loginwithbuttons')
```

Note: "Login with..." template requires that package routes are registered with proper route name (point 1.4)

