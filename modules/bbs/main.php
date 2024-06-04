<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/var.php';
$d['bbs']['skin'] = $d['bbs']['skin_total'];
$d['bbs']['isperm'] = true;

if($uid)
{
	$R = $R['uid'] ? $R : getUidData($table[$m.'data'],$uid);
	if (!$R['uid']) getLink($g['s'].'/','','존재하지 않는 게시물입니다.','');
	$B = getUidData($table[$m.'list'],$R['bbs']);
	include_once $g['dir_module'].'var/var.'.$B['id'].'.php';
	include_once $g['dir_module'].'mod/_view.php';
	if ($d['bbs']['isperm'])
	{
		if(strpos('_'.$B['puthead'],'[v]'))
		{
			$g['add_header_inc'] = $g['dir_module'].'var/code/'.$B['id'].'.header.php';
			if($B['imghead']) $g['add_header_img'] = $g['url_module'].'/var/files/'.$B['imghead'];
		}
		if(strpos('_'.$B['putfoot'],'[v]'))
		{
			$g['add_footer_inc'] = $g['dir_module'].'var/code/'.$B['id'].'.footer.php';
			if($B['imgfoot']) $g['add_footer_img'] = $g['url_module'].'/var/files/'.$B['imgfoot'];
		}
		if($R['mbruid']) $g['member'] = getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'*');
		if(!$_HS['titlefix']) $g['browtitle'] = $_HS['title'].' - '.getStripTags($R['subject']);
		$g['meta_tit'] = $_HS['name'].' - '.$B['name'];
		$g['meta_sbj'] = str_replace('"','\'',$R['subject']);
		$g['meta_key'] = $R['tag'] ? $B['name'].','.$R['tag'] : $B['name'].','.str_replace('"','\'',$R['subject']);
		$g['meta_des'] = getStrCut(getStripTags($R['content']),150,'');
		$g['meta_cla'] = $R['category'];
		$g['meta_rep'] = '';
		$g['meta_lan'] = 'kr';
		$g['meta_bui'] = getDateFormat($R['d_regis'],'Y.m.d');

	}
}
else {
	if($bid)
	{
		$B = getDbData($table[$m.'list'],"id='".$bid."'",'*');
		if (!$B['uid'])
		{
			if($_stop=='Y') exit;
			getLink($g['s'].'/?r='.$r.'&_stop=Y','','존재하지 않는 게시판입니다.','');
		}
		include_once $g['dir_module'].'var/var.'.$B['id'].'.php';

		$_SEO = getDbData($table['s_seo'],'rel=3 and parent='.$B['uid'],'*');
		if ($_SEO['uid'])
		{
			$g['meta_tit'] = $_SEO['title'];
			$g['meta_sbj'] = $_SEO['subject'];
			$g['meta_key'] = $_SEO['keywords'];
			$g['meta_des'] = $_SEO['description'];
			$g['meta_cla'] = $_SEO['classification'];
			$g['meta_rep'] = $_SEO['replyto'];
			$g['meta_lan'] = $_SEO['language'];
			$g['meta_bui'] = $_SEO['build'];
		}
		else {
			$g['meta_tit'] = $_HS['name'].' - '.$B['name'];
			$g['meta_sbj'] = $B['name'];
			$g['meta_key'] = $B['name'];
		}
		if(!$_HS['titlefix']&&!$_HM['uid']) $g['browtitle'] = $_HS['title'].' - '.strip_tags($B['name']);
	}
	else {
		if (!$d['bbs']['skin_total']) getLink('','','게시판아이디가 지정되지 않았습니다.','-1');
	}
}

$mod = $mod ? $mod : 'list';
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby && strpos('[asc][desc]',$orderby) ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : $d['bbs']['recnum'];

if ($mod == 'list')
{
	if (!$my['admin'] && !strstr(','.($d['bbs']['admin']?$d['bbs']['admin']:'.').',',','.$my['id'].','))
	{
		if ($d['bbs']['perm_l_list'] > $my['level'] || strpos('_'.$d['bbs']['perm_g_list'],'['.$my['sosok'].']'))
		{
			$g['main'] = $g['dir_module'].'mod/_permcheck.php';
			$d['bbs']['isperm'] = false;
		}
	}
	if ($d['bbs']['isperm'])
	{
		if(strpos('_'.$B['puthead'],'[l]'))
		{
			$g['add_header_inc'] = $g['dir_module'].'var/code/'.$B['id'].'.header.php';
			if($B['imghead']) $g['add_header_img'] = $g['url_module'].'/var/files/'.$B['imghead'];
		}
		if(strpos('_'.$B['putfoot'],'[l]'))
		{
			$g['add_footer_inc'] = $g['dir_module'].'var/code/'.$B['id'].'.footer.php';
			if($B['imgfoot']) $g['add_footer_img'] = $g['url_module'].'/var/files/'.$B['imgfoot'];
		}
	}
	if (!$d['bbs']['hidelist']) include_once $g['dir_module'].'mod/_list.php';
}
else if ($mod == 'write')
{
    

	if (!$my['admin'] && !strstr(','.($d['bbs']['admin']?$d['bbs']['admin']:'.').',',','.$my['id'].','))
	{
		if ($d['bbs']['perm_l_write'] > $my['level'] || strpos('_'.$d['bbs']['perm_g_write'],'['.$my['sosok'].']'))
		{
			$g['main'] = $g['dir_module'].'mod/_permcheck.php';
			$d['bbs']['isperm'] = false;
		}
		if ($R['uid'] && $reply != 'Y')
		{
			if ($my['uid'] != $R['mbruid'])
			{
				 if (!strpos('_'.$_SESSION['module_'.$m.'_pwcheck'],'['.$R['uid'].']')) $g['main'] = $g['dir_module'].'mod/_pwcheck.php';
			}
		}
	}
	if ($d['bbs']['isperm'])
	{
		if(strpos('_'.$B['puthead'],'[w]'))
		{
			$g['add_header_inc'] = $g['dir_module'].'var/code/'.$B['id'].'.header.php';
			if($B['imghead']) $g['add_header_img'] = $g['url_module'].'/var/files/'.$B['imghead'];
		}
		if(strpos('_'.$B['putfoot'],'[w]'))
		{
			$g['add_footer_inc'] = $g['dir_module'].'var/code/'.$B['id'].'.footer.php';
			if($B['imgfoot']) $g['add_footer_img'] = $g['url_module'].'/var/files/'.$B['imgfoot'];
		}
	}
	if ($reply == 'Y') $R['subject'] = $d['bbs']['restr'].$R['subject'];
	if (!$R['uid']) $R['content'] = $B['writecode'] ? $B['writecode'] : $R['content'];
	$_SESSION['wcode'] = $date['totime'];
}
else if ($mod == 'delete')
{
	$g['main'] = $g['dir_module'].'mod/_pwcheck.php';
}
else if ($mod == 'rss')
{
	include_once $g['dir_module'].'mod/_rss.php';
	exit;
}

