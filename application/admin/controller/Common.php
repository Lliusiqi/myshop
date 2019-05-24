<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Common extends Controller
{
    protected function initialize(){
        $this -> checkSession();
    }
    /**
     * 检查session是否存在
     *
     * @return \think\Response
     */
    public function checkSession()
    {
        if (!Session::has('name')) {
            $this -> redirect('Login/index');
        }
    }

}
