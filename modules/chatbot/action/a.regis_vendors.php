<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 


// 신규등록 
$NOWUID = $LASTUID ? $LASTUID : $R['uid'];


$next_link = $g['s'].'/?r='.$r.'&m='.$m.'&page=build/step5&vendor='.$NOWUID;
getLink($next_link,'parent.','','');


?>
