<!DOCTYPE html>
<html lang="en">
    <head>
    <?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.control.php' ?>  
    </head>

    <body>
    <div class="snap-drawers">
      <div class="snap-drawer snap-drawer-left" id="myDrawer">
         <?php include $g['dir_layout'].'/_includes/drawer-left.php' ?>
      </div>
    </div>

    <div class="snap-content" data-extension="drawer" >
        <?php include $g['dir_layout'].'/_includes/header.php' ?>
        <?php include $g['dir_layout'].'/_includes/content.control.php';?> <!-- feed 출력 관리 -->
        
        <section class="bar bar-standard bar-header-secondary">        
            <div class="dm-tabmenu">
                <ul class="dm-ul topMenu-ul">
                    <!-- _import.foot.php swiper 세팅 스크립트 참조 -->
                </ul>
            </div>
        </section>  
        <div class="content" >
            <section>
                <div class="dm-actual-body">
                    <div class="dm-event-banner"></div>
                </div>
            </section> 
            <div class="swiper-container" id="swiper-menu">
              <div class="swiper-wrapper">

                <!-- 최신 -->
                <div class="swiper-slide">
                    <section id="filter-wrap" >
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
                    <section data-role="HotFeed-wrap">                     
                        <?php echo $Feed_New_List ;?>
                    </section>

                </div><!-- /.swiper-slide -->
                
                <!-- 인기 -->
                <div class="swiper-slide">            
                    <section id="filter-wrap" >
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
                    <section data-role="HotFeed-wrap">                     
                        <?php echo $Feed_Hot_List ;?>
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
   
    <?php include $g['dir_layout'].'/_includes/footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>   
    </body>
</html>