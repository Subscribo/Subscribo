<?php return [
    400 => [
        \Subscribo\Exception\Exceptions\InvalidInputHttpException::DEFAULT_EXCEPTION_CODE => 'Check validationErrors for error messages',
    ],
    404 => [
        0 => 'Check your url',
        \Subscribo\Exception\Exceptions\InstanceNotFoundHttpException::DEFAULT_EXCEPTION_CODE => 'Check ID or Identifier of requested object',
    ]
];
