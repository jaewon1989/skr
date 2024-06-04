<?php
include_once $g['path_module'].$module.'/var/var.php';
include_once $g['path_module'].$module.'/_main.php';
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;
$p=$p?$p:1;

$_WHERE = 'uid>0';
if ($display != '') $_WHERE .= ' and display='.$display;
if ($cat){
   //$C=getUidData($table[$m.'category'],$cat);
   //$cat_depth=$C['depth'];

   $_WHERE .= " and category like '%".$cat."%'";	
} 
if ($stock) $_WHERE .= ' and stock=1';
if ($is_free) $_WHERE .= ' and is_free=1';
if ($is_cash) $_WHERE .= ' and is_cash=1';
if ($maker) $_WHERE.= " and maker='".$maker."'";
if ($brand) $_WHERE.= " and brand='".$brand."'";
if ($where && $keyw)
{
	$_WHERE .= getSearchSql($where,$keyw,$ikeyword,'or');	
}

$RCD = getDbArray($table[$module.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'product'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>