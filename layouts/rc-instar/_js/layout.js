// mobile_main.php 에서 본 함수 초기화할때 세팅한 값 정렬
var module='chatbot'; // 모듈명
var modal_login = '#modal-login';

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

// profile 페이지의 경우 주소에 나타나 id 값으로 피드 추출
var getUrlId = $.urlParam('id');


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
        offset : 5,
        timer: 500,
        delay : 800
    });

}

// 메뉴 닫고 push 이동
var closeMenuPush = function(url,title){
    $('#myDrawer').drawer('hide');
    PUSH({
      url        : url,
      hash       : '',
      timeout    : '',
      transition : 'fade'
    });

}

// 메뉴 오픈시 스크롤 top 이벤트
$('#myDrawer').on('show.rc.drawer',function(e){
     console.log(e);
    $('.cb-leftmenu-background').scrollTop(0);
});

$('#modal-botView').on('show.rc.modal',function(e){
     console.log(e);
    //$('.cb-leftmenu-background').scrollTop(0);
});

// 팝업 호출 함수
var showPopUp = function(title,message){
    var popup = $('#popup-default');
    var tpl = '<header class="bar bar-nav">';
           tpl+='<a class="icon icon-close pull-right" data-history="back" role="button"></a>';
           tpl+='<h1 class="title">'+title+'</h1>';
        tpl+= '</header>';
        tpl+= '<div class="content">';
            tpl+= '<p class="content-padded">'+message+'</p>';
        tpl+= '</div>';
     $(popup).html(tpl);
     $(popup).popup('show');
}

// drawer 메뉴 닫기
$(document).on('tap','[data-menupush="true"]',function(e){
    e.preventDefault();
    var need_login = ['build','mybot','added','talked','mybot/message','mybot/story','mybot/statistics'];
    var menu = $(this).data('menu');
    var url = $(this).attr('href');
    var title = $(this).data('title');
    if($.inArray(menu,need_login)!=-1){
       if(memberid){
           if(menu=='build'){
               if(can_build) closeMenuPush(url,title);
               else {
                   var p_title ='프리미엄 안내';
                   var p_message ='이미 등록되어 있습니다. 추가 등록은 프리미업 업체만 가능합니다.';
                   alert(p_message);
                   return false;
               }
           }
           else closeMenuPush(url,title); // 로그인 한 경우 push 진행
       }
       else getLoginModal();
    }else{
       closeMenuPush(url,title);
    }

});

// 피드 공유
var shareFeed=function(vendor,uid,title,url) {
   var popOption = "width=640, height=400, resizable=no, scrollbars=no, status=no;";
   switch(vendor) {
      case "f" : window.open("http://www.facebook.com/sharer.php?s=100&p[url]="+encodeURIComponent(url)+"&p[title]="+encodeURIComponent(title)+"&p[summary]="+encodeURIComponent(title),"",popOption); break;
      case "t" : window.open("http://twitter.com/share?text="+encodeURIComponent(title)+"&url="+encodeURIComponent(url),"",popOption); break;
      case "k" : window.open("https://story.kakao.com/s/share?text="+encodeURIComponent(title)+"&url="+encodeURIComponent(url),"",popOption); break;
      default : break;
   }
}


//#####  SNS Share ############################################################################################################
// require sharrre.js

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

// 공유 sheet 오픈시 메타정보 세팅


$(document).on('tap','.btn-shares',function(){
      var id=$(this).attr('id');
      snsWin(id,$(this));
});

function snsWin(id,Ele){
      var $this=Ele;
      var id_arr={'facebook':facebook,'twitter':twitter,'kakaotalk':twitter}; // kakaotalk 인 경우 속임수로 twitter 를 매칭해준다.
      var obj=id_arr[id];
      $('#'+id).sharrre({
            share : {
                  obj : true
            },
            url : $this.data('url'),
            text : $this.data('text'),
            enableHover: false,
            enableTracking: false,
            template : false,
            render: function(api, options){ // 카운트 숨기기
                 $('.count').hide();
            },
            click: function(api, options){
                  //api.simulateClick(); 클릭 수 계산하는 기능 오류
                  if(id=='kakaotalk') executeKakaoLink(Ele);
                  else api.openPopup(id);
            }
      });
}


