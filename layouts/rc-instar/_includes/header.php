<section class="cb-box-shadow-actualelement"></section>
<header id="cb-header" class="bar bar-nav cb-box-shadow">
    <div class="cb-cell-layout">
        <div class="cb-cell cb-cell-left" data-toggle="drawer" data-target="#myDrawer" data-history="false">
            <span class="cb-icon cb-icon-menu"></span>
        </div>
        <div class="cb-cell cb-cell-center">
            <a href="<?php echo $g['s']?>/">CHATBOT</a>
        </div>
        <div class="cb-cell cb-cell-right">
            <?php if($my['uid']):?>
            <a href="#" data-toggle="modal" data-role="getComponent" data-target="#modal-profile" data-markup="mProfile" data-url="/?mod=profile"><span class="cb-icon cb-icon-option"></span></a>
            </a>
            <?php endif?>
            <span class="cb-icon cb-icon-search"></span>
        </div>
    </div>
</header>
