<?php if($is_home):?>
    <section id="cb-slidebanner">
        <div class="cb-slidebanner-wrapper">
            <span class="cb-icon cb-icon-prev swiper-button-prev"></span>
            <span class="cb-icon cb-icon-next swiper-button-next"></span>
            <div class="cb-slidebanner-items swiper-container">
                <ul class="swiper-wrapper">
                    <li class="cb-slidebanner-item swiper-slide">
                        <div class="cb-slidebanner-overlay"></div>
                        <img src="<?php echo $g['img_layout']?>/iphone-large.jpg" alt="Banner Item Left" />
                    </li>
                    <li class="cb-slidebanner-item swiper-slide">
                        <img src="<?php echo $g['img_layout']?>/iPhoneText.jpg" alt="Banner Item Center" />
                    </li>
                    <li class="cb-slidebanner-item swiper-slide">
                        <div class="cb-slidebanner-overlay"></div>
                        <img src="<?php echo $g['img_layout']?>/read-full-review-7.jpg" alt="Banner Item Right" />
                    </li>
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
                <?php if($is_home || ($m=='chatbot' && $page=='list') || $is_oneFrame):?>
                    <span>CATEGORY</span>
                    <?php if($is_oneFrame):?>
                    <div class="cb-category-menudropdown-caret cb-upward" data-role="showHide-cat"></div>
                    <?php endif?>
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

                    <span><a class="cb-icon cb-icon-house"></a> HOME</span>
                    <!-- cb-upward / cb-downward 로 토글 처리 하세요 -->
                    <?php if($M1['is_child']):?>
                    <div class="cb-category-menudropdown-caret cb-upward" style="display: none;"></div>
                    <div class="cb-cateogry-menudropdown-items">
                        <ul>
                            <?php while($_M2=db_fetch_array($M2)):?>
                            <li class="cb-cateogry-menudropdown-item<?php echo $_M2['id']==$_CA[1]?' cb-selected':''?>">
                                <a href="<?php echo RW('c='.$M1['id'].'/'.$_M2['id'])?>"><?php echo $_M2['name']?></a>
                            </li>
                            <?php endwhile?>
                        </ul>
                    </div>
                    <?php endif?>
                <?php endif?>
                </div>
                <div class="cb-category-menuitems cb-right">
                    <ul>
                        <?php foreach ($top_menu as $name => $link):?>
                          <li><a href="<?php echo RW($link)?>" ><?php echo $name?></a></li>   
                        <?php endforeach?>   
                    </ul>
                </div>
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
