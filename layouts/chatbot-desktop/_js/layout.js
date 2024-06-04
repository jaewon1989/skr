var module ='chatbot';

// 메세지 출력 함수 
var show__Notify=function(container,message){
    $.notify({
        // options
        message: message 
    },{
        // settings
        element: container,  
        type: 'black',
        placement:{
            from: 'bottom',
            align: "center"
        },
        animate: {
            enter: 'animated fadeInUp',
            exit: 'animated fadeOutDown'
        },
        z_index: 1031,
        offset : 15,
        timer: 500,
        delay : 300
    });
}

// 피드 공유 
var shareFeed=function(vendor,uid,title) {
   var popOption = "width=640, height=400, resizable=no, scrollbars=no, status=no;";
   var url=rooturl+'/?m='+module+'&mod=view&uid='+uid; 
   switch(vendor) {
      case "f" : window.open("http://www.facebook.com/sharer.php?s=100&p[url]="+encodeURIComponent(url)+"&p[title]="+encodeURIComponent(title)+"&p[summary]="+encodeURIComponent(title),"",popOption); break;
      case "t" : window.open("http://twitter.com/share?text="+encodeURIComponent(title)+"&url="+encodeURIComponent(url),"",popOption); break;
      case "k" : window.open("https://story.kakao.com/s/share?text="+encodeURIComponent(title)+"&url="+encodeURIComponent(url),"",popOption); break;
      default : break;
   }
}

// 메타정보 변경 
var setMetaContent=function(title,url,description,image){
    
    // facebook 세팅
    $('meta[property="og:title"]').attr('content',title);
    $('meta[property="og:url"]').attr('content',url);
    $('meta[property="og:description"]').attr('content',description);
    $('meta[property="og:image"]').attr('content',image);

    // twitter 세팅 
    $('meta[name="twitter:card"]').attr('content',description);
    $('meta[name="twitter:url"]').attr('content',url);
    $('meta[name="twitter:title"]').attr('content',title);
    $('meta[name="twitter:description"]').attr('content',description);
    $('meta[name="twitter:image"]').attr('content',image);
  
    // google 세팅 
    $('meta[itemprop="name"]').attr('content',title);
    $('meta[itemprop="description"]').attr('content',description);
    $('meta[itemprop="image"]').attr('content',image); 
}



// 클립보드 복사 이벤트 처리 (clipboard.js 필요함. _import.head.php 파일 참조)
 var clipboard = new Clipboard('[data-role="clipboard-copy"]');
 clipboard.on('success', function(e) {
    var ele=e.trigger;
    var notify_container = $(ele).data('container');
    var msg = $(ele).data('feedback');
    show__Notify(notify_container,msg);
 });      


// url 파라미터 체크 
$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

$(document).on('click','[data-act]',function(){
    var $this=$(this);
    var act=$(this).attr('data-act');
    var title=$(this).attr('data-title'); // 피드 제목 : share 이벤트시 필요 
    var target=$(this).attr('data-target'); // 액션대상 PK (회원,피드,댓글...)
    var vendor = $(this).attr('data-vendor')?$(this).attr('data-vendor'):'';
    var uid = $(this).attr('data-uid')?$(this).attr('data-uid'):'';
    var register=$(this).attr('data-register'); // 등록자 PK
    var position=$(this).attr('data-position'); // 액션 trigger 위치 (feedView, topNavi, feedList)
    var type=$(this).attr('data-type'); // 액션 타입 : 각 액션에 따라 다른 옵션  
    var notify_container = $this.data('container')?$this.data('container'):'body'; // 알림 출력 container
    var addData; // 각 액션별 추가 데이타 전송
    
    if(memberid==''){
        alert('로그인을 먼저 해주세요');
        return false;
    }else{
        // sns 공유 
        if(act=='share'){
            shareFeed(type,target,title);
            return false;
        }
        if(act=='delete-bot'){
            var cfm = confirm("관련 채팅,추가내역 모두 삭제됩니다.");
            if (cfm == false) {
                return false;
            } 
        }
          
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : act,
            target : target,
            position : position,
            type : type,
            register : register,
            vendor : vendor,
            uid : uid,
            addData : addData
        },function(response){
            var result=$.parseJSON(response);//$.parseJSON(response);
            var content=result.content;
            var error=result.error;
            var message=result.message; // 성공 or 에러 메세지 
            var notify_act=["add","delete-bot"];    
            if(error){ // 실패시 
                show__Notify(notify_container,message); // 실패 메세지 
            }else{ // 성공시 

                // 메세지 보여주는 경우
                if($.inArray(act,notify_act)!=-1){
                    if(act=='delete-bot'){
                        var target_botBox = $(document).find('#bot-box-'+uid);
                        $(target_botBox).remove(); 
                    }          
                    show__Notify(notify_container,message);
                
                // 메세지 안 보여주는 경우
                }else{
                    if(act=='paging'){
                        if(mod=='otherStyle'){
                            var btn = result.btn;
                            var list = result.list;
                            $('[data-role="otherStyle-nav-wrap"]').html(btn);
                            $('[data-role="otherStyle-list-wrap"]').html(list);
                        }

                    }else if(act=='delete'){
                        location.reload();
                    }

                }  
           
            } // 성공시  
        });           
    }
});  
var setBotActive = function(val,botuid,notify_container){
   var act = 'setBotActive';
   $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
        act : act,
        setVal : val,
        botuid : botuid
   },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var content=result.content;
        var error=result.error;
        var message=result.message; // 성공 or 에러 메세지 
        show__Notify(notify_container,message); // 메세지 출력  
    });           
}

