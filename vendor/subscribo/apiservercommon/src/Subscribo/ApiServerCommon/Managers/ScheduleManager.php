<?php

namespace Subscribo\ApiServerCommon\Managers;

use Illuminate\Console\Scheduling\Schedule;

/**
 * Class ScheduleManager
 *
 * @package Subscribo\ApiServerCommon
 */
class ScheduleManager
{
    /** @var Schedule|null  */
    protected $schedule;

    /** @var callable[] */
    protected $registeredCallbacks = [];

    /** @var callable[] */
    protected $scheduledCallbacks = [];

    /** @var bool */
    protected $toScheduleHardcoded = true;

    /**
     * @param Schedule $schedule
     */
    public function restart(Schedule $schedule = null)
    {
        $this->schedule = $schedule;
        $this->registeredCallbacks = array_merge($this->registeredCallbacks, $this->scheduledCallbacks);
        $this->scheduledCallbacks = [];
        $this->toScheduleHardcoded = true;
    }

    /**
     * @param Schedule $schedule
     */
    public function schedule(Schedule $schedule)
    {
        if ($this->schedule !== $schedule) {
            $this->restart($schedule);
        }
        if ($this->toScheduleHardcoded) {
            $this->scheduleHardcoded($schedule);
            $this->toScheduleHardcoded = false;
        }
        while ($this->registeredCallbacks) {
            $callback = array_shift($this->registeredCallbacks);
            call_user_func($callback, $schedule);
            $this->scheduledCallbacks[] = $callback;
        }
    }

    /**
     * @param callable $callback
     */
    public function addCallback(callable $callback)
    {
        if ($this->schedule) {
            call_user_func($callback, $this->schedule);
            $this->scheduledCallbacks[] = $callback;
        } else {
            $this->registeredCallbacks[] = $callback;
        }
    }

    /**
     * @param Schedule $schedule
     */
    protected function scheduleHardcoded(Schedule $schedule)
    {
        $schedule->call(
            function() {
                //something to do regularly
            }
        )->hourly();
    }
}
