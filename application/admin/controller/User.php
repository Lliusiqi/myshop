<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Users;
use app\admin\controller\Common;

class User extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        
        $data = Users::paginate(3);
        $this -> assign('list',$data);
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function add()
    {
        return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function doAdd(Request $request)
    {
        $data = $request -> param();
        // dump($data);die;
        $user = new Users;
        $res = $user -> save($data);
        if ($res) {
            $this ->success('添加成功！','index');
        }else{
            $this ->error('添加失败！');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $id = $request -> param('user_id');
        $data = Users::get($id);
        return view('update',['list'=>$data]);
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
    public function doUpdate(Request $request)
    {
        $id['user_id'] = input('user_id');
        $data = $request -> except('user_id');
        $user = new Users;
        $res = $user -> save($data,$id);
        if ($res) {
            $this ->success('修改成功！','index');
        }else{
            $this ->error('修改失败！');
        }
    }
    
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function del()
    {
        $id = input('user_id');
        $res = Users::destroy($id);
        if ($res) {
            $this -> redirect('index');
        }else{
            $this -> success('删除失败！');
        }
    }
    public function doUser()
    {
        $data = input('post.');
        $res = Users::where('user_name',$data['name']) -> find();
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
    }
    public function doup(){
        $data = input('post.name');
        $id = input('post.id');
        $res = Users::where('user_name',$data) -> find();
        // dump($res);die;
        if ($res) {
            if ($res['user_id'] == $id) {
                echo 0;
            }else{
                echo 1;
            }
        }else{
            echo 0;
        }
    }
    
}
