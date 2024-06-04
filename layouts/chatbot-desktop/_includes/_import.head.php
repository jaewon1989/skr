<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1" />
<meta name="apple-mobile-web-app-capable" content="no">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<!-- Seo -->
<meta name="robots" content="<?php echo strip_tags($g['meta_bot'])?>">
<meta name="title" content="<?php echo strip_tags($g['meta_tit'])?>">
<meta name="keywords" content="<?php echo strip_tags($g['meta_key'])?>">
<meta name="description" content="<?php echo strip_tags($g['meta_des'])?>">
<link rel="image_src" href="<?php echo strip_tags($g['meta_img'])?>">

<title><?php echo $g['browtitle']?></title>

<!-- Favicons -->
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

<!--[if lt IE 9]>
<script src="<?php echo $g['s']?>/_core/js/html5shiv.min.js"></script>
<script src="<?php echo $g['s']?>/_core/js/respond.min.js"></script>
<![endif]-->

<!--link href="/plugins/swiper/3.2.7/css/swiper.css" rel="stylesheet"-->
<link href="/plugins/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
<link href="//cdn.jsdelivr.net/font-nanum/1.0/nanumgothic/nanumgothic.css" rel="stylesheet">

<link href="<?php echo $g['url_layout']?>/_css/cb-common.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/cb.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/cb-new.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/pswp/photoswipe.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/pswp/default-skin.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/animation.css" rel="stylesheet">

<!-- jQuery -->
<script src="/plugins/jquery/3.6.0/jquery-3.6.0.min.js"></script>

<!-- 클립보드 : clipboard.js  : https://github.com/zenorocha/clipboard.js-->
<?php getImport('clipboard','clipboard.min','1.5.5','js') ?>

<!--script src="<?php echo $g['s']?>/_core/js/rc.swiper.js"></script-->
<!--script src="<?php echo $g['url_layout']?>/_js/pswp/rc.photoswipe.js"></script-->
<!--script src="<?php echo $g['url_layout']?>/_js/pswp/photoswipe-ui-default.min.js"></script-->

<!-- 사이트 헤드 코드 -->
<?php echo $_HS['headercode']?>

<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/cssjs.engine.php'?>


