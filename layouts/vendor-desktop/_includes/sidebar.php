<?php
//---------------------------------------------------
// 챗봇/토픽 에 따른 메뉴출력 분기
$bottype = $_SESSION['bottype'];
$roleType = $_SESSION['roleType'];

$g['side_menu'] = array();
$_botMenu = [];
if($_member) {
    $_botMenu[] = ['id'=>'main', 'name'=> ('chat' !== $bottype ? '콜봇' : '챗봇').' 리스트'];
    $_botMenu[] = ['id'=>'mem', 'name'=>'사용자 관리', 'aSideMenu'=>[
        ['id'=>'membergroup', 'name'=>'사용자 그룹 관리'], ['id'=>'memberperm', 'name'=>'그룹 권한 관리'], ['id'=>'memberadd', 'name'=>'사용자 추가/수정']
    ]];
} else {
    if($roleType =='topic'){
        $_sql = "parent=27 and hidden=0 and id<>'channel'";
        $M1=getDbArray($table['s_menu'],$_sql,'uid,name,id,is_child','gid','asc','',1);
    }else{
        $M1=getDbArray($table['s_menu'],"parent=27 and hidden=0",'uid,name,id,is_child','gid','asc','',1);
    }
    while($_M1 = db_fetch_array($M1)){
        if(($_M1['id'] =='dashboard' || $_M1['id'] =='analysis' || $_M1['id'] =='monitering') && $roleType =='topic') continue;

        $g['side_menu'][] = $_M1['id'];
        $_tempMenu = ['id'=>$_M1['id'], 'name'=>$chatbot->getRoleTypeName($_M1['name']), 'aSideMenu'=>[]];

        if($roleType=='topic') $_wh = "parent='".$_M1['uid']."' and hidden=0 and id in ('config','graph','intentSet','entitySet')";
        else $_wh = "parent='".$_M1['uid']."' and hidden=0";
        $M2=getDbArray($table['s_menu'],$_wh,'uid,name,id,is_child','gid','asc','',1);
        while($_M2=db_fetch_array($M2)){
            if($bottype == 'call' && ($_M2['id'] == 'skin' || $_M2['id'] == 'intro')) continue;
            if('chat' === $bottype && 'blackList' === $_M2['id']) continue;
            $g['side_menu'][] = $_M2['id'];
            $_tempMenu['aSideMenu'][] = ['id'=>$_M2['id'], 'name'=>$chatbot->getRoleTypeName($_M2['name'])];
        }
        $_botMenu[] = $_tempMenu;
    }
}
$_page = str_replace('adm/','',$page);

?>
<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <ul class="nav" id="side-menu">
            <?php if(!$_member) {?>
            <li class="no-menu sideMenu">
                <a  class="wave-effect">
                    <i class="fa fa-arrow-left fa-fw bot-config" aria-hidden="true" data-role="sideMenu-size"></i>
                    <span class="hide-menu go-config" style="padding-left: 0px;" data-role="go-config" data-url="/adm/config">
                        <?=$getListBot['bot_avatar']?>
                    </span>
                </a>
            </li>
            <?php }?>
            <li style="pointer-events: none;">
                <a>
                    <span style="padding-left: 0px; cursor: default;">
                        <?=$getListBot['name']?>
                    </span>
                </a>
            </li>
            <?php
            foreach($_botMenu as $_menu) {
                $_mmenu = $_menu['id'] == "main" ? $_menu['id']."?bottype=".$bottype : $_menu['id'];
                $_child = isset($_menu['aSideMenu']) && count($_menu['aSideMenu']) > 0 ? true : false;

                if(in_array($_menu['id'], $my['mymenu'])) {
            ?>
            <li class="sideMenu">
                <a href="/adm/<?=$_mmenu?>" class="waves-effect <?=($_child ? "has-arrow" : "")?>" <?=($_child ? 'data-child="true"' : '')?>>
                    <!--
                    <i class="fa <?=$menu_icon[$_menu['id']]?> fa-fw" aria-hidden="true"></i>
                    -->
                    <img src="/_core/skin/images/<?=$menu_icon[$_menu['id']]?>.svg" />
                    <span class="hide-menu"><?=$_menu['name']?></span>
                </a>
                <?php if($_child) {?>
                <?php $aSideMenu = $_menu['aSideMenu']; ?>
                <ul class="submenu <? if($_member) {?>collapse in<?}?>">
                    <?php
                    foreach($_menu['aSideMenu'] as $_sub) {
                        if(in_array($_sub['id'], $my['mymenu']) && 'api' !== $_sub['id']) {
                    ?>
                    <li class="<?=($_page == $_sub['id'] ? 'active' : '')?>">
                        <a href="/adm/<?=$_sub['id']?>" class="waves-effect">
                            <i class="fa <?=$menu_icon[$_sub['id']]?> fa-fw" aria-hidden="true"></i>
                            <span class="hide-menu"><?=$_sub['name']?></span>
                        </a>
                    </li>
                    <?php
                        }
                    }
                    ?>
                </ul>
                <?php }?>
            </li>
            <?php
                }
            }
            ?>
        </ul>
    </div>
    <div class="resizeSideMenu">
        <!-- <i class="fa fa-arrow-left fa-fw bot-config" ></i> -->
        <img src="/_core/skin/images/toggle.svg" class="bot-config" aria-hidden="true" data-role="sideMenu-size" >
    </div>
</div>
<!-- Left navbar-header end -->

<!-- side menu -->
<script>
    $('[data-role="sideMenu-size"]').on('click',function(e) {
        var arrow = $(this);
        e.preventDefault();
        var sideMenuClose;
        if($('body').hasClass('content-wrapper')) sideMenuClose = true;
        else sideMenuClose = false;

        if(sideMenuClose){
            $('body').removeClass('content-wrapper');
            $('.small-sideMenu').css("display","inline");
            $('[data-child="true"]').addClass('has-arrow');
            $('.bot-config').css({
                "position": "absolute",
            });
            $(arrow).removeClass('fa-arrow-right');
            $(arrow).addClass('fa-arrow-left');

        }else{
            $('body').addClass('content-wrapper');
            $('.small-sideMenu').css("display","none");
            $('[data-child="true"]').removeClass('has-arrow');
            $('.submenu').removeClass('in');
            $('.submenu').attr("aral-expanded","false");
            $('.bot-config').css({
                "position": "static",
            });
            $(arrow).removeClass('fa-arrow-left');
            $(arrow).addClass('fa-arrow-right');
        }
    });

    $('[data-role="go-config"]').on('click',function(){
         var url = $(this).data('url');
         location.href= url;
    });
</script>
