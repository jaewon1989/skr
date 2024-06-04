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

$R = getDbData($table[$m.'reply'],"vendor='".$V['uid']."' and bot='".$bot."' and quesCat='".$quesCat."'",'uid'); 
$uid = $R['uid'];
if($uid){
    $RST = $chatbot->getReplyShowType($uid); // 답변 출력타입값 
    $rc_data = array();
    $rc_data['uid'] = $uid; // 답변 uid 
    $RMD = $chatbot->getReplyMultiData($rc_data);
}

$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;
$data['quesCat'] = $quesCat;

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
                        <?php $_WHERE2='vendor='.$V['uid'].' and type=1 and uid<>5';?>
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
                            <h4 class="panel-title"><code>질문의도</code> 분류</h4>
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
                        <code><?php echo $B['service']?></code> > <span class="main-color"><?php echo $CINFO['name']?></span> <span class="text-muted">에 대한 답변을 설정해주세요</span>
                    </h3>
                    <div class="btn-save" data-role="btn-send">저장하기</div>
                </div>
                <form class="form-horizontal rb-form" name="regisReplyForm" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="r" value="<?php echo $r?>">
                    <input type="hidden" name="m" value="<?php echo $m?>">
                    <input type="hidden" name="c" value="<?php echo $c?>">
                    <input type="hidden" name="page" value="<?php echo $page?>">
                    <input type="hidden" name="cat" value="<?php echo $cat?>">
                    <input type="hidden" name="a" value="set_reply">
                    <input type="hidden" name="quesCat" value="<?php echo $quesCat?>" />
                    <input type="hidden" name="bot" value="<?php echo $bot?>" />
                    <input type="hidden" name="uid" value="<?php echo $uid?>" /> <!-- chatbot_reply 테이블 uid -->

                    <div class="form-group">
                        <label class="col-md-9"><code class="label-fa"><i class="fa fa-text-width"></i></code>텍스트 타입</label>
                        <div class="col-md-3 select-show" id="showHide-wrapper-text">
                            <input type="hidden" name="showType[]" value="<?php echo $RST['show_text']?'T':''?>" data-role="showType-text">
                            <span class="showHide-label"><span class="showHide-textOn<?php echo $RST['show_text']?' active':''?>">ON</span>/<span class="showHide-textOff<?php echo $RST['show_text']?'':' active'?>">OFF</span></span>  
                            <div class="cb-cell cb-cell-right<?php echo $RST['show_text']?'':' botSwitch-off'?>">
                                <div class="cb-switch" data-role="btn-showHide" data-target="text" data-container="#showHide-wrapper-text">
                                    <span class="cb-switch-button"></span>
                                 </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fg-content">
                                <?php 
                                       $data['showType'] ='T';
                                       $replyMultiData = $chatbot->getReplyMultiData($data);
                                       echo $replyMultiData['html'];
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="label-box">
                            <label class="col-md-9"><code class="label-fa"><i class="fa fa-bars"></i></code>메뉴 타입</label>
                            <div class="col-md-3 select-show" id="showHide-wrapper-menu">
                                <input type="hidden" name="showType[]" value="<?php echo $RST['show_menu']?'M':''?>" data-role="showType-menu">
                                <span class="showHide-label"><span class="showHide-textOn<?php echo $RST['show_menu']?' active':''?>">ON</span>/<span class="showHide-textOff<?php echo $RST['show_menu']?'':' active'?>">OFF</span></span>  
                                <div class="cb-cell cb-cell-right<?php echo $RST['show_menu']?'':' botSwitch-off'?>">
                                    <div class="cb-switch" data-role="btn-showHide" data-target="menu" data-container="#showHide-wrapper-menu">
                                        <span class="cb-switch-button"></span>
                                     </div>
                                </div>
                            </div>
                        </div>    
                        <div class="col-md-12 scroll-group">
                            <div class="fg-content" data-role="rMenu-wrapper">
                                <?php 
                                       $data['showType'] ='M';
                                       $replyMultiData = $chatbot->getReplyMultiData($data);
                                       echo $replyMultiData['html'];
                                ?>

                                <div class="card" data-role="add-menu">
                                    <div class="menu-img-more"></div>
                                </div>
                            </div>    
                        </div>    
                    </div>           
                </form>
                <?php else:?>
                <h3 class="text-muted">좌측 <code>질문의도 분류</code> 항목과 우상단 <code>캠퍼스</code>를 선택해주세요. </h3>
                <?php endif?>
            </div>
        </div> 
    </div>
