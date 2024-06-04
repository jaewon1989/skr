
<div id="bbslist">
	<div class="nopost">
		<img src="<?php echo $g['img_module_skin']?>/nopost.gif" alt="등록된 포스트가 없습니다" />
	</div>
	<?php if ($my['admin'] || ($d['bbs']['perm_l_write'] <= $my['level'] && !strstr($d['bbs']['perm_g_write'],'['.$my['sosok'].']'))):?>
	<div class="write">
		<?php if($B['uid']):?><span class="btn00"><a href="<?php echo $g['bbs_write']?>">글쓰기</a></span><?php endif?>
	</div>
	<?php endif?>
</div>
