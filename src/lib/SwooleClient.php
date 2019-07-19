<?php

namespace LinCmsAsync\lib;

use Swoole\Client;

class SwooleClient
{
	private $client;
    /*
     * 定义单例模式的变量
     * */
    private static $_instance = null;

    private $host = '127.0.0.1';
    private $port = 9051;

    public static function getInstance()
    {
        if(empty(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;

    }
    private function __construct()
    {

        // try{
        	if (!empty(config('swoole_server.host'))) {
        		$this->host = config('swoole_server.host');
        	}
        	if (!empty(config('swoole_server.port'))) {
        		$this->port = config('swoole_server.port');
        	}
            //重启
            $this->client = new Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
            $result = $this ->client->connect($this->host, $this->port);
        // }catch(\Exception $e)
        // {
        //     throw new \Exception('连接被拒绝请确认端口是否开启');
        // }


    }

    /*
     *发送邮件
     * */
    public function sSend($data){
        $this->client->send($data);
        return true;
    }

    /*
     * 基础类库编写 魔术方法
     * */
    public function __call($name,$arguments){

        if(count($arguments) != 2){
            return '';
        }
        $this->client->$name($arguments[0],$arguments[1]);
    }
}