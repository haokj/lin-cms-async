<?php

namespace LinCmsAsync;

use think\Container;
use think\swoole\facade\Task;

/**
 * Class LinTask
 * @package app\lib\task
 * @method void email(string $to, string $title, $content) static 发送邮件
 */
class LinTask
{
    /**
     * 调用自定义任务
     * @param $class
     * @param $params
     * @return void
     */
    public static function custom($class, $params)
    {
        self::__callStatic($class, $params);
    }

    /**
     * @param $class
     * @param $arguments
     */
    public static function __callStatic($class, $arguments)
    {
        $libPath = config('lin_task.lib_path') ?? 'app\lib\task';
        $complete_namespace = $libPath . '\\' . ucfirst($class);
        self::asyncTask($complete_namespace, $arguments);
    }

    protected static function asyncTask($class, $arguments)
    {
        Task::async(function () use ($class, $arguments) {
            $class = Container::get($class);
            $class->run($arguments);
        });
    }
}