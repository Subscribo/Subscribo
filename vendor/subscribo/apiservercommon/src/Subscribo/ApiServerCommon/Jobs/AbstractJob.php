<?php

namespace Subscribo\ApiServerCommon\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Abstract class AbstractJob
 *
 * @package Subscribo\ApiServerCommon
 */
abstract class AbstractJob implements SelfHandling, ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;
}
