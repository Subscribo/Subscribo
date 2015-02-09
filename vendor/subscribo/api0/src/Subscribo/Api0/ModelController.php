<?php namespace Subscribo\Api0;

use App\Http\Controllers\Controller;
use Subscribo\ModelBase\AbstractModel;
use Subscribo\ModelBase\ModelFactory;
use Subscribo\Modifier\Modifier;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\BadRequestHttpException;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Api0\Exceptions\InvalidQueryHttpException;
use Subscribo\Exception\Exceptions\InternalServerErrorHttpException;
use Subscribo\Exception\Exceptions\NotFoundHttpException;
use Validator;
use Input;
use Subscribo\Support\Str;
use Request;

/**
 * Class ModelController
 *
 * @package Subscribo\Api0
 */
class ModelController extends Controller {

    /**
     * @var \Subscribo\ModelBase\ModelFactory
     */
    private $modelFactory;

    /**
     * @var \Subscribo\Modifier\Modifier
     */
    private $modifier;


    public function __construct(ModelFactory $modelFactory, Modifier $modifier)
    {
        $this->modelFactory = $modelFactory;
        $this->modifier = $modifier;
    }


    public function getIndex($modelNameStub, $identifier = null)
    {
        $model = $this->_retrieveModel($modelNameStub);
        $filterableBy = $model->getFilterableByProperties();
        $parsedParameters = self::_parseParameters(Request::all(), $model);
        $query = $model::query();
        self::_applyParsedParameters($query, $parsedParameters);
        if (is_null($identifier)) {
            $result = $query->get();
            return $result;
        }
        if (self::_isNonNegativeInteger($identifier)) {
            $filterValue = intval($identifier);
            $filterColumn = $model->getKeyName();
            $errorMessage = 'This model is not filterable by primary key.';
        } else {
            $filterValue = $identifier;
            $filterColumn = 'identifier';
            $errorMessage = 'This model is not filterable by identifier, and if you want to filter by primary key, you need to provide a non negative integer.';
        }
        if ( ! empty($filterableBy[$filterColumn])) {
            $query->where($filterColumn, $filterValue);
        } else {
            throw new BadRequestHttpException($errorMessage);
        }
        $result = $query->first();
        if ($result) {
            return $result;
        }
        throw new NotFoundHttpException("Requested model instance not found");
    }

