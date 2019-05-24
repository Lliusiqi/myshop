<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Brand;
use app\admin\controller\Common;

class Brands extends Common 
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        
        
        // echo $count;
        if (request() -> isAjax()) {
            $count = Brand::count();
            $page = input('get.page');
            $limit = input('get.limit');
            $res = Brand::page($page,$limit) -> order('brand_id','desc') -> select();
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
        return view();
    }
    public function doAdd()
    {
        $data = input('post.');
        $data['is_show'] = $data['is_show']=='on'?'1':'0';
        $user = new Brand;
        $res = $user -> allowField(true) -> save($data);
        if ($res) {
            $this -> redirect('Brands/index');
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
    public function upload(Request $request)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( './uploads');
        if($info){
        // 成功上传后 获取上传信息
        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
        echo json_encode(['code'=>0,'msg'=>$info->getSaveName()]);
        }else{
        // 上传失败获取错误信息
        echo json_encode(['code'=>1,'msg'=>$file->getError()]);
        }
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
        $res = Brand::get($id);
        $this -> assign('data',$res);
        return view();
    }
    public function doUpdate()
    {
        $data = input('post.');
        $data['is_show'] = empty($data['is_show'])?0:1;
        $user = new Brand;
        $res = $user -> allowField(true) -> save($data,['brand_id'=>$data['brand_id']]);
        if ($res) {
            $this -> redirect('Brands/index');
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
            $res = Brand::destroy($id);
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
            $brand_id = input('post.brand_id');
            $is_show = input('post.is_show')==1?0:1;
            $user = new Brand;
            $res = $user -> save(['is_show'=>$is_show],['brand_id'=>$brand_id]);
        }
    }
}
