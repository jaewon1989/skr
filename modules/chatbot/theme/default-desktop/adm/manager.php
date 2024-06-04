<?
require_once $g['path_module'].'member/var/var.join.php';
$sort   = $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'asc';
$recnum = $recnum && $recnum < 200 ? $recnum : 10;
$_WHERE='vendor='.$V['uid'];

$RCD = db_query("select * from rb_chatbot_manager where vendor=".$V['uid']." order by uid",$DB_CONNECT);
//$RCD = getDbArray($table[$m.'manager'],$_WHERE,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table[$m.'manager'],$_WHERE);
$TPG = getTotalPage($NUM,$recnum);


?>
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<div class="cb-management-member">
    <h1>부관리자 등록</h1>
    <form name="procForm" action="<?php echo $g['s']?>/" action="<?php echo $g['s']?>/" enctype="multipart/form-data" method="post" >
        <input type="hidden" name="r" value="<?php echo $r?>" />
        <input type="hidden" name="m" value="<?php echo $m?>" />
        <input type="hidden" name="a" value="regis_manager" />
        <input type="hidden" name="c" value="<?php echo $c?>" />
        <input type="hidden" name="page" value="<?php echo $page?>" />
        <input type="hidden" name="check_email" value="0" />
        <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />

        <div class="cb-viewchat-search">
            <table style="width:100%;">
                <tr>
                    <td style="width:27%">
                        <input class="cb-viewchat-search-datebox" placeholder="이름을 입력해주세요" type="text" name="name" style="width:90%">
                    </td>
                    <td style="width:27%">
                        <input class="cb-viewchat-search-datebox" placeholder="botalks@botalks.com" onblur="sameCheck(this,'hLayerid');" type="text" name="email" style="width:90%"><br/>                        
                    </td>
                    <td style="width:27%">
                        <input class="cb-viewchat-search-datebox" placeholder="비밀번호를 입력해주세요." type="password" name="pw1" style="width:90%">
                    </td>   
                    <td>
                        <span class="cb-viewchat-search-button" style="cursor:pointer" data-role="btn-regis">등록</span>
                    </td> 
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3"><span id="hLayerid" class="join-message"></span></td>
                </tr>

            </table>
        </div>
    </form>
    <h1>부관리자 관리</h1>
    <div class="cb-viewchat-search-result">
        <form name="listForm" action="<?php echo $g['s']?>/" action="<?php echo $g['s']?>/" enctype="multipart/form-data" method="post" >
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <input type="hidden" name="a" value="do_UserAction" />
            <input type="hidden" name="act" value="change-managerAuth" />            
            <input type="hidden" name="c" value="<?php echo $c?>" />
            <input type="hidden" name="page" value="<?php echo $page?>" />
            <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />

            <table class="cb-management-table">
                <thead>
                    <tr>
                        <th>아이디</th>
                        <th>일자</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($R = db_fetch_array($RCD)):?>
                    <?php 

                       $userPic = $chatbot->getUserAvatar($R['mbruid'],'src');
                       $userName = $chatbot->getUserName($R['mbruid']);
                    ?>
                 
                    <tr>
                        <td>
                            <img src="<?php echo $userPic?>" alt="viewchat search result" />
                            <span class="cb-name"><?php echo $userName?></span>
                            <input type="hidden" name="manager_members[]" value="<?php echo $R['mbruid']?>"/>
                        </td>
                        <td>
                            <span class="cb-date"><?php echo getDateFormat($R['d_regis'],'Y-m-d')?></span>
                        </td>
                        <td>
                            <div class="cb-viewchat-search-timebox">
                                <select name="auth[]" style="font-size:inherit;">
                                    <option value=""> 변경적용  </option>
                                    <option value="1"<?php if($R['auth']==1):?> selected<?php endif?>> 승인 </option>
                                    <option value="2"<?php if($R['auth']==2):?> selected<?php endif?>> 미승인 </option>
                                </select>
                
                            </div>
                        </td>
                    </tr>
                    <?php endwhile?>
                   
                </tbody>
            </table>
        </div>
        <div class="text-center pt" >
             <ul class="pagination pagination-sm">
                <script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
              </ul>
              <span class="pull-right">
                <span class="cb-viewchat-search-button" style="cursor:pointer" data-role="change-auth">변경적용</span>
              </span>
        </div>
    </form>
</div>

<!-- End of  bootstrap-timepicker,  https://github.com/jdewit/bootstrap-timepicker/ , http://jdewit.github.io/bootstrap-timepicker/ : 메뉴얼 -->
<?php getImport('bootstrap-timepicker','js/bootstrap-timepicker.min',false,'js')?>
<?php getImport('bootstrap-timepicker','css/bootstrap-timepicker.min',false,'css')?>
<script>
 $('.tpicker').timepicker({
    defaultTime : '',
    //showSeconds : true, // 초 노출
    showMeridian:true, // 24시 모드 
    maxHours: 24,
    minuteStep : 15
 });

</script>
<!-- bootstrap-datepicker,  http://eternicode.github.io/bootstrap-datepicker/  -->
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script>
// 날짜 선택 
$('.input-daterange').datepicker({
    format: "yyyy-mm-dd",
    todayBtn: "linked",
    language: "kr",
    calendarWeeks: true,
    todayHighlight: true,
    autoclose: true
});

$('[data-role="btn-search"]').on('click',function(){
   var f = document.procForm;
   f.submit(); 
});

function sameCheck(obj,layer)
{
    if (obj.name == 'id')
    {
        <?php if($d['member']['login_emailid']):?>
        if (!chkEmailAddr(obj.value))
        {
            obj.form.check_email.value = '0';
            obj.focus();
            getId(layer).innerHTML = '이메일형식이 아닙니다.';
            return false;
        } 
        <?php else:?>
        if (obj.value.length < 4 || obj.value.length > 12 || !chkIdValue(obj.value))
        {
            obj.form.check_id.value = '0';
            obj.focus();
            getId(layer).innerHTML = '사용할 수 없는 아이디입니다.';
            return false;
        }
        <?php endif?>
    }
    if (obj.name == 'email')
    {
        if (!chkEmailAddr(obj.value))
        {
            obj.form.check_email.value = '0';
            obj.focus();
            getId(layer).innerHTML = '이메일형식이 아닙니다.';
            return false;
        }
    }
    frames._action_frame_<?php echo $m?>.location.href = '<?php echo $g['s']?>/?r=<?php echo $r?>&m=member&a=same_check&fname=' + obj.name + '&fvalue=' + obj.value + '&flayer=' + layer;
}

// 부운영자 등록폼 전송 
$('[data-role="btn-regis"]').on('click',function(){
    var f = document.procForm;

    if (f.name.value == '')
    {
        alert('이름을 입력해 주세요.');
        f.name.focus();
        return false;
    }

    if (f.check_email.value == '0')
    {
        alert('<?php echo $d['member']['login_emailid']?'이메일':'아이디'?>를 확인해 주세요.');
        f.email.focus();
        return false;
    }
    if (f.pw1.value == '')
    {
        alert('패스워드를 입력해 주세요.');
        f.pw1.focus();
        return false;
    }

    getIframeForAction(f);
    f.submit();
});

// 부운영자 등록폼 전송 
$('[data-role="change-auth"]').on('click',function(){
    var f = document.listForm;

    getIframeForAction(f);
    f.submit();
});




</script>