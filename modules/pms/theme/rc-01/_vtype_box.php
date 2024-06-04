<div class="rb-viewtype-box">
    <div class="row">
        <?php while($R=db_fetch_array($RCD)):?>
        <?php $_M=getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'*')?>
        <?php 
            $img='';
            if($R['isphoto']&&$R['upload'])
            {
                $img=getUpImageSrc($R); // sys.func.php 파일 참조 
                $img_data=array('src'=>$img,'width'=>'350','height'=>'150','qulity'=>'90','filter'=>'','align'=>'');
            }
        ?>
        <div class="col-sm-6">
            <div class="rb-blog-item thumbnail">
                <a href="<?php echo $g['blog_view'].$R['uid']?>">
                    <?php if($img ):?>
                    <img src="<?php echo getTimThumb($img_data)?>" alt="<?php echo $_R['subject']?>">
                    <?php else:?>
                    <img src="http://placehold.it/400x150?text=사진없음" alt="<?php echo $_R['subject']?>">
                    <?php endif?>
                </a>
                <div class="caption">
                    <h4 class="rb-title"><a href="<?php echo ($rwcat?$g['blog_home_rw'].'/c/'.$rwcat.'/':$g['blog_view']).$R['uid']?>"><?php echo $R['subject']?></a></h4>
                    <p class="rb-description"><?php echo $R['review']?$R['review']:getStrCut(getStripTags($R['content']),$d['blog']['rlength'],'..')?></p>
                    <ul class="rb-meta list-inline">
                        <li><abbr class="rb-date updated"><span class="glyphicon glyphicon-calendar"></span> <time class="timeago rb-tooltip rb-help" data-toggle="tooltip" datetime="<?php echo getDateFormat($R['d_regis'],'c')?>" data-tooltip="tooltip" title="<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?>"></time></abbr></li>
                        <li><span class="rb-popover rb-help" data-placement="top" data-content="" title=""><span class="glyphicon glyphicon-user"></span> <?php echo $_M[$_HS['nametype']]?></span></li>
                       <?php if(IsPostCat($B['uid'],$R['uid'])):?>
                        <li><a href="<?php echo getPostCatLink($B['uid'],$R['uid']);?>" class="rb-tooltip" title="분류"><span class="glyphicon glyphicon-folder-close"></span> <?php echo getPostCatName($B['uid'],$R['uid']);?></a></li>
                        <?php endif?>
                        <li><a href="<?php echo $g['blog_view'].$R['uid']?>#comment" data-toggle="tooltip" title="댓글"><span class="glyphicon glyphicon-comment"></span> <?php echo $R['comment']?></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endwhile?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".rb-viewtype-box .rb-description").shorten({
            showChars: 77,
            moreText: '',
            lessText: ''
        });
        
    });
</script>