$_HM['layout'] = $_HM['layout'] ? $_HM['layout'] : $d['bbs']['layout'];
$d['bbs']['skin'] = $d['bbs']['skin'] ? $d['bbs']['skin'] : $d['bbs']['skin_main'];
$d['bbs']['skin'] = $skin ? $skin : $d['bbs']['skin'];

if ($g['mobile']&&$_SESSION['pcmode']!='Y')
{
	//$d['bbs']['skin'] = $d['bbs']['m_skin'] ? $d['bbs']['m_skin'] : $d['bbs']['skin_mobile'];
	$d['bbs']['skin'] = $d['bbs']['m_skin'] ? $d['bbs']['m_skin'] : ($d['bbs']['skin_mobile']?$d['bbs']['skin_mobile']:$d['bbs']['skin_main']);

}

include_once $g['path_module'].$m.'/theme/'.$d['bbs']['skin'].'/_var.php';

if ($c) $g['bbs_reset']	= getLinkFilter($g['s'].'/?'.($_HS['usescode']?'r='.$r.'&amp;':'').'c='.$c,array($skin?'skin':'',$iframe?'iframe':'',$cat?'cat':''));
else $g['bbs_reset']	= getLinkFilter($g['s'].'/?'.($_HS['usescode']?'r='.$r.'&amp;':'').'m='.$m,array($bid?'bid':'',$skin?'skin':'',$iframe?'iframe':'',$cat?'cat':''));
$g['bbs_list']	= $g['bbs_reset'].getLinkFilter('',array($p>1?'p':'',$sort!='gid'?'sort':'',$orderby!='asc'?'orderby':'',$recnum!=$d['bbs']['recnum']?'recnum':'',$type?'type':'',$where?'where':'',$keyword?'keyword':''));
$g['pagelink']	= $g['bbs_list'];
$g['bbs_orign'] = $g['bbs_reset'];
$g['bbs_view']	= $g['bbs_list'].'&amp;uid=';
$g['bbs_write'] = $g['bbs_list'].'&amp;mod=write';
$g['bbs_modify']= $g['bbs_write'].'&amp;uid=';
$g['bbs_reply']	= $g['bbs_write'].'&amp;reply=Y&amp;uid=';
$g['bbs_action']= $g['bbs_list'].'&amp;a=';
$g['bbs_delete']= $g['bbs_action'].'delete&amp;uid=';
$g['bbs_print'] = $g['bbs_reset'].'&amp;iframe=Y&amp;print=Y&amp;uid=';

if ($_HS['rewrite'] && $sort == 'gid' && $orderby == 'asc' && $recnum == $d['bbs']['recnum'] && $p==1 && $bid && !$cat && !$skin && !$type && !$iframe)
{
	$g['bbs_reset']= $g['r'].'/b/'.$bid;
	$g['bbs_list'] = $g['bbs_reset'];
	$g['bbs_view'] = $g['bbs_list'].'/';
	$g['bbs_write']= $g['bbs_list'].'/write';
}

$g['dir_module_skin'] = $g['dir_module'].'theme/'.$d['bbs']['skin'].'/';
$g['url_module_skin'] = $g['url_module'].'/theme/'.$d['bbs']['skin'];
$g['img_module_skin'] = $g['url_module_skin'].'/image';

$g['dir_module_mode'] = $g['dir_module_skin'].$mod;
$g['url_module_mode'] = $g['url_module_skin'].'/'.$mod;

if($_m != $g['sys_module']&&!$_HM['uid']) $g['location'] .= ' &gt; <a href="'.$g['bbs_reset'].'">'.($B['uid']?$B['name']:'전체게시물').'</a>';

if($d['bbs']['sosokmenu'])
{
	$_CA = explode('/',$d['bbs']['sosokmenu']);
	$g['location'] = '<a href="'.RW(0).'">HOME</a>';
	$_tmp['count'] = count($_CA);
	$_tmp['split_id'] = '';
	for ($_i = 0; $_i < $_tmp['count']; $_i++)
	{
		$_tmp['location'] = getDbData($table['s_menu'],"id='".$_CA[$_i]."'",'*');
		$_tmp['split_id'].= ($_i?'/':'').$_tmp['location']['id'];
		$g['location']   .= ' &gt; <a href="'.RW('c='.$_tmp['split_id']).'">'.$_tmp['location']['name'].'</a>';
		$_HM['uid'] = $_tmp['location']['uid'];
	}
}

$g['main'] = $g['main'] ? $g['main'] : $g['dir_module_mode'].'.php';
?>