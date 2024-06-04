<?php if($is_home):?>
    <section id="cb-slidebanner">
        <div class="cb-slidebanner-wrapper">
            <span class="cb-icon cb-icon-prev swiper-button-prev"></span>
            <span class="cb-icon cb-icon-next swiper-button-next"></span>
            <div class="cb-slidebanner-items swiper-container">
                <ul class="swiper-wrapper">
                    <?php for($i=1;$i<4;$i++):?>
                    <li class="cb-slidebanner-item swiper-slide">
                        <img src="<?php echo $g['img_layout']?>/banner<?php echo $i?>.png" alt="Banner Item-<?php echo $i?>" />
                    </li>
                    <?php endfor?>
                </ul>
            </div>
        </div>        
    </section>
<?php endif?>
<?php if($_HP['id']!='login'&&$_HP['id']!='join'&&$_HP['id']!='idpwsearch'&&$_HP['id']!='profile'):?>
<section id="cb-category">
    <div class="cb-category-spacefill"></div>
    <nav class="cb-category-selection">
        <div class="cb-content cb-category-menu">
            <div class="cb-layout">
                <div class="cb-category-menudropdown cb-left">
                <?php if($is_home || ($m=='chatbot' && $page=='list') || ($is_oneFrame && $_CA[0]!='intro')):?>
                    <span>CATEGORY</span>
                    <div class="cb-cateogry-menudropdown-items" <?php echo $is_oneFrame?' style="display:none;"':''?> data-role="category-wrapper">
                        <a href="<?php echo RW('c=regisBot')?>" id="btn-regis">+ 챗봇 등록하기</a>
                        <ul>
                        <?php $upjong = explode(',',$d['chatbot']['upjong']);?>
                           <?php foreach ($upjong as $item):?>
                            <li class="cb-cateogry-menudropdown-item<?php echo $cat==$item?' cb-selected':''?>">
                                <a href="<?php echo $catLink.$item?>"><?php echo $item?></a>
                            </li>
                            <?php endforeach?>                      
                        </ul>
                    </div>
                <?php else:?>
<!-- 
                    <a href="<?php echo $g['s']?>/" id="btn-home">HOME</a>
                    <!-- cb-upward / cb-downward 로 토글 처리 하세요 --> 
                    <?php if($M1['is_child']):?>
                    <div class="cb-category-menudropdown-caret cb-upward" style="display: none;"></div>
                    <div class="cb-cateogry-menudropdown-items" data-role="menu-wrapper">
                        <ul>
                            <li class="cb-cateogry-menudropdown-item mypage">
                                <a href="#">마이페이지</a>
                            </li>
                            <?php while($_M2=db_fetch_array($M2)):?>
                                <?php if($M1['id']=='mybot'&&$_M2['id']=='manager'):?>
                                   <?php if(!$my['manager']):?>
                                   <li class="cb-cateogry-menudropdown-item<?php echo $_M2['id']==$_CA[1]?' cb-selected':''?>">
                                     <a href="<?php echo RW('c='.$M1['id'].'/'.$_M2['id'])?>"><?php echo $_M2['name']?></a>
                                   </li>
                                   <?php endif?>
                                <?php else:?>
                                <li class="cb-cateogry-menudropdown-item<?php echo $_M2['id']==$_CA[1]?' cb-selected':''?>">
                                    <a href="<?php echo RW('c='.$M1['id'].'/'.$_M2['id'])?>"><?php echo $_M2['name']?></a>
                                </li>
                                <?php endif?>
                            <?php endwhile?>
                        </ul>
                    </div>
                    <?php endif?>
                <?php endif?>
                </div>
         <!--        <div class="cb-category-menuitems cb-right">
                    <ul>
                        <?php $i=1;foreach ($top_menu as $name => $link):?>
                          <li><a href="<?php echo RW($link)?>" data-role="menu-<?php echo $i?>" ><?php echo $name?></a></li>   
                        <?php $i++;endforeach?>   
                    </ul>
                </div> -->
            </div>
        </div>
    </nav>
</section>
<?php endif?>

<script>

$('[data-role="showHide-cat"]').on('click',function(){
   var $this = $(this);
   var catBox = $('[data-role="category-wrapper"]');
   if($this.hasClass('cb-upward')){
       $(catBox).show();
       $this.removeClass('cb-upward').addClass('cb-downward');
   }else{
       $(catBox).hide();
       $this.removeClass('cb-downward').addClass('cb-upward');
   } 
});


<?php if($_CA[0]=='intro'):?>
$(function(){
    var menuBox = $('[data-role="menu-wrapper"]');
    var intro_menu = $('[data-role="menu-1"]');
    var intro_area = $('.minH');
    var showHideIntroMenu = function(){
        if($(intro_menu).hasClass('menu-showed')){
           $(menuBox).hide();
           $(intro_menu).removeClass('menu-showed');
        }else{
           $(menuBox).show();
           $(intro_menu).addClass('menu-showed');
        } 
    }
    // 챗봇소개 메뉴 클릭 이벤트  
    $(intro_menu).on('click',function(e){
       e.preventDefault();
       showHideIntroMenu();
    });
    $(intro_area).on('click',function(e){
        e.preventDefault();
        showHideIntroMenu();       
    });
});
<?php endif?>


<?php if($is_home):?>
$(document).ready(function() {
    var swiper = new Swiper('.swiper-container', {
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        slidesPerView: 3,
        paginationClickable: true,
        spaceBetween: 0,
        grabCursor: true,
        loop: true
    });
});
<?php endif?>
</script>
