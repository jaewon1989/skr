<?php
$g['mdl_slogin'] = 'slogin';
include_once($g['path_module'].$g['mdl_slogin'].'/var/var.php');
include_once($g['path_module'].$g['mdl_slogin'].'/lib/getAccessUrl.php');

 $sArray = array(
    "facebook" =>"페이스북으로",
    "naver" =>"네이버로",
    "kakao" =>"카카오톡으로"
 );
 $to_do = array("login"=>'로그인',"join"=>'가입');
?>

<div class="cb-signup-usingsocial">
    <?php foreach ($sArray as $en=>$han):?>
        <div class="cb-signup-socialbox cb-signup-<?php echo $en?>">
            <div class="cb-layout">
                <div class="cb-left">
                    <span class="cb-icon cb-icon-<?php echo $en?>"></span>
                </div>
                <div class="cb-right">
                    <a href="<?php echo $accessUrl[$en]['callapi']?>"><?php echo $han?> <?php echo $to_do[$wdgvar['mod']]?>하기</a>
                </div>
            </div>
        </div>
    <?php endforeach?>
  
</div>	


 