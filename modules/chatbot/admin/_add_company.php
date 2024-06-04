<!-- 이미지 선택이벤트가 발생할 때 기존 이미지 uid 를 이곳에 저장 : 수정시 기존 이미지 지우기 위해서 -->
 <div class="form-group">
	<label class="col-sm-3 control-label">소유회원 아이디</label>
	<div class="col-sm-9">
		<div class="input-group">
			<input class="form-control" placeholder="좌측 회원 리스트에서 선택 " type="text" name="mbr_info" value="<?php echo $mbr_info?>" disabled>
			<span class="input-group-btn">
				<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#blog_id-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
			</span>
		</div>
		<p class="help-block collapse alert alert-warning" id="blog_id-guide">
			<small> 좌측 회원 리스트에서 선택해주세요.</small>
      </p>
	</div>				
 </div>
  <div class="form-group">
	<label class="col-sm-3 control-label">업체명</label>
	<div class="col-sm-9">
		<input type="text" name="subject" row="2" class="form-control" placeholder="리스트에 노출되는 내용입니다." value="<?php echo $R['subject']?>" />
	</div>				
 </div>
 <div class="form-group">
	<label class="col-sm-3 control-label">사무실 전화</label>
	<div class="col-sm-9">
		<input type="text" name="tel" class="form-control" placeholder="02-0000-0000" value="<?php echo $R['tel']?>" />
	</div>				
 </div>
 <div class="form-group">
	<label class="col-sm-3 control-label">핸드폰</label>
	<div class="col-sm-9">
		<input type="text" name="tel2" class="form-control" placeholder="010-0000-0000" value="<?php echo $R['tel2']?>" />
	</div>				
 </div>
 <div class="form-group">
	<label class="col-sm-3 control-label">이메일</label>
	<div class="col-sm-9">
		<input type="text" name="email" class="form-control" value="<?php echo $R['email']?>" />
	</div>				
 </div>

 <div class="form-group">
	<label class="col-sm-3 control-label">업체 소개</label>
	<div class="col-sm-9">
		<textarea name="content" row="4" class="form-control" placeholder="상세내용에 노출되는 내용입니다."><?php echo $R['content']?></textarea>
	</div>				
 </div>

 <div class="form-group">
	<label class="col-sm-3 control-label">홈페이지 URL</label>
	<div class="col-sm-9">
		<div class="input-group">
			<input class="form-control" placeholder="예: http://www.yes2424.biz" type="text" name="links" value="<?php echo $R['links']?>">
			<span class="input-group-btn">
				<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#link_url-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
			</span>
		</div>
		<p class="help-block collapse alert alert-warning" id="link_url-guide">
			<small> 업체 링크 URL 을 <code>http</code> 포함해서 입력하세요. </small>
      </p>
	</div>				
</div>
<div class="form-group" id="img-fgroup">
	<label class="col-sm-3 control-label">로고</label>
	<div class="col-sm-9">
        <div class="thumbnail" >
        	<span style="display:none;"><input type="file" name="photos[]" data-photo="<?php echo $photo_uid?>" id="ad-inputPhoto"/></span>
        	<img class="img-responsive" src="<?php echo $f_img_src?>" id="preview-photo" style="height:150px;">
        </div>
        <p class="help-block text-danger">
        	<small>이미지 등록 및 변경시 이미지를 클릭하세요.</small>
        </p>
	</div>				
</div>