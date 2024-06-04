<section id="cb-signup-section">
    <div id="cb-signup">
        <h1>JOIN US</h1>
        <form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" enctype="multipart/form-data"method="post" onsubmit="return saveCheck(this);">
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="c" value="<?php echo $c?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <input type="hidden" name="front" value="<?php echo $front?>" />
            <input type="hidden" name="a" value="join" />
            <input type="hidden" name="check_email" value="0" />
            <input type="hidden" name="comp" value="<?php echo $comp?>" /> 
            <input type="hidden" name="sex" value="" />
            <input type="hidden" name="age" value="" />

            <div class="cb-signup-profileholder" id="photo-profile">
                <div id="photo-preview">
                    <span style="display:none"><input type="file" name="profile_photo"  id="profile-inputPhoto"/></span>
                    <span class="cb-icon cb-icon-camera" id="photo-click"></span>
                </div>
            </div>
            <?php include $g['dir_module_skin'].'_info_formGP.php';?>      

            <input type="submit" value="가입하기">
             <!-- 소셜 로그인 위젯  -->
             <?php getWidget('default/slogin',array())?>
        </form>

        <div class="cb-signup-login">
            <a href="<?php echo RW('mod=login')?>">로그인 하기</a>
        </div>
    </div>    
</section>

<script>
// 프로필 사진 선택시 이벤트 
$('#photo-profile').on("click","#photo-click", function() {
    $('#profile-inputPhoto').click();
});

$('#photo-profile').on('change','#profile-inputPhoto',function(e){
     var files=e.target.files;
     var file=files[0];
     var reader = new FileReader();
     reader.readAsDataURL(file);
      //로드 한 후
     reader.onload = function  () {
        // 프로필 페이지 사진 업데이트 
        $("#photo-preview").css({"background":"url('"+reader.result+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"150px 150px"});
    } 
});


function sameCheck(obj,layer)
{
    if (obj.name == 'id')
    {
        <?php if($d['member']['login_emailid']):?>
        if (!chkEmailAddr(obj.value))
        {
            obj.form.check_email.value = '0';
            obj.focus();
            getId(layer).innerHTML = '이메일형식이 아닙니다.';
            return false;
        } 
        <?php else:?>
        if (obj.value.length < 4 || obj.value.length > 12 || !chkIdValue(obj.value))
        {
            obj.form.check_id.value = '0';
            obj.focus();
            getId(layer).innerHTML = '사용할 수 없는 아이디입니다.';
            return false;
        }
        <?php endif?>
    }
    if (obj.name == 'email')
    {
        if (!chkEmailAddr(obj.value))
        {
            obj.form.check_email.value = '0';
            obj.focus();
            getId(layer).innerHTML = '이메일형식이 아닙니다.';
            return false;
        }
    }
    frames._action_frame_<?php echo $m?>.location.href = '<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&a=same_check&fname=' + obj.name + '&fvalue=' + obj.value + '&flayer=' + layer;
}

function saveCheck(f)
{
    if (f.name.value == '')
    {
        alert('이름을 입력해 주세요.');
        f.name.focus();
        return false;
    }

    if (f.sex.value == '')
    {
        alert('성별을 선택해 주세요.  ');
        return false;
    }

    if (f.check_email.value == '0')
    {
        alert('<?php echo $d['member']['login_emailid']?'이메일':'아이디'?>를 확인해 주세요.');
        f.email.focus();
        return false;
    }
    if (f.pw1.value == '')
    {
        alert('패스워드를 입력해 주세요.');
        f.pw1.focus();
        return false;
    }
    if (f.pw2.value == '')
    {
        alert('패스워드를 한번더 입력해 주세요.');
        f.pw2.focus();
        return false;
    }
    if (f.pw1.value != f.pw2.value)
    {
        alert('패스워드가 일치하지 않습니다.');
        f.pw1.focus();
        return false;
    }
     
    if (f.age.value == '')
    {
        alert('연력을 선택해 주세요.  ');
        return false;
    }

    getIframeForAction(f);
    f.submit();

}
</script>