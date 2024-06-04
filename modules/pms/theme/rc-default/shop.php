<?php
$vtype=$vtype?$vtype:'gallery';
?>
<link href="<?php echo $g['url_module_skin']?>/_main.css" rel="stylesheet">
<section class="rb-blog rb-blog-list">
    <form name="ListForm" action="<?php echo $g['s']?>/" method="get">
         <input type="hidden" name="r" value="<?php echo $r?>" />
	   <input type="hidden" name="c" value="<?php echo $c?>" />
	   <input type="hidden" name="m" value="<?php echo $m?>" />
	   <input type="hidden" name="mod" value="<?php echo $mod?>" />
	   <input type="hidden" name="cat" value="<?php echo $cat?>" />
	   <input type="hidden" name="sort" value="<?php echo $sort?>" />
	   <input type="hidden" name="orderby" value="<?php echo $orderby?>" />
	   <input type="hidden" name="recnum" value="<?php echo $recnum?>" />
	   <input type="hidden" name="type" value="<?php echo $type?>" />
	   <input type="hidden" name="iframe" value="<?php echo $iframe?>" />
	   <input type="hidden" name="skin" value="<?php echo $skin?>" />
	   <input type="hidden" name="maker" value="<?php echo $maker?>" />
	   <input type="hidden" name="brand" value="<?php echo $brand?>" />
        <div class="rb-blog-heading">
            <div class="btn-toolbar">
                <div class="btn-group btn-group-sm rb-sort" data-toggle="buttons">
                    <label class="btn btn-default<?php echo $sort=='gid'?' active':''?>" onclick="btnFormSubmit(this);">
                        <input type="radio" name="sort" value="gid"<?php if($sort=='gid'):?> checked<?php endif?> id="gid" autocomplete="off"> 등록순
                    </label>
                    <label class="btn btn-default<?php echo $sort=='hit'?' active':''?>" onclick="btnFormSubmit(this);">
                        <input type="radio" name="sort" value="hit"<?php if($sort=='hit'):?> checked<?php endif?> id="hit" autocomplete="off" > 조회순
                    </label>
                    <label class="btn btn-default<?php echo $sort=='price'?' active':''?>" onclick="btnFormSubmit(this);">
                        <input type="radio" name="sort" value="price"<?php if($sort=='price'):?> checked<?php endif?> id="price" autocomplete="off" > 가격순
                    </label>
                    <label class="btn btn-default<?php echo $sort=='comment'?' active':''?>" onclick="btnFormSubmit(this);">
                        <input type="radio" name="sort" value="comment"<?php if($sort=='comment'):?> checked<?php endif?> id="comment" autocomplete="off" > 댓글순
                    </label>
                </div>
                <div class="btn-group btn-group-sm rb-sort" data-toggle="buttons">
                    <label class="btn btn-default<?php if($orderby=='asc'):?> active<?php endif?>" data-toggle="tooltip" title="오름차순" onclick="btnFormSubmit(this);">
                        <input type="radio" name="orderby" value="asc"<?php if($orderby=='asc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-asc"></i> 정순
                    </label>
                    <label class="btn btn-default<?php if($orderby=='desc'):?> active<?php endif?>" data-toggle="tooltip" title="내림차순" onclick="btnFormSubmit(this);">
                        <input type="radio" name="orderby" value="desc"<?php if($orderby=='desc'):?> checked<?php endif?>> <i class="fa fa-sort-amount-desc"></i> 역순
                    </label>
                </div>
                <div class="btn-group btn-group-sm rb-viewtype" data-toggle="buttons">
                    <label class="btn btn-default <?php echo $vtype=='review'?'active':''?>" data-toggle="tooltip" title="미디어형" onclick="btnFormSubmit(this);">
                        <input type="radio" id="review" autocomplete="off" name="vtype" value="review"<?php if($vtype=='review'):?> checked<?php endif?> > <i class="fa fa-th-list fa-lg"></i>
                    </label>
                    <label class="hidden btn btn-default <?php echo $vtype=='box'?'active':''?>" data-toggle="tooltip" title="박스형" onclick="btnFormSubmit(this);">
                        <input type="radio" id="box" autocomplete="off" name="vtype" value="box"<?php if($vtype=='box'):?> checked<?php endif?>> <i class="fa fa-th fa-lg"></i>
                    </label>
                    <label class="btn btn-default <?php echo $vtype=='list'?'active':''?>" data-toggle="tooltip" title="리스트형" onclick="btnFormSubmit(this);">
                        <input type="radio" id="list" autocomplete="off" name="vtype" value="list"<?php if($vtype=='list'):?> checked<?php endif?>> <i class="fa fa-bars fa-lg"></i>
                    </label>
                    <label class="btn btn-default <?php echo $vtype=='gallery'?'active':''?>" data-toggle="tooltip" title="갤러리형" onclick="btnFormSubmit(this);">
                        <input type="radio" id="gallery" autocomplete="off" name="vtype" value="gallery"<?php if($vtype=='gallery'):?> checked<?php endif?>> <i class="fa fa-picture-o fa-lg"></i>
                    </label>
                    <label class="hidden btn btn-default <?php echo $vtype=='map'?'active':''?>" data-toggle="tooltip" title="지도형" onclick="btnFormSubmit(this);">
                        <input type="radio" id="map" autocomplete="off" name="vtype" value="map"<?php if($vtype=='map'):?> checked<?php endif?>> <i class="fa fa-map-marker fa-lg"></i>
                    </label>
                </div>
            </div>
        </div>
        <!-- blog-body -->
        <div class="rb-blog-body">
            <?php include $g['dir_module_skin'].'_vtype_'.$vtype.'.php'?>
            <?php if(!$NUM && ($vtype=='review' || $vtype=='gallery')):?>
            <div class="rb-nopost">
                 <h2 class=""><i class="fa fa-exclamation-circle fa-4x"></i></h2>
                 <p>등록된 상품이 없습니다.</p>
            </div>
            <?php endif?>
        </div>
        <!-- / blog-body -->
        <div class="rb-blog-footer">
            <nav>
                <ul class="pagination" style="margin: 0">
                 <script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
                 </ul>
            </nav>
        </div>
    </form>
</section>
