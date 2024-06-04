<?php
if($my['photo']){
  $avatar_src = $g['url_root'].'/_var/avatar/'.$my['photo'];
  $avatar_bg = 'style="background: url('.$avatar_src.') center center no-repeat;background-size:150px 150px;"';   
} 
?>
<section id="cb-lost-found">
    <div class="cb-lost-found-wrapper" id="photo-container" style="position:relative;">
        <h1>설정하기</h1>
        <div class="cb-lost-found-profileholder" id="photo-setting">
            <div id="photo-preview" <?php echo $avatar_bg?>>
                 <span style="display:none"><input type="file" name="setting_photo"  id="setting-inputPhoto"/></span>
                 <span class="cb-icon cb-icon-user<?php echo $my['uid']?'ed':''?>" id="change_photo"></span>
            </div>
        </div>

        <div class="cb-lost-found-tab">
            <ul class="cb-cell-layout nav nav-tabs" role="tablist">
                <li class="cb-cell cb-tab-item cb-userinfo-change active">
                    <a href="#settings-info" role="tab" data-toggle="tab">회원정보 변경</a>
                </li>
                <li class="cb-cell cb-tab-item cb-password-change">
                    <a href="#settings-pw" role="tab" data-toggle="tab">비밀번호 변경</a>
                </li>             
            </ul>
        </div>
    </div>
    <div class="tab-content">
          <div class="tab-pane fade active in" id="settings-info">
            <form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" enctype="multipart/form-data"method="post" onsubmit="return infoChangeCheck(this);">
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="c" value="<?php echo $c?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <input type="hidden" name="front" value="<?php echo $front?>" />
            <input type="hidden" name="a" value="info_update" />
            <input type="hidden" name="sex" value="<?php echo $my['sex']?>" />
            <input type="hidden" name="age" value="<?php echo $my['age']?>" />
                <?php include str_replace('profile','join', $g['dir_module_skin']).'_info_formGP.php';?>
                <input type="submit" value="변경 하기">
            </form> 
        </div>
        <div class="tab-pane fade" id="settings-pw"> 
            <form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" method="post" onsubmit="return pwChangeCheck(this);">
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <input type="hidden" name="front" value="<?php echo $front?>" />
            <input type="hidden" name="a" value="pw_update" />
                <div class="cb-inputnaked">
                    <input type="password" placeholder="현재 비밀번호" name="pw" id="pw">
                </div>
                <div class="cb-inputnaked">
                    <input type="password" placeholder="새 비밀번호" name="pw1" id="pw1">
                </div>
                <div class="cb-inputnaked">
                    <input type="password" placeholder="새 비번 확인" name="pw2" id="pw2">
                </div>

                <input type="submit" value="변경 하기">
            </form>
        </div>
      
     </div>        
</section>
<script type="text/javascript">
//<![CDATA[
// 사진 업데이트 - ios / desktop 
$('#photo-setting').on("click","#change_photo", function() {
     $('#setting-inputPhoto').click();
});
$('#photo-setting').on('change','#setting-inputPhoto',function(e){
     var files=e.target.files;
     var file=files[0];
     var reader = new FileReader();
     reader.readAsDataURL(file);
      //로드 한 후
     reader.onload = function  () {
            //로컬 이미지를 보여주기
           
             $("#photo-preview").css({"background":"url('"+reader.result+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"150px 150px"});
   
            // 사진 폼  ajax 전송 
             data = new FormData();
             data.append("setting_photo",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
             data.append("agent","desktop"); //  
             $.ajax({
                 type: "POST",
                 url : rooturl+'/?r='+raccount+'&m=member&a=updateUserPic',
                 data:data,
                 cache: false,
                 contentType: false,
                 processData: false,
                 success:function(response) {
                      var result=$.parseJSON(response);
                      setTimeout(function(){
                         show__Notify('#photo-container',result.message); 
                      },200);
                         
                 }
            }); // ajax
    } 
});

// 일반정보 변경
function infoChangeCheck(f)
{
   getIframeForAction(f);
   f.submit();
}

// 패스워드 변경 
function pwChangeCheck(f)
{
    if (f.pw.value == '')
    {
        alert('현재 패스워드를 입력해 주세요.');
        f.pw.focus();
        return false;
    }

    if (f.pw1.value == '')
    {
        alert('변경할 패스워드를 입력해 주세요.');
        f.pw1.focus();
        return false;
    }
    if (f.pw2.value == '')
    {
        alert('변경할 패스워드를 한번더 입력해 주세요.');
        f.pw2.focus();
        return false;
    }
    if (f.pw1.value != f.pw2.value)
    {
        alert('변경할 패스워드가 일치하지 않습니다.');
        f.pw1.focus();
        return false;
    }

    if (f.pw.value == f.pw1.value)
    {
        alert('현재 패스워드와 변경할 패스워드가 같습니다.');
        f.pw1.value = '';
        f.pw2.value = '';
        f.pw1.focus();
        return false;
    }

    if(('정말로 수정하시겠습니까?       ')){
        getIframeForAction(f);
        f.submit();
    }else{
        return false;
    }
}
//]]>
</script>