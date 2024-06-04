<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<!-- Seo -->
<meta name="robots" content="<?php echo strip_tags($g['meta_bot'])?>">
<meta name="title" content="<?php echo strip_tags($g['meta_tit'])?>"> 
<meta name="keywords" content="<?php echo strip_tags($g['meta_key'])?>"> 
<meta name="description" content="<?php echo strip_tags($g['meta_des'])?>">
<link rel="image_src" href="<?php echo strip_tags($g['meta_img'])?>"> 

<title><?php echo $g['browtitle']?></title>

<!-- Favicons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $g['s']?>/_core/images/ico/apple-touch-icon-144-precomposed.png">
<link rel="shortcut icon" href="<?php echo $g['s']?>/_core/images/ico/favicon.ico">

<!-- Open Graph data for Facebook -->
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php echo strip_tags($g['url_root'].$_SERVER['REQUEST_URI'])?>" />
<meta property="og:title" content="<?php echo strip_tags($g['meta_tit'])?>" />
<meta property="og:description" content="<?php echo strip_tags($g['meta_des'])?>" />
<meta property="og:image" content="<?php echo strip_tags($g['meta_img'])?>" />
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="<?php echo strip_tags($g['meta_tit'])?>">
<meta itemprop="description" content="<?php echo strip_tags($g['meta_des'])?>">
<meta itemprop="image" content="<?php echo strip_tags($g['meta_img'])?>"> 
<!-- Twitter Card data -->
<meta name="twitter:card" content="summary"/>
<meta name="twitter:url" content="<?php echo strip_tags($g['url_root'].$_SERVER['REQUEST_URI'])?>"/>
<meta name="twitter:title" content="<?php echo strip_tags($g['meta_tit'])?>" />
<meta name="twitter:description" content="<?php echo strip_tags($g['meta_des'])?>" />
<meta name="twitter:image:src" content="<?php echo strip_tags($g['meta_img'])?>" />

<!-- ratchet css -->
<?php //getImport('ratchet','css/ratchet',false,'css')?>
<?php //getImport('ratchet','css/ratchet-theme-ios.min',false,'css')?>
<?php //getImport('ratchet','css/ratchet-theme-android.min',false,'css')?>

<!-- ratchet plus CSS -->
<?php getImport('ratchet','css/ratchet',false,'css')?>
<?php getImport('ratchet','css/ratchet-plus',false,'css')?>

<!-- jQuery -->
<?php getImport('jquery','jquery-'.$d['ov']['jquery'].'.min',false,'js')?>

<!-- 시스템 폰트 -->
<?php getImport('font-awesome','css/font-awesome',false,'css')?> 

<!-- theme css -->
<link href="<?php echo $g['url_layout']?>/_css/ratchet-theme-rb.css" rel="stylesheet">

<!-- 사이트 헤드 코드 -->
<?php echo $_HS['headercode']?>

<!-- photoswipe -->
<?php getImport('photoswipe','photoswipe',false,'css')?>
<?php getImport('photoswipe','default-skin/default-skin',false,'css')?>

<!-- Swiper : https://github.com/nolimits4web/Swiper -->
<?php getImport('swiper','css/swiper',false,'css') ?>

<?php getImport('snap','snap',false,'css') ?>

<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/cssjs.engine.php' ?>

<!-- Swiper : https://github.com/nolimits4web/Swiper -->
<?php getImport('swiper','js/swiper.jquery.min',false,'js') ?>
