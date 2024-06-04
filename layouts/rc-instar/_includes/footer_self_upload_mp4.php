<?php
$feed = new feed();
$profile_url = $feed->getProfile_url($my['id']);
$module = $feed->module;
?>
<footer class="bar bar-footer bar-light bg-faded p-x-0" data-role="bar-footer">
    <section id="footer">
        <div class="dm-actual-body">
            <div class="dm-takeup-space">
                <div class="dm-footer-item">
                    <span class="dm-icon dm-icon-homeblack" data-href="<?php echo $g['s']?>"></span>
                </div>
                <div class="dm-footer-item">
                    <span class="dm-icon dm-icon-listgray" data-toggle="drawer" data-target="#myDrawer"></span>
                </div>
                <div class="dm-footer-item">
                    <div class="dm-lens-box">
                       <!--  <span class="dm-icon dm-icon-lens" <?php if($my['uid']):?>data-toggle="modal"<?php endif?> data-target="#modal-regis" data-register="<?php echo $my['uid']?>" data-role="getComponent" data-mod="photo" data-markup="regis"></span> -->
                       <span class="dm-icon dm-icon-lens" <?php if($my['uid']):?>data-toggle="goCamera"<?php else:?>data-toggle="checkLogin"<?php endif?> data-register="<?php echo $my['uid']?>"></span>
                    </div>

                </div>
                <div class="dm-footer-item">
                    <a class="dm-icon dm-icon-persongray"<?php echo (!$my['uid'])?' data-status="nologin"':' data-href="'.$profile_url.'"';?>></a>
                </div>
                <div class="dm-footer-item">
                    <a href="<?php echo $g['path_market']?>">
                    <span class="dm-img dm-img-market" ></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</footer>
<script>
// platform 체크 함수 
function checkMos(){
    var MOS;

    if (navigator.userAgent.indexOf('APP_AUTOBUILDER_IOS') != -1 && (navigator.userAgent.toLowerCase().indexOf('iphone') != -1 || navigator.userAgent.toLowerCase().indexOf('ipad') != -1)) {
         MOS = 'ios';
    }else if (navigator.userAgent.indexOf('APP_AUTOBUILDER_ANDROID') != -1 && navigator.userAgent.toLowerCase().indexOf('android') != -1) {
         MOS = 'android';
    }else{
        MOS ='web';
    }  
    
    return MOS;
} 


// 등록 모달 호출 : MOS - 웹 or 하이브리드앱 구분값 , addData - 추가 적용 값 (앱인 경우 파일 리스트를 적용시킨다.) 
function openRegisModal(MOS,preivew,regisType,video_link){
    $('#modal-regis').modal();
    var module = '<?php echo $module?>';
    var markup = 'regis';
    var mod = regisType;
    var register = memberuid; // 피드, 댓글인 경우 등록자 PK
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
       register : register,
       markup : markup,
       mod : mod
    },function(response){
       var result = $.parseJSON(response);
       var content=result.content;
       $('#modal-regis').find('[data-role="content"]').html(content);
       $('input[name="platform"]').val(MOS); // 등록 OS 저장 : app or web 구분값 --> 글 등록 프로세스가 상이함.
       $('input[name="regisType"]').val(regisType);
       $('input[name="links"]').val(video_link);
       
       // 앱인 경우 
       if(MOS=='ios'||MOS=='android'){
           $('#modal-regis').find('[data-role="first-add-img"]').remove(); // 추가 이미지 제거 
           $('#modal-regis').find('[data-role="preview-ul"]').append(preivew); // 업로드 리스트 추가  
       } 

       init_afterAjax(); // ajax 로 가져온 마크업 dom 인식시키기    
    });  
}

// 포토 or 동영상 업로드 후 콜백 함수 
function Success_App_Upload(arg){
    var result=$.parseJSON(arg);
    var code =result.code;
    var fileList = result.fileList;
    var registType;
    var video_link;
    var MOS = checkMos(); // platform 체크 

    if(code==0){
        var file_list = ''; // ul 은 regis.html 파일에 있음. 
        var files=fileList.split(',');
        $.each(files,function(key,list){
            var list_arr = list.split('^^');
            var src = list_arr[0];
            var uid = list_arr[1];
            var type = list_arr[2];
            if(type==2){
                regisType = 'photo';
                video_link = null;
                file_list += '<li class="photo-li">';
                file_list += '<input type="hidden" name="photos[]" value="'+uid+'" data-type="'+type+'"/>';
                file_list += '<img src="'+src+'" style="border:solid 1px #d9d9d9">';
                file_list += '</li>';  
            }else if(type==5){
                regisType = 'video';
                var video_view = '<video id="my-video" class="video-js" controls preload="auto" width="282" height="212">';
                video_view += '<source src="'+src+'" type="video/mp4">';
                video_view += '</video>';
                var video_preview = '<video width="250" height="150" controls>';
                video_preview += '<source src="'+src+'" type="video/mp4">';
                video_preview += '</video>';
                video_link = 'video^^title^^description^^'+src+'^^screen shot^^'+video_view;
                file_list += '<li>';
                file_list += '<input type="hidden" name="videos[]" value="'+uid+'" data-type="'+type+'"/>';
                file_list += video_preview;
                file_list += '</li>';
            }
            
        });
    
        // 등록모달 호출 : 업로드  파일 리스트를 첨부한다.
        openRegisModal(MOS,file_list,regisType,video_link);

    }
}  

// 사진/동영상 등록 버튼 탭 이벤트 
$(document).on('tap','[data-toggle="goCamera"]',function(){
    var param = {
        mode : 0,
        maxCount : 10,
        desUrl : rooturl+'/?r='+raccount+'&m=sns&a=app_upload',
        succFn : 'Success_App_Upload' // Succ Fn name
    };
    var MOS = checkMos();
    if(MOS=='ios'||MOS=='android') Hybrid.exe('HybridIfEx.goCamera', param);
    else{
       // web 인 경우 
       var preview =null;
       var regisType ='photo';
       var video_link = null;
       openRegisModal(MOS,preview,regisType,video_link);  
    }  
});

// $(document).ready(function(){
//    var str = 'a,b,c';
//    var str_arr= str.split(',');
//    $.each(str_arr,function(key,val){
//        console.log(val);
//    }) 

// });

</script>