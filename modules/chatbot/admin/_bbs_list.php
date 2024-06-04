<?php
if($bbsid=='notice') $regis_file = 'addNotice';
else if($bbsid=='talkLog') $regis_file = 'addTalk';

$_WHERE="bbsid='".$bbsid."'"; // 부모 페이지에서 지정된다.

$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;

if($account) $_WHERE .=' and site='.$account;
if ($d_start) $_WHERE .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $_WHERE .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($bid) $_WHERE .= ' and bbs='.$bid;
if ($category) $_WHERE .= " and category ='".$category."'";
if ($notice) $_WHERE .= ' and notice=1';
if ($hidden) $_WHERE .= ' and hidden=1';
if ($where && $keyw)
{
	if (strstr('[name][nic][id][ip]',$where)) $_WHERE .= " and ".$where." like '%".$keyw."%'";
	else $_WHERE .= getSearchSql($where,$keyw,$ikeyword,'or');	
}

$RCD = getDbArray($table['bbsdata'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['bbsdata'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>
<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal rb-form">
	 <input type="hidden" name="r" value="<?php echo $r?>" />
	 <input type="hidden" name="m" value="<?php echo $m?>" />
	 <input type="hidden" name="module" value="<?php echo $module?>" />
	 <input type="hidden" name="front" value="<?php echo $front?>" />

	 <div class="rb-heading well well-sm" style="position:relative;">
   
				<div class="form-group">
					<label class="col-sm-1 control-label">기간</label>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-5">
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
									<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>','<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-1,substr($date['today'],0,4)))?>');">어제</button>
									<button class="btn btn-default" type="button" onclick="dropDate('<?php echo getDateFormat($date['today'],'Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">오늘</button>
									<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2),substr($date['today'],6,2)-7,substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">일주</button>
									<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/d',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">한달</button>
									<button class="btn btn-default" type="button" onclick="dropDate('<?php echo getDateFormat(substr($date['today'],0,6).'01','Y/m/d')?>','<?php echo getDateFormat($date['today'],'Y/m/d')?>');">당월</button>
									<button class="btn btn-default" type="button" onclick="dropDate('<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>01','<?php echo date('Y/m/',mktime(0,0,0,substr($date['today'],4,2)-1,substr($date['today'],6,2),substr($date['today'],0,4)))?>31');">전월</button>
									<button class="btn btn-default" type="button" onclick="dropDate('','');">전체</button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group hidden-xs">
					<label class="col-sm-1 control-label">정렬</label>
					<div class="col-sm-10">
						<div class="btn-toolbar">
							<div class="btn-group btn-group-sm" data-toggle="buttons">
								<label class="btn btn-default<?php if($sort=='gid'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
									<input type="radio" value="gid" name="sort"<?php if($sort=='gid'):?> checked<?php endif?>> 등록일
								</label>
								 <label class="btn btn-default<?php if($sort=='hit'):?> active<?php endif?>" onclick="btnFormSubmit(this);">
									<input type="radio" value="hit" name="sort"<?php if($sort=='hit'):?> checked<?php endif?>> 조회
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
				<div class="form-group">
					<label class="col-sm-1 control-label">검색</label>
					<div class="col-sm-10">
						<div class="input-group input-group-sm">
							<span class="input-group-btn hidden-xs" style="width:165px">
								<select name="where" class="form-control btn btn-default">
								   <option value="subject"<?php if($where=='subject'):?> selected="selected"<?php endif?>>제목</option>
									<option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>본문</option>
									<?php if($bbsid=='talkLog'):?>
									<option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>이름</option>
									<option value="nic"<?php if($where=='nic'):?> selected="selected"<?php endif?>>닉네임</option>
									<option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>아이디</option>
								    <?php endif?>
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
				<div class="form-group">
					<label class="col-sm-1 control-label">출력</label>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-2">
								<select name="recnum" onchange="this.form.submit();" class="form-control input-sm">
									<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20</option>
									<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35</option>
									<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50</option>
									<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75</option>
									<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90</option>
								</select>
							</div>
							<div class="col-sm-2">
							</div>
						</div>
					</div>
				</div>
                <a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','<?php echo $regis_file?>');" class="pull-right btn-link rb-modal-blog" style="position:absolute;top: 15px;right: 15px"><i class="fa fa-plus"></i> 신규 등록</a>

		</div>
