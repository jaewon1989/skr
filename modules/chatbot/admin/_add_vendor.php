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
		<input type="text" name="name" class="form-control" placeholder="리스트에 노출되는 내용입니다." value="<?php echo $R['name']?>" />
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
		<input type="text" name="tel2" class="form-control" placeholder="010-0000-0000" value="<?php echo $mbr_tel2?>" />
	</div>				
 </div>
 <div class="form-group">
	<label class="col-sm-3 control-label">이메일</label>
	<div class="col-sm-9">
		<input type="text" name="email" class="form-control" value="<?php echo $mbr_email?>" />
	</div>				
 </div>

 <div class="form-group">
	<label class="col-sm-3 control-label">업체 소개</label>
	<div class="col-sm-9">
		<textarea name="intro" row="4" class="form-control" placeholder="업체소개 요약"><?php echo $R['intro']?></textarea>
	</div>				
 </div>

<!--  <div class="form-group">
	<label class="col-sm-3 control-label">홈페이지 URL</label>
	<div class="col-sm-9">
		<div class="input-group">
			<input class="form-control" placeholder="예: http://www.bottalks.co.kr" type="text" name="links" value="<?php echo $R['links']?>">
			<span class="input-group-btn">
				<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#link_url-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
			</span>
		</div>
		<p class="help-block collapse alert alert-warning" id="link_url-guide">
			<small> 업체 링크 URL 을 <code>http</code> 포함해서 입력하세요. </small>
      </p>
	</div>				
</div> -->
<div class="form-group" id="img-fgroup">
	<label class="col-sm-3 control-label">로고</label>
	<div class="col-sm-9">
        <div class="thumbnail" >
        	<input type="hidden" name="logo" value="<?php echo $R['logo']?>"/>
        	<span style="display:none;"><input type="file" name="file" id="logo-inputPhoto"/></span>
        	<img class="img-responsive" src="<?php echo $logo_img_src?>" id="preview-logo" style="height:150px;">
        </div>
        <p class="help-block text-danger">
        	<small>이미지 등록 및 변경시 이미지를 클릭하세요.</small>
        </p>
	</div>				
</div>
<script type="text/javascript">

// 이미지 클릭 이벤트 
$('#preview-logo').on('click',function(){
      $('#logo-inputPhoto').click();
});

// 로고 업로드 및 미리보기 
$(document).on('change','#logo-inputPhoto',function(e){
    var file=$(this)[0].files[0];
    var saveDir=$('input[name="saveDir"]').val();
    data = new FormData();
    data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
    data.append("saveDir",saveDir);
    data.append("sescode","<?php echo $sescode?>");
    data.append("item","logo");
    $.ajax({
      type: "POST",
      url : rooturl+'/?r=<?php echo $r?>&m=<?php echo $module?>&a=user_ajax_upload',
      data:data,
      cache: false,
      contentType: false,
      processData: false,
      success: function(result) {
          var val = $.parseJSON(result);
          var code=val[0];
          if(code=='100') // code 값이 100 일때만 실행 
          {
             var source= val[1];// path + tempname
             var upuid= val[2]; // upload 테이블 저장 uid
           
            $('input[name="logo"]').val(source);
            $('#preview-logo').attr("src",source);
          } // success
      }
    }); // ajax   
}); 

// 카테고리 선택 이벤트 
$('[data-role="a-cat"]').click(function(e){
    e.preventDefault();
	$('[data-role="a-cat"]').css({"color":"#666","font-weight":"normal"});
    $(this).css({"color":"#428bca","font-weight":"bold"});
    var cat_uid = $(this).data('uid');
    var cat_name = $(this).data('name');
    var cat_depth =$(this).data('depth');

    $('input[name="category"]').val(cat_uid);
    $('input[name="cat_name"]').val(cat_name);
    $('input[name="cat_depth"]').val(cat_depth);    
});

// 회원 선택 이벤트 
$('[data-act="sel-mbr"]').click(function(e){
	e.preventDefault();
	$('[data-act="sel-mbr"]').removeClass('active');
	$(this).addClass('active');
    var mbr_info = $(this).data('info');
    var mbr_uid = $(this).data('uid');
    var mbr_tel2 = $(this).data('tel2');
    var mbr_email = $(this).data('email');

    $('input[name="mbr_info"]').val(mbr_info);
    $('input[name="mbruid"]').val(mbr_uid);
    $('input[name="email"]').val(mbr_email);
    $('input[name="tel2"]').val(mbr_tel2);

});


function saveCheck(f)
{
    if (f.mbruid.value == '')
	{
		alert('업체 소유 회원을 선택하세요.     ');
		return false;
	}

	if (f.name.value == '')
	{
		alert('업체명을 입력해주세요.     ');
		f.subject.focus();
		return false;
	}
	if (f.tel2.value == '')
	{
		alert('핸드폰 번호를 입력해주세요.     ');
		f.tel2.focus();
		return false;
	}


  	getIframeForAction(f);
	f.submit();
			
}

</script>
