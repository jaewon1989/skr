// mobile_main.php 에서 본 함수 초기화할때 세팅한 값 정렬
var module='sns'; // 모듈명 
var btn_follow = ''; // 팔로우 버튼
var layer_btn='[data-toggle="sp-layer-btn"]'; // 클릭시 레이어 활성화 버튼
var layer_layer = $('[data-toggle="sp-layer"]'); // 활성화 된 레이어
var write_btn = $('[data-toggle="write"]'); // 활성화 된 레이어
var btn_next_layer = $('[data-nextmodal]'); // 활성화 된 레이어
var feedList_wrap = $('[data-role="feedList-wrap"]');// 피드 리스트 div
var filter_wrap = $('[data-role="filter-wrap"]'); // 필터 출력 박스  
var act_error=0;
var popup_confirm = $('#popup-confirm');
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

var init_afterAjax=function(){
    
    // 스와이프 & 포토스와이프 초기화
    RC_initPhotoSwipe();
    RC_initSwiper();
}
    
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

// 레이어 호출 함수 
$(document).on('tap',layer_btn,function(){
    var target = $(this).data('target');
    $('[data-toggle="sp-layer"]').fadeOut(200);
    $(this).parent().find(target).fadeIn(200);
}) 

// 토글 레이어 바깥쪽 클릭시 닫힘
$(document).on('mouseup',function (e){
     var container = layer_layer;
     if(container.has(e.target).length == 0) container.fadeOut(200);
});

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
    Kakao.init('928b2975c63f16a7093a7ba21b9a7300');//사용할 앱의 Javascript 키를 설정해 주세요.    
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
$('a[data-toggle=tab]').on('shown.bs.tab', function (e) {
   $(window).trigger("resize");
});
  



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



// 컴포넌트 호출시 해당 페이지 세팅하는 함수    
$(document).on('tap','[data-role="getComponent"]',function(){
    var $this = $(this);
    var component = $(this).attr('data-toggle');
    var mod = $(this).attr('data-mod'); // 쪽지인 경우 write, view, list  
    var position = $(this).attr('data-position'); // 쪽지인 경우 write, view, list  
    var subject = $(this).attr('data-subject'); // 피드 제목 : share 이벤트시 필요 
    var markup = $(this).attr('data-markup');
    var target = $(this).attr('data-target'); // 액션대상 PK (회원,피드,댓글...)
    var register = $(this).attr('data-register'); // 피드, 댓글인 경우 등록자 PK
    var registerid = $(this).attr('data-registerid'); // 피드, 댓글인 경우 등록자 userid 
    var need_login = ['paper','regis','recommended','notice','invite','settings']
    var uid = $(this).attr('data-uid'); // 대상 PK
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
       title : subject,
       uid : uid,
       register : register,
       markup : markup,
       mod : mod,
       position : position,
       addData : addData
    },function(response){
       var result = $.parseJSON(response);
       var content=result.content;
       if(markup=='paper'){
            $('[data-role="'+mod+'-content"]').html(content);
            // 글쓰기 모드시 해당 유저 검색창에 입력 및 리스트 체크 처리 
            if(mod=='write'){
                $('[data-role="search-followship"]').val(registerid);
                var rsv_member_list = $('#followship-listitem-'+ register);
                $(rsv_member_list).addClass('selected');  

            } 
       } 
       else $(target).find('[data-role="content"]').html(content);
       init_afterAjax(); // ajax 로 가져온 마크업 dom 인식시키기    
    }); 
   
});

