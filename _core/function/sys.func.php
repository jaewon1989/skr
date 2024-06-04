<?php
//TIME얻기
function getCurrentDate()
{
	$MicroTsmp = explode(' ',microtime());
	return $MicroTsmp[0]+$MicroTsmp[1];
}
//링크
function getLink($url,$target,$alert,$history)
{
	include_once $GLOBALS['g']['path_core'].'function/lib/getLink.lib.php';
}
//윈도우오픈
function getWindow($url,$alert,$option,$backurl,$target)
{
	include_once $GLOBALS['g']['path_core'].'function/lib/getWindow.lib.php';
}
//검색sql
function getSearchSql($w,$k,$ik,$h)
{
	include_once $GLOBALS['g']['path_core'].'function/lib/searchsql.lib.php';
	return LIB_getSearchSql($w,$k,$ik,$h);
}
//페이징
function getPageLink($lnum,$p,$tpage,$img)
{
	include_once $GLOBALS['g']['path_core'].'function/lib/page.lib.php';
	return LIB_getPageLink($lnum,$p,$tpage,$img);
}
//문자열끊기
function getStrCut($long_str,$cutting_len,$cutting_str)
{
	$rtn = array();$long_str = trim($long_str);
    return preg_match('/.{'.$cutting_len.'}/su', $long_str, $rtn) ? $rtn[0].$cutting_str : $long_str;
}
//링크필터링
function getLinkFilter($default,$arr)
{
	foreach($arr as $val) if ($GLOBALS[$val]) $default .= '&amp;'.$val.'='.urlencode($GLOBALS[$val]);
	return $default;
}
//총페이지수
function getTotalPage($num,$rec)
{
	return @intval(($num-1)/$rec)+1;
}
//날짜포맷
function getDateFormat($d,$f)
{
	return $d ? getDateCal($f,$d,0) : '';
}
//시간조정/포맷
function getDateCal($f,$d,$h)
{
	return date($f,mktime((int)substr($d,8,2)+$h,(int)substr($d,10,2),(int)substr($d,12,2),substr($d,4,2),substr($d,6,2),substr($d,0,4)));
}
//시간값
function getVDate($t)
{
	$date['PROC']	= $t ? getDateCal('YmdHisw',date('YmdHis'),$t) : date('YmdHisw');
	$date['totime'] = substr($date['PROC'],0,14);
	$date['year']	= substr($date['PROC'],0,4);
	$date['month']	= substr($date['PROC'],0,6);
	$date['today']  = substr($date['PROC'],0,8);
	$date['nhour']  = substr($date['PROC'],0,10);
	$date['tohour'] = substr($date['PROC'],8,6);
	$date['toweek'] = substr($date['PROC'],14,1);
	return $date;
}
//남은날짜
function getRemainDate($d)
{
	if(!$d) return 0;
	return ((substr($d,0,4)-date('Y')) * 365) + (date('z',mktime(0,0,0,substr($d,4,2),substr($d,6,2),substr($d,0,4)))-date('z'));
}
//지난시간
function getOverTime($d1,$d2)
{
	if (!$d2) return array(0);
	$d1 = date('U',mktime(substr($d1,8,2),substr($d1,10,2),substr($d1,12,2),substr($d1,4,2),substr($d1,6,2),substr($d1,0,4)));
	$d2 = date('U',mktime(substr($d2,8,2),substr($d2,10,2),substr($d2,12,2),substr($d2,4,2),substr($d2,6,2),substr($d2,0,4)));
	$tx = $d1-$d2;$ar = array(1,60,3600,86400,2592000,31104000);
	for ($i = 0; $i < 5; $i++) if ($tx < $ar[$i+1]) return array((int)($tx/$ar[$i]),$i);
	return array(substr($d1,0,4)-substr($d2,0,4),5);
}
//요일
function getWeekday($n)
{
	return $GLOBALS['lang']['admin']['week'][$n];
}
//시간비교
function getNew($time,$term)
{
	if(!$time) return false;
	$dtime = date('YmdHis',mktime(substr($time,8,2)+$term,substr($time,10,2),substr($time,12,2),substr($time,4,2),substr($time,6,2),substr($time,0,4)));
	if ($dtime > $GLOBALS['date']['totime']) return true;
	else return false;
}
//퍼센트
function getPercent($a,$b,$flag)
{
	return round($a / $b * 100 , $flag);
}
//지정문자열필터링
function filterstr($str)
{
	$str = str_replace(',','',$str);
	$str = str_replace('.','',$str);
	$str = str_replace('-','',$str);
	$str = str_replace(':','',$str);
	$str = str_replace(' ','',$str);
	return $str;
}
//문자열복사
function strCopy($str1,$str2)
{
	$badstrlen = getUTFtoUTF($str1) == $str1 ? strlen($str1) : intval(strlen($str1)/3);
	return str_pad('',($badstrlen?$badstrlen:1),$str2);
}
//아웃풋
function getContents($str,$html)
{
	include_once $GLOBALS['g']['path_core'].'function/lib/getContent.lib.php';
	return LIB_getContents($str,$html);
}
//쿠키배열
function getArrayCookie($ck,$split,$n)
{
	$arr = explode($split,$ck);
	return $arr[$n];
}
//대괄호배열
function getArrayString($str)
{
	$arr1 = array();
	$arr1['data'] = array();
	$arr2 = explode('[',$str);
	foreach($arr2 as $val)
	{
		if($val=='') continue;
		$arr1['data'][] = str_replace(']','',$val);
	}
	$arr1['count'] = count($arr1['data']);
	return $arr1;
}
//성별
function getSex($flag)
{
	return $GLOBALS['lang']['admin']['sex'][$flag-1];
}
//생일->나이
function getAge($birth)
{
	if (!$birth) return 0;
	return substr($GLOBALS['date']['today'],0,4) - substr($birth,0,4) + 1;
}
//나이->출생년도
function getAgeToYear($age)
{
	return substr($GLOBALS['date']['today'],0,4)-($age-1);
}
//사이즈포멧
function getSizeFormat($size,$flag)
{
	if ($size/(1024*1024*1024)>1) return round($size/(1024*1024*1024),$flag).'GB';
	if ($size/(1024*1024)>1) return round($size/(1024*1024),$flag).'MB';
	if ($size/1024>1) return round($size/1024,$flag).'KB';
	if ($size/1024<1) return $size.'B';
}
//파일타입
function getFileType($ext)
{
	if (strpos('_gif,jpg,jpeg,png,bmp,',strtolower($ext))) return 2;
	if (strpos('_swf,',strtolower($ext))) return 3;
	if (strpos('_mid,wav,mp3,',strtolower($ext))) return 4;
	if (strpos('_mp4,asf,asx,avi,mpg,mpeg,wmv,wma,mov,flv,',strtolower($ext))) return 5;
	if (strpos('_doc,xls,ppt,hwp,docx,xlsx,pptx,pdf,',strtolower($ext))) return 6;
	if (strpos('_zip,tar,gz,tgz,alz,',strtolower($ext))) return 7;
	return 1;
}
//파일확장자
function getExt($name)
{
	$nx=explode('.',$name);
	return $nx[count($nx)-1];
}
//이미지추출
function getImgs($code,$type)
{
	$erg = '/src[ =]+[\'"]([^\'"]+\.(?:'.$type.'))[\'"]/i';
	preg_match_all($erg, $code, $mtc, PREG_PATTERN_ORDER);
	return $mtc[1];
}
//이미지체크
function getThumbImg($img)
{
	$arr=array('.jpg','.gif','.png');
	foreach($arr as $val) if(is_file($img.$val)) return $GLOBALS['g']['s'].'/'.str_replace('./','',$img).$val;
}
function getUploadImage($upfiles,$d,$content,$ext)
{
	include_once $GLOBALS['g']['path_core'].'function/lib/getUploadImage.lib.php';
	return LIB_getUploadImage($upfiles,$d,$content,$ext);
}
//도메인
function getDomain($url)
{
	$urlexp = explode('/',$url);
	return $urlexp[2];
}
//엔터티
function getKeyword($url)
{
	$urlexp = explode('?' , urldecode($url));
	if (!trim($urlexp[1])) return '';
	$queexp = explode('&' , $urlexp[1]);
	$quenum = count($queexp);
	for ($i = 0; $i < $quenum; $i++){$valexp = explode('=',trim($queexp[$i])); if (strstr(',query,q,p,',','.$valexp[0].',')&&!is_numeric($valexp[1])) return $valexp[1] == getUTFtoUTF($valexp[1]) ? $valexp[1] : getKRtoUTF($valexp[1]);}
	return '';
}
//검색엔진
function getSearchEngine($url)
{
	$set = array('naver','nate','daum','yahoo','google');
	foreach($set as $val) if (strpos($url,$val)) return $val;
	return 'etc';
}
//브라우져
function getBrowzer($agent)
{
	if(isMobileConnect($agent)) return 'Mobile';
	$set = array('rv:12','rv:11','MSIE 10','MSIE 9','MSIE 8','MSIE 7','MSIE 6','Firefox','Opera','Chrome','Safari');
	foreach($set as $val) if (strpos('_'.$agent,$val)) return str_replace('rv:','MSIE ',$val);
	return '';
}
//디바이스종류
function getDeviceKind($agent,$type)
{
	if (!$type) return 'desktop';
	if ($type == 'ipad' || (strstr($agent,'android')&&!strstr($agent,'mobile'))) return 'tablet';
	return 'phone';
}
//모바일접속체크
function isMobileConnect($agent)
{
	if($_SESSION['pcmode']=='E') return 'RB-Emulator';
	$_xagent = strtolower($agent);
	$_agents = array('android','iphone','ipad','ipod','blackberry','windows phone');
	foreach($_agents as $_key) if(strpos($_xagent,$_key)) return $_key;
	return '';
}
//폴더네임얻기
function getFolderName($file)
{
	if(is_file($file.'/name.txt')) return implode('',file($file.'/name.txt'));
	return basename($file);
}
function getKRtoUTF($str)
{
	return iconv('euc-kr','utf-8',$str);
}
function getUTFtoKR($str)
{
	return iconv('utf-8','euc-kr',$str);
}
function getUTFtoUTF($str)
{
	return iconv('utf-8','utf-8',$str);
}
//관리자체크
function checkAdmin($n)
{
	if(!$GLOBALS['my']['admin']) getLink('','',_LANG('fs001','admin'),$n?$n:'');
}
//MOD_rewrite
function RW($rewrite)
{
	if ($GLOBALS['_HS']['rewrite'])
	{
		if(!$rewrite) return $GLOBALS['g']['r']?$GLOBALS['g']['r']:'/';
		$rewrite = str_replace('c=','c/',$rewrite);
		$rewrite = str_replace('mod=','p/',$rewrite);
		$rewrite = str_replace('m=admin','admin',$rewrite);
		return $GLOBALS['g']['r'].'/'.$rewrite;
	}
	else return $GLOBALS['_HS']['usescode']?('./?r='.$GLOBALS['_HS']['id'].($rewrite?'&amp;'.$rewrite:'')):'./'.($rewrite?'?'.$rewrite:'');
}
//위젯불러오기
function getWidget($widget,$wdgvar)
{
	global $DB_CONNECT,$table,$date,$my,$r,$s,$m,$g,$d,$c,$mod,$_HH,$_HD,$_HS,$_HM,$_HP,$_CA;
	static $wcsswjsc;
	if (!is_file($g['wdgcod']) && !strpos('_'.$wcsswjsc,'['.$widget.']'))
	{
		$wcss = $g['path_widget'].$widget.'/main.css';
		$wjsc = $g['path_widget'].$widget.'/main.js';
		if (is_file($wcss)) $g['widget_cssjs'] .= '<link href="'.$g['s'].'/widgets/'.$widget.'/main.css" rel="stylesheet">'."\n";
		if (is_file($wjsc)) $g['widget_cssjs'] .= '<script src="'.$g['s'].'/widgets/'.$widget.'/main.js"></script>'."\n";
		$wcsswjsc.='['.$widget.']';
	}
	$wdgvar['widget_id'] = str_replace('/','-',$widget);
	$wdgvar['widgetlang'] = $_HS['lang']?$_HS['lang']:$d['admin']['syslang'];
	include getLangFile($g['path_widget'].$widget.'/lang.',$wdgvar['widgetlang'],'.php');
	include $g['path_widget'].$widget.'/main.php';
}
//문자열필터(@ 1.1.0)
function getStripTags($string)
{
	return str_replace('&nbsp;',' ',str_replace('&amp;nbsp;',' ',strip_tags($string)));
}
//스위치로드(@ 1.1.0)
function getSwitchInc($pos)
{
	$incs = array();
	if(isset($GLOBALS['d']['switch'][$pos]))
	{
		foreach ($GLOBALS['d']['switch'][$pos] as $switch => $sites)
		{
			if(strpos('_'.$sites,'['.$GLOBALS['r'].']'))
			$incs[] = $GLOBALS['g']['path_switch'].$pos.'/'.$switch.'/main.php';
		}
	}
	return $incs;
}
//알림기록(@ 2.0.0)
function putNotice($rcvmember,$sendmodule,$sendmember,$message,$referer,$target)
{
	global $g,$d,$s,$table,$date,$my,$_HS;
	include $g['path_module'].'notification/var/var.php';
	if ($rcvmember && $message && !strstr($d['ntfc']['cut_modules'],'['.$sendmodule.']'))
	{
		$R=getDbData($table['s_mbrdata'],'memberuid='.$rcvmember,'noticeconf');
		$N = explode('|',$R['noticeconf']);
		if (!$N[0] && !strstr($N[3],'['.$sendmodule.']') && !strstr($N[4],'['.$sendmember.']'))
		{
			$message = $my['admin'] ? $message : strip_tags($message);
			$QKEY = 'uid,mbruid,site,frommodule,frommbr,message,referer,target,d_regis,d_read';
			$QVAL = "'".$g['time_srnad']."','".$rcvmember."','".$s."','".$sendmodule."','".$sendmember."','".$message."','".$referer."','".$target."','".$date['totime']."',''";
			getDbInsert($table['s_notice'],$QKEY,$QVAL);
			getDbUpdate($table['s_mbrdata'],'num_notice='.getDbRows($table['s_notice'],'mbruid='.$rcvmember." and d_read=''"),'memberuid='.$rcvmember);
			if ($N[5])
			{
				include_once $g['path_core'].'function/email.func.php';
				$M = getDbData($table['s_mbrdata'],'memberuid='.$rcvmember,'name,email');
				getSendMail($M['email'].'|'.$M['name'],$my['email'].'|'.$my['nic'],'['.$_HS['name'].'] 새 알림이 도착했습니다.',$message,'HTML');
			}
		}
	}
}
//모달링크(@ 2.0.0)
function getModalLink($modal)
{
	global $g,$r;
	return $g['s'].'/?r='.$r.'&amp;iframe=Y&amp;modal='.$modal;
}
//JS/CSS임포트(@ 2.0.0)
function getImport($plugin,$path,$version,$kind)
{
	global $g,$d;
	if ($kind == 'js') echo '<script src="'.$g['s'].'/plugins/'.$plugin.'/'.($version?$version:$d['ov'][$plugin]).'/'.$path.'.js"></script>';
	else echo '<link href="'.$g['s'].'/plugins/'.$plugin.'/'.($version?$version:$d['ov'][$plugin]).'/'.$path.'.css" rel="stylesheet">';
}
//썸네일(@ 2.0.0)
function getThumbPic($width,$height,$crop,$img)
{
	global $g;
	return $g['s'].'/_core/opensrc/thumb/image.php?width='.($width?$width:'').'&amp;height='.($height?$height:'').'&amp;cropratio='.$crop.'&amp;image='.$img;
}
//트리(@ 2.0.0)
function getTreeMenu($conf,$code,$depth,$parent,$tmpcode)
{
	$ctype = $conf['ctype']?$conf['ctype']:'uid';
	$id = 'tree_'.filterstr(microtime());
	$tree = '<div class="rb-tree"><ul id="'.$id.'">';
	$CD=getDbSelect($conf['table'],($conf['site']?'site='.$conf['site'].' and ':'').'depth='.($depth+1).' and parent='.$parent.($conf['dispHidden']?' and hidden=0':'').($conf['mobile']?' and mobile=1':'').' order by gid asc','*');
	$_i = 0;
	while($C=db_fetch_array($CD))
	{
		$rcode= $tmpcode?$tmpcode.'/'.$C[$ctype]:$C[$ctype];
		$t_arr = explode('/', $code);
		$t1_arr = explode('/', $rcode);
		$topen= in_array($t1_arr[count($t1_arr)-1], $t_arr)?true:false;

		$tree.= '<li>';
		if ($C['is_child'])
		{
			$tree.= '<a data-toggle="collapse" href="#'.$id.'-'.$_i.'-'.$C['uid'].'" class="rb-branch'.($conf['allOpen']||$topen?'':' collapsed').'"></a>';
			if ($conf['userMenu']=='link') $tree.= '<a href="'.RW('c='.$rcode).'"><span'.($code==$rcode?' class="rb-active"':'').'>';
			else if($conf['userMenu']=='bookmark') $tree.= '<a data-scroll href="#rb-tree-menu-'.$C['id'].'"><span'.($code==$rcode?' class="rb-active"':'').'>';
			else $tree.= '<a href="'.$conf['link'].$C['uid'].'&amp;code='.$rcode.($conf['bookmark']?'#'.$conf['bookmark']:'').'"><span'.($code==$rcode?' class="rb-active"':'').'>';
			if($conf['dispCheckbox']) $tree.= '<input type="checkbox" name="tree_members[]" value="'.$C['uid'].'">';
			if($C['hidden']) $tree.='<u title="'._LANG('fs002','admin').'" data-tooltip="tooltip">';
			$tree.= $C['name'];
			if($C['hidden']) $tree.='</span>';
			$tree.='</u></a>';

			if($conf['dispNum']&&$C['num']) $tree.= ' <small>('.$C['num'].')</small>';
			if(!$conf['hideIcon'])
			{
				//if($C['mobile']) $tree.= '<i class="glyphicon glyphicon-phone" title="'._LANG('fs005','admin').'" data-tooltip="tooltip"></i>&nbsp;';
				if($C['target']) $tree.= '<i class="glyphicon glyphicon-new-window" title="'._LANG('fs004','admin').'" data-tooltip="tooltip"></i>&nbsp;';
				if($C['reject']) $tree.= '<i class="glyphicon glyphicon-ban-circle" title="'._LANG('fs003','admin').'" data-tooltip="tooltip"></i>';
			}

			$tree.= '<ul id="'.$id.'-'.$_i.'-'.$C['uid'].'" class="collapse'.($conf['allOpen']||$topen?' in':'').'">';
			$tree.= getTreeMenu($conf,$code,$C['depth'],$C['uid'],$rcode);
			$tree.= '</ul>';
		}
		else {
			$tree.= '<a href="#." class="rb-leaf"></a>';
			if ($conf['userMenu']=='link') $tree.= '<a href="'.RW('c='.$rcode).'"><span'.($code==$rcode?' class="rb-active"':'').'>';
			else if ($conf['userMenu']=='bookmark') $tree.= '<a data-scroll href="#rb-tree-menu'.$C['id'].'"><span'.($code==$rcode?' class="rb-active"':'').'>';
			else $tree.= '<a href="'.$conf['link'].$C['uid'].'&amp;code='.$rcode.($conf['bookmark']?'#'.$conf['bookmark']:'').'"><span'.($code==$rcode?' class="rb-active"':'').'>';
			if($conf['dispCheckbox']) $tree.= '<input type="checkbox" name="tree_members[]" value="'.$C['uid'].'">';
			if($C['hidden']) $tree.='<u title="'._LANG('fs002','admin').'" data-tooltip="tooltip">';
			$tree.= $C['name'];
			if($C['hidden']) $tree.='</u>';
			$tree.='</span></a>';

			if($conf['dispNum']&&$C['num']) $tree.= ' <small>('.$C['num'].')</small>';
			if(!$conf['hideIcon'])
			{
				//if($C['mobile']) $tree.= '<i class="glyphicon glyphicon-phone" title="'._LANG('fs005','admin').'" data-tooltip="tooltip"></i>&nbsp;';
				if($C['target']) $tree.= '<i class="glyphicon glyphicon-new-window" title="'._LANG('fs004','admin').'" data-tooltip="tooltip"></i>&nbsp;';
				if($C['reject']) $tree.= '<i class="glyphicon glyphicon-ban-circle" title="'._LANG('fs003','admin').'" data-tooltip="tooltip"></i>';
			}
		}
		$tree.= '</li>';
		$_i++;
	}
	$tree.= '</ul></div>';
	return $tree;
}
//현재경로(@ 2.0.0)
function getLocation($loc)
{
	if ($loc) return str_replace(' - Home - ','',strip_tags(str_replace('<li',' - <li',$loc)));
	else {
		global $g,$table,$_HS,$_HP,$_HM,$_CA,$c;
		$_loc = '<li><a href="'.RW(0).'">Home</a></li>';
		if ($_HM['uid'])
		{
			$_cnt = count($_CA)-1;
			$_cod = '';
			for ($i = 0; $i < $_cnt; $i++)
			{
				$_val  = getDbData($table['s_menu'],"id='".$_CA[$i]."'",'id,name');
				$_cod .= $_val['id'].'/';
				$_loc .= '<li><a href="'.RW('c='.substr($_cod,0,strlen($_cod)-1)).'">'.$_val['name'].'</a></li>';
			}
			$_loc .= '<li class="active">'.$_HM['name'].'</li>';
		}
		else if ($_HP['uid'])
		{
			if ($_HP['linkedmenu'])
			{
				$_sok = explode('/',$_HP['linkedmenu']);
				$_cnt = count($_sok);
				$_cod = '';
				for ($i = 0; $i < $_cnt; $i++)
				{
					$_val  = getDbData($table['s_menu'],"id='".$_CA[$i]."'",'id,name');
					$_cod .= $_val['id'].'/';
					$_loc .= '<li><a href="'.RW('c='.substr($_cod,0,strlen($_cod)-1)).'">'.$_val['name'].'</a></li>';
				}
			}
			$_loc .= '<li class="active">'.$_HP['name'].'</li>';
		}
		else if ($g['push_location'])
		{
			$_loc .= $g['push_location'];
		}
		return $_loc;
	}
}
//페이지타이틀(@ 2.0.0)
function getPageTitile()
{
	global $g,$_HS,$_HP,$_HM;
	$title = str_replace('{site}',$_HS['name'],$_HS['title']);
	$title = str_replace('{location}',getLocation($g['location']),$title);
	if ($_HM['uid']) $title = str_replace('{subject}',$_HM['name'],$title);
	else if ($_HP['uid']) $title = str_replace('{subject}',$_HP['name'],$title);
	else $title = $_HS['name'];
	return $title;
}
//메타이미지(@ 2.0.0)
function getMetaImage($str)
{
	if (!$str) return '';
	if (strstr($str,'://'))	return $str;
	$imgs = getArrayString($str);
	$R = getUidData($GLOBALS['table']['s_upload'],$imgs['data'][0]);
	if ($R['type'] == 2 || $R['type'] == 5) return $R['url'].$R['folder'].'/'.$R['tmpname'];
	if ($R['type'] == -1) return $R['src'];
	return '';
}
//암호화(@ 2.0.0)
function getCrypt($str,$salt)
{
	$salt = substr(base64_encode($salt.'salt'),0,22);
	$ver0 = implode('',file($GLOBALS['g']['path_var'].'php.version.txt'));
	$ver1 = explode('.',$ver0);
	if ($ver1[0] > 5 || ($ver1[0] > 4 && $ver1[1] > 4))
	if(function_exists('password_hash')) return password_hash($str,PASSWORD_BCRYPT,array('cost'=>10,'salt'=>$salt)).'$1';
	if ($ver1[0] > 4 || ($ver1[0] > 3 && $ver1[1] > 1) || ($ver1[0] > 3 && $ver1[1] > 0 && $ver1[2] > 1))
	{
		if (in_array('sha512',hash_algos())) return hash('sha512',$str.$salt).'$2';
		else if (in_array('sha256',hash_algos())) return hash('sha256',$str.$salt).'$3';
	}
	return md5(sha1(md5($str.$salt))).'$4';
}

