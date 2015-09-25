# Subscribo project

Contains packages and other files for building Subscribo API backend as well as Subscribo Frontend servers

## Outline

 * A.1 [Frontend server installation](#a-frontend)
 * B.1.i [Backend server installation on Homestead](#b1i-installation-for-development-using-homestead-box-on-vagrant)
 * B.1.ii [Backend server installation on web server](#b1ii-installing-and-configuring-for-staging-or-production-using-web-server)
 * C.1 [Local Satis server configuration](#c-satis)

## A. Frontend

### A.1. Installation

You need to decide following:

 * where you want to put your frontend project: `/path/to/your/frontend/project/` = `path/to/your/project/parent/your_frontend_project_subdirectory_name`
 * host name of your frontend: `frontend.hostname`

##### A.1.1 Setup and/or configure your Web Server, Virtual Server or [Homestead box](http://laravel.com/docs/5.1/homestead)

Notes:
 * You do not need DB for frontend.
 * If you already have had Vagrant box configured and updated its configuration, you might need to run `vagrant reload --provision` to let vagrant box find your new sites
 * Add new record with `frontend.hostname` to your `/etc/hosts` if needed (you might find IP address needed for new record in `path/to/your/homestead/configuration/Homestead.yaml` under key `ip`)

##### A.1.2 [Install](http://laravel.com/docs/5.1/installation) Laravel 5.1

In order to install into 'frontend' directory you may use this composer command
(provided you have [composer](http://getcomposer.org) installed and current Laravel version is 5.1):

```sh
    composer create-project laravel/laravel --prefer-dist your_frontend_project_subdirectory_name
```

##### A.1.3 [Install and configure](/vendor/subscribo/apiclient/README.md) package Subscribo ApiClient

##### A.1.4 For development you can optionally [install](/vendor/subscribo/clientchecker/README.md) package Subscribo ClientChecker

##### A.1.5 Set environment variables

In development you may use .env.frontend file generated during
[Backend Installation](#b1i-installation-for-development-using-homestead-box-on-vagrant)
for setting up environment variables:

```sh
    $ cp /path/to/your/backend/project/.env.frontend /path/to/your/frontend/project/.env
    $ vi /path/to/your/frontend/project/.env # Modify as needed (see below)
```

Only following environment keys are needed for frontend (you may delete the rest):

```sh
    APP_ENV=local
    APP_DEBUG=true
    APP_KEY=SomeRandomString32CharactersLong # you may reset this via php artisan key:generate

    SUBSCRIBO_REST_CLIENT_PROTOCOL=http
    SUBSCRIBO_REST_CLIENT_HOST=frontend.hostname
    SUBSCRIBO_REST_CLIENT_TOKEN_RING=simple_some_long_hash

    CACHE_DRIVER=file
    SESSION_DRIVER=file
    QUEUE_DRIVER=sync
```

Frontend server does not use database, so database setting are not needed.

It takes needed information from backend, for which it needs to have set connection correctly:
`SUBSCRIBO_REST_CLIENT_PROTOCOL`, `SUBSCRIBO_REST_CLIENT_HOST` and especially `SUBSCRIBO_REST_CLIENT_TOKEN_RING`.

####### Finding out your Token ring

If `SUBSCRIBO_REST_CLIENT_TOKEN_RING` is not set to string starting with 'simple_'
or you are getting `Internal Server Error` when trying to access any frontend page communicating with backend
and log connected to that particular error is having something like 'Unauthorized' or 'TokenConfigurationException'
you need to set this value properly.

You might find the proper token ring in Backend server database,
table `user_tokens`, field `token_ring`.
Be sure you select correct row, i.e. row having type `SubscriboDigest`
connected to correct user - i.e. user having type `server` and connected to your service.

(First find ID of your service in `services` table - check that `url` of your service agrees with `frontend.hostname`,
then find ID of user of type `server` connected to your service ID,
finally find appropriate user token in table `user_tokens` having type SubscriboDigest and connected to your user,
and copy over token_ring to `.env` file.)

You may also use following sql (modify its end for service with different identifier):

```sql
    SELECT `token_ring` FROM `user_tokens` JOIN `users` ON `user_tokens`.`user_id` = `users`.`id` JOIN `services` ON `users`.`service_id` = `services`.`id` WHERE `user_tokens`.`type` = "SubscriboDigest" AND `users`.`type` = "server" AND `services`.`identifier` = "FRONTEND"
```

## B. Backend

### B.1. Installation

#### B.1.i Installation for development using [Homestead box](http://laravel.com/docs/5.1/homestead) on Vagrant

You need to decide following:

 * where you want to put your project: `path/to/your/project` = `path/to/your/project/parent/your_project_subdirectory_name`
 * where you want to put your Homestead configuration: `path/to/your/homestead/configuration`
 * host name of your project: `backend.hostname`

Optionally:
 * Do you want to also make additional frontend?
 * If so, host name of additional frontend: `frontend.hostname`

##### B.1.i.1 Change to directory, which should be parent directory of your project

```sh
    $ cd path/to/your/project/parent
```

##### B.1.i.2 Clone the git project into a new (nonexistent) or empty subdirectory

```sh
    $ git clone https://github.com/Subscribo/Subscribo your_project_subdirectory_name
```

##### B.1.i.3 Create `.env` file using `.env.example` as a template

```sh
    $ cd path/to/your/project/
    $ cp .env.example .env
    $ vi .env                        # modify as needed
```

Notes:
* Setup DB access details, if you have changed them in your vagrant box or created a special DB for this project
* Do not forget to setup 'SUBSCRIBO_REST_CLIENT_HOST' to hostname accessible from host
  (this is actually the host name of your backend server - `backend.hostname`;
   you need to put this also under key `sites:` in your `Homestead.yaml`)

##### B.1.i.4 Install [Vagrant](https://www.vagrantup.com) and [Homestead](http://laravel.com/docs/5.1/homestead)

##### B.1.i.5 Configure and run your Homestead box (and modify your `/etc/hosts` if needed)

```sh
    $ cd path/to/your/homestead/configuration
    $ vi Homestead.yaml
    $ vagrant up
    $ sudo vi /etc/hosts
```

##### B.1.i.6 Run `install_backend.sh` from within your Homestead box

```sh
    $ cd path/to/your/homestead/configuration
    $ vagrant ssh
    vagrant@homestead:$ cd path/where/your/project/is/mapped
    vagrant@homestead:$ source bin/install_backend.sh test
```

Note: instead of running `install_backend.sh` via `source` command, you may run it as a script,
(which has an advantage on stopping on first error).
To do so, run these commands **instead** of last line in previous script:

```sh
    vagrant@homestead:$ chmod +x bin/install_backend.sh
    vagrant@homestead:$ bin/install_backend.sh test
```

##### B.1.i.7 Running artisan commands from terminal running on host machine

When using Homestead box and have compatible php installed on your host machine,
you may also run some of artisan command directly from terminal of your host machine (i.e. not via `vagrant ssh`)
To be able to run also database-related commands (such as `php artisan migrate:refresh`)
you need to set up environment first.
You may do so by creating and modifying file `.env.commandline` and setting up respective environment variable.

To do once:

```sh
    $ cd path/to/your/project/
    $ cp .env .env.commandline
    $ vi .env.commandline            # modify as needed (see below)
```

You need to modify following `.env.commandline` keys (see Connecting to databases in
[Laravel Homestead docs](http://laravel.com/docs/5.1/homestead#daily-usage)

```sh
APP_ENV=commandline
DB_HOST=127.0.0.1
DB_PORT=33060
```

Note: make sure, that whenever you change important settings in `.env` file (such as `SUBSCRIBO_COMMON_SECRET`)
     you need to change it in `.env.commandline` as well

Now, whenever you open new terminal window on your host machine and plan to run artisan commands
related to DB operations, run first following:
```sh
    $ SUBSCRIBO_ENV=commandline
    $ export SUBSCRIBO_ENV
```

Notes:

 * if you want to run DB related artisan commands from your IDE, you also might need to set environment variable
   'SUBSCRIBO_ENV' to value 'commandline'
 * usual error which is displayed, when you did not so, and meaning there is a problem with a connection to your DB, is:
```
    [PDOException]
    SQLSTATE[HY00] [2002] No such file or directory
```

#### B.1.ii Installing and configuring for staging or production using web server


##### B.1.ii.1 Install and setup Web server (and optionally also database, queue server, mail server, etc.)

##### B.1.ii.2 Set up [environment variables](/.env.example) in a way appropriate for that service

* Setup DB access, Mail driver and access etc.
* Do not forget to setup 'SUBSCRIBO_REST_CLIENT_HOST' to hostname accessible from host

##### B.1.ii.3 Login to your Web server via ssh

##### B.1.ii.4 Clone the git project into a new (nonexistent) or empty subdirectory

###### B.1.ii.4.a If your project directory does not exist yet:

```sh
    $ cd path/to/your/project/parent
    $ git clone https://github.com/Subscribo/Subscribo your_project_subdirectory_name
```

###### B.1.ii.4.b Alternatively, if your project directory does already exist:

```sh
    $ cd path/to/your/project/
    $ ls -A                                               # Ensure, that the directory is empty (should display nothing)
    $ git clone https://github.com/Subscribo/Subscribo .  # Note the trailing dot
```

##### B.1.ii.5 Change directory to your project directory:

```sh
    $ cd path/to/your/project/
```

##### B.1.ii.6 Modify and run `install_backend.sh`

```sh
    $ vi bin/install_backend.sh         # Modify as needed (e.g. remove things you do not want script would do)
    $ chmod +x bin/install_backend.sh   # Make script executable
    $ bin/install_backend.sh test       # Run it
```

## C. Satis

Running 'composer update' would not work, if packages resource is not configured, as there are private packages used.
For development you may configure local packages resource using [Satis](https://github.com/composer/satis)

### C.1 Satis setup and configuration

##### C.1.1 [Install composer globally](https://getcomposer.org/doc/00-intro.md#globally)
(or modify 'bin/configure_satis.sh' )

##### C.1.2. Installing Satis using provided script 'bin/configure_satis.sh'

```sh
    $ cd /path/to/your/project/parent
    $ chmod +x /path/to/Subscribo/bin/configure_satis.sh
    $ /path/to/Subscribo/bin/configure_satis.sh
```

Notes:
 * This script is not suitable for refreshing satis configuration, only for first-time install
 * It might be faster and easier to run this script from your host terminal (as opposed to `vagrant ssh`)

##### C.1.3. Configure your vagrant box / virtual server to serve Satis public directory and update your 'etc/hosts' file

**Important:** You might need to run vagrant 'reload --provision' to let vagrant box find your new sites

##### C.1.4. Add to your project's 'composer.json' file or create '~/.composer/config.json' with this content:

```json
    {
        "repositories": [
            {
                "type": "composer",
                "url": "http://satis.url.you.provided.to.script"
            }
        ]
    }
```

Note: `configure_satis.sh` script provides similar file for you in `bin/files/composer`
directory and provides you with a hint how to copy it over your previous configuration (if any).
It is recommended that you first check the content of your current configuration before you overwrite it.

### C.2 Satis update

Usually you may use script generated during installing Satis for updating Satis with current content
of your project's `vendor/subscribo` directory.

```sh
    $ /path/to/Subscribo/bin/update_satis.sh
```

Notes:
 * Hook to `update_satis.sh` has been added as pre-install and pre-update script,
   because if your would running `composer update` or `composer install` without your Local Satis server been up-to-date,
   you might overwrite your (recent) changes.
 * When running `update_satis.sh` subdirectories of `packages` directory is cleaned from previous content, but `.git`
   subdirectory is not deleted. This is true for all files and directories starting with dot. If you rename / remove any
   file / directory in some package root starting with dot (e.g. `.gitignore`) you need to replicate your change
   in corresponding copy in `packages` directory manually.

### C.3 Usage

When your Satis server is running, you can run:

```sh
    $ cd /path/to/Subscribo
    $ composer update
```

## General notes:

 * These scripts are made ad-hoc. If you experience problems, you might need to do things manually.
   Studying provided scripts might give you some guidance.
