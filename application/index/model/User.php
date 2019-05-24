<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
    protected $pk='u_id';
    //开启自动写入时间戳
    protected $autoWriteTimestamp=true;
    protected $updateTime=false;

    public function setUPwdAttr($value)
    {
    	return md5($value);
    }
}
