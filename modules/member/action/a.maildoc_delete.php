<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

unlink($g['dir_module'].'doc/'.$type.'.txt');

getLink($g['s'].'/?r='.$r.'&m=admin&module='.$m.'&front=maildoc','parent.','삭제되었습니다. ','');
?>