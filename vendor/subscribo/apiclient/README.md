# Api Client Package

Umbrella package for API Client functionality, to be used in Frontend Servers

## 1. Installation

### 1.1 Setup your project's composer.json

Add repository containing this package

```json
    "repositories": [{"type": "composer", "url": "http://your.resource.url"}],
```

Notes:
1. `http://your.resource.url` is just a placeholder for actual url with Subscribo private packages
2. You need to have access to this repository as well as to resources it points to
3. You might find it convenient to place this `repositories` configuration
    in system wide or global composer configuration (e.g. in your `~/.composer/config.json` file)

Set minimum stability to 'dev':

```json
    "minimum-stability": "dev",
```

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/apiclient": "@dev",
```
(Note: you may already include also ` "twbs/bootstrap": "^3.3.5",` dependency,
if you want to [install Twitter Bootstrap](#191-install-bootstrap-composer-package) via Composer

and update composer

```sh
    composer update
```

### 1.3 To register ApiClientServiceProvider with Laravel (5.0 / 5.1)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider')) {
        $app->register('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider');
    }
```

### 1.4 [Configure](../restclient/README.md#14-configuration) Package Subscribo RestClient:

setup token ring to be used (e.g. by setting `SUBSCRIBO_REST_CLIENT_TOKEN_RING=your_token_ring` in appropriate .env file)

If you don't have your token ring, contact your Subscribo Administrator.

You might need to setup also other Rest Client settings if you are not using the defaults
(especially host (`SUBSCRIBO_REST_CLIENT_HOST`)  and protocol (`SUBSCRIBO_REST_CLIENT_PROTOCOL` )
when you are using development host).

### 1.5 [Setup](../apiclientauth/README.md) Package Subscribo ApiClientAuth:

Set driver configuration to 'remote' in config/auth.php:

```php
    'driver' => 'remote',
```

### 1.6 To use default Laravel (5.0 / 5.1) login and registration controllers with ApiClientAuth

exchange in app/Http/Controllers/Auth/AuthController.php original trait AuthenticatesAndRegistersUsers
for trait \Subscribo\ApiClientAuth\Traits\AuthenticatesAndRegistersUsersTrait

### 1.7 Set up [authentication and registration routes](http://laravel.com/docs/5.1/authentication#included-routing)
and [home route](http://laravel.com/docs/5.1/authentication#protecting-routes):

E.g. insert into `app/Http/Routes.php` :
```php
Route::get('/auth/login', '\\App\\Http\\Controllers\\Auth\\AuthController@getLogin');
Route::post('/auth/login', '\\App\\Http\\Controllers\\Auth\\AuthController@postLogin');
Route::get('/auth/logout', '\\App\\Http\\Controllers\\Auth\\AuthController@getLogout');
Route::get('/auth/register', '\\App\\Http\\Controllers\\Auth\\AuthController@getRegister');
Route::post('/auth/register', '\\App\\Http\\Controllers\\Auth\\AuthController@postRegister');
Route::get('/home', ['middleware' => 'auth', function () { return view('home'); }]);
```

Note: we are using here a home template, to be published (among other resources) by next step.

### 1.8 Publish resources:

```sh
php artisan vendor:publish
```

### 1.9 Install [Twitter Bootstrap](http://getbootstrap.com)

There are various ways, how you can install Bootstrap, needed by default templates, e.g.:

#### 1.9.1 Install Bootstrap composer package

add into your projects `composer.json`:
```json
    "twbs/bootstrap": "^3.3.5",
```

and update composer

```sh
    composer update
```

#### 1.9.2 Make it available within `public/css/app.css`

##### 1.9.2.A Either copy and rename compiled Bootstrap css into your public/css directory:

```sh
    cd /to/your/project/root
    mkdir -p public/css
    cp vendor/twbs/bootstrap/dist/css/bootstrap.css public/css
    mv public/css/bootstrap.css public/css/app.css
```

##### 1.9.2.B Or configure Laravel Elixir to include it when compiling app.css

###### 1.9.2.B.1 [Install Laravel Elixir](http://laravel.com/docs/5.1/elixir#installation)

###### 1.9.2.B.2 Add Bootstrap less to Gulp source files:

Modify `gulpfile.js` in your project root, so that it include something like this:

```javascript
elixir(function(mix) {
    mix.less(
        [
            'app.less',
            '../../../vendor/twbs/bootstrap/less/bootstrap.less'
        ]
    )
})

```

###### 1.9.2.B.3 [Run Gulp](http://laravel.com/docs/5.1/elixir#running-elixir):

```sh
    gulp
```

Note: you may ignore Error in notifier: `Error in plugin 'gulp-notify'`
