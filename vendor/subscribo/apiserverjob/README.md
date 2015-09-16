# Package Subscribo ApiServerJob - Jobs and jobs related functionality for API Server

## Contains:

* ScheduleManager
* Regular jobs
* Triggered jobs


## How to use ScheduleManager

### Installing and registering it in ServiceProvider of package using it:

```php

    public function register()
    {
        $this->registerServiceProvider('\\Subscribo\\ApiServerJob\\Integration\\Laravel\\ApiServerJobServiceProvider')
            ->registerScheduleManager();
    }
```
Note: helper function Subscribo\Support\ServiceProvider::registerServiceProvider() is used in this example,
returning an actual instance of ApiServerJobServiceProvider also in case it is already registered.

### Adding callback (need to be early enough, e.g. in boot() method of ServiceProvider of package using it:

```php
    public function boot()
    {
        $this->app-make('subscribo.schedule.manager')->addCallback(
            function ($schedule) {
                $schedule->call(
                    function () {
                        //something to be done every hour
                    }
                )->hourly();
            }
        );
    }
```
