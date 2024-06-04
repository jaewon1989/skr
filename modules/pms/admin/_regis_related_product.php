<?php
include_once $g['path_module'].$module.'/var/var.php';
include_once $g['path_module'].$module.'/_main.php';
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 20;
$R=getUidData($table[$module.'product'],$uid);
$joint = getArrayString($R['joint']); // 관련상품 
$_WHERE='(';
foreach($joint['data'] as $val){
      $_WHERE.='uid='.$val.' or ';
}
$_WHERE= substr($_WHERE,0,strlen($_WHERE)-4).')';	

$RCD = getDbArray($table[$module.'product'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$module.'product'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);
?>
<div class="page-header">
	<h4>
		<small><?php echo number_format($NUM)?> 개 ( <?php echo $p?>/<?php echo $TPG.($TPG>1?'pages':'page')?> )</small>
		<a href="#" data-toggle="modal" data-target="#modal_window" data-role="search-product" class="pull-right btn btn-link"><i class="fa fa-plus"></i> 관련상품 등록</a>
	</h4>
</div>
<?php include $g['path_module'].$module.'/admin/product.php';?>
<script>
// 선택박스 체크 이벤트 핸들러
$(".checkAll-post-user").click(function(){
	$(".rb-post-user").prop("checked",$(".checkAll-post-user").prop("checked"));
});

function actCheck(act)
{
     var l = document.getElementsByName('selected_product[]');
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
	
	if (act == 'product_multi_delete')
	{
		if(confirm('정말로 삭제하시겠습니까?    '))
		{
			for (i = 0; i < n; i++)
			 {
				if(l[i].checked == true)
				{
					$(l[i]).parent().parent().remove();
				}
			}			
		}
	}
	return false;
}


// 상품찾기 이벤트 : modalSetting() 함수는 search_product.php 에 존재한다. 
$('[data-role="search-product"]').on('click',function() {
	modalSetting('modal_window','<?php echo getModalLink('&amp;m=admin&amp;module=catalog&amp;front=search_product')?>');
});

</script>
