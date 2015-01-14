<?php namespace Subscribo\Exception\Handlers;

use Exception;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Response as LaravelResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Subscribo\Config\Config;
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

    protected $dontReport = ['Subscribo\\Exception\\Interfaces\\HttpClientErrorInterface'];

    /**
     * @param Request|null $request
     * @param Exception $e
     * @return LaravelResponse|Response
     */
    public function render($request = null, Exception $e)
    {
        //   return  \App::make('App\Exceptions\Handler')->render($request, $e);
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
            $suggestion = $this->getSuggestion($statusCode, $exceptionCode);
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
            if ($suggestion) {
                $defaultContentData['metaData']['suggestion'] = $suggestion;
            }
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
        if ($e instanceof ContainDataInterface) {
            $content = $e->getOutputData($defaultContentData);
        } else {
            $content = $defaultContentData;
        }
        $response = new LaravelResponse($content, $statusCode, $headers);
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
        return 'Other Error';

    }

    protected function getSuggestion($statusCode, $exceptionCode)
    {
        $config = new Config(null, dirname(dirname(dirname(dirname(__FILE__)))), true);
        $config->loadFile('suggestions');
        $suggestion = $config->get('suggestions.'.$statusCode.'.'.$exceptionCode);
        return $suggestion;
    }

    public function report(Exception $e)
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return null;
            }
        }
        $context = [];
        if ($e instanceof MarkableExceptionInterface) {
            MarkableExceptionFactory::mark($e);
            $context['exceptionHash'] = $e->useMark();
        }
        if ($e instanceof ContainDataInterface) {
            $context['exceptionData'] = $e->getData();
        }
        $this->log->error($e, $context);

    }

    public function remember(Exception $e, Request $request = null, $rememberMultiple = false)
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return $e;
            }
        }
        if (($e instanceof MarkableExceptionInterface) and ($e->isMarkUsed()) and ( ! $rememberMultiple)) {
            // If we know, we have already marked and logged this exception and it is not requested explicitly to log it again, then we just return it
            return $e;
        }
        $result = MarkableExceptionFactory::mark($e);
        $context = ['exceptionHash' => $result->useMark()];
        if ($e instanceof ContainDataInterface) {
            $context['exceptionData'] = $e->getData();
        }
        if ($request instanceof Request) {
            $context['request']['url'] = $request->getUri();
        }
        $this->log->error($e, $context);
        return $result;
    }


    /**
     * @param Exception $e
     * @param Request $request
     * @return LaravelResponse|mixed|Response
     */
    public function handle(Exception $e, Request $request = null)
    {
        $remembered = $this->remember($e, $request);

        $response = $this->render($request, $remembered);

        return $response;
    }

}
