<!DOCTYPE html>
<html lang="<?php echo $lang['xlayout']['lang']?>">
<head>
<?php include $g['dir_layout'].'/_includes/_import.head.php';?>
</head>
<body id="rb-body" class="rb-layout-home">

	<div class="container">
		<div class="row">
			<div class="col-md-12" role="main" id="content-main">
				<?php include __KIMS_CONTENT__;?>
			</div>
		</div>
	</div>

	<?php include $g['dir_layout'].'/_includes/footer.php'?>
	<?php include $g['dir_layout'].'/_includes/_import.foot.php'?>
	
</body>
</html>
