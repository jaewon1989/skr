<?
$uid = trim($_GET['uid']);

$mod = $uid ? 'update' : 'create';

$modText = $uid ? '수정' : '등록';


$groupList = [];
$query = "Select A.* From rb_s_mbrgroup A Order by A.gid asc ";
$RCD = db_query($query,$DB_CONNECT);
while($R = db_fetch_array($RCD)) {
    $groupList[] = $R;
}

if($uid) {
    $query = "Select A.uid as mbruid, A.id, B.* From rb_s_mbrid A ";
    $query .="left join rb_s_mbrdata B on A.uid = B.memberuid ";
    $query .="Where A.uid = '".$uid."'";
    $RCD = db_query($query,$DB_CONNECT);
    $manager = db_fetch_assoc($RCD);
}
?>

    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addBotModal-title">사용자 <?=$modText?></h4>
            </div>

            <form id="memberForm" onsubmit="return chkMemberForm($(this));">
                <input type="hidden" name="mode" value="<?=$mod?>Member" />
                <input type="hidden" name="mbruid" value="<?=$uid?>" />
                <input type="hidden" name="level" value="10" />
                <div class="modal-body" style="padding-left:30px; padding-right:30px; overflow-y:scroll;">
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">그룹</label>
                        <div class="col-md-9">
                            <select name="group" class="form-control" style="width:100% !important;">
                                <option value="">- 그룹 선택 -</option>
                                <? foreach($groupList as $_data){?>
                                <option value="<?=$_data['uid']?>" <?=($manager['mygroup'] == $_data['uid'] ? 'selected' : '')?>><?=$_data['name']?></option>
                                <? }?>
                            </select>
                        </div>
                    </div>
                    <!--
                    <div class="form-group row">
                        <label class="col-md-3 control-label">레벨</label>
                        <div class="col-md-9">
                            <label class="radio-inline">
                                <input type="radio" name="level" value="1" <?=($manager['level'] == 1 ? "checked" : "")?> /> 관리자
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="level" value="10" <?=($manager['level'] == 10 ? "checked" : "")?> /> 일반사용자
                            </label>
                        </div>
                    </div>
                    -->
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">사용자명</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" value="<?=$manager['name']?>" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">아이디</label>
                        <div class="col-md-9">
                            <input type="text" class="input_enc form-control" name="id" value="<?=$manager['id']?>" placeholder="영문,숫자 4~20자" <?=('update' === $mod ? "readonly" : "")?> />
                        </div>
                    </div>
                    <? if('create' === $mod) {?>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">비밀번호</label>
                        <div class="col-md-9">
                            <input type="password" class="input_enc form-control" name="pw1" placeholder="비밀번호를 입력해주세요.">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">비밀번호 확인</label>
                        <div class="col-md-9">
                            <input type="password" class="input_enc form-control" name="pw2" placeholder="비밀번호를 한번 더 입력해주세요.">
                        </div>
                    </div>
                    <? }?>
                    <? if('update' === $mod) {?>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="pw_change" name="pw_change" value="true" onclick="getPwBox($(this));" />
                                <label class="task-done" for="pw_change">비밀번호 변경</label>
                                <button type="button" class="btn" id="reset_pw" name="reset_pw" style="margin-left: 20px" onclick="resetPw();">비밀번호 초기화</button>
                            </div>
                        </div>
                    </div>
                    <div id="pw_box" class="form-group row" style="display:none;">
                        <div>
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9" style="margin-bottom:10px;">
                                <input type="password" class="form-control" name="prev_pw" placeholder="기존 비밀번호를 입력해주세요.">
                            </div>
                        </div>
                        <div>
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9" style="margin-bottom:10px;">
                                <input type="password" class="form-control" name="pw1" placeholder="신규 비밀번호를 입력해주세요.">
                            </div>
                        </div>
                        <div>
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="pw2" placeholder="비밀번호를 한번 더 입력해주세요.">
                            </div>
                        </div>
                    </div>
                    <? }?>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">이메일</label>
                        <div class="col-md-9">
                            <input type="text" class="input_enc form-control" name="email" value="<?=$manager['email']?>">
                        </div>
                    </div>
                    <? if('update' === $mod) {?>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">상태</label>
                        <div class="col-md-9">
                            <select name="is_lock" class="form-control" style="width:100% !important;">
                                <option value="N" <?php if ('N' === $manager['is_lock']){echo 'selected';} ?>>정상</option>
                                <option value="Y" <?php if ('Y' === $manager['is_lock']){echo 'selected';} ?>>잠금</option>
                            </select>
                        </div>
                    </div>
                    <?php }?>
                    <div class="form-password-notice-div" <?php if('update' === $mod) echo 'style="display: none;"'; ?>>
                        <span class="password-notice-span">비밀번호 유의사항</span>
                        <div class="password-rule">
                            <span>1. 비밀번호는 9-16글자로 구성해야 합니다.</span>
                            <span>2. 영어 대문자가 한 글자 이상 반드시 포함되어 있어야 합니다. (A-Z)</span>
                            <span>3. 영어 소문자가 한 글자 이상 반드시 포함되어 있어야 합니다. (a-z)</span>
                            <span>4. 숫자가 한 글자 이상 반드시 포함되어 있어야 합니다. (0-9)</span>
                            <span>5. 특수문자가 한 글자 이상 반드시 포함되어 있어야 합니다 ! @ # $ % ^ & . * ( )</span>
                            <span>6. 연속된 문자를 3개 이상 사용할 수 없습니다. (abc, 123)</span>
                            <span>7. 동일한 문자를 연속으로 3개 이상 사용할 수 없습니다. (aaa, 111)</span>
                            <span>8. 비밀번호에 아이디를 포함할 수 없습니다.</span>
                            <span>9. 직전 2개 비밀번호를 재사용 할 수 없습니다.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-role="btn-saveInfo" data-depth="">확인</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
                </div>
            </form>
        </div>
    </div>

