<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
class Goods extends Common
{
	//商品列表
	public function goodsList()
	{
		$cate_id=input('get.cate_id',0,'intval');
		// dump($cate_id);exit;
		if(empty($cate_id)){
			$where=[];
			session('cate_id',null);
		}else{
			//获取当前分类下所有子类cate_id
			$cate_model=model('Category');
			$info=$cate_model->where('is_show',1)->select();
			$cate_id=getCateId($info,$cate_id);
			// print_r($cate_id);exit;
			session('cate_id',$cate_id);
			$where=[
              ['cate_id','in',$cate_id]
			];
		}
        //获取品牌信息
        $goods_model=model('Goods');
        $brand_id=$goods_model->where($where)->column('brand_id');
        // print_r($brand_id);exit;
        $brand_id=array_unique($brand_id);
        // print_r($brand_id);exit;
        $brand_model=model('Brand');
        $brandWhere=[
            ['brand_id','in',$brand_id]
        ];
        $brandInfo=$brand_model->field('brand_id,brand_name')->where($brandWhere)->select();
        // dump($brandInfo);exit;

		//获取价格区间
         $shop_price=$goods_model->where($where)->order("shop_price",'desc')->limit(1)->value('shop_price');
         // echo $shop_price;exit;
         $priceInfo=$this->getPriceSection($shop_price);
         // exit;
        //获取商品数据+分页

        // exit;
        $goodsInfo=$goods_model->where($where)->limit(4)->select();
        // print_r($goodsInfo);exit;
        $obj= new \page\AjaxPage();
        $p=1;
        $page_num=4;
        $count=$goods_model->where($where)->count();
        // echo $count;exit;
        $str=$obj->ajaxpager(1,$count,4);
        // echo $str;exit;
         //获取浏览历史记录信息
        if($this->checkLogin()){
        	$historyInfo=$this->getHistoryInfoDb();
        }else{
        	$historyInfo=$this->getHistoryInfoCookie();
        }
        //  print_r($historyInfo);

        // exit;
        $this->assign('brandInfo',$brandInfo);
        $this->assign('priceInfo',$priceInfo);
        $this->assign('goodsInfo',$goodsInfo);
        $this->assign('historyInfo',$historyInfo);
        $this->assign('str',$str); //实体
		$this->getLeftCateInfo();
		return view();
	}
	//从数据库中获取浏览历史记录
	public function getHistoryInfoDb(){
		$history_model=model('History');
		$user_id=$this->getUserId();
		$goods_id=$history_model->where('user_id',$user_id)->order("look_time",'desc')->column('goods_id');
		$goods_id=array_slice(array_unique($goods_id),0,4);
		// print_r($goods_id);exit;
		$goodsInfo=$this->getHistoryInfo($goods_id);
		// return $goodsInfo;
		if(!empty($goodsInfo)){
			return $goodsInfo;
		}else{
			return false;
		}
	}
	//从cookie中获取浏览历史记录
	public function getHistoryInfoCookie(){
		$str=cookie('historyInfo');
		// echo $str;
		// return false;
		if(!empty($str)){
			//解密
			// echo 111;exit;
			$info=getBase64Info($str);
			// print_r($info);exit;
			$info=array_reverse($info);
			// print_r($info);
			$goods_id=[];
			foreach($info as $k=>$v){
				$goods_id[]=$v['goods_id'];
			}
			// print_r($goods_id);exit;
			$goods_id=array_slice(array_unique($goods_id),0,4);
			// print_r($goods_id);exit;
			$goodsInfo=$this->getHistoryInfo($goods_id);
			if(!empty($goodsInfo)){
                return $goodsInfo;
			}else{
				return false;
			}
		}
	}
	//获取浏览历史的商品数据
     public function getHistoryInfo($goods_id){
       //查询数据必须按照指定的商品id顺序查询
     	$g_id=implode(',',$goods_id);
     	$where=[
            ['goods_id','in',$g_id]
     	];
     	$exp=new \think\db\Expression("field(goods_id,$g_id)");
     	$goodsInfo=model('Goods')->where($where)->order($exp)->select();
     	// echo model('Goods')->getLastSql();exit;
     	// print_r($goodsInfo);exit;
     	return $goodsInfo;
     }

	//商品详情
	public function goodsProduct()
	{ 
	    //接收商品id
        $goods_id=input('get.goods_id',0,'intval');
        // dump($goods_id);exit;
         if(empty($goods_id)){
         	$this->error('请选择一件商品');exit;
         }
        //根据id查询一条数据
        $goods_model=model('Goods');
        $goodsInfo=$goods_model->where("goods_id",$goods_id)->find();
        //存入浏览记录
         if($this->checkLogin()){
         	// echo 1;exit;
         	//把浏览记录信息存入数据库
         	$this->saveHistoryDb($goods_id);

         }else{
         	// echo 2;exit;
           //把浏览记录信息存入cookie中
         	$this->saveHistoryCookie($goods_id);
         }
          // exit;

		$this->getLeftCateInfo();
		$this->assign('goodsInfo',$goodsInfo);
		return view();
	}

