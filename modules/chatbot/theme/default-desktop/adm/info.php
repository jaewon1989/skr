<?php include_once $g['dir_module_skin'].'_pc/my/_menu.php'?>


<div id="userinfo">



	<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="a" value="user_regis" />


	<table>
	<tr>
	<td class="td1">브랜드명<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="brand" value="<?php echo $MYMK['brand']?>" size="60" class="input" />
		<div class="guide">
		 판매자의 브랜드명을 입력해 주세요.
		</div>
	</td>
	</tr>

	<tr>
	<td class="td1">브랜드사진<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="file" name="photo" size="60" class="dnfile" />
		<div class="guide">
		브랜드용 노출사진을 등록해 주세요.<br />
		(권장사이즈 : 200*200픽셀 / jpg,png,gif)
		</div>
		<?php if(is_file($g['dir_module'].'upload/photo/'.$MYMK['photo'])):?>
		<div class="xphoto"><img src="<?php echo $g['url_module']?>/upload/photo/<?php echo $MYMK['photo']?>" width="150" height="150" alt="" /></div>
		<?php endif?>
	</td>
	</tr>

	<tr>
	<td class="td1">연락처<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<?php $tel2=explode('-',$MYMK['tel2'])?>
		<input type="text" name="tel2_1" value="<?php echo $tel2[0]?>" maxlength="3" size="4" class="input" />-
		<input type="text" name="tel2_2" value="<?php echo $tel2[1]?>" maxlength="4" size="4" class="input" />-
		<input type="text" name="tel2_3" value="<?php echo $tel2[2]?>" maxlength="4" size="4" class="input" />
	</td>
	</tr>

	<tr>
	<td class="td1">이메일<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="email" value="<?php echo $MYMK['email']?>" size="60" class="input" />
	</tr>

	<tr>
	<td class="td1">홈페이지<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="home" value="<?php echo $MYMK['home']?>" size="60" class="input" />
	</td>
	</tr>

	<tr>
	<td class="td1">이체은행<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="bank" size="60" value="<?php echo $MYMK['bank']?>" class="input" />
		<div class="guide">
		상품이 판매되었을 경우 정산받으실 이체은행 정보를 입력해 주세요.<br />
		(보기)국민은행 123-45-12345 홍길동
		</div>	
	</td>
	</tr>

	<tr>
	<td class="td1">소개말</td>
	<td class="td2">:</td>
	<td class="td3">
		<textarea name="intro" rows="5" cols="57"><?php echo $MYMK['intro']?></textarea>
		<div class="guide">
		브랜드 소개말을 200~500자 사이로 입력해 주세요.<br />
		등록된 소개말은 브랜드 상세보기 및 리뷰를 통해 노출됩니다.<br />
		태그는 사용할 수 없습니다.
		</div>
	</td>
	</tr>
	</table>


	<br />
	<br />

	<table>
	<tr>
	<td class="td1">통신판매</td>
	<td class="td2">:</td>
	<td class="td3" colspan="4">
		<input type="text" name="comp_tongsin" value="<?php echo $MYMK['comp_tongsin']?>" class="input" />
		<span class="guide">(통신판매업 신고번호를 입력하세요)</span>	
	</td>
	</tr>
	<tr>
	<td class="td1">회사명</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="comp_name" class="input" value="<?php echo $MYMK['comp_name']?>" />
	</td>
	<td class="td1">등록번호</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="comp_num_1" size="4" maxlength="3" class="input" value="<?php echo substr($MYMK['comp_num'],0,3)?>" /> -
		<input type="text" name="comp_num_2" size="3" maxlength="2" class="input" value="<?php echo substr($MYMK['comp_num'],3,2)?>" /> -
		<input type="text" name="comp_num_3" size="5" maxlength="5" class="input" value="<?php echo substr($MYMK['comp_num'],5,5)?>" />
		<input type="radio" name="comp_type" value="1"<?php if($MYMK['comp_type']==1):?> checked="checked"<?php endif?> />개인
		<input type="radio" name="comp_type" value="2"<?php if($MYMK['comp_type']==2):?> checked="checked"<?php endif?> />법인
	</td>
	</tr>

	<tr>
	<td class="td1">대표자명</td>
	<td class="td2">:</td>
	<td class="td3" colspan="4">
		<input type="text" name="comp_ceo" class="input" value="<?php echo $MYMK['comp_ceo']?>" />
	</td>
	</tr>
	<tr>
	<td class="td1">업태</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="comp_upte" class="input" value="<?php echo $MYMK['comp_upte']?>" />
	</td>
	<td class="td1">종목</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="comp_jongmok" class="input" value="<?php echo $MYMK['comp_jongmok']?>" />
	</td>
	</tr>
	<tr>
	<td class="td1">대표전화</td>
	<td class="td2">:</td>
	<td class="td3">
		<?php $tel1=explode('-',$MYMK['tel1'])?>
		<input type="text" name="tel1_1" value="<?php echo $tel1[0]?>" maxlength="4" size="4" class="input" />-
		<input type="text" name="tel1_2" value="<?php echo $tel1[1]?>" maxlength="4" size="4" class="input" />-
		<input type="text" name="tel1_3" value="<?php echo $tel1[2]?>" maxlength="4" size="5" class="input" />
	</td>
	<td class="td1">팩스</td>
	<td class="td2">:</td>
	<td class="td3">
		<?php $fax=explode('-',$MYMK['comp_fax'])?>
		<input type="text" name="comp_fax_1" value="<?php echo $fax[0]?>" maxlength="4" size="4" class="input" />-
		<input type="text" name="comp_fax_2" value="<?php echo $fax[1]?>" maxlength="4" size="4" class="input" />-
		<input type="text" name="comp_fax_3" value="<?php echo $fax[2]?>" maxlength="4" size="5" class="input" />
	</td>
	</tr>

	<tr>
	<td class="td1">주소</td>
	<td class="td2">:</td>
	<td class="td3" colspan="4">
		<div>
		<input type="text" name="comp_zip_1" id="zip1" value="<?php echo substr($MYMK['comp_zip'],0,3)?>" maxlength="3" size="3" readonly="readonly" class="input" />-
		<input type="text" name="comp_zip_2" id="zip2" value="<?php echo substr($MYMK['comp_zip'],3,3)?>" maxlength="3" size="3" readonly="readonly" class="input" /> 
		<input type="button" value="우편번호" class="btngray btn" onclick="OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&m=zipsearch&zip1=zip1&zip2=zip2&addr1=addr1&focusfield=addr2');" />
		</div>
		<div><input type="text" name="comp_addr1" id="addr1" value="<?php echo $MYMK['comp_addr1']?>" size="60" readonly="readonly" class="input" /></div>
		<div><input type="text" name="comp_addr2" id="addr2" value="<?php echo $MYMK['comp_addr2']?>" size="60" class="input" /></div>
		</div>

	</td>
	</tr>

	</table>


	<div class="bottombox">
		<input type="submit" value="판매자정보 수정" class="btnblue" />
	</div>

	</form>


