<?php
$vendor = $V['uid'];
$RFB = $chatbot->getFirstBotData($vendor,1); // 첫번째 bot 추출 
$bot = $botuid?$botuid:$RFB['uid'];
// 스펨방지 코드 
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];

?>
<input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$m?>/" />
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<input type="hidden" name="now_depth" value="1" />
<input type="hidden" name="now_uid" value="0" />
<section class="clearfix">
    <form name="procForm" action="<?php echo $g['s']?>/" method="get">
        <input type="hidden" name="r" value="<?php echo $r?>" />
        <input type="hidden" name="m" value="<?php echo $m?>" />
        <input type="hidden" name="c" value="<?php echo $c?>" />
        <input type="hidden" name="page" value="<?php echo $page?>" />
        
        <div class="cb-chatbot-factory-wrapper" style="float:left;margin-left:40px;margin-top:20px;">
            <h1>메뉴 관리</h1> 
        </div>
        <?php $_WHERE2='vendor='.$V['uid'].' and type=1 and uid<>5';?>
        <?php $BCD = getDbArray($table[$m.'bot'],$_WHERE2,'*','gid','desc','',1);?>
        <div class="cb-viewchat-search-timebox" style="float:right;width:30%;margin-top:20px;margin-right:3.5%">
            <select name="botuid" style="font-size:inherit;" onchange="this.form.submit();">
                <?php $i=1;while($B=db_fetch_array($BCD)):?>
                <option value="<?php echo $B['uid']?>" <?php if($botuid==$B['uid']):?>selected<?php endif?>>
                    <?php echo $B['service']?>
                </option>
                <?php $i++;endwhile?> 
            </select>
        </div>
    </form>
</section>
<section id="keywordBox-wrapper">
	<?php for($i=1;$i<5;$i++):?>
	<div class="keywordBox-inner">
		<div class="keyword-Box<?php echo $i==4?' last-box':''?>">
			<h5 class="keyword-title"><?php echo $i?>차 메뉴</h5>
			<div class="keywordList-wrapper" data-role="keywordListWrapper-<?php echo $i?>">
				<ul class="keyword-list dd nestable-menu" data-role="keywordBox-<?php echo $i?>" data-depth="<?php echo $i?>">
					<?php if($i==1):?>
					<?php echo $chatbot->getKeywordList($vendor,$bot,1,''); // vendor, bot, depth?>
				    <?php endif?>
				</ul>
			</div>	    
		</div>
		<div class="addBtn-wrapper">
		   	<div class="cb-button cb-btn-addKeyword" data-role="btn-addKeyword" data-depth="<?php echo $i?>">+ <?php echo $i?>차 메뉴 추가하기</div>		
		</div>
    </div>

    <?php endfor?>
</section>

<!-- 엔터티 추가 모달-->
<div id="modal-addKeyword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="addKeywordModal-title"></h4>
            </div>
            <div class="modal-body" data-role="content">
            	<input class="form-control" placeholder="콤마(,)로 구분해서 입력하면 동시에 여러개 메뉴를 등록할 수 있습니다." type="text" name="keywords" >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-role="btn-saveKeyword" data-depth="">저장하기</button> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 엔터티 상세정보 수정 -->
