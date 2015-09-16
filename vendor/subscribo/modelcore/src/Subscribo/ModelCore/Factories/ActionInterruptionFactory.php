<?php

namespace Subscribo\ModelCore\Factories;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Widget;

/**
 * Class ActionInterruptionFactory
 *
 * @package Subscribo\ModelCore
 */
class ActionInterruptionFactory
{
    /** @var string  */
    protected $callingClassName;
    /** @var int|null  */
    protected $serviceId;
    /** @var int|null  */
    protected $accountId;

    /**
     * @param $callingClassName
     * @param int|null $serviceId
     * @param int|null $accountId
     */
    public function __construct($callingClassName, $serviceId, $accountId)
    {
        $this->callingClassName = $callingClassName;
        $this->serviceId = $serviceId;
        $this->accountId = $accountId;

    }

    public static function getEndpointBase($serverRequestType)
    {
        switch ($serverRequestType) {
            case Questionary::TYPE:

                return 'answer/question/';
            case ClientRedirection::TYPE:

                return 'answer/redirection/';
            case Widget::TYPE:

                return 'answer/widget/';
        }
        throw new InvalidArgumentException('Unknown Server Request type');
    }

    /**
     * @param $continueMethod
     * @param array $extraData
     * @param ServerRequest $serverRequest
     * @param bool $toSave Whether to save ActionInterruption after processing
     * @return ActionInterruption
     */
    public function makeActionInterruption($continueMethod, array $extraData = [], ServerRequest $serverRequest = null, $toSave = true)
    {
        if (false === strpos($continueMethod, '@')) {
            $continueMethod = $this->callingClassName.'@'.$continueMethod;
        }
        $actionInterruption = ActionInterruption::make();
        $actionInterruption->extraData = $extraData;
        $actionInterruption->continueMethod = $continueMethod;
        $actionInterruption->accountId = $this->accountId;
        $actionInterruption->serviceId = $this->serviceId;
        if ($serverRequest) {
            $this->syncActionInterruptionWithServerRequest($actionInterruption, $serverRequest, false);
        }
        if ($toSave) {
            $actionInterruption->save();
        }

        return $actionInterruption;
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @param ServerRequest $serverRequest
     * @param bool $toSave Whether to save ActionInterruption after processing
     * @return ActionInterruption
     */
    public function syncActionInterruptionWithServerRequest(ActionInterruption $actionInterruption, ServerRequest $serverRequest, $toSave = true)
    {
        $endpointBase = static::getEndpointBase($serverRequest->getType());

        $actionInterruption->syncWithServerRequest($serverRequest, $endpointBase);
        if ($toSave) {
            $actionInterruption->save();
        }

        return $actionInterruption;
    }
}
