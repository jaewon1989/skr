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
                <form role="search" class="app-search hidden-xs">
                    <input type="text" placeholder="Search..." class="form-control"> <a href=""><i class="fa fa-search"></i></a>
                </form>
            </li>
        </ul>
        <ul class="nav navbar-top-links navbar-right pull-right">
            <?php if($my['uid']):?>
            <?php 
             $user_avatar_src = $chatbot->getUserAvatar($my['uid'],'src');  // use 아바타 정보  src, bg 옵션  
            ?>

            <li>
                <a href="<?php echo $g['s']?>/adm/main">
                    <span class="cb-userwrappers">
                        <img src="<?php echo $user_avatar_src?>" />
                    </span>
                    <span class="cb-name"><?php echo $my[$_HS['nametype']]?>님</span>
                </a>
            </li>
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