//############  카카오톡 & 카카오 스토리  링크 내용 ########################################



 function executeKakaoLink(ele) {
    //Kakao.init('928b2975c63f16a7093a7ba21b9a7300');//사용할 앱의 Javascript 키를 설정해 주세요.  (_import.head 에 초기설정함)
    var _tit =$(ele).data('title');
    var _url = $(ele).data('url');
    var _img=$(ele).data('image');
    var _br  = '\r\n';

    console.log([_tit,_url,_img]);

     if(!navigator.userAgent.match(/(android)|(iphone)|(ipod)|(ipad)/i)){
         alert('이 기능은 모바일에서만 사용할 수 있습니다.');
     }else{
         Kakao.Link.sendTalkLink({
         installTalk : true,
         label: _tit+_br+_url+_br, // 링크주소는 앱에서 등록해야 하므로 앱 등록하지 않고 링크를 보내기 위해서 라벨에 보낸다.
         image: {
             src: _img,
             width: '300',
             height: '200'
          }
          });
     }
     Kakao.cleanup(); // kakao 초기화 해줌

 }
//#####  SNS Share ############################################################################################################


// // 더 보기 초기화
// doShorten();


// 탭메뉴에서 피드 요청시 탭변경 후 재배열
// $('a[data-toggle=tab]').on('shown.bs.tab', function (e) {
//    $(window).trigger("resize");
// });



// 액션 실행시 타겟 uid 와 본인이 같은 경우 메세지
var actSelfMsg=function(act){
    var act_message={
       "follow":"본인은 팔로우할 수 없습니다.",
       "report":"본인이 쓴글은 신고할 수 없습니다. ",
       "block":"본인은 차단할 수 없습니다.",
       "like":"본인글에 좋아요를 보낼 수 없습니다.",
       "comment":"본인 글에 댓글을 달 수 없습니다.",
       "report-comment":"본인 댓글은 신고할 수 없습니다.",
       "paper":"본인에게 쪽지를 보낼 수 없습니다.",
    }
    return act_message[act];
}

// confirm 시 보여지는 문구
var get__confirmQuestion=function(act){
    var confirmQuestion={
       "block":"정말로 차단하시겠습니까?",
       "unblock":"정말로 해제하시겠습니까?"
    }

    return confirmQuestion[act];
}

// 좋아요 숫자 합계 업데이트
var update__Total=function(obj,act,type,target){
    var total_like_wrap = $('[data-role="totalWrap-'+act+'-'+target+'"]');
    $(total_like_wrap).each(function(){
        var old_total_string = $(total_like_wrap).text();
        var old_total_int=old_total_string.replace(/,/gi,'');
        var new_total_int,new_total_string;

        // total_like 값 업데이트
        if(type=='plus') new_total_int=parseInt(old_total_int)+1;
        else new_total_int=parseInt(old_total_int)-1;

        // 계산된 값 콤마 추가
        new_total_string=new_total_int.toLocaleString().split(".")[0];

        // 업데이트값 적용
        $(total_like_wrap).text('');
        $(total_like_wrap).text(new_total_int);

    });

}

// 로그인 모달 호출 함수
var getLoginModal =function(){
    $('#modal-login').modal();
    var markup = 'mLogin';
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
       markup : markup
    },function(response){
        var result = $.parseJSON(response);
        var content=result.content;
        $('#modal-login').find('[data-role="content"]').html(content);
        init_afterAjax(); // ajax 로 가져온 마크업 dom 인식시키기
    });
}

// 로그인 모달 호출 이벤트
$(document).on('tap','[data-role="getLoginModal"]',function(e){
    e.preventDefault();
    getLoginModal();
});

