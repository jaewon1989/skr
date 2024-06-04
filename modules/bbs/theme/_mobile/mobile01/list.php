<div id="bbslist">

	<div class="title">

		<div class="article">
			<a href="<?php echo $g['bbs_reset']?>"><span class="name"><?php echo $B['name']?$B['name']:($_HM['name']?$_HM['name']:$_HP['name'])?></span></a>
			<span class="stat"><?php echo number_format($NUM+count($NCD))?>개(<?php echo $p?>/<?php echo $TPG?>페이지)</span>
		</div>
		
		<div class="category">
			<?php if($B['category']):$_catexp = explode(',',$B['category']);$_catnum=count($_catexp)?>
			<select onchange="document.bbssearchf.cat.value=this.value;document.bbssearchf.submit();">
			<option value="">&nbsp;+ <?php echo $_catexp[0]?></option>
			<option value="" class="sline">-------------------</option>
			<?php for($i = 1; $i < $_catnum; $i++):if(!$_catexp[$i])continue;?>
			<option value="<?php echo $_catexp[$i]?>"<?php if($_catexp[$i]==$cat):?> selected="selected"<?php endif?>>ㆍ<?php echo $_catexp[$i]?><?php if($d['theme']['show_catnum']):?>(<?php echo getDbRows($table[$m.'data'],'site='.$s.' and notice=0 and bbs='.$B['uid']." and category='".$_catexp[$i]."'")?>)<?php endif?></option>
			<?php endfor?>
			</select>
			<?php endif?>
		</div>
		<div class="clear"></div>
	</div>
     
	<?php foreach($NCD as $R):?> 
	<?php $R['mobile']=isMobileConnect($R['agent'])?>
	<a name="D<?php echo $R['uid']?>"></a>
	<div class="list notice<?php if($R['uid'] == $uid):?> dselected<?php endif?>" onclick="goHref('<?php echo $g['bbs_view'].$R['uid']?>');">
		<div class="sbj">
			<img src="<?php echo $g['img_module_skin']?>/ico_notice.gif" alt="공지" class="notice" />
			<span class="subject ntc"><?php echo $R['subject']?></span>
			<?php if($R['comment']):?><span class="comment">[<?php echo $R['comment']?><?php if($R['oneline']):?>+<?php echo $R['oneline']?><?php endif?>]</span><?php endif?>
			<?php if($R['trackback']):?><span class="trackback">[<?php echo $R['trackback']?>]</span><?php endif?>
			<?php if(getNew($R['d_regis'],24)):?><span class="new">new</span><?php endif?>
		</div>
		<div class="info"> 
			<?php echo $R[$_HS['nametype']]?> <span>|</span> 
			<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?> <span>|</span> 
			조회 <?php echo $R['hit']?> 
			<?php if(strstr($R['content'],'.jpg')):?><img src="<?php echo $g['img_core']?>/_public/ico_pic.gif" class="imgpos" alt="사진" title="사진" /><?php endif?>
			<?php if($R['upload']):?><img src="<?php echo $g['img_core']?>/_public/ico_file.gif" class="imgpos" alt="첨부파일" title="첨부파일" /><?php endif?>
			<?php if($R['hidden']):?><img src="<?php echo $g['img_core']?>/_public/ico_hidden.gif" class="imgpos" alt="비밀글" title="비밀글" /><?php endif?>
		</div>
	</div>
	<?php endforeach?> 

	<?php foreach($RCD as $R):?> 
	<?php $R['mobile']=isMobileConnect($R['agent'])?>
	<a name="D<?php echo $R['uid']?>"></a>
	<div class="list<?php if($R['uid'] == $uid):?> dselected<?php endif?>" onclick="goHref('<?php echo $g['bbs_view'].$R['uid']?>');">
		<div class="sbj">
			<?php if($R['mobile']):?><img src="<?php echo $g['img_core']?>/_public/ico_mobile.gif" class="imgpos" alt="모바일" title="모바일(<?php echo $R['mobile']?>)로 등록된 글입니다" /><?php endif?>
			<?php if($R['category']):?><span class="cat">[<?php echo $R['category']?>]</span><?php endif?>
			<span class="subject"><?php echo $R['subject']?></span>
			<?php if($R['comment']):?><span class="comment">[<?php echo $R['comment']?><?php if($R['oneline']):?>+<?php echo $R['oneline']?><?php endif?>]</span><?php endif?>
			<?php if($R['trackback']):?><span class="trackback">[<?php echo $R['trackback']?>]</span><?php endif?>
			<?php if(getNew($R['d_regis'],24)):?><span class="new">new</span><?php endif?>
		</div>
		<div class="info">
			<?php echo $R[$_HS['nametype']]?> <span>|</span> 
			<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?> <span>|</span> 
			조회 <?php echo $R['hit']?> 
			<?php if(strstr($R['content'],'.jpg')):?><img src="<?php echo $g['img_core']?>/_public/ico_pic.gif" class="imgpos" alt="사진" title="사진" /><?php endif?>
			<?php if($R['upload']):?><img src="<?php echo $g['img_core']?>/_public/ico_file.gif" class="imgpos" alt="첨부파일" title="첨부파일" /><?php endif?>
			<?php if($R['hidden']):?><img src="<?php echo $g['img_core']?>/_public/ico_hidden.gif" class="imgpos" alt="비밀글" title="비밀글" /><?php endif?>
		</div>
	</div>
	<?php endforeach?> 

	<?php if(!$NUM):?>
	<div class="none">등록된 게시물이 없습니다.</div>
	<?php endif?>

	<div class="page">
	<?php echo getPageLink($d['theme']['pagenum'],$p,$TPG,$g['img_core'].'/page/default')?>
	</div>

	<?php if($B['uid']):?>
	<div class="btnbox">
		<div class="xl">
		<form name="bbssearchf" action="<?php echo $g['s']?>/">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="c" value="<?php echo $c?>" />
		<input type="hidden" name="m" value="<?php echo $m?>" />
		<input type="hidden" name="bid" value="<?php echo $bid?>" />
		<input type="hidden" name="cat" value="<?php echo $cat?>" />
		<input type="hidden" name="sort" value="<?php echo $sort?>" />
		<input type="hidden" name="orderby" value="<?php echo $orderby?>" />
		<input type="hidden" name="recnum" value="<?php echo $recnum?>" />
		<input type="hidden" name="type" value="<?php echo $type?>" />
		<input type="hidden" name="iframe" value="<?php echo $iframe?>" />
		<input type="hidden" name="skin" value="<?php echo $skin?>" />

		<?php if($d['theme']['search']):?>
		<select name="where">
		<option value="subject|tag"<?php if($where=='subject|tag'):?> selected="selected"<?php endif?>>제목+태그</option>
		<option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>본문</option>
		<option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>이름</option>
		<option value="nic"<?php if($where=='nic'):?> selected="selected"<?php endif?>>닉네임</option>
		<option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>아이디</option>
		<option value="term"<?php if($where=='term'):?> selected="selected"<?php endif?>>등록일</option>
		</select>
		
		<input type="text" name="keyword" value="<?php echo $_keyword?>" class="input" />
		<input type="submit" value=" 검색 " class="btnblue" />
		<?php endif?>

		</form>
		</div>
		<div class="xr">
		<span class="btn00"><a href="<?php echo $g['bbs_list']?>">목록</a></span>
		<?php if($B['uid']):?><span class="btn00"><a href="<?php echo $g['bbs_write']?>">쓰기</a></span><?php endif?>
		</div>
		<div class="clear"></div>
	</div>
	<?php endif?>

</div>



