<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/var.join.php';

$_mod	= $_GET['mod'];
$front	= $front? $front: 'login';


if ($g['mobile'] && $_SESSION['pcmode'] != 'Y')
{
	$_front = '_mobile/'.$front;
}
else {
	$_front = '_desktop/'.$front;
}

$page	= $page ? $page : 'main';


switch ($front)
{
	case 'join' : 

		if (!$d['member']['join_enable'])
		{
			getLink('','','죄송합니다. 지금은 회원가입을 하실 수 없습니다.','-1');
		}
		if ($g['mobile']&&$_SESSION['pcmode']!='Y')
		{
			if (!$d['member']['join_mobile'])
			{
				getLink('','','죄송합니다. 회원가입은 PC모드로 접속해야 합니다.','-1');
			}
		}
		if ($my['uid'])
		{
			getLink(RW(0),'','','');
		}

		$page = $page == 'main' ? 'step3' : $page;

		// if (!$d['member']['form_agree'])
		// {
		// 	$page = $page == 'step1' ? 'step2' : $page;
		// }
		// if (!$d['member']['form_jumin'])
		// {
		// 	$page = $page == 'step2' ? 'step3' : $page;
		// }
		$_HM['layout'] = $_HM['layout'] ? $_HM['layout'] : $d['member']['layout_join'];
	break;

	case 'login' : 

		if ($my['uid'])
		{
			getLink(RW(0),'','','');
		}
		$_HM['layout'] = $_HM['layout'] ? $_HM['layout'] : $d['member']['layout_login'];
	break;

	case 'profile' :

		if (!$my['uid'])
		{
			getLink($g['s'].'/?r='.$r.'&mod=login&referer='.urlencode(RW('mod=profile')),'','','');
		}
		$_HM['layout'] = $d['member']['layout_profile']?$d['member']['layout_profile']:$_HM['layout'];
	break;

	case 'mypage' :

		if (!$my['uid'])
		{
			getLink($g['s'].'/?r='.$r.'&mod=login&referer='.urlencode(RW('mod=mypage')),'','','');
		}
		$_HM['layout'] = $_HM['layout'] ? $_HM['layout'] : $d['member']['layout_mypage'];
	break;

}


$g['url_reset']	= $g['s'].'/?r='.$r.'&amp;'.($_mod ? 'mod='.$_mod : 'm='.$m.'&amp;front='.$front).($iframe?'&amp;iframe='.$iframe:'');
$g['url_page']	= $g['url_reset'].'&amp;page='.$page;

$g['dir_module_skin'] = $g['dir_module'].'pages/'.$_front.'/';
$g['url_module_skin'] = $g['url_module'].'/pages/'.$_front;
$g['img_module_skin'] = $g['url_module_skin'].'/image';

$g['dir_module_mode'] = $g['dir_module_skin'].$page;
$g['url_module_mode'] = $g['url_module_skin'].'/'.$page;

if($d['member']['sosokmenu'])
{
	$_CA = explode('/',$d['member']['sosokmenu']);
	$g['location'] = '<a href="'.RW(0).'">HOME</a>';
	$_tmp['count'] = count($_CA);
	$_tmp['split_id'] = '';
	for ($_i = 0; $_i < $_tmp['count']; $_i++)
	{
		$_tmp['location'] = getDbData($table['s_menu'],"id='".$_CA[$_i]."'",'*');
		$_tmp['split_id'].= ($_i?'/':'').$_tmp['location']['id'];
		$g['location']   .= ' &gt; <a href="'.RW('c='.$_tmp['split_id']).'">'.$_tmp['location']['name'].'</a>';
	}
	$g['location']   .= ' &gt; <a href="'.RW('mod='.$_HP['id']).'">'.$_HP['name'].'</a>';
}

$g['main'] = $g['dir_module_mode'].'.php';

?>