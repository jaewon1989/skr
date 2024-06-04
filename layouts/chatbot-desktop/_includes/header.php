<header id="cb-header">
    <div class="cb-header-wrapper">
        <div class="cb-cell-layout">
            <div class="cb-cell cb-logoarea">
                <?php if($V['uid']):?>
                <a href="<?php echo RW('c=mybot')?>">
                    <img src="<?php echo $vendor_logo?>" alt="logo" class="logo-img"/>
                </a>
                <?php endif?>
            </div>
            <div class="cb-cell cb-searcharea">
     <!--            <form id="searchForm" action="<?php echo $g['s']?>/" method="get">
                    <div class="cb-searchbox">
                        <input type="hidden" name="r" value="<?php echo $r ?>">
                        <input type="hidden" name="m" value="chatbot">
                        <input type="hidden" name="page" value="search">
                        <input type="text" name="keyword" value="<?php echo $keyword?>" data-role="search-input" />
                        <span class="cb-icon cb-icon-search" data-role="btn-search"></span>
                    </div>
                </form> -->
            </div>
            <div class="cb-cell cb-utilityarea">
                   
                <ul>
               
                    <?php if($my['uid']):?>
                    <?php 
                     $user_avatar_src = $chatbot->getUserAvatar($my['uid'],'src');  // use 아바타 정보  src, bg 옵션  
                    ?>

                    <li>
                        <a href="<?php echo RW('mod=profile')?>">
                            <span class="cb-userwrappers">
                                <img src="<?php echo $user_avatar_src?>" />
                            </span>
                            <span class="cb-name"><?php echo $my[$_HS['nametype']]?>님</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo RW('c=mybot')?>"><?php echo $chatbot->getUserBotList($my['uid'],'all-inline',$where,30,1);?></a>
                    </li>
                    <li>
                        <a href="<?php echo $g['s']?>/?a=logout"><span class="cb-name" style="line-height:33px;">로그아웃</span></a>
                    </li>
 
                    <?php endif?>
                </ul>
            </div>
        </div>
    </div>
</header>
<script>
$('[data-role="btn-search"]').on('click',function(){
    $('#searchForm').submit();
});
</script>