// 봇 활성화  
$('[data-role="bot-active"]').on("click", function() {
    var pDiv = $(this).parent();
    var botuid = $(this).data('uid');
    var notify_container = $(this).data('container');
    var botActive_label = $('[data-role="botActive-label-'+botuid+'"]');

    if($(pDiv).hasClass("botSwitch-off")) {
        $(pDiv).removeClass("botSwitch-off");
        $(botActive_label).text('챗봇 활성화');
        setBotActive('on',botuid,notify_container);  
    }else{
        $(pDiv).addClass("botSwitch-off");
        $(botActive_label).text('챗봇 비활성화');
        setBotActive('off',botuid,notify_container);      
    }
});

// 봇 추가 이벤트  
$('[data-role="btn-addBot"]').on("click", function() {
    var type = $(this).data('type');
    var notify_container = $(this).data('container');
    if(type==1){
        alert('프리미엄 회원만 추가할수 있습니다');
        return false;
    } 
    
});

// 봇 삭제 이벤트
$('[data-role="btn-addBot"]').on("click", function(){ 
   var botuid = $(this).data('uid');
   var notify_container = 
   $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
        act : 'bot-delete',
        position : 'desktop',
        botuid : botuid
   },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var content=result.content;
        var error=result.error;
        var message=result.message; // 성공 or 에러 메세지 
        show__Notify(notify_container,message); // 메세지 출력  
    });  
});       

// 컴포넌트 호출시 해당 페이지 세팅하는 함수    
$(document).on('click','[data-role="getComponent"]',function(){
    var $this = $(this);
    var component = $this.attr('data-toggle');
    var mod = $this.attr('data-mod'); // 쪽지인 경우 write, view, list  
    var position = $this.attr('data-position'); // 쪽지인 경우 write, view, list  
    var title = $this.attr('data-title'); // 제목 : share 이벤트시 필요 
    var markup = $this.attr('data-markup');
    var target = $this.attr('data-target'); // 액션대상 PK (회원,피드,댓글...)
    var register = $this.attr('data-register'); // 피드, 댓글인 경우 등록자 PK
    var registerid = $this.attr('data-registerid'); // 피드, 댓글인 경우 등록자 userid 
    var need_login = ['paper','regis','recommended','notice','invite','settings']
    var uid = $this.attr('data-uid'); // 대상 PK
    var id = $this.attr('data-id');
    var addData=null;
    if($.inArray(markup,need_login)!=-1){
        if(memberid ==''){
           alert('로그인을 먼저 해주세요. ');
           //$(target).modal('hide'); // paper-write 페이지 호출방지 
           $(modal_login).modal();
           return false;
        } 
    }
    if(markup=='share'){
       var meta_title= $this.attr('data-metaTitle');
       var meta_url= $this.attr('data-metaUrl');
       var meta_desc= $this.attr('data-metaDesc');
       var meta_img= $this.attr('data-metaImg');
       var dataArray={"meta_title":meta_title,"meta_url":meta_url,"meta_desc":meta_desc,"meta_img":meta_img};
       addData = JSON.stringify(dataArray);
       
       // 메타정보 변경 
       setMetaContent(meta_title,meta_url,meta_desc,meta_img); 
       
    }

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
       title : title,
       uid : uid,
       id : id,
       register : register,
       markup : markup,
       mod : mod,
       position : position,
       addData : addData
    },function(response){
       var result = $.parseJSON(response);
       var content=result.content;
       $(target).find('[data-role="content"]').html(content);
       init_afterAjax();
    }); 
   
});
// 초기화 함수 
var init_afterAjax=function(){
    
    // 스와이프 & 포토스와이프 초기화
    /*RC_initPhotoSwipe();
    RC_initSwiper();*/
    //init_Clipboard();
}


$(document).ready(function(){
   // swiper, photoswipe, drawer 리세팅 
   init_afterAjax();

});

$('[data-history="back"]').on('click',function(){
    history.back();
});