<div id="modal-updateKeyword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" data-role="updateKeywordModal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class="col-md-3 control-label">메뉴출력 여부</label>
                        <div class="col-md-9">
                            <div>
                                <label style="margin-right:100px;">
                                    <input type="radio" data-role="showMenu-radio" name="showMenu" value="1" /> 출력함
                                </label>
                                <label>
                                    <input type="radio" data-role="hideMenu-radio" name="showMenu" value="0" /> 출력안함
                                </label>
                            </div> 
                            <p class="help-block mute" style="color:#dc3545;">
                                <small>(<strong>출력안함</strong>으로 설정하면 문장입력시에만 검색됩니다.)</small>
                            </p>                             
                        </div>

                    </div>
     <!--                <div class="form-group">
                        <label class="col-md-3 control-label">답변타입</label>
                        <div class="col-md-9">
                            <div>
                                <select name="replyType" class="form-control">
                                   <option value=""> 선택 </option>
                                   <option value="01"> 텍스트</option>
                                   <option value="02"> 링크 </option>
                                   <option value="03"> 이미지 </option>
                                   <option value="04"> 전화 </option>
                                </select>
                            </div> 
                            <p class="help-block mute" style="color:#dc3545;">
                                <small>(<strong>출력안함</strong>으로 설정하면 문장입력시에만 검색됩니다.)</small>
                            </p>                             
                        </div>
                    </div> -->
                    <div class="info1-wrapper">
                        <div class="form-group">
                            <label class="col-md-3 control-label">메뉴명</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="keyword">
                            </div>
                        </div>
                    </div>
                     <div class="info1-wrapper">
                        <div class="form-group">
                            <label class="col-md-3 control-label">메뉴 설명</label>
                            <div class="col-md-9">
                                <textarea class="form-control ta-content" row="4" name="content"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">텍스트 답변</label>
                        <div class="col-md-9">
                            <textarea class="form-control ta-content" row="4" name="summary"></textarea>
                        </div>
                    </div>
                    <div class="info3-wrapper">
                        <div class="form-group">
                            <label class="col-md-3 control-label">PC URL</label>
                            <div class="col-md-9">
                                <textarea class="form-control ta-link" row="2" name="link1"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">모바일 URL</label>
                            <div class="col-md-9">
                                <textarea class="form-control ta-link" row="2" name="link2"></textarea>
                            </div>
                        </div>
                       <!--  <div class="form-group">
                            <label class="col-md-3 control-label">구매하기 URL</label>
                            <div class="col-md-9">
                                <textarea class="form-control" row="2" name="link2"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">할인가</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="price1">
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">정가</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="price2">
                                    <span class="input-group-addon">원</span>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="info2-wrapper">    
                        <div class="form-group">
                            <label class="col-md-3 control-label">대표 이미지</label>
                            <div class="col-md-9">
                                <div class="media">
                                    <div class="media-left" style="width: 25% !important;">
                                        <div class="cb-chatbot-form-profileholder">
                                            <span style="display:none;"><input type="file" name="file" id="fimg-inputfile"/></span>
                                            <div id="preview-fimg">
                                                <span class="cb-icon cb-icon-camera" id="getFimgPhoto"></span>
                                            </div>
                                            <p class="help-block mute"><small>(업로드시 카메라를 클릭)</small></p>
                                        </div>
                                    </div>
                                    <div class="media-body media-right">
                                        <p class="imgType-wrapper" data-role="uploadImg-type">
                                            <label class="radio-inline">
                                                <input type="radio" name="img_type" data-type="upload" checked> 업로드
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="img_type" data-type="insert"> URL 입력 
                                            </label>
                                        </p>
                                        <p data-role="insertImg-wrapper" style="display:none;">
                                            <textarea class="form-control" row="5" name="img_url" style="height:125px;"></textarea>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" data-role="cancel-edit" >취소하기</button>
                <button type="button" class="btn btn-primary" data-role="btn-updateKeyword" data-depth="">저장하기</button>
            </div>
        </div>
    </div>
</div>
<?php getImport('nestable','jquery.nestable',false,'js') ?>
<script>
var vendor = '<?php echo $vendor?>';
var bot = '<?php echo $bot?>';
var module ='<?php echo $m?>';
var addModal = $('#modal-addKeyword');
var updateModal = $('#modal-updateKeyword');
var keywords_ele = $(addModal).find('input[name="keywords"]');

// nestable 초기화 
var init_nestable =function(){
	$('.nestable-menu').nestable();
}

// tooltip 초기화 
var init_tooltip =function(){
    $('body').tooltip({
		selector: '[data-tooltip=tooltip]',
		html: 'true',
		container: 'body'
	});	
}

// 메세지 출력 함수 
var print__Notify=function(container,message){
    $.notify({
        // options
        message: message 
    },{
        // settings
        element: container,  
        type: 'black',
        placement:{
            from: 'bottom',
            align: "center"
        },
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        z_index: 1031,
        offset : -50,
        timer: 500,
        delay : 300
    });
}

// 메뉴순서 저장 
var saveGid = function(){
	for(var i=1;i<5;i++){
		var keywordBox = $('[data-role="keywordBox-'+i+'"]'); 
		var keyword_member = $(keywordBox).find('input[name="keyword_member[]"]').map(function(){return $(this).val()}).get();
	    sessionStorage.setItem("keyword_member_"+i,keyword_member);
	}
	
}

// 기존에 등록된 메뉴명 추출 
var get_keywordName_array = function(depth){
    var keywordBox = $('[data-role="keywordBox-'+depth+'"]');
    var arr = [];
    $(keywordBox).find('[data-role="keyword-item"]').each(function(){
            arr.push($(this).text());
    });

    return arr;
} 

