# Client Checker Package

Client Checker for testing communicating with API from a browser.

## 1. Installation

### 1.1 Setup your project's composer.json

(if it is not setup already)

Add repository containing this package

```json
    "repositories": [{"type": "composer", "url": "http://your.resource.url"}],
```

(Note: you need to have access to this repository as well as to resources it points to)

Set minimum stability to 'dev':

```json
    "minimum-stability": "dev"
```
### 1.2 Add dependency on this package to your project's composer.json under "require" or "require-dev" keys

```json
    "subscribo/clientchecker": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To use with Laravel 5.0

add

```php
    '\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider',
```

under 'providers' key in config/app.php file.

or (for conditional inclusion) add

```php
if (class_exists('\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider')) {
    $app->register('\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider');
}
```

to bootstrap/app.php or to another convenient place

### 1.4 If you have [installed and configured](../apiclient/README.md) package Subscribo ApiClient, you are done,
 otherwise you might need to [configure](../restproxy/README.md) package Subscribo RestProxy (steps 1.4 and 1.5)

## 2. Usage

### 2.1 Navigate your browser to /client url of your domain

(or whatever url is defined in your config files)

### 2.2 Click on "Initialize"

in order to populate the select with available API endpoints

### 2.3 Select or fill in Verb, Select or fill in URL, optionally fill in json in Request Body and Click "Reload"

### 2.4 You can click "Display"

in order to interpret (possible) HTML response