    public function addElement($modelNameStub)
    {
        $model = $this->_retrieveModel($modelNameStub);
        $modifiedInput = $this->modifier->modifyMultiple(Input::all(), $model::$modificationRulesBeforeValidation);
        $validator = Validator::make($modifiedInput, $model::$rules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->messages()->all());
        }
        try {
            $toFill = $this->modifier->modifyMultiple($validator->valid(), $model::$modificationRulesAfterValidation);
            $model->fill($toFill);
            $saveResult = $model->save();
            if ($saveResult) {
                return $model::find($model->getKey());
            }
            throw new \Exception('Attempt to save a new model failed');
        } catch (\Exception $e) {
            throw new InternalServerErrorHttpException("Attempt to create a new model failed", array(), 0, $e);
        }
    }

    public function putElement($modelNameStub, $identifier)
    {
        $model = $this->_retrieveModel($modelNameStub);
        $input = Input::all();
        foreach($model->getFillable() as $fillableFieldName) {
            if ( ! array_key_exists($fillableFieldName, $input)) {
                $input[$fillableFieldName] = null;
            }
        }
        return $this->_putOrModifyElement($model, $identifier, $input);
    }

    public function modifyElement($modelNameStub, $identifier)
    {
        $model = $this->_retrieveModel($modelNameStub);
        $input = Input::all();
        return $this->_putOrModifyElement($model, $identifier, $input);
    }

    public function deleteElement($modelNameStub, $identifier)
    {
        $model = $this->_retrieveModel($modelNameStub);
        $modelToDelete = $this->_findElement($model, $identifier);
        try {
            $deletionResult = $modelToDelete->delete();
            if ($deletionResult) {
                $modelToDelete->setAttribute($modelToDelete->getKeyName(), null);
                return $modelToDelete;
            }
            throw new \Exception('Attempt to delete a model failed');
        } catch (\Exception $e) {
            throw new InternalServerErrorHttpException("Attempt to delete this model failed", array(), 0, $e);
        }
    }


    /**
     * @param \Subscribo\ModelBase\AbstractModel $model
     * @param string $identifier
     * @param array $input
     * @return \Subscribo\ModelBase\AbstractModel
     * @throws \Subscribo\Exception\Exceptions\InternalServerErrorHttpException
     * @throws \Subscribo\Exception\Exceptions\InvalidInputHttpException
     */
    private function _putOrModifyElement($model, $identifier, array $input)
    {
        $modelToChange = $this->_findElement($model, $identifier);
        $differentInput = array();
        foreach($input as $key => $value) {
            if ($modelToChange->getAttribute($key) != $value) {
                $differentInput[$key] = $value;
            }
        }
        $filteredRules = array();
        foreach($differentInput as $key => $value) {
            if ( ! empty($model::$rules[$key])) {
                $filteredRules[$key] = $model::$rules[$key];
            }
        }
        $modifiedInput = $this->modifier->modifyMultiple($differentInput, $model::$modificationRulesBeforeValidation);
        $validator = Validator::make($modifiedInput, $filteredRules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->messages()->all());
        }
        try {
            $toFill = $this->modifier->modifyMultiple($validator->valid(), $model::$modificationRulesAfterValidation);
            $modelToChange->fill($toFill);
            $saveResult = $modelToChange->save();
            if ($saveResult) {
                return $model::find($modelToChange->getKey());
            }
            throw new \Exception('Attempt to save a modified model failed');
        } catch (\Exception $e) {
            throw new InternalServerErrorHttpException("Attempt to change this model failed", array(), 0, $e);
        }
    }

    /**
     * @param \Subscribo\ModelBase\AbstractModel $model
     * @param string $identifier
     * @return \Subscribo\ModelBase\AbstractModel
     * @throws \Subscribo\Exception\Exceptions\BadRequestHttpException
     * @throws \Subscribo\Exception\Exceptions\InstanceNotFoundHttpException
     */
    private function _findElement($model, $identifier)
    {
        $filterableBy = $model->getFilterableByProperties();
        if (self::_isNonNegativeInteger($identifier)) {
            $filterValue = intval($identifier);
            $filterColumn = $model->getKeyName();
            $errorMessage = 'This model is not filterable by primary key.';
        } else {
            $filterValue = $identifier;
            $filterColumn = 'identifier';
            $errorMessage = 'This model is not filterable by identifier, and if you want to filter by primary key, you need to provide a non negative integer.';
        }
        $query = $model::query();
        if ( ! empty($filterableBy[$filterColumn])) {
            $query->where($filterColumn, $filterValue);
        } else {
            throw new BadRequestHttpException($errorMessage);
        }
        $found = $query->first();
        if (empty($found)) {
            throw new InstanceNotFoundHttpException();
        }
        return $found;
    }


    /**
     *
     * @param string $modelNameStub
     * @return \Subscribo\ModelBase\AbstractModel
     * @throws \Subscribo\Exception\Exceptions\NotFoundHttpException
     */
    protected function _retrieveModel($modelNameStub)
    {
        $result = $this->modelFactory->resolveModelFromUriStub($modelNameStub);
        if (is_null($result)) {
            throw new NotFoundHttpException("Model '".$modelNameStub."' not found or not available via this api call.");
        }
        return $result;
    }

    protected static function _isNonNegativeInteger($value)
    {
        if ( ! is_numeric($value)) {
            return false;
        }
        if (is_string($value)) {
            if ( ! ctype_digit($value)) {
                return false;
            }
        } elseif ( ! is_int($value)) {
            return false;
        }
        $intValue = intval($value);
        if ($intValue < 0) {
            return false;
        }
        return true;
    }

    protected static function _applyParsedParameters(\Illuminate\Database\Eloquent\Builder $query, array $parameters)
    {
        if ( ! empty($parameters['with'])) {
            $query->with($parameters['with']);
        }
        if (isset($parameters['limit'])) {
            $query->limit($parameters['limit']);
        }
        if (isset($parameters['offset'])) {
            $query->offset($parameters['offset']);
        }
        if (isset($parameters['paginate'])) {
            $query->paginate($parameters['paginate']);
        }
        if ( ! empty($parameters['where'])) {
            foreach($parameters['where'] as $key => $whereCondition) {
                if (is_array($whereCondition)) {
                    $column = $whereCondition['column'];
                    $operator = isset($whereCondition['operator']) ? $whereCondition['operator'] : '=';
                    $value = $whereCondition['value'];
                } else {
                    $column = $key;
                    $operator = '=';
                    $value = $whereCondition;
                }
                $query->where($column, $operator, $value);
            }
        }
    }

    protected static function _parseParameters(array $requestParameters, AbstractModel $model)
    {
        $result = array('where' => array());
        $nonNegativeIntegerParameters = array('limit', 'offset', 'paginate', 'page');
        foreach ($nonNegativeIntegerParameters as $parameterName) {
            $found = self::_takeNonNegativeInteger($parameterName, $requestParameters);
            if (false === $found) {
                continue;
            }
            $result[$parameterName] = $found;
            unset($requestParameters[$parameterName]);
        }
        if (array_key_exists('with', $requestParameters)) {
            $result['with'] = self::_parseParameterWith($requestParameters, $model);
            unset($requestParameters['with']);
        }

        $filterableBy = $model->getFilterableByProperties();
        foreach ($filterableBy as $columnName => $someValue)
        {
            if (array_key_exists($columnName, $requestParameters)) {
                $result['where'][$columnName] = $requestParameters[$columnName];
                unset($requestParameters[$columnName]);
            }
        }
        if ( ! empty($requestParameters)) {
            $unrecognizedParameterKeys = array_keys($requestParameters);
            if (1 < count($unrecognizedParameterKeys)) {
                $errorMessage = "Unrecognized parameters '".implode("', '", $unrecognizedParameterKeys)."'.";
            } else {
                $errorMessage = "Unrecognized parameter '".reset($unrecognizedParameterKeys)."'.";
            }
            throw new InvalidQueryHttpException($errorMessage);
        }
        return $result;
    }

    protected static function _parseParameterWith(array $requestParameters, AbstractModel $model)
    {
        $toParse = trim($requestParameters['with']);
        if (empty($toParse)) {
            throw new InvalidQueryHttpException("Parameter 'with' should be non empty or not present at all.");
        }
        $parts = explode(',', $toParse);
        $result = array();
        $availableRelations = array_keys($model->getAvailableRelations());
        foreach ($parts as $part) {
            $processed = trim($part);
            if (empty($processed)) {
                throw new InvalidQueryHttpException("Parameter 'with' contains empty element.");
            }
            if (false === array_search($processed, $availableRelations)) {
                $processed = Str::camel($part);
            }
            if (false === array_search($processed, $availableRelations)) {
                throw new InvalidQueryHttpException("Parameter 'with' contain element '" . $part . "' which is not among available relations.");
            }
            $result[] = $processed;
        }
        return $result;
    }


    protected static function _takeNonNegativeInteger($needle, array $haystack)
    {
        if ( ! array_key_exists($needle, $haystack)) {
            return false;
        }
        $value = $haystack[$needle];
        if ( ! self::_isNonNegativeInteger($value)) {
            $errorMessage = "Parameter '".$needle."' should have a non negative integer value.";
            throw new InvalidQueryHttpException($errorMessage);
        }
        $result = intval($value);
        return $result;
    }
}
