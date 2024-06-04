<?
    $bottype = $_SESSION['bottype'];

    if(isset($_SESSION['bot_info'])) {
        $is_chat = array_search('chat', array_column($_SESSION['bot_info'], 'bottype'));
        $is_call = array_search('call', array_column($_SESSION['bot_info'], 'bottype'));
    }
?>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top m-b-0">
    <div class="navbar-header">
        <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse">
            <i class="fa fa-bars"></i>
        </a>
        <div class="top-left-part">
            <a class="logo" href="/adm/dashboard">
                <? if($_SESSION['mbr_uid'] == 8626 || $my['cgroup'] == "ccacs") {?>
                    <b style="width:auto; padding:5px 0 0 20px;"><img src="/_core/images/skt_logo.png" style="height:45px;" alt="home" /></b>
                <?} else {?>
                <b><img src="<?php echo $g['img_layout']?>/logo_header.png" alt="home" /></b>
                <span class="hidden-xs small-sideMenu" style="margin-left: -10px">
                     <img src="<?php echo $g['img_layout']?>/logo_text.png" alt="home" />
                </span>
                <?}?>
            </a>
        </div>
        <ul class="nav navbar-top-links navbar-left m-l-20 top_nav">
            <? if($is_chat !== false) {?>
            <li>
                <a href="/adm/main?bottype=chat" class="<?=($bottype=='chat' ? 'on' : '')?>">챗봇 관리</a>
            </li>
            <?}?>
            <? if($is_call !== false) {?>
            <li>
                <a href="/adm/main?bottype=call" class="<?=($bottype=='call' ? 'on' : '')?>">콜봇 관리</a>
            </li>
            <?}?>
            <span style="display:none;"><?=$bottype?></span>
        </ul>
        <ul class="nav navbar-top-links navbar-right pull-right top-userInfo">
            <?php if($my['uid']):?>
            <?php
              $user_avatar_src = $chatbot->getUserAvatar($my['uid'],'src');  // use 아바타 정보  src, bg 옵션
              $userInfo_url = $chatbot->getExUrl('userInfo');
            ?>
            <li>
                <a href="<?php echo $userInfo_url?>">
                    <span class="cb-userwrappers">
                        <img src="<?php echo $user_avatar_src?>" />
                    </span>
                    <span class="cb-name"><?php echo $my[$_HS['nametype']]?>님</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $g['s']?>/?a=logout"><span class="cb-name" style="line-height:33px;">로그아웃</span></a>
            </li>
            <? if(in_array('mem', $my['mymenu'])) {?>
            <li>
                <a href="/adm/membergroup"><span class="cb-name" style="line-height:33px;">사용자 관리</span></a>
            </li>
            <?}?>
            <?php endif?>

        </ul>
    </div>
    <!-- /.navbar-header -->
    <!-- /.navbar-top-links -->
    <!-- /.navbar-static-side -->
</nav>
<!-- Left navbar-header -->