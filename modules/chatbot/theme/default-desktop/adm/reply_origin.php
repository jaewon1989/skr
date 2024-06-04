<?php 
include $g['path_module'].$m.'/includes/tree.func.php';
$base_url = $g['chatbot_page'].'&bot='.$bot;
if($cat)
{
    $CINFO = getUidData($table[$m.'category'],$cat);
    $ctarr = getMenuCodeToPathBlog($table[$m.'category'],$cat,0);
    $ctnum = count($ctarr);
    $CINFO['code'] = '';

    for ($i = 0; $i < $ctnum; $i++)
    {
        $CXA[] = $ctarr[$i]['uid'];
        $CINFO['code'] .= $ctarr[$i]['id'].($i < $ctnum-1 ? '/' : '');
        $_code .= $ctarr[$i]['uid'].($i < $ctnum-1 ? '/' : '');
    }
    $code = $code ? $code : $_code;

    for ($i = 0; $i < $ctnum; $i++) $CXA[] = $ctarr[$i]['uid'];
}

// quesCat 세팅 
$quesCat = $code;

// 선택된 vendor,bot,cat 으로 멀티답변 데이타 세팅
$rc_data = array();
$rc_data['vendor'] = $V['uid'];
$rc_data['bot'] = $B['uid'];
$rc_data['quesCat'] = $code;
$RC = $chatbot->getReplyMultiData($rc_data);