// 컴포넌트 마크업 가져오는 함수
var getCptMarkUp=function(markup,container){
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
       markup : markup
    },function(response){
        var result = $.parseJSON(response);
        var content=result.content;
        $(container).html(content);
        init_afterAjax(); // ajax 로 가져온 마크업 dom 인식시키기
    });
}

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
$(document).on("tap",'[data-role="bot-active"]',function() {
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
$(document).on("click",'[data-role="btn-addBot"]', function() {
    var type = $(this).data('type');
    var notify_container = $(this).data('container');
    if(type==1){
        alert('프리미엄 회원만 추가할수 있습니다');
        return false;
    }

});

// 봇 삭제 이벤트
$(document).on("click",'[data-role="btn-addBot"]',function(){
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

// 광고 메세지 전송 이벤트
$(document).on('tap','[data-role="btn-sendMessage"]',function(){
    var vendor = $(this).data('vendor');
    var message_ta = $('[data-role="ta-message"]');
    var container = $(document).find('#msgTa-wrapper');
    var message = $(message_ta).val();
    var botuid = $('select[name="botuid"]').val();
    if(botuid==''){
        alert('챗봇 서비스를 선택해주세요.');
        return false;
    }
    if(message==''){
        alert('메세지를 입력해주세요.   ');
        $(message_ta).focus();
        return false;
    }
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=regis_notification',{
        message : message,
        vendor : vendor,
        botuid : botuid,
        send_mod : 'mobile'
    },function(response){
       var result = $.parseJSON(response);
       var message=result.message;
       show__Notify(container,message);
    });

});

$(document).on('tap','[data-role="btn-search"]',function(){
    $('#modal-search').modal();
    var markup='mSearch';
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
       markup : markup
    },function(response){
        var result = $.parseJSON(response);
        var content=result.content;
        $('#modal-search').find('[data-role="content"]').html(content);
        init_afterAjax(); // ajax 로 가져온 마크업 dom 인식시키기
    });
});

// 컴포넌트 호출시 해당 페이지 세팅하는 함수
$(document).on('tap','[data-role="getComponent"]',function(){
    var $this = $(this);
    var component = $(this).attr('data-toggle');
    var mod = $(this).attr('data-mod'); // 쪽지인 경우 write, view, list
    var position = $(this).attr('data-position'); // 쪽지인 경우 write, view, list
    var title = $(this).attr('data-title');
    var markup = $(this).attr('data-markup');
    var target = $(this).attr('data-target'); // 액션대상 PK (회원,피드,댓글...)
    var register = $(this).attr('data-register'); // 피드, 댓글인 경우 등록자 PK
    var registerid = $(this).attr('data-registerid'); // 피드, 댓글인 경우 등록자 userid
    var need_login = ['paper','regis','recommended','notice','invite','settings']
    var uid = $(this).attr('data-uid'); // 대상 PK
    var id = $(this).attr('data-id'); //
    var addData=null;
    if($.inArray(markup,need_login)!=-1){
       if(memberid ==''){
           alert('로그인을 먼저 해주세요. ');
           //$(target).modal('hide'); // paper-write 페이지 호출방지
           getLoginModal();
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
       register : register,
       markup : markup,
       mod : mod,
       position : position,
       id : id,
       addData : addData
    },function(response){
       var result = $.parseJSON(response);
       var content=result.content;
       if(markup=='mobile-botView') $(target).html(content);
       else $(target).find('[data-role="content"]').html(content);
       init_afterAjax(); // ajax 로 가져온 마크업 dom 인식시키기
    });

});


// Paper 페이지 추출 함수
var get_PaperViewPage=function(register,index){
    var markup ='paper';
    var mod = 'view';

    $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=get_Component_Page',{
           markup : markup,
           mod : mod,
           register_index : index,
           register : register
      },function(response){
           var result = $.parseJSON(response);
           var content=result.content;
           $('#paper-view').html(content);
    });
}


