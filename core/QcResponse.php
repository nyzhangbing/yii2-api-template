<?php

namespace app\core;

class QcResponse
{
    public $code;
    public $message;
    public $request_id;
    public $data;
    public $profiling;

    public function __construct($data = null, int $code = 0, string $message = 'ok')
    {
        $this->code = $code;
        $this->message = Common::convertStrEncoding2UTF8($message);
        $this->request_id = \Yii::$app->getRequestId();
        $this->data = $data ?? new \stdClass();
        if (!YII_DEBUG) {
            unset($this->profiling);
        } else {
            //todo:调试模式下设置debug信息
            $this->profiling = [];
            $dbProfiling = \Yii::getLogger()->getDbProfiling();
            $this->profiling['db'] = [
                'sqlCount' => $dbProfiling[0],
                'duration' => $dbProfiling[1]
            ];
        }
    }
}