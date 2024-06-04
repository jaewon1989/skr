<meta charset="utf-8">
<!--<meta id="dm-viewport" name="viewport" content="width=device-width, initial-scale=1">-->
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1" />
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
<link href="/plugins/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="<?php echo $g['url_layout']?>/_css/animation.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/swiper.min.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/pswp/photoswipe.css" rel="stylesheet">
<link href="<?php echo $g['url_layout']?>/_css/pswp/default-skin.css" rel="stylesheet">

<!-- kimsQ RC core JavaScript -->
<script src="/plugins/jquery/3.6.0/jquery-3.6.0.min.js"></script>
<!-- katalk js -->
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>

<script src="<?php echo $g['s']?>/_core/js/rc.swiper.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/pswp/rc.photoswipe.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/pswp/photoswipe-ui-default.min.js"></script>

<script>
// 회원 uid 값 추가
var memberuid='<?php echo $my['uid']?>';
</script>

<!-- 더보기 : jquery.shorten : http://viralpatel.net -->
<?php getImport('jquery-shorten','jquery.shorten',false,'js') ?>

<!-- SNS 공유 : sharrre.js  -->
<?php getImport('sharrre','jquery.sharrre',false,'js') ?>

<!-- 엔진코드:삭제하지마세요 -->
<?php include $g['path_core'].'engine/cssjs.engine.php';?>