// 댓글 리스트 가져오기 : position - 출력위치 , parent - post uid
var getCommentList=function(position,parent){
      var commentListWrap = $('[data-role="commentListWrap-'+parent+'"]');
     $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=get_CommentList',{
           position : position,
           parent : parent

        },function(response){
            console.log(position);
           var result = $.parseJSON(response);
           var content=result.content;
           $(commentListWrap).html(content);
           //init__ViewMarkupDom(); // ajax 로 가져온 마크업 dom 인식시키기
     });
}

//클립보드 복사 이벤트 처리 (clipboard.js 필요함. _import.head.php 파일 참조)
var init_Clipboard =function(){
  var clipboard=new Clipboard('[data-role="clipboard-copy"]');
}
$(document).on('tap','[data-role="clipboard-copy"]',function(e){
    var clipboard = new Clipboard(e.currentTarget);
    clipboard.on('success', function(e) {
        var ele=e.trigger;
        var notify_container = $(ele).data('container');
        var msg = $(ele).data('feedback');
        show__Notify(notify_container,msg);
        clipboard.destroy();
    });

});

// 사용자 액션 콜백 함수
var actCallBack=function(trigger,notify_container){
    var act = $(trigger).attr('data-act');
    var target = $(trigger).attr('data-target');
    var position = $(trigger).attr('data-position');
    var type = $(trigger).attr('data-type');

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
        act : act,
        target : target,
        position : position,
        type : type
    },function(response){
        var result = $.parseJSON(response);//$.parseJSON(response);
        var content=result.content;
        var error=result.error;
        var message=result.message; // 성공 or 에러 메세지

        if(error){ // 실패시
            show__Notify(notify_container,message); // 실패 메세지
        }else{ // 성공시
            if(act=='unblock'){
                $(trigger).attr('data-act','block');
                $(trigger).removeClass('blocked');
                $(trigger).text('차단');
            }else if(act=='block'){
                $(trigger).attr('data-act','unblock');
                $(trigger).addClass('blocked');
                $(trigger).text('차단 해제');
            }

            show__Notify(notify_container,message);
        } // 성공시

    }); // ajsx post

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

// 더보기 적용
var doShorten = function(){
    $(".dm-candidate-introduction p").shorten({
        showChars: 90,
        moreText: '전체보기',
        lessText: '더보기 닫기',
        showMethod : 'modal',
        modal : $('#modal-feedView-more')
    });
};


//검색 처리
$(document).on('tap','[data-role="feedSearch"]',function(){
    var $this = $(this);
    var mod = $(this).data('mod');
    $('.dm-viewfilter .dm-right > .dm-icon-filtergray').addClass('active');
    get_FeedList(mod,'search');

});

// 로그인 요청 이벤트
$(document).on('tap','[data-toggle="checkLogin"]',function(){
     alert('로그인을 먼저 해주세요');
     getLoginModal();
     return false;
})

// 스크린 크기, 디자인 기준, Scale 값 취득
var resetScreen = function(){
   var scale = $.urlParam('scale');
   var dm_DeviceWidth = screen.width,
   dm_DesignStandard = 640,
   dm_Multiplied = (dm_DeviceWidth / dm_DesignStandard).toFixed(2);
   var last_scale = scale?scale:dm_Multiplied;
   $("#dm-viewport").attr("content", "width=device-width, initial-scale=" + last_scale + ",user-scalable=no");
}

$(document).ready(function(){
   // swiper, photoswipe, drawer 리세팅
   var call = $.urlParam('call');
   init_afterAjax();
   //if(call==null) resetScreen();
});

// 초기화 함수
var init_afterAjax=function(){

    // 스와이프 & 포토스와이프 초기화
    RC_initPhotoSwipe();
    //RC_initSwiper();
    //init_Clipboard();
}
// Only needed if you want to fire a callback
window.addEventListener('push', init_afterAjax);
window.addEventListener('popstate', init_afterAjax);