</div>


<script type="text/javascript">
//<![CDATA[

function saveCheck(f)
{
	if (f.brand.value == '')
	{
		alert('브랜드명을 입력해 주세요.');
		f.brand.focus();
		return false;
	}

	if (f.photo.value != '')
	{
		var extarr = f.photo.value.split('.');
		var filext = extarr[extarr.length-1].toLowerCase();
		var permxt = '[jpg][png][gif]';

		if (permxt.indexOf(filext) == -1)
		{
			alert('jpg/png/gif 파일만 등록할 수 있습니다.    ');
			f.photo.focus();
			return false;
		}
	}

	if (f.tel2_1.value == '')
	{
		alert('연락처 입력해 주세요.');
		f.tel2_1.focus();
		return false;
	}
	if (f.tel2_2.value == '')
	{
		alert('연락처를 입력해 주세요.');
		f.tel2_2.focus();
		return false;
	}
	if (f.tel2_3.value == '')
	{
		alert('연락처를 입력해 주세요.');
		f.tel2_3.focus();
		return false;
	}

	if (f.email.value == '')
	{
		alert('이메일을 입력해 주세요.');
		f.email.focus();
		return false;
	}
	if (f.home.value == '')
	{
		alert('홈페이지 주소를 입력해 주세요.');
		f.home.focus();
		return false;
	}

	if (f.bank.value == '')
	{
		alert('이체은행을 입력해 주세요.');
		f.bank.focus();
		return false;
	}


	return confirm('정말로 수정하시겠습니까?       ');
}
//]]>
</script>




