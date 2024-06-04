    const clearInput = function(text) {
        return $.trim(text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, ""));
    }

    // 템플릿 업데이트 함수 (add, edit, delete, change gid.. )
    const updateBot = function (data) {
        const botListWrapper = $('[data-role="botList-wrapper"]'),
            role = data.role;

        $.post(rooturl + '/?r=' + raccount + '&m=' + module + '&a=regis_bot', {
            data: data
        }, function (response) {
            const result = $.parseJSON(response);//$.parseJSON(response);

            if (null !== result && 'object' === typeof result && -1 === result[0]) {
                if (401 === result[1]) {
                    location.href = rooturl + '/?r=' + raccount + '&mod=login';
                } else {
                    alert(result[1]);
                }
            } else {
                if ('add' === role) {
                    $('#modal-addBot').modal('hide');
                    location.reload();
                }
            }
        });
    };

    const updateLocalBot = function (data) {
        const dataVariable = {vendor: vendor, bot: data.localBot},
            linkType = 'copyBot';

        $(".preloader").fadeIn();
        $.post(rooturl + '/?r=' + raccount + '&m=' + module + '&a=do_VendorAction', {
            linkType: linkType,
            data: dataVariable
        }, function (response) {
            $('.preloader').fadeOut();
            location.reload();
        });
        return false;
    };

    function getRemoteDBList(listArray) {
        $('[data-role="remoteBotSelect-wrapper"]').hide();

        const dbSelectWrapper = $('[data-role="remoteDBSelect-wrapper"]'),
            dbListWrapper = $('[data-role="remoteDBList-wrapper"]');
        let html = '<option value="">DB 선택 </option>';

        $.each(listArray,function(i,data){
            html+='<option value="'+data.name+'">'+data.name+'</option>';
        });
        $(dbListWrapper).html(html).promise().done(function() {
            $(dbSelectWrapper).show();
        });
    }

    function getRemoteBotList(listArray) {
        const botSelectWrapper = $('[data-role="remoteBotSelect-wrapper"]'),
            botListWrapper = $('[data-role="remoteBotList-wrapper"]');
        let html = '<option value="">챗봇 선택 </option>';

        $.each(listArray,function(i,data){
            let uid = data.uid,
                name = data.name,
                induCat = data.induCat;

            html+='<option value="'+uid+'" induCat="'+induCat+'">'+name+'</option>';
        });
        $(botListWrapper).html(html).promise().done(function() {
            $(botSelectWrapper).show();
        });
    }

    // 챗봇 등록폼 전송
    (function ($) {
        $.fn.serializeFormJSON = function () {

            let obj = {},
                arr = this.serializeArray();

            $.each(arr, function () {
                if (obj[this.name]) {
                    if (!obj[this.name].push) {
                        obj[this.name] = [obj[this.name]];
                    }
                    obj[this.name].push(this.value || '');
                } else {
                    obj[this.name] = this.value || '';
                }
            });
            return obj;
        };
    })(jQuery);

    // 노출/숨김 활성화
    $('[data-role="btn-showHide"]').on("click", function() {
        const pDiv = $(this).parent(),
            botUid = $(this).data('uid'),
            botActive_label = $('[data-role="botActive-label-' + botUid + '"]');

        if($(pDiv).hasClass("botSwitch-off")) {
            $(pDiv).removeClass("botSwitch-off");
            $(pDiv).addClass("botSwitch-on");
            $(botActive_label).text('ON');
        }else{
            $(pDiv).addClass("botSwitch-off");
            $(pDiv).removeClass("botSwitch-on");
            $(botActive_label).text('OFF');
        }

    });

    // bo
    $('[data-role="btn-saveBot"]').on('click',function(e){
        const form = $('#botForm'),
            data = form.serializeFormJSON();
        data['role'] = 'add';

        $.each(data,function(key,functionData){
            functionData = clearInput(functionData);
            if('' === functionData) $('#botForm [name="'+key+'"]').val('');
            data[key] = functionData;
        });
        let induCat = data.induCat,
            name = data.name,
            add_method = data.add_method,
            remoteServer = data.remoteServer,
            remoteDB = data.remoteDB,
            remoteBot = data.remoteBot,
            callNo = data.callno,
            localVendor = data.localVendor,
            localBot = data.localBot,
            roleType = data.roleType,
            db = $('select[name="remoteServer"] option:selected').attr('db');

        if('new' === add_method){
            if(!name){
                alert((botType === 'call' ? '콜봇': '챗봇') + '명을 선택해주세요');
                $('input[name="name"]').focus();
                return false;
            }
            if('call' === botType) {
                if(!callNo){
                    alert('전화번호를 입력해주세요');
                    $('input[name="callno"]').focus();
                    return false;
                }
                if(!isValidPhoneNumber(callNo)) {
                    alert('전화번호가 올바르지 않습니다.');
                    $('input[name="callno"]').focus();
                    return false;
                }
            }
            if(!induCat && 'bot' === roleType){
                alert('업종을 선택해주세요');
                return false;
            }
            updateBot(data);
        }else if('copy' === add_method){
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
        }else if('local' === add_method){
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

    // 챗봇 추가 방식 선택
    $('input[name="_add_method"]').click(function(){
        const val = $(this).val(),
            localServerWrapper = $('[data-role="localServer-wrapper"]'),
            remoteServerWrapper = $('[data-role="remoteServer-wrapper"]'),
            dsbjWrapper = $('[data-role="dsbj-wrapper"]');
        $('input:hidden[name=add_method]').val(val);

        if('copy' === val){
            $(remoteServerWrapper).show();
            $(dsbjWrapper).hide(); // 기본항목 숨김
            $('.b_local').hide();

        }else if('local' === val){
            $(localServerWrapper).show();
            $(remoteServerWrapper).hide();
            $(dsbjWrapper).hide(); // 기본항목 숨김

        }else if('new' === val){
            $('.b_remote').hide();
            $('.b_local').hide();
            $(dsbjWrapper).show(); // 기본항목 보이기
        }
    });

    // 로컬 벤더 선택 이벤트
    $('select[name="localVendor"]').on('change',function(){
        const val = $('>option:selected', this).val();

        if(val){
            $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
                vendor: val,
                botType: botType,
                linkType: 'get-localBotList'
            },function(response){
                const result=$.parseJSON(response),//$.parseJSON(response);
                    botSelectWrapper = $('[data-role="localBotSelect-wrapper"]'),
                    botListWrapper = $('[data-role="localBotList-wrapper"]');
                let html = '<option value="">챗봇 선택 </option>';

                $.each(result,function(i,data){
                    let uid = data.uid,
                        name = data.name,
                        induCat = data.induCat;

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
        const val = $('>option:selected', this).val(),
            db = $('>option:selected', this).attr('db'),
            data = {"server": val};
        let linkType;

        $('[data-role="remoteDBSelect-wrapper"]').hide();
        $('[data-role="remoteBotSelect-wrapper"]').hide();

        if(val){
            linkType = db ? 'get-remoteDBList' : 'get-remoteBotList';

            $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
                data: data,
                linkType: linkType
            },function(response){
                const result=$.parseJSON(response);//$.parseJSON(response);

                if('get-remoteBotList' === linkType) {
                    getRemoteBotList(result.content);
                } else {
                    getRemoteDBList(result.content);
                }
            });
        }
    });

    $('select[name="remoteDB"]').on('change',function(){
        const val = $(this).val(),
            linkType = 'get-remoteBotList',
            data = {"server": $('select[name="remoteServer"] option:selected').val(), "db": val};

        if(val){
            $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
                data: data,
                linkType: linkType
            },function(response){
                const result=$.parseJSON(response);//$.parseJSON(response);

                getRemoteBotList(result.content);
            });
        }
    });

    // 템플릿 지정 체크박스 선택/해제 이벤트
    $('[data-role="chkBox-isTemp"]').on('click',function(){
        let val = $(this).val();

        return false;
    });

    // 선택박스 체크 이벤트 핸들러
    $('[data-role="select-all"]').click(function(){
        const parent = $(this).data('parent');

        $(parent).find('tbody [data-role="checkbox"]').prop("checked",$(this).prop("checked"));
    });

    $('[data-role="btn-act"]').on('click',function(){
        const act = $(this).data("act");
        let uid_arr = $('input[name="uid[]"]:checked').map(function(){return $(this).val()}).get(),
            linkType,
            data = {"uid_arr": JSON.stringify(uid_arr)};

        if(0 === uid_arr.length){
            alert('데이터가 선택되지 않았습니다.');
            return false;
        }else{
            if('del' === act){
                let cfm = confirm('선택하신 데이터를 삭제하시겠습니까?');
                if(false === cfm){
                    return false;
                }
            }
        }

        if('del' === act) linkType = 'multiDeleteBot';
        else linkType = act;

        $.post(rooturl+'/?r='+raccount+'&m='+moduleid+'&a=do_AdminAction',{
            data: data,
            linkType: linkType
        },function(response){
            location.reload();
        });
    });

    $('.glyphicon').click(function(){
       if($(this).hasClass('glyphicon-th-list')){
           location.href = '/adm/list';
       }
       else{
           location.href = '/adm/main';
       }
    });

    $(document).on('click','[data-role="botMenu"]',function(e){
        const action = $(this).data('action'),
            vendor = $(this).data('vendor'),
            bot = $(this).data('bot'),
            data = {vendor: vendor, bot:bot};
        let linkType;

        if('dialog' === action){
            location.href = rooturl+'/adm/graph?bot='+bot+'&roleType='+roleType;

            return false;
        }else{
            if('delete' === action){
                const cfm = confirm('관련 데이타 모두 삭제하시겠습니까?');

                if(true === cfm) linkType = 'deleteBot';
                else return false;
            }
            else if('copy' === action) linkType = 'copyBot';
            else linkType = action;

            if('delete' === action || 'copy' === action){
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

    $('[data-toggle="modal"]').on('click', function() {
        $('#modal-addBot').removeClass("fade").show();
    });

    $('[data-dismiss="modal"]').on('click', function() {
        $('#modal-addBot').addClass("fade").hide();
    });

    $('body').tooltip({
        selector: '[data-tooltip=tooltip]',
        container: 'body'
    });

    $(function (){
        if (mySuper) {
            $(".btn-addBot").show();
        }

        if ('call' !== botType) {
            $("#sortField option[value='callno']").remove();
            $("#searchField option[value='callno']").remove();
            $("#callDiv").remove(); // botModal callDiv 삭제
        }

        if (myManager) {
            $(".botMenu-wrapper").remove();
        }

        // main 페이지 검색, 정렬시 선택값 고정
        $("#sortField").val(sortFieldValue);
        $("#searchField").val(searchFieldValue);

        //botModal 원격 복사 시작시 select 태그 내에 들어가는 option
        Object.keys(copyRemoteServers).forEach(function(key) {
            let item = copyRemoteServers[key];
            let option = $('<option>').val(key).attr('db', item.need_db).text(key);
            $('select[name="remoteServer"]').append(option);
        });
    })
