<?php

namespace app\index\controller;

use think\Controller;

use think\facade\Request;

class Common extends Controller
{
   public function __construct()
   {
        parent::__construct();

    //获取控制器
        $controller_name=Request::controller();
       // echo $controller_name;die;
    //进行if判断
        if($controller_name=='Index'){
             $is_show='leftNav';
        }else{
            $is_show='leftNav none';
        }
        $this->assign('is_show',$is_show);
   }

   //查询左侧导航栏分类数据
   public function getLeftCateInfo()
   {
      //查询导航栏数据
      $cate_model=model('Category');
      $cateInfo=$cate_model->where('is_nav_show',1)->select();
      $this->assign('cateInfo',$cateInfo);
      //查询左侧分类数据
      $cateLeft=$cate_model->where('is_show',1)->select()->toArray();
      //print_r($cateLeft);die;
      //调用common里封装的递归函数
      $cateLeft=getLeftCateInfo($cateLeft);

      $this->assign('cateLeft',$cateLeft);
   }

   //检测是否登录
   public function checkLogin()
   {
      return session("?userInfo");
   }
   //获取用户id
   public function getUserId(){
      return session('userInfo.u_id');
   }
   //同步浏览历史记录
   public function asyncHistory()
   {
     //把cookie中的信息取出来 存入到数据库
    $str=cookie('historyInfo');
    if(!empty($str)){
      // echo $str;exit;
     $info=getBase64Info($str);
     // dump($_COOKIE);
      // print_r($info);exit;
     foreach($info as $k=>$v){
       $info[$k]['user_id']=$this->getUserId();
     }
     // print_r($info);exit;
    $res=model('History')->saveAll($info);
    if($res){
      cookie('userInfo',null);
    }
    }
   }
   //同步购物车数据
   public function asyncCart(){
    $str=cookie('cartInfo');
    if(!empty($str)){
     //解密
      $cartInfo=getBase64Info($str);
     // print_r($cartInfo);exit;
      $user_id=$this->getUserId();
      $cart_model=model('Cart');
      foreach($cartInfo as $k=>$v){
       $where=[
         ['user_id','=',$user_id],
         ['goods_id','=',$v['goods_id']],
         ['is_del','=',1]
       ];
       $info=$cart_model->where($where)->find();
       // print_r($info);
       if(!empty($info)){
           $res=$this->checkGoodsNumber($v['buy_number'],$v['goods_id'],$info['buy_number']);

           if(!$res){
            fail('库存不足');
           }
          //累加 修改
           $cartWhere=[
              ['is_del','=',1],
              ['cart_id','=',$info['cart_id']]
           ];
           $cart_model->where($cartWhere)->update(['buy_number'=>$info['buy_number']+$v['buy_number']]);
       }else{
         $res=$this->checkGoodsNumber($v['buy_number'],$v['goods_id']);
         if(!$res){

          fail('库存不足');
         }
          //添加 入库
           $v['user_id']=$user_id;
           $v['update_time']=time();
           print_r($v);exit;
           $cart_model->insert($v);
        // print_R($v);
       }
      }
      cookie('cartInfo',null);
      // exit;
      // print_r($cartInfo);exit;
    }
    // exit;
   }
//检测库存
   public function checkGoodsNumber($buy_number,$goods_id,$already_number=0){
   $num=$buy_number+$already_number;
   $goods_number=model('Goods')->where('goods_id',$goods_id)->value('goods_number');
   // echo $num;
   // echo $goods_number;exit;
   if($num>$goods_number){
     return false;
   }else{
     return true;
   }
   }
//获取收货地址信息
   public function getAddressInfo(){
    $address_model=model('Address');
    $user_id=$this->getUserId();
    $where=[
       ['user_id','=',$user_id],
       ['is_del','=',1]
    ];
    $addressInfo=$address_model->where($where)->select();
    // if(empty($addressInfo)){
    //   return false;
    // }else{
    if(!empty($addressInfo)){
      //处理 省市区
      $area_model=model('Area');
      foreach($addressInfo as $k=>$v){
        $addressInfo[$k]['province']=$area_model->where("id",$v['province'])->value('name');
        $addressInfo[$k]['city']=$area_model->where("id",$v['city'])->value('name');
        $addressInfo[$k]['area']=$area_model->where("id",$v['area'])->value('name');
      }
      }
      return $addressInfo;
    
    // }
   }

}
