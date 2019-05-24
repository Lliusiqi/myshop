<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\captcha\Captcha;
use app\admin\model\Admin;
use think\facade\Session;

class Login extends Controller
{
    public function verify()
    {
        $config = [
            // 验证码字体大小
            'fontSize' => 50,
            // 验证码位数
            'length' => 3,
            // 关闭验证码杂点
            'useNoise' => false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = input('post.');
        $verify = $data['verify']??'';
        // dump($data);
        if ($verify) {
            // 检测输入的验证码是否正确，$value为用户输入的验证码字符串
            if( !captcha_check($data['verify']))
            {
                $this -> assign([
                    'errorMsg'=>'验证码错误',
                    'data' => $data,
                ]);
            }else{
                //检测通过，查询数据库是否存在该用户
                $user = Admin::where('admin_name', $data['admin_name'])->find();
                // dump($user);
                $sale = $user['sale'];
                $pwd = md5(md5($data['admin_pwd']).$sale);//echo $pwd;die;
                if ($pwd !== $user['pwd']) {
                    $this -> assign([
                        'errorMsg'=>'密码错误',
                        'data' => $data,
                    ]);
                }else{
                    Session::set('name',$user);
                    $this -> redirect('Index/index');
                }

            }
        }
        
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function unLogin()
    {
        Session::delete('name');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
