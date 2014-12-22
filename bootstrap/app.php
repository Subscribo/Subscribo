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

$app->singleton(
	'Illuminate\Contracts\Http\Kernel',
	'App\Http\Kernel'
);

$app->singleton(
	'Illuminate\Contracts\Console\Kernel',
	'App\Console\Kernel'
);

$app->singleton(
	'Illuminate\Contracts\Debug\ExceptionHandler',
	'App\Exceptions\Handler'
);

if (class_exists('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider')) {
    $app->register('\\Subscribo\\SchemaBuilder\\SchemaBuilderServiceProvider');
}

if (class_exists('\\Subscribo\\Api0\\Api0ServiceProvider')) {
    $app->register('\\Subscribo\\Api0\\Api0ServiceProvider');
}

if (class_exists('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider')) {
    $app->register('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider');
}

if (class_exists('\\Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider')) {
    $app->register('\\Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider');
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
