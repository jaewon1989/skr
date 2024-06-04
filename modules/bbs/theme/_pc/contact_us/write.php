<?php
if (!$_SESSION['upsescode'])
{
  $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
}
$sescode = $_SESSION['upsescode'];
if($R['uid']){
    $u_arr = getArrayString($R['upload']);
    $_tmp=array();
    $i=0;
    foreach ($u_arr['data'] as $val) {
       $U=getUidData($table['s_upload'],$val);
       if(!$U['fileonly']) $_tmp[$i]=$val;
       $i++;
    }
    $insert_array='';
    // 중괄로로 재조립
    foreach ($_tmp as $uid) {
        $insert_array.='['.$uid.']';
    }
}
// 폼전송 후 리턴 페이지 세팅 
$success_url = $g['bbs_write'].'&type=success'; 

// 현제 url 데이타 추출 
$now_url =$_SERVER['REQUEST_URI'];

// 전송 여부 체크 
if (preg_match("/type=success/i", $now_url)) $is_submit = true;
else $is_submit = false;
?>
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<link href="<?php echo $g['url_module_skin']?>/_main.css" rel="stylesheet">
<!-- 템플릿 시작 -->
<section class="content-right">
    <h2 class="content-title"><i class="fa fa-circle-o text-primary" aria-hidden="true"></i> <?php echo $B['name']?></h2>
    <?php if($is_submit):?>
      문의내용이 관리자에게 이메일로 전송되었습니다.
    <?php else:?>    
    <form name="writeForm" method="post" action="<?php echo $g['s']?>/" enctype="multipart/form-data" onsubmit="return writeCheck(this);">
        <input type="hidden" name="r" value="<?php echo $r?>" />
        <input type="hidden" name="m" value="<?php echo $m?>" />
        <input type="hidden" name="bid" value="<?php echo $R['bbsid']?$R['bbsid']:$bid?>" />
        <input type="hidden" name="a" value="write_contactUs" />
        <input type="hidden" name="c" value="<?php echo $c?>" />
        <input type="hidden" name="cuid" value="<?php echo $_HM['uid']?>" />
      
        <input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
        <input type="hidden" name="reply" value="<?php echo $reply?>" />
        <input type="hidden" name="nlist" value="<?php echo $success_url?>" />
        <input type="hidden" name="pcode" value="<?php echo $date['totime']?>" />
        <input type="hidden" name="category" value="문의" />
        <input type="hidden" name="sess_Code" value="<?php echo $sescode?>_<?php echo $my['uid']?>_<?php echo $B['uid']?>" />
        <input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $reply=='Y'?'':$R['upload']?>" />
        <input type="hidden" name="adddata" /> <!-- email^^tel 저장 -->
        <input type="hidden" name="num_upfile" /> <!-- 첨부파일 갯수  -->
        <input type="hidden" name="backtype" value="list" /> <!-- 폼 전송후 리턴타입 : nlist 로 지정된다. nlist 를 success 페이지로 세팅한다.   -->   

        <table class="table table-bordered">
            <colgroup>
              <col width="20%">
              <col>
            </colgroup>
            <tbody>
              <tr>
                <th scope="row">성명</th>
                <td><input type="text" class="form-control" name="name" placeholder="성함을 입력해주세요" style="width: 315px"></td>
              </tr>
              <tr>
                <th scope="row">이메일</th>
                <td><input type="email" class="form-control" name="email" placeholder="이메일을 입력해주세요" style="width: 315px"></td>
              </tr>
              <tr>
                <th scope="row">연락처</th>
                <td><input type="text" class="form-control"  name="tel" placeholder="010-0000-0000" style="width: 315px"></td>
              </tr>
              <tr>
                <th scope="row">제목</th>
                <td><input type="text" class="form-control" placeholder="제목을 입력해주세요" name="subject"></td>
              </tr>
              <tr>
                <th scope="row">내용</th>
                <td>
                    <textarea class="form-control" name="content" rows="7" placeholder="200자 이내로 등록해 주세요." id="meta-content" data-maxlength="200"></textarea>
                    <span class="float-xs-right pr-1">글자수  <strong class="text-danger" id="nowLength-wrap">0</strong> / 200</span>
                </td>
              </tr>
              <tr>
                <th scope="row">파일첨부</th>
                <td>
                  <div class="input-group" >
                    <span class="input-group-btn">
                        <span style="display:none;"><input type="file" name="upfile[]" id="attach-input" /></span>
                        <button class="btn btn-default" type="button" data-role="open-file">파일선택</button>
                    </span>
                    <input type="text" class="form-control" name="preview_attach" data-role="preview-attach" >
                  </div>
                  <p class="form-text text-muted mb-0">
                    <small>파일은 최대 50MB 까지 등록 할 수 있습니다.</small>
                  </p>
                </td>
              </tr>
            </tbody>
        </table>

        <p class="text-center">
            <button type="button" name="button" class="btn btn-default">취소</button>
            <button type="submit" name="button" class="btn btn-primary">보내기</button>
        </p>
    </form>
    <?php endif?>

</section>
 
<script type="text/javascript">
// 입력 글자 수 체크 
$('#meta-content').on({
    "focus keydown keyup": function() {
        var that = $(this),
            max = that.data('maxlength'),
            now = that.val().length;
            if(now > max) return false;
            else $('#nowLength-wrap').text(now);
    }
 });
// 첨부파일 오픈 이벤트 
$('[data-role="open-file"]').on('click',function(){
    $('#attach-input').click();
});

// 첨부파일 미리보기 이벤트 
$('#attach-input').on('change',function(e){
     var files=e.target.files;
     var file=files[0];
     var file_size = file.size;
     var file_name = file.name;
     console.log(file_size);
     if(file_size>50000000){
         alert('파일 사이즈는 50M 까지 등록할 수 있습니다.');
         $('input[name="attach"]').val('');
         $('input[name="preview_attach"]').val('파일이 등록되지 않았습니다.');
         return false;
     }else{
        $('input[name="preview_attach"]').val(file.name);
        $('input[name="num_upfile"]').val(1);
     }
}); 


// 폼 체크 
function writeCheck(f)
{
    if (f.name && f.name.value == '')
    {
       alert('보내는 분 성함을 입력해 주세요. ');
       f.name.focus();
        return false;
    }
    if (f.email.value == '')
    {
        alert('이메일을 입력해 주세요.      ');
        f.email.focus();
        return false;
    }

    if (f.tel.value == '')
    {
        alert('연락처를 입력해 주세요.      ');
        f.tel.focus();
        return false;
    }

    if (f.subject.value == '')
    {
        alert('제목을 입력해 주세요.      ');
        f.subject.focus();
        return false;
    }

    if (f.content.value =='')
    {
        f.content.focus();
        alert('내용을 입력해 주세요.       ');
        return false;
    }
    // email, tel 을 adddata 에 저장 
    var adddata = f.tel.value+'^^'+f.email.value;
    f.adddata.value=adddata;

    getIframeForAction(f);
    f.submit();

}

//]]>
</script>
