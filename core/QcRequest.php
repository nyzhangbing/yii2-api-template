<?php

namespace app\core;

use yii\helpers\VarDumper;
use yii\log\Logger;

class QcRequest
{
    public $url;
    public $params;
    public $headers;
    public $body;
    public $method;

    public function __construct($url, $method, array $params = [], array $headers = [], array $body = [])
    {
        $this->url = $url;
        $this->method = strtoupper($method);
        $this->params = $params;
        $this->body = $body;
        $this->headers = $headers;
        self::build_head();
    }

    public function send()
    {
        $logData = [
            'url' => $this->url,
            'method' => $this->method,
            'req_headers' => VarDumper::export($this->headers),
            'req_params' => VarDumper::export($this->params),
            'req_body' => VarDumper::export($this->body),
            'slow' => false,
        ];
        $logLevel = Logger::LEVEL_INFO;
        try {
            $beginTime = microtime(true);
            $ch = curl_init();
            if (!empty($this->params)) {
                $this->url .= '?' . self::build_query($this->params);
            }
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HEADER => false,
                CURLOPT_NOBODY => false,
                CURLOPT_CUSTOMREQUEST => $this->method,
                CURLOPT_URL => $this->url
            ];
            $headers = [];
            foreach ($this->headers as $key => $val)
                array_push($headers, "$key: $val");
            $options[CURLOPT_HTTPHEADER] = $headers;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            if (!empty($this->body)) {
                $options[CURLOPT_POSTFIELDS] = self::build_post($this->body);
            }
            curl_setopt_array($ch, $options);
            $ret = curl_exec($ch);
            $logData['response_body'] = VarDumper::export($ret);
            $logData['response_code'] = -1;
            if ($ret === false) {
                throw new \Exception(curl_error($ch));
            }
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $logData['response_code'] = $responseCode;
            if ($responseCode !== 200)
                throw new \Exception(VarDumper::export($ret));
            $costTime = microtime(true) - $beginTime;
            if ($costTime >= \Yii::$app->params['execute_slow_time']) {
                $logData['slow'] = true;
                $logLevel = Logger::LEVEL_WARNING;
            }
            $logData['cost_time'] = microtime(true) - $beginTime;
            return $this->formatResponse($ret);
        } catch (\Exception $ex) {
            $logData['error'] = $ex->getMessage();
            $logLevel = Logger::LEVEL_ERROR;
            throw $ex;
        } finally {
            if (isset($ch))
                curl_close($ch);
            if ($logLevel === Logger::LEVEL_INFO) {
                \Yii::info($logData, 'curl');
            } elseif ($logLevel === Logger::LEVEL_WARNING) {
                \Yii::warning($logData, 'curl');
            } elseif ($logLevel === Logger::LEVEL_ERROR) {
                \Yii::error($logData, 'curl');
            }
        }
    }

    private function build_head()
    {
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    private function build_query($params, $numericPrefix = '', $argSeparator = '&', $prefixKey = '')
    {
        $str = '';
        foreach ($params as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                $prefixKey .= '[]';
                if (is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    private function build_post($params, $numericPrefix = '', $argSeparator = '&', $prefixKey = '')
    {
        $str = '';
        foreach ($params as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= $key . '=' . urlencode($val);
                } else {
                    $str .= $prefixKey . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey === '') {
                    $prefixKey .= $key;
                }
                $prefixKey .= '[]';
                $str .= $argSeparator . $this->build_post($val, $numericPrefix, $argSeparator, $prefixKey);
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    protected function formatResponse($response)
    {
        return $response;
    }
}