<?php namespace Subscribo\Api1\Traits;

use Validator;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InvalidQueryHttpException;
use Subscribo\Exception\Exceptions\InvalidIdentifierHttpException;
use Subscribo\Api1\Context;

/**
 * Trait ContextRequestValidationTrait
 *
 * Class using this trait need to have property $context of type Context
 *
 * @package Subscribo\Api1
 */
trait ContextRequestValidationTrait
{


    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    protected function assembleValidator(array $data, array $rules, array $messages = array(), array $customAttributes = array())
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        return $validator;
    }

    protected function validateRequestBody(array $validationRules)
    {
        $data = array_intersect_key($this->context->getRequest()->json()->all(), $validationRules);
        $validator = $this->assembleValidator($data, $validationRules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->errors()->all());
        }
        return $validator->valid();
    }

    protected function validateRequestQuery(array $validationRules)
    {
        $data = array_intersect_key($this->context->getRequest()->query(), $validationRules);
        $validator = $this->assembleValidator($data, $validationRules);
        if ($validator->fails()) {
            throw new InvalidQueryHttpException($validator->errors()->all());
        }
        return $validator->valid();
    }

    protected function validatePositiveIdentifier($id)
    {
        if ( ! (ctype_digit($id) or is_int($id))) {
            throw new InvalidIdentifierHttpException(['id' => 'Identifier have to be a positive integer']);
        }
        return intval($id);
    }
}