// 비로그인 상황에서 검색버튼 클릭시 
$('[data-markup="search"]').on('tap',function(){
   if(memberid ==''){
      alert('로그인을 먼저 해주세요. ');
      //$(target).modal('hide'); // paper-write 페이지 호출방지 
      $(modal_login).modal();
   }else{
      return true;
   }
      
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

// Paper view - 선택박스 선택시 메세지 출력 
$(document).on('change','#multiUser-select',function(){
    var selected_index = $('#multiUser-select option:selected').attr('data-index'); // 
    var register=[];

    // 배열 재 정의 
    $('#multiUser-select option').each(function(){
        var option_val = $(this).val();
        register.push(option_val);
    });   

    // paper view 페이지 재호출 
    get_PaperViewPage(register,selected_index);
  
});

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

// 클립보드 복사 이벤트 처리 (clipboard.js 필요함. _import.head.php 파일 참조)
$(document).one('click','[data-role="clipboard-copy"]',function(e){
     var $this = $(this);
     var target = $this.data('target');
     var container = $this.data('container');
     var notify_container = $(container);
     var clipboard = new Clipboard('[data-role="clipboard-copy"]');
     clipboard.on('success', function(e) {
        var ele=e.trigger;
        var msg = $(ele).data('feedback');
        show__Notify(notify_container,msg);
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
     

// 피드 사용자 액션 confirm 
$(popup_confirm).find('[data-confirmact="btn-confirm"]').tap(function(){
    if($(this).data('confirm')=='no'){
        setTimeout(function(){
             $(popup_confirm).popup('hide');                   
              return false;
        },50);
        
    }else{
        var actName = $(popup_confirm).find('[data-role="actName"]').attr("data-value");
        var $this = $('[data-act="'+actName+'"]');
        var notify_container=$this.data('container');
        $(popup_confirm).popup('hide');
        actCallBack($this,notify_container);
    } 
    return false; 
});


// 피드 사용자 액션 전체 관리   
$(document).on('tap','[data-act]',function(e){
    var $this = $(this);
    var act = $(this).attr('data-act');
    var title = $(this).attr('data-title'); // 피드 제목 : share 이벤트시 필요 
    var target = $(this).attr('data-target'); // 액션대상 PK (회원,피드,댓글...)
    var register = $(this).attr('data-register'); // 등록자 PK
    var post = $(this).attr('data-post')?$(this).attr('data-post'):'';
    var mod = $(this).attr('data-mod')?$(this).attr('data-mod'):'';
    var page = $(this).attr('data-page')?$(this).attr('data-page'):'';
    var position = $(this).attr('data-position'); // 액션 trigger 위치 (feedView, topNavi, feedList)
    var share_url = $(this).attr('data-shareurl');
    var option =$(this).attr('data-option'); // 각 액션타입에 따른 옵션 1
    var type = $(this).attr('data-type'); // 액션 타입 : 각 액션에 따라 다른 옵션 1 
    var notify_container; // 알림 출력 container
    var addData; // 각 액션별 추가 데이타 전송 
    var getUid = $.urlParam('uid'); // 랜딩 view 페이지로 url 접근했을 경우 $_GET['uid'] 값 
    var like_wrap = $('[data-role="like-wrap"]');
    var header_likeList = $('[data-role="header-likeList"]'); // 좋아요 헤더 dm-like 추가하면 하트가 분홍색으로 변함  
    var header_commentList = $('[data-role="header-commentList"]'); // 댓글 헤더  dm-reply 추가 하면 댓글 아이콘에 분홍색으로 변함 
    var comment_textarea = $('[data-role="comment-textarea"]'); // 댓글 입력 textarea
    var comment_ListWrap = $('[data-role="comment-ListWrap"]'); // 댓글 리스트 출력 wrap
    var paper_textarea = $('[data-role="paper-textarea"]'); // 쪽지 입력 textarea
    var paper_ListWrap = $('[data-role="paper-ListWrap"]'); // 쪽지 리스트 출력 wrap
   
    // 메세지 출력 container 설정 : 모달일때는 메세지는 모달 기준으로 출력하고 랜딩 페이지인 경우 body 기준 출력한다.  
    if((position =='feedView' || position=='msgBox') && !getUid) notify_container = $('#modal-default');
    else if(position =='feedList_D') notify_container = $('#feedListBottom-'+target); 
    else if(position =='popup') notify_container = $(this).data('container');
   
    if(memberid==''){
        alert('로그인을 먼저 해주세요');
        $(modal_login).modal();
        return false;
    }else{
        // 유효성 체크 
        if(act!='paper' && act!='paging'){
            if(memberuid==register || memberuid==target){
               alert(actSelfMsg(act));
               return false;  
           }  

        }
        
        if(act=='paging'){
            if(page=='') return false;
        } 
         
        if($(this).hasClass('reported')){
            alert('이미 신고되었습니다');  
            return false;
        } 
        if(act=='unblock'|| act=='block'){
            var confirmQuestion=get__confirmQuestion(act);

            $(popup_confirm).find('[data-role="actName"]').attr("data-value",act); // act 명 입력 
            $(popup_confirm).find('[data-role="question"]').text(confirmQuestion);
            $(popup_confirm).popup({backdrop:'static'});
            return false; 
        }

        if(act=='share'){
            shareFeed(type,target,title,share_url);
            return false;
        }
        // 댓글 달기 
        if(act=='comment'){
            var $tarea = $(comment_textarea);

            if($tarea.val()==''){
               alert('댓글을 입력해주세요.');
               setTimeout(function(){
                  $tarea.focus();
               },200);
               return false;
            }else{
               var content = $tarea.val();
               var dataArray = $tarea.data();
               dataArray['content']=content;
               addData = JSON.stringify(dataArray);               
            }
        }
        // 쪽지 
        if(act=='paper'){
            var $tarea = $(paper_textarea);
            var msg_content;
            var msg_type;
            var selected_row=$('[data-role="search-followship-result"]').find('.selected'); // 선택체크된 row 
            var selected_user=$(selected_row).find('input[name="rsv_member[]"]').map(function(){return $(this).val()}).get();
            var multi_user;
            

            // write 페이지 인 경우 받은 사람 있는지  
            if(position=='paper_write'){
                if(selected_user==''){
                    alert('선택된 수신자가 없습니다.');
                    return false;
                }else{
                    multi_user=selected_user; // uid^^id,uid^^id,.... 배열 
                }                
            } 

            if($tarea.val()==''){
                alert('메세지를 입력해주세요');
                return false;
               // msg_content=null;
               // msg_type='heart';
            }else{

               msg_content = $tarea.val();
               msg_type='text';
            }
            var dataArray={"content":msg_content,"type":msg_type,"multi_user":multi_user};
            addData = JSON.stringify(dataArray);
        }               
        
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : act,
            target : target,
            position : position,
            type : type,
            register : register,
            post : post,
            mod : mod,
            page : page,
            addData : addData
        },function(response){
            var result = $.parseJSON(response);//$.parseJSON(response);
            var content=result.content;
            var error=result.error;
            var message=result.message; // 성공 or 에러 메세지 
            var notify_act=["follow","unfollow","report","like","cancellike","comment","paper"];
     
            if(error){ // 실패시 
                show__Notify(notify_container,message); // 실패 메세지 
            }else{ // 성공시 
                
                if($.inArray(act,notify_act)!=-1){
                    if(act=='follow'){
                        if(position!='profile') $this.attr('class','dm-button dm-button-followactive');
                        $this.attr('data-act','unfollow');
                    }else if(act=='unfollow'){
                        if(position!='profile') $this.attr('class','dm-button dm-button-followplus');
                        $this.attr('data-act','follow');     
                    }else if(act=='report'){
                        $this.addClass('reported');
                        $this.text('신고됨');
                    }else if(act=='block'){
                            $this.attr('data-act','unblock');
                            $this.addClass('blocked');
                            $this.text('차단 해제');  
                    }else if(act=='like' || act=='cancellike'){
                       if(act=='like'){
                           $(header_likeList).addClass('dm-like');
                           $this.parent().parent().parent().find('.dm-icon-heartfilled').addClass('dm-like');
                           $this.attr('data-act','cancellike');
                           update__Total($this,'like','plus',target);
                       }else if(act=='cancellike'){
                           $(header_likeList).removeClass('dm-like');
                           $this.parent().parent().parent().find('.dm-icon-heartfilled').removeClass('dm-like');
                           $this.attr('data-act','like');
                           update__Total($this,'like','minus',target);
                        }
                        // like 결과 출력 
                        $(like_wrap).html(content);

                    }else if(act=='comment'){
                       $(comment_ListWrap).html(content);
                       if(option=='commentBox') getCommentList('commentBox',target)
                       else getCommentList('feedList',target);// 리스트에도 댓글 리스트 업데이트 

                       $(comment_textarea).val('');
                       update__Total($this,'comment','plus',target);
                    }else if(act =='paper'){
                        if(position =='paper_write'){
                            var mod = 'view';
                            var markup = 'paper';
                            var registerid = selected_user; // 선택된 수신자 배열
                            
            
                            // view 페이지 오픈 
                            $('#paper-view').page({ start: '#paper-write' });
                           
                            // view 페이지 가져오기 
                            get_PaperViewPage(selected_user,0); // 처음에는 index 0 인 msg rows 를 출력한다 .

                        }else{
                            $(paper_ListWrap).html(content);
                            $(paper_textarea).val('');     
                        }
                       
                    }
                    // 성공시에는 메세지 보여주는 act 인 경우에만 보여준다.      
                
                     show__Notify(notify_container,message);                    
                
                // 메세지 보여주는 경우 

                }else{
                    if(act=='paging'){
              
                             var btn = result.btn;
                             var list = result.list;
                             $('[data-role="otherStyle-nav-wrap"]').html(btn);
                             $('[data-role="otherStyle-list-wrap"]').html(list);
                              
                    }

                }
           
            } // 성공시 

        }); // ajsx post

    }// 회원인 경우

});  

// 쪽지보내기 관련 ###########################################################################################

$(document).on('keyup','[data-role="search-followship"]',function(){
    var result_container = $('[data-role="search-followship-result"]');
    var keyword = $(this).val();
    var act = 'search-followship';
    if(keyword!=''){
        $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=do_UserAction',{
            act : act,
            keyword : keyword
        },function(response){
               var result = $.parseJSON(response);
               var content=result.content;
               $(result_container).html(content);
        }); 
    }else{
        return;
    } 
 
})

$(document).on('tap','[data-role="check-followship-listitem"]',function(){
    var member_seq = $(this).data('uid');
    var rsv_member_list = $('#followship-listitem-'+member_seq)
    if($(rsv_member_list).hasClass('selected')) $(rsv_member_list).removeClass('selected');
    else $(rsv_member_list).addClass('selected');   
});

// 쪽지 보내기 관려 ##################################################################


// 필터 모달 오픈 이벤트 > 필터링 엘리먼트 data-mod 속성값 지정   
$('#modal-filters').on('show.rc.modal',function(e){
     var trigger=e.relatedTarget;
     var mod = $(trigger).data('mod'); // hot,new,best 
     $(this).find('[data-role="feedSearch"]').attr('data-mod',mod);// 필터 모드에 입력   
});

// 비디오 팝업 관련 이벤트 *******************************************************************************************
    
    // 비디오 팝업 열때 내용 채우기 
    $('#popup-video').on('show.rc.popup',function(e){
        var trigger=e.relatedTarget;
        var uid = $(trigger).data('uid'); 
        var markup ='video';
        var popup = $(trigger).data('target');
        $(popup).loader();
     
        $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=get_VideoIframe',{
            markup : markup,
            post : uid
        },function(response){
           $(popup).loader('hide'); 
           $(popup).find('[data-role="content"]').html(response);
           //init__ViewMarkupDom(); // ajax 로 가져온 마크업 dom 인식시키기    
     });  

    });   

    // 비디오 팝업 닫을 때 내용 지우기  
    $('#popup-video').on('hide.rc.popup',function(){
         $(this).find('[data-role="content"]').html('');
    });

