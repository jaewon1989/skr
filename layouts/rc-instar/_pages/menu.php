<header class="bar bar-nav">
    <button class="btn btn-link btn-nav pull-left rb-back" data-transition="fade">
        <span class="icon icon-left-nav"></span>&nbsp;이전
    </button>
  <a class="btn btn-link btn-nav pull-right" href="<?php echo RW(0)?>" data-transition="fade">
    <span class="icon icon-home"></span>
  </a>
    <h1 class="title">메뉴</h1>
</header>



<nav class="bar bar-tab">
  <a class="tab-item active" href="#">
    <span class="icon icon-list"></span>
    <span class="tab-label">메뉴</span>
  </a>
  <a class="tab-item" href="#">
    <span class="icon icon-person"></span>
    <span class="tab-label">내정보</span>
  </a>
  <a class="tab-item" href="#">
    <span class="icon icon-info"></span>
    <span class="tab-label">연락하기</span>
  </a>
  <a class="tab-item" href="#">
    <span class="icon icon-search"></span>
    <span class="tab-label">통합검색</span>
  </a>
  <a class="tab-item" href="#">
    <span class="icon icon-gear"></span>
    <span class="tab-label">설정</span>
  </a>
</nav>

<div class="content">
    <div class="content-padded">
        <p>
            메뉴를 선택해주세요 <br>
        </p>
    </div>

    <h5 class="content-padded">소비자 미디어</h5>
    <ul class="table-view">
        <li class="table-view-cell"><a href="<?php echo RW('c=portfolio-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">5</span>소비자 이슈칼럼</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=portfolio-02')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">15</span>소비자 이슈파인더</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=portfolio-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">25</span>소비자 핫이슈</a></li>
            <li class="table-view-cell"><a href="<?php echo RW('c=portfolio-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">25</span>소비자 기본상식,이론</a></li>
                    <li class="table-view-cell"><a href="<?php echo RW('c=portfolio-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">25</span>소비자 상품비교</a></li>
    </ul>

    <h5 class="content-padded">미래 소비자포럼</h5>
    <ul class="table-view">
        <li class="table-view-cell"><a href="<?php echo RW('c=blog-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">5</span>공지사항</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=blog-02')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">15</span>블로그</a></li>
    </ul>

    <h5 class="content-padded">커뮤니티</h5>
    <ul class="table-view">
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">5</span>참여형 Q&A</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-02')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">25</span>정보공유 게시판</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">35</span>소비경험 게시판</a></li>
                <li class="table-view-cell"><a href="<?php echo RW('c=gallery-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">35</span>그룹</a></li>
    </ul>

    <h5 class="content-padded">후원</h5>
    <ul class="table-view">
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade">후원회원 안내</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-02')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade">후원내역 확인하기</a></li>
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade">기부금현황</a></li>
                <li class="table-view-cell"><a href="<?php echo RW('c=gallery-03')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade">후원회원 문의</a></li>
    </ul> 
    <h5 class="content-padded">자료실</h5>
    <ul class="table-view">
        <li class="table-view-cell"><a href="<?php echo RW('c=gallery-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" class="navigate-right" data-transition="fade"><span class="badge">5</span>연구자료</a></li>

    </ul>    

    <h5 class="content-padded">소개</h5>
    <ul class="table-view">
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="badge">5</span>
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              공지사항
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              인사말
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              설립목적
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              조직도
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              함께하는 사람들
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              연혁
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              정관
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              보도자료 및 언론기사
            </div>
          </a>
        </li>
        <li class="table-view-cell media">
          <a class="navigate-right" href="<?php echo RW('c=notice-01')?>&amp;prelayout=<?php echo $d['layout']['dir']?>/blank" data-transition="fade">
            <span class="media-object icon icon-more pull-left"></span>
            <div class="media-body">
              찾아오시는길
            </div>
          </a>
        </li>
    </ul>



</div>


