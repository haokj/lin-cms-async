<?php

namespace LinCmsAsync;

class LinTimer
{
	/**
	 * 创建一个定时器
	 */
	public function tick()
	{
		$params = func_get_args();
		if(count($params) < 2) {
			throw new \Exception('缺少参数');
		}

		$msec = array_shift($params);
		$callback = array_shift($params);

		$this->checkSec($msec);
		$this->checkCallback($callback);

		return \Swoole\Timer::tick($msec, function($timer_id) use ($callback, $params){
				array_push($params, $timer_id);
				call_user_func_array($callback, $params);
		});
	}

	/**
	 * 创建一个一次性定时器
	 */
	public function after()
	{
		$params = func_get_args();
		if(count($params) < 2) {
			throw new \Exception('缺少参数');
		}

		$msec = array_shift($params);
		$callback = array_shift($params);

		$this->checkSec($msec);
		$this->checkCallback($callback);

		return \Swoole\Timer::after($msec, function() use ($callback, $params){
				call_user_func_array($callback, $params);
		});
	}

	/**
	 * 清除定时器
	 */
	public function clear($timer_id)
	{
		\Swoole\Timer::clear($timer_id);
	}

	public function getVersion()
	{
		if (function_exists('swoole_version')) {
			return swoole_version();
		}
		throw new \Exception('缺少swoole扩展');
	}

	//检测回调函数
	private function checkCallback($callback)
	{
		if (is_string($callback)) {
			if(!function_exists($callback) && !($callback instanceof \Closure)) {
				throw new \Exception('回调函数不正确');
			}
		}
		if (is_array($callback)) {
			if (count($callback) < 2) {
				throw new \Exception('回调函数不正确');
			}
			if (!method_exists($callback[0], (string)$callback[1])) {
				throw new \Exception($callback[0].'类中不存在方法：'.(string)$callback[1]);
			}
		}
		return true;
	}

	//检测时间毫秒
	private function checkSec($sec)
	{
		if (!is_numeric($sec) || empty($sec)) {
			throw new \Exception('请传入正确毫秒参数');
		}
		$version_rst = $this->checkVersion('4.2.10');
		if (!$version_rst) {
			if ((int)$sec > 86400000) {
				throw new \Exception('swoole 4.2.10 以下定时毫秒数不能大于86400000');
			}
		}
		return true;
	}

	//检测当前版本 与 传入的版本比较
	private function checkVersion($the_version)
	{
		$version = $this->getVersion();

		$the_versions = explode('.', $the_version);
		$versions = explode('.', $version);
		foreach ($the_versions as $key=>$val) {
			if ((int)$val > (int)$versions[$key]) {
				return false;
			}
			if ((int)$val < (int)$versions[$key]) {
				return true;
			}
		}
		return $version;
	}

}