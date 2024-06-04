<?php 
if($front!='regis') $product_uid_name='product_members[]';
else $product_uid_name='selected_product[]';
?>

<?php if($front!='regis'):?>
<?php include $g['path_module'].$module.'/admin/_product_searchform.php';?>
<form name="listForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>">
	<input type="hidden" name="r" value="<?php echo $r?>">
	<input type="hidden" name="m" value="<?php echo $module?>">
	<input type="hidden" name="a" value="">
<?php endif?>
	<div class="table-responsive">
		<table class="table table-striped">          
			<thead>
				<tr>
					<th><label data-tooltip="tooltip" title="선택"><input type="checkbox" class="checkAll-post-user"></label></th>
					<th>번호</th>
					<th>구분</th>
					<th>스크린샷</th>
					<th>페이지명</th>
					<th>퍼블리싱 파일</th>
					<th>진행률</th>
					<th>qna</th>
					<th>날짜</th>
				</tr>
			</thead>
		     <tbody data-role="productList-wrapper">
				<?php while($R=db_fetch_array($RCD)):?>
				<?php $C= getUidData($table[$module.'category'],$R['category'])?>
				<tr>
				    <td><input type="checkbox" name="<?php echo $product_uid_name?>" value="<?php echo $R['uid']?>" class="rb-post-user" onclick="checkboxCheck();"/></td>
					<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
					<td><?php echo $C['name']?></td>
					<td class="pic"><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;cat=<?php echo $R['category']?>&amp;uid=<?php echo $R['uid']?>" target="_blank" title="매장보기"><img src="<?php echo getPic($R,'b')?>" width="80" height="65" alt="" /></a></td>
					<td class="sbj">
						<a href="<?php echo str_replace('front=main','front=regis',$g['adm_href'])?>&amp;uid=<?php echo $R['uid']?>" title="상품등록정보">
							<?php echo $R['name'].' > '.$R['content']?>
						</a>
					</td>
					<td class="sbj"><?php echo $R['review']?></td>
					<td class="price"><?php echo $R['vote']?></td>
					<td class="hit"><?php echo number_format($R['qna'])?></td>
					<td class="hit"><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>
				</tr> 
		     <?php endwhile?> 
		</tbody>
	</table>
     </div>	
    <?php if(!$NUM):?>
    	<div class="rb-none" data-role="no-product">등록된 상품이 없습니다.</div>
	<?php endif?>
		<div class="rb-footer clearfix">
			<div class="pull-right">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>	
			<div style="padding-top:20px;">
				<button type="button" onclick="chkFlag('product_members[]');checkboxCheck();" class="btn btn-default btn-sm">선택/해제 </button>
				<?php if($front!='search_product'):?>
				<button type="button" onclick="actCheck('product_multi_delete');" class="btn btn-default btn-sm ">삭제</button>
			      <?php endif?>
			</div>
	</div>
<?php if($front!='regis'):?>
</form>
<?php endif?>