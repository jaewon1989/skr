<style>
.rb-list-gallery {padding-bottom: 20px;}
</style>
<div class="rb-viewtype-gallery">
    <div class="row">
       <?php foreach ($RCD as $R):?>
        <div class="col-xs-6 col-sm-4 col-md-3">
            <div class="rb-blog-item">
                <div class="rb-img-wrapper">
                    <img class="img-thumbnail" src="<?php echo getPic($R,'m')?>" alt="<?php echo $U['name']?>" width="130">
                    <div class="rb-overlay">
                        <a href="<?php echo getPic($R,'b')?>" class="btn lightbox">Zoom</a>
                        <a href="<?php echo $g['shop_view'].$R['uid']?>" class="btn">View</a>
                    </div>
                </div>
                <div>
                    <?php if(getPumjeol($R)):?><span class="badge">(품절)</span><?php endif?>
                    <a href="<?php echo $g['shop_view'].$R['uid']?>"><?php echo $R['name']?></a> 
                     <?php echo getGoodsIcon($R,$m)?>
                 </div>
                 <ul class="rb-meta list-inline">
                      <li><?php echo getPrice($R,'원')?></li>
                      <li>(적립<?php echo number_format($R['point'])?>)</li>
                       <?php if(getNew($_R['d_regis'],24)):?>
                       <li>
                            <div class="rb-sticker rb-sticker-new"></div>
                       </li>
                     <?php endif?>
                 </ul>  
            </div>
        </div>
        <?php endforeach?>
    </div>               
</div>
