<?php namespace Subscribo\App\Model;

use Subscribo\App\Model\Factories\CustomerFactory;


/**
 * Model Customer
 *
 * Model class for being changed and used in the application
 */
class Customer extends \Subscribo\App\Model\Base\Customer {

    /**
     * @param $email
     * @return self[]
     */
    public static function findAllByEmail($email)
    {
        $query = self::query()->where('email', $email);
        $result = $query->get();
        return $result;
    }



}
