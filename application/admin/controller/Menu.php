<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Menu as MenuModel;
use app\admin\controller\Common;

class Menu extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if (request() -> isAjax()) {
            $count = MenuModel::count();
            $page = input('get.page');
            $limit = input('get.limit');
            $res = MenuModel::page($page,$limit) -> order('menu_id','desc') -> select();
            $data = createTrees($res);
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
        //查询数据库
        $res = MenuModel::select();
        //无限极分类处理
        $res = createTrees($res);
        // 将结果传给模板
        $this -> assign('data',$res);
        // $ress = MenuModel::where('is_show','1') -> select();
        // // 将结果传给模板
        // $this -> assign('datas',$ress);

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
        $data = $request -> post();
        // dump($data);die;
        $res = MenuModel::create($data);
        if ($res) {
            $this -> redirect('Menu/index');
        }else{
            $this -> error('添加失败');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {   
        $res = MenuModel::get($id);
        $data = MenuModel::select();
        //无限极分类
        $data = \createTrees($data);
        // dump($res);die;
        $this -> assign(['datas'=>$data,'data'=>$res]);
        return view();
    }
    public function doUpdate()
    {

        $data = input('post.');
        // dump($data);die;
        $user = new MenuModel;
        $res = $user -> allowField(true) -> save($data,['menu_id'=>$data['menu_id']]);
        if ($res) {
            $this -> redirect('Menu/index');
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
            $res = MenuModel::destroy($id);
            if ($res) {
                echo 1;
            }else{
                echo 0;
            }
        }
        
    }
    public function ajaxUpdate(){
        $id['menu_id'] = input('post.id');
        $data['menu_name'] = input('post.value');
        
        $user = new MenuModel;
        $res = $user -> save($data,$id);
        // echo $user ->getLastSql();
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
    }
}
