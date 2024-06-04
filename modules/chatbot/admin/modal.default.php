<style>
#rb-body {background: #fff}
</style>
<?php
if($_front=='addReply') $modal_title='답변';
else if($_front=='addQuestion') $modal_title='쇼핑몰 질문';
else if($_front=='addQuestionC') $modal_title='일반 질문';
else if($_front=='addVendor') $modal_title ='업체';
else if($_front=='addBot') $modal_title ='챗봇';


$last_modal_title='';
if($uid) $last_modal_title=$modal_title.' 수정';
else $last_modal_title=$modal_title.' 신규등록';

$_page=$_front;


?>
<!--@부모레이어를 제어할 수 있도록 모달의 헤더와 풋터를 부모레이어에 출력시킴-->
<div id="_modal_header" class="hidden">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title"><?php echo $last_modal_title?></h4>
</div>
<div>
	 <?php include $g['path_module'].$module.'/admin/'.$_page.'.php';?>
</div>
<div id="_modal_footer" class="hidden">
	<button id="_close_btn_" type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
</div>
<script type="text/javascript">
//<![CDATA[
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
//]]>
</script>
