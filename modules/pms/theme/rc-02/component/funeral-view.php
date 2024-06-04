<link href="/modules/catalog/theme/rc-01/article.css" rel="stylesheet">

<header class="bar bar-nav">
      <a href="#" class="btn btn-link btn-nav pull-right" data-history="back">
            <span class="icon icon-close"></span>
      </a>
      <a href="#popover-view-menu" class="btn btn-link btn-nav pull-right">
            <span class="icon icon-more-vertical"></span>
      </a>
      <h1 class="title rb-left">
            <span data-role="title"></span>
            <span class="badge badge-primary badge-inverted" data-role="catalogName"></span>
      </h1>
</header>
<div class="content">
      <section class="rb-format-default">
            <div class="rb-blog-header card card-inverse" data-role="coverImg" >
                <div class="card-block">
                    <p class="card-text rb-category"><span class="badge" data-role="category"></span></p>
                    <h4 class="card-title rb-title" data-role="title"></h4>
                    <p class="card-text rb-destription" data-role="review"></p>
                    <p class="card-text rb-meta">
                       <span class="badge badge-inverted"><i class="fa fa-calendar"></i> <span data-role="created"></span>
                       <span class="badge badge-inverted"><i class="fa fa-user"></i> <span data-role="author"></span></span>
                       <span class="badge badge-inverted"><i class="fa fa-eye"></i> <span data-role="viewed"></span></span>
                    </p>
                    <p class="card-text rb-scroll"><span class="icon icon-down-nav"></span>&nbsp;아래로 스크롤</p>
                </div>
            </div>
            <div class="rb-blog-body">
                <article class="rb-article" data-role="content">
                </article>
            </div>
            <div class="rb-blog-footer">
                <ul class="table-view rb-author" style="display: none">
                    <li class="table-view-cell table-view-divider">작성자</li>
                    <li class="table-view-cell media">
                        <a data-toggle="modal" data-target="modal-bbs-default" data-title="프로필 보기">
                            <img class="media-object pull-left rb-avatar" src="http://live2.kimsq.com/avatar/sf14534519348196_m.jpg" data-role="avatar">
                            <div class="media-body">
                                <strong data-role="author"></strong>
                                <p data-role="email"></p>
                            </div>
                        </a>
                    </li>
                </ul>
                <div data-role="tagList">
                     <!-- 태그 리스트 : view 모달 호출시 ajax 로 가져온다.-->
                </div>
                <div data-role="attachList">    
                     <!-- 첨부 파일 리스트  : view 모달 호출시 ajax 로 가져온다.-->                 
                </div>

                <div class="content-padded">
                    <p class="segmented-control rb-share ">
                        <a class="control-item rb-share-facebook"><i class="fa fa-facebook fa-lg"></i></a>
                        <a class="control-item rb-share-twitter"><i class="fa fa-twitter fa-lg"></i></a>
                        <a class="control-item rb-share-kakao"><i class="fa fa-comment fa-lg"></i></a>
                    </p>
                </div>

            </div>
      </section>
</div> <!-- .content -->

