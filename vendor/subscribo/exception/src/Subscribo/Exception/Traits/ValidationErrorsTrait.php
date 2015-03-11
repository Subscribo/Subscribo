<?php namespace Subscribo\Exception\Traits;

use Subscribo\Exception\Traits\ContainDataTrait;

/**
 * Trait ValidationErrorsTrait
 *
 * Helps implement ValidationErrorsInterface
 *
 * @package Subscribo\Exception
 */
trait ValidationErrorsTrait
{
    use ContainDataTrait;

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        $data = $this->getKeyData();
        if (empty($data['validationErrors'])) {
            return array();
        }
        return $data['validationErrors'];
    }
}
