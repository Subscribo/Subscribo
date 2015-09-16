<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

if (class_exists('\\Subscribo\\Config\\Integration\\Laravel\\ConfigServiceProvider')) {
    $app->register('\\Subscribo\\Config\\Integration\\Laravel\\ConfigServiceProvider');
}

if (class_exists('\\Subscribo\\ApiServer\\Integration\\Laravel\\ApiServerServiceProvider')) {
    $app->register('\\Subscribo\\ApiServer\\Integration\\Laravel\\ApiServerServiceProvider');
}

if (class_exists('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider')) {
    $app->register('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider');
}

if (class_exists('\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\DevelopmentSeederServiceProvider')) {
    $app->register('\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\DevelopmentSeederServiceProvider');
}

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

if (class_exists('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider')) {
    $app->register('\\Subscribo\\Exception\\Integration\\Laravel\\ApiExceptionHandlerServiceProvider');
    $app->singleton(
        'Illuminate\Contracts\Debug\ExceptionHandler',
        'Subscribo\\Exception\\Interfaces\\ExceptionHandlerInterface'
    );
} else {

    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );
}
if (class_exists('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider')) {
    $app->register('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider');
}

if (class_exists('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider')) {
    $app->register('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider');
}

if (class_exists('\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider')) {
    $app->register('\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider');
}

if (class_exists('\\Subscribo\\Webshop\\Integration\\Laravel\\WebshopServiceProvider')) {
    $app->register('\\Subscribo\\Webshop\\Integration\\Laravel\\WebshopServiceProvider');
}

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