// 비디오 팝업 관련 이벤트 *******************************************************************************************


// 피드 상세보기 모달 > 쪽지 모달 오픈 이벤트  
$('#modal-feedView-paper').on('show.rc.modal',function(e){
     var trigger=e.relatedTarget;
     var register = $(trigger).data('register');
     var modal = $(trigger).data('target');
     if(!memberid){
         alert('로그인을 먼저 해주세요.');
         return false;
     }else{
        if(memberuid==register){
            alert('본인의 게시물 입니다.');
            return false;
        }
     }
   
     $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=get_paperView',{
           register : register
        },function(response){
           var result = $.parseJSON(response);
           var content=result.content;
           $(modal).find('#modal-feedView-msg-content').html(content);
           //init__ViewMarkupDom(); // ajax 로 가져온 마크업 dom 인식시키기    
     });       
}); 

// 피드 상세보기 모달 > 좋아요 모달 오픈 이벤트  
$('#modal-feedView-like').on('show.rc.modal',function(e){
     var trigger=e.relatedTarget;
     var type = $(trigger).data('type');
     var entry = $(trigger).data('entry');
     var modal = $(trigger).data('target');

     $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=get_likePop',{
           type : type,
           entry : entry
        },function(response){
           var result = $.parseJSON(response);
           var content=result.content;
           $(modal).find('#modal-feedView-like-content').html(content);
           //init__ViewMarkupDom(); // ajax 로 가져온 마크업 dom 인식시키기    
     });       
}); 

