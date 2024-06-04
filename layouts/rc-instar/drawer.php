<!DOCTYPE html>
<html lang="<?php echo $lang['xlayout']['lang']?>">
<head>
<?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
</head>
<body id="rb-body">

<div class="snap-drawers">   
    <div class="snap-drawer snap-drawer-left scrollable">
        <?php include $g['path_layout'].$d['layout']['dir'].'/_includes/drawer-menu.php' ?>
	</div>
    <div class="snap-drawer snap-drawer-right scrollable">
    </div>
</div> 

<div class="snap-content" id="content-wrap">

	<header class="bar bar-nav">
	  <button class="btn btn-link btn-nav pull-left" id="rb-sidebar-left-deploy">
	    <span class="icon icon-bars"></span>
	  </button>
	  <a class="btn btn-link btn-nav pull-right" href="#SearchModal">
	  	<span class="icon icon-search"></span>
	  </a>

	  <h1 class="title"><a href="<?php echo RW(0)?>"><?php echo $d['layout']['header_title']?></a></h1>
	</header>

	<div class="content">
		<?php include __KIMS_CONTENT__ ?>
		<?php include $g['dir_layout'].'/_includes/footer.php' ?>
	</div>
</div>

<?php include $g['dir_layout'].'/_includes/modals.php' ?>
<?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>

</body>
</html>
