<script>
var vendor = '<?php echo $V['uid']?>';
var bot = '<?php echo $bot?>';
var module ='<?php echo $m?>';
var addBotModal = $('#modal-addBot');
var botQty = '<?php echo $botQty?$botQty:0?>';


// 템플릿 업데이트 함수 (add, edit, delete, change gid.. )
var updateBot = function(data){
    var botListWrapper = $('[data-role="botList-wrapper"]');
    var role = data.role;
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=regis_bot',{
        data: data
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);

        if(role=='add'){
            $(addBotModal).modal('hide');
            location.reload();
        } 
    }); 
};

// 챗봇 등록폼 전송  
(function ($) {
    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
})(jQuery);

// 노출/숨김 활성화  
$('[data-role="btn-showHide"]').on("click", function() {
    var pDiv = $(this).parent();
    var botuid = $(this).data('uid');
    var notify_container = $(this).data('container');
    var botActive_label = $('[data-role="botActive-label-'+botuid+'"]');

    if($(pDiv).hasClass("botSwitch-off")) {
        $(pDiv).removeClass("botSwitch-off");
        $(pDiv).addClass("botSwitch-on");
        $(botActive_label).text('ON');
       // setBotActive('on',botuid,notify_container);  
    }else{
        $(pDiv).addClass("botSwitch-off");
        $(pDiv).removeClass("botSwitch-on");
        $(botActive_label).text('OFF');
        //setBotActive('off',botuid,notify_container);      
    }

});

// bo
$('[data-role="btn-saveBot"]').on('click',function(e){
    var form = $('#botForm');
    var data = $('#botForm').serializeFormJSON();
    data['role'] = 'add';
    var induCat = data.induCat;
    var name = data.name;
    var add_method = data.add_method;
    var remoteServer = data.remoteServer;
    var remoteBot = data.remoteBot;

    if(add_method=='new'){
        if(!name){
            alert('챗봇명을 선택해주세요');
            setTimeout(function(){
               $('input[name="name"]').focus();
            },10);
            
            return false;
        }else if(!induCat){
             alert('업종을 선택해주세요');
            return false;
        }
        else updateBot(data);     
    }else if(add_method=='copy'){
        if(!remoteServer){
            alert('서버를 선택해주세요');
            return false;
        }else if(!remoteBot){
            alert('챗봇을 선택해주세요');
            return false;
        }
        else updateBot(data);
    }    
       
});

$('body').tooltip({
    selector: '[data-tooltip=tooltip]',
    container: 'body'
}); 

// 챗봇 추가 방식 선택
$('input[name="add_method"]').click(function(){
    var val = $(this).val();
    var remoteServerWrapper = $('[data-role="remoteServer-wrapper"]');
    var dsbjWrapper = $('[data-role="dsbj-wrapper"]');
    if(val =='copy'){
        $(remoteServerWrapper).show();
        $(dsbjWrapper).hide(); // 기본항목 숨김 

    }else if(val =='new'){
        $(remoteServerWrapper).hide();
        $(dsbjWrapper).show(); // 기본항목 보이기  
    }
    
}); 

// 리모트 서버 선택 이벤트 
// 챗봇 추가 방식 선택
$('select[name="remoteServer"]').on('change',function(){
    var val = $(this).val();
    var linkType = 'get-remoteBotList';
    var botSelectWrapper = $('[data-role="remoteBotSelect-wrapper"]');
    var botListWrapper = $('[data-role="remoteBotList-wrapper"]');
    var data = {
        "server": val, // cloud,kiosk
    };
    var getBotList = function(listArray){
        var html = '<option value="">챗봇 선택 </option>';
        $.each(listArray,function(i,data){
            var uid = data.uid;
            var name = data.name;
            html+='<option value="'+uid+'">'+name+'</option>';
        });

        return html;
    }

    if(val){
        $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
            data: data,
            linkType: linkType
        },function(response){
            var result=$.parseJSON(response);//$.parseJSON(response);
            var botList = getBotList(result.content);
            
            $(botListWrapper).html(botList);
            setTimeout(function(){
               $(botSelectWrapper).show(); 
            },20);             
      
        });     
    }    
    
}); 

// 템플릿 지정 체크박스 선택/해제 이벤트 
$('[data-role="chkBox-isTemp"]').on('click',function(){
    var val = $(this).val();

});

// 선택박스 체크 이벤트 핸들러
$('[data-role="select-all"]').click(function(){
    var parent = $(this).data('parent'); 
    $(parent).find('tbody [data-role="checkbox"]').prop("checked",$(this).prop("checked"));
});

$('[data-role="btn-act"]').on('click',function(){
    var act = $(this).data("act");
    var uid_arr = $('input[name="uid[]"]:checked').map(function(){return $(this).val()}).get();
    var linkType;


    var data = {
        "uid_arr": JSON.stringify(uid_arr),
    };

    if(uid_arr.length==0){
        alert('데이터가 선택되지 않았습니다.');  
        return false;
    }else{
        if(act =='del'){
            var cfm = confirm('선택하신 데이터를 삭제하시겠습니까?');
            if(cfm ==false){
                return false;
            }    
        }            
    }  

    if(act =='del') linkType = 'multiDeleteBot';
    else linkType = act;

    $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
        data: data,
        linkType: linkType
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        location.reload();

    }); 
});   

$(document).on('click','[data-role="botMenu"]',function(){
    var action = $(this).data('action');
    var vendor = $(this).data('vendor');
    var bot = $(this).data('bot');
    var data = {vendor: vendor, bot:bot};
    var linkType;

    if(action=='dialog'){
        location.href = rooturl+'/adm/graph&bot='+bot;
    }else if(action =='delete'){        
        var cfm = confirm('관련 데이타 모두 삭제하시겠습니까?');
        if(cfm==true) linkType = 'deleteBot';
        else return false;
    }
    else if(action == 'copy') linkType = 'copyBot';
    else linkType = action;

    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
        linkType: linkType,        
        data: data
    },function(response){
        location.reload();

    }); 
    return false; 
});

</script> 