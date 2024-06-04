<?php if($NUM):?>
<div class="rb-viewtype-list">
    <table class="table">
        <colgroup> 
            <col width="100"> 
            <col> 
            <col width="80"> 
            <col width="100"> 
            <col width="80">
            <col width="80">
        </colgroup>
        <thead>
            <tr>
                <th></th>
                <th class="rb-title">상품기본정보</th>
                <th class="rb-user">구매혜택</th>
                <th class="rb-hit">판매가격</th>
                <th class="rb-time">적립금</th>
                <th class="rb-time">상품평</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($RCD as $R):?>
             <tr>
                <td><a href="<?php echo $g['shop_view'].$R['uid']?>"><img src="<?php echo getPic($R,'q')?>" width="80" alt="<?php echo $R['name']?>" class="pic" /></a></td>
                <td class="rb-title">
                       <?php if(getPumjeol($R)):?><span class="badge">[품절]</span><?php endif?>
                       <a href="<?php echo $g['shop_view'].$R['uid']?>">
                             <?php if($R['maker']):?>[<?php echo $R['maker']?>]<?php endif?>
                             <?php if($R['brand']):?>[<?php echo $R['brand']?>]<?php endif?>
                             <?php if($R['country']):?>[<?php echo $R['country']?>]<?php endif?>
                             <?php echo $R['name']?>
                        </a> 
                       <br />
                        <?php echo getGoodsIcon($R,$m)?>        
                </td>
                <td class="rb-user">
                    <?php if($R['is_free']):?><span class="badge">무료배송</span><br /><?php endif?>
                </td>
                <td class="rb-num"> 
                     <?php echo getPrice($R,'원')?>
                     <?php if($R['addoptions']):?><br /><img src="<?php echo $g['img_module_skin']?>/list/ico_option.gif" alt="옵션" /><?php endif?>
                </td>
                <td class="rb-num"><?php echo number_format($R['point'])?>원</td>
                <td class="rb-time">
                     <?php if($R['comment']):?>
                            <img src="<?php echo $g['img_module_skin']?>/s_<?php echo $R['comment']?round(($R['vote']/$R['comment'])/2):0?>.gif" alt="" />
                            <br />
                            (<?php echo $R['comment']?>건)
                            <?php else:?>
                            &nbsp;
                       <?php endif?>
                </td>
            </tr>
            <?php endforeach?>     
        </tbody>
    </table>
</div>
<?php else:?>
 <div class="rb-bbs-body rb-nopost">
      <h2 class=""><i class="fa fa-exclamation-circle fa-4x"></i></h2>
       <p>등록된 상품이 없습니다.</p>
 </div>
<?php endif?>
