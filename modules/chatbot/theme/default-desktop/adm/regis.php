<?php 
include_once $g['dir_module_skin'].'_pc/my/_menu.php';
if ($uid) $R=getUidData($table[$m.'goods'],$uid);
?>


<div id="productregis">



	<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="a" value="product_regis" />
	<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
	<input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $R['upload']?>" />

	<?php if(!$MYMK['auth']):?>
	<div class="notice">
		<img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" />
		정식 판매자등록 심사전이거나 판매 보류상태이므로 상품을 등록할 수 없습니다.
	</div>
	<?php endif?>
	<?php if($MYMK['level'] > 2):?>
	<div class="notice">
		<img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" />
		회원님께서는 우수판매자로 선정되어 심사없이 마켓에 즉시 등록됩니다.
	</div>
	<?php endif?>

	<table>
	<!-- 1.x / 2.x 버전구분 (modify by taiji88. 15/1/8) -->
	<tr>
	<td class="td1">킴스큐 버전</td>
	<td class="td2">:</td>
	<td class="td3">
		<select name="kims_v">
		<option value="rb1"<?php if(!$R['kims_v'] || $R['kims_v'] == 'rb1'):?> selected="selected"<?php endif?>>Rb1</option>
		<option value="rb2"<?php if($R['kims_v'] == 'rb2'):?> selected="selected"<?php endif?>>Rb2</option>
		</select>
		<span class="guide">
		킴스큐 코어 버전을 선택해 주세요.
		</span>
	</td>
	</tr>
	<tr>
	<td class="td1">상품명<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="name" value="<?php echo $R['name']?>" size="60" class="input" />
		<div class="guide">
		이 상품의 정확한 명칭을 입력해 주세요.
		</div>
	</td>
	</tr>
	<tr>
	<td class="td1">판매가격<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<div class="shift"><input type="checkbox" name="price_free" value="1"<?php if(!$R['price']):?> checked="checked"<?php endif?> onclick="price_freecheck(this);" /><span class="b">무료</span></div>
		<div id="price_div"<?php if(!$R['price']):?> class="hide"<?php endif?>>
		<br />
		<input type="text" name="price" value="<?php echo $R['price']?>" size="10" class="input" />원
		</div>
		<div class="guide">
		유료판매시 <?php echo number_format($d['qmarket']['price_min'])?>원이상부터 <?php echo number_format($d['qmarket']['price_step'])?>원 단위로 등록하실 수 있습니다.<br />
		등록하신 가격은 부가세(VAT) 포함 가격 입니다.
		</div>
	</tr>

	<!-- add by taiji88 (옵션추가. 15/5/12) -->
	<tr>
	<td class="td1">가격옵션<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<select name="opt" onchange="optChange(this);">
		<option value="" <?php if(!$R['options']):?>selected<?php endif?>>옵션적용안함</option>
		<option value="1" <?php if($R['options']):?>selected<?php endif?>>옵션적용</option>
		</select> (옵션은 기본가격의 추가 금액으로 설정하세요)
		
		<?php $opt_arr = explode('|^^|', $R['options'])?>
		<div id="opt_dt" <?php if(!$uid || !$R['options']):?>style="display:none"<?php endif?>>
		옵션이름: <input type="text" name="opt1" value="<?php echo $opt_arr[0]?>" size="60" class="input" /> (쉼표(,)로 구분)<br />
		옵션단가: <input type="text" name="opt2" value="<?php echo $opt_arr[1]?>" size="60" class="input" /> (쉼표(,)로 구분)
		</div>
	</td>
	</tr>

	<tr>
	<td class="td1">속성/테마<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<select name="cat" onchange="catChange(this);">
		<option value="">속성</option>
		<option value="">-------</option>
		<option value="6"<?php if($R['cat']=='6'):?> selected="selected"<?php endif?>>패키지</option>
		<option value="1"<?php if($R['cat']=='1'):?> selected="selected"<?php endif?>>모듈</option>
		<option value="2"<?php if($R['cat']=='2'):?> selected="selected"<?php endif?>>위젯</option>
		<option value="5"<?php if($R['cat']=='5'):?> selected="selected"<?php endif?>>스위치</option>
		<option value="3"<?php if($R['cat']=='3'):?> selected="selected"<?php endif?>>레이아웃</option>
		<option value="7"<?php if($R['cat']=='7'):?> selected="selected"<?php endif?>>서비스</option>
		<option value="0"<?php if($R['cat']=='0'&&$R['uid']):?> selected="selected"<?php endif?>>기타</option>
		<option value="4"<?php if($R['cat']=='4'):?> selected="selected"<?php endif?>>제휴마켓</option>
		</select>

		<select name="theme">
		<option value="">테마</option>
		<option value="">-------</option>
		<?php $themecat = explode(',',trim($d['qmarket']['themecat']))?>
		<?php foreach($themecat as $val):?>
		<option value="<?php echo $val?>"<?php if($R['theme']==$val):?> selected="selected"<?php endif?>><?php echo $val?></option>
		<?php endforeach?>
		</select>
		
		<div id="layouttype" class="layouttype shift<?php if($R['cat']!='3'):?> hide<?php endif?>">
		<input type="radio" name="mobile" id="laytype_p" value="0"<?php if(!$R['mobile']):?> checked="checked"<?php endif?> /><label for="laytype_p">PC모드용 레이아웃</label>
		<input type="radio" name="mobile" id="laytype_m" value="1"<?php if($R['mobile']):?> checked="checked"<?php endif?> /><label for="laytype_m">모바일용 레이아웃</label>
		</div>

		<div class="guide">
		상품속성 및 테마를 정확히 지정해 주세요.<br />
		</div>
	</td>
	</tr>
	<tr>
	<td class="td1">상품사진<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="file" name="preview" size="60" class="dnfile" />
		<div class="guide">
		이 상품의 사진을 등록해 주세요.<br />
		(권장사이즈 : 200*200픽셀 / jpg,png,gif)
		</div>
		<?php if(is_file($g['dir_module'].'upload/preview/'.$R['preview'])):?>
		<div class="xphoto"><img src="<?php echo $g['url_module']?>/upload/preview/<?php echo $R['preview']?>" width="150" height="150" alt="" /></div>
		<?php endif?>
	</td>
	</tr>

	<tr>
	<td class="td1">첨부파일<span>*</span></td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="file" name="dnfile" size="60" class="dnfile" />
		<div class="guide">
		사용자가 실제로 다운로드 받거나 실시간 설치할 첨부파일을 등록해 주세요.<br />
		<span class="b">패키지 :</span> rb_<span class="b">package</span>_압축폴더명.zip
		<a href="http://www.kimsq.co.kr/r/devGuide/47#package" target="_blank" style="color:#000000;text-decoration:underline;">패키지구성방법</a><br />
		<span class="b">모듈 :</span> rb_<span class="b">module</span>_압축폴더명.zip
		<a href="http://www.kimsq.co.kr/r/devGuide/47#module" target="_blank" style="color:#000000;text-decoration:underline;">패키지구성방법</a><br />
		<span class="b">위젯 :</span> rb_<span class="b">widget</span>_압축폴더명.zip
		<a href="http://www.kimsq.co.kr/r/devGuide/47#widget" target="_blank" style="color:#000000;text-decoration:underline;">패키지구성방법</a><br />
		<span class="b">레이아웃</span> : rb_<span class="b">layout</span>_압축폴더명.zip
		<a href="http://www.kimsq.co.kr/r/devGuide/47#layout" target="_blank" style="color:#000000;text-decoration:underline;">패키지구성방법</a><br />
		<span class="b">스위치 :</span> rb_<span class="b">switch</span>_압축폴더명.zip
		<a href="http://www.kimsq.co.kr/r/devGuide/47#switch" target="_blank" style="color:#000000;text-decoration:underline;">패키지구성방법</a><br />
		<span class="b">게시판테마</span> : rb_<span class="b">bbstheme</span>_(PC/MOBILE)_압축풀더명.zip
		<a href="http://www.kimsq.co.kr/r/devGuide/47#bbstheme" target="_blank" style="color:#000000;text-decoration:underline;">패키지구성방법</a><br />
		첨부파일은 반드시 zip 파일이어야 합니다.<br />
		</div>
		<?php if(is_file($g['dir_module'].'upload/data/'.$R['dnfile'])):?>
		<div class="down"><img src="<?php echo $g['img_core']?>/file/small/zip.gif" alt="" /> <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;a=download&amp;uid=<?php echo $R['uid']?>" target="_action_frame_<?php echo $m?>"><?php echo $R['dnfile']?> (<?php echo getSizeFormat(filesize($g['dir_module'].'upload/data/'.$R['dnfile']),1)?>)</a></div>
		<?php endif?>
	</td>
	</tr>
	<tr>
	<td class="td1">설치위치</td>
	<td class="td2">:</td>
	<td class="td3">
		<select name="inspath">
		<option value="">-------------</option>
		<option value="layouts"<?php if($R['inspath']=='layouts'):?> selected="selected"<?php endif?>>/rb/layouts/</option>
		<option value="modules"<?php if($R['inspath']=='modules'):?> selected="selected"<?php endif?>>/rb/modules/</option>
		<option value="widgets"<?php if($R['inspath']=='widgets'):?> selected="selected"<?php endif?>>/rb/widgets/</option>
		<option value="switchs"<?php if($R['inspath']=='switchs'):?> selected="selected"<?php endif?>>/rb/switchs/</option>
		</select>
		<input type="text" name="insfolder" value="<?php echo $R['insfolder']?>" size="20" class="input" />
		<div class="guide">
		이 자료가 설치되어야 할 경로를 압축을 풀었을때 생성되는 폴더명을 포함해서 입력해 주세요.<br />
		잘못된 설치정보를 입력하면 자동설치가 되지 않으므로 주의하세요.<br />
		잘못된 경로를 입력하면 등록심사에서 제한되므로 자동설치를 지원하지 않으시려면 입력하지 않으셔도 됩니다.<br />
		게시판테마는 /rb/moodules/ 를 선택하시고 bbs/theme/_pc 또는 _mobile/테마폴더명 으로 입력해 주세요.<br />
		자동설치를 원치 않으시면 입력하지 않으셔도 됩니다.<br />
		<span style="color:#ff0000;">설치위치가 정확해야만 원격설치가 가능합니다. 패키지 구성방법을 참고하세요.</span>
		</div>
	</td>
	</tr>
	<tr>
	<td class="td1">보증기간</td>
	<td class="td2">:</td>
	<td class="td3">
		<select name="asterm">
		<option value="0"<?php if(!$R['asterm']):?> selected="selected"<?php endif?>>무보증</option>
		<?php for($i = 1; $i < 37; $i++):?>
		<option value="<?php echo $i?>"<?php if($R['asterm']==$i):?> selected="selected"<?php endif?>><?php echo sprintf('%02d',$i)?>개월</option>
		<?php endfor?>
		<option value="-1"<?php if($R['asterm']<0):?> selected="selected"<?php endif?>>무제한</option>
		</select>
		<span class="guide">
		이 상품에 대한 보증기간을 선택해 주세요.
		</span>
	</td>
	</tr>
	<tr>
	<td class="td1">사용범위</td>
	<td class="td2">:</td>
	<td class="td3 shift">
		<input type="radio" name="cpterm" id="market_cp1" value="1"<?php if($R['cpterm']):?> checked="checked"<?php endif?> /><label for="market_cp1">패키지/도메인에 따라 1COPY로 한정(재사용시 재구매)</label><br />
		<input type="radio" name="cpterm" id="market_cp2" value="0"<?php if(!$R['cpterm']):?> checked="checked"<?php endif?> /><label for="market_cp2">COPY수 제한없이 모든 패키지/도메인에서 재사용 가능</label>
	</td>
	</tr>
	<tr>
	<td class="td1">개작버젼</td>
	<td class="td2">:</td>
	<td class="td3 shift">
		<input type="radio" name="remake" id="market_remake1" value="1"<?php if($R['remake']):?> checked="checked"<?php endif?> /><label for="market_remake1">이 상품의 소스를 이용하여 제작한 상품의 마켓등록을 허용하지 않습니다.</label><br />
		<input type="radio" name="remake" id="market_remake2" value="0"<?php if(!$R['remake']):?> checked="checked"<?php endif?> /><label for="market_remake2">이 상품의 소스를 수정 또는 응용하여 만든 상품의 마켓 등록을 허용합니다.</label>
	</td>
	</tr>
	<tr>
	<td class="td1">라이선스</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="license" value="<?php echo $R['license']?>" size="10" class="input" />
		<a class="hand u" onclick="document.procForm.license.value='RBL';">RBL</a> ,
		<a class="hand u" onclick="document.procForm.license.value='GPL';">GPL</a> , 
		<a class="hand u" onclick="document.procForm.license.value='LGPL';">LGPL</a> ,
		<a class="hand u" onclick="document.procForm.license.value='FREE';">FREE</a> ,
		<a class="hand u" onclick="document.procForm.license.value='';">없음</a> 
	</td>
	</tr>
	<tr>
	<td class="td1">버젼</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="version" value="<?php echo $R['version']?>" size="10" class="input" />
		<a class="hand u" onclick="document.procForm.version.value='1.0.0';">1.0.0</a> , 
		<a class="hand u" onclick="document.procForm.version.value='';">없음</a> 
		<div class="guide">
		버젼을 등록하면 버젼을 변경할 경우에 자동 업데이트 알림이 지원됩니다.<br />
		버젼은 1.0.0 과 같은 형식으로 세자리 수를 점(.)으로 구분하여주세요.<br />
		</div>
	</td>
	</tr>
	<tr>
	<td class="td1">태그</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="tag" value="<?php echo $R['tag']?>" size="60" class="input" />
		<div class="guide">
		이 상품을 가장 잘 표현할 수 있는 단어를 콤마(,)로 구분해서 입력해 주세요.<br />
		상품명 및 태그는 상품검색시 가장 우선순위로 사용됩니다.
		</div>
	</td>
	</tr>
	<tr>
	<td class="td1">데모URL</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="demo" value="<?php echo $R['demo']?>" size="60" class="input" />
		<div class="guide">
		이 상품의 데모주소를 입력해 주세요
		</div>
	</td>
	</tr>
	<tr>
	<td class="td1">관련그룹</td>
	<td class="td2">:</td>
	<td class="td3">
	<select name="forum" style="width:395px;">
	<option value="">없음</option>
	<option value="">--------------------------------</option>
	<?php $RCD = getDbArray($table['forumlist'],'bbstype=2 and maker='.$my['uid'],'*','uid','desc',0,1)?>
	<?php while($F=db_fetch_array($RCD)):?>
	<option value="<?php echo $F['uid']?>"<?php if($R['forum']==$F['uid']):?> selected="selected"<?php endif?>><?php echo $F['name']?>(<?php echo $F['id']?>)</option>
	<?php endwhile?>
	</select>
	</td>
	</tr>
	<tr>
	<td class="td1">관련노트북</td>
	<td class="td2">:</td>
	<td class="td3">
	<select name="note" style="width:395px;">
	<option value="">없음</option>
	<option value="">--------------------------------</option>
	<?php $RCD = getDbArray($table['forumlist'],'bbstype=3 and maker='.$my['uid'],'*','uid','desc',0,1)?>
	<?php while($F=db_fetch_array($RCD)):?>
	<option value="<?php echo $F['uid']?>"<?php if($R['note']==$F['uid']):?> selected="selected"<?php endif?>><?php echo $F['name']?>(<?php echo $F['id']?>)</option>
	<?php endwhile?>
	</select>
	</td>
	</tr>

	<!-- 할인 막기 (정산시스템 정상화 될때 까지. 관리자는 예외) -->
	<?php //if($my['admin']):?>
	<tr>
	<td class="td1">오늘만 무료</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="todayfree" value="<?php echo $R['todayfree']?>" size="20" class="input" maxlength="8" />
		<span class="guide">
		무료로 판매할 기간을 <span class="b"><?php echo $date['today']?></span> 형식으로 입력하세요.
		</span>
	</td>
	</tr>
	<tr>
	<td class="td1">특별세일</td>
	<td class="td2">:</td>
	<td class="td3">
		<input type="text" name="sailing" value="<?php echo $R['sailing']?>" size="20" class="input" maxlength="15" />
		<span class="guide">
		할인판매 기간과 가격을 <span class="b"><?php echo $date['today']?>,할인가</span> 형식으로 입력하세요.
		</span>
	</td>
	</tr>
	<?php //endif?>
	<!-- 할인 막기 끝 -->

	<tr>
	<td class="td1">요구사항</td>
	<td class="td2">:</td>
	<td class="td3">
		<textarea name="adddata" rows="3" cols="47"><?php echo htmlspecialchars(trim($R['adddata']))?></textarea>
		<div class="guide">
		Rb버젼이나 서버환경,연동상품등의 정보가 필요할 경우 입력해 주세요.<br />
		보기) 킴스큐Rb 1.0.5 버젼 이상부터 지원됩니다.
		</div>
	</td>
	</tr>
	</table>

	<br />

	<div class="iconbox">
		<a href="#." onclick="OpenWindow('<?php echo $g['s']?>/?r=<?php echo $r?>&m=upload&mod=photo&gparam=upfilesValue|upfilesFrame|editFrame');" /><img src="<?php echo $g['img_core']?>/_public/ico_photo.gif" alt="" />사진</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('layout');">레이아웃</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('table');">테이블</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('box');">박스</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('char');">특수문자</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('link');">링크</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />

		<a href="#." onclick="ToolCheck('icon');">아이콘</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('flash');">플래쉬</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('movie');">동영상</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="ToolCheck('html');">HTML</a>
		<img src="<?php echo $g['img_core']?>/_public/split_01.gif" alt="" class="split" />
		<a href="#." onclick="frames.editFrame.ToolboxShowHide(0);" /><img src="<?php echo $g['img_core']?>/_public/ico_edit.gif" alt="" />편집</a>
	</div>
	<div>
	<input type="hidden" name="html" id="editFrameHtml" value="<?php echo $R['html']?$R['html']:'HTML'?>" />
	<input type="hidden" name="content" id="editFrameContent" value="<?php echo htmlspecialchars($R['content'])?>" />
	<iframe name="editFrame" id="editFrame" src="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=editor&amp;toolbox=Y" width="100%" height="450" frameborder="0" scrolling="no"></iframe>
	</div>
	<div>
	<iframe name="upfilesFrame" id="upfilesFrame" src="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=upload&amp;mod=list&amp;gparam=upfilesValue|editFrame&amp;code=<?php echo $R['upload']?>" width="100%" height="0" frameborder="0" scrolling="no"></iframe>
	</div>

	<div class="bottombox">		
		<div>
		<input type="checkbox" id="agree_market_1" /><label for="agree_market_1">이 상품은 저작권 침해요소가 포함되지 않았음이 확실합니다.</label><br />
		<input type="checkbox" id="agree_market_2" /><label for="agree_market_2">마켓 이용약관에 동의합니다.</label><br />
		</div>
		<input type="checkbox" name="backc" id="backc" value="Y" /><label for="backc">상품<?php echo $R['uid']?'수정':'등록'?>후 이화면으로 돌아옵니다.(미체크시 상품리스트로 이동)</label>
		<input type="submit" value="상품<?php echo $R['uid']?'수정':'등록'?>" class="btnblue" />
	</div>

	</form>


