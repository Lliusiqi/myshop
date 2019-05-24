<?php

namespace app\index\controller;

use think\Controller;

class Order extends Common
{
    //确认订单
    public function confirmOrder()
    {
    	$goods_id=input("post.goods_id");
    	$address_id=input("post.address_id");
    	$pay_type=input("post.pay_type");
    	$order_talk=input("post.order_talk");
    	$order_model=model('Order');
    	 //开启事务
         $order_model->startTrans();
    	try{
        //验证是否为空
         if(empty($goods_id)){
         	throw new \Exception('请选择一件商品');
         }
         if(empty($address_id)){
         	throw new \Exception('请选择一个收货地址');
         }
          if(empty($pay_type)){
         	throw new \Exception('请选择一个支付方式');
         }
         //  if(empty($order_talk)){
         // 	throw new \Exception('请填写一条留言');
         // }

       
    	//添加订单表的数据
         $order['user_id']=$this->getUserId();
         $order['order_no']=$this->get0rderNo();
         $order['order_amount']=$this->getOrderAmount($goods_id);
         $order['order_talk']=$order_talk;
         $order['pay_type']=$pay_type;
         $res=$order_model->save($order);
         if(empty($res)){
         	throw new \Exception('订单信息写入失败');
         }
         //获取刚刚添加的订单的id
        $order_id=$order_model->order_id;
    	//添加订单收货地址数据
         $address_model=model('OrderAddress');
    	 $add_model=model('Address');
    	 $addressInfo=$add_model->where("address_id",$address_id)->find();
    	 // print_r($addressInfo);exit;
    	 if(empty($addressInfo)){
    	 	throw new \Exception('收货地址不存在');
    	 }
    	 // $address['province']=$addressInfo['province'];
    	 unset($addressInfo['create_time']);
    	 unset($addressInfo['update_time']);
    	 unset($addressInfo['is_del']);
         $addressInfo=$addressInfo->toArray();
         $addressInfo['order_id']=$order_id;
         $res1=$address_model->save($addressInfo);
         // dump($res1);exit;
         if(empty($res1)){
         	throw new \Exception('订单收货地址写入失败');
         }
    	//添加订单商品表数据
             $detail_model=model('OrderDetail');
          // //订单表详情添加
		        //   $goodsInfo=$this->getOrderDetail($goods_id);//商品详情信息
		        //   $user_id=$this->getUserId();
		        //   // print_r($goodsInfo);exit;
		        //   foreach($goodsInfo as $k=>$v){
		        //   	$goodsInfo[$k]['order_id']=$order_id;
		        //   	$goodsInfo[$k]['user_id']=$user_id;
		        //   }
		        //   // print_r($goodsInfo);exit;
		        //   if(empty($goodsInfo)){
		        //   	//抛出一个异常
		        //   	throw new \Exception('没有此商品');
		        //   }
		        //   $order_detail=model('OrderDetail');
		        //   $res3=$order_detail->allowField(true)->saveAll($goodsInfo);
		        //   // print_r($res3);die;
		        //   if(empty($res3)){
		        //   	//抛出一个异常
		        //   	throw new \Exception('订单表详情添加失败');
		        //   }
             $user_id=$this->getUserId();
             $cartWhere=[
               ['user_id','=',$user_id],
               ['c.goods_id','in',$goods_id],
               ['is_del','=',1]
             ];
             $cart_model=model('Cart');
            $goodsInfo=$cart_model
                ->field('g.goods_id,goods_name,shop_price,goods_img,buy_number,user_id')
                ->alias('c')
                ->join('tp_goods g','c.goods_id=g.goods_id')
                ->where($cartWhere)
                ->select();
                if(empty($goodsInfo)){
                	throw new \Exception('请至少选择一个商品进行下单');
                }
                // print_r($goodsInfo);exit;
                $goodsInfo=$goodsInfo->toArray();
                foreach($goodsInfo as $k=>$v){
                	$goodsInfo[$k]['order_id']=$order_id;
                	// $goodsInfo[$k]['user_id']=$user_id;
                }
                // print_r($goodsInfo);exit;
                $res2=$detail_model->saveAll($goodsInfo);
                if(empty($res2)){
                	throw new \Exception('订单商品数据写入失败');
                }
    	//清除购物车的数据
          $cartWhere1=[
               ['user_id','=',$user_id],
               ['goods_id','in',$goods_id],
               
             ];
          $res3=$cart_model->where($cartWhere1)->update(['is_del'=>2]);
          // $res3=false;
           if(empty($res3)){
                throw new \Exception('清空购物车失败');
             }
    	//修改商品表的库存
          $goods_model=model('Goods');
          foreach($goodsInfo as $k=>$v){
          $res4=$goods_model->where("goods_id",$v['goods_id'])->update(['goods_number'=>['dec',$v['buy_number']]]);
          if(empty($res4)){
          	throw new \Exception('商品的库存修改失败');
           }
          }
        //提交
        $order_model->commit();
        // successly('下单成功');
        echo json_encode(['font'=>'下单成功','code'=>1,'order_id'=>$order_id]);
    	}catch (\Exception $e){
    	  // echo $e->getMessage();
    		fail($e->getMessage());
    	  //回滚
    		$order_model->rollback();
    		// fail($e->getMessage());
    	}
    	
    } 
    //获取订单号
    public function get0rderNo(){
    	// $arr=['10','11','12'];
    	$user_id=$this->getUserId();
    	return time().rand(100,999).$user_id;
    }
    //获取订单总金额
    public function getOrderAmount($goods_id){
    $cart_model=model('Cart');
    $user_id=$this->getUserId();
    $where=[
       ['is_del','=',1],
       ['user_id','=',$user_id],
       ['c.goods_id','in',$goods_id]
    ];
   $cartInfo= $cart_model
       ->field('buy_number,shop_price')
       ->alias('c')
       ->join('tp_goods g','c.goods_id=g.goods_id')
       ->where($where)
       ->select();
       if(!empty($cartInfo)){
       	// print_r($cartInfo);
       	// exit;
       	$count=0;
       	foreach($cartInfo as $k=>$v){
       		$count+=$v['buy_number']*$v['shop_price'];
       	}
       	// echo $count;
       	return $count;
       }else{
       	return 0;
       }
    }
    //获取商品详情信息
    // public function getOrderDetail($goods_id){
			 //    	$cart_model=model('Cart');
			 //    	$user_id=$this->getUserId();
			 //    	// print_r($user_id);die;
			 //    	$where=[
			 //          ['user_id','=',$user_id],
			 //          ['c.goods_id','in',$goods_id],
			 //          ['is_del','=',1],
			 //          ['is_on_sale','=',1]
			 //    	];
			 //    	$goodsInfo=$cart_model
			 //    	  ->field('g.goods_id,goods_name,goods_img,shop_price,buy_number,goods_number')
			 //    	  ->alias('c')
			 //    	  ->join('goods g','c.goods_id=g.goods_id')
			 //    	  ->where($where)
			 //    	  ->select()
			 //    	  ->toArray();
			 //    	  // print_r($goodsInfo);die;
			 //    	  return $goodsInfo;
    // }