// 피드 상세보기 모달 > 댓글 모달 오픈 이벤트  
$('#modal-feedView-comment').on('show.rc.modal',function(e){
     var trigger=e.relatedTarget;
     var type = $(trigger).data('type');
     var entry = $(trigger).data('entry');
     var modal = $(trigger).data('target');

     $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=get_commentPop',{
           type : type,
           entry : entry
        },function(response){
           var result = $.parseJSON(response);
           var content=result.content;
           $(modal).find('#modal-feedView-comment-content').html(content);
           //init__ViewMarkupDom(); // ajax 로 가져온 마크업 dom 인식시키기    
     });       
}); 

// 좋아요 모달 닫을 때 내용 지우기 
$('#modal-feedView-like').on('hide.bs.modal',function(){
     $(this).find('#modal-feedView-like-content').html('');             
});

// 피드 상세보기 모달 오픈 후 초기화 함수 실행 
$('#modal-feedView').on('shown.rc.modal',function(){
   //init_afterAjax();
});

// $('#modal-mymenuModal').on('show.rc.modal',function(){
//    $('#sheet-default').removeClass('active');
//    $('#sheet-default').hide();
// });

// content 슬라이드 변경시 추가적용 액션 정의 함수 ---> 필터 교체 
var do_contentSlide_Change=function(mod){
    var list_type=sessionStorage.getItem('list-type'); 

    $.get(rooturl+'/?r='+raccount+'&m='+module+'&a=after_contentSlide_Change',{
           mod : mod,
           list_type : list_type
      },function(response){
           var result = $.parseJSON(response);
           var content=result.content;
           $(filter_wrap).html(content); // 필터 교체 
           init_afterAjax();
     });       
}

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

