<?php namespace Subscribo\Exception\Handlers;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Exception\Interfaces\HttpExceptionInterface as ExtendedHttpExceptionInterface;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Interfaces\ExceptionHandlerInterface;
use Subscribo\Exception\Factories\MarkableExceptionFactory;

/**
 * Class ApiExceptionHandler
 *
 * Handler to handle Exceptions within Laravel Framework
 *
 * @package Subscribo\Exception
 */
class ApiExceptionHandler extends Handler implements ExceptionHandlerInterface {

    protected $dontReport = ['Subscribo\\Exception\\Interfaces\\DoNotReportInterface'];

    /** @var LocalizerInterface  */
    protected $localizer;

    protected $originalLocale = 'en';

    public function __construct(LoggerInterface $log, LocalizerInterface $localizer)
    {
        $this->localizer = $localizer->duplicate('messages', 'exception');
        parent::__construct($log);
    }


    /**
     * @param Request|null $request
     * @param Exception $e
     * @return Response
     */
    public function render($request = null, Exception $e)
    {
        $mark = null;
        if ($e instanceof MarkableExceptionInterface)
        {
            $mark = $e->getMark();
            $e = $e->getMarkedOriginal();
        }
        $exceptionCode = $e->getCode();
        $message = $e->getMessage();
        if ($e instanceof HttpExceptionInterface) {
            $statusCode = intval($e->getStatusCode());
            $headers = $e->getHeaders();
            if (is_null($message) or ($message === ''))
            {
                $message = $this->getMessageFromStatusCode($statusCode);
            }
            $defaultContentData = [
                'message' => $message,
                'metaData' => [
                    'statusCode' => $statusCode,
                    'exceptionCode' => $exceptionCode,
                ],
            ];
        } else {
            $statusCode = 500;
            $headers = array();
            $defaultContentData = [
                'message' => 'Internal Server Error',
                'metaData' => [
                    'statusCode' => $statusCode,
                    'suggestion' => 'Please contact an administrator or try again later'
                ],
            ];
            if ($mark) {
                $defaultContentData['metaData']['suggestion'] = "Please contact an administrator and provide following exception hash '".$mark."' together with current date and time as well as accessed url or try again later";
            }
        }
        if ($mark) {
            $defaultContentData['metaData']['exceptionHash'] = $mark;
            $defaultContentData['metaData']['exceptionDateTime'] = date('Y-m-d H:i:s');
        }
        if ($request instanceof Request) {
            $defaultContentData['metaData']['accessedUrl'] = $request->getUri();
        }
        $defaultContentData = $this->addSuggestions($defaultContentData);
        if ($e instanceof ContainDataInterface) {
            $content = $e->getOutputData([$e->getKey() => $defaultContentData]);
        } else {
            $content = ['error' => $defaultContentData];
        }
        $response = new Response($content, $statusCode, $headers);
        if ($e instanceof ExtendedHttpExceptionInterface) {
            $response->setStatusCode($statusCode, $e->getStatusMessage());
        }
        return $response;
    }

    /**
     * @param int $statusCode
     * @return string
     */
    protected function getMessageFromStatusCode($statusCode)
    {
        if (array_key_exists($statusCode, Response::$statusTexts))
        {
            return Response::$statusTexts[$statusCode];
        }
        if (500 <= $statusCode) {
            return 'Other Server Error';
        }
        if (400 <= $statusCode) {
            return 'Other Client Error';
        }
        if (300 <= $statusCode) {
            return 'Other Redirect';
        }
        return 'Other Error';

    }


