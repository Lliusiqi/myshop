<?php
namespace app\index\controller;

use think\Controller;

class User extends Common
{
	public function __construct()
	{
		parent::__construct();
		if(!$this->checkLogin()){
           $this->error("请先登录",url('Login/login'));
		}
	}
	//个人中心首页
	public function userList()
	{
		if(!$this->checkLogin()){
           $this->error("请先登录",url('Login/login'));
		}
		return view();
	}
}