// 유저 리스트 탭 : 남성, 여성, 동영상 탭 이벤트 
$(document).on('click','[data-role="userTab"]',function(){
    var tabMod = $(this).attr('data-tabMod');
    var mod= $(this).attr('data-mod');
    var feedList_wrap=$('#swiper-menu').find('.swiper-slide.active');  
   
    $('[data-role="userTab"]').removeClass('active');
    $(this).addClass('active');

    // 세션 저장 및 페이지 초기화 
    sessionStorage.setItem('userTabMod',tabMod);
    currentPage=1;
    
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_UserList',{
        mod : mod,
        tabMod : tabMod
    },function(response){
        var result=$.parseJSON(response);
        var $content=$(result.content);
        $(feedList_wrap).html($content);  
  
    });
});
var tabMod=sessionStorage.getItem('userTabMod');
tabMod=tabMod?tabMod:'tab-women';

// 무한 스크롤 적용 #################################################################################################
//var currentPage=1;

// 피드 더보기(페이징) 함수 
function get_MoreFeed(){
    var feed = $('#swiper-menu').find('.swiper-slide.active').find('[data-role="feed-item"]:last');
    var mod = $('#swiper-menu').find('.swiper-slide.active').data('mod');
    var totalPage=$(feed).data('totalpage');
    var currentPage=$(feed).data('currentpage');

    // 피드 리스트 출력 조건 값 추출 : list type & 검색조건 배열  
    var feedListCondition = getFeedFilterCondintion();
    var search_array = feedListCondition[1]; // 검색조건 배열  
    var list_type = feedListCondition[0]; // 리스트 타입 값  
    var feedList_wrap=$('[data-role="feedListWrap-'+mod+'"]');  
    var actFile;
    if(mod=='best') actFile='get_MoreUser';
    else actFile = 'get_MoreFeed';

    console.log([totalPage,currentPage]);

    if(totalPage > currentPage) {
        console.log('yes');
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a='+actFile,{
            currentPage : currentPage,
            mod : mod,
            search_array : search_array,
            type : list_type,
            tabMod : tabMod,
            userid : getUrlId
        },function(response){
            var result=$.parseJSON(response);
            var $content=$(result.content);
            $(feedList_wrap).append($content); 

           // swiper, photoswipe, drawer 리세팅   
            init_afterAjax();
        });     
        
    }
    currentPage++;
}

