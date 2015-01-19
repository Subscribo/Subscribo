<?php namespace Subscribo\Auth\Guards;

use Subscribo\Auth\Interfaces\StatelessGuardInterface;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\Auth\Traits\StatelessToNonStatelessTrait;

class SimpleGuard extends BaseStatelessGuard implements StatelessGuardInterface, Guard {
    use StatelessToNonStatelessTrait;
}
