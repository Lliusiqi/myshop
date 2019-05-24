<?php

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
      'u_email'=>'require|email|unique:user|checkEmail',
      'u_code'=>'require|checkCode',
      'u_pwd'=>'require|checkPwd',
      'u_pwd1'=>'require|confirm:u_pwd'

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
       'u_email.require'=>'邮箱不能为空',
       'u_email.email'=>'邮箱格式有误',
       'u_email.unique'=>'邮箱已被注册',
       'u_code.require'=>'验证码必填',
       'u_pwd.require'=>'密码必填',
       'u_pwd1.require'=>'确认密码必填',
       'u_pwd1.confirm'=>'确认密码必须与密码一致',
    ];

 protected function checkEmail($value,$rule,$data){
    $emailInfo=session('emailInfo');
    if($value==$emailInfo['email']){
        return true;
    }else{
        return '当前注册邮箱与发送邮件邮箱不一致';
    }
 }

 protected function checkCode($value,$rule,$data){
     $emailInfo=session('emailInfo');
     if($value!=$emailInfo['code']){
        return '验证码有误';
     }else if((time()-$emailInfo['send_time'])>300){
        return '验证码已失效,五分钟内输入有效';
     }else{
        return true;
     }

 }

 protected function checkPwd($value,$rule,$data){
   $reg='/^[a-z0-9]{6,12}$/';
   if(!preg_match($reg,$value)){
     return '密码允许数字字母组成6-12位';
   }else{
    return true;
   }
 }

}
