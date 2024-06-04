<?php include $g['path_module'].$module.'/admin/_product_query.php';?>
<div class="page-header">
	 <h4>가이드 리스트 
	        <a href="<?php echo str_replace('main', 'regis',$g['adm_href'])?>"  class="pull-right btn btn-link"><i class="fa fa-plus"></i> 새 가이드 등록</a>
	 </h4>
</div>

<?php include $g['path_module'].$module.'/admin/product.php';?>

	
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
		alert('선택된 가이드가 없습니다.      ');
		return false;
	}
	
	if (act == 'product_multi_delete')
	{
		if(confirm('정말로 삭제하시겠습니까?    '))
		{
			f.a.value = act;
			f.submit();
		}
	}
	return false;
}

//]]>
</script>
