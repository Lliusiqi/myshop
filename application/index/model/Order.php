<?php

namespace app\index\model;

use think\Model;

class Order extends Model
{
    protected $pk="order_id";
    protected $autoWriteTimestamp=true;
}
