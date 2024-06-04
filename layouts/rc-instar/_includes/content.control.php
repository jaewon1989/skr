<?php
$_SESSION['wcode']=$date['totime'];
$feed=new feed();
$TMPL=array();

// 최신 피드 출력 
$feed->recnum = $d['sns']['feed_m_single_recnum'];
$feed_new = $feed->getFeed($mbruid,$type,'new',$search,1); // mod 에 다라서 query 만 다르게 한다. 
$TMPL['rows']=$feed_new[0];
$skin=new skin('feed/default');
$Feed_New_List=$skin->make();

$feed_hot = $feed->getFeed($mbruid,$type,'hot',$search,1); // mod 에 다라서 query 만 다르게 한다. 
$TMPL['rows']=$feed_hot[0];
$skin=new skin('feed/default');
$Feed_Hot_List=$skin->make();

// 동영상 피드 출력 
$feed_video = $feed->getFeed($mbruid,$type,'video',$search,1); // mod 에 다라서 query 만 다르게 한다. 
$TMPL['rows']=$feed_video[0];
$skin=new skin('feed/default');
$Feed_Video_List=$skin->make();

// 패션왕 출력 
$women_search=array('sex'=>'female');
$feed->user_recnum =4;
$getUser = $feed->getUser($mbruid,'best',$women_search,1);
$Feed_Best_List = $getUser[0];

// 기본 필터 설정  
$mod_title = array('hot'=>'인기','new'=>'최신','best'=>'패션왕','video'=>'영상',);
$TMPL['mod'] = $mod;  
$TMPL['mod_title'] = $mod_title[$mod];
$TMPL['listType_single_active'] = 'active';

if($mod=='best') $filter_markup ='best';
else $filter_markup ='default';

$default_filter_skin=new skin('filter/filter_'.$filter_markup);
$filter_default=$default_filter_skin->make();

// search 등 기타 페이지와 메인 페이지 구분 > 무하스크롤과 연관 
if($m && $m=='sns') $object ='sub';
else $object='main';

?>