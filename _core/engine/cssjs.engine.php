<?php
if(!defined('__KIMS__')) exit;
$g['incdir'] = $g['incdir']?$g['incdir']:$g['path_layout'].$d['layout']['dir'].'/_includes/';
$g['wcache'] = $d['admin']['cache_flag']?'?nFlag='.$date[$d['admin']['cache_flag']]:'';
$g['cssset'] = array
(
	$g['dir_module'].'_main'=>$g['url_module'].'/_main',
	$g['dir_module_comm']=>$g['url_module_comm'],
	$g['dir_module_mode']=>$g['url_module_mode'],
	$g['dir_module_admin']=>$g['url_module_admin'],
);
?>

<script>
var rooturl = '<?php echo $g['url_root']?>';
var rootssl = '<?php echo $g['ssl_root']?>';
var fronturl = '<?php echo $g['front_host']?>';
var raccount= '<?php echo $r?>';
var moduleid= '<?php echo $m?>';
var memberid= '<?php echo $my['id']?>';
var is_admin= '<?php echo $my['admin']?>';
var sm_time= '<?php echo $g['sm_time']?>';
</script>

<script src="<?php echo $g['s']?>/_core/js/sys.js"></script>

<?php foreach ($g['cssset'] as $_key => $_val):?>
<?php if (is_file($_key.'.css')):?>
<link href="<?php echo $_val?>.css<?php echo $g['wcache']?>" rel="stylesheet">
<?php endif?>

<?php if (is_file($_key.'.js')):?>
<script src="<?php echo $_val?>.js<?php echo $g['wcache']?>"></script>
<?php endif?>
<?php endforeach?>


<!-- 헤더 스위치 -->
<?php foreach($g['switch_2'] as $_switch) include $_switch ?>