	//获取价格区间
	public function getPriceSection($max_price){
		$price=$max_price/7; //1000
		$priceInfo=[];
		// echo  $price;exit;
		//0-1000 1000-2000 2000-3000 
        for($i=0;$i<=6;$i++){
        	// echo $i;
        	// echo $i*$price;
        	$start=$i*$price;
        	$end=$price*($i+1)-0.01;
        	// echo "<br>";
        	// echo $end.'<br>';
        	// echo $start.'-'.$end;
        	$priceInfo[]=number_format($start,2).'-'.number_format($end,2);
            // echo '<br>';

        }
        $priceInfo[]=($end+0.01).'及以上';
        // print_r($priceInfo);exit;
        return $priceInfo;
	}
	//重新获取价格
	public function getPriceInfo()
	{
		$brand_id=input('post.brand_id',0,'intval');
		 $where=[
              ['brand_id','=',$brand_id],
              
            ];
		if(session("?cate_id")){
            // echo '有分类';
            $cate_id=session('cate_id');
            $where[]=['cate_id','in',$cate_id];
            
		}
		// print_r($where);exit;
		$goods_model=model('Goods');
		$shop_price=$goods_model->where($where)->order("shop_price","desc")->limit(1)->value('shop_price');
		// echo $shop_price;exit;
		$priceInfo=$this->getPriceSection($shop_price);
		// print_r($priceInfo);
		echo json_encode($priceInfo);
	}
	//获取商品的数据
	public function getGoodsInfo()
	{
		//获取条件
		$brand_id=input('post.brand_id',0,'intval');
		// $brand_id=$this->request->post('brand_id/d');
		$price=input('post.price','');
		$order_field=input('post.order_field','');
		$order_type=input('post.order_type','');
		$field=input('post.field','');
		$p=input('post.p',1);
		// echo $price;exit;
		$where=[];
		//处理条件
		if(session('?cate_id')){
			$cate_id=session('cate_id');
			$where[]=['cate_id','in',$cate_id];
		}
		if(!empty($brand_id)){
			$where[]=['brand_id','=',$brand_id];
		}
		if(!empty($price)){
			//检测-是否存在
			// $pos=strpos($price,'-');
			// dump($pos);exit;
			if(strpos($price,'-')){
			$price=explode('-',$price);
			// print_r($price);exit;
			$one=str_replace(',','',$price[0]);
			$two=str_replace(',','',$price[1]);
			// echo $one;
			// echo $two;
			$where[]=['shop_price','>=',$one];
			$where[]=['shop_price','<=',$two];
			}else{
			$price=(int)$price;
			// echo $price;exit;
			$where[]=['shop_price','>=',$price];
			}
		}
		if(!empty($field)){
			$where[]=[$field,'=',1];
		}
		//查询数据
		$goods_model=model('Goods');
         if(empty($order_field)){
         	$goodsInfo=$goods_model->where($where)->page($p,4)->select();
         }else{
         	$goodsInfo=$goods_model->where($where)->order($order_field,$order_type)->page($p,4)->select();
         }
         // echo $goods_model->getLastSql();exit;
         //获取页码
        $obj= new \page\AjaxPage();
        $page_num=4;
        $count=$goods_model->where($where)->count();
        $str=$obj->ajaxpager(1,$count,$page_num);
        $this->view->engine->layout(false);
        $this->assign('goodsInfo',$goodsInfo);
        $this->assign('str',$str);
        echo $this->fetch('goods');
	}
	//把浏览记录信息存入数据库
	public function saveHistoryDb($goods_id){
      $info=[
        'goods_id'=>$goods_id,
        'look_time'=>time(),
        'user_id'=>$this->getUserId()
      ];
      // print_r($info);exit;
      $history_model=model('History');
      $history_model->save($info);
	}
    //把浏览记录信息存入cookie
	public function saveHistoryCookie($goods_id){
		//判断cookie中是否有值
		$history_str=cookie('historyInfo');
		if(!empty($history_str)){
			$info=getBase64Info($history_str);
			if(strlen($history_str)>4000){
                 array_shift($info);
			}
           
           // print_r($info);exit;
           $info[]=[
              'goods_id'=>$goods_id,
              'look_time'=>time()
           ];
           // print_r($info);exit;
          //  $str=base64_encode(serialize($info));
          // cookie('historyInfo',$str);
		}else{
			 $info=[
           [
              'goods_id'=>$goods_id,
              'look_time'=>time()
           ]
        ];         
	     }
	      $str=createBase64Info($info);
           // echo $str;exit;
          cookie('historyInfo',$str);

		}
       

	public function test(){
		$str=cookie('historyInfo');
		$info= getBase64Info($str);
		// echo $str;
		print_r($info);
	}
}