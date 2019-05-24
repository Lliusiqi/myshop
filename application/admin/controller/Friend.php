<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Friend as FriendModel;

class Friend extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = FriendModel::select();
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
        $data = input('post.');
        // dump($data);
        $validate = new \app\admin\validate\Friends;
        $result = $validate->check($data);
        // die;
        if(!$result){
            $this -> error($validate->getError());
        }else{
            $res1 = FriendModel::where('name',$data['name']) -> count();
            if ($res1) {
                $this -> error('该名称已存在');
            }else{
                $res = FriendModel::create($data);
                if ($res) {
                    $this -> success('添加成功','Friend/index');
                }else{
                    $this -> error('添加失败');
                }
            }
            
        }
        
    }

    public function upload()
    {
        $res = upload('file');
        echo $res;
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // echo $id;
        $res = FriendModel::destroy($id);
        if ($res) {
            $this -> redirect('Friend/index');
        }else{
            $this -> success('删除失败');
        }
    }
    public function doAjax()
    {
        if (request() -> isAjax()) {
            $value = input('value');
            // echo $value;
            $res = FriendModel::where('title',$post['title']) -> count();
            echo $res;
        }
        
    }
}
