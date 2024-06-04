<div id="bbswrite">

	<form name="writeForm" method="post" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data" onsubmit="return writeCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="a" value="write" />
	<input type="hidden" name="c" value="<?php echo $c?>" />
	<input type="hidden" name="cuid" value="<?php echo $_HM['uid']?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="bid" value="<?php echo $R['bbsid']?$R['bbsid']:$bid?>" />
	<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
	<input type="hidden" name="reply" value="<?php echo $reply?>" />
	<input type="hidden" name="nlist" value="<?php echo $g['bbs_list']?>" />
	<input type="hidden" name="pcode" value="<?php echo $date['totime']?>" />
	<input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $reply=='Y'?'':$R['upload']?>" />

	<table>

		<?php if(!$my['id']):?>
		<tr>
		<td class="td1">작성자명</td>
		<td class="td2">
			<input size="20" type="text" name="name" value="<?php echo $R['name']?>" class="input subject" />
		</td>
		</tr>
		<?php if(!$R['uid']||$reply=='Y'):?>
		<tr>
		<td class="td1">비밀번호</td>
		<td class="td2">
			<input size="20" type="password" name="pw" value="<?php echo $R['pw']?>" class="input subject" />
			<?php if($R['hidden']&&$reply=='Y'):?>
			<div class="guide">
			비밀답변은 비번을 수정하지 않아야 원게시자가 열람할 수 있습니다.
			</div>
			<?php endif?>
		</td>
		</tr>
		<?php endif?>
		<?php endif?>


		<?php if($B['category']):$_catexp = explode(',',$B['category']);$_catnum=count($_catexp)?>
		<tr>
		<td class="td1">카테고리</td>
		<td class="td2">
			<select name="category">
			<option value="">&nbsp;+ <?php echo $_catexp[0]?>선택</option>
			<?php for($i = 1; $i < $_catnum; $i++):if(!$_catexp[$i])continue;?>
			<option value="<?php echo $_catexp[$i]?>"<?php if($_catexp[$i]==$R['category']||$_catexp[$i]==$cat):?> selected="selected"<?php endif?>>ㆍ<?php echo $_catexp[$i]?>(<?php echo getDbRows($table[$m.'data'],'site='.$s.' and notice=0 and bbs='.$B['uid']." and category='".$_catexp[$i]."'")?>)</option>
			<?php endfor?>
			</select>	
		</td>
		</tr>
		<?php endif?>

		<tr>
		<td class="td1">제목</td>
		<td class="td2">
			<input type="text" name="subject" value="<?php echo ($reply=='Y'?'RE:':'').$R['subject']?>" class="input subject" />
			<span class="check">
			<?php if($my['admin']):?>
			<input type="checkbox" name="notice" value="1"<?php if($R['notice']):?> checked="checked"<?php endif?> />공지글
			<?php endif?>
			<?php if($d['theme']['use_hidden']==1):?>
			<input type="checkbox" name="hidden" value="1"<?php if($R['hidden']):?> checked="checked"<?php endif?> />비밀글
			<?php elseif($d['theme']['use_hidden']==2):?>
			<input type="hidden" name="hidden" value="1" />
			<?php endif?>
			</span>
		</td>
		</tr>

		<tr>
		<td class="td1">내용</td>
		<td class="td2">
			<input type="hidden" name="html" value="TEXT" />
			<textarea name="content"><?php echo $reply=='Y'?'':strip_tags($R['content'])?></textarea>
		</td>
		</tr>

		<?php if($d['theme']['show_wtag']):?>
		<tr>
		<td class="td1">검색태그</td>
		<td class="td2">
			<input size="80" type="text" name="tag" value="<?php echo $R['tag']?>" class="input subject" />
			<div class="guide">
			이 게시물을 가장 잘 표현할 수 있는 단어를 콤마(,)로 구분해서 입력해 주세요.
			</div>			
		</td>
		</tr>
		<?php endif?>

		<?php if($d['theme']['perm_upload'] <= $my['level']):?>
		<tr>
		<td class="td1">파일첨부</td>
		<td class="td2">
			<?php for($i = 0; $i < $d['theme']['num_upload']; $i++):?>
			<input size="80" type="file" name="upfile[]" value="" class="input subject" /><br />
			<?php endfor?>
			<input type="hidden" name="num_upfile" value="<?php echo $d['theme']['num_upload']?>" />
		</td>
		</tr>
		<?php endif?>

		<?php if($d['theme']['perm_photo'] <= $my['level']):?>
		<tr>
		<td class="td1">사진첨부</td>
		<td class="td2">
			<input type="hidden" name="num_photo" value="<?php echo $d['theme']['num_photo']?>" />
			<?php for($i = 0; $i < $d['theme']['num_photo']; $i++):?>
			<input size="80" type="file" name="upfile[]" value="" class="input subject" /><br />
			<?php endfor?>
			<div class="guide">
			<select name="insert_photo">
			<option value="bottom">사진을 내용하단에 삽입</option>
			<option value="top">사진을 내용상단에 삽입</option>
			<option value="">내용에 삽입하지 않음</option>
			</select>
			</div>
			<div class="guide">
			사진첨부는 Windows7폰,안드로이드 2.2버젼 이상 일부 모바일기기에서만 지원됩니다.<br />
			(jpg/png/gif 첨부가능)
			</div>	
		</td>
		</tr>
		<?php endif?>

		<?php if($d['upload']['data']):?>
		<tr>
		<td class="td1">파일삭제</td>
		<td class="td2">
			<ul>
			<?php foreach($d['upload']['data'] as $_u):?>
			<li>
				<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=upload&amp;a=files_delete&amp;file_uid=<?php echo $_u['uid']?>&amp;isreload=Y" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 삭제하시겠습니까?   ');"><?php echo $_u['name']?></a>
				<span class="size">(<?php echo getSizeFormat($_u['size'],1)?>)</span>
				<span class="down">(<?php echo number_format($_u['down'])?>)</span>
			</li>
			<?php endforeach?>
			</ul>
		</td>
		</tr>
		<?php endif?>

		<?php if((!$R['uid']||$reply=='Y')&&is_file($g['path_module'].$d['bbs']['snsconnect'])):?>
		<tr>
		<td class="td1">소셜연동</td>
		<td class="td2 shift">
			<?php include_once $g['path_module'].$d['bbs']['snsconnect']?>
		</td>
		</tr>
		<?php endif?>

	</table>


	<div class="after">
	게시물 등록(수정/답변)후
	<input type="radio" name="backtype" id="backtype1" value="list"<?php if(!$_SESSION['bbsback'] || $_SESSION['bbsback']=='list'):?> checked="checked"<?php endif?> /><label for="backtype1">목록으로 이동</label>
	<input type="radio" name="backtype" id="backtype2" value="view"<?php if($_SESSION['bbsback']=='view'):?> checked="checked"<?php endif?> /><label for="backtype2">본문으로 이동</label>
	</div>

	<div class="bottombox">
		<input type="button" value="취소" class="btngray" onclick="cancelCheck();" />
		<input type="submit" value="확인" class="btnblue" />
	</div>

	</form>


</div>