function getCryptByCCaaS($str, $salt)
{
    $salt = substr(hash('sha256', $salt . 'salt'), 0, 22);

    if (in_array('sha512', hash_algos(), true)) {
        $hash = hash('sha512', $str . $salt);
        return $hash . '$1';
    }

    if (in_array('sha256', hash_algos(), true)) {
        $hash = hash('sha256', $str . $salt);
        return $hash . '$2';
    }

    return md5(sha1(md5($str . $salt))) . '$3';
}

//언언반환(@ 2.0.0)
function _LANG($kind,$module)
{
	return $GLOBALS['lang'][$module][$kind];
}
function _LANG_($kind,$module,$defaultstr)
{
	return $GLOBALS['lang'][$module][$kind] ? $GLOBALS['lang'][$module][$kind] : $defaultstr;
}
//언언셋인클루드(@ 2.0.0)
function getLangFile($path,$lang,$file)
{
	$langFile1 = $path.$lang.$file;
	$langFile2 = $path.'DEFAULT'.$file;
	if (is_file($langFile1)) return $langFile1;
	else if(is_file($langFile2)) return $langFile2;
	else return $GLOBALS['g']['path_var'].'empty.php';
}

// html to markdown class 호출 함수
function getBotManClass($className)
{
      global $g;

      $path=$g['path_core'].'opensrc/BotMan-master/src/';

      $className = ltrim($className, '\\');
      $fileName  = '';
      $namespace = '';
      if ($lastNsPos = strrpos($className, '\\')) {
          $namespace = substr($className, 0, $lastNsPos);
          $className = substr($className, $lastNsPos + 1);
          $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
      }
      $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

      require_once $path.$fileName;
}

