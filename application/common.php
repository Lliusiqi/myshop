<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/*
    随机盐值
*/
function setSale()
{
    $long = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRETUVWXYZ~!@#$%^&*_+';
    // substr($long,26,5);
    $sale = substr(str_shuffle($long),0,5);
    return $sale;
}
// 无限极分类
function createTree($data,$field='cate_id',$parent_id = 0,$level = 1)
{
    static $result = [];
    if ($data) {
        foreach ($data as $key => $val) {
            if ($val['parent_id'] == $parent_id) {
                $val['level'] = $level;
                $result[] = $val;
                createTree($data,$field='cate_id',$val[$field],$level+1);
            }
        }
        return $result;
    }

}
// 无限极分类
function createTrees($data,$parent_id = 0,$level = 1)
{
    static $result = [];
    if ($data) {
        foreach ($data as $key => $val) {
            if ($val['parent_id'] == $parent_id) {
                $val['level'] = $level;
                $result[] = $val;
                createTrees($data,$val['menu_id'],$level+1); 
            }
        }
        return $result;
    }

}
// //获取左侧分类数据
// function getLeftCateInfo($cateLeft,$parent_id=0){
//     $info=[];//给一个$info一个空数组
//     foreach($cateLeft as $k=>$v){//循环查询出来的数据
//         if($v['parent_id']==$parent_id){//判断如果$v里面的id等于参数上的id的话
//             $v['son']=getLeftCateInfo($cateLeft,$v['cate_id']);//就在调用一次这个函数，在循环查询出的数组里的分类表里的id最后附给数组里的子分类
//             $info[]=$v;//把最后分配的变量$v填到$info这个空数组里
//         }
//     }

//     return $info;   //最后把数组返回去
// }
function getLeftCateInfo($cateLeft,$parent_id=0){
    $info=[];
    foreach($cateLeft as $k=>$v){
        if($v['parent_id']==$parent_id){
            $v['son']=getLeftCateInfo($cateLeft,$v['cate_id']);
            $info[]=$v;
        }
    }
    return $info;
}
//上传文件
function upload($img)
{
    // 获取表单上传文件 例如上传了001.jpg
    $file = request()->file($img);
    // 移动到框架应用根目录/uploads/ 目录下
    $info = $file->move('./uploads');
    if($info){
        // 成功上传后 获取上传信息
        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
        return json_encode(['code'=>0,'msg'=>$info->getSaveName()]);
    }else{
        // 上传失败获取错误信息 
        return json_encode(['code'=>1,'msg'=>$file->getError()]);
    }
}


    //
   function getCateId($cateInfo,$parent_id)
   {
      static $id=[];
      foreach($cateInfo as $k=>$v){
        if($v['parent_id']==$parent_id){
          $id[]=$v['cate_id'];
          //print_r($id);die;
         getCateId($cateInfo,$v['cate_id']); 
        }
      }
      return $id;
    }

//json_encode的方法
function successly($font){
    echo json_encode(['code'=>1,'font'=>$font]);
}

function fail($font){
    echo json_encode(['code'=>2,'font'=>$font]);exit;
}
function createCode(){
    return rand(100000,999999);
}
//发送邮件
function sendEmail($address,$subject,$body){
    //实例化PHPMailer核心类
            
            $mail= new \email\PHPMailer();

            //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
            $mail->SMTPDebug =0;

            //使用smtp鉴权方式发送邮件
            $mail->isSMTP();

            //smtp需要鉴权 这个必须是true
            $mail->SMTPAuth=true;

            //链接qq域名邮箱的服务器地址
            $mail->Host = 'smtp.163.com';//163邮箱：smtp.163.com

            //设置使用ssl加密方式登录鉴权
            $mail->SMTPSecure = 'ssl';//163邮箱就注释

            //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
            $mail->Port = 465;//163邮箱：25

            //设置smtp的helo消息头 这个可有可无 内容任意
            // $mail->Helo = 'Hello smtp.qq.com Server';

            //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
            //$mail->Hostname = 'http://localhost/';

            //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
            $mail->CharSet = 'UTF-8';

            //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
            $mail->FromName = 'root';

            //smtp登录的账号 这里填入字符串格式的qq号即可
            $mail->Username ='l718liusiqi@163.com';

            //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
            $mail->Password = 'admin123';//163邮箱也有授权码 进入163邮箱帐号获取

            //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
            $mail->From = 'l718liusiqi@163.com';

            //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
            $mail->isHTML(true);

            //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
            $mail->addAddress($address);

            //添加多个收件人 则多次调用方法即可
            // $mail->addAddress('xxx@163.com','爱代码，爱生活世界');

            //添加该邮件的主题
            $mail->Subject = $subject;

            //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
            $mail->Body = $body;

            //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
            // $mail->addAttachment('./d.jpg','mm.jpg');
            //同样该方法可以多次调用 上传多个附件
            // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

            $status = $mail->send();

            //简单的判断与提示信息
            if($status) {
               // echo 'ok';
                return true;
            }else{
               // echo 'no';
                return false;
            }
}
//字符串base64加密
function createBase64Info($info){
  return  base64_encode(serialize($info));
}
//字符串Base64解密
function getBase64Info($str){
  return  unserialize(base64_decode($str));
}