    //订单成功页面
    public function successOrder(){
    	// echo 111;exit;
    	$order_id=input("get.order_id",0,'intval');
    	if(empty($order_id)){
    		$this->error('没有此订单信息',url('Index/index'));
    	}
    	//根据订单id 查询订单号 订单总金额 支付方式
         $order_model=model('Order');
         $user_id=$this->getUserId();
         $where=[
            ['user_id','=',$user_id],
            ['order_id','=',$order_id],
            ['is_del','=',1]
         ];
        $orderInfo=$order_model->where($where)->find();
        // print_R($orderInfo);exit;
       if(empty($orderInfo)){
       	 $this->error('没有此订单信息',url('Index/index'));
       }

    	$this->getLeftCateInfo();
    	$this->assign('orderInfo',$orderInfo);
    	return $this->fetch();
    }
    //异常处理
    //测试支付宝
    public function alipay(){
      //获取订单id
       $order_id= $this->request->param('order_id');
      
      //根据订单id查询订单信息
      
       $order_model = model('Order');
       $orderInfo = $order_model->where('order_id',$order_id)->find();

       // die;




      $config=config('alipay.');
      // print_r($config);exit;
      
      require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
      require_once '../extend/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
     
     //商户订单号，商户网站订单系统中唯一订单号，必填
     $out_trade_no = $orderInfo['order_no'];

    //订单名称，必填
    $subject = '沙箱测试应用';

    //付款金额，必填
    $total_amount = $orderInfo['order_amount'];
    // $total_amount = "{$orderInfo.order_amount}";

    //商品描述，可空
    $body = $orderInfo['order_talk'];

    //构造参数
    $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
    $payRequestBuilder->setBody($body);
    $payRequestBuilder->setSubject($subject);
    $payRequestBuilder->setTotalAmount($total_amount);
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    // exit;

    $aop = new \AlipayTradeService($config);

    $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

    //输出表单
    var_dump($response);
   
    }


    public function returnUrl(){
      echo '同步通知';exit;
      //接收到订单信息 订单id 订单金额


      //检测订单信息

      //检测支付金额
       
      //验签

      //支付成功

    }

      public function notifyUrl(){
      //接收到订单信息 订单id 订单金额


      //检测订单信息

      //检测支付金额
       
      //验签

      //改支付状态 改订单状态

      //支付成功

    }
   
}
