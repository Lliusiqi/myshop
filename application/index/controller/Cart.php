<?php

namespace app\index\controller;

use think\Controller;

class Cart extends Common
{
    //加入购物车
    public function addCart(){
    	$goods_id=input("post.goods_id",0,'intval');
    	$buy_number=input("post.buy_number",0,'intval');
    	//验证
    	if(empty($goods_id)){
    		fail('请选择一件商品');
    	}
    	$goodsWhere=[
          ['goods_id','=',$goods_id],
          ['is_on_sale','=',1]
    	];
    	$goods_model=model('Goods');
    	$goodsInfo=$goods_model->where($goodsWhere)->find();
    	if(empty($goodsInfo)){
    		fail('请选择一件商品');
    	}

    	// echo 'ok';exit;
    	//加入购物车
    	if($this->checkLogin()){
    		$this->saveCartInfoDb($goods_id,$buy_number,$goodsInfo['shop_price']);
    	}else{
    		$this->saveCartInfoCookie($goods_id,$buy_number);
    	}

    	// exit;
    }
    //加入购物车 进数据库
    public function saveCartInfoDb($goods_id,$buy_number,$add_price){
      //根据商品id 用户id 在购物车表中进行查询
    	$cart_model=model('Cart');
    	$user_id=$this->getUserId();
    	$cartWhere=[
          ['goods_id','=',$goods_id],
          ['user_id','=',$user_id],
          ['is_del','=',1]
    	];
    	$cartInfo=$cart_model->where($cartWhere)->find();
    	// print_r($cartInfo);
    	if(!empty($cartInfo)){
    		// 检测库存 累加 修改
    		$res=$this->checkGoodsNumber($buy_number,$goods_id,$cartInfo['buy_number']);
    		if(!$res){
    			// echo '库存不足 ';exit;
    			fail('库存不足');
    		}
    		$where=[
                 ['goods_id','=',$goods_id]
    		];
    		$result=$cart_model->where($where)->update(['buy_number'=>$buy_number+$cartInfo['buy_number']]);
    		// dump($result);exit;
    	}else{
    		//检测库存 添加
    		$res=$this->checkGoodsNumber($buy_number,$goods_id);
    		if(!$res){
    			// echo '库存不足 ';exit;
    			fail('库存不足');
    		}
    		$info=[
              'goods_id'=>$goods_id,
              'buy_number'=>$buy_number,
              'add_price'=>$add_price,
              'user_id'=>$user_id
    		];
    		$result=$cart_model->save($info);
    		// dump($result);exit;
    	}
    	if($result){
    		// return true;
    		successly('加入购物车成功');
    	}else{
    		// return false;
    		fail('加入购物车失败');
    	}
    }
    //加入购物车 进cookie
    public function saveCartInfoCookie($goods_id,$buy_number){
    	$str=cookie('cartInfo');
    	$flag=0;
    	// dump($str);exit;
    	//判断cookie是否有值
    	if(!empty($str)){
          // echo 111;die;
    		$cartInfo=getBase64Info($str);
    		//当前购买商品 3 是否在cookie中出现过
           foreach($cartInfo as $k=>$v){
           	 if($goods_id==$v['goods_id']){
           	 	//检测库存
           	 	$res=$this->checkGoodsNumber($buy_number,$goods_id,$v['buy_number']);
           	 	if(!$res){
           	 		fail('库存不足');
           	 	}
           	 	//累加
           	 	// echo '累加';
           	 	$cartInfo[$k]['buy_number']=$buy_number+$v['buy_number'];
           	 	// print_r($cartInfo);exit;
           	 	//把累加或添加后的购物车数据存储到cookie中
                $str=createBase64Info($cartInfo);
    		        cookie('cartInfo',$str);
    		        successly('加入购物车成功');
    		      exit;
           	 }else{
           	 	$flag=1;
           	 }
           }
           //添加
           if($flag==1){
           	    $res=$this->checkGoodsNumber($buy_number,$goods_id);
           	 	if(!$res){
           	 		fail('库存不足');
           	 	}
           	// echo '添加';exit;
           	$cartInfo[]=[
               'goods_id'=>$goods_id,
               'buy_number'=>$buy_number,
               'create_time'=>time()
           	];
           	// print_r($cartInfo);exit;
           	//把累加或添加后的购物车数据存储到cookie中
               $str=createBase64Info($cartInfo);
    		   cookie('cartInfo',$str);
    		   successly('加入购物车成功');
           }
            // exit;
    	}else{
    		// echo 222;die;
    		//检测库存
    		$res=$this->checkGoodsNumber($buy_number,$goods_id);
    		if(!$res){
    			fail('库存不足');
    		}
    		//把数据存储到cookie中
    		$cartInfo=[
                ['goods_id'=>$goods_id,'buy_number'=>$buy_number,'create_time'=>time()]
                // ['goods_id'=>3,'buy_number'=>5,'create_time'=>time()],
    		];
    		// print_r($cartInfo);exit;
    		$str=createBase64Info($cartInfo);
    		cookie('cartInfo',$str);
    		successly('加入购物车成功');
    	}
    	
    }

