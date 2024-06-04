<script>
var vendor = '<?php echo $V['uid']?>';
var bot = '<?php echo $bot?>';
var botType = '<?php echo $bottype?>';
var module ='<?php echo $m?>';
var addBotModal = $('#modal-addBot');
var botQty = '<?php echo $botQty?$botQty:0?>';

var clearInput = function(text) {
    text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");
    return $.trim(text);
}

// 템플릿 업데이트 함수 (add, edit, delete, change gid.. )
var updateBot = function(data){
    var botListWrapper = $('[data-role="botList-wrapper"]');
    var role = data.role;
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=regis_bot',{
        data: data
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        if(result !== null && typeof result === 'object' && result[0] == -1) {
            if(result[1] == 401) {
                location.href=rooturl+'/?r='+raccount+'&mod=login';
            } else {
                alert(result[1]);
            }
        } else {
            if(role=='add'){
                $(addBotModal).modal('hide');
                location.reload();
            }
        }
    });
};

var updateLocalBot = function(data){
    var data = {vendor: vendor, bot:data.localBot};
    var linkType = 'copyBot';
    $(".preloader").fadeIn();
    $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
        linkType: linkType,
        data: data
    },function(response){
        $('.preloader').fadeOut();
        location.reload();
    });
    return false;
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
    $.each(data,function(key,fdata){
        fdata = clearInput(fdata);
        if(fdata == '') $('#botForm [name="'+key+'"]').val('');
        data[key] = fdata;
    });
    var induCat = data.induCat;
    var name = data.name;
    var add_method = data.add_method;
    var remoteServer = data.remoteServer;
    var remoteDB = data.remoteDB;
    var remoteBot = data.remoteBot;
    var bottype = data.bottype;
    var callno = data.callno;
    var localVendor = data.localVendor;
    var localBot = data.localBot;
    var roleType = '<?php echo $_GET['role']?$_GET['role']:'bot'?>';
    var db = $('select[name="remoteServer"] option:selected').attr('db');

    if(add_method=='new'){
        if(!name){
            alert('<?php echo $_botType?>명을 선택해주세요');
            $('input[name="name"]').focus();
            return false;
        }
        if(bottype == 'call') {
            if(!callno){
                alert('전화번호를 입력해주세요');
                $('input[name="callno"]').focus();
                return false;
            }
            if(!isValidPhoneNumber(callno)) {
                alert('전화번호가 올바르지 않습니다.');
                $('input[name="callno"]').focus();
                return false;
            }
        }
        if(!induCat && roleType=='bot'){
            alert('업종을 선택해주세요');
            return false;
        }
        updateBot(data);
    }else if(add_method=='copy'){
        if(!remoteServer){
            alert('서버를 선택해주세요');
            return false;
        }else if(db && !remoteDB) {
            alert('DB를 선택해주세요');
            return false;
        }else if(!remoteBot){
            alert('챗봇을 선택해주세요');
            return false;
        } else {
            data.name = $('[data-role="remoteBotList-wrapper"] option:selected').text();
            data.induCat = $('[data-role="remoteBotList-wrapper"] option:selected').attr('induCat');
            updateBot(data);
        }
    }else if(add_method=='local'){
        if(!localVendor){
            alert('벤더를 선택해주세요');
            return false;
        }else if(!localBot){
            alert('챗봇을 선택해주세요');
            return false;
        } else {
            data.name = $('[data-role="localBotList-wrapper"] option:selected').text();
            data.induCat = $('[data-role="localBotList-wrapper"] option:selected').attr('induCat');
            updateLocalBot(data);
        }
    }
});

$('body').tooltip({
    selector: '[data-tooltip=tooltip]',
    container: 'body'
});

// 챗봇 추가 방식 선택
$('input[name="_add_method"]').click(function(){
    var val = $(this).val();
    var localServerWrapper = $('[data-role="localServer-wrapper"]');
    var remoteServerWrapper = $('[data-role="remoteServer-wrapper"]');
    var dsbjWrapper = $('[data-role="dsbj-wrapper"]');
    $('input:hidden[name=add_method]').val(val);
    if(val =='copy'){
        $(remoteServerWrapper).show();
        $(dsbjWrapper).hide(); // 기본항목 숨김
        $('.b_local').hide();

    }else if(val =='local'){
        $(localServerWrapper).show();
        $(remoteServerWrapper).hide();
        $(dsbjWrapper).hide(); // 기본항목 숨김

    }else if(val =='new'){
        $('.b_remote').hide();
        $('.b_local').hide();
        $(dsbjWrapper).show(); // 기본항목 보이기
    }
});

