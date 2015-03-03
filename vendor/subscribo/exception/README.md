# Subscribo Exception Package

contain shared functionality connected to exceptions and their handling

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add to your project's composer.json dependency on this package under "require" key

```json
    "subscribo/exception": "@dev",
```

(Note: do not add trailing comma if it is the last item listed)

### 1.3 If using Exception Handler functionality, you might need to add (to the same place) also packages suggested in composer.json

(with "@dev" version specification), especially:

```json
    "subscribo/environment": "@dev",
    "subscribo/serviceprovider": "@dev",
```

### 1.4 If you are using Laravel (5.0), you might want to register ApiExceptionHandlerServiceProvider:

To do so, add

```php
    '\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider',
```

under 'providers' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider')) {
        $app->register('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, respective steps might not be necessary.

## 2. Usage

### 2.1 Exceptions, Interfaces, Traits

You can throw Exceptions, as necessary, paying attention to signature of their constructors

#### 2.1.1 ContainDataInterface

Subscribo version of HttpException (serving as a base class of other Exception classes marking an Http Error)
is implementing ContainDataInterface, allowing it and its child classes to carry extended information which could be provided to http client.

If you want to implement ContainDataInterface in other classes, using ContainDataTrait might help you to do so, but you will probably need to set the data via constructor or setter and add a $_containedData = array() property
(see phpDoc in ContainDataTrait or implementation in HttpException)

#### 2.1.2 MarkableExceptionInterface

MarkableExceptionInterface is denoting classes, which could be marked with unique mark (e.g. during logging) and this mark could be also provided to http client,
in order to make it possible to trace up a particular occurrence of an Exception.

If you want to implement it, you can use MarkableExceptionTrait, or wrap your exception in MarkingException.

You can use MarkableExceptionFactory::mark(Exception $e) to mark or wrap an Exception.

### 2.2 Exception Handler

ApiExceptionHandler is implementing both ExceptionHandlerContract (Laravel) and ExceptionHandlerInterface (Subscribo)
allowing it to be used instead of default Laravel ExceptionHandler or in try/catch structures in controllers / routes / etc.

#### 2.2.1 Replacing default Laravel ExceptionHandler

To replace Laravel Exception Handler with this one, you can replace reference to 'App\Exceptions\Handler' with 'Subscribo\\Exception\\Handlers\\ApiExceptionHandler'
or with 'Subscribo\\Exception\\Interfaces\\ExceptionHandlerInterface'
(this second option is available if you have registered '\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider')

in bootstrap/app.php:
```php
$app->singleton(
    'Illuminate\Contracts\Debug\ExceptionHandler',
    'Subscribo\\Exception\\Interfaces\\ExceptionHandlerInterface'
);

```

#### 2.2.2 Direct usage

To use it within controller / route / etc, return result of handle() method.
If you have it available, you can provide also Request object to it.

Note: as ApiExceptionHandler need an instance of Logger in its construction, you might want to use DependencyInjectionContainer in it's instantiation,
or, if you have registered '\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider', you can simply use Facade:

```php

    try {
        //do something
        return $response;
    } catch (\Exception $e) {
        return \Subscribo\ApiExceptionHandler::handle($e, $request);
    }

```
