<!-- timeago -->
<?php getImport('jquery-timeago','jquery.timeago',false,'js')?>
<?php getImport('jquery-timeago','locales/jquery.timeago.ko',false,'js')?>
<script>
jQuery(document).ready(function() {
	$(".rb-update time").timeago();
});
</script>   
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<style type="text/css">
.datepicker {z-index: 1151 !important;}
</style>
<script language="javascript">
//<![CDATA[

// 이미지 변경 메뉴 클릭시 이벤트
$('.change-img').on('click',function(e){
   e.preventDefault();
   var id=$(this).attr('id');
   $('#mbr-'+id).click();
});
// 툴팁 
$('[data-toggle=tooltip]').tooltip();

// datepicker 세팅 
$('.input-daterange').datepicker({
	format: "yyyy/mm/dd",
	todayBtn: "linked",
	language: "kr",
	calendarWeeks: true,
	todayHighlight: true,
	autoclose: true
});

// 세션 생성
function sessionSetting2(name,value,target,check)
{
	getIframeForAction('');
	frames.__iframe_for_action__.location.href = rooturl + '/?r='+raccount+'&m='+moduleid+'&a=session.setting&target='+target+'&name='+name+'&value=' + value + '&check=' + check;
}

// 선택박스 체크 이벤트 핸들러
$(".checkAll-act-list").click(function(){
	$(".mbr-act-list").prop("checked",$(".checkAll-act-list").prop("checked"));
	checkboxCheck();
});

// page 으로 리스트 체크박스명과 모듈명 추출함수
function getPageData(page,val)
{
	var m; // 모듈 
	var ck; // 체크박스
   if(page=='post' || page=='comment' || page=='oneline' ){
    	m='bbs'; // 최종적으로 모듈명을 넘겨준다. 
    	ck = document.getElementsByName(page+'_members[]');
   }else if(page=='scrap'|| page=='paper'|| page=='point'){
   	m='member';
      ck= document.getElementsByName('members[]');
   }else if(page=='notice'){
   	m='notification';
   	ck = document.getElementsByName('noti_members[]');
   }
   var result={"m":m,"ck":ck,};

   return result[val];
}  

// 선택박스 체크시 액션버튼 활성화 함수
function checkboxCheck()
{
	var page='<?php echo $page?>'; 
 	var f = document.adm_list_form;
	var l =getPageData(page,'ck'); // 체크박스명 얻기  
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

// TEXTAREA 자동 높이 조절 (overflow auto resize)
function resize(obj) {
  obj.style.height = "1px";
  obj.style.height = (20+obj.scrollHeight)+"px";
}
 //]]>  
</script>