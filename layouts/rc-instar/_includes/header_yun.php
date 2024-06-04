<header class="bar bar-nav bar-light bg-faded p-x-0">
    <div class="dm-actual-body">
        <section class="dm-header dm-border-color">
            <div id="yg-header">
                <div class="mbtn">
                    <a data-toggle="drawer" data-target="#myDrawer"><img src="/data/skin/mobile_ver2_default/images/custom/shopMenu.png" alt=""></a>
                </div>
                <div class="logo">
                    <a href="/play"><img src="/data/skin/mobile_ver2_default/images/custom/shopLogo.png" alt=""></a>
                </div>
                <div class="srbtn">
                    <a href="#none"><img id="header_srbtn" src="/data/skin/mobile_ver2_default/images/custom/shopSearch.png" alt=""></a>
                </div>
                <div class="ping">
                    <?php if(!$my['id']):?>
                        <a data-toggle="modal" data-target="#modal-login" data-title="로그인" data-url="/play">로그인</a>
                    <?php else:?>
                        <a href="/mypage/emoney">
                        <img class="coin" src="<?php echo $g['path_market']; ?>data/skin/mobile_ver2_default/images/custom/shopCoin.png" alt=""> 
                         <?php echo number_format($my['money'])?>원
                        </a>
                    <?php endif ?>
                </div>
            </div>
            <div class="dm-navigator" style="display: none;"></div>
            <div class="dm-search-area" style="display: none;">
                <div class="dm-search-a">
                    <div class="dm-takeup-space">
                        <div class="dm-left">
                            <span class="dm-icon dm-icon-searchwhite"></span>
                        </div>
                        <div class="dm-center">
                            <input placeholder="검색하기" type="text" />
                        </div>
                        <div class="dm-right">
                            <span class="dm-icon dm-icon-personplus"></span>
                        </div>
                    </div>
                </div>
                <div class="dm-search-b">
                    <div class="dm-takeup-space">
                        <div class="dm-left">
                            <span class="dm-icon dm-icon-search"></span>
                            <input placeholder="검색하기" type="text" />
                        </div>
                        <div class="dm-right">
                            <span class="dm-label dm-ft-default">취소</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</header>