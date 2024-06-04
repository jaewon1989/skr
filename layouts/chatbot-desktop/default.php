<!DOCTYPE html>
<html lang="<?php echo $lang['xlayout']['lang']?>">
<head>
<?php include $g['dir_layout'].'/_includes/_import.head.php';?>
</head>
<body id="rb-body" class="cb-body<?php echo $is_vendorPage?' vendorPage':''?>">
	    <?php include $g['dir_layout'].'/_includes/header.php'?>
	<?php if(!$no_nav):?>
	    <?php include $g['dir_layout'].'/_includes/banner_nav.php';?>
    <?php endif?>
    <?php if($is_oneFrame):?>
        <div class="minH">
        <?php include __KIMS_CONTENT__ ?>
        </div>
    <?php else:?>
		<section id="cb-category-content">
		    <div class="cb-category-content-wrapper cb-content">
		        <div class="cb-category-menudropdown-<?php echo $has_submenu?'on':'off'?> cb-layout">
		            <div class="cb-category-menuspacefill"></div>
		            <div class="cb-category-contentarea minH">   
				       <?php include __KIMS_CONTENT__ ?>
				    </div>
				</div>       
			</div>
		</section>
    <?php endif?>
	<?php include $g['dir_layout'].'/_includes/footer.php'?>
    <?php include $g['dir_layout'].'/_includes/_import.foot.php';?>
</body>
</html>
