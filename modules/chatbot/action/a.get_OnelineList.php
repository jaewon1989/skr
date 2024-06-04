<?php
if(!defined('__KIMS__')) exit;
include $theme.'/function.php';
$mod='write'; // 이것은 쓰기모드의 리스트를 보여준다는 의미 
?>
[RESULT:
<?php if($depth):?>
<?php echo getOneline2List($mod,$parent)?>
<?php else:?>
<?php echo getOnelineList($theme,$parent)?>
<?php endif?>
:RESULT]
<?php
exit;
?>