?>
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<section id="cb-chatbot-factory" style="padding: 35px 35px 80px 35px">
    <form name="procForm" action="<?php echo $g['s']?>/" method="get">
         <input type="hidden" name="r" value="<?php echo $r?>" />
         <input type="hidden" name="m" value="<?php echo $m?>" />
         <input type="hidden" name="c" value="<?php echo $c?>" />
         <input type="hidden" name="page" value="<?php echo $page?>" />
         <input type="hidden" name="cat" value="<?php echo $cat?>" />

         <div class="cb-viewchat-search">
            <table style="width:100%">
                <tr>
                    <td class="cb-chatbot-factory-wrapper">
                       <h1>답변 관리</h1> 
                    </td>
                    <td style="width:30%;">
                        <?php $_WHERE2='vendor='.$V['uid'].' and type=1';?>
                        <?php $_BCD = getDbArray($table[$m.'bot'],$_WHERE2,'*','gid','desc','',1);?>
                        <div class="cb-viewchat-search-timebox" style="width:95%;margin-left:5%;">
                            <select name="bot" style="font-size:inherit;" onchange="this.form.submit();">
                                <option value="">캠퍼스를 선택해주세요</option>
                                <?php $i=1;while($_B=db_fetch_array($_BCD)):?>
                                <option value="<?php echo $_B['uid']?>" <?php if($bot==$_B['uid']):?>selected<?php endif?>>
                                    <?php echo $_B['service']?>
                                </option>
                                <?php $i++;endwhile?> 
                            </select>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <div class="cb-chatbot-factory-wrapper" id="rb-body">

        <div id="catebody" class="row">
            <div id="category" class="col-sm-4 col-md-3 col-lg-3">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading rb-icon">
                            <h4 class="panel-title">질문 분류</h4>
                        </div>
                        <div class="panel-collapse collapse in" id="collapmetane">
                                
                            <div class="panel-body">
                                <div style="min-height:300px;">
                                    <link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
                                    <?php $_treeOptions=array('table'=>$table[$m.'category'],'dispNum'=>true,'dispHidden'=>false,'dispCheckbox'=>false,'allOpen'=>false,'bookmark'=>false)?>
                                    <?php $_treeOptions['link'] = $base_url.'&amp;cat='?>
                                    <?php echo getTreeCategory($_treeOptions,$code,0,0,'')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="catinfo" class="col-sm-8 col-md-9 col-lg-9">
                <?php if($cat&&$bot):?>
                <div class="content-header">
                    <h3>
                        <code><?php echo $B['service']?></code> > <strong class="main-color"><?php echo $CINFO['name']?></strong> <span class="text-muted">에 대한 답변을 설정해주세요</span>
                    </h3>
                    <div class="btn-save">저장하기</div>
                </div>
                <form class="form-horizontal rb-form" name="procForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
                    <input type="hidden" name="r" value="<?php echo $r?>">
                    <input type="hidden" name="m" value="<?php echo $m?>">
                    <input type="hidden" name="a" value="vendor_add_reply">
                    <input type="hidden" name="quesCat" value="<?php echo $quesCat?>" />
                    <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />
                    <input type="hidden" name="bot" value="<?php echo $bot?>" />

                    <div class="form-group">
                        <label class="col-md-9"><code class="label-fa"><i class="fa fa-text-width"></i></code>텍스트 타입</label>
                        <div class="col-md-3 select-show" id="showHide-wrapper-text">
                            <input type="hidden" name="showType[]" value="" data-role="showType-text">
                            <span class="showHide-label"><span class="showHide-textOn<?php echo $R['show_text']?' active':''?>">ON</span>/<span class="showHide-textOff<?php echo $R['show_text']?'':' active'?>">OFF</span></span>  
                            <div class="cb-cell cb-cell-right<?php echo $RC['show_text']?'':' botSwitch-off'?>">
                                <div class="cb-switch" data-role="btn-showHide" data-target="text" data-container="#showHide-wrapper-text">
                                    <span class="cb-switch-button"></span>
                                 </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fg-content">
                                <textarea name="text[]" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="label-box">
                            <label class="col-md-9"><code class="label-fa"><i class="fa fa-square-o"></i></code>카드 타입</label>
                            <div class="col-md-3 select-show" id="showHide-wrapper-card">
                                <input type="hidden" name="showType[]" value="" data-role="showType-card">
                                <span class="showHide-label"><span class="showHide-textOn<?php echo $R['show_text']?' active':''?>">ON</span>/<span class="showHide-textOff<?php echo $R['show_text']?'':' active'?>">OFF</span></span>  
                                <div class="cb-cell cb-cell-right<?php echo $RC['show_card']?'':' botSwitch-off'?>">
                                    <div class="cb-switch" data-role="btn-showHide" data-target="card" data-container="#showHide-wrapper-card">
                                        <span class="cb-switch-button"></span>
                                     </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 scroll-group" >
                            <div class="fg-content" data-role="card-wrapper">
                                <div class="card" data-role="card-item">
                                   <div class="card-img-wrapper" data-role="cardImg-wrapper"><!-- 해당 이미지 배경으로 세팅--></div>
                                   <div class="card-body">
                                        <p class="card-text">
                                            <input type="text" name="card_title[]" placeholder="제목을 입력해주세요." /> 
                                        </p>
                                        <p class="card-text">
                                            <input type="text" name="card_subTitle[]" placeholder="부제목을 입력해주세요." /> 
                                        </p>
                                        <p class="card-text">
                                            <input type="text" name="card_link[]" placeholder="링크를 연결해주세요." /> 
                                        </p>
                                    </div>
                                </div>
                                <div class="card" data-role="add-card">
                                    <div class="card-img-more"></div>
                                </div>
                            </div>   
                        </div>    
                    </div>
                    <div class="form-group">
                        <div class="label-box">
                            <label class="col-md-9"><code class="label-fa"><i class="fa fa-picture-o"></i></code>이미지 타입</label>
                            <div class="col-md-3 select-show" id="showHide-wrapper-image">
                                <input type="hidden" name="showType[]" value="" data-role="showType-image">
                                <span class="showHide-label"><span class="showHide-textOn<?php echo $R['show_text']?' active':''?>">ON</span>/<span class="showHide-textOff<?php echo $R['show_text']?'':' active'?>">OFF</span></span>  
                                <div class="cb-cell cb-cell-right<?php echo $RC['show_image']?'':' botSwitch-off'?>">
                                    <div class="cb-switch" data-role="btn-showHide" data-target="image" data-container="#showHide-wrapper-image">
                                        <span class="cb-switch-button"></span>
                                     </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 scroll-group">
                            <div class="fg-content" data-role="image-wrapper">
                                <div class="card" data-role="image-item">
                                   <div class="card-img-wrapper" data-role="cardImg-wrapper"><!-- 해당 이미지 배경으로 세팅--></div>
                                </div>
                                <div class="card" data-role="add-image">
                                    <div class="image-img-more"></div>
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="form-group">
                        <div class="label-box">
                            <label class="col-md-9"><code class="label-fa"><i class="fa fa-bars"></i></code>메뉴 타입</label>
                            <div class="col-md-3 select-show" id="showHide-wrapper-menu">
                                <input type="hidden" name="showType[]" value="" data-role="showType-menu">
                                <span class="showHide-label"><span class="showHide-textOn<?php echo $R['show_text']?' active':''?>">ON</span>/<span class="showHide-textOff<?php echo $R['show_text']?'':' active'?>">OFF</span></span>  
                                <div class="cb-cell cb-cell-right<?php echo $RC['show_menu']?'':' botSwitch-off'?>">
                                    <div class="cb-switch" data-role="btn-showHide" data-target="menu" data-container="#showHide-wrapper-menu">
                                        <span class="cb-switch-button"></span>
                                     </div>
                                </div>
                            </div>
                        </div>    
                        <div class="col-md-12 scroll-group">
                            <div class="fg-content" data-role="rMenu-wrapper">
                                <div class="card" data-role="menu-item">
                                    <div class="card-body">
                                        <p class="card-text" style="border-top:none;">
                                            <input type="text" name="menu_title[]" placeholder="메뉴명을 입력해주세요." /> 
                                        </p>
                                        <p class="card-text">
                                            <input type="text" name="menu_link[]" placeholder="링크를 연결해주세요." /> 
                                        </p>
                                    </div>
                                </div>
                                <div class="card" data-role="add-menu">
                                    <div class="menu-img-more"></div>
                                </div>
                            </div>    
                        </div>    
                    </div>           
                </form>
                <?php else:?>
                <h3 class="text-muted">좌측 <code>질문분류</code> 항목과 우상단 <code>캠퍼스</code>를 선택해주세요. </h3>
                <?php endif?>
            </div>
        </div> 
    </div>