// TimThumb 이미지 출력함수
function getTimThumb($data=array())
{
	global $g;
	$img_src=$data['src'];
	$w=$data['width'];
	$h=$data['height'];
	$q=$data['qulity'];
 	$f=$data['filter'];
	$a=$data['align'];
	$t=$data['type'];
	$s=$data['style'];
	$open_src = $g['url_root'].'/_core/opensrc/timthumb/thumb.php';
    $img_qry = $open_src.'?src='.$img_src;
    $img_qry .=($w?'&w='.$w:'').($h?'&h='.$h:'').($q?'&q='.$q:'').($f?'&f='.$f:'').($a?'&a='.$a:'').($t?'&t='.$t:'').($s?'&s='.$s:'');

    if($img_src) $result=$img_qry;
    else $result='';

    return $result;
}

function getMIMEType($chFileName) {
    if (function_exists("finfo_file")) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $mime = finfo_file($finfo, $chFileName);
        finfo_close($finfo);
        return $mime;
    } else if (function_exists("mime_content_type")) {
        return mime_content_type($chFileName);
    } else {
        $file = escapeshellarg($chFileName);
        $mime = shell_exec("file -b --mime-type " . $chFileName);
        return $mime;
    }
}

function getUploadFileCheck($chFile, $chFileType='image', $nFileSize=(2*1024*1024)) {
    $ext = strtolower(preg_replace('/^.*\.([^.]+)$/D', '$1', $chFile['name']));
    $aExt = array("phtml", "shtml", "html", "htm", "php", "sphp", "inc", "lib", "asp", "jsp", "exe", "bat", "scr", "js");
    if(!$chFile['name'] || in_array($ext, $aExt)) {
        return '업로드할 수 없는 파일입니다.';
    } else {
        if($chFileType == 'image') {
            $aFileType = array('jpeg', 'jpg', 'gif', 'png');
            $msg = '이미지 포맷(JPG, GIF, PNG)만 등록가능합니다.';
        } else if($chFileType == 'excel') {
            $aFileType = array('xls', 'xlsx');
            $msg = '엑셀 파일만 등록가능합니다.';
        }
        if(!in_array($ext, $aFileType)) {
            return $msg;
        } else if(filesize($chFile['tmp_name']) > $nFileSize) {
            return '업로드 파일의 용량은 2M 이하여야 합니다.';
        } else {
            return true;
        }
    }
}

