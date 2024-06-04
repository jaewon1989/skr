<?php
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 301 ? $recnum : 20;
$bbsque ='uid>0';
// 키원드 검색 추가 
if ($keyw)
{
	$bbsque .= " and (id like '%".$keyw."%' or name like '%".$keyw."%')";
}
$RCD = getDbArray($table[$module.'list'],$bbsque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'list'],$bbsque);
$TPG = getTotalPage($NUM,$recnum);
if ($uid)
{
	$R = getUidData($table[$module.'list'],$uid);
	if ($R['uid'])
	{
		include_once $g['path_module'].$module.'/var/var.'.$R['id'].'.php';
	}
}
?>
<div class="row">
   <div class="col-md-4 col-lg-3">
   	<div class="panel panel-default">  <!-- 메뉴 리스트 패털 시작 -->
   		
   		<!-- 메뉴 패널 헤더 : 아이콘 & 제목 --> 
			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-file-text-o fa-2x"></i>
				</div>
				<h4 class="panel-title">
					게시판 리스트 
					<span class="pull-right">
						<button type="button" class="btn btn-default btn-xs<?php if(!$_SESSION['sh_site_bbs_search']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#panel-search" data-tooltip="tooltip" title="검색필터" onclick="sessionSetting('sh_site_bbs_search','1','','1');getSearchFocus();"><i class="glyphicon glyphicon-search"></i></button>
					</span>
				</h4>
			</div>
			<div id="panel-search" class="collapse<?php if($_SESSION['sh_site_bbs_search']):?> in<?php endif?>">
				<form role="form" action="<?php echo $g['s']?>/" method="get">
				<input type="hidden" name="r" value="<?php echo $r?>">
				<input type="hidden" name="m" value="<?php echo $m?>">
				<input type="hidden" name="module" value="<?php echo $module?>">
				<input type="hidden" name="front" value="<?php echo $front?>">
					<div class="panel-heading rb-search-box">
						<div class="input-group">
							<div class="input-group-addon"><small>출력수</small></div>
							<div class="input-group-btn">
								<select class="form-control" name="recnum" onchange="this.form.submit();">
								<option value="15"<?php if($recnum==15):?> selected<?php endif?>>15</option>
								<option value="30"<?php if($recnum==30):?> selected<?php endif?>>30</option>
								<option value="60"<?php if($recnum==60):?> selected<?php endif?>>60</option>
								<option value="100"<?php if($recnum==100):?> selected<?php endif?>>100</option>
								</select>
							</div>
						</div>
					</div>
					<div class="rb-keyword-search input-group input-group-sm">
						<input type="text" name="keyw" class="form-control" value="<?php echo $keyw?>" placeholder="아이디 or 이름">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit">검색</button>
						</span>
					</div>
				</form>
			</div>
			<div class="panel-body" style="border-top:1px solid #DEDEDE;">
				<?php if($NUM):?>
				<form name="bbsform" role="form" action="<?php echo $g['s']?>/" method="post" target="_orderframe_">
				<input type="hidden" name="r" value="<?php echo $r?>" />
				<input type="hidden" name="m" value="<?php echo $module?>" />
				<input type="hidden" name="a" value="bbsorder_update" />
				<div class="dd" id="nestable-menu">
					<ul class="dd-list list-unstyled">
					<?php $_i=1;while($BR = db_fetch_array($RCD)):?>
					<li class="dd-item" data-id="<?php echo $_i?>">
						<input type="checkbox" name="bbsmembers[]" value="<?php echo $BR['uid']?>" checked class="hidden"/>
						<span class="dd-handle <?php if($BR['uid']==$R['uid']):?>alert alert-info<?php endif?>" ><i class="fa fa-arrows fa-fw"></i> 
						   <?php echo $BR['name']?>(<?php echo $BR['id']?>)
						</span>
						<span title="<?php echo number_format($BR['num_r'])?>개" data-tootip="tooltip">
							<a href="<?php echo $g['adm_href']?>&amp;recnum=<?php echo $recnum?>&amp;p=<?php echo $p?>&amp;uid=<?php echo $BR['uid']?>" data-tooltip="tooltip" title="수정하기">
								<i class="glyphicon glyphicon-edit"></i>
							</a>
						</span>	
					</li>
					<?php $_i++;endwhile?>
					</ul>
				</div>
				</form>
				<!-- nestable : https://github.com/dbushell/Nestable -->
				<?php getImport('nestable','jquery.nestable',false,'js') ?>
				<script>
					$('#nestable-menu').nestable();
					$('.dd').on('change', function() {
						var f = document.bbsform;
						getIframeForAction(f);
						f.submit();
					});
				</script>
				
				<?php else:?>
				<div class="none">등록된 게시판이 없습니다.</div>
				<?php endif?>
				
         </div>  
        	<div class="panel-footer rb-panel-footer">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>
		</div> <!-- 좌측 패널 끝 -->	
   </div><!-- 좌측  내용 끝 -->	
   <!-- 우측 내용 시작 -->
   <div id="tab-content-view" class="col-md-8 col-lg-9">
		<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="makebbs" />
		<input type="hidden" name="bid" value="<?php echo $R['id']?>" />
		<input type="hidden" name="perm_g_list" value="<?php echo $R['perm_g_list']?>" />
		<input type="hidden" name="perm_g_view" value="<?php echo $R['perm_g_view']?>" />
		<input type="hidden" name="perm_g_write" value="<?php echo $R['perm_g_write']?>" />
		<input type="hidden" name="perm_g_down" value="<?php echo $R['perm_g_down']?>" />
       
     	 <div class="page-header">
			<h4>
				<?php if($R['uid']):?>
				게시판 등록정보
				<div class="pull-right rb-top-btnbox hidden-xs">
			  		<a href="<?php echo $g['adm_href']?>" class="btn btn-default"><i class="fa fa-plus"></i> 새게시판 만들기</a>
			    </div>
				<?php else:?>
				새게시판 만들기 
				<?php endif?>
			</h4>
	    </div>
       <div class="form-group">
				<label class="col-sm-2 control-label">게시판 이름</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input class="form-control" placeholder="" type="text" name="name" value="<?php echo $R['name']?>"<?php if(!$R['uid'] && !$g['device']):?> autofocus<?php endif?>>
						<span class="input-group-btn">
							<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#bbs_name-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
						</span>
						<?php if($R['uid']):?>
						<span class="input-group-btn">
							<a href="<?php echo RW('m='.$module.'&bid='.$R['id'])?>" target="_blank" class="btn btn-default" data-tooltip="tooltip" title="게시판 보기">
							<i class="fa fa-link fa-lg"></i>
							</a>
						</span>
						<?php endif?>
					</div>
					<p class="help-block collapse alert alert-warning" id="bbs_name-guide">
						<small> 게시판제목에 해당되며 한글,영문등 자유롭게 등록할 수 있습니다.</small>
			      </p>
				</div>				
		 </div>
		 <div class="form-group">
				<label class="col-sm-2 control-label">게시판 아이디</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input class="form-control" placeholder="" type="text" name="id" value="<?php echo $R['id']?>" <?php if($R['uid']):?>disabled<?php endif?>>
						<span class="input-group-btn">
							<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#bbs_id-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
						</span>
						<?php if($R['uid']):?>
						<span class="input-group-btn">
							<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;a=deletebbs&amp;uid=<?php echo $R['uid']?>" onclick="return hrefCheck(this,true,'삭제하시면 모든 게시물이 지워지며 복구할 수 없습니다.\n정말로 삭제하시겠습니까?');"  class="btn btn-default" data-tooltip="tooltip" title="삭제하기">
							<i class="fa fa-trash-o fa-lg"></i>
							</a>
						</span>
						<?php endif?>
					</div>
					<p class="help-block collapse alert alert-warning" id="bbs_id-guide">
						<small> 영문 대소문자+숫자+_ 조합으로 만듭니다.</small>
			      </p>
				</div>				
		 </div>
		 <div class="form-group">
				<label class="col-sm-2 control-label">카테고리</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input class="form-control" placeholder="" type="text" name="category" value="<?php echo $R['category']?>" />
						<span class="input-group-btn">
							<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#bbs_cat-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
						</span>
					</div>
					<p class="help-block collapse alert alert-warning" id="bbs_cat-guide">
						<small> 
							분류를 <strong>콤마(,)</strong>로 구분해 주세요. <strong>첫분류는 분류제목</strong>이 됩니다.<br />
					      보기)<strong>구분</strong>,유머,공포,엽기,무협,기타
					     </small>
			      </p>
				</div>				
		 </div>
       <div class="form-group">
				<label class="col-sm-2 control-label">레이아웃</label>
				<div class="col-sm-10">
			   	 <select name="layout" class="form-control">
					 	<option value="">&nbsp;+ 사이트 대표레이아웃</option>
						<option value="">--------------------------------</option>
							<?php $dirs = opendir($g['path_layout'])?>
							<?php while(false !== ($tpl = readdir($dirs))):?>
							<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
							<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
							<option value="">--------------------------------</option>
							<?php while(false !== ($tpl1 = readdir($dirs1))):?>
							<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
							<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['bbs']['layout']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
							<?php endwhile?>
							<?php closedir($dirs1)?>
							<?php endwhile?>
							<?php closedir($dirs)?>
					</select>
				</div>				
		 </div>
		 <div class="form-group">
		  	  <label class="col-sm-2 control-label">게시판 테마 </label> 
		     <div class="col-sm-10">
 			  		    <select name="skin" class="form-control">
						<option value="">&nbsp;+ 게시판 대표테마</option>
						<option value="">--------------------------------</option>
						<?php $tdir = $g['path_module'].$module.'/theme/_pc/'?>
						<?php $dirs = opendir($tdir)?>
						<?php while(false !== ($skin = readdir($dirs))):?>
						<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
						<option value="_pc/<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['bbs']['skin']=='_pc/'.$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
						<?php endwhile?>
						<?php closedir($dirs)?>
					</select>						
				</div> <!-- .col-sm-10  -->
		</div> <!-- .form-group  -->
		<div class="form-group">
		  	  <label class="col-sm-2 control-label text-muted">(모바일 접속)</label> 
		     <div class="col-sm-10">
 			  		<select name="m_skin" class="form-control">
						<option value="">&nbsp;+ 게시판 모바일 대표테마</option>
						<option value="">--------------------------------</option>
						<?php $tdir = $g['path_module'].$module.'/theme/_mobile/'?>
						<?php $dirs = opendir($tdir)?>
						<?php while(false !== ($skin = readdir($dirs))):?>
						<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
						<option value="_mobile/<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['bbs']['m_skin']=='_mobile/'.$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
						<?php endwhile?>
						<?php closedir($dirs)?>
					</select>					
				</div> <!-- .col-sm-10  -->
		</div> <!-- .form-group  -->
		<div class="form-group">
				<label class="col-sm-2 control-label">연결메뉴</label>
				<div class="col-sm-10">
					<div class="input-group">
					   	 <select name="sosokmenu" class="form-control">
							 	<option value="">&nbsp;+ 사용 안함</option>
								<option value="">----------------------</option>
							   <?php include_once $g['path_core'].'function/menu1.func.php'?>
							   <?php $cat=$d['bbs']['sosokmenu']?>
							   <?php getMenuShowSelect($s,$table['s_menu'],0,0,0,0,0,'')?>
							</select>
							<span class="input-group-btn">
								<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#sosok_menu-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
							</span>
				   </div>
				   <p class="help-block collapse alert alert-warning" id="sosok_menu-guide">
						<small> 
							이 게시판을 메뉴에 연결하였을 경우 해당메뉴를 지정해 주세요.<br />
					     연결메뉴를 지정하면 게시물수,로케이션이 동기화됩니다.<br />
					   </small>
			      </p>
				</div>				
		 </div>
		 <div class="form-group">
		      <label class="col-sm-2 control-label">소셜연동</label>
				<div class="col-sm-10">
					<div class="input-group">
					   	 <select name="snsconnect" class="form-control">
								<option value="0">&nbsp;+ 연동안함</option>
								<option value="0">--------------------------------</option>
								<?php $tdir = $g['path_module'].'social/inc/'?>
								<?php if(is_dir($tdir)):?>
								<?php $dirs = opendir($tdir)?>
								<?php while(false !== ($skin = readdir($dirs))):?>
								<?php if($skin=='.' || $skin == '..')continue?>
								<option value="social/inc/<?php echo $skin?>"<?php if($d['bbs']['snsconnect']=='social/inc/'.$skin):?> selected="selected"<?php endif?>>ㆍ<?php echo str_replace('.php','',$skin)?></option>
								<?php endwhile?>
								<?php closedir($dirs)?>
								<?php endif?>
							</select>
							<span class="input-group-btn">
								<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#sns_connect-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
							</span>
				   </div>
				   <p class="help-block collapse alert alert-warning" id="sns_connect-guide">
						<small> 
							게시물 등록시 SNS에 동시등록을 가능하게 합니다.<br />
					      이 서비스를 위해서는 소셜연동 모듈을 설치해야 합니다.<br />
					   </small>
			      </p>
				</div>				
		 </div>
		<!-- 추가설정 시작 : panel-group 으로 각각의 panel 을 묶는다.--> 
       <div id="bbs-settings" class="panel-group">
			 <div id="bbs-settings-add" class="panel panel-default"> <!-- 추가설정-->
			    <div class="panel-heading">
					 <h4 class="panel-title">
						 <a onclick="boxDeco('add');" href="#bbs-settings-add-body" data-parent="#bbs-settings" data-toggle="collapse" class="collapsed">
							<i class="fa fa-caret-right fa-fw"></i>추가설정
  					    </a>
					 </h4>
				 </div> <!-- .panel-heading -->
				<div class="panel-collapse collapse" id="bbs-settings-add-body">
					<div class="panel-body">
						  <!-- .form-group 나열  -->
						  <?php include $g['path_module'].$module.'/admin/_add_fgroup.php';?>
					</div>
					<div class="panel-footer">
	  					<small class="text-muted">
	  						<i class="fa fa-info-circle fa-lg fa-fw"></i> 이상 추가설정 내용입니다.
	  					</small>
					</div> 
				</div> <!-- .panel-body & .panel-footer : 숨겼다 보였다 하는 내용  -->
			 </div> <!-- .panel 전체 --> 
			 <div id="bbs-settings-right" class="panel panel-default"><!--권한설정-->				
				 <div class="panel-heading">
					<h4 class="panel-title">
						<a onclick="boxDeco('right');" href="#bbs-settings-right-body" data-parent="#bbs-settings" data-toggle="collapse" class="collapsed">
							<i class="fa fa-caret-right fa-fw"></i>권한설정
						</a>
					</h4>
				 </div>				
				 <div class="panel-collapse collapse" id="bbs-settings-right-body">
					 <div class="panel-body">
				        <!-- .form-group 나열  -->
				        <?php include $g['path_module'].$module.'/admin/_right_fgroup.php';?> 
				    </div>
				    <div class="panel-footer">
	  					 <small class="text-muted">
	  					 	 <i class="fa fa-info-circle fa-lg fa-fw"></i> 이상 권한설정 내용입니다.
	  					 </small>
					</div> 
	          </div> <!-- .panel-body & .panel-footer : 숨겼다 보였다 하는 내용  -->
          </div>  <!-- .panel 전체 --> 
          <div id="bbs-settings-hcode" class="panel panel-default"><!--헤더삽입-->				
				 <div class="panel-heading">
					<h4 class="panel-title">
						<a onclick="boxDeco('hcode');" href="#bbs-settings-hcode-body" data-parent="#bbs-settings" data-toggle="collapse" class="collapsed">
							<i class="fa fa-caret-right fa-fw"></i>헤더삽입
						</a>
					</h4>
				 </div>				
				 <div class="panel-collapse collapse" id="bbs-settings-hcode-body">
					 <div class="panel-body">
				        <!-- .form-group 나열  -->
				        <?php include $g['path_module'].$module.'/admin/_header_fgroup.php';?> 
				    </div>
				    <div class="panel-footer">
	  					 <small class="text-muted">
	  					 	 <i class="fa fa-info-circle fa-lg fa-fw"></i> 이상 헤더삽입 내용입니다.
	  					 </small>
					</div> 
	          </div> <!-- .panel-body & .panel-footer : 숨겼다 보였다 하는 내용  -->
          </div>  <!-- .panel 전체 --> 
          <div id="bbs-settings-fcode" class="panel panel-default"><!--풋터삽입-->				
				 <div class="panel-heading">
					<h4 class="panel-title">
						<a onclick="boxDeco('fcode');" href="#bbs-settings-fcode-body" data-parent="#bbs-settings" data-toggle="collapse" class="collapsed">
							<i class="fa fa-caret-right fa-fw"></i>풋터삽입
						</a>
					</h4>
				 </div>				
				 <div class="panel-collapse collapse" id="bbs-settings-fcode-body">
					 <div class="panel-body">
				        <!-- .form-group 나열  -->
				        <?php include $g['path_module'].$module.'/admin/_footer_fgroup.php';?>
				    </div>
				    <div class="panel-footer">
	  					 <small class="text-muted">
	  					 	 <i class="fa fa-info-circle fa-lg fa-fw"></i> 이상 풋터삽입 내용입니다.
	  					 </small>
					</div> 
	          </div> <!-- .panel-body & .panel-footer : 숨겼다 보였다 하는 내용  -->
          </div>  <!-- .panel 전체 --> 
       </div> <!-- .panel-group -->   
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> <?php echo $R['uid']?'게시판속성 변경':'새게시판 만들기'?></button>
			</div>
		</div>
	</form>
		
  </div> <!-- 우측내용 끝 --> 
