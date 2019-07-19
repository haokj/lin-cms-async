<?php

namespace LinCmsAsync\lib;

use think\swoole\Server;

class SwooleServer extends Server
{
	protected $host = '127.0.0.1';
    protected $port = 9501;
    protected $mode = SWOOLE_PROCESS;
    protected $sockType = SWOOLE_SOCK_TCP;
    protected $serverType = 'server';
    protected $option = [
        'worker_num'=> 4,
        'daemonize'	=> false,
        'task_worker_num' => 2,
    ];

    public function __construct()
    {
        $this->initialize();
        parent::__construct();
    }

    private function initialize()
    {
        $conf = config('swoole_server.');
        if ($conf) {
            if (isset($conf['host']))   $this->host = $conf['host'];
            if (isset($conf['port']))   $this->port = $conf['port'];
            if (isset($conf['sock_type']))   $this->sockType = $conf['sock_type'];
            if (isset($conf['type']))   $this->serverType = $conf['type'];
            foreach ($conf as $k=>$v) {
            
                if (!in_array($k, ['host','port','type','mode','sock_type','swoole_class']) && strpos('on',$k)!==0) {
                	$this->option[$k] = $v;
                }
            }
        }
    }

    public function onConnect($server, $fd)
    {
    }

    public function onReceive($server, $fd, $reactor_id, $data)
    {
        $task_id = $server->task($data);
    }

    public function onTask($server, $task_id, $reactor_id, $data)
    {
        $data = json_decode($data, true);
        $class = $data['class'];
        $method = $data['method'];
        $obj = new $class();
        $obj->$method($data['data']);
    }

    public function onFinish($server, $task_id, $data)
    {

    }
}