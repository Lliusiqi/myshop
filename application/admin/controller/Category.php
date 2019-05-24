<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Category as CategoryModel;
use app\admin\controller\Common;

class Category extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if (request() -> isAjax()) {
            $count = CategoryModel::count();
            $page = input('get.page');
            $limit = input('get.limit');
            $res = CategoryModel::page($page,$limit) -> order('addtime','desc') -> select();
            $data = \createTree($res);
            if ($data) {
                foreach ($data as $key => $v) {
                    $data[$key]['level'] = str_repeat('|--',$v['level']-1);
                }
            }
            $data['code'] = 0;
            $data['msg'] = '';
            $data['count'] = $count;
            $data['data'] = $res;
            $data = \json_encode($data);
            echo $data;
        }else{
            return view();
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function add()
    {
        $res = CategoryModel::select();
        //无限极分类
        $res = \createTree($res);
        // dump($res);die;
        $this -> assign('data',$res);
        return view();
    }
    public function doAdd()
    {
        $data = input('post.');
        $data['is_show'] = empty($data['is_show'])?0:1;
        $data['is_nav_show'] = empty($data['is_nav_show'])?0:1;
        $res = CategoryModel::create($data);
        if ($res) {
            $this -> redirect('Category/index');
        }else{
            $this -> error('添加失败');
        }
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
        
        // echo $id;
        $res = CategoryModel::get($id);
        $data = CategoryModel::select();
        //无限极分类
        $data = \createTree($data);
        // dump($res);die;
        $this -> assign(['datas'=>$data,'data'=>$res]);
        return view();
    }
    public function doUpdate()
    {
        $data = input('post.');
        $data['is_show'] = empty($data['is_show'])?0:1;
        $data['is_nav_show'] = empty($data['is_nav_show'])?0:1;
        // dump($data);die;
        $user = new CategoryModel;
        $res = $user -> allowField(true) -> save($data,['cate_id'=>$data['cate_id']]);
        if ($res) {
            $this -> redirect('Category/index');
        }else{
            $this -> error('修改失败');
        }
    }
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        if (request() -> isAjax()) {
            $id = input('get.id');
            // echo $id;
            $res = CategoryModel::destroy($id);
            if ($res) {
                echo 1;
            }else{
                echo 0;
            }
        }
        
    }
    public function check()
    {
        if (request() -> isAjax()) {
            $cate_id = input('post.cate_id');
            $is_show = input('post.is_show')==1?0:1;
            $user = new CategoryModel;
            $res = $user -> save(['is_show'=>$is_show],['cate_id'=>$cate_id]);
        }
    }
    public function checks()
    {
        if (request() -> isAjax()) {
            $cate_id = input('post.cate_id');
            $is_nav_show = input('post.is_nav_show')==1?0:1;
            $user = new CategoryModel;
            $res = $user -> save(['is_nav_show'=>$is_nav_show],['cate_id'=>$cate_id]);
        }
    }
}
