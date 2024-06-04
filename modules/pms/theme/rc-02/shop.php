<?php
$vtype=$vtype?$vtype:'gallery';
?>
<link href="<?php echo $g['url_module_skin']?>/_main.css" rel="stylesheet">

<header class="bar bar-nav">
    <a class="btn btn-link btn-nav pull-left" href="/">
          <img src="<?php echo $g['img_layout']?>/logo-black.png" class="rb-logo">
    </a>
    <h1 class="title"><?php echo $_SHOPTITLE?></h1>
</header>

<div class="content">

    <div class="row">
       <?php foreach ($RCD as $R):?>
        <div class="col-xs-6 col-sm-4 col-md-3">
            <div class="card">
                <img class="card-img-top img-fluid" src="<?php echo getPic($R,'m')?>" alt="<?php echo $U['name']?>">
                <div class="card-block">
                    <h4 class="card-title"><?php echo $R['name']?></h4>
                    <p class="card-text"><?php echo $R['review']?></p>
                    <a href="<?php echo $g['shop_view'].$R['uid']?>" class="btn btn-primary">View</a>
                </div>
            </div>
        </div>
        <?php endforeach?>
    </div>               

    <?php if(!$NUM && ($vtype=='review' || $vtype=='gallery')):?>
    <div class="rb-nopost">
        <h2 class=""><i class="fa fa-exclamation-circle fa-4x"></i></h2>
        <p>등록된 상품이 없습니다.</p>
    </div>
    <?php endif?>


</div>