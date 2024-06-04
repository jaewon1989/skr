<!DOCTYPE html>
<html lang="<?php echo $lang['xlayout']['lang']?>">
<head>
<?php include $g['dir_layout'].'/_includes/_import.head.php';?>
</head>
<body id="rb-body" class="cb-body<?php echo $no_scroll?' no-scroll':''?>">
	<!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper">

        <?php include $g['dir_layout'].'/_includes/header.php';?><!-- Navigation -->
    	<?php if($page!='adm/main' && $page!='adm/list'):?>
    	    <?php include $g['dir_layout'].'/_includes/sidebar.php';?><!-- Left navbar-header -->
        <?php endif?>
        <!-- Page Content -->
        <div id="page-wrapper" <?php if($page=='adm/main' || $page=='adm/list'):?>class="main-pageWrapper"<?php endif?>>
            <?php include __KIMS_CONTENT__ ?>
            <?php include $g['dir_layout'].'/_includes/footer.php';?>
            <!-- /.container-fluid -->           
        </div>
    </div> 
    <?php include $g['dir_layout'].'/_includes/_import.foot.php';?>

</body>
</html>
