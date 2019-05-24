<?php
namespace app\index\controller;
use think\Controller;
use app\admin\model\Category;

class Index extends Common
{
    public function index()
    {
    	//获取楼层
    	$cate_id=1;
    	$FloorInfo=$this->getFloorInfo($cate_id);
    	$this->getLeftCateInfo();
    	$this->assign('FloorInfo',$FloorInfo);
        return view();
    }

    public function getFloorInfo($cate_id)
    {
    	//拿顶级id下的子分类
    	$info=[];
    	$cate_model=model('Category');
    	$topwhere=[
    		['cate_id','=',$cate_id],
    		['is_show','=',1]
    	];
    	$info['firstInfo']=$cate_model->field('cate_id,cate_name')->where($topwhere)->find();
    	// print_r($info['firstInfo']);die;
    	//拿二级分类
    	$secondwhere=[
    		['parent_id','=',$cate_id],
    		['is_show','=',1]
    	];
    	$info['secondInfo']=$cate_model->field('cate_id,cate_name')->where($secondwhere)->select();

    	//拿商品数据  获取当前顶级分类下的所有字分类
    	$cateInfo=$cate_model->where('is_show',1)->select();
    	$id=getCateId($cateInfo,$cate_id);
    	$goods_model=model('Goods');
    	$goodswhere=[
    		['cate_id','in',$id],
    		['is_on_sale','=',1]
    	];
    	$info['goodsInfo']=$goods_model->where($goodswhere)->limit(8)->select();
    	//print_r($info['goodsInfo']);die;
    	return $info;
    }

    //获取下一楼数据
    public function getMore()
    {
        $cate_id=input('post.cate_id',0,'intval');
        $floor_num=input('post.floor_num',0,'intval');
        if(empty($cate_id)){
            echo '没有此分类数据';
        }
        $where=[
         ['parent_id','=',0],
         ['cate_id','>',$cate_id]
        ];
        $cate_model=model('Category');
        $cate_id=$cate_model->where($where)->order('cate_id','asc')->limit(1)->value('cate_id');
        // print_r($info);exit;
        $info=$this->getFloorInfo($cate_id);
        $floor_num+=1;
        // $floor_num=$floor_num+1;
        $this->view->engine->layout(false);
        // echo "<div>hello world</div>";
        $this->assign('FloorInfo',$info);
        $this->assign('floor_num',$floor_num);
        echo $this->fetch("div");
    }

}