</div>


<script type="text/javascript">
//<![CDATA[
// add by taiji88 (옵션추가. 15/5/12)
function optChange(f)
{
	if(f.value == '1') getId('opt_dt').style.display = 'block';
	else getId('opt_dt').style.display = 'none';
}
function catChange(obj)
{
	if (obj.value == '3')
	{
		getId('layouttype').style.display = 'block';
	}
	else {
		getId('layouttype').style.display = 'none';
	}
}
function price_freecheck(obj)
{
	if (obj.checked == true)
	{
		getId('price_div').style.display = 'none';
	}
	else {
		getId('price_div').style.display = 'block';
		document.procForm.price.focus();
	}
}
function saveCheck(f)
{
	<?php if(!$MYMK['auth']):?>
	alert('죄송합니다. 현재는 상품을 등록할 수 없습니다.   ');
	return false;
	<?php endif?>
	if (getId('agree_market_1').checked == false)
	{
		alert('저작권 침해요소 확인에 체크하셔야 상품을 등록하실 수 있습니다.    ');
		return false;
	}
	if (getId('agree_market_2').checked == false)
	{
		alert('마켓 이용약관에 동의하셔야 상품을 등록하실 수 있습니다.    ');
		return false;
	}
	if (f.name.value == '')
	{
		alert('상품명을 입력해 주세요.');
		f.name.focus();
		return false;
	}

	if (f.price_free.checked == true)
	{
		f.price.value = '0';
	}
	else {

		if (f.price.value == '')
		{
			alert('판매가격을 입력해 주세요.');
			f.price.focus();
			return false;
		}

		var n_price = parseInt(filterNum(f.price.value));
		if (n_price < <?php echo $d['qmarket']['price_min']?>)
		{
			alert('판매가격은 적어도 <?php echo number_format($d['qmarket']['price_min'])?>원 이상이어야 합니다.');
			f.price.value = '<?php echo $d['qmarket']['price_min']?>';
			return false;
		}
		if ((n_price % <?php echo $d['qmarket']['price_step']?>) > 0)
		{
			alert('판매가격은 <?php echo number_format($d['qmarket']['price_step'])?>원 단위로만 등록가능합니다.');
			f.price.value = n_price - (n_price % <?php echo $d['qmarket']['price_min']?>);
			return false;
		}
	}

	if(f.opt.value)
	{
		var opt1_arr = f.opt1.value.split(',');
		var opt2_arr = f.opt2.value.split(',');

		if(opt1_arr.length !== opt2_arr.length)
		{
			alert('옵션이름과 단가는 갯수가 일치해야 합니다.');
			return false;
		}

		for(i=0; i<opt2_arr.length; i++)
		{
			if(!getTypeCheck(opt2_arr[i], '0123456789'))
			{
				alert('옵션 단가는 숫자로 입력하셔야 합니다.');
				return false;
			}
		}
	}

	if (f.cat.value == '')
	{
		alert('상품 속성을 선택해 주세요.');
		f.cat.focus();
		return false;
	}
	if (f.cat.value == '6')
	{
		if (f.inspath.value != '' || f.insfolder.value != '')
		{
			alert('패키지의 설치위치는 최상위이므로 기본값으로 지정해 주세요.   ');
			f.insfolder.value = '';
			f.inspath.value = '';
			f.inspath.focus();
			return false;
		}
	}

	if (f.theme.value == '')
	{
		alert('상품 테마를 선택해 주세요.');
		f.theme.focus();
		return false;
	}

	if (f.uid.value == '' || f.uid.value == '0')
	{
		if (f.preview.value == '')
		{
			alert('상품사진을 등록해 주세요.');
			f.preview.focus();
			return false;
		}

		var extarr = f.preview.value.split('.');
		var filext = extarr[extarr.length-1].toLowerCase();
		var permxt = '[jpg][png][gif]';

		if (permxt.indexOf(filext) == -1)
		{
			alert('jpg/png/gif 파일만 등록할 수 있습니다.    ');
			f.preview.focus();
			return false;
		}


		if (f.dnfile.value == '')
		{
			alert('첨부파일을 등록해 주세요.');
			f.dnfile.focus();
			return false;
		}

		var extarr = f.dnfile.value.split('.');
		var filext = extarr[extarr.length-1].toLowerCase();
		var permxt = '[zip]';

		if (permxt.indexOf(filext) == -1)
		{
			alert('zip 파일만 등록할 수 있습니다.    ');
			f.dnfile.focus();
			return false;
		}
	}

	<?php if($my['admin']):?>
	if (f.todayfree.value != '' && f.sailing.value != '')
	{
		alert('오늘만 무료와 특별세일중 한가지만 등록할 수 있습니다.');
		return false;
	}

	if (f.price_free.checked == true)
	{
		if (f.todayfree.value != '' || f.sailing.value != '')
		{
			alert('오늘만 무료와 특별세일은 유료상품만 등록할 수 있습니다.');
			return false;
		}
	}
	<?php endif?>

	frames.editFrame.getEditCode(f.content,f.html);
	if (f.content.value == '')
	{
		alert('내용을 입력해 주세요.       ');
		frames.editFrame.getEditFocus();
		return false;
	}


	if (getId('upfilesFrame'))
	{
		frames.upfilesFrame.dragFile();
	}


	return confirm('정말로 <?php echo $R['uid']?'수정':'등록'?>하시겠습니까?       ');
}
//]]>
</script>