<script>
const memberInterfacePath = "/interface/internal/member";

function getPwBox(obj) {

    if (obj.is(":checked")) {
        $("#pw_box").show();
        $(".form-password-notice-div").show();
    } else {
        $("#pw_box").hide();
        $(".form-password-notice-div").hide();
    }
}

function clearInput(text) {
    text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");
    return $.trim(text);
}

function resetPw() {
    const mbruid = $("input[name='mbruid']").val(),
        id = $("input[name='id']").val();

    if (confirm("해당 아이디(" + id + ")의 비밀번호를 초기화 하시겠습니까?")) {
        $.post(memberInterfacePath, {"mode": "resetPw", "mbruid": mbruid, "id": id}, function(response) {
            alert(response.result);
            if (!response.error) {
                location.reload();
            }
        }, "json");
    }
}

function chkMemberForm(obj) {
    const formMode = obj.find(":input[name=mode]").val();

    if (!commonValidation(obj)) {
        return false;
    }
    if ("createMember" === formMode) {
        if (!createMemberValidation(obj)) {
            return false;
        }
    } else if ("updateMember" === formMode && $("#pw_change").is(":checked")) {
        if (!modifyMemberValidation(obj)) {
            return false;
        }
    }
    if (!emailValidation(obj)) {
        return false;
    }

    const formData = obj.serializeArray();
    const jsonData = {};
    for (let i = 0; i < formData.length; i++) {
        jsonData[formData[i].name] = formData[i].value;
    }

    $.post(memberInterfacePath, jsonData, function(response) {
        alert(response.result);
        if (!response.error){
            location.reload();
        }
    }, 'json');
    return false;
}

function commonValidation(obj) {
    if (!obj.find(":input[name=group] option:selected").val()) {
        alert("그룹을 선택해주세요.");
        obj.find(":input[name=group]").focus();
        return false;
    }
    if (!obj.find(":input[name=name]").val()) {
        alert("사용자명을 입력해주세요.");
        obj.find(":input[name=name]").focus();
        return false;
    }
    if (!obj.find(":input[name=id]").val()) {
        alert("아이디를 입력해주세요.");
        obj.find(":input[name=id]").focus();
        return false;
    }

    return true;
}

