<!DOCTYPE html>
<html lang="en">
    <head>
    <?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
    <?php include $g['path_layout'].'instar/_includes/_import.control.php' ?>
    </head>

    <body>
    <div class="snap-drawers">
      <div class="snap-drawer snap-drawer-left" id="myDrawer">
         <?php include $g['dir_layout'].'/_includes/drawer-left.php' ?>
      </div>
    </div>

    <div class="snap-content" data-extension="drawer">
        <header class="bar bar-nav bar-light bg-faded p-x-0">
            <div class="dm-actual-body">
                <section class="dm-header dm-border-color">
                    <div class="dm-after-signin">
                        <div class="dm-takeup-space">
                            <div class="dm-left">
                                <span class="dm-icon dm-icon-allmenu" data-toggle="drawer" data-target="#myDrawer"></span><span class="dm-img dm-img-logo"></span>
                            </div>
                            <div class="dm-right">
                                <span class="dm-icon dm-icon-goldcoin"></span><span class="dm-amount dm-ft-default">15,000</span><span class="dm-label dm-ft-default">원</span>
                                <span class="dm-icon dm-icon-search" data-toggle="modal" data-target="#"></span>
                            </div>
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
        <section class="bar bar-standard bar-header-secondary">        
            <div class="dm-tabmenu">
                <ul class="dm-ul topMenu-ul">
                    <!-- _import.foot.php swiper 세팅 스크립트 참조 -->
                </ul>
            </div>
        </section>  
        <section class="bar bar-standard bar-header-third">
            <div class="dm-actual-body">
                <div class="dm-event-banner"></div>
            </div>
        </section>

        <div class="content">
            <div class="swiper-container">
              <div class="swiper-wrapper">

                <!-- 인기 -->
                <div class="swiper-slide">
                    <p>최신최신최신최신최신최신최신최신최신최신최신최신 </p>
                    <p>최신최신최신최신최신최신최신최신최신최신최신최신 </p>
                     <p>최신최신최신최신최신최신최신최신최신최신최신최신 </p>
                    <p>최신최신최신최신최신최신최신최신최신최신최신최신 </p>
                     <p>최신최신최신최신최신최신최신최신최신최신최신최신 </p>
                    <p>최신최신최신최신최신최신최신최신최신최신최신최신 </p>

                </div><!-- /.swiper-slide -->
                
                <!-- 인기 -->
                <div class="swiper-slide">            
                    <section>
                        <div class="dm-actual-body">
                            <div class="dm-viewfilter">
                                <div class="dm-takeup-space">
                                    <div class="dm-left">
                                        <h3 class="dm-h3 dm-ft-default">인기</h3>
                                    </div>
                                    <div class="dm-right">
                                        <span class="dm-icon dm-icon-filtergray" data-toggle="modal" data-target="#modal-filter" data-title="필터 설정" data-url="/filter"></span>
                                        <ul class="dm-ul">
                                            <li>
                                                <span class="dm-icon dm-icon-purplerect"></span>
                                            </li>
                                            <li>
                                                <span class="dm-icon dm-icon-gostyle"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                </div><!-- /.swiper-slide -->

                <div class="swiper-slide">
                  <div class="content-padded">
                    <p>Swipe to the right to reveal the left menu.</p>
                    <p>(On desktop click and drag from left to right)</p>
                     <p>Swipe to the right to reveal the left menu.</p>
                    <p>(On desktop click and drag from left to right)</p>
                     <p>Swipe to the right to reveal the left menu.</p>
                    <p>(On desktop click and drag from left to right)</p>
                  </div>
                </div><!-- /.swiper-slide -->

                <div class="swiper-slide">
                  <div class="content-padded">
                    <p>Swipe to the right to reveal the left menu.</p>
                    <p>(On desktop click and drag from left to right)</p>
                     <p>Swipe to the right to reveal the left menu.</p>
                    <p>(On desktop click and drag from left to right)</p>
                     <p>Swipe to the right to reveal the left menu.</p>
                    <p>(On desktop click and drag from left to right)</p>
                  </div>
                </div><!-- /.swiper-slide -->

              </div><!-- /.swiper-wrapper -->
            </div><!-- /.swiper-container -->
        </div><!-- /.content -->
         
    </div><!-- /.snap-content -->
    <?php include $g['dir_layout'].'/_includes/.footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>   
    </body>
</html>