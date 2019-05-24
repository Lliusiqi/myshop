<?php
namespace app\index\controller;
use think\Controller;

class Address extends Common
{
	public function __construct()
	{
		parent::__construct();
		if(!$this->checkLogin()){
           $this->error("请先登录",url('Login/login'));
		}
	}
	//显示添加收货地址的视图
	public function addressList()
	{
		//查询当前用户的收货地址信息
        $addressInfo=$this->getAddressInfo();
        // dump($addressInfo);exit;
        // print_r($addressInfo);exit;


		//查询所有的省份信息
        $provinceInfo=$this->getAreaInfo(0);
        // print_r($provinceInfo);exit;
        $this->assign('provinceInfo',$provinceInfo);
        $this->assign('addressInfo',$addressInfo);
		return view();
	}
	//获取区域信息
	public function getAreaInfo($pid)
	{
      $where=[
       ['pid','=',$pid]
      ];
      $area_model=model('Area');
      $areaInfo=$area_model->where($where)->select();
      return $areaInfo;
	} 
    //获取区域
    public function getArea(){
    	$id=input('post.id',0,'intval');
    	// echo $id;
    	$info=$this->getAreaInfo($id);
    	// print_r($info);
    	 echo json_encode($info);
    	// $option="";
    	// foreach($info as $k=>$v){
        // $option.="";
    	// }
    }

	public function addressDo()
	{
      $data=input('post.');
      $user_id=$this->getUserId();
      $data['user_id']=$user_id;
      //控制器进行validate验证

      //入库
      $address_model=model('Address');
      if($data['is_default']==1){
      	$where=[
           ['user_id','=',$user_id],
           ['is_del','=',1]
      	];
      	$address_model->where($where)->update(['is_default'=>2]);
      }
      $res=$address_model->save($data);
      // dump($res);
      if($res){
      	successly('添加成功');
      }else{
      	fail('添加失败');
      }
	}

	public function addressDel()
	{
       $address_id=input('post.address_id','','intval');
       //echo $address_id;exit;
       if(empty($address_id)){
       	$this->error('请至少选择一个收货地址进行删除',url('Address/addressList'));exit;
       }
       $address_model=model('Address');
       $where=[
          ['address_id','in',$address_id]
       ];
       $info=[
         'is_del'=>2
       ];
       $res=$address_model->where($where)->update($info);
       if($res){
       	successly('删除成功');
       }else{
       	fail('删除失败');
       }

	}
	//设为默认
	public function setAddressDefault()
	{
		$address_id=input('get.address_id','','intval');
		//echo $address_id;exit;
		if(empty($address_id)){
			$this->error('请至少选择一个收货地址',url('Address/addressList'));exit;
		}
		$address_model=model('Address');
		//开启事务
		$address_model->startTrans();
		//把所有收货地址状态改为2
		$user_id=$this->getUserId();
		$where=[
           ['user_id','=',$user_id],
           ['is_del','=',1]
		];
		$res1=$address_model->where($where)->update(['is_default'=>2]);
		//把当前收货地址状态改为1
		$res2=$address_model->where('address_id',$address_id)->update(['is_default'=>1]);
		if($res1!==false&&$res2){
			//提交
			$address_model->commit();
			$this->redirect('Address/addressList');
		}else{
			//回滚
			$address_model->rollback();
			$this->error('设置失败');

		}

	}
    //修改
	public function addressEdit()
	{
      $address_id=input('get.address_id',0,'intval');
      if(empty($address_id)){
      	$this->error("请选择一个要修改的收货地址",url('Address/addressList'));
      }
      //根据当前的id 查询要修改的一条数据
      $where=[
        ['address_id','=',$address_id],
        ['is_del','=',1]
      ];
      $address_model=model('Address');
      $addressInfo=$address_model->where($where)->find();
      // print_r($addressInfo);exit;
      //查询所有的省份 作为第一个下拉菜单
      $provinceInfo=$this->getAreaInfo(0);
      //获取选择省份下 市的信息
      $cityInfo=$this->getAreaInfo($addressInfo['province']);
       //获取选择市下 区/县的信息
      $areaInfo=$this->getAreaInfo($addressInfo['city']);
      $this->assign('provinceInfo',$provinceInfo);
      $this->assign('addressInfo',$addressInfo);
      $this->assign('cityInfo',$cityInfo);
      $this->assign('areaInfo',$areaInfo);
      return $this->fetch();
	}
    //执行修改
    public function addressEditDo()
    {
    	$data=input('post.');
    	if($data['is_default']==1){
    		$user_id=$this->getUserId();
    		$address_model=model('Address');
    		$where=[
                 ['user_id','=',$user_id],
                 ['is_del','=',1]
      	    ];
      	$address_model->where($where)->update(['is_default'=>2]);
    	}
    	$addressWhere=[
           ['address_id','=',$data['address_id']],
           ['is_del','=',1],
           ['user_id','=',$user_id]
    	];
    	$res=$address_model->where($addressWhere)->update($data);
    	// dump($res);

    }

    //设为默认
    // public function setDefault(){
    // 	$address_id=input("post.address_id",0,'intval');
    // 	if(empty($address_id)){
    // 		fail('请至少选择一个收货地址');
    // 	}
    // 	$address_model=model('Address');
    // 	$user_id=$this->getUserId();
    // 	$where=[
    //        ['user_id','=',$user_id],
    //        ['is_del','=',1]
    // 	];
    // 	//tp5.1中 开启事务
    // 	$address_model->startTrans();
    // 	$res1=$address_model->where($where)->update(['is_default'=>2]);//语法没有错误 受影响的行数0 1 false
    // 	$res2=$address_model->where('address_id',$address_id)->update(['is_default'=>1]);
    // 	// $res2=false;
    //    //判断 执行成功 提交
    //     if($res1!==false&&$res2){
    //     	$address_model->commit();
    //     	successly('设置成功');
    //     }else{
    //     	$address_model->rollback();
    //     	fail('设置失败');
    //     }
    // }

}