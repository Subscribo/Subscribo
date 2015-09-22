<?php

namespace Subscribo\ApiServerJob\Integration\Laravel;

use Subscribo\Support\ServiceProvider;
use Subscribo\ApiServerJob\Managers\ScheduleManager;

/**
 * Class ApiServerJobServiceProvider
 *
 * @package Subscribo\ApiServerJob
 */
class ApiServerJobServiceProvider extends ServiceProvider
{
    protected $toRegisterSchedule = false;

    protected $scheduleManagerRegistered = false;

    public function register()
    {
    }

    public function boot()
    {
        $this->registerTranslationResources('emails');
        $this->registerViews();
        if ($this->toRegisterSchedule) {
            $this->registerSchedule();
        }
        $this->commands(['\\Subscribo\\ApiServerJob\\Commands\\MaintainHourly']);
    }

    /**
     * Register ScheduleManager is not registered yet
     */
    public function registerScheduleManager()
    {
        if ($this->scheduleManagerRegistered) {

            return;
        }
        $this->app->singleton('subscribo.schedule.manager', 'Subscribo\\ApiServerJob\\Managers\\ScheduleManager');
        if ( ! $this->app->bound('Illuminate\\Console\\Scheduling\\Schedule')) {
            $this->app->singleton('Illuminate\\Console\\Scheduling\\Schedule');
        }
        $this->app->rebinding('Illuminate\\Console\\Scheduling\\Schedule', function ($app, $schedule) {
            $scheduleManager = $this->app->make('subscribo.schedule.manager');
            $scheduleManager->schedule($schedule);
        });
        $this->scheduleManagerRegistered = true;
        $this->toRegisterSchedule = true;
    }

    /**
     * Registering callbacks bound in ScheduleManager to schedule
     */
    public function registerSchedule()
    {
        /** @var ScheduleManager $scheduleManager */
        $scheduleManager = $this->app->make('subscribo.schedule.manager');
        $schedule = $this->app->make('Illuminate\\Console\\Scheduling\\Schedule');
        $scheduleManager->schedule($schedule);
    }
}
