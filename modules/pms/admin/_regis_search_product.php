<?php include $g['path_module'].$module.'/admin/_product_query.php';?>
<div class="tab-content">
	<div class="tab-pane active">
		    <?php include $g['path_module'].$module.'/admin/product.php';?>			
	</div>
</div>
<!--@부모레이어를 제어할 수 있도록 모달의 헤더와 풋터를 부모레이어에 출력시킴-->
<div id="_modal_header" class="hidden">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title">상품리스트</h4>
</div>
<div id="_modal_footer" class="hidden">	
	<button type="submit" class="btn btn-primary pull-left" onclick="frames._modal_iframe_modal_window.saveCheck();">적용하기 </button>
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
