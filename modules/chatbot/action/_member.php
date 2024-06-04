<?
$vendor = trim($_GET['vendor']);
$uid = trim($_GET['uid']);

$mod = $uid ? 'modify' : 'add';
$modText = $uid ? '����' : '���';

$query = "Select uid, name, id From ".$table[$m.'bot']." Where role='bot' and hidden=0 and display=1 and vendor='".$vendor."' Order by uid DESC ";
$BCD = db_query($query,$DB_CONNECT);

if($uid) {
    $query = "Select A.bot, B.id, C.name, C.email From ".$table[$m.'manager']." A ";
    $query .="left join ".$table['s_mbrid']." B on A.mbruid = B.uid ";
    $query .="left join ".$table['s_mbrdata']." C on A.mbruid = C.memberuid ";
    $query .="Where A.uid = '".$uid."'";
    $RCD = db_query($query,$DB_CONNECT);
    $manager = db_fetch_assoc($RCD);
}

if(!$g['https_on']) {
    include_once $g['path_core'] . "function/ssRsa.php";
    include_once $g['path_core'] . "function/ssRsaForm.php";

    echo "<script type='text/javascript' src='/_core/js/ssRsa.js'></script>";
}
?>
<form id="RSARegist" style="display:none;">
    <input type="hidden" name="linkType" value="">
    <input type="hidden" name="uid" value="" />
    <input type="hidden" name="vendor" value="" />
    <input type="hidden" name="bot" value="" />
    <input type="hidden" name="name" value="">
    <input type="hidden" name="id" value="">
    <input type="hidden" name="pw_change" value="">
    <input type="hidden" name="pw1" value="">
    <input type="hidden" name="pw2" value="">
    <input type="hidden" name="email" value="">
    <input type="hidden" name="G_RSAKey" value="<?=$_SESSION['ssRsa']['public']?>" />
