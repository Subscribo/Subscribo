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
     * @param array $customValues
     * @return Validator
     */
    protected function assembleValidator(array $data, array $rules, array $messages = [], array $customAttributes = [], array $customValues = [])
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        if ($customValues) {
            $validator->addCustomValues($customValues);
        }
        return $validator;
    }

    /**
     * @param array $validationRules
     * @return array
     * @throws InvalidInputHttpException
     */
    protected function validateRequestBody(array $validationRules)
    {
        $data = array_intersect_key($this->context->getRequest()->json()->all(), $validationRules);
        $validator = $this->assembleValidator($data, $validationRules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->errors()->all());
        }
        return $validator->valid();
    }

    /**
     * @param array $validationRules
     * @return array
     * @throws \Subscribo\Exception\Exceptions\InvalidQueryHttpException
     */
    protected function validateRequestQuery(array $validationRules)
    {
        $data = array_intersect_key($this->context->getRequest()->query(), $validationRules);
        $validator = $this->assembleValidator($data, $validationRules);
        if ($validator->fails()) {
            throw new InvalidQueryHttpException($validator->errors()->all());
        }
        return $validator->valid();
    }

    /**
     * @param $id
     * @return int
     * @throws \Subscribo\Exception\Exceptions\InvalidIdentifierHttpException
     */
    protected function validatePositiveIdentifier($id)
    {
        if ( ! (ctype_digit($id) or is_int($id))) {
            /** @var Context $context */
            $context = $this->context;
            $localizer = $context->getLocalizer()->duplicate('controllers', 'api1');
            throw new InvalidIdentifierHttpException([
                'id' => $localizer->trans('contextRequestValidationTrait.errors.wrongIdentifier'),
            ]);
        }
        return intval($id);
    }
}
