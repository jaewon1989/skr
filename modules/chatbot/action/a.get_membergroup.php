<?
$uid = trim($_GET['uid']);

$mod = $uid ? 'modify' : 'add';
$modText = $uid ? '수정' : '등록';

$botList = [];
$query = "Select A.* From rb_chatbot_bot A ";
$query .="Where A.role = 'bot' and A.hidden = 0 and A.display = 1 ";
$query .="Order by A.bottype desc, A.uid desc ";
$RCD = db_query($query,$DB_CONNECT);
while($R = db_fetch_array($RCD)) {
    if ($GLOBALS['_cloud_'] === true && !array_key_exists($R['id'], $_SESSION['mbr_bot'])) continue;
    $botList[$R['uid']] = $R;
}

$aGroupBot = [];
if($uid) {
    $query = "Select A.* From rb_s_mbrgroup A ";
    $query .="Where A.uid = '".$uid."'";
    $group = db_fetch_assoc(db_query($query,$DB_CONNECT));
    $aBotUid = explode(",", $group['bot']);
    foreach($aBotUid as $_bot) {
        if(array_key_exists($_bot, $botList)) {
            $aGroupBot[$_bot] = array('name'=>$botList[$_bot]['name'], 'bottype'=>$botList[$_bot]['bottype']);
            unset($botList[$_bot]);
        }
    }
}
?>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addBotModal-title">그룹 <?=$modText?></h4>
            </div>
            <form id="memberForm" onsubmit="return groupFormCheck($(this));">
                <input type="hidden" name="linkType" value="group_<?=$mod?>" />
                <input type="hidden" name="group_uid" value="<?=$uid?>" />
                <div class="modal-body" style="padding-left:30px; padding-right:30px; overflow-y:scroll; max-height:500px;">
                    <div class="form-group">
                        <label class="control-label">그룹명</label>
                        <input type="text" class="form-control" name="name" value="<?=$group['name']?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?php echo ('chat' === $_SESSION['bottype']) ? "챗봇" : '콜봇'?> 선택</label>
                        <div class="input-group">
                            <select name="bot" id="bot_list" class="form-control" style="width:100% !important;">
                                <? foreach ($botList as $bot => $_data) { ?>
                                    <? if ($_SESSION['bottype'] === $_data['bottype']) : ?>
                                        <option value="<?= $bot ?>">[<?= ($_data['bottype'] == 'chat' ? '챗봇' : '콜봇') ?>
                                            ] <?= $_data['name'] ?></option>
                                    <? endif; ?>
                                <? } ?>
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn_group_bot" data-action="add">추가</button>
                            </span>
                        </div>
                    </div>
                    <div id="gbot_list" class="form-group">
                        <label class="control-label">허용 <?php echo ('chat' === $_SESSION['bottype']) ? "챗봇" : '콜봇'?></label>
                        <? foreach ($aGroupBot as $bot => $_data) { ?>
                            <div class="alert alert-info alert-dismissible mb-10" role="alert"
                                 style="padding: 8px 12px;margin-bottom:5px;
                                <?php if ($_SESSION['bottype'] !== $_data['bottype']) echo 'display:none;' ?>
                                ">
                                <input type="hidden" class="bot_uid" name="group_botUid[]" value="<?= $bot ?>">
                                <span class="bot_name">[<?= ($_data['bottype'] == 'chat' ? '챗봇' : '콜봇') ?>] <?= $_data['name'] ?></span>
                                <span class="pull-right btn_group_bot" style="color: #31708F; cursor: pointer;"
                                      data-placement="top" title="" data-action="delete">
                                <i class="fas fa-times"></i>
                            </span>
                            </div>
                        <? } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-role="btn-saveInfo" data-depth="">확인</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
                </div>
            </form>
        </div>
    </div>

<? exit;?>

