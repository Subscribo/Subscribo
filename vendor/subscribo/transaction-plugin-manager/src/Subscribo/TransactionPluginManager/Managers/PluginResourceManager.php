<?php

namespace Subscribo\TransactionPluginManager\Managers;

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
use Subscribo\TransactionPluginManager\Facades\LocalizerFacade;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PluginResourceManager
 *
 * @package Subscribo\TransactionPluginManager
 */
class PluginResourceManager implements PluginResourceManagerInterface
{
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
     * @param int|mixed|string $questionary
     * @param TransactionProcessorInterface $processor
     * @return void
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     */
    public function interruptByQuestionary($questionary, TransactionProcessorInterface $processor)
    {
        $questionaryFactory = new QuestionaryFactory($this->localizer, $this->assembleDefaultDomain($processor));
        $questionaryInstance = $questionaryFactory->make($questionary);
        $this->assembleActionInterruption($questionaryInstance, $processor);

        throw new QuestionaryServerRequestHttpException($questionaryInstance);
    }

    /**
     * @param mixed|string $widget
     * @param TransactionProcessorInterface $processor
     * @return void
     * @throws \Subscribo\RestCommon\Exceptions\WidgetServerRequestHttpException
     */
    public function interruptByWidget($widget, TransactionProcessorInterface $processor)
    {
        $widgetFactory = new WidgetFactory($this->assembleDefaultDomain($processor));
        $widgetInstance = $widgetFactory->make($widget);
        $this->assembleActionInterruption($widgetInstance, $processor);

        throw new WidgetServerRequestHttpException($widgetInstance);
    }

    /**
     * @param mixed|string $redirection
     * @param TransactionProcessorInterface $processor
     * @return void
     * @throws \Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException
     */
    public function interruptByClientRedirection($redirection, TransactionProcessorInterface $processor)
    {
        $clientRedirectionFactory = new ClientRedirectionFactory($this->assembleDefaultDomain($processor));
        $clientRedirectionInstance = $clientRedirectionFactory->make($redirection);
        $this->assembleActionInterruption($clientRedirectionInstance, $processor);

        throw new ClientRedirectionServerRequestHttpException($clientRedirectionInstance);
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

        return ['result' => $driver->makeProcessor($transactionFacade)->process()->export()];
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
     * @param ServerRequest $serverRequest
     * @param TransactionProcessorInterface $processor
     * @return ActionInterruption
     */
    protected function assembleActionInterruption(ServerRequest $serverRequest, TransactionProcessorInterface $processor)
    {
        $transactionModelInstance = $processor->getTransactionFacade()->getTransactionModelInstance();
        $transactionHash = $transactionModelInstance->hash;
        $serviceId = $transactionModelInstance->serviceId;
        $accountId = $transactionModelInstance->accountId;
        $extraData = [
            'transactionDriverIdentifier' => $processor->getDriverIdentifier(),
            'transactionHash' => $transactionHash,
        ];
        $factory = new ActionInterruptionFactory(get_class($this), $serviceId, $accountId);

        return $factory->makeActionInterruption('resumeFromInterruption', $extraData, $serverRequest, true);
    }
}
