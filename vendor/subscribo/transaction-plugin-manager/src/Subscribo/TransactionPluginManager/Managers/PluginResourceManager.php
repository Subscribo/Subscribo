<?php

namespace Subscribo\TransactionPluginManager\Managers;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Subscribo\Api1\Context;
use Subscribo\ModelCore\Factories\ActionInterruptionFactory;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\ModelCore\Models\Transaction;
use Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException;
use Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException;
use Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException;
use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Widget;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\TransactionPluginManager\Facades\TransactionFacade;
use Subscribo\TransactionPluginManager\Factories\QuestionaryFactory;
use Subscribo\TransactionPluginManager\Factories\WidgetFactory;
use Subscribo\TransactionPluginManager\Factories\ClientRedirectionFactory;
use Subscribo\TransactionPluginManager\Interfaces\TransactionDriverManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\PluginResourceManagerInterface;
use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\QuestionaryFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginDriverInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessorInterface;
use Subscribo\TransactionPluginManager\Interfaces\InterruptionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface;
use Subscribo\TransactionPluginManager\Facades\InterruptionFacade;
use Subscribo\TransactionPluginManager\Facades\LocalizerFacade;
use Subscribo\TransactionPluginManager\Bases\TransactionProcessingResultBase;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Support\Str;
use Subscribo\ApiServerJob\Jobs\Triggered\Transaction\SendConfirmationEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Psr\Log\LoggerInterface;

/**
 * Class PluginResourceManager
 *
 * @package Subscribo\TransactionPluginManager
 */
class PluginResourceManager implements PluginResourceManagerInterface
{
    use DispatchesJobs;

    /** @var TransactionDriverManagerInterface  */
    protected $driverManager;

    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var LocalizerFacade  */
    protected $localizerFacade;

    /** @var LoggerInterface  */
    protected $logger;

    /**
     * @param TransactionDriverManagerInterface $driverManager
     * @param LocalizerInterface $localizer
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransactionDriverManagerInterface $driverManager,
        LocalizerInterface $localizer,
        LoggerInterface $logger
    ) {
        $this->driverManager = $driverManager;
        $this->localizer = $localizer;
        $this->logger = $logger;
        $this->localizerFacade = new LocalizerFacade($localizer);
    }

    /**
     * Returns specified driver, connected to this instance of PluginResourceManagerInterface as its resource manager
     *
     * @param TransactionPluginDriverInterface|string $driver
     * @return TransactionPluginDriverInterface
     * @throws InvalidArgumentException
     */
    public function getDriver($driver)
    {
        if ($driver instanceof TransactionPluginDriverInterface) {
            $instance = $driver;
        } else {
            $instance = $this->driverManager->getDriver($driver);
        }

        return $instance->withPluginResourceManager($this);
    }

