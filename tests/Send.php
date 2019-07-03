<?php

use LinCmsAsync\LinTask;
use think\Request;

class Send {
	
	/**
	 * 发送邮件 形式1
	 */
	public function sendEmailOne(Request $request)
	{
		$data = $request -> post();
        //异步任务
        LinTask::custom('wechat',$data);#自定义任务
        //发送成功
        return writeJson(201,'','ok',0);
	}

	/**
	 * 发送邮件 形式2
	 */
	public function sendEmailTwo(Request $request)
	{
		$data = $request -> post();
        //异步任务
        LinTask::email($data['to'], $data['title'], $data['content']);#内置任务
        //发送成功
        return writeJson(201,'','ok',0);
	}
}