
<div id="cb-leftmenu" class="cb-state-signin">
    <div class="cb-leftmenu-background">
        <div class="cb-leftmenu-header">
            <div class="cb-layout">
                <div class="cb-left">
                    <div class="cb-leftmenu-profileholder" <?php if($my['uid']):?>style="background:none;"<?php endif?>>
                        <?php $user_avatar_src = $chatbot->getUserAvatar($my['uid'],'src');?>
                        <img src="<?php echo $user_avatar_src?>" alt="Signed User Image" style="border-radius:50%;" />    
                    </div>
                </div>
                <div class="cb-right" style="padding-top:8px;">
                    <?php if($my['uid']):?>
                    <h1 class="leftmenu-h1"><?php echo $my[$_HS['nametype']]?> 님</h1>
                    <a href="<?php echo $g['s']?>/?a=logout">로그아웃</a><a>·</a><a href="#" data-toggle="modal" data-role="getComponent" data-target="#modal-profile" data-markup="mProfile" data-url="/?mod=profile">정보수정</a>
                    <a href="<?php echo RW('c=mybot')?>" data-menuPush="true" data-menu="mybot" data-title="기본설정 변경">
                    <?php echo $chatbot->getUserBotList($my['uid'],'all-inline',$where,30,1);?>
                    </a>
                    <?php else:?>
                    <h1 class="leftmenu-h1">로그인을 해주세요</h1>
                    <a href="#" data-role="getLoginModal" data-url="/?mod=login">로그인</a><a>·</a><a href="#" data-toggle="modal" data-target="#modal-join" data-role="getComponent" data-markup="mJoin" data-url="/?mod=join">회원가입</a>
                    <?php endif?>
                </div>
            </div>
        </div>
        <div class="cb-leftmenu-body">
            <ul>
                <li>
                    <a href="#" class="cb-cell-layout" data-toggle="showhide" data-target="#sm-cat">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-cate"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <span class="cb-icon cb-icon-downward"></span>
                            <h3>CATEGORY</h3>
                        </div>
                    </a>
                    <ul class="cb-leftmenu-submenu" style="display:none;" id="sm-cat">
                        <?php $upjong = explode(',',$d['chatbot']['upjong']);?>
                        <?php foreach ($upjong as $item):?> 
                        <li>
                            <a href="<?php echo $catLink.$item?>" data-menuPush="true" data-menu="<?php echo $item?>" ><?php echo $item?></a>
                        </li>
                        <?php endforeach?>
                    </ul>
                </li>
                <li>
                    <a href="#" class="cb-cell-layout" data-toggle="showhide" data-target="#sm-intro">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-robot"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <span class="cb-icon cb-icon-downward"></span>
                            <h3>챗봇소개</h3>
                        </div>
                    </a>    
                    <ul class="cb-leftmenu-submenu" style="display:none;" id="sm-intro">
                        <li>
                            <a href="<?php echo RW('c=intro/bottalks')?>" data-menuPush="true" data-menu="bottalks">봇톡스 소개</a>
                        </li>
                        <li>
                            <a href="<?php echo RW('c=intro/customized')?>" data-menuPush="true" data-menu="customized">맞춤형 챗봇</a>
                        </li>
                        <li>
                            <a href="<?php echo RW('c=intro/premium')?>" data-menuPush="true" data-menu="premium">프리미엄 서비스</a>
                        </li>
                    </ul>
                    
                </li>
                <li>
                    <a href="<?php echo RW('c=build')?>" class="cb-cell-layout" data-menuPush="true" data-menu="build" data-title="챗봇 만들기">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-tool"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <h3>챗봇 만들기</h3>
                        </div>
                    </a>
                </li>
     
                <li>
                    <a href="<?php echo RW('c=talked')?>" class="cb-cell-layout" data-menuPush="true" data-menu="talked">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-msg"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <h3>내가 대화한 챗봇</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?php echo RW('c=added')?>" class="cb-cell-layout" data-menuPush="true" data-menu="added">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-shopper"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <h3>ADD 챗봇</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="cb-cell-layout" data-toggle="showhide" data-target="#my-bot">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-robot"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <span class="cb-icon cb-icon-downward"></span>
                            <h3>나의 챗봇</h3>
                        </div>
                    </a>    
                    <ul class="cb-leftmenu-submenu" style="display:none;" id="my-bot">
                         <li>
                            <a href="<?php echo RW('c=mybot')?>" data-menuPush="true" data-menu="mybot" data-title="기본설정 변경">기본설정 변경</a>
                        </li>
                        <li>
                            <a href="<?php echo RW('c=mybot/message')?>" data-menuPush="true" data-menu="mybot/message" data-title="광고 메세지">광고 메세지</a>
                        </li>
                        <li>
                            <a href="<?php echo RW('c=mybot/story')?>" <?php if(!$my['uid']):?>data-role="getLoginModal"<?php endif?> data-title="대화보기">대화보기</a>
                        </li>
                        <li>
                            <a href="<?php echo RW('c=mybot/statistics')?>" <?php if(!$my['uid']):?>data-role="getLoginModal"<?php endif?> data-title="통계">통계</a>
                        </li>
                    </ul>
                    
                </li>
                <li>
                    <a href="<?php echo RW('c=regisBot')?>" class="cb-cell-layout" data-menuPush="true" data-menu="build" data-title="챗봇 등록하기">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-regis"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <h3>챗봇 등록하기</h3>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="<?php echo RW('c=support')?>" class="cb-cell-layout" data-menuPush="false" data-menu="support">
                        <div class="cb-cell cb-cell-left">
                            <span class="cb-icon cb-icon-agent"></span>
                        </div>
                        <div class="cb-cell cb-cell-right">
                            <h3>고객센터</h3>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>
$('[data-toggle="showhide"]').on('tap',function(e){
    e.preventDefault();
    var target = $(this).data('target');
    var updown = $(this).find('.cb-cell-right > .cb-icon');
    if($(updown).hasClass('cb-icon-downward')){
        $(updown).removeClass('cb-icon-downward');
        $(updown).addClass('cb-icon-upward');
    }else{
        $(updown).removeClass('cb-icon-upward');
        $(updown).addClass('cb-icon-downward');
    } 
    $(target).toggle(50);
});
</script>