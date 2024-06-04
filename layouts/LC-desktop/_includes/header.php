<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top m-b-0">
    <div class="navbar-header"> 
        <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse">
            <i class="fa fa-bars"></i>
        </a>
        <div class="top-left-part">
            <a class="logo" href="index.html">
                <b>
                    <img src="<?php echo $g['img_layout']?>/logo_header.png" alt="home" /></b>
                <span class="hidden-xs" style="margin-left: -10px">
                     <img src="<?php echo $g['img_layout']?>/logo_text.png" alt="home" />
                </span>
            </a>
        </div>
        <ul class="nav navbar-top-links navbar-left m-l-20 hidden-xs">
            <li>
                <a class="LC-title">Learning Center</a>
            </li>
        </ul>
        <ul class="nav navbar-top-links navbar-right pull-right">
            <?php if($my['uid']):?>
            <li>
                <a href="<?php echo $g['s']?>/?a=logout"><span class="cb-name" style="line-height:33px;">로그아웃</span></a>
            </li>

            <?php endif?>

        </ul>
    </div>
    <!-- /.navbar-header -->
    <!-- /.navbar-top-links -->
    <!-- /.navbar-static-side -->
</nav>
<!-- Left navbar-header -->