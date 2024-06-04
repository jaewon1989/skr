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

    <div class="snap-content" data-extension="drawer">
        <?php include $g['dir_layout'].'/_includes/header.php' ?>
        <?php include $g['dir_layout'].'/_includes/content.control.php';?> <!-- feed 출력 관리 -->

         <section class="bar bar-standard bar-header-secondary">        
            <div class="dm-tabmenu">
                <ul class="dm-ul topMenu-ul">
                    <!-- _import.foot.php swiper 세팅 스크립트 참조 -->
                </ul>
            </div>
        </section>  

    <!-- main content -->
    <div class="content" id="main-content">
        <section>
            <div class="dm-actual-body">
                <div class="dm-event-banner"></div>
            </div>
        </section>
        <section class="filter-wrap affix-top" data-role="filter-wrap">
              <?php echo $filter_default?>
        </section>    

        <div class="swiper-container" id="swiper-menu">
          <div class="swiper-wrapper">
                <!-- 최신 -->
                <div class="swiper-slide" data-mod="new">   <!-- data-mod 필터 수정시 사용  -->        
                    <section data-role="feedListWrap-new">  <!-- data-role 더보기등 피드 리스트 출력시 사용  -->                    
                        <?php echo $Feed_New_List ;?>
                    </section>
                </div><!-- /.swiper-slide -->
                
                <!-- 인기 -->
                <div class="swiper-slide" data-mod="hot">            
                    <section data-role="feedListWrap-hot">                     
                        <?php echo $Feed_Hot_List ;?>
                    </section>
                </div><!-- /.swiper-slide -->

                <!-- 패션왕 -->
                <div class="swiper-slide" data-mod="best">            
                    <section data-role="feedListWrap-best">                     
                        <?php echo $Feed_Best_List ;?>
                    </section>
                </div><!-- /.swiper-slide -->

                <!-- 영상 -->
                <div class="swiper-slide" data-mod="video">            
                    <section data-role="feedListWrap-video">                     
                        <?php echo $Feed_Video_List ;?>
                    </section>
                </div><!-- /.swiper-slide -->
         
          
          </div><!-- /.swiper-wrapper -->

        </div><!-- /.swiper-container -->

      </div><!-- /.content -->
    </div><!-- /.snap-content -->

    <?php include $g['dir_layout'].'/_includes/footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.yun.php' ?> 
    <?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>
   

    </body>
</html>
<script>

// 탑메뉴 및 메인 content swiper 세팅 
var Menu = new Swiper('#swiper-menu', {
    pagination: '.topMenu-ul',
    paginationClickable: true,
    effect: 'slide',
    initialSlide: 1,
    loop: false,
    autoHeight: true,
    slideActiveClass :'active',
    bulletClass : 'nav-inline',
    bulletActiveClass : 'active' ,
    paginationBulletRender: function (index, className) {
        var menu;
        var width;
        if(memberuid) width='20%';
        else width='25%';

        if (index == 0) menu = 'dm-latest';
        if (index == 1) menu = 'dm-favorite';
        if (index == 2) menu = 'dm-fashionking';
        if (index == 3) menu = 'dm-video';
       
        return '<li class="'+ menu +' '+ className+'"></li>';
     },
    onSlideChangeEnd: function (Menu) {
        $('#main-content').animate({scrollTop:0}, '100');
        var mod = $('#swiper-menu').find('.swiper-slide.active').data('mod');
        $('#swiper-menu').find('.swiper-slide.active').css("height","auto").css("overflow","auto");
        do_contentSlide_Change(mod); // content 슬라이드 변경될 때 추가적용 함수 
        // swiper, photoswipe, drawer 리세팅  
        init_afterAjax();
    }
});

// 드로어 익스텐션 초기화
snapper = new Snap({
    element: $("#myDrawer")[0],
    maxPosition: 1,
    minPosition: -1,
    transitionSpeed: 0.1
})

// Initialize drawer
RC_initDrawer();

</script>