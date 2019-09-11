<?php

namespace app\core;

use yii\console\Application;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;

class LogstashTarget extends LogTarget
{
    public $host;
    public $port;

    const MESSAGE_VALUE_MAX_LENGTH = 4096;

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        try {
            $socket = stream_socket_client("udp://$this->host:$this->port", $errorNumber, $error, 3);
            $messages = array_map([$this, 'formatMessage'], $this->messages);
            foreach ($messages as $message)
                fwrite($socket, json_encode($message, JSON_UNESCAPED_UNICODE));
            fclose($socket);
        } catch (\Exception $ex) {

        }
    }
}