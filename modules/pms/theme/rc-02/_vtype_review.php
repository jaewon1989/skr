<div class="rb-viewtype-review">
    <ul class="media-list">
        <?php foreach($RCD as $R):?>

        <li class="media rb-blog-item">
            <div class="media-left">
                <a href="<?php echo $g['shop_view'].$R['uid']?>">
                    <img class="media-object img-thumbnail" src="<?php echo getPic($R,'m')?>" alt="<?php echo $R['name']?>">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading rb-title">
                    <a href="<?php echo $g['shop_view'].$R['uid']?>"><?php echo $R['name']?></a>
                </h4>
                <p class="rb-description"><?php echo $R['review']?></p>
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
        </li>
        <?php endforeach?>
    </ul>
</div>