function emailValidation(obj) {
    if (!obj.find(":input[name=email]").val()) {
        alert("이메일을 입력해주세요.");
        obj.find(":input[name=email]").focus();
        return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(obj.find(":input[name=email]").val())) {
        alert("이메일을 확인하세요");
        obj.find(":input[name=email]").focus();
        return false;
    }

    return true;
}

function createMemberValidation(obj) {
    if (!obj.find(":input[name=pw1]").val()) {
        alert("비밀번호를 입력해주세요.");
        obj.find(":input[name=pw1]").focus();
        return false;
    }
    if (!obj.find(":input[name=pw2]").val()) {
        alert("비밀번호를 한번 더 입력해주세요.");
        obj.find(":input[name=pw2]").focus();
        return false;
    }
    if (obj.find(":input[name=pw1]").val() !== obj.find(":input[name=pw2]").val()) {
        alert("입력된 비밀번호가 다릅니다.");
        obj.find(":input[name=pw2]").focus();
        return false;
    }

    return checkPwPolicy(obj);
}

function modifyMemberValidation(obj) {
    if (!obj.find(":input[name=prev_pw]").val()) {
        alert("기존 비밀번호를 입력해주세요.");
        obj.find(":input[name=prev_pw]").focus();
        return false;
    }
    if (!obj.find(":input[name=pw1]").val()) {
        alert("신규 비밀번호를 입력해주세요.");
        obj.find(":input[name=pw1]").focus();
        return false;
    }
    if (!obj.find(":input[name=pw2]").val()) {
        alert("비밀번호를 한번 더 입력해주세요.");
        obj.find(":input[name=pw2]").focus();
        return false;
    }
    if (obj.find(":input[name=pw1]").val() !== obj.find(":input[name=pw2]").val()) {
        alert("입력된 신규 비밀번호가 다릅니다.");
        obj.find(":input[name=pw2]").focus();
        return false;
    }

    return checkPwPolicy(obj);
}

function checkPwPolicy(obj) {
    const pwElement = obj.find(":input[name=pw1]");
    const pwValue = pwElement.val();
    const idValue = obj.find(":input[name=id]").val();

    if(pwValue.length < 9 || pwValue.length > 16) {
        alert("비밀번호는 9-16글자로 구성해야 합니다.");
        pwElement.focus();
        return false;
    }
    if (!/[A-Z]/.test(pwValue)) {
        alert("영어 대문자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) A-Z");
        pwElement.focus();
        return false;
    }
    if (!/[a-z]/.test(pwValue)) {
        alert("영어 소문자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) a-z");
        pwElement.focus();
        return false;
    }
    if (!/[0-9]/.test(pwValue)) {
        alert("숫자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) 0-9");
        pwElement.focus();
        return false;
    }
    if (!/[!@#$%^&.*()]/.test(pwValue)) {
        alert("특수문자가 한 글자 이상 반드시 포함되어 있어야 합니다. ex) ! @ # $ % ^ & . * ( )");
        pwElement.focus();
        return false;
    }
    if (/(.)\1{2,}/.test(pwValue)) {
        alert("동일한 문자를 연속으로 3개 이상 사용할 수 없습니다. ex) aaa,111");
        pwElement.focus();
        return false;
    }
    if (isConsecutiveSequence(pwValue)) {
        alert("연속된 문자를 3개 이상 사용할 수 없습니다. ex) abc, 123");
        pwElement.focus();
        return false;
    }
    if (pwValue.includes(idValue)) {
        alert("비밀번호에 아이디를 포함할 수 없습니다.");
        pwElement.focus();
        return false;
    }

    return true;
}

function isConsecutiveSequence(str) {
    for (let i = 0; i < str.length - 2; i++) {
        if (str.charCodeAt(i) + 1 === str.charCodeAt(i + 1) &&
            str.charCodeAt(i) + 2 === str.charCodeAt(i + 2)) {
            return true;
        }

        if (!isNaN(str[i]) && !isNaN(str[i + 1]) && !isNaN(str[i + 2])) {
            if (parseInt(str[i]) + 1 === parseInt(str[i + 1]) &&
                parseInt(str[i]) + 2 === parseInt(str[i + 2])) {
                return true;
            }
        }
    }

    return false;
}

</script>

<? exit;?>