</div> <!-- .row 전체 box --> 
<iframe name="_orderframe_" class="hide"></iframe>
<script type="text/javascript">
//<![CDATA[
// 추가설정 패널 디자인 조정 
function boxDeco(val)
{
	var layer_arr=["add","right","hcode","fcode"]; // 레이어 배열 
   var parent='bbs-settings-';
   var this_layer='bbs-settings-'+val;
   for(var i=0;i<layer_arr.length;i++)
   {
      if(layer_arr[i]!=val) $('#'+parent+layer_arr[i]).addClass("panel-default").removeClass("panel-primary");
   }	
   $('#'+this_layer).addClass("panel-primary").removeClass("panel-default");	
}
function saveCheck(f)
{
    var l1 = f._perm_g_list;
    var n1 = l1.length;
    var l2 = f._perm_g_view;
    var n2 = l2.length;
    var l3 = f._perm_g_write;
    var n3 = l3.length;
    var l4 = f._perm_g_down;
    var n4 = l4.length;
    var i;
	var s1 = '';
	var s2 = '';
	var s3 = '';
	var s4 = '';
	for	(i = 0; i < n1; i++)
	{
		if (l1[i].selected == true && l1[i].value != '')
		{
			s1 += '['+l1[i].value+']';
		}
	}
	for	(i = 0; i < n2; i++)
	{
		if (l2[i].selected == true && l2[i].value != '')
		{
			s2 += '['+l2[i].value+']';
		}
	}
	for	(i = 0; i < n3; i++)
	{
		if (l3[i].selected == true && l3[i].value != '')
		{
			s3 += '['+l3[i].value+']';
		}
	}
	for	(i = 0; i < n4; i++)
	{
		if (l4[i].selected == true && l4[i].value != '')
		{
			s4 += '['+l4[i].value+']';
		}
	}
	f.perm_g_list.value = s1;
	f.perm_g_view.value = s2;
	f.perm_g_write.value = s3;
	f.perm_g_down.value = s4;
	if (f.name.value == '')
	{
		alert('게시판이름을 입력해 주세요.     ');
		f.name.focus();
		return false;
	}
	if (f.bid.value == '')
	{
		if (f.id.value == '')
		{
			alert('게시판아이디를 입력해 주세요.      ');
			f.id.focus();
			return false;
		}
		if (!chkFnameValue(f.id.value))
		{
			alert('게시판아이디는 영문 대소문자/숫자/_ 만 사용가능합니다.      ');
			f.id.value = '';
			f.id.focus();
			return false;
		}
	}
   if (confirm('정말로 실행하시겠습니까?         '))
		{
			getIframeForAction(f);
			f.submit();
		}
			
}
//]]>
</script>
