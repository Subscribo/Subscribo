<?php namespace Subscribo\ModelCore\Models;

/**
 * Model Customer
 *
 * Model class for being changed and used in the application
 */
class Customer extends \Subscribo\ModelCore\Bases\Customer {

    /**
     * @param $email
     * @return Customer[]|static[]
     */
    public static function findAllByEmail($email)
    {
        $query = static::query()->where('email', $email);
        $result = $query->get();
        return $result;
    }



}