// 로컬 벤더 선택 이벤트
$('select[name="localVendor"]').on('change',function(){
    var val = $('>option:selected', this).val();
    if(val){
        $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
            vendor: val,
            botType: botType,
            linkType: 'get-localBotList'
        },function(response){
            var result=$.parseJSON(response);//$.parseJSON(response);
            var botSelectWrapper = $('[data-role="localBotSelect-wrapper"]');
            var botListWrapper = $('[data-role="localBotList-wrapper"]');
            var html = '<option value="">챗봇 선택 </option>';
            $.each(result,function(i,data){
                var uid = data.uid;
                var name = data.name;
                var induCat = data.induCat;
                html+='<option value="'+uid+'" induCat="'+induCat+'">'+name+'</option>';
            });
            $(botListWrapper).html(html).promise().done(function() {
                $(botSelectWrapper).show();
            });
        });
    }
});

// 리모트 서버 선택 이벤트
// 챗봇 추가 방식 선택
$('select[name="remoteServer"]').on('change',function(){
    var val = $('>option:selected', this).val();
    var db = $('>option:selected', this).attr('db');
    var linkType;
    var data = {"server": val};

    $('[data-role="remoteDBSelect-wrapper"]').hide();
    $('[data-role="remoteBotSelect-wrapper"]').hide();

    if(val){
        linkType = db ? 'get-remoteDBList' : 'get-remoteBotList';

        $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
            data: data,
            linkType: linkType
        },function(response){
            var result=$.parseJSON(response);//$.parseJSON(response);
            if(linkType == 'get-remoteBotList') {
                getRemoteBotList(result.content);
            } else {
                getRemoteDBList(result.content);
            }
        });
    }
});
$('select[name="remoteDB"]').on('change',function(){
    var val = $(this).val();
    var linkType = 'get-remoteBotList';
    var data = {"server": $('select[name="remoteServer"] option:selected').val(), "db": val};

    if(val){
        $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
            data: data,
            linkType: linkType
        },function(response){
            var result=$.parseJSON(response);//$.parseJSON(response);
            getRemoteBotList(result.content);
        });
    }
});

function getRemoteDBList(listArray) {
    $('[data-role="remoteBotSelect-wrapper"]').hide();

    var dbSelectWrapper = $('[data-role="remoteDBSelect-wrapper"]');
    var dbListWrapper = $('[data-role="remoteDBList-wrapper"]');
    var html = '<option value="">DB 선택 </option>';
    $.each(listArray,function(i,data){
        html+='<option value="'+data.name+'">'+data.name+'</option>';
    });
    $(dbListWrapper).html(html).promise().done(function() {
        $(dbSelectWrapper).show();
    });
}

function getRemoteBotList(listArray) {
    var botSelectWrapper = $('[data-role="remoteBotSelect-wrapper"]');
    var botListWrapper = $('[data-role="remoteBotList-wrapper"]');
    var html = '<option value="">챗봇 선택 </option>';
    $.each(listArray,function(i,data){
        var uid = data.uid;
        var name = data.name;
        var induCat = data.induCat;
        html+='<option value="'+uid+'" induCat="'+induCat+'">'+name+'</option>';
    });
    $(botListWrapper).html(html).promise().done(function() {
        $(botSelectWrapper).show();
    });
}

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

$(document).on('click','[data-role="botMenu"]',function(e){
    var action = $(this).data('action');
    var vendor = $(this).data('vendor');
    var bot = $(this).data('bot');
    var data = {vendor: vendor, bot:bot};
    var linkType;

    if(action=='dialog'){
        location.href = rooturl+'/adm/graph?bot='+bot+'&roleType='+roleType;
        return false;
    }else{
        if(action =='delete'){
            var cfm = confirm('관련 데이타 모두 삭제하시겠습니까?');
            if(cfm==true) linkType = 'deleteBot';
            else return false;
        }
        else if(action == 'copy') linkType = 'copyBot';
        else linkType = action;

        if(action=='delete' || action=='copy'){
             $(".preloader").fadeIn();
        }
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
            linkType: linkType,
            data: data
        },function(response){
            $('.preloader').fadeOut();
            location.reload();

        });
        return false;
    }
});

    <?php if($my['manager']) { ?>
        $(".botMenu-wrapper").remove();
    <?php } ?>
</script>