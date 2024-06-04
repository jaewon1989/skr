<?php


$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$_WHERE ='uid>0';

if($cat) $_WHERE .= " and quesCat like '%/".$cat."/%'"; // 카테고리 등록시 '/1/2/3/' 와 같은 형태로 등록한다.
if($vendor) $_WHERE .= " and vendor='".$vendor."'";
if($r_type) $_WHERE .= " and r_type='".$r_type."'";
if($language) $_WHERE .= " and lang='".$language."'";

if($where && $keyw) $_WHERE .= " and ".$where." like '%".trim($keyw)."%'";

$RCD = getDbArray($table[$module.'ruleC'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'ruleC'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);


?>

<form name="xlsUploadForm" id="xlsUploadForm" action="/" method="post" target="_action_frame_<?php echo $module?>" enctype="multipart/form-data">	
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
    <input type="hidden" name="a" value="_admin/regis_questionC_by_excel">
    <span style="display:none;"><input type="file" name="xlsfile" id="input-regis-q" /></span>
</form>
<!-- 검색폼 -->
<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal rb-form">
	 <input type="hidden" name="r" value="<?php echo $r?>" />
	 <input type="hidden" name="m" value="<?php echo $m?>" />
	 <input type="hidden" name="module" value="<?php echo $module?>" />
	 <input type="hidden" name="front" value="<?php echo $front?>" />
      
	 <div class="rb-heading well well-sm search-area" style="position:relative;">
		
	   <div class="form-group">
			<label class="col-sm-1 control-label">검색</label>
			<div class="col-sm-8">
				<div class="input-group input-group-sm">
					<span class="input-group-btn hidden-xs" style="width:165px">
						<select name="where" class="form-control btn btn-default input-sm">
							<option value="reply"<?php if($where=='reply'):?> selected="selected"<?php endif?>>내용</option>
							<option value="morpheme"<?php if($where=='morpheme'):?> selected="selected"<?php endif?>>형태소</option>

						</select>
					</span>
					<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
					<span class="input-group-btn">
						<button class="btn btn-default" type="submit">검색</button>
						<a href="<?php echo $g['adm_href']?>" class="btn btn-default">초기화</a>
					</span>
				</div>
			</div>
	    </div>
	    <a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','addQuestionC');" class="pull-right btn-link rb-modal-blog" style="position:absolute;top: 15px;right: 15px"><i class="fa fa-plus"></i> 신규 등록</a>
	    <button type="button" id="btn-regis" class="btn btn-default btn-sm" style="position:absolute;top: 10px;right: 110px"><i class="fa fa-file-excel-o" aria-hidden="true"></i> CSV 파일 등록</button>
	
	</div>	
</form>
<!-- //검색폼 -->

<div class="panel panel-default table-responsive">
	<div class="panel-heading btn-toolbar">
		<span class="pull-left">
			 총<code><?php echo number_format($NUM)?></code>건 (<?php echo $p?>/<?php echo $TPG?>페이지)
		</span>

		<div class="btn-group pull-right">
		
         <a href="<?php echo '/?'.$_SERVER['QUERY_STRING']?>&amp;p=<?php echo $p-1?>" class="btn btn-default btn-page" <?php echo $p>1?'':'disabled'?> data-toggle="tooltip" data-placement="bottom" title="" data-original-title="이전">
            <i class="fa fa-chevron-left fa-lg"></i>
         </a>
         <a href="<?php echo '/?'.$_SERVER['QUERY_STRING']?>&amp;p=<?php echo $p+1?>" class="btn btn-default btn-page" <?php echo $NUM>($p*$recnum)?'':'disabled'?> data-toggle="tooltip" data-placement="bottom" title="" data-original-title="다음">
            <i class="fa fa-chevron-right fa-lg"></i>
          </a> 
      </div>
		<div class="btn-group pull-right">
			 <div class="btn-group dropup hidden-xs">
		      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
		        <i class="fa fa-list"></i> <?php echo $recnum?>개씩  <span class="caret"></span>
		       </button> 
		      <ul class="dropdown-menu pull-right" role="menu">
		        <li <?php $recnum=='20'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=20">20개 출력</a></li>
		        <li <?php $recnum=='35'?'class="active"':''?>> <a href="<?php echo $g['adm_href']?>&amp;recnum=35">35개 출력</a></li>
		        <li <?php $recnum=='50'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=50">50개 출력</a></li>
		        <li <?php $recnum=='75'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=75">75개 출력</a></li>
		        <li <?php $recnum=='90'?'class="active"':''?>><a href="<?php echo $g['adm_href']?>&amp;recnum=90">90개 출력</a></li>
		      </ul>
		    </div>
	   </div>
	</div>  
	<!-- //.panel-heading -->
   <form name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="admin_action" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	<input type="hidden" name="act" value="" />

	<input type="hidden" name="_WHERE" value="<?php echo $_WHERE?>" />
	<input type="hidden" name="_num" value="<?php echo $NUM?>" />
	   <!-- 리스트 테이블 시작-->

	 	<table class="table table-hover" id="adList-table" style="border-bottom:solid 1px #ddd;">
			<thead>
				<tr>
			       <th><input type="checkbox"  class="checkAll-member" data-toggle="tooltip" title="전체선택"></th>
					<th>번호</th>
				  	<th>질문</th>
					<th>형태소</th>
					<th>답변</th>
		            <th>관리</th>			
	   		   </tr>
			</thead>
			<tbody>
	  	        <?php while($R=db_fetch_array($RCD)):?>
				<?php $VND = getDbData($table[$module.'vendor'],'uid='.$R['vendor'],'uid,name');?>
			    <tr>	<!-- 라인이 체크된 경우 warning 처리됨  -->
					<td><input type="checkbox" name="list_members[]"  onclick="checkboxCheck();" class="rb-member" value="<?php echo $R['uid']?>"></td>
					<td><?php echo ($NUM-((($p-1)*$recnum)+$_recnum++))?></td>
					<td><?php echo $R['question']?></td>
					<td><?php echo $R['pattern']?></td>
					<td><?php echo $R['reply']?></td>
				    <td>
                      <a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','addQuestionC');" class="btn btn-primary btn-sm rb-modal-blog">수정</a>
				    </td>					
					  
	            </tr>
	            <?php endwhile?>
			</tbody>
		</table>

	    <!-- 리스트 테이블 끝 -->

    <?php if($NUM):?>
   <!--목록에 체크된 항목이 없을 경우  fieldset이 disabled 됨-->
	<div class="panel-footer btn-toolbar">
		<fieldset id="list-bottom-fset" disabled> <!--목록에 체크된 항목이 없을 경우  fieldset이 disabled 됨-->
			<div class="btn-group">
				<div class="btn-group dropup">
					<button type="button" class="btn btn-danger act-btn">
						<span class="adm-act" data-act="delete">
						    <i class="fa fa-trash"></i> 삭제</span>
					    </span>
					</button>
				</div>
			</div>
		</fieldset>
	    <div class="btn-group pull-right">
		
			
	    </div>
	    <div class="col-sm-12 text-center">
	    	  	<ul class="pagination pagination-sm">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				</ul>
       </div>
	</div> <!-- // .panel-footer-->
	
</form>

	<?php else:?>
	<hr>
	<div class="well text-center text-muted" style="margin-top:15px">
		<i class="fa fa-exclamation-circle fa-lg"></i> 조건에 해당하는 질문가 없습니다.
	</div>
	<?php endif?>

</div>  <!-- // .panel-->

<iframe name="_action_frame_chatbot" width="0" height="0" frameborder="0" scrolling="no"></iframe>

<script>

$(document).on('click','#btn-regis',function(){
    $('#input-regis-q').click();
});

$(document).on('change','#input-regis-q',function(){
  	
  	var f = document.xlsUploadForm;
  	var extarr = f.xlsfile.value.split('.');
	var filext = extarr[extarr.length-1].toLowerCase();
	var permxt = '[txt]';
	if (permxt.indexOf(filext) == -1)
	{
		alert('txt확장자의 CSV 파일만 등록할 수 있습니다.    ');
		return false;
	}else{
	   f.submit();	
	}	
    
    
});

var _advUid;
var _advFront;
function AdvUidSet(uid,front)
{
	_advUid = uid;
	_advFront= front;
}

// Adv 생성/수정 modal 호출하는 함수 : 위에서 지정한 Adv uid  로 호출 
$('.rb-modal-blog').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module='.$module.'&amp;front=modal.default&amp;uid=')?>'+_advUid+'&amp;_front='+_advFront);
});

