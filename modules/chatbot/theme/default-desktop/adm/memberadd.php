<?
$query = "Select A.uid as mbruid, A.id, B.level, B.name, B.email, B.last_log, B.d_regis, B.super, C.name as groupname From rb_s_mbrid A ";
$query .="left join rb_s_mbrdata B on A.uid = B.memberuid ";
$query .="left join rb_s_mbrgroup C on B.mygroup = C.uid ";
$query .="Where B.auth=1 and B.admin <> 1 ";
$query .="Order by A.uid desc ";
$RCD = db_query($query,$DB_CONNECT);
$aMember = [];
while($R = db_fetch_array($RCD)) {
    $R['levelName'] = $R['level'] == 1 ? '관리자' : '일반사용자';
    $R['last_log'] = $R['last_log'] ? date("Y-m-d H:i:s", strtotime($R['last_log'])) : '';
    $R['d_regis'] = date("Y-m-d H:i:s", strtotime($R['d_regis']));
    $R['btnClass'] = $R['super'] ? 'btn-default' : 'btn-primary';
    $R['disabled'] = $R['super'] ? 'disabled' : '';
    $aMember[] = $R;
}
?>
<style>
    .table-full th, .table-full td {text-align:center;}
</style>
<div class="container-fluid table-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">사용자 관리 > 사용자 추가/수정</h4>
        </div>
    </div>

    <div class="table-container">
        <div class="row table-tool">
            <div class="col-lg-12 col-md-12 hidden-sm">
                <button type="button" class="btn btn-primary" onclick="getManagerInfo()">사용자 등록</button>
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="intEntTable-wrapper">
            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                <table class="table table-striped table-full" id="tbl-intentSet" data-role="tbl-intentSet">
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-name">이름</th>
                            <th class="intEnt-des">아이디</th>
                            <th class="intEnt-name">그룹</th>
                            <th class="intEnt-name">레벨</th>
                            <th class="intEnt-des">접속일시</th>
                            <th class="intEnt-ex">등록일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                    <? foreach($aMember as $_data) {?>
                        <tr class="tr_<?=$_data['mbruid']?>">
                            <td class="txt-oflo">
                                <?=$_data['name']?>
                            </td>
                            <td class="txt-oflo">
                                <?=$_data['id']?>
                            </td>
                            <td class="txt-oflo">
                                <?=$_data['groupname']?>
                            </td>
                            <td class="txt-oflo">
                                <?=$_data['levelName']?>
                            </td>
                            <td class="txt-oflo">
                                <span class="cb-date"><?=$_data['last_log']?></span>
                            </td>
                            <td class="txt-oflo">
                                <span class="cb-date"><?=$_data['d_regis']?></span>
                            </td>
                            <td class="txt-oflo">
                                <button type="button" class="btn <?=$_data['btnClass']?>" data-role="btn-mmodify" data-uid="<?=$_data['mbruid']?>" <?=$_data['disabled']?> style="padding:3px 10px;">수정</button>
                                <button type="button" class="btn <?=$_data['btnClass']?>" data-role="btn-mdelete" data-uid="<?=$_data['mbruid']?>" <?=$_data['disabled']?> style="padding:3px 10px;">삭제</button>
                            </td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal-managerinfo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
</div>

<script>
    $(document).ready(function() {
        const isPwChange = "<?php echo $_SESSION['is_pw_change']; ?>",
            loginMbruid = "<?php echo $_SESSION['mbr_uid']; ?>";

        if (isPwChange === "Y") {
            getManagerInfo(loginMbruid);
        }
    });
    function getManagerInfo(uid) {
        var uid = uid == undefined || uid == '' ? '' : uid
        $("#modal-managerinfo").load('/?r=<?=$r?>&m=<?=$m?>&a=get_memberadd&uid='+uid, function(){
             $("#modal-managerinfo").modal();
        });
    }
    $(document).on("click", "[data-role=btn-mmodify]", function() {
        getManagerInfo($(this).data("uid"));
    });
    $(document).on("click", "[data-role=btn-mdelete]", function() {
        if(!confirm("삭제하시겠습니까?")) return false;
        var uid = $(this).data("uid");
        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=save_member', {
            linkType:'member_delete', mbruid: uid
        }, function(response) {
            if(response.result) {
                $(".tr_"+uid).remove();
            } else {
                alert(response.msg);
            }
        },'json');
        return false;
    });
</script>