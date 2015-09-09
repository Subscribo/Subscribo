# Subscribo project

Contains packages and other files for building Subscribo API backend as well as Subscribo Frontend servers

## A. Frontend

### 1. Installation

#### 1.1 [Install](http://laravel.com/docs/5.1/installation) Laravel 5.1

In order to install into 'frontend' directory you may use this composer command
(provided you have [composer](http://getcomposer.org) installed and current Laravel version is 5.1):

```sh
    composer create-project laravel/laravel --prefer-dist frontend
```

#### 1.2 [Install and configure](/vendor/subscribo/apiclient/README.md) package Subscribo ApiClient

#### 1.3 For development you can optionally [install](/vendor/subscribo/clientchecker/README.md) package Subscribo ClientChecker

## B. Backend

### 1. Installation

#### 1.1 Clone git project

#### 1.2 Install Homestead / Vagrant box

#### 1.3 Setup Homestead configuration

#### 1.4 Run Vagrant box

#### 1.5 Create (copy from .env.example) and configure .env and .env.commandline files

#### 1.6 Setup database configurations and passwords (both at Vagrant box and in your .env and .env.commandline files)

#### 1.7 When running artisan commands dealing with database from normal (i.e. not vagrant ssh) terminal, you need to set up SUBSCRIBO_ENV environment variable first
```sh
    $ SUBSCRIBO_ENV=commandline
    $ export SUBSCRIBO_ENV
```

#### 1.8 Build generated files
```sh
    $ php artisan build
```

#### 1.9 Run migrations and seed
```sh
    $ php artisan migrate:refresh --seed
```

#### 1.10 Publish vendor files
```sh
    $ php artisan vendor:publish --force
```

#### 1.11 [Setup scheduler](http://laravel.com/docs/5.1/scheduling) in users crontab on Vagrant box
(you need to 'vagrant ssh' first in your vagrant directory)

#### 1.12 Setup job listener on your Vagrant box
```sh
    $ cd to/your/vagrant/config
    $ vagrant ssh
    > cd to/your/project
    > php artisan queue:listen
```

