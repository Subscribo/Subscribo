# Package Subscribo ApiServerCommon - API Server functionality not specific to a particular version of API

## Contains:

* Jobs
* ScheduleManager

## How to use ScheduleManager

### Installing and registering it in ServiceProvider of package using it:

```php

    public function register()
    {
        $this->registerServiceProvider('\\Subscribo\\ApiServerCommon\\Integration\\Laravel\\ApiServerCommonServiceProvider')
            ->registerScheduleManager();
    }
```
Note: helper function Subscribo\Support\ServiceProvider::registerServiceProvider() is used in this example,
returning an actual instance of ApiServerCommonServiceProvider also in case it is already registered.

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
