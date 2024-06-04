<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

$tmpname	= $_FILES['upfile']['tmp_name'];
$realname	= $_FILES['upfile']['name'];
$nameinfo	= explode('_',str_replace('.zip','',$realname));
$plFolder	= $nameinfo[2];
for($_i = 3; $_i < count($nameinfo); $_i++)
{
	$plFolder .= '_';
	$plFolder .= $nameinfo[$_i];
}
$fileExt	= strtolower(getExt($realname));
$extPath	= $g['path_tmp'].'app';
$extPath1	= $extPath.'/';
$saveFile	= $extPath1.$date['totime'].'.zip';
$plfldPath	= $g['path_module'].$plFolder;
$tgFolder	= $plfldPath.'/';

if (is_uploaded_file($tmpname))
{
	if ($fileExt != 'zip' || substr($realname,0,10) != 'rb_module_')
	{
		getLink('reload','parent.',_LANG('a2001','market'),'');
	}

	if (is_file($g['path_module'].$plFolder.'/main.php'))
	{
		getLink('','',sprintf(_LANG('a2002','market'),$plFolder),'');
	}

	move_uploaded_file($tmpname,$saveFile);

	require $g['path_core'].'opensrc/unzip/ArchiveExtractor.class.php';
	require $g['path_core'].'function/dir.func.php';
	
	$extractor = new ArchiveExtractor();
	$extractor -> extractArchive($saveFile,$extPath1);
	unlink($saveFile);
	mkdir($plfldPath,0707);
	@chmod($plfldPath,0707);
	DirCopy($extPath1,$tgFolder);
	DirDelete($extPath);
	mkdir($extPath,0707);
	@chmod($extPath,0707);
}
else {
	getLink('','',_LANG('a2003','market'),'');
}

$module		= $plFolder;
$_tmptable2 = $table;
$table		= array();
$table_db	= $g['path_module'].$module.'/_setting/db.table.php';
$table_sc	= $g['path_module'].$module.'/_setting/db.schema.php';
if(is_file($table_db)) 
{	
	$_tmptable1 = array();
	$_tmptfile  = $g['path_var'].'table.info.php';
	include $table_db;
	include $table_sc;

	$_tmptable1 = $table;
	rename($table_db,$table_db.'.done');

	$fp = fopen($_tmptfile,'w');
	fwrite($fp, "<?php\n");
	foreach($_tmptable2 as $key => $val) fwrite($fp, "\$table['$key'] = \"$val\";\n");
	foreach($_tmptable1 as $key => $val) fwrite($fp, "\$table['$key'] = \"$val\";\n");
	fwrite($fp, "?>");
	fclose($fp);
	@chmod($_tmptfile,0707);
}
else {
	if(is_file($table_db.'.done')) include $table_db.'.done';
}

$maxgid = getDbCnt($_tmptable2['s_module'],'max(gid)','');

$QKEY = "gid,system,hidden,mobile,name,id,tblnum,icon,d_regis";
$QVAL = "'".($maxgid+1)."','0','0','1','".getFolderName($g['path_module'].$module)."','$module','".count($table)."','kf-module','".$date['totime']."'";

getDbInsert($_tmptable2['s_module'],$QKEY,$QVAL);

?>
<script>
var pt = parent.parent.parent ? parent.parent.parent : parent.parent;
var ex = pt.location.href.split('&_admpnl_');
var gx = ex[0] + '&_admpnl_=' + escape(pt.frames._ADMPNL_.location.href);
pt.location.href = gx;
</script>
<?php
exit;
//if ($reload == 'Y') getLink('reload',"parent.parent.",_LANG('a2004','market'),'');
//else getLink('',"parent.parent.$('#modal_window').modal('hide');",_LANG('a2004','market'),'');
?>