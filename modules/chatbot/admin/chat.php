<?php
include $g['path_module'].$module.'/includes/base.class.php';
include $g['path_module'].$module.'/includes/ad.class.php';
$Ad = new Ad();

$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

$_WHERE ='uid>0';
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($ad_start) $_WHERE .= ' and d_start >= '.str_replace('/','',$ad_start).'000000';
if ($ad_finish) $_WHERE .= ' and d_end <= '.str_replace('/','',$ad_finish).'240000';

// if($cat){
// 	$CID = getDbSelect($table[$module.'catidx'],'category='.$cat,'category,post');
//     $_WHERE2 .=' and (';
//     while($C=db_fetch_array($CID)){
//     	$_WHERE2 .='uid='.$C['post'].' or';
//     }
//     $_WHERE .=rtrim($_WHERE2,' or').')';
// }
if($auth) $_WHERE .= ' and auth='.$auth;
if($cat) $_WHERE .= " and category='".$cat."'";
if($sido) $_WHERE .= " and sido='".$sido."'";
if($gugun) $_WHERE .= " and gugun='".$gugun."'";
if($dong) $_WHERE .= " and dong='".$dong."'";

if($where && $keyw) $_WHERE .= " and ".$where." like '%".trim($keyw)."%'";

$RCD = getDbArray($table[$module.'post'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'post'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);

$xyear1	= substr($date['totime'],0,4);
$xmonth1= substr($date['totime'],4,2);
$xday1	= substr($date['totime'],6,2);
$xhour1	= substr($date['totime'],8,2);
$xmin1	= substr($date['totime'],10,2);

$autharr = array('대기','승인','보류','거절');
$dsparr = array('숨김','노출');

?>
<style>
.panel .table>tbody>tr>td {
   text-align: center;
}
</style>
<!-- 검색폼 -->
<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal rb-form">
	 <input type="hidden" name="r" value="<?php echo $r?>" />
	 <input type="hidden" name="m" value="<?php echo $m?>" />
	 <input type="hidden" name="module" value="<?php echo $module?>" />
	 <input type="hidden" name="front" value="<?php echo $front?>" />
      
	 <div class="rb-heading well well-sm search-area" style="position:relative;">
	 	
	 	 	 <div class="form-group">
					<label class="col-sm-1 control-label">등록기간</label>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-4">
								<div class="input-daterange input-group input-group-sm" id="datepicker">
									<input type="text" class="form-control" name="d_start" placeholder="시작일 선택" value="<?php echo $d_start?>">
									<span class="input-group-addon">~</span>
									<input type="text" class="form-control" name="d_finish" placeholder="종료일 선택" value="<?php echo $d_finish?>">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit">기간적용</button>
									</span>
								</div>
							</div>
							<div class="col-sm-3 hidden-xs">
								<span class="input-group-btn">
									<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');">어제</button>
									<button class="btn btn-default" onclick="dropDate('<?php echo getDateFormat($date['today'],'Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">오늘</button>
									<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">일주</button>
									<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">한달</button>
									<button class="btn btn-default" onclick="dropDate('<?php echo getDateFormat(substr($date['today'],0,6).'01','Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">당월</button>
									<button class="btn btn-default" onclick="dropDate('<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');">전월</button>
									<button class="btn btn-default" onclick="dropDate('','');">전체</button>
								</span>
							</div>							
						</div>
					</div>
			</div>
			<div class="form-group">
					<label class="col-sm-1 control-label">광고기간</label>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-4">
								<div class="input-daterange input-group input-group-sm" id="datepicker">
									<input type="text" class="form-control" name="ad_start" placeholder="시작일 선택" value="<?php echo $ad_start?>">
									<span class="input-group-addon">~</span>
									<input type="text" class="form-control" name="ad_finish" placeholder="종료일 선택" value="<?php echo $ad_finish?>">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit">기간적용</button>
									</span>
								</div>
							</div>
							<div class="col-sm-3 hidden-xs">
								<span class="input-group-btn">
									<button class="btn btn-default" onclick="dropDate2('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');">어제</button>
									<button class="btn btn-default" onclick="dropDate2('<?php echo getDateFormat($date['today'],'Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">오늘</button>
									<button class="btn btn-default" onclick="dropDate2('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">일주</button>
									<button class="btn btn-default" onclick="dropDate2('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">한달</button>
									<button class="btn btn-default" onclick="dropDate2('<?php echo getDateFormat(substr($date['today'],0,6).'01','Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">당월</button>
									<button class="btn btn-default" onclick="dropDate2('<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');">전월</button>
									<button class="btn btn-default" onclick="dropDate2('','');">전체</button>
								</span>
							</div>							
						</div>
					</div>
			</div>
	 			
			<div class="form-group">
				<label class="col-sm-1 control-label">분류</label>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-2">
		 	 	  	  	    <select name="auth" class="form-control input-sm" onchange="this.form.submit();" data-toggle="tooltip" title="광고인증">
						   	     <option value="">광고인증</option>
								 <option value="">--------</option>
								 <option value="0"<?php if($auth == 0):?> selected="selected"<?php endif?>><?php echo $autharr[0]?></option>
								 <option value="1"<?php if($auth == 1):?> selected="selected"<?php endif?>><?php echo $autharr[1]?></option>
								 <option value="2"<?php if($auth == 2):?> selected="selected"<?php endif?>><?php echo $autharr[2]?></option>
								 <option value="3"<?php if($auth == 3):?> selected="selected"<?php endif?>><?php echo $autharr[3]?></option>
							</select>
						</div>
						<div class="col-sm-2">
	 	 	  	  	        <select name="cat"  class="form-control input-sm" onchange="this.form.submit();" data-toggle="tooltip" title="카테고리">
								<option value="">카테고리</option>
								<option value="">--------</option>
								<?php $CAT = getDbArray($table[$module.'category'],'hidden=0','uid,name','gid','asc',0,1)?>
								<?php while($C=db_fetch_array($CAT)):?>
								<option value="<?php echo $C['uid']?>"<?php if($C['uid']==$cat):?> selected="selected"<?php endif?>><?php echo $C['name']?></option>
								<?php endwhile?>
							</select>
						</div>
						<div class="col-sm-2">
		 	 	  	        <select name="sido"  class="form-control input-sm" onchange="this.form.submit();" data-toggle="tooltip" title="시/도">
								<option value="">시/도</option>
								<option value="">--------</option>
								<?php $adq=db_query("select sido from ".$table[$module.'zipcode']." where 1 group by sido",$DB_CONNECT);?>
								<?php while($AR=db_fetch_array($adq)):?>
								<option value="<?php echo $AR['sido']?>"<?php if($AR['sido']==$sido):?> selected="selected"<?php endif?>><?php echo $AR['sido']?></option>
								<?php endwhile?>
							</select>
						</div>
						<div class="col-sm-2">
		 	 	  	        <select name="gugun"  class="form-control input-sm" onchange="this.form.submit();" data-toggle="tooltip" title="구/군">
								<option value="">구/군</option>
								<option value="">--------</option>
								<?php $adq=db_query("select gugun from ".$table[$module.'zipcode']." where sido='".$sido."' group by gugun",$DB_CONNECT);?>
								<?php while($AR=db_fetch_array($adq)):?>
								<option value="<?php echo $AR['gugun']?>"<?php if($AR['gugun']==$gugun):?> selected="selected"<?php endif?>><?php echo $AR['gugun']?></option>
								<?php endwhile?>
							</select>
						</div>
                        <div class="col-sm-2">
		 	 	  	        <select name="dong"  class="form-control input-sm" onchange="this.form.submit();" data-toggle="tooltip" title="동">
								<option value="">동</option>
								<option value="">--------</option>
								<?php $adq=db_query("select dong from ".$table[$module.'zipcode']." where sido='".$sido."' and gugun='".$gugun."' group by dong",$DB_CONNECT);?>
								<?php while($AR=db_fetch_array($adq)):?>
								<option value="<?php echo $AR['dong']?>"<?php if($AR['dong']==$dong):?> selected="selected"<?php endif?>><?php echo $AR['dong']?></option>
								<?php endwhile?>
							</select>
						</div>
					</div>
				</div>
			</div>
		   <div class="form-group">
				<label class="col-sm-1 control-label">검색</label>
				<div class="col-sm-8">
					<div class="input-group input-group-sm">
						<span class="input-group-btn hidden-xs" style="width:165px">
							<select name="where" class="form-control btn btn-default input-sm">
								<option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>이름</option>
								<option value="nic"<?php if($where=='nic'):?> selected="selected"<?php endif?>>닉네임</option>
								<option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>아이디</option>
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
		    <a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','addAdv');" class="pull-right btn-link rb-modal-blog" style="position:absolute;top: 15px;right: 15px"><i class="fa fa-plus"></i> 신규 등록</a>
	
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
	<input type="hidden" name="mod" value="post" />
	<input type="hidden" name="act" value="" />
	<input type="hidden" name="auth" value="" /> <!-- 승인값 추가 -->
	<input type="hidden" name="display" value="" /> <!-- 승인값 추가 -->

	<input type="hidden" name="_WHERE" value="<?php echo $_WHERE?>" />
	<input type="hidden" name="_num" value="<?php echo $NUM?>" />
	   <!-- 리스트 테이블 시작-->

	 	<table class="table table-hover" id="adList-table" style="border-bottom:solid 1px #ddd;">
			<thead>
				<tr>
					<th><input type="checkbox"  class="checkAll-member" data-toggle="tooltip" title="전체선택"></th>
					<th>번호</th>
					<th>승인</th>
					<th>상태</th>					
				    <th>분류</th>
					<th>타입</th>
					<th>이미지</th>
					<th>광고업체</th>
					<th>제목</th>
					<th>콜수</th>
					<th>광고주</th>
					<th>광고기간</th>
					<th>시/도</th>
					<th>구/군</th>
					<th>동</th>	
					<th>등록일</th>
	                <th>관리</th>			
	   		   </tr>
			</thead>
			<tbody>
				<?php while($R=db_fetch_array($RCD)):?>
				<?php
                  $ad_start = substr($R['d_start'],0,4).'/'.substr($R['d_start'],4,2).'/'.substr($R['d_start'],6,2);
	              $ad_end = substr($R['d_end'],0,4).'/'.substr($R['d_end'],4,2).'/'.substr($R['d_end'],6,2);
	              $ad_type_print = $Ad->getAdTypePrint($R['ad_type']);
	              $f_img_src = $Ad->getAdFeaturedImgSrc($module,$R['uid']);
	              // category 정보
				  $cat_info = $Ad->getCatInfo($R['category']); 
				  $category = $cat_info['category'];
				  $cat_name = $cat_info['name'];
				  $CP = getUidData($table[$module.'company'],$R['company']);
				?>
	  
				<tr>	<!-- 라인이 체크된 경우 warning 처리됨  -->
					<td><input type="checkbox" name="list_members[]"  onclick="checkboxCheck();" class="rb-member" value="<?php echo $R['uid']?>"></td>
					<td><?php echo ($NUM-((($p-1)*$recnum)+$_recnum++))?></td>
					<td><?php echo $autharr[$R['auth']]?></td>
					<td><?php echo $dsparr[$R['display']]?></td>					
				    <td><?php echo $cat_name?></td>
					<td><?php echo $ad_type_print ?></td> <!-- Ad.class.php : getAdTypePrint() 참조 -->
					<td><img src="<?php echo $f_img_src?>" class="thumbnail" width="50" height="50"/></td>
					<td><?php echo $CP['subject']?></td>
					<td><?php echo $R['subject']?></td>
					<td><?php echo number_format($R['hit'])?></td>
					<td><a href="#" data-toggle="modal" data-target="#modal_window" class="rb-modal-mbrinfo" onmousedown="mbrIdDrop('<?php echo $R['mbruid']?>','profile');"><?php echo $R['name'].'('.$R['id'].')'?></a></td><!-- main -->
					<td><?php echo $ad_start.' ~ '.$ad_end?></td>
					<td><?php echo $R['sido']?></td>
					<td><?php echo $R['gugun']?></td>
					<td><?php echo $R['dong']?></td>
			  	    <td><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>	
				    <td>
                      <a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','addAdv');" class="btn btn-primary btn-sm rb-modal-blog">수정</a>
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
					<button type="button" class="btn btn-default dropdown-toggle act-btn" data-toggle="dropdown">
						<i class="fa fa-wrench"></i> 관리 <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li role="presentation" class="dropdown-header">광고승인 상태변경</li>
						<li><a href="#" class="adm-act" data-act="auth" data-val="0">대기</a></li>
						<li><a href="#" class="adm-act" data-act="auth" data-val="1">승인</a></li>
						<li><a href="#" class="adm-act" data-act="auth" data-val="2">보류</a></li>
						<li><a href="#" class="adm-act" data-act="auth" data-val="3">거절</a></li>
						<li role="presentation" class="dropdown-header">광고노출 상태변경</li>
						<li><a href="#" class="adm-act" data-act="display" data-val="0">숨김</a></li>
						<li><a href="#" class="adm-act" data-act="display" data-val="1">노출</a></li>
					</ul>
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
		<i class="fa fa-exclamation-circle fa-lg"></i> 조건에 해당하는 광고가 없습니다.
	</div>
	<?php endif?>
</div>  <!-- // .panel-->
<?php include $g['path_module'].$module.'/admin/_tool_modal.php';?>  <!-- 쪽지, 메일, 포인트 지급 모달 인클루드 : form 전에 위치해야 한다. -->
<?php include $g['path_module'].$module.'/admin/_modal.php';?>
<!-- 코드미러를 먼저 호출하고 난 후에 summernote 호출해야 코드미러가 적용이 됨-->
<!-- include summernote codemirror-->

<?php getImport('codemirror','codemirror',false,'css')?>
<?php getImport('codemirror','codemirror',false,'js')?>
<?php getImport('codemirror','theme/monokai',false,'css')?>
<?php getImport('codemirror','mode/htmlmixed/htmlmixed',false,'js')?>
<?php getImport('codemirror','mode/xml/xml',false,'js')?>

<!-- include summernote css/js-->
<?php getImport('summernote','dist/summernote.min',false,'js')?>
<?php getImport('summernote','lang/summernote-ko-KR',false,'js')?>
<?php getImport('summernote','dist/summernote',false,'css')?>


<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<style type="text/css">
.datepicker {z-index: 1151 !important;}
</style>
<!-- bootstrap Validator : 광고추가시 필요-->
<?php getImport('bootstrap-validator','dist/css/bootstrapValidator.min',false,'css')?>
<?php getImport('bootstrap-validator','dist/js/bootstrapValidator.min',false,'js')?>
<script>
var _advUid;
var _advFront;
function AdvUidSet(uid,front)
{
	_advUid = uid;
	_advFront=front;
}

// Adv 생성/수정 modal 호출하는 함수 : 위에서 지정한 Adv uid  로 호출 
$('.rb-modal-blog').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module=ad&amp;front=modal.default&amp;uid=')?>'+_advUid+'&amp;_front='+_advFront);
});

</script>

<!-- basic -->
<script>
$('.input-daterange').datepicker({
	format: "yyyy/mm/dd",
	todayBtn: "linked",
	language: "kr",
	calendarWeeks: true,
	todayHighlight: true,
	autoclose: true
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
	// 하단 광고관리 액션 버튼 상태 변경
	if (j) $('#list-bottom-fset').prop("disabled",false);
	else $('#list-bottom-fset').prop("disabled",true);
}

// 광고 이름,닉네임 클릭시 uid & mod( 탭 정보 : info, main, post 등) 지정하는 함수  
var _mbrModalUid;
var _mbrModalMod;
function mbrIdDrop(uid,mod)
{
	_mbrModalUid = uid;
	_mbrModalMod = mod;
}

// 광고정보 modal 호출하는 함수 : 위에서 지정한 광고 uid & mod 로 호출한다 .
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

// 광고기간 검색 적용 함수
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
