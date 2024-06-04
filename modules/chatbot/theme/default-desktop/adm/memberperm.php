<?php
$query = "Select A.* From rb_s_mbrgroup A Order by A.gid asc ";
$RCD = db_query($query,$DB_CONNECT);
$aGroup = [];
while($R = db_fetch_array($RCD)) {
    $aGroup[] = ['uid'=>$R['uid'], 'name'=>$R['name']];
}

$query = "Select A.* From rb_s_menu A Where A.parent = 27 and A.hidden = 0 Order by A.gid ASC ";
$RCD = db_query($query,$DB_CONNECT);
$aMenu = [];
while($R = db_fetch_array($RCD)) {
    $aMenu[$R['uid']] = ['id'=>$R['id'], 'name'=>('chat' === $_SESSION['bottype']) ? $R['name'] : str_replace('챗봇', '콜봇', $R['name']), 'aSub'=>[]];

    $query = "Select A.* From rb_s_menu A Where A.parent = ".$R['uid']." and A.hidden = 0 Order by A.gid ASC ";
    $SRCD = db_query($query,$DB_CONNECT);
    while($SR = db_fetch_array($SRCD)) {
        if ('api' === $SR['id']) {
            continue;
        }
        if ('call' === $_SESSION['bottype'] && 'intro' === $SR['id']) {
            continue;
        }
        if ('chat' === $_SESSION['bottype'] && 'blackList' === $SR['id']) {
            continue;
        }
        $aMenu[$SR['parent']]['aSub'][] = ['id'=>$SR['id'], 'name'=>$SR['name']];
    }
}
$_member = ['id'=>'mem', 'name'=>'사용자 관리'];
$_member['aSub'] = [['id'=>'membergroup', 'name'=>'사용자 그룹 관리'], ['id'=>'memberperm', 'name'=>'그룹 권한 관리'], ['id'=>'memberadd', 'name'=>'사용자 추가/수정']];
$aMenu[] = $_member;

?>

<!-- bootstrap css -->
<style>
    .tgroupWrap {position: relative; display: flex; flex: 1;}
    .tgroup {position: relative; background: #fff; border: 1px solid #cbcbcb; display: flex; flex-direction: column; overflow: hidden;}
    .group_list {position:relative; border-top:1px solid #e4e7ea;}
    .group_list li {display:block; border-bottom:1px solid #e4e7ea;}
    .group_list li.on {background-color:#eef1ff;}
    .group_list li a {display:block; padding:10px; cursor:pointer; color:#797979; text-decoration:none;}
    .group_list li.on a {font-weight:600;}
    .menu_list {position:relative; border-top:1px solid #e4e7ea;}
    .menu_list label {margin-bottom:0;}
    .menu_list>li {padding:15px 10px; border-bottom:1px solid #e4e7ea;}
    .menu_list li input[type="checkbox"] {width:17px; height:17px; margin-top:0; vertical-align:middle; margin-right:5px;}
    .menu_list>li>label {font-weight:600;}
    .menu_list .sub {position:relative; margin-top:15px;}
    .menu_list .sub li {position:relative; display:inline-block; width:15%; margin:5px 0;}
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">사용자 관리 > 그룹 권한 관리</h4>
        </div>
    </div>

    <div class="row tgroupWrap">
        <div class="col-xs-4 tgroup">
            <div class="white-box nodeInfoWrap">
                <h3 class="box-title">그룹 목록</h3>
                <ul class="group_list">
                    <? foreach($aGroup as $key=>$_data) {?>
                    <li><a data-uid="<?=$_data['uid']?>"><?=$_data['name']?></a></li>
                    <?}?>
                </ul>
            </div>
        </div>

        <!-- 대화상자 현황 -->
        <div class="col-xs-8 tgroup" style="margin-left:40px;">
            <div class="white-box nodeInfoWrap">
                <h3 class="box-title flex">
                    <span>메뉴 접근 권한</span>
                    <button type="button" class="btn btn-primary" style="float:right;" onclick="getMenuPerm()">저장
                    </button>
                </h3>
                <ul class="menu_list">
                    <? foreach ($aMenu as $key => $_data) { ?>
                        <li>
                            <label>
                                <input type="checkbox" name="<?= $_data['id'] ?>" value="<?= $_data['id'] ?>"
                                       class="m_main"> <?= $_data['name'] ?>
                            </label>
                            <? if (count($_data['aSub']) > 0) { ?>
                                <ul class="sub">
                                    <? foreach ($_data['aSub'] as $_sub) { ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="<?= $_sub['id'] ?>"
                                                       value="<?= $_sub['id'] ?>"> <?= $_sub['name'] ?>
                                            </label>
                                        </li>
                                    <? } ?>
                                </ul>
                            <? } ?>
                        </li>
                    <? } ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(".group_list a").on("click", function() {
        $(this).closest("ul").find("li").removeClass("on");
        $(this).parent().addClass("on");
        $.post("/?r=<?=$r?>&m=<?=$m?>&a=save_member", {
            linkType:'get_member_perm', group_uid: $(this).data("uid")
        }, function(response) {
            if(response.result) {
                console.log(response.data);
                $("ul.menu_list input:checkbox").each(function() {
                    var _checked = response.data.indexOf($(this).attr("name")) > -1 ? true : false;
                    $(this).prop("checked", _checked);
                });
            }
        },'json');

    });

    $(".menu_list input[type=checkbox]").on("change", function() {
        if($(this).hasClass("m_main")) {
            $(this).closest("li").find("ul.sub input[type=checkbox]").prop("checked", $(this).prop("checked"));
        } else {
            let isAllChecked = true;

            $(this).closest(".sub").find('input[type="checkbox"]').each(function() {
                if (!$(this).prop("checked")) {
                    isAllChecked = false;
                }
            });

            if (isAllChecked) {
                $(this).closest(".sub").closest("li").find(".m_main").prop("checked", true);
            } else {
                $(this).closest(".sub").closest("li").find(".m_main").prop("checked", false);
            }
        }
    });

    function getMenuPerm() {
        var group_uid = $(".group_list li.on a").data("uid");
        var menus = $(".menu_list input[type=checkbox]").map(function(){
            if($(this).prop("checked")) return $(this).val();
        }).get().join(",");

        if(!group_uid) {
            alert("그룹을 선택해주세요.");return false;
        }
        $.post("/?r=<?=$r?>&m=<?=$m?>&a=save_member", {
            linkType:'member_perm', group_uid: group_uid, menus: menus
        }, function(response) {
            if(response.result) {
                alert("적용되었습니다.");
            } else {
                alert(response.msg);
            }
        },'json');
    }

    $(".group_list li:first-child a").click();
</script>