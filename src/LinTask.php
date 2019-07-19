<?php

namespace LinCmsAsync;

use LinCmsAsync\lib\SwooleClient;

/**
 * Class LinTask
 * @package app\lib\task
 * @method void email(void $to, string $title, $content) static 发送邮件
 */
class LinTask
{
    public static function __callStatic($method, $args)
    {
        $class = self::getClass($method, $args);
        $data = [
            'class' => $class,
            'method' => $method,
            'data' => $args
        ];
        SwooleClient::getInstance()->sSend(json_encode($data));
    }

    /**
     * @param string $method 方法名
     * @param array $args 参数
     * @throws \Exception
     * @return string
     */
    public static function getClass(&$method, &$args)
    {

        if($method == 'email' && (
            (!isset($args[0])) || !is_string($args[0]) || (!class_exists($args[0]))  
            )) {
            $class = 'LinCmsAsync\\template\\Email';
            $method = 'run';
        } else {
            if (is_array($args) && isset($args[0]) && !empty($args[0])) {
                if (!class_exists($args[0])) {
                    throw new \Exception('自定义类不存在：'.$args[0]);
                } else if (!method_exists($args[0], $method)) {
                    throw new \Exception('自定义方法不存在：'.$method);
                } else {
                    $class = $args[0];
                    array_shift($args);
                }
            } else {
                throw new \Exception('参数错误');
            }
        }
        
        return $class;
    }
}