// 현재 선택된 메뉴 정보 추출 
var get_Selected_Menu = function(depth,data){
    var sel_name ='';
    var sel_uid = '';
    var result;
    var keywordBox = $('[data-role="keywordBox-'+depth+'"]');
    $(keywordBox).find('[data-role="keyword-item"]').each(function(){
         if($(this).hasClass('active')){
             sel_name = $(this).text();
             sel_uid = $(this).attr('data-uid'); 
         } 
    });

    if(data=='name') result = sel_name;
    else if(data=='uid') result = sel_uid;

    return result;
}

// 모달 타이틀 추출 
var get_Modal_title = function(depth){
    var dynamic_title,static_title;
    var depth_1_name = get_Selected_Menu(1,'name');
    var depth_2_name = get_Selected_Menu(2,'name');
    var depth_3_name = get_Selected_Menu(3,'name');

    var static_title = '추가하기';
    if(depth==1) dynamic_title = '1차메뉴';
    else if(depth==2) dynamic_title = depth_1_name+' > 하위메뉴';
    else if(depth==3) dynamic_title = depth_1_name+' > '+depth_2_name+' > 하위메뉴';
    else if(depth==4) dynamic_title = depth_1_name+' > '+depth_2_name+' > '+depth_3_name+' > 하위메뉴';    
    
    return dynamic_title+static_title;
}

// 동일한 메뉴명 체크  
var has_sameName = function(depth,keywords){
    var arr = keywords.split(',');
    var arr_cnt = arr.length;
    var arr_unique_cnt = $.unique(arr).length;
    var keywordName_arr = get_keywordName_array(depth);
    var result=false;
    if(arr_cnt!=arr_unique_cnt) result = 'same_input';
    
    $.each(arr,function(i,val){
         if($.inArray(val,keywordName_arr)!='-1') result ='alreay_registed';
    });
    return result;
}

// 전체 스크립트 초기화 
var init_script = function(){
    init_nestable();
    init_tooltip();
    saveGid();
    $('body').find('[role="tooltip"]').remove();
}

// 리스트 업데이트 함수 (add, edit, delete, change gid.. )
var updateKWD = function(data){
    var depth = data.depth?data.depth:'';
    var parent = data.parent?data.parent:'';
    var uid = data.uid?data.uid:'';
    var act = data.act?data.act:'';
    var keywords = data.keywords?data.keywords:'';
    var keyword_member = data.keyword_member?data.keyword_member:'';
    var check_active = data.check_active?data.check_active:'';
    //업데이트정보 
    var keyword = data.keyword?data.keyword:'';
    var link1 = data.link1?data.link1:'';
    var link2 = data.link2?data.link2:'';
    var price1 = data.price1?data.price1:'';
    var price2 = data.price2?data.price2:'';
    var summary = data.summary?data.summary:'';
    var content = data.content?data.content:'';
    var img_url = data.img_url?data.img_url:'';
    var showMenu = data.showMenu?data.showMenu:0;
    var box_depth;
    if(act=='get-subMenu') box_depth = parseInt(depth)+1;
    else box_depth = depth;
    var keywordBox = $('[data-role="keywordBox-'+box_depth+'"]');
    var keywordListWrapper = $('[data-role="keywordListWrapper-'+box_depth+'"]');
  
    if(bot==''){
        alert('챗봇을 먼저 선택해주세요.');
        return false; 
    }

	$.post(rooturl+'/?r='+raccount+'&m='+module+'&a=manage_keyword',{
		vendor : vendor,
		bot : bot,
        depth : depth,
        parent : parent,
        keywords : keywords,
        keyword_member : keyword_member,
        uid : uid,
        keyword : keyword,
        link1 : link1,
        link2 : link2,
        price1 : price1,
        price2 : price2,
        summary : summary,
        content : content,
        img_url : img_url,
        showMenu : showMenu,
        act : act
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var content = result.content;
        var message = result.message;
        if(act=='delete-keyword'){
            // 선택된 메뉴가 삭제될 경우 
            if(check_active){
                if(depth==1){
                    $('[data-role="keywordBox-2"]').html('');
                    $('[data-role="keywordBox-3"]').html('');
                }else if(depth==2) $('[data-role="keywordBox-3"]').html('');
            }
        }else if(act=='edit-gid'){
        	print__Notify(keywordBox,message);
        }else if(act=='change-showMenu'){
            $(keywordBox).html(content);
            setTimeout(function(){
                print__Notify(keywordBox,message);
            },100);
        }
        else $(keywordBox).html(content);
        
        // 신규 저장 및 업데이트인 경우  
        if(act=='save-keyword'||act=='update-keyword'){
        	if(act=='save-keyword') $(addModal).modal('hide');
            else if(act=='update-keyword') $(updateModal).modal('hide');
            // 모든 input 값 초기화 
            $(addModal).find("input").val('');
            $(updateModal).find('input[type="text"]').val('');
            $(updateModal).find("textarea").val('');
        }else if(act=='get-subMenu'){
        	if(depth==1){
        		console.log(depth);
        		// 1차메뉴 선택시 3차메뉴
        		$('[data-role="keywordBox-3"]').html('');
                $('[data-role="keywordBox-4"]').html('');
        	}else if(depth==2){
                $('[data-role="keywordBox-4"]').html('');
            }
        }
        // 관련 스크립트 초기화 
        init_script();
    }); 
}