</section>
<script>
$(document).on('change','select[name="menu_linkTarget[]"]',function(){
    var img_path = '/files/reply_image/';
    var link_target = $(this).val();
    var link_ta = $(this).parent().parent().find('textarea[name="menu_link[]"]'); 
    if(link_target =='I') $(link_ta).val(img_path);
    else $(link_ta).val('');
});

// 저장 버튼
$('[data-role="btn-send"]').on('click',function(){
    var f = document.regisReplyForm;
    var form_error = 0;
    var showType=$('input[name="showType[]"]').map(function(){return $(this).val()}).get();
    var showText = false;
    var showMenu = false;
    var showCard = false;
    var showImg = false;
    var is_showType =0;
    for(var i=0;i<showType.length;i++) 
    {  
       if(showType[i]=='T'){
           showText=true;
           is_showType++;
       }else if(showType[i]=='M'){
           showMenu=true;
           is_showType++;
       }else if(showType[i]=='C'){
           showCard=true;
           is_showType++;
       }else if(showType[i]=='I'){
           showImg=true;
           is_showType++;
       }         
    }
    
    // 출력 타입이 있는 경우 
    if(is_showType){

        // 텍스트 타입 체크 
        if(showText){
            $(document).find('textarea[name="text_arr[]"]').each(function(){
                var text = $(this).val();
                var self = $(this); 
                if(text==''){
                    form_error++; // 메뉴링크 오류 추가 
                    alert('텍스트 답변을 입력해주세요.');
                    setTimeout(function(){
                        $(self).focus();
                    },200);
                    return false;
                }
            }); 
        }
       
        // 메뉴타입 체크 
        if(showMenu){
            var menu_title = $('input[name="menu_title[]"]').map(function(){return $(this).val()}).get();
            var menu_link = $('textarea[name="menu_link[]"]').map(function(){return $(this).val()}).get();
            var menu_linkTarget = $('select[name="menu_linkTarget[]"]').map(function(){return $(this).val()}).get();
            var menu_title_error = 0;
            var menu_link_error = 0;
            var menu_linkTarget_error = 0;
       
            $(document).find('input[name="menu_title[]"]').each(function(){
                var title = $(this).val();
                var self = $(this); 
                if(title==''){
                    form_error++; // 메뉴명 오류 추가 
                    alert('메뉴명을 입력해주세요');
                    setTimeout(function(){
                        $(self).focus();

                    },200);
                    return false;
                }
            });
            
            // 메뉴명이 모두 입력되었을때
            if(!form_error){
                $(document).find('textarea[name="menu_link[]"]').each(function(){
                    var link = $(this).val();
                    var self = $(this); 
                    if(link==''){
                        form_error++; // 메뉴링크 오류 추가 
                        alert('메뉴링크를 입력해주세요');
                        setTimeout(function(){
                            $(self).focus();
                        },200);
                        return false;
                    }
                });
            }
            if(!form_error){
                $(document).find('select[name="menu_linkTarget[]"]').each(function(){
                    var linkTarget = $(this).val();
                    var self = $(this); 
                    if(linkTarget==''){
                        form_error++; // 메뉴 링크대상 오류 추가 
                        alert('메뉴링크 대상을 모두 선택해주세요');
                        return false;
                    }
                });
            } 
          
        }
        if(!form_error){
           getIframeForAction(f);
           f.submit();    
        }
        
    }else{
        alert('답변 타입을 1개 이상 선택해주세요');
        return false;
    }  
    
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
