<?php

namespace app\core;

use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;

abstract class LogTarget extends Target
{

    final public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        $body = [
            'level' => $level,
            'category' => $category,
            'timestamp' => $timestamp,
            'datetime' => date('Y-m-d H:i:s', $timestamp),
            'env' => YII_ENV,
        ];
        if (\Yii::$app instanceof \yii\web\Application) {
            $user_id = 0;
            if (!\Yii::$app->user->isGuest)
                $user_id = \Yii::$app->user->getId();
            $body['url'] = \Yii::$app->request->url;
            $body['method'] = \Yii::$app->request->method;
            $body['client_ip'] = \Yii::$app->request->getUserIP();
            $body['req_headers'] = json_encode(\Yii::$app->request->getHeaders()->toArray(), JSON_UNESCAPED_UNICODE);
            $body['req_params'] = json_encode(\Yii::$app->request->get(), JSON_UNESCAPED_UNICODE);
            $body['req_body'] = json_encode(\Yii::$app->request->post(), JSON_UNESCAPED_UNICODE);
            $body['user_id'] = $user_id;
            $body['request_id'] = \Yii::$app->getRequestId();
        } elseif (\Yii::$app instanceof \yii\console\Application) {
            $params = \Yii::$app->request->getParams();
            $body['category'] = $category === 'application' ? 'console' : $category;
            $body['url'] = array_shift($params);
            $body['args'] = implode('&', $params);
        }
        if (!is_string($text)) {
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $body['message'] = (string)$text;
            } else if (is_array($text)) {
                foreach ($text as $key => $value) {
                    if (!is_string($value))
                        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                    $body[$key] = $value;
                }
            } else {
                $body['message'] = VarDumper::export($text);
            }
        } else {
            $body['message'] = $text;
        }
        return $body;
    }
}