<?php
$SITES = getDbArray($table['s_site'],'','*','gid','asc',0,1);

$type	= $type ? $type : 'point';
$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

//사이트선택적용
//$accountQue = $account ? 'a.site='.$account.' and ':'';
$_WHERE ='uid>0';
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($flag == '+') $_WHERE .= ' and price > 0';
if ($flag == '-') $_WHERE .= ' and price < 0';

if ($where && $keyw)
{
	if ($keyw=='my_mbruid') $_WHERE .= ' and my_mbruid='.$keyw;
	else $_WHERE .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table['s_'.$type],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_'.$type],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>
<style>
#rb-body .search-area .btn {font-size: 12px}
.btn-group > .btn-page {margin-left: -1px;}
.panel .panel-heading {
    background: linear-gradient(to bottom, #ffffff 0%, #f5f5f5 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
}
.panel {
    background: none repeat scroll 0 0 #fefefe;
    border: 1px solid #c9c9c9;
    box-shadow: 1px 1px 0 0 rgba(222, 222, 222, 0.1) inset, 1px 1px 0 0 rgba(255, 255, 255, 1);
    margin-bottom: 1px;
    padding: 0;
    position: relative;
}
.panel .table th {
    background: linear-gradient(to bottom, #ffffff 0%, #f5f5f5 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
    box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.05);
    height: auto;
    position: relative;
    text-align: center;
}
</style>
<!-- 검색폼 -->
<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal rb-form">
	 <input type="hidden" name="r" value="<?php echo $r?>" />
	 <input type="hidden" name="m" value="<?php echo $m?>" />
	 <input type="hidden" name="module" value="<?php echo $module?>" />
	 <input type="hidden" name="front" value="<?php echo $front?>" />
	 <input type="hidden" name="type" value="<?php echo $type?>" />

	 <div class="rb-heading well well-sm search-area">
	 	 	 <div class="form-group">
					<label class="col-sm-1 control-label">기간</label>
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
	 	 	 <!-- 고급검색 시작 -->
	 	 	 <div id="search-more" class="collapse<?php if($_SESSION['sh_mbrlist']):?> in<?php endif?>">				
				 <div class="form-group">
					<label class="col-sm-1 control-label">필터</label>
					<div class="col-sm-10">
						<div class="row">
							 <div class="col-sm-2">
			 	 	  	  	       <select name="flag" class="form-control input-sm" onchange="this.form.submit();">
										<option value="">&nbsp;+ 구분</option>
										<option value="">--------</option>
										<option value="+"<?php if($flag=='+'):?> selected="selected"<?php endif?>>획득</option>
										<option value="-"<?php if($flag=='-'):?> selected="selected"<?php endif?>>사용</option>
									 </select>
							  </div>
							  <div class="col-sm-8 hidden-xs">
							  	   <div class="btn-toolbar">
											<div class="btn-group btn-group-sm" data-toggle="buttons">
												<label class="btn btn-default<?php if($sort=='uid'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
													<input type="radio" value="uid" name="sort"<?php if($sort=='uid'):?> checked<?php endif?>> 등록일
												</label>
												 <label class="btn btn-default<?php if($sort=='price'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
													<input type="radio" value="price" name="sort"<?php if($sort=='price'):?> checked<?php endif?>>금액
												</label>						
											</div>
											<div class="btn-group btn-group-sm" data-toggle="buttons">
												<label class="btn btn-default<?php if($orderby=='desc'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
													<input type="radio" value="desc" name="orderby"<?php if($orderby=='desc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-desc"></i>역순
												</label>
												<label class="btn btn-default<?php if($orderby=='asc'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
													<input type="radio" value="asc" name="orderby"<?php if($orderby=='asc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-asc"></i>정순
												</label>
											</div>
										</div>
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
									<option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>내용</option>
		                      <option value="my_mbruid"<?php if($where=='my_mbruid'):?> selected="selected"<?php endif?>>회원코드</option>
								</select>
							</span>
							<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">검색</button>
							</span>
						</div>
					</div>
			</div>
		</div> <!-- 고급검색 -->
			<div class="form-group">
				<div class="col-sm-offset-1 col-sm-10">
					<button type="button" class="btn btn-link rb-advance<?php if(!$_SESSION['sh_mbrlist']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#search-more" onclick="sessionSetting('sh_mbrlist','1','','1');">고급검색<small></small></button>
					<a href="<?php echo $g['adm_href']?>" class="btn btn-link">초기화</a>
				</div>
			</div>
		
	</div>	
</form>
<!-- //검색폼 -->

<div class="panel panel-default table-responsive">
	<div class="panel-heading btn-toolbar">
		<span class="pull-left">
			 총<code><?php echo number_format($NUM)?></code>개 (<?php echo $p?>/<?php echo $TPG?>페이지)
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
	<input type="hidden" name="a" value="" />
	<input type="hidden" name="pointType" value="<?php echo $type?>" />
 
   <!-- 리스트 테이블 시작-->
 	<table class="table table-hover">
		<thead>
			<tr>
				<th class="text-center"><input type="checkbox"  class="checkAll-point-member" data-toggle="tooltip" title="전체선택"></th>
				<th class="text-center">번호</th>
				<th class="text-center">획득/사용자</th>
				<th class="text-center">획득/사용액</th>
				<th>지금자</th>
				<th>내용</th>
				<th>날짜</th>
		   </tr>
		</thead>
		<tbody>
			<?php while($R=db_fetch_array($RCD)):?>
        	<?php $M1=getDbData($table['s_mbrdata'],'memberuid='.$R['my_mbruid'],'*')?>
	     <?php if($R['by_mbruid']){$M2=getDbData($table['s_mbrdata'],'memberuid='.$R['by_mbruid'],'*');}else{$M2=array();}?>
			<tr>	<!-- 라인이 체크된 경우 warning 처리됨  -->
				<td class="text-center"><input type="checkbox" name="point_mbrmembers[]"  onclick="checkboxCheck();" class="rb-poin-member" value="<?php echo $R['uid']?>"></td>
				<td class="text-center"><?php echo ($NUM-((($p-1)*$recnum)+$_recnum++))?></td>
				<td><a href="#" data-toggle="modal" data-target="#modal_window" class="rb-modal-mbrinfo" onmousedown="mbrIdDrop('<?php echo $M1['uid']?>','point');" data-toggle="tooltip" title="획득/사용내역"><?php echo $M1[$_HS['nametype']]?></a></td><!-- main -->
			   <td><?php echo number_format($R['price'])?></td>
				<td>
					<?php if($M2['memberuid']):?>
					 <a href="#" data-toggle="modal" data-target="#modal_window" class="rb-modal-mbrinfo" onmousedown="mbrIdDrop('<?php echo $M2['uid']?>','point');" data-toggle="tooltip" title="획득/사용내역"><?php echo $M1[$_HS['nametype']]?></a></td><!-- post -->
				   <?php else:?>
				   	시스템
			 		<?php endif?>
            </td>
            <td><?php echo strip_tags($R['content'])?></td>
			   <td><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>	
         </tr>
         <?php endwhile?>
		</tbody>
	</table>

    <!-- 리스트 테이블 끝 -->

    <?php if($NUM):?>
   <!--목록에 체크된 항목이 없을 경우  fieldset이 disabled 됨-->
	<div class="panel-footer btn-toolbar">
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
		<i class="fa fa-exclamation-circle fa-lg"></i> 조건에 해당하는 회원이 없습니다.
	</div>
	<?php endif?>
</div>  <!-- // .panel-->

<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
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
//<![CDATA[
// 툴팁 이벤트 
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
}); 

// 선택박스 체크 이벤트 핸들러
$(".checkAll-point-member").click(function(){
	$(".rb-poin-member").prop("checked",$(".checkAll-point-member").prop("checked"));
	checkboxCheck();
});
// 선택박스 체크시 액션버튼 활성화 함수
function checkboxCheck()
{
	var f = document.listForm;
    var l = document.getElementsByName('mbrmembers[]');
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
	// 하단 회원관리 액션 버튼 상태 변경
	if (j) $('#list-bottom-fset').prop("disabled",false);
	else $('#list-bottom-fset').prop("disabled",true);
}

function actQue(flag,ah)
{
	var f = document.listForm;
    var l = document.getElementsByName('mbrmembers[]');
    var n = l.length;
    var i;
	var j=0;
	
	if (flag == 'admin_delete')
	{
		for	(i = 0; i < n; i++)
		{
			if (l[i].checked == true)
			{
				j++;
			}
		}
		if (!j)
		{
			alert('회원을 선택해주세요.     ');
			return false;
		}

		if (confirm('정말로 실행하시겠습니까?     '))
		{
			getIframeForAction(f);
			f.a.value = flag;
			f.auth.value = ah;
			f.submit();
		}
	}
	return false;
}


// 기간 검색 적용 함수
function dropDate(date1,date2)
{
	var f = document.procForm;
	f.d_start.value = date1;
	f.d_finish.value = date2;
	f.submit();
}

</script>
