<?php /*a:1:{s:72:"D:\phpStudy\PHPTutorial\WWW\myshop\application\index\view\index\div.html";i:1555059701;}*/ ?>
<div cate_id="<?php echo htmlentities($FloorInfo['firstInfo']['cate_id']); ?>">
      <div class="i_t mar_10">
        <span class="floor_num"><?php echo htmlentities($floor_num); ?>F</span>
        <span class="fl"><?php echo htmlentities($FloorInfo['firstInfo']['cate_name']); ?></span>                
        <span class="i_mores fr">
            <?php if(is_array($FloorInfo['secondInfo']) || $FloorInfo['secondInfo'] instanceof \think\Collection || $FloorInfo['secondInfo'] instanceof \think\Paginator): $i = 0; $__LIST__ = $FloorInfo['secondInfo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <a href="#"><?php echo htmlentities($v['cate_name']); ?></a>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </span>
      </div>
      <div class="content">

        <div class="fresh_mid">
            <ul>
                <?php if(is_array($FloorInfo['goodsInfo']) || $FloorInfo['goodsInfo'] instanceof \think\Collection || $FloorInfo['goodsInfo'] instanceof \think\Paginator): $i = 0; $__LIST__ = $FloorInfo['goodsInfo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li>
                    <div class="name"><a href="#"><?php echo htmlentities($v['goods_name']); ?></a></div>
                    <div class="price">
                        <font>￥<span><?php echo htmlentities($v['shop_price']); ?></span></font> &nbsp;
                    </div>
                    <div class="img"><a href="<?php echo url('Goods/goodsProduct'); ?>?goods_id=<?php echo htmlentities($v['goods_id']); ?>"><img src="/uploads/<?php echo htmlentities($v['goods_img']); ?>" width="185" height="155" /></a></div>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                                                                                
            </ul>
        </div>
      </div>
    </div>    