    //购物车列表展示
    public function cartList(){
    	// echo '购物车列表';exit;
    	if($this->checkLogin()){
    		$cartInfo=$this->getCartInfoDb();
    	}else{
    		$cartInfo=$this->getCartInfoCookie();
    	}
    	// exit;
      // print_r($cartInfo);exit;
      // print_r($cartInfo);exit;
      //获取总价
      if(!empty($cartInfo)){
        $count=0;
        foreach($cartInfo as $k=>$v){
         $count+=$v['shop_price']*$v['buy_number'];
        }
        // echo $count;
        $this->assign('count',$count);
      }
    	$this->assign('cartInfo',$cartInfo);
    	$this->getLeftCateInfo();
    	return view();
    }
    //从数据库中获取购物车的数据
    public function getCartInfoDb(){
      $user_id=$this->getUserId();
      $where=[
        ['user_id','=',$user_id],
        ['is_del','=',1]
      ];
      $cart_model=model('Cart');
      $cartInfo=$cart_model
      ->field("c.goods_id,goods_name,buy_number,shop_price,goods_img,goods_number,add_price")
      ->alias('c')
      ->join("tp_goods g","c.goods_id=g.goods_id")
      ->where($where)
      ->order("create_time",'desc')
      ->select();
      // print_r($cartInfo);exit;
      if(!empty($cartInfo)){
      	return $cartInfo;
      }else{
      	return false;
      }
    }
    //从cookie中获取购物车的数据
    public function getCartInfoCookie(){
    	$str=cookie('cartInfo');
    	if(!empty($str)){
            //解密
            // echo 111;exit;
            $cartInfo=getBase64Info($str);
            // print_r($cartInfo);exit;
            $cartInfo=array_reverse($cartInfo);
            // print_r($cartInfo);exit;
            $goods_id=[];
            foreach($cartInfo as $k=>$v){
            	$goods_id[]=$v['goods_id'];
            }

            if(empty($goods_id)){
            	return false;
            }else{
              // print_r($goods_id);exit;
              $g_id=implode(',',$goods_id);
              $goods_model=model('Goods');
              $exp=new \think\db\Expression("field(goods_id,$g_id)");
              $where=[
                ['is_on_sale','=',1],
                ['goods_id','in',$g_id]
              ];
              // print_r($cartInfo);
              $goodsInfo=$goods_model->where($where)->order($exp)->select();
              // print_R($cartInfo);
              // print_r($goodsInfo);exit;
              foreach($goodsInfo as $k=>$v){
           	    foreach($cartInfo as $key=>$val){
           		  if($v['goods_id']==$val['goods_id']){
           			$goodsInfo[$k]['buy_number']=$val['buy_number'];
           		 }
           	   }
             }
             return $goodsInfo;
            }
           
           // print_r($goodsInfo);exit;
    	}else{
    		return false;
    	}
    }
    
