<?php namespace Subscribo\Auth\Interfaces;

interface CanBeGuestInterface
{
    /**
     * @return bool
     */
    public function isGuest();

    /**
     * @return CanBeGuestInterface
     */
    public static function findGuest();

}