// 엔터티 클릭 이벤트 
$(document).on('click','[data-role="keyword-item"]',function(){
	var depth = $(this).data("depth");
	var uid = $(this).data("uid");
	var keywordBox =$('[data-role="keywordBox-'+depth+'"]');
	var kwd_name = $(keywordBox).find('.kwd-name');
    var kwd_handle = $('.dd-handle');
    var this_name = $(this).text();
  
    // 전체 item css  
	$(kwd_name).removeClass("active");
	$(kwd_name).parent().find(kwd_handle).removeClass('active');
    
    // this item css 
    $(this).addClass("active");
    $(this).parent().find(kwd_handle).addClass('active');

    // 서브 메뉴 출력 
    var data = {
		"vendor" : vendor,
		"bot" : bot,
		depth : depth,
		parent : uid,
		act : 'get-subMenu'
	} 
    updateKWD(data);

});

// 상세정보 버튼 클릭 
$(document).on('click','[data-role="edit-kwd"]',function(){
    var depth = $(this).data('depth')
    var keyword = $(this).data('keyword');
    var uid = $(this).data('uid');
    var parent = $(this).data('parent');
    var link1 = $(this).data('link1');
    var link2 = $(this).data('link2');
    var price1 = $(this).data('price1');
    var price2 = $(this).data('price2');
    var summary = $(this).data('summary');
    var img_url = $(this).data('imgurl');
    var showMenu = $(this).data('showmenu');
    var content = $(this).data('content');
    var modal_title = keyword+' 상세정보';
    var btn_update = $(updateModal).find('[data-role="btn-updateKeyword"]'); 
    // 트리거값 모달에 적용 
    $(updateModal).find('input[name="keyword"]').val(keyword); 
    $(updateModal).find('textarea[name="link1"]').val(link1);
    $(updateModal).find('textarea[name="link2"]').val(link2);
    $(updateModal).find('input[name="price1"]').val(price1);
    $(updateModal).find('input[name="price2"]').val(price2);
    $(updateModal).find('textarea[name="summary"]').val(summary);
    $(updateModal).find('textarea[name="content"]').val(content);
    $(updateModal).find('textarea[name="img_url"]').val(img_url);
    $(updateModal).find('[data-role="updateKeywordModal-title"]').text(modal_title); // 모달 타이틀 지정 
    console.log(showMenu);

    // 메뉴출력 여부값 적용 
    if(showMenu=='1'){
        $(updateModal).find('[data-role="showMenu-radio"]').prop("checked",true); 
        $(updateModal).find('[data-role="hideMenu-radio"]').prop("checked",false); 
    }else{
        $(updateModal).find('[data-role="hideMenu-radio"]').prop("checked",true);
        $(updateModal).find('[data-role="showMenu-radio"]').prop("checked",false);  
    } 

    // 이미지 적용 
    if(img_url!=''){
       $(updateModal).find('#preview-fimg').css({"background":"url('"+img_url+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"150px 150px"}); 
    }  

    // 업데이트 버튼에 값 적용       
    $(btn_update).attr("data-depth",depth);
    $(btn_update).attr("data-uid",uid);
    $(btn_update).attr("data-keyword",keyword);
    $(btn_update).attr("data-parent",parent);
    $(updateModal).modal();
});


