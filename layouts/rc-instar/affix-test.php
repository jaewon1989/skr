<!DOCTYPE html>
<html lang="en">
    <head>
    <?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.control.php' ?>  
    </head>

 <body class="hd-sub hd-sm">

    <div class="snap-drawers">
      <div class="snap-drawer snap-drawer-right" id="drawer">
      </div>
    </div>

    <div class="snap-content" data-extension="drawer">

      <!-- header -->
      <header class="bar bar-nav bar-dark bg-primary">
        <a class="icon icon-simbol pull-left"></a>
        <a class="icon icon-bars pull-right" data-target="#drawer" data-toggle="drawer" data-direction="right" data-template="./template/drawer.html"></a>
        <a class="icon icon-qrcode pull-right"></a>
        <a class="icon icon-logout pull-right"></a>
        <h1 class="title left">시설/작업현황</h1>
      </header>

      <!-- secondary header-->
      <div class="bar bar-standard bar-header-secondary hd-bar-white" id="header-secondary">
        <div class="clearfix">
          <button class="btn btn-link btn-nav pull-left">
            <span class="icon icon-left-nav"></span>
            유해위험기구현황_상세
          </button>
        </div>
      </div>

      <!-- main content -->
      <div class="content">
        <div class="nav nav-inline affix-top" id="tabs">
        </div>

        <div class="swiper-container hd-swiper-01">
          <div class="swiper-wrapper">

            <div class="swiper-slide">


            </div><!-- /.swiper-slide -->

            <div class="swiper-slide">

              <h5 class="hd-heading no-bullet content-padded">제원기본정보</h5>
              <div class="card hd-search hd-search-result">
                <div class="card-block p-a-1">
                  <dl class="row">
                    <dt class="col-xs-4">설비명</dt>
                    <dd class="col-xs-8">통합개발센터</dd>
                    <dt class="col-xs-4">제조사</dt>
                    <dd class="col-xs-8">지에스에이</dd>
                    <dt class="col-xs-4">제작일</dt>
                    <dd class="col-xs-8">2012.05.15</dd>
                    <dt class="col-xs-4">설치일</dt>
                    <dd class="col-xs-8">2016.07.18</dd>
                    <dt class="col-xs-4 spacing-m1">기기형식번호</dt>
                    <dd class="col-xs-8">SJ-A0751624-S10</dd>
                    <dt class="col-xs-4">용도</dt>
                    <dd class="col-xs-8">-</dd>
                    <dt class="col-xs-4">운전방식</dt>
                    <dd class="col-xs-8">-</dd>
                  </dl>
                </div>
              </div><!-- /.card -->

              <h5 class="hd-heading no-bullet content-padded">제원상세정보</h5>
              <div class="card hd-search hd-search-result">
                <div class="card-block p-a-1">

                  <h5 class="h6">주권</h5>

                  <div class="row p-x-0">
                    <div class="col-xs-6">
                      <div class="card bg-sky">
                        <dl class="card-block row">
                          <dt class="col-xs-3 hd-none">압입능력</dt>
                          <dd class="col-xs-9 text-xs-right">10 ton</dd>
                        </dl>
                      </div>
                    </div>
                    <div class="col-xs-6 p-l-0">
                      <div class="card bg-sky">
                        <dl class="card-block row">
                          <dt class="col-xs-3 hd-none">클러치</dt>
                          <dd class="col-xs-9 text-xs-right spacing-m1">슬라이딩핀클러치</dd>
                        </dl>
                      </div>
                    </div>
                    <div class="col-xs-6">
                      <div class="card bg-sky">
                        <dl class="card-block row">
                          <dt class="col-xs-3 hd-none">브레이크</dt>
                          <dd class="col-xs-9 text-xs-right spacing-m1">디스크브레이크</dd>
                        </dl>
                      </div>
                    </div>
                    <div class="col-xs-6 p-l-0">
                      <div class="card bg-sky">
                        <dl class="card-block row">
                          <dt class="col-xs-3 hd-none">방호장치</dt>
                          <dd class="col-xs-9 text-xs-right spacing-m1">과부하방지장치</dd>
                        </dl>
                      </div>
                    </div>
                  </div><!-- /.row -->

                </div>
              </div><!-- /.card -->

              <h5 class="hd-heading no-bullet content-padded">설비정비 및 철거정보</h5>

              <div class="card hd-search hd-search-result">
                <div class="card-block p-a-1">
                  <dl class="row">
                    <dt class="col-xs-3">정비부서</dt>
                    <dd class="col-xs-9">재선원료부</dd>
                    <dt class="col-xs-3">폐기</dt>
                    <dd class="col-xs-9">Y</dd>
                    <dt class="col-xs-3">폐기년도</dt>
                    <dd class="col-xs-9">2014.07.01</dd>
                    <dt class="col-xs-12">특기사항</dt>
                    <dd class="col-xs-12 none-line-clamp p-l-1 p-t-05">
                      특기사항내용입니다. 특기사항내용입니다. 특기사항내용입니다.특기사항내용입니다. 특기사항내용입니다. 특기사항내용입니다.
                    </dd>
                  </dl>
                </div>
              </div>

            </div><!-- /.swiper-slide -->

            <div class="swiper-slide">

            </div><!-- /.swiper-slide -->


          </div><!-- /.swiper-wrapper -->

        </div><!-- /.swiper-container -->

      </div><!-- /.content -->
    </div><!-- /.snap-content -->


    <!-- 업체 및 부서검색 모달 -->
    <div class="modal" id="U_SM_E10000"></div>


    



    <?php include $g['dir_layout'].'/_includes/footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.foot.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>   
    <script>
      $('#tabs').scroll({
        type: 'affix',
        offset:{top:50}
      });
      // apply animation
      $('#tabs').on('affixed.rc.scroll',function(){
        $(this).addClass('animated fadeInDown')
      })
      $('#tabs').on('affixed-top.rc.scroll',function(){
        $(this).removeClass('animated fadeInDown')
      })
      // Initialize Swiper
      var swiper = new Swiper('.hd-swiper-01', {
        initialSlide: 1, // 데모용 : 2번째 슬라이드를 시작슬라이드로 설정 함.
        pagination: '.nav-inline',
        paginationClickable: true,
        effect: 'slide',
        loop: false,
        autoHeight: true,
        slideActiveClass :'active',
        bulletClass : 'nav-link',
        bulletActiveClass : 'active' ,
        paginationBulletRender: function (index, className) {
        var title;
        if (index === 0) title = '관리기본정보';
        if (index === 1) title = '설비제원'
        if (index === 2) title = '관리이력'
        return '<a class="' + className + '">'+title+'</a>';
        },
        onSlideChangeEnd: function (swiper) {
            $('.content').animate({scrollTop:0}, '100');
        }
      });
    </script>
    </body>
</html>