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
	<button type="submit" class="btn btn-primary pull-left" onclick="frames._modal_iframe_modal_window.actCheck('apply');">적용하기 </button>
	<button id="_close_btn_" type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
</div>
	
<script type="text/javascript">
//<![CDATA[
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

function actCheck(act)
{
	var f = document.listForm;
     var l = document.getElementsByName('product_members[]');
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
		alert('선택된 상품이 없습니다.      ');
		return false;
	}
      
      // 선택 상품 적용하기 
      if(act=='apply'){
      	var noProduct=$(parent.document).find('[data-role="no-product"]');
           var productListWrapper=$(parent.document).find('[data-role="productList-wrapper"]');
            
            // no-product 요소 히든 처리 
            $(noProduct).addClass('hidden'); 

            // 아작스로 리스트 세팅  
            $.post(rooturl+'/?r='+raccount+'&m=<?php echo $module?>&a=getProductList',{
                productArray : s
                  },function(response){
                        var result=$.parseJSON(response);
                        console.log(result);
                        if(!result.error) $(productListWrapper).append(result.list);  
                        else  $(productListWrapper).text(result.error);       
             });            
            // 모달 닫기 
      	 $(parent.document).find('#modal_window').removeClass('in');
      	 $(parent.document).find('#modal_window').css('display','none');
            $(parent.document).find('.modal-backdrop').remove();
            $(parent.document).find('body').removeClass('modal-open');
      
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
//]]>
</script>
