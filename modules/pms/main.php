<?php
if(!defined('__KIMS__')) exit;
include_once $g['dir_module'].'var/var.php';

$mod = $mod ? $mod : 'shop';
if (strstr('[uploader][address][company][coupon][orderinfo][tax]',$mod))
{
	$type = $type ? $type : 'main';
	$iframe = 'Y';
	$g['dir_module_skin'] = $g['dir_module'].'lang.'.$_HS['lang'].'/'.$mod.'/';
	$g['url_module_skin'] = $g['url_module'].'/lang.'.$_HS['lang'].'/'.$mod;
	$g['img_module_skin'] = $g['url_module_skin'].'/image';
	$g['dir_module_mode'] = $g['dir_module_skin'].$type;
	$g['url_module_mode'] = $g['url_module_skin'].'/'.$type;
	$g['main'] = $g['dir_module_mode'].'.php';
}
else {
      include_once $g['dir_module'].'_main.php';
	$g['cart_file'] = $g['dir_module'].'tmp/cart/'.$_SESSION['cartid'].'.txt';
	$d['shop']['skin'] = $d['shop']['skin_main'];
	if($d['shop']['layout']) $_HM['layout'] = $d['shop']['layout'];
	if ($uid && !$product)
	{
		$R=getUidData($table[$m.'product'],$uid);
		if (!$R['uid']||($R['display']>1&&!$my['admin'])) getLink('','','존재하지 않는 상품입니다.','-1');

            // 테마 지정 추가
		$C = getUidData($table[$m.'category'],$R['category']);
		if($skin) $d['shop']['skin'] = $skin;
		if ($g['mobile']&&$_SESSION['pcmode']!='Y')
		{
			if ($d['shop']['skin_mobile']!='none')
			{
				if ($C['uid']) $d['shop']['skin'] = $C['skin_mobile'] ? $C['skin_mobile'] : $d['shop']['skin_mobile'];
				else $d['shop']['skin'] = $d['shop']['skin_mobile'];
			}
			if ($d['shop']['layout_m']) $prelayout = $d['shop']['layout_m'];
		}
		$d['shop']['skin']=$C['skin_mobile']?$C['skin_mobile']:$d['shop']['skin_mobile']; 
		$isPumjeol=getPumjeol($R);
		$mod = 'view';
	}
	switch ($mod)
	{
		case 'shop' : 
			$_SHOPTITLE = '전체상품';
			if ($_GET['cat']!='') $cat = $_GET['cat'];
			if ($_GET['brand']!='') $brand = $_GET['brand'];
			if ($_GET['maker']!='') $maker = $_GET['maker'];
			if ($cat)
			{
				$C = getUidData($table[$m.'category'],$cat);
				if (!$C['uid']||($C['reject']&&!$my['admin'])) getLink($g['s'].'/?r='.$r.'&m='.$m,'','존재하지 않는 매장입니다.','');
				if($C['skin']) $d['shop']['skin'] = $C['skin'];
				if($C['layout']) echo $_HM['layout'] = $C['layout'];
				$g['add_header_inc'] = $g['dir_module'].'var/code/'.sprintf('%05d',$cat).'.header.php';
				$g['add_footer_inc'] = $g['dir_module'].'var/code/'.sprintf('%05d',$cat).'.footer.php';
				if($C['imghead']) $g['add_header_img'] = $g['dir_module'].'var/files/'.$C['imghead'];
				if($C['imgfoot']) $g['add_footer_img'] = $g['dir_module'].'var/files/'.$C['imgfoot'];
				if($C['sosokmenu']) $d['shop']['sosokmenu'] = $C['sosokmenu'];
				if(!$_HS['titlefix']) $g['browtitle'] = $_HS['title'].' - '.strip_tags($C['name']);
				$_SHOPTITLE = $C['name'];
			}
			$sort	= $sort ? $sort : 'gid';
			$orderby= $orderby && strstr('[asc][desc]',$orderby) ? $orderby : 'desc';
			$recnum	= $recnum && $recnum < 200 ? $recnum : ($C['recnum']?$C['recnum']:20);
			$_WHERE = 'display<2';
			if ($C['uid']) $_WHERE.= ' and ('.getShopCategoryCodeToSql($table[$m.'category'],$C['uid']).')';
			if ($maker) { $_WHERE.= " and maker='".$maker."'"; $_SHOPTITLE = $maker;}
			if ($brand) { $_WHERE.= " and brand='".$brand."'"; $_SHOPTITLE = $brand;}
			if ($where && $keyword) $_WHERE .= getSearchSql($where,$keyword,$ikeyword,'or');
			$RCD = array();
			$_RCD = getDbArray($table[$m.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
			$NUM = getDbRows($table[$m.'product'],$_WHERE);
			while($_C=db_fetch_array($_RCD)) $RCD[] = $_C;
			$TPG = getTotalPage($NUM,$recnum);
		break;
		case 'event' : 
			if (!$cat) getLink($g['s'].'/?r='.$r.'&m='.$m,'','존재하지 않는 매장입니다.','');
			$C = getUidData($table[$m.'event'],$cat);
			if (!$C['uid']) getLink($g['s'].'/?r='.$r.'&m='.$m,'','존재하지 않는 매장입니다.','');
			if($C['skin']) $d['shop']['skin'] = $C['skin'];
			if($C['layout']) $_HM['layout'] = $C['layout'];
			$g['add_header_inc'] = $g['dir_module'].'var/event/code/'.sprintf('%05d',$cat).'.header.php';
			$g['add_footer_inc'] = $g['dir_module'].'var/event/code/'.sprintf('%05d',$cat).'.footer.php';
			if($C['imghead']) $g['add_header_img'] = $g['dir_module'].'var/event/files/'.$C['imghead'];
			if($C['imgfoot']) $g['add_footer_img'] = $g['dir_module'].'var/event/files/'.$C['imgfoot'];
			if($C['sosokmenu']) $d['shop']['sosokmenu'] = $C['sosokmenu'];
			if(!$_HS['titlefix']) $g['browtitle'] = $_HS['title'].' - '.strip_tags($C['name']);
			$_SHOPTITLE = $C['name'];
			$sort	= $sort ? $sort : 'gid';
			$orderby= $orderby && strstr('[asc][desc]',$orderby) ? $orderby : 'asc';
			$recnum	= $recnum && $recnum < 200 ? $recnum : 20;
			$RCD = array();
			$_C = getArrayString($C['goods']);
			if ($sort != 'gid' || $keyword)
			{
				$_WHERE = '(';
				foreach($_C['data'] as $val) $_WHERE .= 'uid='.$val.' or ';
				$_WHERE  = substr($_WHERE,0,strlen($_WHERE)-4);
				$_WHERE .= ')';
				if ($where && $keyword) $_WHERE .= getSearchSql($where,$keyword,$ikeyword,'or');
				$_RCD = getDbArray($table[$m.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
				$NUM = getDbRows($table[$m.'product'],$_WHERE);
				while($_C=db_fetch_array($_RCD)) $RCD[] = $_C;
			}
			else {
				$_skip1 = (($p-1)*$recnum);
				$_skip2 = $_skip1+$recnum;
				for ($_e = $_skip1; $_e < $_skip2; $_e++)
				{
					if(!$_C['data'][$_e]) continue;
					$RCD[] = getUidData($table[$m.'product'],$_C['data'][$_e]);
				}
				$NUM = $_C['count'];
			}
			$TPG = getTotalPage($NUM,$recnum);
			$pmod = 'shop';
		break;
		case 'review' : 
			$sort	= 'uid';
			$orderby= 'desc';
			$recnum	= '15';
			$_WHERE = 'product='.$product.' and hidden=0';
			$NUM = getDbRows($table[$m.'comment'],$_WHERE);
			$RCD = getDbArray($table[$m.'comment'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
			$TPG = getTotalPage($NUM,$recnum);
		break;
		case 'reviewall' : 
			$sort	= 'uid';
			$orderby= 'desc';
			$recnum	= '20';
			$_WHERE = 'hidden=0';
			if($myrcd&&$my['uid']) $_WHERE .= ' and mbruid='.$my['uid'];
			$NUM = getDbRows($table[$m.'comment'],$_WHERE);
			$RCD = getDbArray($table[$m.'comment'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
			$TPG = getTotalPage($NUM,$recnum);
		break;
		case 'qna' : 
			$sort	= 'uid';
			$orderby= 'desc';
			$recnum	= '15';
			$_WHERE = 'product='.$product.' and hidden=0';
			$NUM = getDbRows($table[$m.'qna'],$_WHERE);
			$RCD = getDbArray($table[$m.'qna'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
			$TPG = getTotalPage($NUM,$recnum);
		break;
		case 'qnaall' : 
			$sort	= 'uid';
			$orderby= 'desc';
			$recnum	= '20';
			$_WHERE = 'hidden=0';
			if($myrcd&&$my['uid']) $_WHERE .= ' and mbruid='.$my['uid'];
			$NUM = getDbRows($table[$m.'qna'],$_WHERE);
			$RCD = getDbArray($table[$m.'qna'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
			$TPG = getTotalPage($NUM,$recnum);
		break;
	}
	if (strstr('[myorder][mywish][mycash]',$mod))
	{
		if (!$my['uid']) getLink($g['s'].'/?r='.$r.'&m='.$m.'&mod=login&referer='.urlencode($g['s'].'/?r='.$r.'&m='.$m.'&mod='.$mod),'','','');
	}
	if($skin) $d['shop']['skin'] = $skin;
	if ($g['mobile']&&$_SESSION['pcmode']!='Y')
	{
		if ($d['shop']['skin_mobile']!='none')
		{
			if ($C['uid']) $d['shop']['skin'] = $C['skin_mobile'] ? $C['skin_mobile'] : $d['shop']['skin_mobile'];
			else $d['shop']['skin'] = $d['shop']['skin_mobile'];
		}
		if ($d['shop']['layout_m']) $prelayout = $d['shop']['layout_m'];
	}
	$pmod = $pmod ? $pmod : $mod;
	$g['shop_reset']= getLinkFilter($g['s'].'/?'.($_HS['usescode']?'r='.$r.'&amp;':'').($c?'c='.$c:'m='.$m),array($mod?'mod':'',$skin?'skin':'',$iframe?'iframe':'')).($cat!=''?'&amp;cat='.$cat:'');
	$g['shop_list']	= $g['shop_reset'].getLinkFilter('',array('p','sort','orderby','recnum','maker','brand',$type?'type':'',$where&&$keyword?'where,keyword':''));
	$g['shop_view']	= $g['shop_list'].'&amp;uid=';
	$g['pagelink']  = $g['shop_list'];
	$g['dir_module_skin'] = $g['dir_module'].'theme/'.$d['shop']['skin'].'/';
	$g['url_module_skin'] = $g['url_module'].'/theme/'.$d['shop']['skin'];
	$g['img_module_skin'] = $g['url_module_skin'].'/image';
	
	$g['dir_module_mode'] = $g['dir_module_skin'].$pmod;
	$g['url_module_mode'] = $g['url_module_skin'].'/'.$pmod;
	
	if($d['shop']['sosokmenu'])
	{
		$_CA = explode('/',$d['shop']['sosokmenu']);
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
		if($C['uid']) $g['location'] .= ' &gt; <a href="'.$g['shop_reset'].'">'.$C['name'].'</a>';
	}
	else {
		if($C['uid']) $g['location'] .= ' &gt; <a href="'.$g['shop_reset'].'">'.$C['name'].'</a>';
	}
	$g['main'] = $g['dir_module_mode'].'.php';
}
?>