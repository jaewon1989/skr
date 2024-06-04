<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no">
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

<!-- jQuery -->
<script src="/plugins/jquery/3.6.0/jquery-3.6.0.min.js"></script>

<?php getImport('swiper','css/swiper','3.2.7','css') ?>
<!-- bootstrap js -->
<?php getImport('bootstrap','js/bootstrap.min','3.3.7','js')?>

<!-- 클립보드 : clipboard.js  : https://github.com/zenorocha/clipboard.js-->
<?php getImport('clipboard','clipboard.min','1.5.5','js') ?>

<!-- 시스템 폰트 -->
<?php getImport('font-awesome','css/fontawesome-all','5.0.9','css')?>
<link href="//cdn.jsdelivr.net/font-nanum/1.0/nanumgothic/nanumgothic.css" rel="stylesheet">
<link href="/plugins/bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet">

<!-- Menu CSS -->
<link href="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
<!-- toast CSS -->
<link href="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
<!-- morris CSS -->
<link href="<?php echo $g['url_layout']?>/pixeladmin/plugins/bower_components/morrisjs/morris.css" rel="stylesheet">
<!-- animation CSS -->
<link href="<?php echo $g['url_layout']?>/pixeladmin/css/animate.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="<?php echo $g['url_layout']?>/pixeladmin/css/style.css" rel="stylesheet">
<!-- color CSS -->
<link href="<?php echo $g['url_layout']?>/pixeladmin/css/colors/blue-dark.css" id="theme" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<link href="<?php echo $g['url_layout']?>/_css/kiere.css?<?=date('ymdHi')?>" rel="stylesheet">	
<script src="<?php echo $g['s']?>/_core/js/rc.swiper.js"></script>

<!-- 사이트 헤드 코드 -->
<?php echo $_HS['headercode']?>

<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/cssjs.engine.php';?>