    /**
     * @return LocalizerFacade|LocalizerFacadeInterface
     */
    public function getLocalizer()
    {
        return $this->localizerFacade;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param array|int|mixed|string $questionary
     * @param TransactionProcessorInterface $processor
     * @param InterruptionFacadeInterface|null $interruption
     * @return TransactionProcessingResultBase|TransactionProcessingResultInterface
     */
    public function interruptByQuestionary($questionary, TransactionProcessorInterface $processor, InterruptionFacadeInterface $interruption = null)
    {
        $questionaryFactory = new QuestionaryFactory($this->localizer, $this->assembleDefaultDomain($processor));
        $questionaryInstance = $questionaryFactory->make($questionary);
        $this->generateActionInterruption($processor, $questionaryInstance, $interruption);
        $exception = new QuestionaryServerRequestHttpException($questionaryInstance);

        return TransactionProcessingResultBase::makeInterruptionResult($processor->getTransactionFacade(), $exception);
    }

    /**
     * @param \Subscribo\Omnipay\Shared\Interfaces\WidgetInterface|string|mixed $widget
     * @param TransactionProcessorInterface $processor
     * @param InterruptionFacadeInterface|null $interruption
     * @return TransactionProcessingResultBase|TransactionProcessingResultInterface
     */
    public function interruptByWidget($widget, TransactionProcessorInterface $processor, InterruptionFacadeInterface $interruption = null)
    {
        $widgetFactory = new WidgetFactory($this->assembleDefaultDomain($processor));
        $widgetInstance = $widgetFactory->make($widget);
        $this->generateActionInterruption($processor, $widgetInstance, $interruption);
        $exception = new WidgetServerRequestHttpException($widgetInstance);

        return TransactionProcessingResultBase::makeInterruptionResult($processor->getTransactionFacade(), $exception);
    }

    /**
     * @param mixed|string $redirection
     * @param TransactionProcessorInterface $processor
     * @param InterruptionFacadeInterface|null $interruption
     * @return TransactionProcessingResultBase|TransactionProcessingResultInterface
     */
    public function interruptByClientRedirection($redirection, TransactionProcessorInterface $processor, InterruptionFacadeInterface $interruption = null)
    {
        $clientRedirectionFactory = new ClientRedirectionFactory($this->assembleDefaultDomain($processor));
        $clientRedirectionInstance = $clientRedirectionFactory->make($redirection);
        $this->generateActionInterruption($processor, $clientRedirectionInstance, $interruption);
        $exception = new ClientRedirectionServerRequestHttpException($clientRedirectionInstance);

        return TransactionProcessingResultBase::makeInterruptionResult($processor->getTransactionFacade(), $exception);
    }

    /**
     * @param TransactionProcessorInterface $processor
     * @return InterruptionFacade
     */
    public function prepareInterruptionFacade(TransactionProcessorInterface $processor)
    {
        $actionInterruption = $this->generateActionInterruption($processor);

        return new InterruptionFacade($actionInterruption);
    }

    /**
     * @param TransactionProcessingResultInterface $processingResult
     * @return array
     * @throws \Exception
     * @throws \Subscribo\RestCommon\Exceptions\ServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     * @throws \Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException
     */
    public function finalizeTransactionProcessingResult(TransactionProcessingResultInterface $processingResult)
    {
        static::addResultToTransaction($processingResult);
        $status = $processingResult->getStatus();
        switch ($status) {
            case TransactionProcessingResultInterface::STATUS_INTERRUPTION:
                $result = $processingResult->getException();
                break;
            case TransactionProcessingResultInterface::STATUS_ERROR:
                $result = $this->makeResultFromError($processingResult);
                break;
            case TransactionProcessingResultInterface::STATUS_SUCCESS:
            case TransactionProcessingResultInterface::STATUS_WAITING:
            case TransactionProcessingResultInterface::STATUS_FAILURE:
            default:
                $result = $processingResult->export();
        }
        if ($result instanceof Exception) {

            throw $result;
        }
        $transaction = $processingResult->getTransactionFacadeObject()->getTransactionModelInstance();
        if ($transaction->confirmationMessage) {
            $job = new SendConfirmationEmail($transaction);
            $this->dispatch($job);
        }

        return ['result' => $result];
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @param array|null $validatedData
     * @param string $action
     * @param Context $context
     * @param ServerRequest $serverRequest
     * @return mixed|array
     * @throws \RuntimeException
     */
    public function resumeFromInterruption(ActionInterruption $actionInterruption, $validatedData, $action, Context $context, ServerRequest $serverRequest)
    {
        $this->localizer = $context->getLocalizer();
        $this->localizerFacade = new LocalizerFacade($this->localizer);

        if (empty($actionInterruption->extraData['transactionHash'])) {
            throw new RuntimeException('Key transactionHash not found in extraData of ActionInterruption provided');
        }
        $transaction = Transaction::findByHash($actionInterruption->extraData['transactionHash']);
        if (empty($transaction)) {
            throw new RuntimeException('Transaction by given hash not found');
        }
        $transactionFacade = new TransactionFacade($transaction);

        if (empty($actionInterruption->extraData['transactionDriverIdentifier'])) {
            throw new RuntimeException(
                'Key transactionDriverIdentifier not found in extraData of ActionInterruption provided'
            );
        }
        $driver = $this->getDriver($actionInterruption->extraData['transactionDriverIdentifier']);
        $processingData = $transaction->processingData ?: [];
        $type = $serverRequest->getType();
        switch ($type) {
            case Questionary::TYPE:
                $processingData['answerFromQuestionary'] = $validatedData;
                $transaction->processingData = $processingData;
                $this->processResumeFromInterruptionByQuestionary($transactionFacade, $serverRequest);
                break;
            case Widget::TYPE:
                $processingData['answerFromWidget'] = $validatedData;
                break;
            case ClientRedirection::TYPE:
                $processingData['answerFromClientRedirection'] = $validatedData;
                break;
            default:
                throw new RuntimeException('Unknown ServerRequest type');
        }
        $transaction->processingData = $processingData;
        $transaction->save();

        return $this->finalizeTransactionProcessingResult($driver->makeProcessor($transactionFacade)->process());
    }

    /**
     * @param TransactionProcessingResultInterface $processingResult
     */
    protected static function addResultToTransaction(TransactionProcessingResultInterface $processingResult)
    {
        $transaction = $processingResult->getTransactionFacadeObject()->getTransactionModelInstance();
        $status = $processingResult->getStatus();
        switch ($status) {
            case TransactionProcessingResultInterface::STATUS_SUCCESS:
                $transaction->result = Transaction::RESULT_SUCCESS;
                break;
            case TransactionProcessingResultInterface::STATUS_WAITING:
                $transaction->result = Transaction::RESULT_WAITING;
                break;
            case TransactionProcessingResultInterface::STATUS_ERROR:
                if (static::noPartOfTransactionHaveBeenProcessed($processingResult)) {
                    $transaction->result = Transaction::RESULT_ERROR;
                } else {
                    $transaction->result = Transaction::RESULT_UNDETERMINED;
                }
                break;
            case TransactionProcessingResultInterface::STATUS_FAILURE:
                if (static::noPartOfTransactionHaveBeenProcessed($processingResult)) {
                    $transaction->result = Transaction::RESULT_FAILURE;
                } else {
                    $transaction->result = Transaction::RESULT_UNDETERMINED;
                }
                break;
            case TransactionProcessingResultInterface::STATUS_INTERRUPTION:
            default:
                return;

        }
        $transaction->save();
    }

    /**
     * @param TransactionFacade $transactionFacade
     * @param Questionary $questionary
     */
    protected function processResumeFromInterruptionByQuestionary(TransactionFacade $transactionFacade, Questionary $questionary)
    {
        if (QuestionaryFacadeInterface::CODE_MULTIPLE_QUESTIONARY === $questionary->code) {
            foreach ($questionary->extraData['codesPerDomain'] as $codes) {
                foreach ($codes as $code) {
                    $this->processQuestionaryCode($transactionFacade, $code);
                }
            }
        } else {
            $this->processQuestionaryCode($transactionFacade, $questionary->code);
        }
    }

    protected function processQuestionaryCode(TransactionFacade $transactionFacade, $code)
    {
        $data = $transactionFacade->getAnswerFromQuestionary();
        switch ($code) {
            case QuestionaryFacadeInterface::CODE_CUSTOMER_BIRTH_DATE:
                $birthDate = null;
                $birthDateTimestamp = empty($data['birth_date_date']) ? false : strtotime($data['birth_date_date']);
                if (false !== $birthDateTimestamp) {
                    $birthDate = date('Y-m-d', $birthDateTimestamp);
                } elseif (
                    isset($data['birth_date_year'])
                    and isset($data['birth_date_month'])
                    and isset($data['birth_date_day'])
                    and is_numeric($data['birth_date_year'])
                    and is_numeric($data['birth_date_month'])
                    and is_numeric($data['birth_date_day'])
                ) {
                    $birthDate = $data['birth_date_year'].'-'.$data['birth_date_month'].'-'.$data['birth_date_day'];
                }
                $person = $transactionFacade->getTransactionModelInstance()->salesOrder->acquireBillingPerson();
                if ($birthDate and $person) {
                    $person->dateOfBirth = $birthDate;
                    $person->save();
                }
                break;
            case QuestionaryFacadeInterface::CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER:
                $person = $transactionFacade->getTransactionModelInstance()->salesOrder->acquireBillingPerson();
                if ($person) {
                    $person->nationalIdentificationNumber = $data['nin_number'];
                    $person->save();
                }
                break;
            case QuestionaryFacadeInterface::CODE_CUSTOMER_GENDER:
                $person = $transactionFacade->getTransactionModelInstance()->salesOrder->acquireBillingPerson();
                if ($person) {
                    $person->gender = $data['gender'];
                    $person->save();
                }
                break;
        }
    }

    /**
     * @param TransactionProcessorInterface $processor
     * @return string
     */
    protected function assembleDefaultDomain(TransactionProcessorInterface $processor)
    {
        return 'subscribo/transaction-plugin-manager:'.$processor->getDriverIdentifier();
    }

    /**
     * @param TransactionProcessorInterface $processor
     * @param ServerRequest|null $serverRequest
     * @param InterruptionFacadeInterface|null $interruption
     * @return ActionInterruption
     * @throws \InvalidArgumentException
     */
    protected function generateActionInterruption(
        TransactionProcessorInterface $processor,
        ServerRequest $serverRequest = null,
        InterruptionFacadeInterface $interruption = null
    ) {
        $transactionModelInstance = $processor->getTransactionFacade()->getTransactionModelInstance();
        $serviceId = $transactionModelInstance->serviceId;
        $accountId = $transactionModelInstance->accountId;
        $factory = new ActionInterruptionFactory(get_class($this), $serviceId, $accountId);
        if (empty($interruption)) {
            $transactionHash = $transactionModelInstance->hash;
            $extraData = [
                'transactionDriverIdentifier' => $processor->getDriverIdentifier(),
                'transactionHash' => $transactionHash,
            ];

            return $factory->makeActionInterruption('resumeFromInterruption', $extraData, $serverRequest, true);
        }
        if (empty($serverRequest)) {
            throw new InvalidArgumentException(
                'If interruption argument is provided, then serverRequest argument should be provided as well'
            );
        }
        $actionInterruption = $interruption->getActionInterruptionModelInstance();
        $factory->syncActionInterruptionWithServerRequest($actionInterruption, $serverRequest, true);

        return $actionInterruption;
    }

    /**
     * @param TransactionProcessingResultInterface $processingResult
     * @return array
     */
    protected static function makeResultFromError(TransactionProcessingResultInterface $processingResult)
    {
        $result = $processingResult->export();
        if (TransactionProcessingResultInterface::ERROR_INPUT !== $processingResult->getReason()) {

            return $result;
        }
        $validationErrors = [];
        foreach ($processingResult->getInvalidInputFields() as $cardFieldName => $message) {
            $key = static::mapCardFieldNameToFormFieldName($cardFieldName);
            $validationErrors[$key] = (true === $message) ? $processingResult->getMessage() : $message;
        }
        if (static::takeOnlyAddressFormFields(array_keys($validationErrors))) {
            $validationErrors['address'] = $processingResult->getMessage();
        }
        if ($validationErrors) {
            $result['validationErrors'] = $validationErrors;
        }

        return $result;
    }

    /**
     * @param array $fieldNames
     * @return array
     */
    protected static function takeOnlyAddressFormFields(array $fieldNames)
    {
        $addressFields = ['address', 'gender', 'first_name', 'last_name', 'street', 'post_code', 'city', 'country',
                          'phone', 'mobile', 'delivery_information'];

        return array_intersect($addressFields, $fieldNames);
    }

    /**
     * @param string $cardFieldName
     * @return string
     */
    protected static function mapCardFieldNameToFormFieldName($cardFieldName)
    {
        $key = strval($cardFieldName);
        switch ($key) {
            case 'socialSecurityNumber':
            case 'nationalIdentificationNumber':
                $mapped = 'nationalIdentificationNumber';
                break;
            case 'address1':
            case 'address2':
                $mapped = 'street';
                break;
            case 'postcode':
                $mapped = 'postCode';
                break;
            case 'title':
                $mapped = 'prefix';
                break;
            case 'company':
                $mapped = 'companyName';
                break;
            default:
                $mapped = strval($key);
        }

        return Str::snake($mapped);
    }

    /**
     * @param TransactionProcessingResultInterface $processingResult
     * @return bool
     */
    private static function noPartOfTransactionHaveBeenProcessed(TransactionProcessingResultInterface $processingResult)
    {
        return ((TransactionProcessingResultInterface::NO === $processingResult->moneyAreTransferred())
            and (TransactionProcessingResultInterface::NO === $processingResult->moneyAreReserved()));
    }
}