</form>

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
	<form name="listForm" action="<?php echo $g['s']?>/" method="post">
		<input type="hidden" name="r" value="<?php echo $r?>">
		<input type="hidden" name="m" value="bbs">
		<input type="hidden" name="a" value="">

		<table class="table table-hover" style="border-bottom:solid 1px #ddd;">          
			<thead>
				<tr>
					<th><label data-tooltip="tooltip" title="선택"><input type="checkbox" class="checkAll-post-user"></label></th>
					<th>번호</th>
					<th>제목</th>
					<?php if($bbsid=='talkLog'):?>
					<th>이름</th>
					<th>아이디</th>
					<th>연락처</td> 
				    <?php endif?>
					<th>조회</th>
					<th>등록일</th>
					<th>관리</th>
				</tr>
			</thead>
	     <tbody>
			<?php while($R=db_fetch_array($RCD)):?>
			<?php $M = getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'tel2');?>
			<tr>
				<td><input type="checkbox" name="post_members[]" value="<?php echo $R['uid']?>" class="rb-post-user" onclick="checkboxCheck();"/></td>
				<td>
				    <?php echo $NUM-((($p-1)*$recnum)+$_rec++)?>
				</td>
				<td>
					<a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','<?php echo $regis_file?>');" class="rb-modal-blog btn btn-link">
					    <?php echo $R['subject']?>
				    </a>
				</td>
				<?php if($bbsid=='talkLog'):?>
                <td>
                	<?php echo $R[$_HS['nametype']]?>
                </td>
				<td><?php echo $R['id']?></td>
				<td><?php echo $M['tel2']?></td>
			    <?php endif?>
				<td><?php echo $R['hit']?></td>
				<td><?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?></td>
				<td style="text-align:center;">
					<a href="#"  data-toggle="modal" data-target="#modal_window" onmousedown="AdvUidSet('<?php echo $R['uid']?>','<?php echo $regis_file?>');" class="rb-modal-blog btn btn-primary btn-sm"> 수정</a>
				</td>
			</tr> 
	     <?php endwhile?> 
	    </tbody>
	</table>
    <?php if(!$NUM):?>
    	<div class="well text-center text-muted" style="margin-top:15px">
			<i class="fa fa-exclamation-circle fa-lg"></i> 조건에 해당하는 게시물이 없습니다.
		</div>
	<?php endif?>
		<div class="rb-footer clearfix">
			<div class="pull-right">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>	
			<div>
				<button type="button" onclick="chkFlag('post_members[]');checkboxCheck();" class="btn btn-default btn-sm">선택/해제 </button>
				<button type="button" onclick="actCheck('multi_delete');" class="btn btn-default btn-sm rb-action-btn" disabled>삭제</button>
			</div>
		</div>
	</form>
</div>

<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<style type="text/css">
.datepicker {z-index: 1151 !important;}
</style>
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

// 광고 등로 
var _advUid;
var _advFront;
function AdvUidSet(uid,front)
{
	_advUid = uid;
	_advFront=front;
}

// 회원 이름,닉네임 클릭시 uid & mod( 탭 정보 : info, main, post 등) 지정하는 함수  
var _mbrModalUid;
var _mbrModalMod;
function mbrIdDrop(uid,mod)
{
	_mbrModalUid = uid;
	_mbrModalMod = mod;
}

// 회원정보 modal 호출하는 함수 : 위에서 지정한 회원 uid & mod 로 호출한다 .
$('.rb-modal-mbrinfo').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module=member&amp;front=modal.mbrinfo&amp;uid=')?>'+_mbrModalUid+'&amp;tab='+_mbrModalMod);
});

// Adv 생성/수정 modal 호출하는 함수 : 위에서 지정한 Adv uid  로 호출 
$('.rb-modal-blog').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module=ad&amp;front=modal.default&amp;uid=')?>'+_advUid+'&amp;_front='+_advFront);
});
// 선택박스 체크 이벤트 핸들러
$(".checkAll-post-user").click(function(){
	$(".rb-post-user").prop("checked",$(".checkAll-post-user").prop("checked"));
	checkboxCheck();
});
// 선택박스 체크시 액션버튼 활성화 함수
function checkboxCheck()
{
	var f = document.listForm;
    var l = document.getElementsByName('post_members[]');
    var n = l.length;
    var i;
	var j=0;
	for	(i = 0; i < n; i++)
	{
		if (l[i].checked == true) j++;
	}
	if (j) $('.rb-action-btn').prop("disabled",false);
	else $('.rb-action-btn').prop("disabled",true);
}
// 기간 검색 적용 함수
function dropDate(date1,date2)
{
	var f = document.procForm;
	f.d_start.value = date1;
	f.d_finish.value = date2;
	f.submit();
}
function actCheck(act)
{
	var f = document.listForm;
    var l = document.getElementsByName('post_members[]');
    var n = l.length;
	var j = 0;
    var i;
	var s = '';
    for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;
			s += '['+l[i].value+']';
		}
	}
	if (!j)
	{
		alert('선택된 게시물이 없습니다.      ');
		return false;
	}

	if (act == 'multi_delete')
	{
		if(confirm('정말로 삭제하시겠습니까?    '))
		{
			getIframeForAction(f);
			f.a.value = act;
			f.submit();
		}
	}	
	return false;
}
//]]>
</script>
