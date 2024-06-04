<!DOCTYPE html>
<html lang="ko">
<head>
<?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
<link href="<?php echo $g['url_layout']?>/_css/style.css" rel="stylesheet">
</head>
<body class="container">
    
<div class="snap-drawers">
    <div class="snap-drawer snap-drawer-left" id="drawer-left">
        <?php include $g['dir_layout'].'/_includes/drawer-left.php' ?>
    </div>
</div> 

<div class="snap-content" id="content-wrap">

    <header class="bar bar-nav rb-inverse">
        <button class="btn btn-link btn-nav pull-left" data-toggle="drawer" data-drawer-open="left">
            <span class="icon icon-bars"></span>
        </button>
        <h1 class="title" style="padding-top: 10px">
            <a href="/" data-ignore="push"><img src="<?php echo $g['img_layout']?>/logo-white.png" style="height: 30px"></a>
        </h1>
    </header>

	<div class="content scrollable" id="content" data-snap-ignore="true">
		<?php include __KIMS_CONTENT__ ?>
		<?php include $g['dir_layout'].'/_includes/footer.php' ?>
	</div>

    <?php getWidget('rc-default/floatingButton-2',array())?>

</div>


<?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>
<script src="<?php echo $g['url_layout']?>/_js/script-gitaek.js"></script>
<?php include $g['dir_layout'].'/_includes/modals.php' ?>
</body>
</html>
