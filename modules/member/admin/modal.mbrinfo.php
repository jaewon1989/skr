<?php
$tab = $tab ? $tab : 'profile';
$_R1=getUidData($table['s_mbrid'],$uid);
$_R2=getDbData($table['s_mbrdata'],'memberuid='.$_R1['uid'],'*');
$_M=array_merge($_R1,$_R2);
if (!$_M['uid']) exit;
// 삭제파일명 지정 
if($tab=='post'||$tab=='notice') $del_file='multi_delete';
else $del_file=$tab.'_multi_delete';
?>

<ul class="nav nav-tabs" role="tablist">
   <li<?php if($tab=='profile'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=profile&amp;uid=<?php echo $_M['uid']?>">프로필</a></li>
	<li<?php if($tab=='post'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=post&amp;uid=<?php echo $_M['uid']?>">게시글</a></li>
	<li<?php if($tab=='comment'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=comment&amp;uid=<?php echo $_M['uid']?>">댓글</a></li>
	<li<?php if($tab=='oneline'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=oneline&amp;uid=<?php echo $_M['uid']?>">한줄의견</a></li>
	<li<?php if($tab=='scrap'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=scrap&amp;uid=<?php echo $_M['uid']?>">스크랩</a></li>
	<li<?php if($tab=='paper'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=paper&amp;uid=<?php echo $_M['uid']?>">쪽지</a></li>
 	<li<?php if($tab=='notice'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=notice&amp;uid=<?php echo $_M['uid']?>">알림</a></li> 
	<li<?php if($tab=='point'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=point&amp;uid=<?php echo $_M['uid']?>">포인트</a></li>
	<li<?php if($tab=='log'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=log&amp;uid=<?php echo $_M['uid']?>">접속기록</a></li>
	<li<?php if($tab=='info'):?> class="active"<?php endif?>><a href="<?php echo $g['adm_href']?>&amp;iframe=Y&amp;tab=info&amp;uid=<?php echo $_M['uid']?>">가입정보</a></li>	
</ul>
<!-- tab 내용 -->
<div class="tab-content">
	<div class="tab-pane active">
		    <?php include $g['path_module'].$module.'/admin/manager/'.$tab.'.php';?>			
	</div>
</div>
<!--@부모레이어를 제어할 수 있도록 모달의 헤더와 풋터를 부모레이어에 출력시킴-->
<div id="_modal_header" class="hidden">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title"><small class="label label-<?php echo $_M['now_log']?'primary':'default'?>" data-tooltip="tooltip" title="<?php echo $_M['now_log']?'온라인':'오프라인'?><?php echo $_M['admin']?($_M['adm_view']?'부관리자':'최고관리자'):'일반회원'?>"></small> <?php echo sprintf('<strong>%s</strong> 님의 정보',$_M['name'])?> </h4>
</div>
<div id="_modal_footer" class="hidden">
	<?php if($tab=='info'):?>
	<button type="submit" class="btn btn-primary pull-left" onclick="frames._modal_iframe_modal_window.saveCheck();">정보 수정하기</button>
	<?php elseif($tab!='log' && $tab!='profile'):?>
	<button type="button" class="btn btn-danger btn-sm pull-left act-btn" onclick="frames._modal_iframe_modal_window.actCheck('<?php echo $del_file?>','<?php echo $tab?>');">삭제</button>
	<?php endif?>
	<button id="_close_btn_" type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
</div>

<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<style type="text/css">
.datepicker {z-index: 1151 !important;}
</style>
<script>
 // 툴팁 이벤트 
$(document).ready(function() {
       $('[data-toggle=tooltip]').tooltip();
 }); 

$('.input-daterange').datepicker({
	format: "yyyy/mm/dd",
	todayBtn: "linked",
	language: "kr",
	calendarWeeks: true,
	todayHighlight: true,
	autoclose: true
});

// 선택박스 체크 이벤트 핸들러
$(".checkAll-act-list").click(function(){
	$(".mbr-act-list").prop("checked",$(".checkAll-act-list").prop("checked"));
	checkboxCheck();
});

// tab 으로 리스트 체크박스명과 모듈명 추출함수
function getTabData(tab,val)
{
	var m; // 모듈 
	var ck; // 체크박스
   if(tab=='post' || tab=='comment' || tab=='oneline' ){
    	m='bbs'; // 최종적으로 모듈명을 넘겨준다. 
    	ck = document.getElementsByName(tab+'_members[]');
   }else if(tab=='scrap'|| tab=='paper'|| tab=='point'){
   	m='member';
      ck= document.getElementsByName('members[]');
   }else if(tab=='notice'){
   	m='notification';
   	ck = document.getElementsByName('noti_members[]');
   }
   var result={"m":m,"ck":ck,};

   return result[val];
}  

// 선택박스 체크시 액션버튼 활성화 함수
function checkboxCheck()
{
	var tab=$('input[name="tab"]').val(); 
 	var f = document.adm_list_form;
	var l =getTabData(tab,'ck'); // 체크박스명 얻기  
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
	if (j) $('.act-btn').prop("disabled",false);
	else $('.act-btn').prop("disabled",true);
}

function actCheck(act,tab)
{
	var l; // tab 에 따라서 list 엘리먼트 name 이 바뀐다. 
	var f = document.adm_list_form; // 모든 리스트의 공통 form
   var 	l = getTabData(tab,'ck');
   var n = l.length;
	var j = 0;
   var i;
   var msg;

   // 최종적으로 모듈명을 넘겨준다. 
   f.m.value=getTabData(tab,'m'); 
   
   for (i = 0; i < n; i++)
	{
		if(l[i].checked == true)
		{
			j++;	
		}
	}
	if (!j)
	{
		alert('선택된 리스트가 없습니다.      ');
		return false;
	}
   msg='정말로 삭제하시겠습니가? ';
	if(confirm(msg))
	{		
	 	f.a.value = act;
	   getIframeForAction(f);
		f.submit();
	}
}
</script>
<!-- timeago -->
<?php getImport('jquery-timeago','jquery.timeago',false,'js')?>
<?php getImport('jquery-timeago','locales/jquery.timeago.ko',false,'js')?>
<script>
jQuery(document).ready(function() {
	$(".rb-update time").timeago();
});
</script>   


<script>
$("#checkAll-perm").click(function(){
	$(".rb-module-perm").prop("checked",$("#checkAll-perm").prop("checked"))
})
var submitFlag = false;
function sendCheck(id,t)
{
	var f = document.actionform;
	var f1 = document.addForm;

	if (submitFlag == true)
	{
		alert('응답을 기다리는 중입니다. 잠시 기다려 주세요.');
		return false;
	}
	if (eval("f1."+t).value == '')
	{
		eval("f1."+t).focus();
		return false;
	}
	f.type.value = t;
	f.fvalue.value = eval("f1."+t).value;
	getId(id).innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
	getIframeForAction(f);
	f.submit();
	submitFlag = true;
}
function saveCheck()
{
	var f = document.addForm;
	if (f.pw1.value != f.pw2.value)
	{
		alert('비밀번호가 서로 일치하지 않습니다. ');
		return false;
	}
	if (f.name.value == '')
	{
		alert('이름을 입력해 주세요.   ');
		f.name.focus();
		return false;
	}
	if (f.nic.value == '')
	{
		alert('닉네임을 입력해 주세요.   ');
		f.nic.focus();
		return false;
	}
	if (f.email.value == '')
	{
		alert('이메일을 입력해 주세요.   ');
		f.email.focus();
		return false;
	}
	if (confirm('정말로 수정하시겠습니까?    '))
	{
		getIframeForAction(f);
		f.submit();
	}
	return false;
}
function saveCheck1()
{
	var f = document.permForm;
	if (confirm('정말로 수정하시겠습니까?    '))
	{
		getIframeForAction(f);
		f.submit();
	}
	return false;
}
function modalSetting()
{
	var ht = document.body.scrollHeight - 55;

	parent.getId('modal_window_dialog_modal_window').style.width = '100%';
	parent.getId('modal_window_dialog_modal_window').style.paddingRight = '20px';
	parent.getId('modal_window_dialog_modal_window').style.maxWidth = '900px';
	parent.getId('_modal_iframe_modal_window').style.height = ht+'px'
	parent.getId('_modal_body_modal_window').style.height = ht+'px';

	parent.getId('_modal_header_modal_window').innerHTML = getId('_modal_header').innerHTML;
	parent.getId('_modal_header_modal_window').className = 'modal-header';
	parent.getId('_modal_body_modal_window').style.padding = '0';
	parent.getId('_modal_body_modal_window').style.margin = '0';

	parent.getId('_modal_footer_modal_window').innerHTML = getId('_modal_footer').innerHTML;
	parent.getId('_modal_footer_modal_window').className = 'modal-footer';
}
document.body.onresize = document.body.onload = function()
{
	setTimeout("modalSetting();",100);
	setTimeout("modalSetting();",200);
}
</script>