</form>
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addBotModal-title">��� <?=$modText?></h4>
            </div>
            <form id="managerForm" onsubmit="return managerFormCheck($(this));">
                <input type="hidden" name="linkType" value="<?=$mod?>">
                <input type="hidden" name="uid" value="<?=$uid?>" />
                <input type="hidden" name="vendor" value="<?=$vendor?>" />
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">ê�� ����</label>
                        <div class="col-md-9">
                            <select name="bot" style="padding:5px 10px; border:1px solid #e4e7ea; -webkit-appearance:menulist;">
                                <option value="">-ê�� ����-</option>
                                <?php while($R = db_fetch_array($BCD)){?>
                                <option value="<?=$R['uid']?>" <?=($R['uid']==$manager['bot'] ? "selected" : "")?>><?=$R['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">��ڸ�</label>
                        <div class="col-md-9">
                            <input type="text" class="input_enc form-control" name="name" value="<?=$manager['name']?>" <?=($mod=='modify' ? "readonly" : "")?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">���̵�</label>
                        <div class="col-md-9">
                            <input type="text" class="input_enc form-control" name="id" value="<?=$manager['id']?>" placeholder="����,���� 4~20��" <?=($mod=='modify' ? "readonly" : "")?> />
                        </div>
                    </div>
                    <? if($mod == 'add') {?>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">��й�ȣ</label>
                        <div class="col-md-9">
                            <input type="password" class="input_enc form-control" name="pw1" placeholder="��й�ȣ�� �Է����ּ���.(����,����,Ư������ ���� 8~20��)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">��й�ȣ Ȯ��</label>
                        <div class="col-md-9">
                            <input type="password" class="input_enc form-control" name="pw2" placeholder="��й�ȣ�� �ѹ� �� �Է����ּ���.">
                        </div>
                    </div>
                    <? }?>
                    <? if($mod == 'modify') {?>
                    <div class="form-group row" style="margin-bottom:0px;">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">��й�ȣ ����</label>
                        <div class="col-md-9">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="pw_change" name="pw_change" value="true" onclick="getPwBox($(this));" />
                                <label class="task-done" for="pw_change">��й�ȣ ����</label>
                            </div>
                        </div>
                    </div>
                    <div id="pw_box" class="form-group row" style="display:none;">
                        <div>
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9" style="margin-bottom:10px;">
                                <input type="password" class="input_enc form-control" name="pw1" placeholder="�ű� ��й�ȣ�� �Է����ּ���.">
                            </div>
                        </div>
                        <div>
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <input type="password" class="input_enc form-control" name="pw2" placeholder="��й�ȣ�� �ѹ� �� �Է����ּ���.">
                            </div>
                        </div>
                    </div>
                    <? }?>
                    <div class="form-group row">
                        <label class="col-md-3 control-label" style="padding:7px 7.5px;">�̸���</label>
                        <div class="col-md-9">
                            <input type="text" class="input_enc form-control" name="email" value="<?=$manager['email']?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-depth="">Ȯ��</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">�ݱ�</button>
                </div>
            </form>
        </div>
    </div>

<script>
    function getPwBox(obj) {
        if(obj.is(":checked")) $("#pw_box").show();
        else $("#pw_box").hide();
    }
    function clearInput(text) {
        text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");
        return $.trim(text);
    }
    function managerFormCheck(obj) {
        if(!obj.find(":input[name=bot] option:selected").val()) {
            alert("ê���� �������ּ���."); obj.find(":input[name=bot]").focus(); return false;
        }
        <? if($mod == 'add') {?>
        if(!obj.find(":input[name=name]").val()) {
            alert("��ڸ��� �Է����ּ���."); obj.find(":input[name=name]").focus(); return false;
        }
        if(!obj.find(":input[name=id]").val()) {
            alert("���̵� �Է����ּ���."); obj.find(":input[name=name]").focus(); return false;
        }
        if(!obj.find(":input[name=pw1]").val()) {
            alert("��й�ȣ�� �Է����ּ���."); obj.find(":input[name=pw1]").focus(); return false;
        }
        if(!obj.find(":input[name=pw2]").val()) {
            alert("��й�ȣ�� �ѹ� �� �Է����ּ���."); obj.find(":input[name=pw2]").focus(); return false;
        }
        <? } else {?>
        if($("#pw_change").is(":checked")) {
            if(!obj.find(":input[name=pw1]").val()) {
                alert("�ű� ��й�ȣ�� �Է����ּ���."); obj.find(":input[name=pw1]").focus(); return false;
            }
            if(!obj.find(":input[name=pw2]").val()) {
                alert("��й�ȣ�� �ѹ� �� �Է����ּ���."); obj.find(":input[name=pw2]").focus(); return false;
            }
            if(obj.find(":input[name=pw1]").val() != obj.find(":input[name=pw2]").val()) {
                alert("�Էµ� ��й�ȣ�� �ٸ��ϴ�."); obj.find(":input[name=pw2]").focus(); return false;
            }
        }
        <? } ?>
        if(!obj.find(":input[name=email]").val()) {
            alert("�̸����� �Է����ּ���."); obj.find(":input[name=email]").focus(); return false;
        }

        <? if($_SERVER['HTTPS'] != 'on') {?>
        $("#RSARegist :input[name=bot]").val(obj.find(":input[name=bot] option:selected").val());
        var rsa = new ssRsa($("#RSARegist :input:hidden[name=G_RSAKey]").val());
        obj.find(":input").each(function() {
            if($(this).attr("type") == "checkbox") {
                var inputVal = $(this).is(":checked") ? $(this).val() : "";
            } else {
                var inputVal = $(this).hasClass("input_enc") ? rsa.encrypt($(this).val()) : $(this).val();
            }
            $("#RSARegist :input[name="+$(this).attr("name")+"]").val(inputVal);
        });
        var data = $("#RSARegist").serialize();
        <?} else {?>
        var data = obj.serialize();
        <? } ?>


        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=regis_manager', data, function(response) {
            if(response.result) {
                location.reload();
            } else {
                alert(response.msg);
                //$("#modal-memberinfo").modal("hide").html("");
            }
        },'json');
        return false;
    }
</script>