// 추가 버튼 클릭 
$(document).on('click','[data-role="btn-addKeyword"]',function(){
	var depth = $(this).data('depth')
	var parent_depth = parseInt(depth)-1; // 상위 depth 
    var modal_title = get_Modal_title(depth);
    var selected_parent_menu = get_Selected_Menu(parent_depth,'name');
    if(depth !=1){
	    if(selected_parent_menu==''){
		   alert('상위 메뉴를 선택해주세요.');
	       return false;	
		}		
	}
	$(addModal).find('[data-role="btn-saveKeyword"]').attr("data-depth",depth); // 저장버튼에 depth 추가 
    $(addModal).find('[data-role="addKeywordModal-title"]').text(modal_title); // 모달 타이틀 지정 
	$(addModal).modal();

});

// 저장 버튼 클릭  
$(document).on('click','[data-role="btn-saveKeyword"]',function(){
	var depth = $(this).attr("data-depth");
    var parent_depth = parseInt(depth)-1;
	var parent = get_Selected_Menu(parent_depth,'uid'); // parent uid 추출 
	var keywords = $(keywords_ele).val();
	var has_same_name = has_sameName(depth,keywords);
    var data = {
		"vendor": vendor,
		"bot": bot,
		depth: depth,
		parent: parent,
		keywords: keywords,
		act : 'save-keyword'
	}
	if(keywords==''){
		alert('메뉴명을 입력해주세요.');
        setTimeout(function(){
           $(keywords_ele).focus();
        });
		return false;
	}else{
        if(has_same_name=='same_input'){
            alert('입력된 값에 동일한 메뉴명이 중복되어 있습니다.');
            setTimeout(function(){
               $(keywords_ele).focus();
            });
            return false;
        }else if(has_same_name=='alreay_registed' ){
            alert('입력된 값에 이미 등록된 메뉴명이 존재합니다.');
            setTimeout(function(){
               $(keywords_ele).focus();
            });
            return false;
        }
    } 
	updateKWD(data);
	
});

// 상세정보 저장 버튼 클릭  
$(document).on('click','[data-role="btn-updateKeyword"]',function(){
    var depth = $(this).attr("data-depth");
    var uid = $(this).attr("data-uid");
    var old_keyword = $(this).attr('data-keyword'); // 기존 엔터티
    var parent = $(this).attr('data-parent'); // 
    var keyword_ele = $(updateModal).find('input[name="keyword"]'); 
    var link1_ele = $(updateModal).find('textarea[name="link1"]');
    var link2_ele = $(updateModal).find('textarea[name="link2"]');
    var price1_ele = $(updateModal).find('input[name="price1"]');
    var price2_ele = $(updateModal).find('input[name="price2"]');
    var summary_ele = $(updateModal).find('textarea[name="summary"]');
    var content_ele = $(updateModal).find('textarea[name="content"]');
    var imgUrl_ele = $(updateModal).find('textarea[name="img_url"]');
    var showMenu = $(updateModal).find('input[name="showMenu"]:checked');
    var keyword = $(keyword_ele).val();
    var link1 = link1_ele?$(link1_ele).val():'';
    var link2 = link2_ele?$(link2_ele).val():'';
    var price1 = price1_ele?$(price1_ele).val():'';
    var price2 = price2_ele?$(price2_ele).val():'';
    var summary = summary_ele?$(summary_ele).val():'';
    var content = content_ele?$(content_ele).val():'';
    var imgUrl = imgUrl_ele?$(imgUrl_ele).val():'';
    var showMenu = $(showMenu).val();
    var has_same_name;
    if(keyword!=old_keyword) has_same_name = has_sameName(depth,keyword);
    var data = {
        "vendor": vendor,
        "bot": bot,
        uid : uid,
        depth: depth,
        parent : parent,
        keyword: keyword,
        link1 : link1,
        link2 : link2,
        price1 : price1,
        price2 : price2,
        summary : summary,
        content : content,
        img_url : imgUrl,
        showMenu : showMenu,
        act : 'update-keyword'
    }
    if(keyword==''){
        alert('메뉴명을 입력해주세요.');
        setTimeout(function(){
           $(keyword_ele).focus();
        });
        return false;
    }else{
        if(has_same_name=='alreay_registed' ){
            alert('입력된 값에 이미 등록된 메뉴명이 존재합니다.');
            setTimeout(function(){
               $(keyword_ele).focus();
            });
            return false;
        }
    } 
    updateKWD(data);
    
});


