<div id="rb-popup">
	<div class="page-header">
		<h4>모듈 기본정보</h4>
	</div>

	<div class="row">
		<dl class="dl-horizontal">
			<dt>모듈명</dt>
			<dd><?php echo $MD['name']?></dd>

			<dt>모듈아이디</dt>
			<dd><?php echo $MD['id']?></dd>

			<dt>모듈의위치</dt>
			<dd><?php echo $g['path_module'].$module?>/</dd>

			<dt>테이블생성</dt>
			<dd><?php if($MD['tblnum']):?>
				<?php echo $MD['tblnum']?>개
				<?php else:?>
				없음
				<?php endif?></dd>

			<dt>모듈등록일</dt>
			<dd><?php echo getDateFormat($MD['d_regis'],'Y/m/d')?></dd>
		</dl>
	</div>

	
	<div class="page-header">
		<h4>제작사 정보</h4>
	</div>

	<div class="row">
		<dl class="dl-horizontal">
			<dt>제작사</dt>
			<dd>NetBiz</dd>

			<dt>이메일</dt>
			<dd>skynopi@gmail.com</dd>

			<dt>홈페이지</dt>
			<dd><a href="http://netbiz.kr" target="_blank">http://netbiz.kr</a></dd>
		</dl>
	</div>

	<div class="page-header">
		<h4>원작자 정보</h4>
	</div>

	<div class="row">
		<dl class="dl-horizontal">
			<dt>제작사</dt>
			<dd>레드블록</dd>

			<dt>회원아이디</dt>
			<dd>세븐고(kims)</dd>

			<dt>이메일</dt>
			<dd>admin@kimsq.com</dd>

			<dt>홈페이지</dt>
			<dd><a href="http://www.kimsq.co.kr" target="_blank">www.kimsq.co.kr</a></dd>

			<dt>라이선스</dt>
			<dd>LGPL</dd>
		</dl>
	</div>
</div>