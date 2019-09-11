<?php

namespace app\core;

use app\constants\ErrInfo;
use yii\base\Response;
use yii\web\HttpException;

class ErrorHandler extends \yii\web\ErrorHandler
{

    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        if (\Yii::$app->has('response')) {
            $response = \Yii::$app->getResponse();
            if ($exception instanceof HttpException) {
                $response->setStatusCodeByException($exception);
                $response->data = self::convertExceptionToString($exception);
                \Yii::warning(Common::formatExceptionMessage($exception));
            } else if ($exception instanceof QcException) {
                $response->setStatusCode(200);
                $body = new QcResponse(null, $exception->getCode(), $exception->getMessage());
                $response->data = $body;
                \Yii::warning(Common::formatExceptionMessage($exception));
            } else {
                $response->setStatusCode(200);
                $errorMessage = $exception->getMessage();
                $errorDetail = '';
                if ($exception instanceof \Error) {
                    $errorDetail = Common::formatErrorMessage($exception);
                } else {
                    $errorDetail = Common::formatExceptionMessage($exception);
                }
                $body = new QcResponse(null, ErrInfo::SYSTEM_ERROR['code'], YII_ENV_PROD ? $errorMessage : $errorDetail);
                $response->data = $body;
            }
        } else {
            $response = new Response();
        }
        $response->send();
    }
}