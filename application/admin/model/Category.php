<?php

namespace app\admin\model;

use think\Model;

class Category extends Model
{
    protected $pk = 'cate_id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'addtime';
    protected $updateTime = false;
}