</section>
<script>
// card 추가하기 이벤트 
$(document).on('click','[data-role="add-card"]',function(){
    var this_wrapper = '[data-role="card-wrapper"]'; 
    var card_more = $(this);
    var clone_obj = $(this).parent().parent().find('[data-role="card-item"]:last');
    var card_blank = '<?php echo $g['img_layout']?>/card-blank.png';
    $(clone_obj).clone().appendTo(this_wrapper);
    $(this).remove();
    $(card_more).appendTo(this_wrapper);
    setTimeout(function(){
        $(clone_obj).find('input[name="card_title[]"]').val('');
        $(clone_obj).find('input[name="card_subTitle[]"]').val('');
        $(clone_obj).find('input[name="card_link[]"]').val('');
        $(clone_obj).find('[data-role="cardImg-wrapper"]').css("background","url("+card_blank+") no-repeat center center");

    },10);    

 });

// image 추가하기 이벤트 
$(document).on('click','[data-role="add-image"]',function(){
    var this_wrapper = '[data-role="image-wrapper"]'; 
    var card_more = $(this);
    var clone_obj = $(this).parent().parent().find('[data-role="image-item"]:last');
    var card_blank = '<?php echo $g['img_layout']?>/card-blank.png';
    $(clone_obj).clone().appendTo(this_wrapper);
    $(this).remove();
    $(card_more).appendTo(this_wrapper);
    setTimeout(function(){
        $(clone_obj).find('[data-role="cardImg-wrapper"]').css("background","url("+card_blank+") no-repeat center center");
    },10);    

 });

// 메뉴 추가하기 이벤트 
$(document).on('click','[data-role="add-menu"]',function(){
    var this_wrapper = '[data-role="rMenu-wrapper"]'; 
    var menu_more = $(this);
    var clone_obj = $(this).parent().parent().find('[data-role="menu-item"]:last');
    $(clone_obj).clone().appendTo(this_wrapper);
    $(this).remove();
    $(menu_more).appendTo(this_wrapper);
    setTimeout(function(){
        $(clone_obj).find('input[name="menu_title[]"]').val('');
        $(clone_obj).find('input[name="menu_link[]"]').val('');
    },10);    

 });

// 노출/숨김 활성화  
$('[data-role="btn-showHide"]').on("click", function() {
    var pDiv = $(this).parent();
    var target = $(this).data('target');
    var notify_container = $(this).data('container');
    var target_array = {"text":"T","card":"C","image":"I","menu":"M"};
    var msg;

    if($(pDiv).hasClass("botSwitch-off")) {
        $(pDiv).removeClass("botSwitch-off");
        msg ='출력 처리되었습니다.';
        $(notify_container).find('.showHide-textOn').addClass('active');
        $(notify_container).find('.showHide-textOff').removeClass('active');
        $('[data-role="showType-'+target+'"]').val(target_array[target]);

    }else{
        $(pDiv).addClass("botSwitch-off");
        msg ='숨김 처리되었습니다.';
        $(notify_container).find('.showHide-textOn').removeClass('active');
        $(notify_container).find('.showHide-textOff').addClass('active');
        $('[data-role="showType-'+target+'"]').val('');
    }
    var message = msg;

    show__Notify(notify_container,message); // 메세지 출력  
});

// 추가질문/답변 추가 이벤트 
$('[data-role="add-vqa"]').on('click',function(){
     var clone_obj = $('[data-role="vqa-item"]:last');
     $(clone_obj).clone().appendTo('#vqa-rows');
     setTimeout(function(){
         $(document).find('[data-role="vqa-item"]:last').find('input[name="vqa_reply[]"]').val('');
         $(document).find('[data-role="vqa-item"]:last').find('input[name="vqa_question[]"]').val('');
     },10);    

 });
</script>