    protected function addSuggestions(array $data)
    {
        try {
            $localizer = $this->localizer;
            $mode = $localizer::CAN_TRANSLATE_MODE_ANY_LOCALE;
            $originalLocale = $this->originalLocale;
            $statusCode = isset($data['metaData']['statusCode']) ? $data['metaData']['statusCode'] : null;
            $exceptionCode = isset($data['metaData']['exceptionCode']) ? $data['metaData']['exceptionCode'] : null;
            $exceptionHash = isset($data['metaData']['exceptionHash']) ? $data['metaData']['exceptionHash'] : null;
            $suggestion = '';
            $originalSuggestion = '';
            if (isset($statusCode) and isset($exceptionCode)) {
                $id = 'suggestions.specific.'.$statusCode.'.'.$exceptionCode;
                $suggestion = $localizer->transOrDefault($id, [], null, null, '', $mode);
                $originalSuggestion = $localizer->transOrDefault($id, [], null, $originalLocale, '', $mode);
            }
            if (isset($statusCode)) {
                $id = 'suggestions.fallback.'.$statusCode;
                if (empty($suggestion)) {
                    $suggestion = $localizer->transOrDefault($id, [], null, null, '', $mode);
                }
                if (empty($originalSuggestion)) {
                    $originalSuggestion = $localizer->transOrDefault($id, [], null, $originalLocale, '', $mode);
                }
            }
            if ($exceptionHash) {
                $id = 'suggestions.marked';
                $parameters = ['%mark%' => $exceptionHash];
                $suggestion .= ' '.$localizer->transOrDefault($id, $parameters, null, null, '', $mode);
                $originalSuggestion .= ' '.$localizer->transOrDefault($id, $parameters, null, $originalLocale, '', $mode);
            }
            $suggestion = trim($suggestion);
            $originalSuggestion = trim($originalSuggestion);
            if ($suggestion === $originalSuggestion) {
                $originalSuggestion = null;
            }
            if ($suggestion) {
                $data['metaData']['suggestion'] = isset($data['metaData']['suggestion'])
                    ? ($data['metaData']['suggestion'].' '.$suggestion)
                    : $suggestion;
            }
            if ($originalSuggestion) {
                $data['metaData']['originalSuggestion'] = isset($data['metaData']['originalSuggestion'])
                    ? ($data['metaData']['originalSuggestion'].' '.$originalSuggestion)
                    : $originalSuggestion;
            }
        } catch (Exception $anotherException) {
            $this->log->error(sprintf(
                "Another exception thrown during exception handling: '%s' [%s:%s]",
                $anotherException->getMessage(),
                get_class($anotherException),
                $anotherException->getCode()));
        }
        return $data;
    }


    public function report(Exception $e)
    {
        $report = true;
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                $report = false;
            }
        }
        $context = [];
        if ($e instanceof MarkableExceptionInterface) {
            MarkableExceptionFactory::mark($e);
            $context['exceptionHash'] = $e->useMark();
            $exceptionClassName = get_class($e->getMarkedOriginal());
        } else {
            $exceptionClassName = get_class($e);
        }
        if ($e instanceof ContainDataInterface) {
            $context['exceptionData'] = $e->getData();
        }
        if ($report) {
            $this->log->error($e, $context);
        } else {
            $this->log->debug(sprintf("exception '%s' [%s:%s]", $e->getMessage(), $exceptionClassName, $e->getCode()), $context);
        }
    }

    public function remember(Exception $e, Request $request = null, $rememberMultiple = false)
    {
        $result = $e;
        $report = true;
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                $report = false;
            }
        }
        if (($e instanceof MarkableExceptionInterface) and ($e->isMarkUsed()) and ( ! $rememberMultiple)) {
            // If we know, we have already marked and logged this exception and it is not requested explicitly to log it again, then we just return it
            return $e;
        }
        $context = [];
        if ($report or ($e instanceof MarkableExceptionInterface)) {
            $result = MarkableExceptionFactory::mark($e);
            $context['exceptionHash'] = $result->useMark();
            $exceptionClassName = get_class($result->getMarkedOriginal());
        } else {
            $exceptionClassName = get_class($e);
        }
        if ($e instanceof ContainDataInterface) {
            $context['exceptionData'] = $e->getData();
        }
        if ($request instanceof Request) {
            $context['request']['url'] = $request->getRequestUri();
        }
        if ($report) {
            $this->log->error($e, $context);
        } else {
            $this->log->debug(sprintf("exception '%s' [%s:%s]", $e->getMessage(), $exceptionClassName, $e->getCode()), $context);
        }
        return $result;
    }


    /**
     * @param Exception $e
     * @param Request $request
     * @return Response
     */
    public function handle(Exception $e, Request $request = null)
    {
        $remembered = $this->remember($e, $request);

        $response = $this->render($request, $remembered);

        return $response;
    }

}