    //改变购买数量
    public function changeNumber(){
      $goods_id=input('post.goods_id',0,'intval');
      $buy_number=input('post.buy_number',0,'intval');
      if($buy_number<1){
        fail('最少购买一件');
      }
      // echo $goods_id;
      // echo $buy_number;exit;
      if($this->checkLogin()){
        $this->changeNumberDb($goods_id,$buy_number);
      }else{
       //修改cookie数据中的购买数量
        $this->changeNumberCookie($goods_id,$buy_number);
      }

    }

    //改变数据库的购买数量
    public function changeNumberDb($goods_id,$buy_number){
        // $this->changeNumberDb();
        $user_id=$this->getUserId();
        $res=$this->checkGoodsNumber($buy_number,$goods_id);
        if(!$res){
          fail('库存不足');
        }

        $where=[
          ['user_id','=',$user_id],
          ['goods_id','=',$goods_id],
          ['is_del','=',1]
        ];
        $result=model('Cart')->where($where)->update(['buy_number'=>$buy_number]);
       // print_r($result);exit;
        if($result){
          successly('');
        }else{
          fail('修改失败');
        }
        // if($res){
        //   $where=[
        //     'goods_id' =>$goods_id,
        //     'user_id'=>$this->getUserId()
        //   ];
        //   $updateInfo=[
        //     'buy_number'=>$buy_number,
        //     'update_time'=>time()
        //   ];
        //   $cart_model=model('Cart');
        //   $result=$cart_model->save($updateInfo,$where);
        //   if($result){
        //     successly('修改数量成功');
        //   }else{
        //     fail('修改数量失败');
        //   }
        // }
    }
      //改变cookie的购买数量
     public function changeNumberCookie($goods_id,$buy_number){
        $str=cookie('cartInfo');
        if(!empty($str)){
          $info=getBase64Info($str);
          // print_r($info);exit;
          foreach($info as $k=>$v){
            if($v['goods_id']==$goods_id){
             //检测库存
               $res=$this->checkGoodsNumber($buy_number,$goods_id);
                if(!$res){
                  fail('库存不足');
                }
              $info[$k]['buy_number']=$buy_number;
              $s=createBase64Info($info);
              cookie("cartInfo",$s);
              successly('');
            }
          }
        }
     }

    //获取小计
    public function getTotal(){
      $goods_id=input('post.goods_id',0,'intval');
      //获取商品价格
      $goods_model=model('Goods');
      $goodsWhere=[
        ['goods_id','=',$goods_id],
        ['is_on_sale','=',1]
      ];
      $shop_price=$goods_model->where($goodsWhere)->value('shop_price');
      // echo $shop_price;

      //获取购买数量
      if($this->checkLogin()){
         //从数据库中获取
        $cart_model=model('Cart');
        $user_id=$this->getUserId();
        $cartWhere=[
          ['user_id','=',$user_id],
          ['goods_id','=',$goods_id],
          ['is_del','=',1]
        ];
       $buy_number=$cart_model->where($cartWhere)->value('buy_number');
       // echo $buy_number;exit;
      }else{
        //cookie中获取
        $str=cookie('cartInfo');
        if(!empty($str)){
        $info=getBase64Info($str);
        foreach($info as $k=>$v){
          if($goods_id==$v['goods_id']){
            $buy_number=$v['buy_number'];
          }
        }
       }
      }
     echo  $shop_price*$buy_number;
    }

