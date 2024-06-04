<link href="/modules/catalog/theme/rc-01/article.css" rel="stylesheet">

<div id="page-catalog-category" class="page center">

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

                    <div class="card-deck-wrapper">
                        <div class="card-deck">
                            <div class="card">
                                <a href="#" data-toggle="page" data-start="#page-catalog-category" data-target="#page-catalog-product" data-title="늘곁애 240" data-url="catalog/240">
                                    <img class="card-img-top" src="..." alt="Card image cap">
                                    <div class="card-block">
                                        <h4 class="card-title">늘곁애 240</h4>
                                    </div>
                                </a>
                            </div>
                            <div class="card">
                                <a  href="#" data-toggle="page" data-start="#page-catalog-category" data-target="#page-catalog-product" data-title="늘곁애 360" data-url="catalog/360">
                                    <img class="card-img-top" src="..." alt="Card image cap">
                                    <div class="card-block">
                                        <h4 class="card-title">늘곁애 360</h4>
                                    </div>
                                </a>
                            </div>
                            <div class="card">
                                <a href="#" data-toggle="page" data-start="#page-catalog-category" data-target="#page-catalog-product" data-title="늘곁애 480" data-url="catalog/480">
                                    <img class="card-img-top" src="..." alt="Card image cap">
                                    <div class="card-block">
                                        <h4 class="card-title">늘곁애 480</h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <article class="rb-article" data-role="content">
                    </article>
                </div>
                <div class="rb-blog-footer">

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
</div>


<div id="page-catalog-product" class="page right">

    <header class="bar bar-nav">
        <a href="#" data-history="back" class="btn btn-link btn-nav pull-left">
            <span class="icon icon-left-nav"></span>
        </a>
        <h1 class="title">
            상세보기
        </h1>
    </header>
    <div class="content">
        <div class="content-padded">
            제품 상세보기 내용
        </div>
    </div>

</div>


<script type="text/javascript">
var homePage = document.getElementById("homePage"),
    page1 = document.getElementById("p1"),
    currentPage = homePage;

    function slidePageFrom(page, from) {
        // Position the page at the starting position of the animation
        page.className = "page " + from;
        // Position the new page and the current page at the ending position of their animation with a transition class indicating the duration of the animation
        page.className ="page transition center";
        currentPage.className = "page transition " + (from === "left" ? "right" : "left");
        currentPage = page;
    }
</script>