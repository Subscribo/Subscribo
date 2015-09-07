<?php

namespace Subscribo\ApiServerJob\Utils;

/**
 * Class EmailUtils
 *
 * Utilities to handle email and its message
 *
 * @package Subscribo\ApiServerJob
 */
class EmailUtils
{
    /**
     * @param $message
     * @param array $data
     */
    public static function enhanceEmailMessage($message, array $data = [])
    {
        $addressKeys = ['from', 'sender', 'to', 'cc', 'bcc', 'replyTo'];
        foreach ($addressKeys as $method)
        {
            if (empty($data[$method])) {
                continue;
            }
            $values = is_array($data[$method]) ? $data[$method] : [$data[$method]];
            foreach ($values as $key => $value) {
                if (is_int($key)) {
                    $address = $value;
                    $name = null;
                } else {
                    $address = $key;
                    $name = $value;
                }
                $message->$method($address, $name);
            }

        }
        if (isset($data['subject'])) {
            $message->subject($data['subject']);
        }
        if (isset($data['priority'])) {
            $message->priority($data['priority']);
        }
    }
}
