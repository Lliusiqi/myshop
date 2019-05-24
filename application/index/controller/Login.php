<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Login extends Common
{
    //显示登录的视图
    public function login()
    {
        if($this->request->isPost()&&$this->request->isAjax()){
          // echo '登录';
          //接收账号密码
          $account=input('post.account','');
          $u_pwd=input('post.u_pwd','');
          $remember_me=input('post.remember_me','');
          // dump($account);
          // dump($u_pwd);
          // dump($remember_me);
          //验证
         if(empty($account)){
           fail('账号必填');
         }
         if(empty($u_pwd)){
          fail('密码必填');
         }
         //根据账号查询
         $where=[
           ['u_email','=',$account]
         ];
        $user_model=model('User');
        $userInfo=$user_model->where($where)->find();
        // print_r($userInfo);
        if(!empty($userInfo)){
            $time=time();
            $last_error_time=$userInfo['last_error_time'];
            $error_num=$userInfo['error_num'];
            $wh=[
               ['u_id','=',$userInfo['u_id']]
            ];
           if(md5($u_pwd)==$userInfo['u_pwd']){
            // echo '密码正确';
            //密码正确
            if($error_num>=3&&($time-$last_error_time)<3600){
             $mins=60-ceil(($time-$last_error_time)/60);
              fail('账号已锁定,'.$mins.'分钟后请重新登录');
            }

            //清零
              $updateInfo=['error_num'=>0,'last_error_time'=>null];
              $user_model->where($wh)->update($updateInfo);
            //判断是否记住账号密码
            if($remember_me=='true'){
              $info=['account'=>$account,'u_pwd'=>$u_pwd];
              $t=60*60*24*10;
              cookie('rememberInfo',$info,$t);
            }
            //把id 账号存入session中
               $data=['u_id'=>$userInfo['u_id'],'account'=>$account];
               session('userInfo',$data);
             //同步浏览历史
               $this->asyncHistory();
             // exit;
             //同步购物车数据
               $this->asyncCart();
               // exit;

             //提示登录成功
             successly('登录成功');exit;

           }else{
            // echo '密码有误';
            //密码有误
            if(($time-$last_error_time)>3600){
              //清零
               $updateInfo=['error_num'=>1,'last_error_time'=>$time];
               $res=$user_model->where($wh)->update($updateInfo);
               if($res){
               fail('账号或密码有误,您还有2次机会');
               }
               
            }else{
               if($error_num>=3){
               fail('账号已锁定，请1小时后登录');
            }else{
              $updateInfo=['error_num'=>$error_num+1,'last_error_time'=>$time];
              $res=$user_model->where($wh)->update($updateInfo);
              // dump($res);
              if($res){
                 fail('账号或密码有误,您还有'.(3-($error_num+1)).'次机会');
              }
            }
            }
           }
        }else{
          fail( '账号或密码有误');
        }
        }else{

          // 临时关闭当前模板的布局功能
        $this->view->engine->layout(false);
        return view();
        }
        
    }
    //显示注册的视图
   public function register()
   {
    // 临时关闭当前模板的布局功能
    $this->view->engine->layout(false);
     return $this->fetch();
   }
   //发邮件
   public function sendEmail(){
      $email=input('post.email',''); 
      //验证validate 验证场景
      $reg='/^\w+@\w+\.com$/';
      if(empty($email)){
        fail('邮箱必填');
      }else if(!preg_match($reg,$email)){
        fail('请输入正确邮箱格式');
      }else{
       $count= model('User')->where('u_email',$email)->count();
       if($count>0){
        fail('邮箱已存在');
       } 

      }
      // echo 'ok';exit;

      //发送邮件
      $code=createCode();
      // echo $code;
     $body='您的验证码为:'.$code.',请保密打死不能说，五分钟内有效';
     $res=sendEmail($email,'注册成功',$body);
     // dump($res);
     if($res){
        //把验证码 邮箱 发送时间存入session
        session('emailInfo',['email'=>$email,'code'=>$code,'send_time'=>time()]);
        successly('发送成功');
     }else{
        fail('发送失败');

     }
      }
     

    //执行注册
     public function registerDo()
     {
      $data=input('post.');
      // print_r($data);
      //validate 验证
      $validate=validate('User');
      $res=$validate->check($data);//true false
      if(!$res){
        // echo $validate->getError();exit;
        fail($validate->getError());
      }
      // echo 'ok';
      //入库
      $user_model=model('User');
      $result=$user_model->allowField(true)->save($data);
      // dump($result);
      if($result){
        successly('注册成功');
      }else{
        fail('注册失败');
      }
     }
    //退出
     public function quit()
     {
        //清除session
      session('userInfo',null);
      cookie('rememberInfo',null);
      $this->redirect(url('Login/login'));
     }


    public function test()
     {
      // $emailInfo=session('emailInfo');
      $userInfo=session('userInfo');
      $rememberInfo=cookie('rememberInfo');
      dump($userInfo);
      dump($rememberInfo);exit;

     }

}
