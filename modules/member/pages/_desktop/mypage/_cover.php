<?php
//게시물 링크
function getPostLink($arr)
 {  
    return RW('m=bbs&bid='.$arr['bbsid'].'&uid='.$arr['uid'].($GLOBALS['s']!=$arr['site']?'&s='.$arr['site']:''));
 }
 //동기화URL
function getCyncUrl($cync)
{
	if (!$cync) return $GLOBALS['g']['r'];
	$_r = getArrayString($cync);
	$_r = $_r['data'][5];
	if ($GLOBALS['_HS']['rewrite']&&strpos('_'.$_r,'m:bbs,bid:'))
	{
		$_r = str_replace('m:bbs','b',$_r);
		$_r = str_replace(',bid:','/',$_r);
		$_r = str_replace(',uid:','/',$_r);
		$_r = str_replace(',CMT:','/',$_r);
		$_r = str_replace(',s:','/s',$_r);
		return $GLOBALS['g']['r'].'/'.$_r;
	}
	else return $GLOBALS['g']['s'].'/?'.($GLOBALS['_HS']['usescode']?'r='.$GLOBALS['_HS']['id'].'&amp;':'').str_replace(':','=',str_replace(',','&amp;',$_r));
}
$levelnum = getDbData($table['s_mbrlevel'],'gid=1','*');
$levelname= getDbData($table['s_mbrlevel'],'uid='.$my['level'],'*');
$sosokname= getDbData($table['s_mbrgroup'],'uid='.$my['sosok'],'*');
$joinsite = getDbData($table['s_site'],'uid='.$my['site'],'*');
$lastlogdate = -getRemainDate($my['last_log']);
$my_cimg=$g['path_module'].$m.'/pages/mypage/image/cover/'.$my['id'].'.jpg';
$sample_cimg=$g['path_module'].$m.'/pages/mypage/image/cover/cover_sample.jpg';
$cover_img=is_file($my_cimg)?$my_cimg:$sample_cimg;