</script>

<script type="text/javascript">

// 툴팁 이벤트 
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
}); 

// 선택박스 체크 이벤트 핸들러
$(".checkAll-member").click(function(){
	$(".rb-member").prop("checked",$(".checkAll-member").prop("checked"));
	checkboxCheck();
});

// 선택박스 체크시 액션버튼 활성화 함수
function checkboxCheck()
{
	var f = document.listForm;
    var l = document.getElementsByName('list_members[]');
    var n = l.length;
    var i;
	var j=0;

	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true){
          $(l[i]).parent().parent().addClass('warning'); // 선택된 체크박스 tr 강조표시
			j++;
		}else{
			$(l[i]).parent().parent().removeClass('warning'); 
		} 
	}
	// 하단 질문관리 액션 버튼 상태 변경
	if (j) $('#list-bottom-fset').prop("disabled",false);
	else $('#list-bottom-fset').prop("disabled",true);
}

// 질문 이름,닉네임 클릭시 uid & mod( 탭 정보 : info, main, post 등) 지정하는 함수  
var _mbrModalUid;
var _mbrModalMod;
function mbrIdDrop(uid,mod)
{
	_mbrModalUid = uid;
	_mbrModalMod = mod;
}

// 질문정보 modal 호출하는 함수 : 위에서 지정한 질문 uid & mod 로 호출한다 .
$('.rb-modal-mbrinfo').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module=member&amp;front=modal.mbrinfo&amp;uid=')?>'+_mbrModalUid+'&amp;tab='+_mbrModalMod);
});


// 등록기간 검색 적용 함수
function dropDate(date1,date2)
{
	var f = document.procForm;
	f.d_start.value = date1;
	f.d_finish.value = date2;
	f.submit();
}

// 질문기간 검색 적용 함수
function dropDate2(date1,date2)
{
	var f = document.procForm;
	f.ad_start.value = date1;
	f.ad_finish.value = date2;
	f.submit();
}

// 관리자 액션버튼 클릭 이벤트 
 $('.adm-act').on('click',function(e){
    e.preventDefault();
    var act_name=$(this).data('act');
    var act_val=$(this).data('val');
    // 액션 타입 분기              
    if(act_name=='auth') $('input[name="auth"]').val(act_val); // 승인값 입력
    else if(act_name=='display') $('input[name="display"]').val(act_val); // 승인값 입력

    actSend(act_name);
 });

var submitFlag = false;
function actSend(flag)
{

	var f = document.listForm;
    var l = document.getElementsByName('list_members[]');
    var n = l.length;
    var i;
	var j=0;
	var s='';

	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true)
		{
			j++;
			s += l[i].value +',';
		}
	}
	
	if (!j)
	{
		alert('선택된 리스트가 없습니다.      ');
		return false;
	}

	submitFlag = true;
	f.act.value = flag;
	f.submit();

}


</script>