function getStripScript($data) {
    if (is_array($data)) {
        return array_map(__METHOD__,$data);
    } else {
        return preg_replace("/<script\b[^>]*>(.*?)<\/script>/is", "", trim($data));
    }
}
function getEscString($data) {
    if (is_array($data)) {
        return array_map(__METHOD__,$data);
    } else {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}
function getEsc($data, $bStripTag=true) {
    if (is_array($data)) {
        return array_map(__METHOD__,$data);
    } else {
        return htmlspecialchars($bStripTag ? strip_tags(trim($data)) : trim($data), ENT_QUOTES);
    }
}
function getUnesc($data) {
    if (is_array($data)) {
        return array_map(__METHOD__,$data);
    } else {
        return htmlspecialchars_decode($data, ENT_QUOTES);
    }
}

// 아이디 체크(영문,숫자)
function getValidID($chStr, $nMin=4, $nMax=20) {
    if (!preg_match("/^[a-z0-9]{".$nMin.",".$nMax."}$/i", trim($chStr))) {
        return "아이디는 ".$nMin."~".$nMax."자의 영문, 숫자로 입력해주세요.";
    }
    if (preg_match("/\s+/", $chStr)) {
        return "공백 없이 입력해주세요.";
    }
    $aID = array("www", "admin","administrator","webmaster","master","test","manager","manage", "root","super","group", "web", "mail", "ftp", "data", "ns", "demo", "nobody", "daemon", "guest", "mysql", "psql");
    if (in_array(trim($chStr), $aID)) {
        return "사용할 수 없는 아이디입니다.";
    }
    return true;
}

// 비밀번호 체크(영문,숫자,특수문자 체크)
function getValidPassword($chStr, $nMin=8, $nMax=20) {
    if (preg_match("/\s+/", $chStr)) {
        return "공백 없이 입력해주세요.";
    }
    if(!preg_match('/^.*(?=^.{'.$nMin.','.$nMax.'}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%\^\&\*\(\)\-_\+\=]).*$/', trim($chStr))) {
        return "비밀번호는 ".$nMin."~".$nMax."자의 영문, 숫자, 특수문자를 혼합하여 입력해주세요.";
    }
    return true;
}

function getDeleteDirectory($dir) {
    if(is_dir($dir)) {
        $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($it as $file) {
            if ($file->isDir()) rmdir($file->getPathname());
            else unlink($file->getPathname());
        }
        rmdir($dir);
    }
}

function getCheckNumberFormat($chStr) {
    if(!trim($chStr)) return false;
    $aResult = array();

    // 전화번호
	preg_match('/(02|0[3-8][\d]{1}|01[016789])([\d]{3,4})([\d]{4})/', str_replace('-', '', $chStr), $aMatch);
	if($aMatch[0]) {
	    //$aResult['type'] = preg_match('/^01/', $aMatch[1]) ? '휴대폰번호' : '전화번호';
	    $aResult['type'] = '전화번호';
	    $aResult['value'] = $aMatch[1].'-'.$aMatch[2].'-'.$aMatch[3];
	}

	// 주민번호
	preg_match('/([\d]{6})([1-4][\d]{6})/', str_replace('-', '', $chStr), $aMatch);
	if($aMatch[0]) {
	    $nManNum = $aMatch[0];
	    $chWeight = "234567892345";
	    $nSum = 0;
	    if (strlen($nManNum) == 13) {
	        for ($i=0; $i<12; $i++) {
	            $nSum = $nSum + (substr($nManNum, $i, 1) * substr($chWeight, $i, 1));
	        }
	        $nRst = $nSum%11;
	        $nResult = 11 - $nRst;

	        if ($nResult == 10) $nResult = 0;
	        else if ($nResult == 11) $nResult = 1;

	        $nTemp = substr($nManNum,12,1);
	        if ($nResult == $nTemp) {
	            $aResult['type'] = '주민등록번호';
	            $aResult['value'] = $aMatch[1].'-'.$aMatch[2];
	        }
	    }
	}

	// 사업자번호
	preg_match('/([\d]{3})([\d]{2})([\d]{5})/', str_replace('-', '', $chStr), $aMatch);
    if ($aMatch[0]) {
        $nBizNum = $aMatch[0];
        $chWeight = "137137135";
        $nSum = 0;
        if(strlen($nBizNum) == 10) {
            for ($i=0; $i<9; $i++) {
                $nSum = $nSum + (substr($nBizNum, $i, 1) * substr($chWeight, $i, 1));
            }
            $nSum = $nSum + ((substr($nBizNum, 8, 1) * 5)/10);
            $nRst = $nSum%10;
            $nResult = $nRst == 0 ? 0 : (10 - $nRst);
            $nTemp = substr($nBizNum, 9, 1);
            if ($nResult == $nTemp) {
                $aResult['type'] = '사업자등록번호';
	            $aResult['value'] = $aMatch[1].'-'.$aMatch[2].'-'.$aMatch[3];
            }
        }
    }
    return count($aResult) > 0 ? $aResult : false;
}

function getStrMasking($chStr) {
    // 주민번호
    $chStr = preg_replace('/((?=\D)|^|\b)(\d{2})((0[13578]|1[02])(0[1-9]|[12][0-9]|3[01])|(0[469]|11)(0[1-9]|[12][0-9]|3[0])|(02)(0[1-9]|[12][0-9]))(-|\s)?([1-8])(\d{6})((?=\D)|$|\b)/', '$2$3$10$11******', $chStr);
    // 휴대폰번호
    $chStr = preg_replace('/((?=\D)|^|\b)(01)([01689])(-|\s)?(\d{1}|\d{2})(\d{2})(-|\s)?(\d{4})((?=\D)|$|\b)/', '$2$3$4$5**$7****', $chStr);
    return $chStr;
}
function setMasking($type, $str) {
    $result = "";
    if(!empty($type)) {
        switch($type) {
            case("mobile") :
                $result = preg_replace('/((?=\D)|^|\b)(01)([01689])(-|\s)?(\d{4})(-|\s)?(\d{4})((?=\D)|$|\b)/', '$2$3$4****$6$7', $str);
            break;
            case("manno") :
                $result = preg_replace('/((?=\D)|^|\b)(\d{2})((0[13578]|1[02])(0[1-9]|[12][0-9]|3[01])|(0[469]|11)(0[1-9]|[12][0-9]|3[0])|(02)(0[1-9]|[12][0-9]))(-|\s)?([1-8])(\d{6})((?=\D)|$|\b)/', '$2$3$10$11******', $str);
            break;
            case("ip"):
                $result = preg_replace('/(\d+)([\.]\d+[\.])(\d+)([\.]\d+)/i','***\2***\4',$str);
            break;
            case("name"):
                $result = preg_replace('/.(?=.$)/u', '*', $str);
            break;
        }
    }
    return $result;
}

function getStrToPhoneFormat($chStr) {
    return preg_replace("/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/", "$1-$2-$3", str_replace("-", "", $chStr));
}
function getStrToBizNoFormat($chStr) {
    return preg_replace("/([0-9]{3})([0-9]+)([0-9]{5})/", "$1-$2-$3", str_replace("-", "", $chStr));
}

// 문자열 포맷 첵크(전화번호, 휴대폰, 우편번호...)
function getCheckValidFormat($chType, $chStr) {
    switch($chType) {
        case('alphanum') :
            $bResult = ctype_alnum($chStr); //알파벳과 숫자만인지 확인
        break;
        case('alpha') :
            $bResult = ctype_alpha($chStr); //알파벳만인지 확인
        break;
        case('digit') :
            $bResult = ctype_digit($chStr); //숫자만인지 확인
        break;
        case('phone') :
            $bResult = preg_match("/(02|0[3-8][\d]{1})([\d]{3,4})([\d]{4})/i", str_replace("-", "", $chStr));
        break;
        case('mobile') :
            $bResult = preg_match("/(01[016789])([\d]{3,4})([\d]{4})/i", str_replace("-", "", $chStr));
        break;
        case('mannum') :
            if (preg_match("/[\d]{6}-[1-4][\d]{6}/i", $chStr)) {
                $nManNum = preg_replace("/-/i", "", $chStr);
                $chWeight = "234567892345";
                $nLen =strlen($nManNum);
                $nSum = 0;
                if ($nLen == 13) {
                    for ($i=0; $i<12; $i++) $nSum = $nSum + (substr($nManNum, $i, 1) * substr($chWeight, $i, 1));

                    $nRst = $nSum%11;
                    $nResult = 11 - $nRst;
                    if ($nResult == 10) $nResult = 0;
                    else if ($nResult == 11) $nResult = 1;

                    $nTemp = substr($nManNum,12,1);
                    if ($nResult != $nTemp) $bResult = false;
                    else $bResult = true;
                } else {
                    $bResult = false;
                }
            } else {
                $bResult = false;
            }
            break;

        case('biznum') :
            if (preg_match("/[\d]{3}[\d]{2}[\d]{5}/i", str_replace("-", "", $chStr))) {
                $nBizNum = preg_replace("/-/i", "", $chStr);
                $chWeight = "137137135";
                $nLen = strlen($nBizNum);
                $nSum = 0;
                if ($nLen == 10) {
                    for ($i=0; $i<9; $i++) $nSum = $nSum + (substr($nBizNum, $i, 1) * substr($chWeight, $i, 1));
                    $nSum = $nSum + ((substr($nBizNum, 8, 1) * 5)/10);
                    $nRst = $nSum%10;
                    if ($nRst == 0) $nResult = 0;
                    else $nResult = 10 - $nRst;
                    $nTemp = substr($nBizNum, 9, 1);

                    if ($nResult != $nTemp) $bResult = false;
                    else $bResult = true;
                } else {
                    $bResult = false;
                }
            } else {
                $bResult = false;
            }
        break;

        case('date') :
            $bResult = preg_match("/([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/i", str_replace("-", "", $chStr));
        break;

        case('email') :
            $bResult = preg_match("/[\da-z\-_\.]+@([a-z\d]([a-z\d\-]*)([a-z\d]*)\.)+[a-z]{2,6}/i", $chStr);
        break;

        case('url') :
            $bResult = filter_var($chStr, FILTER_VALIDATE_URL) === false ? false : true;
        break;
    }
    return $bResult;
}

function getDatePeriodKorean($dStart, $dEnd) {
    $dStart = new DateTime($dStart);
    $dEnd = new DateTime($dEnd);
    $interval = $dEnd->diff($dStart);
    $nDays = $interval->days;
    return $nDays.'박'.($nDays+1).'일';
}

function getSearchArrayValByKey($arr,$key){
    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
    $result = iterator_to_array($iterator,true);
    return $result[$key];
}

function getSendSMS_Cafe24($chMode, $chToMobile="", $chMsg="", $chSubject="", $chSMSType="", $rDate="", $rTime="") {
    global $aSMSInfo;

    switch($chMode) {
        // 발송번호 확인
        case('get_from_number') :
            $ch = curl_init();
            $aPostData['userId'] = $aSMSInfo['id']; // SMS 아이디
            $aPostData['passwd'] = $aSMSInfo['key']; // 인증키
            curl_setopt($ch, CURLOPT_URL, $aSMSInfo['fromno_url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $aPostData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $ret = curl_exec($ch);
            curl_close($ch);
            $aResult = json_decode($ret, true);
            return $aResult;
        break;

        // 남은 잔량 확인
        case('get_remain_count') :
            $sms_url = $aSMSInfo['remain_url'];
            $sms['user_id'] = base64_encode($aSMSInfo['id']); //SMS 아이디.
            $sms['secure'] = base64_encode($aSMSInfo['key']) ;//인증키
            $sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.

            $host_info = explode("/", $sms_url);
            $host = $host_info[2];
            $path = $host_info[3]."/".$host_info[4];

            srand((double)microtime()*1000000);
            $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);

            // 헤더 생성
            $header = "POST /".$path ." HTTP/1.0\r\n";
            $header .= "Host: ".$host."\r\n";
            $header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

            // 본문 생성
            foreach($sms AS $index => $value) {
                $data .="--$boundary\r\n";
                $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
                $data .= "\r\n".$value."\r\n";
                $data .="--$boundary\r\n";
            }
            $header .= "Content-length: " . strlen($data) . "\r\n\r\n";

            $fp = fsockopen($host, 80);
            if ($fp) {
                fputs($fp, $header.$data);
                $rsp = '';
                while(!feof($fp)) {
                    $rsp .= fgets($fp,8192);
                }
                fclose($fp);
                $msg = explode("\r\n\r\n",trim($rsp));
                $nCount = $msg[1]; //잔여건수
                return $nCount;
            } else {
                return false;
            }
        break;

        // 문자 발송
        case('get_send_sms') :
            $chToMobile = trim($chToMobile);
            $chMsg = trim($chMsg);
            $chSubject = trim($chSubject);

            $chToMobile = getStrToPhoneFormat($chToMobile);

            $aResult = array();
            $aFromPhone = explode("-", $aSMSInfo['from_phone']);
            if (!$chToMobile || !$chMsg) {
                return array("bResult"=>0, "chErrCode"=>"변수부족");
            }

            $sms_url = $aSMSInfo['send_url'];
            $sms['user_id'] = base64_encode($aSMSInfo['id']); //SMS 아이디.
            $sms['secure'] = base64_encode($aSMSInfo['key']) ;//인증키
            $sms['msg'] = base64_encode(stripslashes($chMsg));
            if($chSMSType == "L"){
                $sms['subject'] =  base64_encode(stripslashes($chSubject));
            }
            $sms['rphone'] = base64_encode($chToMobile);
            $sms['sphone1'] = base64_encode($aFromPhone[0]);
            $sms['sphone2'] = base64_encode($aFromPhone[1]);
            $sms['sphone3'] = base64_encode($aFromPhone[2]);
            $sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
            $sms['testflag'] = $aSMSInfo['develop'] == 1 ? base64_encode("Y") : "";
            $sms['smsType'] = $chSMSType ? base64_encode($chSMSType) : ""; // LMS일경우 L
            if($rDate && $rTime) {
                $sms['rdate'] = base64_encode($rDate); // 20230801
                $sms['rtime'] = base64_encode($rTime); // 093000
            }

            $host_info = explode("/", $sms_url);
            $host = $host_info[2];
            $path = $host_info[3]."/".$host_info[4];

            srand((double)microtime()*1000000);
            $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);

            // 헤더 생성
            $header = "POST /".$path ." HTTP/1.0\r\n";
            $header .= "Host: ".$host."\r\n";
            $header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

            // 본문 생성
            foreach($sms as $index => $value){
                $data .="--$boundary\r\n";
                $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
                $data .= "\r\n".$value."\r\n";
                $data .="--$boundary\r\n";
            }
            $header .= "Content-length: " . strlen($data) . "\r\n\r\n";

            $fp = fsockopen($host, 80);
            if ($fp) {
                fputs($fp, $header.$data);
                $rsp = "";
                while(!feof($fp)) {
                    $rsp .= fgets($fp,8192);
                }
                fclose($fp);
                $msg = explode("\r\n\r\n",trim($rsp));
                $rMsg = explode(",", $msg[1]);
                $chResult= $rMsg[0]; //발송결과
                $nCount= $rMsg[1]; //잔여건수

                //발송결과 알림
                if ($chResult=="success" || $chResult=="reserved") {
                    $aResult['bResult'] = 1;
                    $aResult['nCntRemain'] = $nCount;
                } else {
                    $aResult['bResult'] = 0;
                    $aResult['chErrCode'] = $chResult;
                }
            } else {
                $aResult['bResult'] = 0;
                $aResult['chErrCode'] = 10000; //접속에러
            }
            return $aResult;
        break;
    }
}

function getCreateThumb($saveDir, $chSrcFile, $chThumbFile, $x, $y, $imgcrop=0, $croptarget='t', $quality='90') {
    $params = $_DST = $_SRC = array();
    $chDestDataDir = $saveDir;

    $params['file']=$saveDir."/".$chSrcFile;
    $_DST['file']=$chDestDataDir."/".$chThumbFile;
    $_DST['width']=$x;
    $_DST['height']=$y;
    if($imgcrop)$params['crop'] = 1;

    $temp = getimagesize($params['file']);
    $_SRC['file']		= $params['file'];
    $_SRC['width']	= $temp[0];
	$_SRC['height']	= $temp[1];
	$_SRC['type']	= $temp['mime']; // 1=GIF, 2=JPG, 3=PNG, SWF=4
	$_SRC['string']	= $temp[3];
	$_SRC['filename'] 	= basename($params['file']);
	$_SRC['modified'] 	= filemtime($params['file']);

	// 크롭일 경우
	if($params['crop'] and $x and $y) {
	    if ($_SRC['width'] > $_DST['width'] || $_SRC['height'] > $_DST['height']) {
	        $width_ratio = $_SRC['width']/$_DST['width'];
	        $height_ratio = $_SRC['height']/$_DST['height'];

	        // Es muss an der Breite beschnitten werden
	        if ($width_ratio > $height_ratio) {
	            $_DST['offset_w'] = round(($_SRC['width']-$_DST['width']*$height_ratio)/2);
	            $_SRC['width'] = round($_DST['width']*$height_ratio);
	            // es muss an der H?e beschnitten werden
	        } else if ($width_ratio < $height_ratio) {
	            if ($croptarget != "t") {
	                $_DST['offset_h'] = round(($_SRC['height']-$_DST['height']*$width_ratio)/2); // 중앙 크롭
	            } else {
	                $_DST['offset_h'] = 0; //상단 크롭
	            }
	            $_SRC['height'] = round($_DST['height']*$width_ratio);
	        }
	    } else {
	        $_DST['offset_w'] = $_SRC['width'] = $_SRC['width'];
	        $_DST['offset_h'] = $_SRC['height'] = $_SRC['height'];
	    }
	} else {
	    // 리사이즈일 경우
	    if ($_SRC['width'] > $_DST['width'] || $_SRC['height'] > $_DST['height']) {
	        $params['longside'] = $_DST['width'];
	        $params['shortside'] = $_DST['height'];
	        $temp_large=$temp[0]>$temp[1] ? $temp[0] : $temp[1];
	        //// 양쪽 다 있을 경우 비율로 리사이즈
	        if($x and $y){
	            // 세로가 클 경우
	            if ($temp[0] < $temp[1]) {
	                $tempw = (100*$y)/$temp[1];
	                $_DST['width']=ceil(($temp[0]*$tempw)/100);
	                $_DST['height']=$y;
	            } else {
	                // 가로가 클 경우
	                $temph = (100*$x)/$temp[0];
	                $_DST['width']=$x;
	                $_DST['height']=ceil(($temp[1]*$temph)/100);
	            }
	        //// 한쪽이 안정해졌을경우 ( 한쪽에만 맞춤 )
	        } else if(!$x || !$y) {
	            if($x){ // width 를 수치로 고정
	                $_DST['width']=$x;
	                $_DST['height']=ceil($_DST['width'] * $temp[1] / $temp[0]);
	            } else{
	                $_DST['height']=$y;
	                $_DST['width']=ceil($_DST['height'] * $temp[0] / $temp[1]);
	            }
	        //// 양쪽 다 정해졌을 경우
	        } else {
	            $_DST['width']=$x; $_DST['height']=$y;
	        }
	    } else {
	        $_DST['width']=$_SRC['width']; $_DST['height']=$_SRC['height'];
	    }
	}
	if ($_SRC['type'] == "image/gif")	$_SRC['image'] = imagecreatefromgif($_SRC['file']);
	if ($_SRC['type'] == "image/jpeg")	$_SRC['image'] = imagecreatefromjpeg($_SRC['file']);
	if ($_SRC['type'] == "image/png")	$_SRC['image'] = imagecreatefrompng($_SRC['file']);
	if (!empty($params['type'])) $_DST['type']	= $params['type'];
	else $_DST['type']	= $_SRC['type'];

	$_DST['image'] = imagecreatetruecolor($_DST['width'], $_DST['height']);
	imagecopyresampled($_DST['image'], $_SRC['image'], 0, 0, $_DST['offset_w'], $_DST['offset_h'], $_DST['width'], $_DST['height'], $_SRC['width'], $_SRC['height']);

	if ($_DST['type'] == "image/png")	@ImagePNG($_DST['image'],$chDestDataDir."/".$chThumbFile);
	else @ImageJPEG($_DST['image'],$chDestDataDir."/".$chThumbFile, $quality);
	@chmod($chDestDataDir."/".$chThumbFile,0606);
	imagedestroy($_DST['image']);
	return $chThumbFile;
}

function getEtcStrReplace($chStr) {
    return preg_replace("/[ #\&\+\-_%@=|\/\\\:;.,'\"\^`~\!\?\*$#<>()\[\]\{\}\r\n]/i", "", trim($chStr));
}

function getAESEncrypt($str, $key, $iv="", $replace=true) {
    $iv = $iv ? $iv : str_pad('', 16, chr(0x0));
    $keylen = strlen($key);
    if($keylen == 16) {
        $bit = 128;
    } else if($keylen == 32) {
        $bit = 256;
    }
    $cipher = 'aes-'.$bit.'-cbc';
    if($replace) {
        $encrypted = str_replace(array('+', '/'), array('-', '_'), base64_encode(openssl_encrypt($str, $cipher, $key, OPENSSL_RAW_DATA, $iv)));
    } else {
        $encrypted = base64_encode(openssl_encrypt($str, $cipher, $key, OPENSSL_RAW_DATA, $iv));
    }
    return $encrypted;
}
function getAESDecrypt($str, $key, $iv="", $replace=true) {
    $iv = $iv ? $iv : str_pad('', 16, chr(0x0));
    $keylen = strlen($key);
    if($keylen == 16) {
        $bit = 128;
    } else if($keylen == 32) {
        $bit = 256;
    }
    $cipher = 'aes-'.$bit.'-cbc';
    if($replace) {
        $decrypted = openssl_decrypt(base64_decode(str_replace(array('-', '_'), array('+', '/'), $str)), $cipher, $key, OPENSSL_RAW_DATA, $iv);
    } else {
        $decrypted = openssl_decrypt(base64_decode($str), $cipher, $key, OPENSSL_RAW_DATA, $iv);
    }
    return $decrypted;
}
function getClientIP(){
    if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
        $aIP = array_values(array_filter(explode(',',$_SERVER['HTTP_X_FORWARDED_FOR'])));
        return end($aIP);
    }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        return $_SERVER["REMOTE_ADDR"];
    }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    return '';
}
function getRemoteIP() {
    $ipaddress = "";
    if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function getJSONError($error) {
    $aErrors = array(
        JSON_ERROR_NONE => true,
        JSON_ERROR_DEPTH => 'Json error. Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Json error. State mismatch (invalid or malformed JSON)',
        JSON_ERROR_CTRL_CHAR => 'Json error. Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Json error. Syntax error',
        JSON_ERROR_UTF8 => 'Json error. Malformed UTF-8 characters, possibly incorrectly encoded'
    );
    return isset($aErrors[$error]) ? $aErrors[$error] : 'Json unknown error';
}
function getMecabParse($chStr, $delimiter="|") {
    $mecab_exe = "/usr/local/bin/mecab";
    $mecab_dic = '/usr/local/lib/mecab/dic/mecab-ko-dic';
    $mecab = extension_loaded('mecab') ? new \MeCab\Tagger(array('-d', $mecab_dic)) : "";

    $str = "";
    $chStr = strtolower($chStr);
    if($mecab) {
        $node = $mecab->parseToNode($chStr);
        foreach($node as $m) {
            $feature_arr = explode(',', $m->getFeature());
            $PS = $feature_arr[0];
            if(strpos($PS,'BOS') !== false || strpos($PS,'EOS') !== false) continue;
            $word = $m->getSurface();
            $str .=$word.$delimiter.$PS." ";
        }
    } else {
        $cmd = "echo '".$chStr."' | ".$mecab_exe." -d ".$mecab_dic;
        exec($cmd, $aResult, $return);
        if($retrun == 0 && count($aResult) > 0) {
            foreach($aResult as $node) {
                $m = explode("\t", $node);
                if(strpos($m[0],'BOS') !== false || strpos($m[0],'EOS') !== false) continue;
                $word = $m[0];
                $aInfo = explode(",", $m[1]);
                $PS = $aInfo[0];
                $str .=$word.$delimiter.$PS." ";
            }
        }
    }
    return trim($str);
}
function getMicroTime() {
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
    return $d->format("Y-m-d H:i:s.u");
}
function getBotApiResponseContent($response) {
    $_content = array();
    if($response){
        if(is_array($response)){
            foreach ($response as $resItem) {
                if(is_array($resItem[0])) {
                    $type = $resItem[0]['type'];
                    $content = $resItem[0]['content'];
                } else {
                    $type = $resItem['type'];
                    $content = $resItem['content'];
                }
                if($type == "if") {
                    foreach ($content as $item) {
                        if(!$item) continue;
                        for($i=0, $nCnt=count($item); $i<$nCnt; $i++){
                            $iftype = $item[$i]['type'];
                            $ifcontent = str_replace("\n", " ", $item[$i]['content']);
                            if(is_array($ifcontent)) {
                                $resContent = array();
                                foreach($ifcontent as $resItem) {
                                    $resContent[] = getIsJSONToArray(str_replace("\n", " ", $resItem));
                                }
                                $ifcontent = $resContent;
                            }
                            $_content[] = array("type"=>$iftype,"content"=>$ifcontent);
                        }
                    }
                } else {
                    $content = str_replace("\n", " ", $content);
                    if(is_array($content)) {
                        $resContent = array();
                        foreach($content as $resItem) {
                            $resContent[] = getIsJSONToArray(str_replace("\n", " ", $resItem));
                        }
                        $content = $resContent;
                    }
                    $_content[] = array("type"=>$type,"content"=>$content);
                }
            }
        } else {
            $_content[]= array("type"=>'text',"content"=>str_replace("\n", " ", $response));
        }
    }
    return $_content;
}
function getIsJSONToArray($data) {
    if(is_string($data)) {
        $result = json_decode($data, true);
        if(json_last_error() == JSON_ERROR_NONE) {
            return $result;
        } else {
            return $data;
        }
    } else {
        return $data;
    }
}
function array_key_replace($array, $set) {
    if (is_array($array) && is_array($set)) {
        $newArr = array();
        foreach ($array as $k => $v) {
            $key = array_key_exists( $k, $set) ? $set[$k] : $k;
            $newArr[$key] = is_array($v) ? array_key_replace($v, $set) : $v;
        }
        return $newArr;
    }
    return $array;
}
function getRemoveBackslash($data) {
    if (is_array($data)) {
        return array_map(__METHOD__,$data);
    } else {
        return trim(str_replace(array('\\\\b', '\b', ''), '', $data));
    }
}

function getMorphStrReplace($chStr) {
    return preg_replace("/[.,\!\?\(\)\[\]\{\}\'\"\r\n]/i", "", trim($chStr));
}

function getRemoveStopWords($chStr, $aExcept="") {
    $aStopWords = array("JKS", "JKC", "JKG", "JKO", "JKB", "JKV", "JKQ", "JX", "JC"); // 조사
    $aStopWords[] = "MAJ"; // 접속 부사
    $aStopWords[] = "SE"; // 줄임표 …
    $aStopWords[] = "SC"; // 구분자 , · / :
    $aStopWords[] = "SY"; // 붙임표, 기타기호
    //$aStopWords[] = "IC"; // 감탄사

    if($aExcept) {
        $aExcept = explode(",", $aExcept);
        $aStopWords = array_diff($aStopWords, $aExcept);
    }
    $aStopWords = implode("|", $aStopWords);

    $chStr = preg_replace("/\w+\|(".$aStopWords.")+(\s)?/u", "", $chStr);
    return trim($chStr);
}

function getMecabMorph($chStr, $delimiter="|") {
    $mecab_exe = "/usr/local/bin/mecab";
    $mecab_dic = "/usr/local/lib/mecab/dic/mecab-ko-dic";
    $mecab = extension_loaded('mecab') ? new \MeCab\Tagger(array('-d', $mecab_dic)) : "";

    $str = "";
    $chStr = strtolower($chStr);
    if($mecab) {
        $node = $mecab->parseToNode($chStr);
        foreach($node as $m) {
            $feature_arr = explode(',', $m->getFeature());
            $PS = $feature_arr[0];
            if(strpos($PS,'BOS') !== false || strpos($PS,'EOS') !== false) continue;
            $word = $m->getSurface();
            $str .=$word.$delimiter.$PS." ";
        }
    } else {
        $cmd = "echo '".$chStr."' | ".$mecab_exe." -d ".$mecab_dic;
        exec($cmd, $aResult, $return);
        if($retrun == 0 && count($aResult) > 0) {
            foreach($aResult as $node) {
                $m = explode("\t", $node);
                if(strpos($m[0],'BOS') !== false || strpos($m[0],'EOS') !== false) continue;
                $word = $m[0];
                $aInfo = explode(",", $m[1]);
                $PS = $aInfo[0];
                $str .=$word.$delimiter.$PS." ";
            }
        }
    }
    return trim($str);
}

function utf8_ord($ch) {
    $len = strlen($ch);
    if($len <= 0) return false;

    $h = ord($ch{0});
    if ($h <= 0x7F) return $h;
    if ($h < 0xC2) return false;
    if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
    if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);
    if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
    return false;
}
function utf8_chr($num) {
    if($num<128) return chr($num);
    if($num<2048) return chr(($num>>6)+192).chr(($num&63)+128);
    if($num<65536) return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
    if($num<2097152) return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
    return false;
}
function is_korean($str) {
    $code = utf8_ord($str);
    if((0x1100 <= $code && $code <= 0x11FF) || (0x3130 <= $code && $code <= 0x318F) || (0xAC00 <= $code && $code <= 0xD7A3)) {
        return true;
    } else {
        return false;
    }
}
function getKoreanChars() {
    $aKorean = array();
    $aKorean['aFirst'] = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
    $aKorean['aSecond'] = array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ");
    $aKorean['aLast'] = array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ"," ㅌ","ㅍ","ㅎ");
    return $aKorean;
}
function getKoreanSplit($str, $splitMode = "all") { // splitMode: all 모든 글자 분리, first 초성만 분리
    $aKorean = getKoreanChars();
    $aFirst = $aKorean['aFirst'];
    $aSecond = $aKorean['aSecond'];
    $aLast = $aKorean['aLast'];
    $result = "";
    for ($i=0, $nCnt=mb_strlen($str, "UTF-8"); $i<$nCnt; $i++) {
        $ch = mb_substr($str, $i, 1, 'UTF-8');
        $code = utf8_ord($ch) - 44032;
        if ($code > -1 && $code < 11172) {
            $temp = "";
            $temp .= $aFirst[($code / 588)];

            if($splitMode == "all") {
                $temp .= $aSecond[($code % 588 / 28)];
                $tempLast = $aLast[($code % 28)];
                $temp .= ($tempLast ? $tempLast : "_");
            }
            $result .= $temp;
        } else {
            $result .= $ch;
        }
    }
    return $result;
}
function getKoreanSplitJoin($str) {
    $aKorean = getKoreanChars();
    $aFirst = $aKorean['aFirst'];
    $aSecond = $aKorean['aSecond'];
    $aLast = $aKorean['aLast'];
    $result = "";
    $nFirst = $nSecond = $nLast = -1;
    for ($i=0, $nCnt=mb_strlen($str, "UTF-8"); $i<$nCnt; $i++) {
        $ch = mb_substr($str, $i, 1, 'UTF-8');
        $bKorean = is_korean($ch);
        if($bKorean || $ch == "_") {
            if($nFirst == -1 && in_array($ch, $aFirst)) {
                $nFirst = array_search($ch, $aFirst);
                continue;
            }
            if($nSecond == -1 && in_array($ch, $aSecond)) {
                $nSecond = array_search($ch, $aSecond);
                continue;
            }
            if($nLast == -1 && ($ch == "_" || in_array($ch, $aLast))) {
                $nLast = $ch == "_" ? 0 : array_search($ch, $aLast);
                $nChr = ((($nFirst*21+$nSecond)*28+$nLast)+44032);
                $nFirst = $nSecond = $nLast = -1;
                $result .=utf8_chr($nChr);
            }
        } else {
            $result .=$ch;
        }
    }
    return $result;
}
function getMemberBotMenu() {
    global $table, $my;
    $mybot = $mymenu = [];
    if(isset($my['mygroup']) && $my['mygroup']) {
        $group = getDbData($table['s_mbrgroup'], 'uid='.$my['mygroup'],'*');

        if($group['bot']) {
            $mybot = explode(",", $group['bot']);
        }

        $_SESSION['bottype'] = isset($_GET['bottype']) ? $_GET['bottype'] : ($_SESSION['bottype'] ? $_SESSION['bottype'] : 'chat');

        if ('chat' === $_SESSION['bottype']){
            if($group['menu']) {
                $mymenu = explode(",", $group['menu']);
                $_in = "'".implode("', '", $mymenu)."'";

                $_where = "uid in (Select parent From rb_s_menu Where id in (".$_in."))";
                $RCD = getDbArray("rb_s_menu", $_where, "id, name", 'gid','asc','',1);
                while ($R = db_fetch_array($RCD)) {
                    if(!in_array($R['id'], $mymenu)) $mymenu[] = $R['id'];
                }
            }
        } else {
            if($group['call_menu']) {
                $mymenu = explode(",", $group['call_menu']);
                $_in = "'".implode("', '", $mymenu)."'";

                $_where = "uid in (Select parent From rb_s_menu Where id in (".$_in."))";
                $RCD = getDbArray("rb_s_menu", $_where, "id, name", 'gid','asc','',1);
                while ($R = db_fetch_array($RCD)) {
                    if(!in_array($R['id'], $mymenu)) $mymenu[] = $R['id'];
                }
            }
        }
    }
    $my['mybot'] = $mybot;
    $my['mymenu'] = $mymenu;
    if(in_array('operation', $my['mymenu'])) {
        $my['mymenu'][] = '_reserve_info';
        $my['mymenu'][] = '_disability_info';
    }
}
function getPSToArray($str, $divi="(*|*)") {
    $aPS = array();
    $aStr = explode(" ", $str);
    foreach($aStr as $_str) {
        $_temp = explode($divi, $_str);
        $aPS[] = array("ps"=>$_temp[1], "word"=>$_temp[0]);
    }
    return $aPS;
}
function getChatbotLogToJson($data) {
    if(!$data['vendor'] || !$data['bot'] || !$data['roomToken']) return [];

    include_once $GLOBALS['g']['path_core'] . "function/simple_html_dom.php";

    $aPrintType = array('T'=>'text', 'B'=>'button', 'text'=>'text', 'hMenu'=>'button', 'card'=>'card', 'img'=>'image', 'if'=>'if', 'hform'=>'html');
    $aChatbotLog = [];

    // 챗봇 로그 검색 및 전송
    $chQuery = "Select A.* From rb_chatbot_chatLog A ";
    $chQuery .="Where A.vendor = '".$data['vendor']."' and A.bot = '".$data['bot']."' and A.roomToken = '".$data['roomToken']."' ";
    $chQuery .="Order by A.uid ASC ";
    $RCD = db_query($chQuery, $GLOBALS['DB_CONNECT']);
    while($R=db_fetch_array($RCD)){
        $printType = $R['printType'] == 'T' || $R['printType'] == 'W'? "text" : "button";
        $userMsg = $R['printType'] == "W" ? "Welcome" : $R['content'];

        $aChatbotLog[] = ['speaker'=>'user', 'data'=>['type'=>$printType, 'msg'=>$userMsg], 'time'=>date('Y-m-d H:i:s', strtotime($R['d_regis']))];

        $chQuery = "Select A.* From rb_chatbot_botChatLog A ";
        $chQuery .="Where A.vendor = '".$data['vendor']."' and A.bot = '".$data['bot']."' and A.roomToken = '".$data['roomToken']."' and A.chat = '".$R['uid']."' ";
        $chQuery .="Order by A.uid ASC ";
        $BRCD = db_query($chQuery, $GLOBALS['DB_CONNECT']);
        $aResLog = [];
        $_date = "";
        while($row=db_fetch_array($BRCD)){
            $_tempRes = [];

            if($row['content'] == strip_tags($row['content'])) {
                $_tempRes['type'] = $aPrintType['text'];
                $_tempRes['msg'] = trim($row['content']);
            } else {
                $oHtml = str_get_html($row['content']);

                if($oHtml->find("div.cb-chatting-balloon")) {
                    if($row['findType'] == 'F') {
                        $_tempRes['type'] = "text";
                    } else {
                        $_tempRes['type'] = $aPrintType['text'];
                    }
                    $_tempRes['msg'] = trim($oHtml->find("div.cb-chatting-balloon", 0)->plaintext);

                } else if($oHtml->find("figure[data-type=img]")) {
                    $_tempRes['type'] = $aPrintType['img'];
                    $_tempRes['images'] = [];
                    foreach($oHtml->find("figure[data-type=img]") as $oItem) {
                        $_tempRes['images'][] = trim($oItem->find("img", 0)->src);
                    }

                } else if($oHtml->find("[data-role=menuType-resItem]")) {
                    $_tempRes['type'] = $aPrintType['hMenu'];
                    $_tempRes['buttons'] = [];
                    foreach($oHtml->find("[data-role=menuType-resItem]") as $oItem) {
                        $_tempRes['buttons'][] = trim($oItem->plaintext);
                    }

                } else if($oHtml->find("[data-role=cardType-resItem]")) {
                    $_tempRes['type'] = $aPrintType['card'];
                    $_tempRes['cards'] = [];
                    foreach($oHtml->find("[data-role=cardType-resItem]") as $oItem) {
                        $_tempRes['cards'][] = trim($oItem->find('.card-title', 0)->plaintext);
                    }

                } else if($row['printType'] == 'hform' && $oHtml->find("div.bot_form")) {
                    $_tempRes['type'] = $aPrintType['hform'];
                    $_tempRes['msg'] = 'html 양식';
                }
            }
            $_date = date('Y-m-d H:i:s', strtotime($row['d_regis']));
            $aResLog[] = $_tempRes;
        }

        $aChatbotLog[] = ['speaker'=>'bot', 'data'=>$aResLog, 'time'=>$_date];
    }
    return $aChatbotLog;
}

function getSsoSIDSend() {
    global $table, $my;
    $apiURL = $GLOBALS['g']['sid_send_url'];

    // ajax 요청이 아닐 경우만 실행
    if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        if(isset($_SESSION['sso_login']) && $_SESSION['sso_login'] && $_SESSION['mbr_uid']) {
            $info = getDbData($table['s_mbrdata'],'memberuid='.$_SESSION['mbr_uid'],'*');
            if($info['sid']) {
                $data = [];
                $_data = ['id'=>$my['id'], 'sid'=>$info['sid']];
                $data['postData'] = json_encode($_data, JSON_UNESCAPED_UNICODE);

                $data['headers'][] = "Content-Type: application/json";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiURL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data['postData']);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                if(isset($data['headers']) && count($data['headers']) > 0) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $data['headers']);
                }
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if($http_code == 200 && $response) {
                    $result = json_decode($response, true);
                    if(isset($result['sid']) && trim($result['sid'])) {
                        getDbUpdate($table['s_mbrdata'], "sid='".$result['sid']."'", "memberuid='".$_SESSION['mbr_uid']."'");
                    }
                }
            }
        }
    }
}
?>