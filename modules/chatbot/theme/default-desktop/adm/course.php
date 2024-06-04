<?php 
include $g['path_module'].$m.'/includes/tree.func.php';
$base_url = $g['chatbot_page'];
if($cat)
{
    $CINFO = getUidData($table[$m.'course'],$cat);
    $ctarr = getMenuCodeToPathBlog($table[$m.'course'],$cat,0);
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

$catcode = '';
$is_fcategory =  $CINFO['uid'] && $vtype != 'sub';
$is_regismode = !$CINFO['uid'] || $vtype == 'sub';
if ($is_regismode)
{
    $CINFO['name']     = '';
    $CINFO['mobile']   = '';
    $CINFO['hidden']   = '';
    $CINFO['metaurl']   = '';
    $CINFO['metause']   = '';
    $CINFO['recnum'] = 20;
}
$now_depth = $CINFO['depth'];
$cat_name = $now_depth==1?'과정':'과정';
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
                       <h1>과정 관리</h1> 
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
                            <h4 class="panel-title">과정 분류</h4>
                        </div>
                        <div class="panel-collapse collapse in" id="collapmetane">                                
                            <div class="panel-body">
                                <div style="height:400px;overflow:auto;">
                                    <link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
                                    <?php $_treeOptions=array('table'=>$table[$m.'course'],'dispNum'=>true,'dispHidden'=>false,'dispCheckbox'=>false,'allOpen'=>false,'bookmark'=>false)?>
                                    <?php $_treeOptions['link'] = $base_url.'&amp;cat='?>
                                    <?php $_treeOptions['add_where'] = "type=1"?> 
                                    <?php echo getTreeCategory($_treeOptions,$code,0,0,'')?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="catinfo" class="col-sm-8 col-md-9 col-lg-9">
                <div class="content-header" style="padding:0;border-bottom:0;">
                    <h3>
                        <?php if($is_regismode):?>
                            <div class="breadcrumb" style="padding:10px 15px">
                              과정 등록하기
                            </div>
                        <?php else:?>
                            <?php if($vtype == 'sub'):?>
                                <ol class="breadcrumb" style="padding:10px 15px">                                
                                <?php for ($i = 0; $i < $ctnum; $i++):?>
                                   <li><a href="<?php echo $base_url?>&amp;cat=<?php echo $ctarr[$i]['uid']?>"><?php echo $ctarr[$i]['name']?></a></li>
                                <?php $catcode .= $ctarr[$i]['id'].'/';endfor?>
                                </ol>
                             <?php else:?>
                                    <?php if($cat):?>
                                       <ol class="breadcrumb" style="padding:10px 15px">
                                           <?php for ($i = 0; $i < $ctnum-1; $i++):?>
                                            <li><a href="<?php echo $base_url?>&amp;cat=<?php echo $ctarr[$i]['uid']?>"><?php echo $ctarr[$i]['name']?></a></li>
                                            <?php $delparent=$ctarr[$i]['uid'];$catcode .= $ctarr[$i]['id'].'/';endfor?>
                                            <?php if(!$delparent):?>과정 등록정보<?php endif?>
                                        </ol>
                                    <?php endif?>
                            <?php endif?>
                        <?php endif?>
                    </h3>
                    <div class="btn-save" style="bottom:0 !important;" data-role="btn-send">저장하기</div>
                </div>
                <form class="form-horizontal rb-form" name="regisCourseForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
                    <input type="hidden" name="r" value="<?php echo $r?>">
                    <input type="hidden" name="m" value="<?php echo $m?>">
                    <input type="hidden" name="c" value="<?php echo $c?>">
                    <input type="hidden" name="a" value="regis_course">
                    <input type="hidden" name="cat" value="<?php echo $CINFO['uid']?>" />
                    <input type="hidden" name="vtype" value="<?php echo $vtype?>" />
                    <input type="hidden" name="depth" value="<?php echo intval($CINFO['depth'])?>" />
                    <input type="hidden" name="parent" value="<?php echo intval($CINFO['uid'])?>" />
                    <input type="hidden" name="type" value="1" />
                    <div class="form-group rb-outside">
                        <label class="col-md-1 control-label"> 명칭 </label>
                        <div class="col-md-11">
                            <?php if($is_fcategory):?>
                            <div class="input-group">
                                <input class="form-control" type="text" name="name" value="<?php echo $CINFO['name']?>">
                                <span class="input-group-btn">
                                    <?php if($now_depth<2):?>
                                    <!-- <a href="<?php echo $base_url?>&amp;cat=<?php echo $cat?>&amp;vtype=sub" class="btn btn-default" data-tooltip="tooltip" title="과정 등록">
                                        <i class="fa fa-share fa-rotate-90 fa-lg"></i>
                                    </a> -->
                                   <?php endif?>
                                    <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;a=deleteCourse&amp;cat=<?php echo $cat?>&amp;parent=<?php echo $delparent?>" onclick="return hrefCheck(this,true,'정말로 삭제하시겠습니까?');" class="btn btn-default" data-tooltip="tooltip" title="<?php echo $cat_name?> 삭제">
                                        <i class="fa fa-trash-o fa-lg"></i>
                                    </a>
                                </span>
                            </div>
                            <?php else:?>
                            <input class="form-control" placeholder="콤마(,)로 구분해서 입력하면 동시에 여러개 메뉴를 등록할 수 있습니다." type="text" name="name" value="<?php echo $CINFO['name']?>">
                
                            <?php endif?>
                        </div>
                    </div>
                    <?php if(!$is_regismode):?>
                    <div class="form-group rb-outside">
                        <label class="col-md-1 control-label"> 소개 </label>
                        <div class="col-md-11">
                            <textarea name="content" placeholder="소개내용을 입력해주세요" class="form-control" rows="17"><?php echo $CINFO['content']?></textarea>
                        </div>
                    </div>
                    <?php endif?>
                </form> 
            </div>
        </div> 
    </div>
</section>
<script>
// tooltip 초기화 
$('body').tooltip({
    selector: '[data-tooltip=tooltip]',
    html: 'true',
    container: 'body'
}); 
$(document).on('change','select[name="menu_linkTarget[]"]',function(){
    var img_path = '/files/reply_image/';
    var link_target = $(this).val();
    var link_ta = $(this).parent().parent().find('textarea[name="menu_link[]"]'); 
    if(link_target =='I') $(link_ta).val(img_path);
    else $(link_ta).val('');
});

// 저장 버튼
$('[data-role="btn-send"]').on('click',function(){
    var f = document.regisCourseForm;
    getIframeForAction(f);
    f.submit();    
    
});

// card 추가하기 이벤트 
$(document).on('click','[data-role="add-card"]',function(){
    var this_wrapper = '[data-role="card-wrapper"]'; 
    var card_more = $(this);
    var target_obj = $(this).parent().parent().find('[data-role="card-item"]:last');
    var card_blank = '<?php echo $g['img_layout']?>/card-blank.png';
    var clone_obj = $(target_obj).clone().appendTo(this_wrapper);
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
    var target_obj = $(this).parent().parent().find('[data-role="image-item"]:last');
    var card_blank = '<?php echo $g['img_layout']?>/card-blank.png';
    var clone_obj = $(target_obj).clone().appendTo(this_wrapper);
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
    var target_obj = $(this).parent().parent().find('[data-role="menu-item"]:last');
    var clone_obj = $(target_obj).clone().appendTo(this_wrapper);
    $(this).remove();
    $(menu_more).appendTo(this_wrapper);
    setTimeout(function(){
        $(clone_obj).find('input[name="menu_title[]"]').val('');
        $(clone_obj).find('textarea[name="menu_link[]"]').val('');
        $(clone_obj).find('select[name="menu_link_target[]"]').val('');
        $(clone_obj).find('input[name="menu_uid[]"]').val('');
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
