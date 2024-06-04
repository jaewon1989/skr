<?php
if (!$uid)
{

	$R = $RCD[0];
	$uid = $R['uid'];
	if($R['mbruid']) $g['member'] = getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'*');

	if ($uid)
	{
		include_once $g['dir_module'].'lang.'.$_HS['lang'].'/mod/_view.php';
		include_once $g['dir_module_skin'].'view.php';
	}
	else {
		include_once $g['dir_module_skin'].'nopost.php';	
	}
}
?>