<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Admin;
use app\admin\controller\Common;

class Index extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // dump(setSale());
        // $dale = setSale();
        // dump($dale);
        return view();
    }
    public function admin(Request $request)
    {
        $types = input('get.types');
        $keywords = input('get.keywords');
        // dump($types);
        $where = [];
        if ($types == 'admin_name') {
            $where[] = ['admin_name','like',"%$keywords%"];
        }
        if($types == 'email'){
            $where[] = ['email','like',"%$keywords%"];
        }
        // dump($where);
        $data = Admin::where($where) -> order('admin_id','desc') -> paginate(3,false,['query' => 
            [
                'types' => $types,
                'keywords' => $keywords,
            ]]);
        // echo Admin::getLastSql();
        $this -> assign('list',$data);
        // return view('index',['list'=>$data]);
        if ($request -> isAjax()) {
            return view('page');
        }
        return view();
    }
    public function add()
    {
        return view('add');
    }
    public function doUser()
    {
        $data = input('post.');
        $id = input('post.id');
        $res = Admin::where('admin_name',$data['name']) -> find();
        if ($res) {
            if ($res['admin_id'] == $id) {
                echo 0;
            }else{
                echo 1;
            }
            // echo "<font color='red'>该用户名已存在</font>";
        }else{
            echo 0;
            // echo "<font color='green'>√</font>";
        }
        // dump($data);
    }
    public function doAdd(Request $request)
    {
        $data = $request -> param();
        $validate = new \app\admin\validate\Admin;
        if (!$validate->check($data)) {
            // dump($validate->getError());
            return view('add',[
                'errorMsg' => $validate->getError(),
                'data' => $data
                ]);
        }else{
            $user = new Admin;
            $data['addtime'] = time();
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $sale = setSale();
            $data['sale'] = $sale;
            $data['pwd'] = md5(md5($data['pwd']).$sale);
            $res = $user -> save($data);
            if ($res) {
                $this ->success('添加成功！','admin');
            }else{
                $this ->error('添加失败！');
            }
        }
        
    }
    public function update(Request $request)
    {
        $id = $request -> param('admin_id');
        $data = Admin::get($id);
        return view('update',['list'=>$data]);
    }
    public function doUpdate(Request $request)
    {
        $id['admin_id'] = input('admin_id');
        $data = $request -> except('admin_id');
        $validate = new \app\admin\validate\Admin;
        if (!$validate -> scene('edit') -> check($data)) {
            // dump($validate->getError());
            return view('add',[
                'errorMsg' => $validate->getError(),
                'data' => $data
                ]);
        }else{
            $user = new Admin;
            if ($data['pwd'] && $data['repwd']) {
                $sale = setSale();
                $data['sale'] = $sale;
                $data['pwd'] = md5(md5($data['pwd']).$sale);
                $res = $user -> save($data,$id);
                if ($res) {
                    $this ->success('修改成功！','admin');
                }else{
                    $this ->error('修改失败！');
                }
            }else{
                unset($data['pwd']);
                $res = $user -> save($data,$id);
                if ($res) {
                    $this ->success('修改成功！','admin');
                }else{
                    $this ->error('修改失败！');
                }
            }
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
        $id = input('admin_id');
        $res = Admin::destroy($id);
        if ($res) {
            $this -> redirect('admin');
        }else{
            $this -> success('删除失败！');
        }
    }
    public function ajaxUpdate(){
        $id['admin_id'] = input('post.id');
        $data['admin_name'] = input('post.value');
        
        $user = new Admin;
        $res = $user -> save($data,$id);
        // echo $user ->getLastSql();
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
    }
    
}
