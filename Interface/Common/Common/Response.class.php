<?php
/**
 * 使用3种方式输出通信数据
 * 1 json格式输出数据
 * 2 XML格式输出数据
 * 3 josn和XML混合数据数据
 */

namespace Common\Common;

class Response {
    const JSON = "json";
    /**
    * 按综合方式输出通信数据
    * @param integer $code 状态码
    * @param string $message 提示信息
    * @param array $data 数据
    * @param string $judy_type(array) 数据类型
    * return string
    */
    public static function show($code, $message = '', $data = array(), $type = self::JSON) {
        if(!is_numeric($code)) {
            return '';
        }

        $type = isset($_GET['format']) ? $_GET['format'] : self::JSON;

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );

        if($type == 'json') {
            self::json($code, $message, $data);
            exit;
        } elseif($type == 'array') {
            var_dump($result);
        } elseif($type == 'xml') {
            self::xmlEncode($code, $message, $data);
            exit;
        } else {
            // TODO
        }
    }

    /**
    * 按json方式输出通信数据
    * @param integer $code 状态码
    * @param string $message 提示信息
    * @param array $data 数据
    * return string
    */
    public static function json($code, $message = '', $data = array()) {

        if(!is_numeric($code)) {
            return '';
        }

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        echo json_encode($result);
        exit;
    }

    /**
    * 按xml方式输出通信数据
    * @param integer $code 状态码
    * @param string $message 提示信息
    * @param array $data 数据
    * return string
    */
    public static function xmlEncode($code, $message, $data = array()) {
        if(!is_numeric($code)) {
            return '';
        }

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );

        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<root>\n";

        $xml .= self::xmlToEncode($result);

        $xml .= "</root>";
        echo $xml;
    }

    //将xml数据解析
    public static function xmlToEncode($data) {
        $xml = $attr = "";
        foreach($data as $key => $value) {
            if(is_numeric($key)) {
                $attr = " id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::xmlToEncode($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }

}