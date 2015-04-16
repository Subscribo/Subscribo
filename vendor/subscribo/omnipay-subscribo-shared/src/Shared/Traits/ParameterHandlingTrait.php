<?php namespace Subscribo\Omnipay\Shared\Traits;


trait ParameterHandlingTrait
{
    /**
     * @param array $parameterKeys
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function validateParameters(array $parameterKeys)
    {
        foreach ($parameterKeys as $key) {
            $this->validate($key);
        }
    }

    /**
     * @param array $keys
     * @return array
     */
    protected function getSomeParameters(array $keys)
    {
        $allParameters = $this->getParameters();
        /** Idea: Arr::only()  https://github.com/illuminate/support/blob/master/Arr.php  */
        $selectedParameters = array_intersect_key($allParameters, array_flip($keys));
        return $selectedParameters;
    }
}
