<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'admin_name' => 'require|max:10|min:2',
        'pwd' => 'require|alphaDash|min:8|max:20',
        're_pwd' => 'require|confirm:pwd',
        'email' => 'require|email',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'admin_name.require' => '用户名不能为空！',
        'admin_name.max' => '用户名最大为10个字符！',
        'admin_name.min' => '用户名最小为2个字符！',
        'pwd.require' => '密码不能为空！',
        'pwd.max' => '密码最大为20位！',
        'pwd.min' => '密码最小位8位！',
        'pwd.alphaDash' => '密码只能包含字母和数字，下划线 _ 及破折号 -',
        're_pwd.require' => '重复密码不能为空！',
        're_pwd.confirm' => '两次密码不一致',
        'email.require' => '邮箱不能为空！',
        'email.email' => '邮箱格式不正确！',
    ];
    // edit 验证场景定义
    public function sceneEdit()
    {
        return $this->only(['admin_name','pwd','repwd','email'])
        ->remove('pwd', 'require')
        ->remove('repwd', 'require');
    }
}
