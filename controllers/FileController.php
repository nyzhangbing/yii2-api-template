<?php

namespace app\controllers;

use app\constants\ErrInfo;
use app\core\{
    QcController, QcException
};
use yii\web\Response;

class FileController extends QcController
{
    public function actionDownload()
    {
        $url = \Yii::$app->request->get('url', '');
        $fileName = \Yii::$app->request->get('file_name', '');
        if (!$url || !$fileName)
            throw new QcException(ErrInfo::MISS_REQUIRE_PARAMS);
        $content = file_get_contents($url);
        if (!$content)
            throw new \Exception('无法下载文件，请确认文件路径是否正确');
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $downLoadFileName = urlencode($fileName) . '.' . $extension;
        header("Content-Type: application/octet-stream; charset=utf-8");
        header("Accept-Ranges: bytes");
        header("Accept-Length:" . strlen($content));
        header("Content-Disposition: attachment;filename=" . $downLoadFileName);
        header("Content-Transfer-Encoding: binary ");
        \Yii::$app->response->format = Response::FORMAT_RAW;
        return $content;
    }
}