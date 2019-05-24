<?php

namespace app\admin\model;

use think\Model;

class Friend extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
}
