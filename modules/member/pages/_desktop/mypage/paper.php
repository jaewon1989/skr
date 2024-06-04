<?php
$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 15;

if ($inbox == 3)
{
	$sqlque = 'by_mbruid='.$my['uid'];
}
else
{
	$sqlque = 'my_mbruid='.$my['uid'];
	if ($inbox) $sqlque .= " and inbox='".$inbox."'";
	if ($where && $keyw)
	{
		$sqlque .= getSearchSql($where,$keyw,$ikeyword,'or');
	}
}
if ($d_start) $sqlque .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $sqlque .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
$RCD = getDbArray($table['s_paper'],$sqlque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_paper'],$sqlque);
$TPG = getTotalPage($NUM,$recnum);

?>

<div id="page-profile">
	<?php include $g['dir_module_skin'].'_cover.php';?>
	<p>
		<small><?php echo sprintf('총 %d건',$NUM)?>  (<?php echo $p?>/<?php echo $TPG?> page<?php if($TPG>1):?>s<?php endif?>)</small>
	</p>

	<form name="searchForm" class="form-horizontal" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>">
		<?php if($_mod):?>
		<input type="hidden" name="mod" value="<?php echo $_mod?>" />
		<?php else:?>
		<input type="hidden" name="m" value="<?php echo $m?>" />
		<input type="hidden" name="front" value="<?php echo $front?>" />
		<?php endif?>
		<input type="hidden" name="page" value="<?php echo $page?>" />
		<input type="hidden" name="sort" value="<?php echo $sort?>" />
		<input type="hidden" name="orderby" value="<?php echo $orderby?>" />
		<input type="hidden" name="recnum" value="<?php echo $recnum?>" />
		<input type="hidden" name="type" value="<?php echo $type?>" />
		<input type="hidden" name="iframe" value="<?php echo $iframe?>" />
		<input type="hidden" name="skin" value="<?php echo $skin?>" />

	  <div class="well well-sm search-area">
	   	<div class="form-group">
	   		 <label class="col-sm-1 control-label">필터</label>
				 <div class="col-sm-11">	
						<div class="col-sm-4">
							<select name="inbox" class="form-control input-sm" onchange="this.form.submit();">
								<option value="">구분</option>
								<option value="1"<?php if($inbox==1):?> selected="selected"<?php endif?>>받은쪽지함</option>
								<option value="2"<?php if($inbox==2):?> selected="selected"<?php endif?>>쪽지보관함</option>
								<option value="3"<?php if($inbox==3):?> selected="selected"<?php endif?>>보낸쪽지함</option>
							</select>
						</div>
						<div class="col-sm-5 pull-right">
							<div class="input-daterange input-group input-group-sm" id="datepicker">
								<input type="text" class="form-control" name="d_start" placeholder="시작일" value="<?php echo $d_start?>">
								<span class="input-group-addon">~</span>
								<input type="text" class="form-control" name="d_finish" placeholder="종료일" value="<?php echo $d_finish?>">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
								</span>
							</div>
						</div>
				 </div> <!-- .col-sm-11 -->	 
			 </div><!-- .form-group -->	 
		</div> <!-- .panel -->
   </form>
	<form name="procForm" class="form-horizontal" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>"method="post">
	<input type="hidden" name="r" value="<?php echo $r?>">
   <input type="hidden" name="front" value="<?php echo $front?>">
	<input type="hidden" name="page" value="<?php echo $page?>">
	<input type="hidden" name="p" value="<?php echo $p?>">
	<input type="hidden" name="m" value="<?php echo $m?>">
	<input type="hidden" name="a" value=""> <!-- 액션명  -->	
	 <div class="table-responsive">
		<table class="table table-hover" style="border-bottom:#ccc solid 1px;">
				<thead>
					<tr>
				      <th><input type="checkbox"  class="checkAll-act-list" data-toggle="tooltip" title="전체선택"></th>
						<th>번호</th>
						<th><?php echo $inbox==3?'받는이':'보낸이'?></th>
						<th></th>
						<th>내용</th>
						<th>날짜</th>
					</tr>
				</thead>
				<tbody>
					<?php while($R=db_fetch_array($RCD)):?>
				  	<?php $R['content']=str_replace('&nbsp;',' ',$R['content'])?>
	            <?php $M=getDbData($table['s_mbrdata'],'memberuid='.$R[($inbox==3?'m':'b').'y_mbruid'],'*')?>
	            <?php $by_mbr=getDbData($table['s_mbrdata'],'memberuid='.$R['by_mbruid'],'*')?> <!-- 쪽지 보낸회원 정보 -->
					<tr>
						<td><input type="checkbox" name="members[]"  onclick="checkboxCheck();" class="mbr-act-list" value="<?php echo $R['uid']?>"></td>
						<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
						<td><?php echo $M[$_HS['nametype']]?$M[$_HS['nametype']]:'시스템'?></td>
						<td><i class="fa fa-paper" data-toggle="tooltip" title="<?php echo $R['d_read']?getDateFormat($R['d_read'],'Y.m.d H:i 열람'):'읽지않음'?>"></i></td>
						<td class="rb-sbj">
					      <?php if($my['uid']!=$R['by_mbruid']):?>  
					        <a href="#" class="reply-send" id="<?php echo $R['by_mbruid'].'-'.$by_mbr['email']?>"><?php echo getStrCut(strip_tags($R['content']),50,'..')?> </a>
                     <?php else:?>
                         <?php echo getStrCut(strip_tags($R['content']),50,'..')?> 
                     <?php endif?>
                       <?php if(getNew($R['d_regis'],24)):?><span class="label label-danger"><small>New</small></span><?php endif?>            
						</td>
		            <td class="rb-update">
							<time class="timeago" data-toggle="tooltip" datetime="<?php echo getDateFormat($R['d_regis'],'c')?>" data-tooltip="tooltip" title="<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?>"></time>	
						</td>
					</tr>
					<?php endwhile?>
				</tbody>
			</table>
		    <?php if(!$NUM):?>
	          <div class="rb-none">데이타가 없습니다.</div>
	       <?php endif?>
	 </div>
	</form>
	 <div class="text-center">
		 <span class="btn-group pull-left">
			   <button class="btn btn-default btn-sm act-btn" onclick="actCheck('paper_save');" disabled>보관</button>
			   <button class="btn btn-danger btn-sm act-btn" onclick="actCheck('paper_delete');" disabled>삭제</button>
		 </span>
	    <ul class="pagination pagination-sm" style="padding:0;margin:0">
	       <script type="text/javascript">getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
	     </ul>
	 </div>
</div>
<!-- 쪽지 답장 보내기 모달 -->
<div class="modal fade" id="reply-send" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form name="replyForm" class="form-horizontal" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>" method="post" onsubmit="return sendCheck(this,'');">
	<input type="hidden" name="r" value="<?php echo $r?>">
   <input type="hidden" name="front" value="<?php echo $front?>">
	<input type="hidden" name="page" value="<?php echo $page?>">
	<input type="hidden" name="p" value="<?php echo $p?>">
	<input type="hidden" name="m" value="<?php echo $m?>">
	<input type="hidden" name="rcvmbr" value="">	
	<input type="hidden" name="a" value="paper_send2"> <!-- 액션명  -->	
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title"><span class="label label-danger">쪽지</span> 전송</h4>
			</div>
			<div class="modal-body form-horizontal">
            <div class="form-group">
					<label class="col-sm-3 control-label">수신자</label>
					<div class="col-sm-8">
				      <span id="rcv-email"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">메세지 입력</label>
					<div class="col-sm-8">
				      <textarea  name="msg" class="form-control"></textarea>
					</div>
				</div>				
			</div>			
		   <div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">취소</button>
				<input type="submit" class="btn btn-primary" value="전송" />
			</div>
		</div>  <!--.modal-content-->
	</div> <!--.modal-dialog-->
</div> <!--.modal-->
<!-- 공통 스크립트 -->
<?php include $g['dir_module_skin'].'_common_script.php'?>
<script language="javascript">
//<![CDATA[

// 쪽지 답장 보내기 
$('.reply-send').on('click',function(e){
	e.preventDefault();
   var f=document.replyForm;
   var id=$(this).attr('id');
   var id_arr=id.split('-');
   var rcvmbr=id_arr[0]; // 수신자 uid 
   var rcvemail=id_arr[1];
   f.rcvmbr.value=rcvmbr;
   $('#rcv-email').text(rcvemail); // 수신자 이메일 입력
   $('#reply-send').modal();
});

function actCheck(act)
{
	var f = document.procForm;
    var l = document.getElementsByName('members[]');
    var n = l.length;
	var j = 0;
    var i;

    for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;	
		}
	}
	if (!j)
	{
		alert('선택된 항목이 없습니다.      ');
		return false;
	}
	
	if(confirm('정말로 실행하시겠습니까?    '))
	{
		f.a.value = act;
		f.submit();
	}
}

// 쪽지 전송 체크 
function sendCheck(f,mod)
{
	if(mod=='multi')
	{
		if (f.id.value == '')
		{
			alert('받는사람 이메일이나 아이디를 입력해 주세요. ');
			f.id.focus();
			return false;
		}
		if (f.subject.value == '')
		 {
				alert('제목을 입력해 주세요. ');
				f.subject.focus();
				return false;
		 }		
	}
	
	if (f.msg.value == '')
	{
		alert('메세지를 입력해 주세요. ');
		f.msg.focus();
		return false;
	}
     getIframeForAction(f);
}

 //]]>  
</script>

