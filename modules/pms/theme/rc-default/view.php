<div class="panel panel-default rb-blog-view">
	<div class="panel-heading">
		<h1>
		<?php if($R['is_free']):?><span class="f1">[무료배송]</span><?php endif?>
		<?php if($R['is_cash']):?><span class="f2">[현금결제]</span><?php endif?>
		<?php echo $R['name']?>
		<?php if($my['admin']):?><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=admin&amp;module=<?php echo $m?>&amp;front=regis&amp;uid=<?php echo $R['uid']?>">[상품수정]</a><?php endif?>
		</h1>
		<div><?php echo getGoodsIcon($R)?></div>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<img src="<?php echo getPic($R,'q')?>" width="300" alt="<?php echo $R['name']?>" onclick="imgOrignWin('<?php echo getPic($R,'b')?>');" />
			</div>
			<div class="col-md-6">
				<table class="table">
					<?php if($R['price1']):?>
					<tr>
						<td class="td1">시중가</td>
						<td class="td2">:</td>
						<td class="td3"><span class="price2 s"><?php echo number_format($R['price1'])?>원</span></td>
					</tr>
					<?php endif?>
					<?php getHalinTD($R)?>
					<?php if($R['point']):?>
					<tr>
						<td class="td1">적립금</td>
						<td class="td2">:</td>
						<td class="td3"><span class="point"><span id="orignPoint"><?php echo number_format($R['point'])?></span>원</span></td>
					</tr>
					<?php endif?>
		
					<?php if($R['model']):?>
					<tr>
						<td class="td1">모델명</td>
						<td class="td2">:</td>
						<td class="td3"><?php echo $R['model']?></td>
					</tr>
					<?php endif?>
					<?php if($R['country']):?>
					<tr>
						<td class="td1">원산지</td>
						<td class="td2">:</td>
						<td class="td3"><?php echo $R['country']?></td>
					</tr>
					<?php endif?>
					<?php if($R['maker']):?>
					<tr>
						<td class="td1">제조사</td>
						<td class="td2">:</td>
						<td class="td3"><?php echo $R['maker']?></td>
					</tr>
					<?php endif?>
					<?php if($R['brand']):?>
					<tr>
						<td class="td1">브랜드</td>
						<td class="td2">:</td>
						<td class="td3"><?php echo $R['brand']?></td>
					</tr>
					<?php endif?>
			
				</table>
			 </div>
		</div> <!-- .row-->
		<br/>
		<div class="rb-blog-body">
				<?php echo getContents($R['content'],$R['html'])?>
		</div>


	</div>	<!-- .panel-body-->

</div>


