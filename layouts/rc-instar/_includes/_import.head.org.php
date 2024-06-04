<meta charset="utf-8">
<meta id="dm-viewport" name="viewport" content="width=device-width, initial-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
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
<meta name="twitter:image" content="<?php echo strip_tags($g['meta_img'])?>" />

<!-- Core css -->
<!-- 시스템 폰트 -->
<?php getImport('font-awesome','css/font-awesome',false,'css')?>
<?php getImport('font-kimsq','css/font-kimsq',false,'css')?>

<!-- Custom styles for this template -->
<link href="<?php echo $g['url_layout']?>/_css/rc.cus.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/video-js.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/animation.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/rc.snap.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/swiper.min.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/pswp/photoswipe.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/pswp/default-skin.css" rel="stylesheet">

<?php if($_GET['call']=='external'):?>
<link href="<?php echo $g['url_layout']?>/_css/cb-common-m-ex.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/cb-m-ex.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/kiere-ex.css" rel="stylesheet">
<?php else:?>
<link href="<?php echo $g['url_layout']?>/_css/cb-common-m.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/cb-m.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/kiere.css" rel="stylesheet">
<?php endif?>
<link href="<?php echo $g['url_layout']?>/_css/ex-inner.css" rel="stylesheet">
<!-- iphone startup image -->
<!-- iPhone 6 -->
<link href="<?php echo $g['s']?>/files/static/750x1294.png" media="(device-width: 375px) and (device-height: 667px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
<!-- iPhone 6+ Portrait -->
<link href="<?php echo $g['s']?>/files/static/1242x2148.png" media="(device-width: 414px) and (device-height: 736px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image">

<!-- default Landscape -->
<link href="<?php echo $g['s']?>/files/static/startup.png" rel="apple-touch-startup-image">


<!-- kimsQ RC core JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- katalk js -->
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>


<script>
// 회원 uid 값 추가
var memberuid='<?php echo $my['uid']?>';
</script>

<!-- 더보기 : jquery.shorten : http://viralpatel.net -->
<?php getImport('jquery-shorten','jquery.shorten',false,'js') ?>

<!-- SNS 공유 : sharrre.js  -->
<?php getImport('sharrre','jquery.sharrre',false,'js') ?>
<?php if($cmod != 'dialog' ):?>
<?php getImport('socket.io-client','socket.io',false,'js') ?>
<?php endif?>


<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/cssjs.engine.php';?>
<script src="<?php echo $g['url_layout']?>/_js/pswp/rc.photoswipe.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/pswp/photoswipe-ui-default.min.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/rc.swiper.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/jquery.timer.js"></script>
<script src="<?php echo $g['url_root']?>/_core/js/jquery.bottalks.2.0.js"></script>