    //获取总价
    public function getCount(){
     $goods_id=input('post.goods_id','');
     if(!empty($goods_id)){
       if($this->checkLogin()){
      //从数据库中获取  购买数量 商品价格
      $user_id=$this->getUserId();
      $cart_model=model('Cart');
      $cartWhere=[
         ['user_id','=',$user_id],
         ['c.goods_id','in',$goods_id],
         ['is_del','=',1]
      ];
      $info=$cart_model
      ->field('shop_price,buy_number')
      ->alias('c')
      ->join('tp_goods g','c.goods_id=g.goods_id')
      ->where($cartWhere)
      ->select();
      // print_r($info);
     }else{
       //从cookie中获取购买数量 从数据库中获取商品价格
      $goods_model=model('Goods');
      $where=[
         ['goods_id','in',$goods_id],
         ['is_on_sale','=',1]
      ];
      $info=$goods_model
          ->field('goods_id,shop_price')
          ->where($where)
          ->select();
          $str=cookie('cartInfo');
          $cartInfo=getBase64Info($str);
          // print_r($info);
          // print_r($cartInfo);
          foreach($info as $k=>$v){
            //$info[$k]['buy_number']=
            foreach($cartInfo as $key=>$val){
              if($v['goods_id']==$val['goods_id']){
                $info[$k]['buy_number']=$val['buy_number'];
              }
            }
          }
          // print_r($info);exit;
     }
     $count=0;
     //总价
     foreach($info as $k=>$v){
      $count+=$v['shop_price']*$v['buy_number'];
     }
     echo $count;
     }else{
      echo 0;
     }
    
    }

    //删除
    public function cartDel(){
      $goods_id=input('post.goods_id'); 
      if(empty($goods_id)){
        fail('请至少选择一个商品进行删除');
      } 
      if($this->checkLogin()){
       $this->cartDelDb($goods_id);
      }else{
        $this->cartDelCookie($goods_id);
      }
    }

  //登录后---从数据库中删除
    public function cartDelDb($goods_id){
      $user_id=$this->getUserId();
        $where=[
          ['user_id','=',$user_id],
          ['goods_id','in',$goods_id]
        ];
        $res=model('Cart')->where($where)->update(['is_del'=>2]);
        if($res){
          successly('删除成功');
        }else{
          fail('删除失败');
        }
    }
    //未登录--从cookie中删除商品的数据
    public function cartDelCookie($goods_id){
     // echo $goods_id;exit;
      $str=cookie('cartInfo');
      if(!empty($str)){
        //解密
        $cartInfo=getBase64Info($str);
        // print_R($cartInfo);exit;
         if(strpos($goods_id,',')){
        //批删
          $del_id=explode(',',$goods_id);
          // print_r($del_id);exit;

        }else{
        //单删
          $del_id=[$goods_id];

        }
        // print_r($goods_id);
        foreach($cartInfo as $k=>$v){
          if(in_array($v['goods_id'],$del_id)){
            unset($cartInfo[$k]);
          }
        }
        if(empty($cartInfo)){
           cookie('cartInfo',null);

        }else{
          $cartStr=createBase64Info($cartInfo);
          cookie('cartInfo',$cartStr);
        }
        // print_r($cartInfo);exit;
        // $cartStr=getBase64Info($cartInfo);
        
        successly('删除成功');
      }
     
    }
    //确认结算
    public function conform(){
      if(!$this->checkLogin()){
        $this->error('请先登录',url('Login/login'));
      }
      $goods_id=input('get.goods_id');
      if(empty($goods_id)){
        $this->error('请至少选择一件商品进行结算',url('Cart/cartList'));
      }
      $user_id=$this->getUserId();
      $where=[
        ['user_id','=',$user_id],
        ['c.goods_id','in',$goods_id],
        ['is_on_sale','=',1]
      ];
      //获取商品数据
      $goods_model=model('Goods');
      $goodsInfo=$goods_model
            ->alias('g')
            ->join('cart c',"g.goods_id=c.goods_id")
            ->where($where)
            ->select();
            // print_r($goodsInfo);exit;
            //获取商品总价
            $count=0;
            foreach($goodsInfo as $k=>$v){
              $count+=$v['shop_price']*$v['buy_number'];

            }
            //查询当前用户的收货地址
            $addressInfo=$this->getAddressInfo();

         $this->assign('goodsInfo',$goodsInfo);
         $this->assign('count',$count);
         $this->assign('addressInfo',$addressInfo);
      $this->getLeftCateInfo();
      return $this->fetch();
    }

    public function test(){
    	$str=cookie('cartInfo');
    	$cartInfo=getBase64Info($str);
    	print_r($cartInfo);exit;
    }
}
