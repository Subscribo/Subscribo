# Subscribo project

Contains packages and other files for building Subscribo API backend as well as Subscribo Frontend servers

## A. Frontend

### A.1. Installation

#### A.1.1 [Install](http://laravel.com/docs/5.1/installation) Laravel 5.1

In order to install into 'frontend' directory you may use this composer command
(provided you have [composer](http://getcomposer.org) installed and current Laravel version is 5.1):

```sh
    composer create-project laravel/laravel --prefer-dist frontend
```

#### A.1.2 [Install and configure](/vendor/subscribo/apiclient/README.md) package Subscribo ApiClient

#### A.1.3 For development you can optionally [install](/vendor/subscribo/clientchecker/README.md) package Subscribo ClientChecker

## B. Backend

### B.1. Installation

#### B.1.i Installation for development using Homestead box on Vagrant

You need to decide following:

 * where you want to put your project: path/to/your/project = path/to/your/project/parent/your_project_subdirectory_name
 * where you want to put your Homestead configuration: path/to/your/homestead/configuration
 * host name of your project: backend.hostname

Optionally:
 * Do you want to also make additional frontend?
 * If so, host name of additional frontend: frontend.hostname

##### B.1.i.1 Install [Vagrant](https://www.vagrantup.com) and [Homestead](http://laravel.com/docs/5.1/homestead)

##### B.1.i.2 Configure and run your Homestead box (and modify your `/etc/hosts if needed)

```sh
    $ cd path/to/your/homestead/configuration
    $ vi Homestead.yaml
    $ vagrant up
    $ sudo vi /etc/hosts
```


##### B.1.i.3 Change to directory, which should be parent directory of your project

```sh
    $ cd path/to/your/project/parent
```

#### B.1.i.4 Clone the git project into a new (nonexistent) or empty subdirectory

```sh
    $ git clone https://github.com/Subscribo/Subscribo your_project_subdirectory_name
```

#### B.1.i.5 Create `.env` file using `.env.example` as a template

```sh
    $ cd path/to/your/project/
    $ cp .env.example .env
    $ vi .env                        # modify as needed
```

Notes:
* Setup DB access details, if you have changed them in your vagrant box or created a special DB for this project
* Do not forget to setup 'SUBSCRIBO_REST_CLIENT_HOST' to hostname accessible from host


#### B.1.i.6 Run `install_backend.sh` from within your Homestead box

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

#### B.1.i.7 Running artisan commands from terminal running on host machine

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

Now, whenever you open new terminal window on your host machine and plan to run artisan commands
related to DB operations, run first following:
```sh
    $ SUBSCRIBO_ENV=commandline
    $ export SUBSCRIBO_ENV
```

Note: if you want to run DB related artisan commands from your IDE, you also might need to set environment variable
'SUBSCRIBO_ENV' to value 'commandline'

### B.1.ii Installing and configuring for staging or production using web server


#### B.1.ii.1 Install and setup Web server (and optionally also database, queue server, mail server, etc.)

#### B.1.ii.2 Set up [environment variables](/.env.example) in a way appropriate for that service

* Setup DB access, Mail driver and access etc.
* Do not forget to setup 'SUBSCRIBO_REST_CLIENT_HOST' to hostname accessible from host

#### B.1.ii.3 Login to your Web server via ssh

#### B.1.ii.4 Clone the git project into a new (nonexistent) or empty subdirectory

##### B.1.ii.4.a If your project directory does not exist yet:

```sh
    $ cd path/to/your/project/parent
    $ git clone https://github.com/Subscribo/Subscribo your_project_subdirectory_name
```

##### B.1.ii.4.b Alternatively, if your project directory does already exist:

```sh
    $ cd path/to/your/project/
    $ ls -A                                               # Ensure, that the directory is empty (should display nothing)
    $ git clone https://github.com/Subscribo/Subscribo .  # Note the trailing dot
```

##### B.1.ii.5 Change directory to your project directory:

```sh
    $ cd path/to/your/project/
```

#### B.1.ii.6 Modify and run `install_backend.sh`

```sh
    $ vi bin/install_backend.sh         # Modify as needed (e.g. remove things you do not want script would do)
    $ chmod +x bin/install_backend.sh   # Make script executable
    $ bin/install_backend.sh test       # Run it
```

## C. Satis configuration

Running 'composer update' would not work, if packages resource is not configured, as there are private packages used.
You may configure local packages resource using [Satis](https://github.com/composer/satis)

### 1. [Install composer globally](https://getcomposer.org/doc/00-intro.md#globally)
(or modify 'bin/configure_satis.sh' )

### 2. Installing Satis using provided script 'bin/configure_satis.sh'

```sh
    $ cd /path/to/your/projects
    $ chmod +x /path/to/Subscribo/bin/configure_satis.sh
    $ /path/to/Subscribo/bin/configure_satis.sh
```

### 3. Configure your vagrant box / virtual server to serve Satis public directory and update your 'etc/hosts' file

    Important: You might need to run vagrant 'reload --provision' to let vagrant box find your new sites

### 4. Add to your project's 'composer.json' file or create '~/.composer/config.json' with this content:
```json
    {
        "repositories": [{"type": "composer", "url": "http://satis.url.you.provided.to.script"}]
    }
```

### 5. Now you can run
```sh
    $ cd /path/to/Subscribo
    $ composer update
```
