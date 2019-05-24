<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Brand;
use app\admin\model\Goods_photo as Photo;
use app\admin\controller\Common;

class Goods extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if (request() -> isAjax()) {
            $count = GoodsModel::count();
            $page = input('get.page');
            $limit = input('get.limit');
            $res = GoodsModel::page($page,$limit) -> order('goods_id','desc') -> select();
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
    public function upload()
    {
        $res = upload('file');
        // echo $res;die;
        $res = json_decode($res,true);
        // print_r($res);die;
        if ($res['code'] == 0) {
            $thumb = $this ->imgThumb($res['msg']);
            // echo $thumb;
            echo json_encode(['code'=>0,'msg'=>$res['msg'],'thumb'=>$thumb]);
        }else{
            echo json_encode($res);
        }
        
    }
    public function mulUploads()
    {
        $res = upload('file');
        echo $res;
    }
    public function imgThumb($img)
    {
        $image = \think\Image::open('./uploads/'.$img);
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
        // $result = pathinfo($img);
        $length = stripos($img,'.');
        $thumb = substr($img,0,$length).'_thumb'.substr($img,$length);
        // dump($result);die;
        $res2 = $image->thumb(150, 150)->save('./uploads/'.$thumb);
        return $thumb;
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function add()
    {
        //查询数据库
        $res = CategoryModel::select();
        //无限极分类处理
        $res = \createTree($res);
        // 将结果传给模板
        $this -> assign('data',$res);
        $ress = Brand::where('is_show','1') -> select();
        // 将结果传给模板
        $this -> assign('datas',$ress);

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
        // $data['content'] = isset($data['editorValue'])?'':$data['editorValue']; 
        $data['is_new'] = empty($data['is_new'])?0:1;
        $data['is_best'] = empty($data['is_best'])?0:1;
        $data['is_hot'] = empty($data['is_hot'])?0:1;
        $data['is_on_sale'] = empty($data['is_on_sale'])?0:1;
        //检查货号是否存在
        if (isset($data['goods_sn']) && !empty($data['goods_sn'])) {
            $count = GoodsModel::where('goods_sn',$data['goods_sn']) -> count();
            if ($count) {
                $this -> error('该货号已存在！');
            }else{
                $res = GoodsModel::create($data);
                $id = $res -> goods_id;
                $photo = $this -> createPhoto($data['goods_photo'],$id);
                $pp = new Photo;
                $pp -> saveAll($photo);
                if ($res) {
                    $this -> redirect('Goods/index');
                }else{
                    $this -> error('添加失败');
                }
            }
        }else{
            $res = GoodsModel::create($data);
            $id = $res -> goods_id;
            $photo = $this -> createPhoto($data['goods_photo'],$id);
            $pp = new Photo;
            $pp -> saveAll($photo);
            $result = $this -> setGoods_sn($id);
            $gg = new GoodsModel;
            $gg -> save(['goods_sn'=>$result],['goods_id'=>$id]);
            $this -> redirect('Goods/index');
        }
        
        // $data['goods_sn'] = ()??$this -> setGoods_sn($id);
    }
    public function setGoods_sn($id)
    {
        $str = 'ECS0000'.$id;
        $count = GoodsModel::where('goods_sn',$str) -> count();
        if ($count) {
            $strs = rand(1,20).'_'.$id;
            setGoods_sn($strs);
        }else{
            return $str;
        }
        
    }
    public function createPhoto($photo,$id)
    {
        $photo = explode('|',trim($photo,'|'));
        $data =[];
        foreach ($photo as $val) {
            $data[] = [
                'goods_id' => $id,
                'url' => $val
            ];
        }
        return $data;
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
    public function check()
    {
        if (request() -> isAjax()) {
            $data = input('post.');
            $res = GoodsModel::where($data) -> count();
            echo $res;
        }
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
        if (request() -> isAjax()) {
            $id = input('get.id');
            // echo $id;
            $res = GoodsModel::destroy($id);
            if ($res) {
                echo 1;
            }else{
                echo 0;
            }
        }
    }
    public function ajaxUpdate(){
        $id['goods_id'] = input('post.id');
        $data['goods_name'] = input('post.value');
        
        $user = new GoodsModel;
        $res = $user -> save($data,$id);
        // echo $user ->getLastSql();
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
    }
    public function ajaxUpdates(){
        $id['goods_id'] = input('post.id');
        $key = input('post.key');
        $value = input('post.value');
        if ($value == 1) {
            $user = new GoodsModel;
            $res = $user -> save([$key => 0,],$id);
            if ($res) {
                echo 0;
            }
            
        }else{
            $user = new GoodsModel;
            $res = $user -> save([$key => 1,],$id);
            if ($res) {
                echo 1;
            }
            
        }
    }
    public function delAll()
    {
        if (request() -> isAjax()) {
            $id = input('id');
            // dump($id);
            $res = GoodsModel::destroy($id);
            if ($res) {
                echo json_encode(['code'=>1,'msg'=>'删除成功']);
            }else{
                echo json_encode(['code'=>0,'msg'=>'删除失败']);
            }
        }
    }
}
