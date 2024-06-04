    <div id="quick_mmenu_window" class="quick_mmenu_window" style="height: 100%; display: block; left: 0px; overflow-y: auto;">
        <div class="mbtn" style="top:0px;">
            <a href="/"><img src="/data/skin/mobile_ver2_default/images/custom/menu_market.png" alt="샷핑 MARKET"></a>
        </div>
        <div class="mbtn" style="top:163px;">
            <img src="/data/skin/mobile_ver2_default/images/custom/menu_play.png" alt="샷핑 PLAY">
        </div>
        <div class="contwrap">
            <div class="wrap">
                <div class="wrap2">
                    <div class="box1">
                        <?php if($my['uid']):?>
                        <div class="photo">
                            <img src="<?php echo $g[path_market]; ?>data/icon/member/<?php echo $FM[user_icon_file]; ?>">
                        </div>
                        <div class="ment">
                             반가워요, <strong><?php echo $my['name'];?></strong> 님
                        </div>
                        <div class="btnbox">
                            <a href="/play"><img class="btn1" src="/data/skin/mobile_ver2_default/images/custom/btn_myPlay.png" alt=""></a> &nbsp; <a href="/page/index?tpl=mypage/ping_exchange.html"><img class="btn1" src="/data/skin/mobile_ver2_default/images/custom/btn_myping.png" alt=""></a>
                        </div>
                        <img id="top_lang_img" class="lang" src="/data/skin/mobile_ver2_default/images/custom/lang.png" alt="" data-toggling="langsel">
                        <img class="xbtn" src="/data/skin/mobile_ver2_default/images/custom/03_close.png" alt="" data-history="back">
                        <div class="langsel" id="langsel">
                            <ul>
                                <li><a href="#googtrans(pl|ko)" onclick="window.location='#googtrans(pl|ko)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang1.png" alt="" /> 대한민국</a></li>
                                <li><a href="#googtrans(pl|en)" onclick="window.location='#googtrans(pl|en)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang2.png" alt="" /> 미국</a></li>
                                <li><a href="#googtrans(pl|zh-CN)" onclick="window.location='#googtrans(pl|zh-CN)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang3.png" alt="" /> 중국</a></li>
                                <li><a href="#googtrans(pl|ja)" onclick="window.location='#googtrans(pl|ja)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang4.png" alt="" /> 일본</a></li>
                                <li class="lang_ck">다</li>
                            </ul>
                        </div>
                        <?php else :?>
                        <div class="photo">
                                        <img src="/data/skin/mobile_ver2_default/images/custom/profile_modify_photo.png" alt="">
                                    </div>
                        <div class="ment">
                            <span data-status="nologin" data-msg="no">로그인이 되어있지 않습니다.</span>
                        </div>
                        <div class="btnbox">
                            <a<?php if(!$my['uid']) echo ' data-status="nologin"';?>><img class="btn1" src="/data/skin/mobile_ver2_default/images/custom/btn_myPlay.png" alt=""></a> &nbsp;
                            <a<?php if(!$my['uid']) echo ' data-status="nologin"';?>><img class="btn1" src="/data/skin/mobile_ver2_default/images/custom/btn_myping.png" alt=""></a>
                        </div>
                        <img id="top_lang_img" class="lang" src="/data/skin/mobile_ver2_default/images/custom/lang.png" alt="" data-toggling="langsel">
                        <img class="xbtn" src="/data/skin/mobile_ver2_default/images/custom/03_close.png" alt="" data-history="back">
                        <div class="langsel" id="langsel">
                            <ul>
                                <li><a href="#googtrans(pl|ko)" onclick="window.location='#googtrans(pl|ko)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang1.png" alt="" /> 대한민국</a></li>
                                <li><a href="#googtrans(pl|en)" onclick="window.location='#googtrans(pl|en)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang2.png" alt="" /> 미국</a></li>
                                <li><a href="#googtrans(pl|zh-CN)" onclick="window.location='#googtrans(pl|zh-CN)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang3.png" alt="" /> 중국</a></li>
                                <li><a href="#googtrans(pl|ja)" onclick="window.location='#googtrans(pl|ja)'; window.location.reload(); return event.returnValue=true;"><img class="langimg" src="/data/skin/mobile_ver2_default/images/custom/lang4.png" alt="" /> 일본</a></li>
                                <li class="lang_ck">다</li>
                            </ul>
                        </div>
                    <?php endif ?>
                    </div>
                    <div class="box2">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                        <tr>
                        <?php if($my['uid']):?>
                            <td width="15%" class="lp">
                                MY 핑
                            </td>
                            <td width="30%" align="right" class="rp">
                                <span class="num" onclick="javascript:location.href='/mypage/emoney';"><?php echo number_format($my['money']);?></span>원
                            </td>
                            <td align="center" width="5%">
                                <span class="line"></span>
                            </td>
                            <td class="lp" width="30%">
                                <a href="/play" target="_blank">
                                <span class="purple">PLAY</span> 후기 적고 적립핑 받기 </a>
                            </td>
                            <td width="15%">
                                <span class="playc">
                                        <?php
                                            if($my['uid']) echo "0";
                                            else echo "N"; 
                                        ?>건</span>
                            </td>
                        <?php else : ?>
                            <td width="15%" class="lp">
                                MY 핑
                            </td>
                            <td width="30%" align="right" class="rp">
                                <span class="num" data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다."><?php echo number_format($my['money']);?></span>원
                            </td>
                            <td align="center" width="5%">
                                <span class="line"></span>
                            </td>
                            <td class="lp" width="30%">
                                <a data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다.">
                                <span class="purple">PLAY</span> 후기 적고 적립핑 받기 </a>
                            </td>
                            <td width="15%">
                                <span class="playc">
                                        <?php echo "N"; ?>건</span>
                            </td>
                        <?php endif; ?>
                        </tr>
                        </tbody>
                        </table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top:1px solid #ccc;">
                        <tbody>
                        <tr>
                        <?php if($my['uid']):?>
                            <td align="center">
                                <a href="../mypage/index"><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_01.png" alt=""><br>
                                <span class="stxt">마이페이지</span></a>
                            </td>
                            <td align="center">
                                <span class="line"></span>
                            </td>
                            <td align="center">
                                <a href="/mypage/order_catalog"><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_02.png" alt=""><br>
                                <span class="stxt">배송조회</span></a>
                            </td>
                            <td align="center">
                                <span class="line"></span>
                            </td>
                            <td align="center">
                                <div style="display:inline-block;position:relative;">
                                    <a href="/order/cart"><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_03.png" alt=""><br>
                                    <span class="stxt">장바구니</span></a>
                                    <span class="snumc">
                                        <?php
                                            $_sql = "select member_seq from fm_cart where member_seq=".$my['uid'];
                                            $sql = db_query($_sql,$DB_CONNECT);
                                            echo db_num_rows($sql);
                                        ?>
                                    </span>
                                </div>
                            </td>
                            <td align="center">
                                <span class="line"></span>
                            </td>
                            <td align="center">
                                <a href="/mypage/wish"><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_04.png" alt=""><br>
                                <span class="stxt">MY 옷핀</span></a>
                            </td>
                        <?php else : ?>
                            <td align="center">
                                <a data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다."><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_01.png" alt=""><br>
                                <span class="stxt">마이페이지</span></a>
                            </td>
                            <td align="center">
                                <span class="line"></span>
                            </td>
                            <td align="center">
                                <a data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다."><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_02.png" alt=""><br>
                                <span class="stxt">배송조회</span></a>
                            </td>
                            <td align="center">
                                <span class="line"></span>
                            </td>
                            <td align="center">
                                <div style="display:inline-block;position:relative;">
                                    <a data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다."><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_03.png" alt=""><br>
                                    <span class="stxt">장바구니</span></a>
                                    <span class="snumc">0</span>
                                </div>
                            </td>
                            <td align="center">
                                <span class="line"></span>
                            </td>
                            <td align="center">
                                <a data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다."><img class="sicon" src="/data/skin/mobile_ver2_default/images/custom/menu_icon_04.png" alt=""><br>
                                <span class="stxt">MY 옷핀</span></a>
                            </td>
                        <?php endif; ?>
                        </tr>
                        </tbody>
                        </table>
                    </div>
                    <div class="box3">
                        <ul>
                            <!--
                            <li><a href="#none" onclick="javascript:qmenu_tab_open(1);">추천</a><a class="arrbtn" href="/goods/catalog?code=0001"><img class="arr" src="/data/skin/mobile_ver2_default/images/custom/menu_arrow.png" alt=""></a></li>
                            -->
                            <li><a href="/page/index?tpl=mypage/msgbox.html"><img class="micon" src="<?php echo $g['img_layout'] ?>/04/list_icon01.png" alt=""> 추천</a></li>
                            <li><a href="/page/index?tpl=mypage/msgbox.html"><img class="micon" src="<?php echo $g['img_layout'] ?>/04/list_icon02.png" alt=""> 알림</a><span class="dm-circle-count">
                                        <?php 
                                            if($my['uid']){
                                            $_sql = "select mbruid from rb_s_notice where mbruid=".$my['uid'];
                                            $sql = db_query($_sql,$DB_CONNECT);
                                            echo db_num_rows($sql);
                                            }
                                            else echo "0";
                                        ?></span></li>
                            <li><a href="/page/index?tpl=mypage/msgbox.html"><img class="micon" src="<?php echo $g['img_layout'] ?>/04/list_icon03.png" alt=""> 초대하기</a></li>
                            <li><a href="/page/index?tpl=mypage/msgbox.html"><img class="micon" src="<?php echo $g['img_layout'] ?>/04/list_icon04.png" alt=""> 고객센터</a></li>
                            <li><a href="/page/index?tpl=mypage/msgbox.html"><img class="micon" src="<?php echo $g['img_layout'] ?>/04/list_icon05.png" alt=""> 설정</a></li>
                        </ul>
                    </div>
                    <div class="box4">
                        <?php if($my['uid']):?>
                        <a class="btn1" href="/play/?r=<?php echo $r;?>&m=<?php echo $site;?>&a=logout&referer=<?php echo $g[url_root];?>" style="width:30%;">로그아웃</a>
                        <a class="btn1" href="/goods/recently" style="width:40%;">최근 본 상품</a>
                        <?php else : ?>
                        <a class="btn1" data-status="nologin" data-msg="no" style="width:30%;">로그인</a>
                        <a class="btn1" data-status="nologin" data-msg="해당 메뉴는 로그인이 필요합니다." style="width:40%;">최근 본 상품</a>
                        <?php endif; ?>
                        <a class="btn1" href="/play/?a=pcmode" style="width:20%;">PC버전</a>
                    </div>
                    <div class="bgline">
                    </div>
                </div>
            </div>
        </div>
    </div>