// 검색 현상태값 리턴 함수 
var getFeedFilterCondintion = function(){
    var mbr_sex_value = $('input[name="mbr_sex"]:checked').val();
    var mbr_country_value = $('input[name="mbr_country[]"]:checked').map(function(){return $(this).val()}).get(); 
    var sess_listType = sessionStorage.getItem('list-type'); // list-type 세션값 
    var list_type = sess_listType?sess_listType:'single'; // 최종 list-type 값  
    var search_array={"mbr_sex":mbr_sex_value,"mbr_country":mbr_country_value};
    search_array=JSON.stringify(search_array); 
    
    var feedListCondition=[list_type,search_array];// 리스트 타입과 검색조건 2 가지 값을 리턴한다. 

    return feedListCondition;
  
}

// Ajax 로 가져온 피드 리스트 출력 함수 
var printAjaxFeedList = function(mod,$content){
    var container = $('[data-role="feedListWrap-'+mod+'"]');
    $(container).html('');
    $(container).html($content);
        
    // 초기화 스크립트 초기화 
    init_afterAjax();    
}

// 피드리스트 추출 함수 
function get_FeedList(mod,actType){

    // 피드 리스트 출력 조건 값 추출 : list type & 검색조건 배열  
    var feedListCondition = getFeedFilterCondintion();
    var search_array = feedListCondition[1]; // 검색조건 배열  
    var list_type = feedListCondition[0]; // 리스트 타입 값 
  
    console.log([raccount,module]);
    //return false;

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_FeedList',{
        search_array : search_array,
        mod : mod,
        type : list_type,
        userid : getUrlId
    },function(response){
        if(actType=='search') $('#modal-filters').modal('hide'); // 필터 모달 닫기 
        
        var result = $.parseJSON(response);
        var $content = $(result.content);
        init_afterAjax(); 
        printAjaxFeedList(mod,$content);     
     
    }); 
}

//검색 처리 
$(document).on('tap','[data-role="feedSearch"]',function(){
    var $this = $(this);
    var mod = $(this).data('mod');   
    $('.dm-viewfilter .dm-right > .dm-icon-filtergray').addClass('active');  
    get_FeedList(mod,'search');      

});

// 리스트 타입 (multi or single)
$(document).on('tap','[data-role="btn-rowType"]',function(){
   var $this = $(this);
   var mod = $(this).data('mod');
   var list_type = $(this).data('type');
   sessionStorage.setItem('list-type',list_type); // 스토리지에 저장 
   
   // 활성화 표시 
   $('[data-role="btn-rowType"]').removeClass('active');
   $(this).addClass('active');

   get_FeedList(mod,'list-type');  
});


// 피드 무한 스크롤 
$('#main-content').infinitescroll({
    dataSource: function(helpers, callback) {
        setTimeout(function() {
            callback({
                content: get_MoreFeed
            });
        }, 100);
    },
    percentage: 80
});
// 무한 스크롤 ###################################################################################################################


// 필터 박스 affix 처리 *********************************************
$('.filter-wrap').scroll({
    type: 'affix',
    target: '#main-content',
    offset:{top:150},
    position : 110
});

// apply animation
$('.filter-wrap').on('affixed.rc.scroll',function(){
    $(this).addClass('animated fadeInDown');
})
$('.filter-wrap').on('affixed-top.rc.scroll',function(){
    $(this).removeClass('animated fadeInDown');
});
// 필터 박스 affix 처리 *********************************************

// footer scroll 이벤트  바인딩 
$('[data-role="bar-footer"]').on('down.rc.scroll',function(){
    $(this).animate({
         bottom: "-50px"
      }, 10, function() {
        // Animation complete.
      });
});

$('[data-role="bar-footer"]').on('up.rc.scroll',function(){
    $(this).animate({
         bottom: "0px"
      }, 10, function() {
        // Animation complete.
      });
});





$(document).ready(function(){
   sessionStorage.setItem('list-type','single');
   // swiper, photoswipe, drawer 리세팅 
   init_afterAjax();
});








