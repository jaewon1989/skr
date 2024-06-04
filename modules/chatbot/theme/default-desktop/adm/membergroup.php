<?
$query = "Select A.*, B.nCnt From rb_s_mbrgroup A ";
$query .="left join (Select A.uid, count(*) as nCnt From rb_s_mbrgroup A, rb_s_mbrdata B Where A.uid = B.mygroup and B.auth = 1 AND B.admin != 1 Group by A.uid) as B on A.uid = B.uid ";
$query .="Order by A.gid asc ";
$RCD = db_query($query,$DB_CONNECT);
$aGroup = [];
while($R = db_fetch_array($RCD)) {
    $bots = $R['bot'] ? explode(",", $R['bot']) : [];
    $botArray = [];
    foreach ($bots as $idx => $bot) {
        $query = "
                    SELECT  uid, bottype
                    FROM    rb_chatbot_bot
                    WHERE   uid = " . $bot . "
                ";
        $botInfo = db_fetch_assoc(db_query($query, $DB_CONNECT));
        ++$botArray[$botInfo['bottype']];
    }

    $aGroup[] = array('uid'=>$R['uid'], 'name'=>$R['name'], 'bot'=>number_format($botArray[$_SESSION['bottype']]), 'user'=>number_format($R['nCnt']));
}

?>
<style>
    .table-full th, .table-full td {text-align:center;}
</style>
<div class="container-fluid table-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">사용자 관리 > 사용자 그룹 관리</h4>
        </div>
    </div>

    <div class="table-container">
        <div class="row table-tool">
            <div class="col-lg-12 col-md-12 hidden-sm">
                <button type="button" class="btn btn-primary" onclick="getGroupInfo()">그룹 등록</button>
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="intEntTable-wrapper">
            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                <table class="table table-striped table-full" id="tbl-intentSet" data-role="tbl-intentSet">
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-name">그룹명</th>
                            <th class="intEnt-name"><?php echo 'chat' === $_SESSION['bottype'] ? "챗봇" : '콜봇' ?></th>
                            <th class="intEnt-des">그룹원</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                    <? foreach($aGroup as $_data) {?>
                        <tr class="tr_<?=$_data['uid']?>">
                            <td class="txt-oflo">
                                <?=$_data['name']?>
                            </td>
                            <td class="txt-oflo">
                                <?=$_data['bot']?>
                            </td>
                            <td class="txt-oflo">
                                <?=$_data['user']?>
                            </td>
                            <td class="txt-oflo">
                                <button type="button" class="btn btn-primary" data-role="btn-mmodify" data-uid="<?=$_data['uid']?>" style="padding:3px 10px;">수정</button>
                                <button type="button" class="btn btn-<?=($_data['uid']==1 ? 'default' : 'primary')?>" data-role="btn-mdelete" data-uid="<?=$_data['uid']?>" style="padding:3px 10px;" <?=($R['uid']==1 ? 'disabled' : '')?>>삭제</button>
                            </td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal-groupinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
</div>

<script>
    function getGroupInfo(uid) {
        var uid = uid == undefined || uid == '' ? '' : uid
        $("#modal-groupinfo").load('/?r=<?=$r?>&m=<?=$m?>&a=get_membergroup&uid='+uid, function(){
             $("#modal-groupinfo").modal();
        });
    }
    $(document).on("click", "[data-role=btn-mmodify]", function() {
        getGroupInfo($(this).data("uid"));
    });
    $(document).on("click", "[data-role=btn-mdelete]", function() {
        if(!confirm("삭제하시겠습니까?")) return false;
        var uid = $(this).data("uid");
        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=save_member', {
            linkType:'group_delete', group_uid: uid
        }, function(response) {
            if(response.result) {
                $(".tr_"+uid).remove();
            } else {
                alert(response.msg);
            }
        },'json');
        return false;
    });

    $(document).on("click", ".btn_group_bot", function() {
        var action = $(this).data("action");
        if(action == "add") {
            var botUid = $("#bot_list option:selected").val();
            if(!botUid) return false;
            var botName = $("#bot_list option[value="+botUid+"]").text();
            var html = '<div class="alert alert-info alert-dismissible mb-10" role="alert" style="padding:8px 12px;margin-bottom:5px;">'+
                       '     <input type="hidden" class="bot_uid" name="group_botUid[]" value="'+botUid+'">'+
                       '     <span class="bot_name">'+botName+'</span>'+
                       '     <span class="pull-right btn_group_bot" style="color: #31708F; cursor: pointer;" data-placement="top" title="" data-action="delete">'+
                       '         <i class="fas fa-times"></i>'+
                       '     </span>'+
                       '</div>';
            $("#bot_list option[value="+botUid+"]").remove();
            $("#gbot_list").append(html);
        } else {
            var botUid = $(this).siblings(".bot_uid").val();
            var botName = $(this).siblings(".bot_name").text();
            var html = '<option value="'+botUid+'">'+botName+'</option>';
            $(this).parent().remove();
            $("#bot_list").append(html);
        }
    });

    function groupAddBot(action, botUid) {
        if(action == "add") {
            var botName = $("#bot_list option[value="+botUid+"]").text();
            var html = '<div class="alert alert-info alert-dismissible mb-10" role="alert" style="padding:8px 12px;margin-bottom:5px;">'+
                       '     <input type="hidden" class="bot_uid" name="group_botUid[]" value="'+botUid+'">'+
                       '     <span class="bot_name">'+botName+'</span>'+
                       '     <span class="pull-right btn_group_bot" style="color: #31708F; cursor: pointer;" data-placement="top" title="" data-action="delete">'+
                       '         <i class="fas fa-times"></i>'+
                       '     </span>'+
                       '</div>';
            $("#bot_list option[value="+botUid+"]").remove();
            $("#gbot_list").append(html);
        } else {
            var obj = $("#gbot_list").find("input:hidden[value="+botUid+"]");
            var botName = $(obj).next().text();
            var html = '<option value="'+botUid+'">'+botName+'</option>';
            $(obj).parent().remove();
            $("#bot_list").append(html);
        }
    }

    function groupFormCheck(obj) {
        if(!obj.find(":input[name=name]").val()) {
            alert("그룹명을 입력해주세요."); obj.find(":input[name=name]").focus(); return false;
        }

        var data = obj.serialize();

        $.post("/?r=<?=$r?>&m=<?=$m?>&a=save_member", data, function(response) {
            if(response.result) {
                location.reload();
            } else {
                alert(response.msg);
            }
        },'json');
        return false;
    }
</script>