?>
<div class="panel panel-default cover-container">
	<form name="imgForm" class="form-horizontal" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
			<input type="hidden" name="r" value="<?php echo $r?>">
			<input type="hidden" name="m" value="<?php echo $m?>">
			<input type="hidden" name="a" value="change_mbr_img">
			<input type="hidden" name="img_module_skin" value="<?php echo $g['img_module_skin']?>">
			<input type="file" name="avatar" class="hidden" id="mbr-avatar" onchange="this.form.submit();" accept="image/jpg">
			<input type="file" name="cover" class="hidden" id="mbr-cover" onchange="this.form.submit();" accept="image/jpg">
	<div class="cover">
		<img src="<?php echo $cover_img?>" class="img-responsive" alt="마이페이지 커버 이미지">
	</div>
	<nav class="navbar navbar-default" role="navigation">
	    <div >
	        <ul class="nav navbar-nav hidden-xs">
	        	   <li<?php if($page=='main'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>">메인</a></li>	         
	         <?php if($d['member']['mytab_post']):?><li<?php if($page=='post'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=post">게시글</a></li><?php endif?>
            <?php if($d['member']['mytab_comment']):?><li<?php if($page=='comment'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=comment">댓글</a></li><?php endif?>
            <?php if($d['member']['mytab_oneline']):?><li<?php if($page=='oneline'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=oneline">한줄 의견</a></li><?php endif?>
	         <?php if($d['member']['mytab_scrap']):?><li<?php if($page=='scrap'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=scrap">스크랩</a></li><?php endif?>
	         <?php if($d['member']['mytab_paper']):?><li<?php if($page=='paper'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=paper">쪽지</a></li><?php endif?>
	         <?php if($d['member']['mytab_point']):?><li<?php if($page=='point'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=point">포인트</a></li><?php endif?>
	         <?php if($d['member']['mytab_log']):?><li<?php if($page=='log'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=log">접속기록</a></li><?php endif?>
	        </ul>
	        <ul class="nav navbar-nav visible-xs">
	          <li<?php if($page=='main'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>">메인</a></li>	         
	         <?php if($d['member']['mytab_post']):?><li<?php if($page=='post'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=post">게시글</a></li><?php endif?>
            <?php if($d['member']['mytab_comment']):?><li<?php if($page=='comment'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=comment">댓글</a></li><?php endif?>
	            <li class="dropdown">
	                <a class="dropdown-toggle" data-toggle="dropdown" href="#">더보기
	                    <b class="caret"></b>
	                </a>
	                <ul class="dropdown-menu po-ab">
			            <?php if($d['member']['mytab_oneline']):?><li<?php if($page=='oneline'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=oneline">한줄 의견</a></li><?php endif?>
				          <?php if($d['member']['mytab_paper']):?><li<?php if($page=='paper'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=paper">쪽지</a></li><?php endif?>
				         <?php if($d['member']['mytab_scrap']):?><li<?php if($page=='scrap'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=scrap">스크랩</a></li><?php endif?>
				         <?php if($d['member']['mytab_point']):?><li<?php if($page=='point'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=point">포인트</a></li><?php endif?>
				         <?php if($d['member']['mytab_log']):?><li<?php if($page=='log'):?> class="active text-danger"<?php endif?>><a href="<?php echo $g['url_reset']?>&amp;page=log">접속기록</a></li><?php endif?>
	                </ul>
	            </li>
	        </ul>
	        <ul class="nav navbar-nav navbar-right hidden-xs">
		         <li class="dropdown" style="margin-right:15px;">
	                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                	  <i class="fa fa-cog fa-lg"></i> <b class="caret"></b>
	                </a>
	                <ul class="dropdown-menu">
	                	<?php if($d['member']['mytab_covimg']):?><li><a href="#" class="change-img" id="cover" data-toggle="tooltip" title="페이지 폭 * 600 px" >커버 이미지 변경</a></li><?php endif?>
	                  <?php if($d['member']['mytab_avatar']):?><li><a href="#" class="change-img" id="avatar" data-toggle="tooltip" title="180 px * 180 px">프로필 사진 변경</a></li><?php endif?>
	                  <?php if($d['member']['mytab_info']):?><li><a href="<?php echo $g['url_reset']?>&amp;page=info">내 정보 수정</a></li><?php endif?>
	                  <?php if($d['member']['mytab_pw']):?><li><a href="<?php echo $g['url_reset']?>&amp;page=pw">비밀번호 변경</a></li><?php endif?>
	                  <?php if($d['member']['mytab_out']):?><li><a href="<?php echo $g['url_reset']?>&amp;page=out">회원탈퇴</a></li><?php endif?>
	                </ul>
	            </li>
	        </ul>
	    </div>
	</nav>
	<a href="#" class="profile-thumb">
	    <img src="<?php echo $g['s']?>/_var/avatar/<?php echo $my['photo']?'180.'.$my['photo']:'180.0.gif'?>" class="img-thumbnail media-object cover-avatar" alt="<?php echo $my['name']?> 프로필 사진">
	</a>
	<h2 class="profile-name">
		<a href=""><?php echo $my['name']?$my['name']:'회원명'?><small>(<?php echo $my['id']?$my['id']:'회원 아이디'?>)</small></a>
	</h2>
    <div class="btn-group btn-group-sm visible-xs" id="cover-settings-xs">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        	<i class="fa fa-cog fa-lg"></i> 
            <b class="caret"></b>
        </button>
        <ul class="dropdown-menu pull-right">
            <?php if($d['member']['mytab_covimg']):?><li><a href="#" class="change-img" id="cover" data-toggle="tooltip" title="페이지 폭 * 700 px" >커버 이미지 변경</a></li><?php endif?>
            <?php if($d['member']['mytab_avatar']):?><li><a href="#" class="change-img" id="avatar" data-toggle="tooltip" title="180 px * 180 px">프로필 사진 변경</a></li><?php endif?>
            <?php if($d['member']['mytab_info']):?><li><a href="<?php echo $g['url_reset']?>&amp;page=info">내 정보 수정</a></li><?php endif?>
            <?php if($d['member']['mytab_pw']):?><li><a href="<?php echo $g['url_reset']?>&amp;page=pw">비밀번호 변경</a></li><?php endif?>
            <?php if($d['member']['mytab_out']):?><li><a href="<?php echo $g['url_reset']?>&amp;page=out">회원탈퇴</a></li><?php endif?>
        </ul>
    </div>
   </form> 
</div>
