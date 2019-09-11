<?php

namespace app\core;

use yii\base\Exception;

class QcException extends Exception
{
    private $errorData;//错误详情

    function __construct(array $error, $errorData = null, $previous = null)
    {
        $message = $error['message'];
        $this->errorData = $errorData;
        parent::__construct($message, $error['code'], $previous);
    }

    function getName()
    {
        return get_class($this);
    }

    function getErrorData()
    {
        return $this->errorData;
    }
}