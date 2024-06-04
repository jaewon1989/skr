<?
    $bottype = $_SESSION['bottype'];

    if(isset($_SESSION['bot_info'])) {
        $is_chat = array_search('chat', array_column($_SESSION['bot_info'], 'bottype'));
        $is_call = array_search('call', array_column($_SESSION['bot_info'], 'bottype'));
    }
?>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top m-b-0">
    <div class="navbar-top-header">
        <div class="navbar-top-logo">
            <span class="bold">SK telecom</span>
            <span>AICC</span>
        </div>
        <div>
            <?php if($my['uid']):?>
            <?php
              $user_avatar_src = $chatbot->getUserAvatar($my['uid'],'src');  // use 아바타 정보  src, bg 옵션
              $userInfo_url = $chatbot->getExUrl('userInfo');
            ?>
            <div>
                <a href="javascript:;">
                    <span class="cb-userwrappers">
                        <img src="<?php echo $user_avatar_src?>" />
                    </span>
                    <span class="cb-name"><?php echo ($_SESSION['mbr_uname'] ? $_SESSION['mbr_uname'] : $my[$_HS['nametype']])?>님</span>
                </a>
                <!--
                -->
             </div>
            <?php endif?>
            <div class="cb-toggle-menu" style="position: relative;">
                <button type="button" class="cb-toggle-btn dropdown-toggle" id="faq-download-dropdown" data-toggle="dropdown" aria-expanded="false">
                    <svg width="9" height="6" viewBox="0 0 9 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.75692 5.82851L0.513916 1.58551L1.92892 0.171509L4.75692 3.00051L7.58492 0.171509L8.99992 1.58551L4.75692 5.82851Z" fill="#292C43"/>
                    </svg>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="faq-download-dropdown" style="left: auto; right: 0px;">
                <? if(in_array('mem', $my['mymenu'])) {?>
                    <li>
                        <a href="/adm/membergroup"><span class="cb-name">사용자 관리  </span></a>
                    </li>
                <?}?>
                    <li>
                        <a href="<?php echo $g['s']?>/?a=logout"><span class="cb-name">로그아웃</span></a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <div class="navbar-top-banner">
        <div class="navbar-layout">
            <div>
                <a href="/adm/main?bottype=<?php echo 'chat' === $bottype ? 'chat' : 'call' ?>">
                    <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.187 4.79195L10.9476 1.48245C11.003 1.24195 11.109 1.01389 11.2598 0.811294C11.4105 0.608694 11.603 0.435524 11.8262 0.301675C12.0493 0.167827 12.2988 0.0759218 12.5605 0.0312104C12.8221 -0.0135011 13.0907 -0.0101431 13.3509 0.0410924L16.5876 0.677535C16.886 0.371313 17.2854 0.164639 17.7238 0.0895692C18.1622 0.0144992 18.6152 0.0752284 19.0125 0.262338C19.4098 0.449447 19.7291 0.752479 19.921 1.12443C20.113 1.49639 20.1667 1.91647 20.074 2.31954C19.9812 2.7226 19.7471 3.08611 19.408 3.3537C19.0689 3.62128 18.6438 3.77797 18.1985 3.79947C17.7532 3.82098 17.3127 3.70609 16.9452 3.47263C16.5778 3.23916 16.304 2.90018 16.1663 2.50824L12.9296 1.8718L12.2551 4.80599C14.0618 4.93421 15.8574 5.48923 17.4839 6.39709C17.9672 6.14874 18.5114 6.01913 19.0644 6.02064C19.6174 6.02216 20.1607 6.15476 20.6424 6.40576C21.1242 6.65676 21.5282 7.01776 21.8158 7.45426C22.1034 7.89076 22.2651 8.38815 22.2853 8.89887V8.91853C22.2931 9.37631 22.1869 9.82962 21.9749 10.2434C21.763 10.6573 21.4509 11.0205 21.0629 11.3052C21.0611 11.3436 21.0584 11.382 21.0548 11.4203C21.0548 15.1622 16.5481 18 11.1309 18C5.73096 18 1.29918 15.1697 1.30221 11.4924C1.2978 11.436 1.29442 11.3795 1.29209 11.323C0.637969 10.8692 0.194271 10.2032 0.0504106 9.45912C-0.0934502 8.71505 0.0732203 7.94824 0.516834 7.31322C0.960447 6.6782 1.64802 6.22219 2.441 6.03708C3.23398 5.85196 4.0734 5.95151 4.79011 6.31567C6.42581 5.40465 8.27972 4.88124 10.187 4.79195ZM19.5914 9.96398C20.0046 9.77398 20.2639 9.38369 20.2608 8.95784C20.2503 8.74369 20.1732 8.53688 20.0388 8.36215C19.9044 8.18742 19.7184 8.05218 19.503 7.9726C19.2877 7.89302 19.052 7.87247 18.8243 7.91341C18.5966 7.95435 18.3865 8.05506 18.2191 8.20347L17.6267 8.7276L16.9481 8.30174C15.3126 7.27407 13.4521 6.68724 11.6515 6.65073H10.6327C8.73375 6.67788 6.93511 7.2123 5.3289 8.21844L4.65744 8.63962L4.066 8.12485C3.93846 8.01421 3.78642 7.93051 3.62041 7.87954C3.4544 7.82857 3.27839 7.81155 3.10456 7.82967C2.93073 7.84778 2.76324 7.90059 2.61368 7.98444C2.46412 8.06829 2.33606 8.18118 2.23838 8.31529C2.14069 8.4494 2.07571 8.60152 2.04793 8.76114C2.02016 8.92075 2.03025 9.08403 2.07751 9.23969C2.12477 9.39534 2.20806 9.53964 2.32163 9.6626C2.4352 9.78556 2.57633 9.88424 2.73525 9.95181L3.37632 10.2232L3.32163 10.8737C3.30847 11.0328 3.30847 11.191 3.32467 11.4203C3.32467 13.9567 6.72345 16.1281 11.1309 16.1281C15.5576 16.1281 19.0293 13.9417 19.0324 11.3492C19.0456 11.1909 19.0456 11.032 19.0324 10.8737L18.9797 10.2448L19.5914 9.96398ZM6.03276 10.1128C6.03276 9.74045 6.1928 9.38336 6.47769 9.12007C6.76259 8.85679 7.14898 8.70888 7.55188 8.70888C7.95477 8.70888 8.34117 8.85679 8.62606 9.12007C8.91095 9.38336 9.071 9.74045 9.071 10.1128C9.071 10.4851 8.91095 10.8422 8.62606 11.1055C8.34117 11.3688 7.95477 11.5167 7.55188 11.5167C7.14898 11.5167 6.76259 11.3688 6.47769 11.1055C6.1928 10.8422 6.03276 10.4851 6.03276 10.1128ZM13.122 10.1128C13.122 9.74045 13.282 9.38336 13.5669 9.12007C13.8518 8.85679 14.2382 8.70888 14.6411 8.70888C15.044 8.70888 15.4304 8.85679 15.7153 9.12007C16.0002 9.38336 16.1602 9.74045 16.1602 10.1128C16.1602 10.4851 16.0002 10.8422 15.7153 11.1055C15.4304 11.3688 15.044 11.5167 14.6411 11.5167C14.2382 11.5167 13.8518 11.3688 13.5669 11.1055C13.282 10.8422 13.122 10.4851 13.122 10.1128ZM11.1127 14.8889C9.69789 14.8889 8.31042 14.5426 7.18121 13.7564C7.113 13.6803 7.07797 13.5834 7.08297 13.4847C7.08797 13.386 7.13265 13.2926 7.20827 13.2227C7.28388 13.1528 7.38499 13.1115 7.49181 13.1069C7.59863 13.1023 7.70347 13.1346 7.78582 13.1977C8.74287 13.8463 9.93588 14.1243 11.0965 14.1243C12.2571 14.1243 13.4562 13.8669 14.4224 13.2267C14.4782 13.1763 14.5475 13.1404 14.6231 13.1226C14.6987 13.1048 14.7781 13.1057 14.8533 13.1253C14.9284 13.1448 14.9966 13.1823 15.0511 13.2339C15.1056 13.2856 15.1444 13.3496 15.1637 13.4195C15.1801 13.4896 15.1777 13.5623 15.1567 13.6313C15.1357 13.7003 15.0967 13.7635 15.0432 13.8154C14.3504 14.5613 12.5275 14.8889 11.1127 14.8889Z" fill="#292C43"/>
                    </svg>
                    <span>SK telecom <?=($is_chat !== false && $bottype=='chat')? "챗봇" : (($is_call !== false && $bottype=='call')? "콜봇" : "") ?> 관리</span>
                </a>
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
            </div>
        </div>
    </div>
    <!--
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
    -->
    <!-- /.navbar-header -->
    <!-- /.navbar-top-links -->
    <!-- /.navbar-static-side -->
</nav>
<!-- Left navbar-header -->