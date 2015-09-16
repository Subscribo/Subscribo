<?php namespace Subscribo\ModelCore\Models;


/**
 * Model Contact
 *
 * Model class for being changed and used in the application
 */
class Contact extends \Subscribo\ModelCore\Bases\Contact
{
    /**
     * @param array $data
     * @return Contact|static
     */
    public static function generate(array $data)
    {
        $instance = static::make($data);
        $instance->save();

        return $instance;
    }

    /**
     * @param array $data
     * @return static|Contact
     */
    public static function make(array $data)
    {
        static::reguard();

        return new static($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function dataContainsContact(array $data)
    {
        return (( ! empty($data['phone'])) or ( ! empty($data['mobile'])));
    }

    /**
     * @param array $data
     * @return null|Contact|static
     */
    public static function generateIfDataContainsContact(array $data)
    {
        if (static::dataContainsContact($data)) {

            return static::generate($data);
        }

        return null;
    }
}