// 삭제 버튼 클릭  
$(document).on('click','[data-role="delete-kwd"]',function(){
	var uid = $(this).data('uid');
	var depth = $(this).data('depth');
	var parent = $(this).data('parent');
	var keyword_item = $(this).parent();
    var check_active = $(this).parent().find('[data-role="keyword-item"]').hasClass('active');
	var data = {
		"vendor": vendor,
		"bot": bot,
		uid: uid,
		depth: depth,
		parent: parent,
        check_active: check_active,
		act : 'delete-keyword'
	}
	if(depth<4){
		if(confirm('하위 메뉴까지 모두 삭제됩니다. 삭제하시겠습니까?    ')){
			$(keyword_item).remove();
			updateKWD(data);
		}	
	}else{
		if(confirm('정말로 삭제하시겠습니까?    ')){
			$(keyword_item).remove();
			updateKWD(data);
		}
	}
		

});

// 메뉴 숨김/노출 아이콘(눈) 클릭  
$(document).on('click','[data-role="change-showMenu"]',function(){
    var uid = $(this).data('uid');
    var depth = $(this).data('depth');
    var parent = $(this).data('parent');
    var keyword_item = $(this).parent();
    var check_active = $(this).parent().find('[data-role="keyword-item"]').hasClass('active');
    var showMenu = $(this).attr('data-showMenu');
    var data = {
        "vendor": vendor,
        "bot": bot,
        uid: uid,
        depth: depth,
        parent: parent,
        check_active: check_active,
        showMenu: showMenu,
        act : 'change-showMenu'
    }
    updateKWD(data);    

});

// 순서변경 이벤트 
$('.nestable-menu').on('change', function(e) {
    var target = e.currentTarget;
    var depth = $(target).data('depth');
    var keyword_member = $(target).find('input[name="keyword_member[]"]').map(function(){return $(this).val()}).get();
    var old_keyword_member = sessionStorage.getItem("keyword_member_"+depth);
    if(old_keyword_member !=null && keyword_member!=old_keyword_member){
	    var data = {
			"vendor" : vendor,
			"bot" : bot,
			 depth : depth,
			 keyword_member : keyword_member,
			 act : 'edit-gid'
		} 
		sessionStorage.setItem("keyword_member_"+depth,keyword_member);
	    updateKWD(data);	
    }
    
});

// 추가 모달 오픈 후 포커싱 
$(addModal).on('shown.bs.modal',function(e){
    var modal = e.currentTarget;
    setTimeout(function(){
       $(modal).find(keywords_ele).focus();	
    },10);
     
});

// 상세정보 수정 모달 close 시 모든 입력값 초기화 
$(updateModal).on('hide.bs.modal',function(e){
    var target = e.currentTarget;
    $(target).find('input[type="text"]').val('');
    $(target).find("textarea").val('');
    $(target).find('#preview-fimg').css("background","#c6c6c6");
});

// 포토 등록 
$(function(){

    // 로고 사진 클릭 
    $(document).on('click','#getFimgPhoto',function(){
        $('#fimg-inputfile').click();  
    });

    // 로고 업로드 및 미리보기 
    $(document).on('change','#fimg-inputfile',function(e){
        var file=$(this)[0].files[0];
        var saveDir=$('input[name="saveDir"]').val();
        data = new FormData();
        data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
        data.append("saveDir",saveDir);
        data.append("sescode","<?php echo $sescode?>");
        data.append("item","avatar");
        $.ajax({
            type: "POST",
            url : rooturl+'/?r=<?php echo $r?>&m=<?php echo $m?>&a=upload_keywordImg',
            data:data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(result) {
                var val = $.parseJSON(result);
                var code=val[0];
                if(code=='100') // code 값이 100 일때만 실행 
                {
                     var source=val[1];// path + tempname
                     var upuid=val[2]; // upload 테이블 저장 uid
                     $('textarea[name="img_url"]').val(source);
                     $('#preview-fimg').css({"background":"url('"+source+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"150px 150px"});

                } // success
            }
        }); // ajax   
    });       
 
});

// 이미지 업로드 방식 변경시 
$(document).on('click','[data-role="uploadImg-type"] input',function(){
  var type = $(this).data('type');
  var insertImg_wrapper = $(updateModal).find('[data-role="insertImg-wrapper"]'); 
  if(type=='insert') $(insertImg_wrapper).show();
  else $(insertImg_wrapper).hide(); 
 
});

// 이미지 업로드 URL 입력시 
$(document).on('blur','textarea[name="img_url"]',function(){
  var source = $(this).val();
  if(source!=''){
     $(updateModal).find('#preview-fimg').css({"background":"url('"+source+"')", "background-repeat":"no-repeat", "background-position":"center center","background-size":"150px 150px"});
  }
  
});

$(document).ready(function(){
    init_script();
});
</script>