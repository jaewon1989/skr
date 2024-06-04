<?php
// 스펨방지 코드 
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];
?>
<script>
// 로고 사진 클릭 
$(document).on('tap','#getLogoPhoto',function(){
    $('#logo-inputfile').click();  
});

// 로고 업로드 및 미리보기 
$(document).on('change','#logo-inputfile',function(e){
    var file=$(this)[0].files[0];
    var saveDir=$('input[name="saveDir"]').val();
    data = new FormData();
    data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
    data.append("saveDir",saveDir);
    data.append("sescode","<?php echo $sescode?>");
    data.append("item","avatar");
    $.ajax({
        type: "POST",
        url : rooturl+'/?r=<?php echo $r?>&m=chatbot&a=user_ajax_upload',
        data:data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(result) {
            var val = $.parseJSON(result);
            var code=val[0];
            if(code=='100') // code 값이 100 일때만 실행 
            {
                 var source=val[1];// path + tempname
                 var upuid=val[2]; // upload 테이블 저장 uid
                 $('input[name="avatar"]').val(source);
                 $('#preview-logo').css({"background":"url('"+source+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"150px 150px"});

            } // success
        }
    }); // ajax   
});       

// 회사소개 첨부파일 버튼 클릭 
$(document).on('tap','#cb-attachfile',function(){
    $('#intro-inputfile').click();  
});

// 챗봇 상세페이지 이미지 
$(document).on('change','#intro-inputfile',function(e){
    var file=$(this)[0].files[0];
    var saveDir=$('input[name="saveDir"]').val();
    data = new FormData();
    data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
    data.append("saveDir",saveDir);
    data.append("sescode","<?php echo $sescode?>");
    data.append("item","intro");
    $.ajax({
      type: "POST",
      url : rooturl+'/?r=<?php echo $r?>&m=chatbot&a=user_ajax_upload',
      data:data,
      cache: false,
      contentType: false,
      processData: false,
      success: function(result) {
          var val = $.parseJSON(result);
          var code=val[0];
          if(code=='100') // code 값이 100 일때만 실행 
          {
             var source=val[1];// path + tempname
             var upuid=val[2]; // upload 테이블 저장 uid
             var name = val[3];
             var attach_input_ele = '<input type="hidden" name="attachfiles[]" value="'+upuid+'" />';
             $('#attach-result').append(attach_input_ele);
             $('#preview-intro').text(name);

          } // success
      }
    }); // ajax   
});    

function regisBotCheck(f)
{
    if (f.induCat.value == '')
    {
        alert('업종을 선택해주세요.     ');
        return false;
    }
    if(f.name.value==''){
        alert('업체명을 입력해주세요.    ');
        f.name.focus();
        return false; 
    }
    if(f.service.value==''){
        alert('서비스명을 입력해주세요.    ');
        f.service.focus();
        return false; 
    }
    <?php if($page=='build/regis'):?>
    if(f.boturl.value==''){
        alert('챗봇 URL을 입력해주세요.    ');
        f.boturl.focus();
        return false; 
    }
    <?php endif?>
    
    // 첨부파일 uid 를 upfiles 값에 추가하기  
    var attachfiles=$('input[name="attachfiles[]"]:last').map(function(){return $(this).val()}).get();
    var new_upfiles='';
    if(attachfiles){
        for(var i=0;i<attachfiles.length;i++) 
        {  
            new_upfiles+=attachfiles[i];
        }
        if(new_upfiles) $('input[name="upload"]').val(new_upfiles);
    }

    getIframeForAction(f);
    f.submit();

}
</script>