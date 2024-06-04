<?php
$chatbot->vendor = $V['uid'];

$vendor = $V['uid'];
$bot = $bot;

$query ="Select category1 From ".$table[$m."faq"]." Where vendor=$vendor and bot=$bot and category1 <> '' group by category1";
$category1 = db_query($query,$DB_CONNECT);
?>
<style>
    .d-inline-block {display:inline-block;}
    .dropdown-menu>li>a:hover {background-color:#eee;}
    .table-search select {-webkit-appearance:auto; height:38px; width:100% !important;}
    .table-search th, .table-search td {vertical-align:middle !important; padding:5px 0 !important; background:#fff !important;}
    .table-fluid .table-container table td {padding:3px 0 !important; padding-left:25px !important; vertical-align:middle !important;}
    .table-fluid .table-container table td:last-child {padding-right:5px !important;}
    #tBody input.form-control {height:34px; padding:5px 8px;}
</style>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/rotator.js'?>"></script>
<div class="container-fluid table-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- /.row -->
    <div class="table-container">
        <div class="row table-tool" style="background:#edf1f5;">
            <form class="form-horizontal rb-form" id="frmSearch" name="frmSearch" onsubmit="return getFaqData()">
                <input type="hidden" name="vendor" value="<?=$V['uid']?>" />
                <input type="hidden" name="bot" value="<?=$bot?>" />
                <input type="hidden" name="linkType" value="search" />
                <input type="hidden" name="p" value="1" />
                <div class="white-box" style="padding:10px 0;">
                    <table class="table table-search" style="border-top:0;border-bottom:0; width:90%; margin:0 auto;">
                        <colgroup>
                            <col width="5%">
                            <col width="12%">
                            <col width="5%">
                            <col width="12%">
                            <col width="5%">
                            <col width="12%">
                            <col width="5%">
                            <col width="5%">
                            <col width="12%">
                            <col width="20%">
                            <col width="3%">
                        </colgroup>
                        <tr>
                            <th>대분류</th>
                            <td>
                                <select id="category1" name="category1" class="category form-control">
                                    <option value="">전체</option>
                                    <? while($R = db_fetch_assoc($category1)){?>
                                    <option value="<?=$R['category1']?>"><?=$R['category1']?></option>
                                    <? }?>
                                </select>
                            </td>
                            <th>중분류</th>
                            <td>
                                <select id="category2" name="category2" class="category form-control">
                                    <option value="">전체</option>
                                </select>
                            </td>
                            <th>소분류</th>
                            <td>
                                <select id="category3" name="category3" class="category form-control">
                                    <option value="">전체</option>
                                </select>
                            </td>
                            <td></td>
                            <th>검색</th>
                            <td>
                                <select id="chField" name="chField" class="form-control">
                                    <option value="question">질문</option>
                                    <option value="answer">답변</option>
                                </select>
                            </td>
                            <td colspan="4">
                                <input type="text" id="chFind" name="chFind" class="form-control" value="">
                            </td>
                            <td>
                                <button id="btn_search" type="submit" class="btn btn-default">검색</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>        
        
            <div class="col-lg-12 col-md-12 hidden-sm">
                <div style="float:left">
                    <button class="btn_faq btn btn-danger" data-mod="delete">삭제</button>                
                    <button class="btn_faq btn btn-danger" data-mod="delete_all">전체 삭제</button>
                    <button class="btn_faq btn btn-info" data-mod="add" style="margin-left:15px;">추가</button>
                    <button class="btn_faq btn btn-primary" data-mod="save">저장</button>
                </div>
                <div style="float:right">
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-default dropdown-toggle" type="button" id="faq-download-dropdown" data-toggle="dropdown" aria-expanded="false">다운로드<span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="faq-download-dropdown">
                            <li><a href="javascript:;" onclick="getFaqDown('form')">양식</a></li>
                            <li><a href="javascript:;" onclick="getFaqDown('data')">데이터</a></li>
                        </ul>
                    </div>
                    <button class="btn_faq btn btn-primary" data-mod="upload">엑셀 업로드</button>
                </div>
            </div>

            <!-- /.col-lg-12 -->
        </div>
        <div class="intEntTable-wrapper">
            <form class="form-horizontal" id="frmFaq" name="frmFaq">
                <input type="hidden" name="vendor" value="<?=$V['uid']?>" />
                <input type="hidden" name="bot" value="<?=$bot?>" />
                <input type="hidden" name="linkType" value="" />
                <input type="hidden" name="aUid" value="" />
                <div class="table-responsive table-wrapper" data-role="table-wrapper">                
                    <table class="table table-striped table-full" id="tbl-faq">
                        <colgroup>
                            <col width="5%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="30%">
                            <col width="*">
                        </colgroup>
                        <thead>
                            <tr class="table-header">
                                <th><input type="checkbox" data-role="select-all" data-parent="#tbl-faq"/></th>
                                <th>대분류</th>
                                <th>중분류</th>
                                <th>소분류</th>
                                <th>질문</th>
                                <th>답변</th>
                            </tr>
                        </thead> 
                        <tbody id="tBody"></tbody>
                    </table>
                    
                    <div class="text-center">
                        <ul id="pageLink" class="pagination pagination-sm" style="margin:10px 0 0"></ul>
                    </div>
                </div>
            </form>
        </div>         
    </div>
</div>

<form name="faqDown" id="faqDown" action="/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data"> 
    <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="vendor" value="<?php echo $V['uid']?>" />
    <input type="hidden" name="bot" value="<?php echo $bot?>" />
    <input type="hidden" name="a" value="do_FaqAction">
    <input type="hidden" name="linkType" value="down"/>
    <input type="hidden" name="mod" value=""/>
</form>

<script>    
    var html = '';
    html +='<tr>';
    html +='    <td>';
    html +='        <input type="hidden" name="uid[]" />';
    html +='        <input type="checkbox" data-role="select-all" data-uid="" />';
    html +='    </td>';
    html +='    <td><input type="text" class="form-control" name="category1[]" /></td>';
    html +='    <td><input type="text" class="form-control" name="category2[]" /></td>';
    html +='    <td><input type="text" class="form-control" name="category3[]" /></td>';
    html +='    <td><input type="text" class="form-control" name="question[]" /></td>';
    html +='    <td><input type="text" class="form-control" name="answer[]" /></td>';
    html +='</tr>';
    
    $(".category").on("change", function() {
        var level = $(this).attr("name");
        if(level == "category1") {
            $("select[name=category2]").html("<option value=''>전체</option>");
            $("select[name=category3]").html("<option value=''>전체</option>");
        }
        if(level == "category2") {
            $("select[name=category3]").html("<option value=''>전체</option>");
        }
        
        getFaqData();
    });
    
    // 페이지 링크 관련
    $(document).on("click", "ul.pagination li a", function(e) {
        e.preventDefault();
        if($(this).attr("href").indexOf("p=") > -1) {
            var _p = $(this).attr("href").replace("&p=", "");            
            $('input[name="p"]').val(_p);
            getFaqData();
        }
    });
    
    // 상단 버튼
    $(".btn_faq").on("click", function() {
        var mod = $(this).attr("data-mod");
        if(mod == "add") $("#tBody").prepend(html);
        else if(mod == "down") getFaqDown();
        else if(mod == "upload") getFaqUpload();
        else getFaqControl($(this));
    });
    
    function getFaqData() {
        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=do_FaqAction', $("#frmSearch").serialize(), 
        function(response){
            checkLogCountdown();
            var result=$.parseJSON(response);//$.parseJSON(response);
            
            $('#tBody').html(result.faqList);
            $('#pageLink').html(result.pageLink);
            
            if(result.category2) $("select[name=category2]").append(result.category2);
            if(result.category3) $("select[name=category3]").append(result.category3);
        });
        return false;
    }
    
    function getFaqDown(mod) {
        var form = $('#faqDown');
        $(form).find('input[name="mod"]').val(mod);
        $(form).submit();
    }
    
    function getFaqUpload() {
        var fileInput = $('<input/>', {
            type: 'file',
            name: 'importFile',
            id: 'importFile',
            style: 'display:none'
        });
        $(fileInput).appendTo('body').click();
    }
    
    $(document).on("change", "#importFile", function(e) {
        var target = e.currentTarget;
        var file = target.files[0];
        var data = new FormData();
        data.append("file", file);
        data.append("vendor", "<?=$V['uid']?>");
        data.append("bot", "<?=$bot?>");
        data.append("linkType", "upload");
        data.append("sescode", "<?=$sescode?>");
        
        if (!file.name.match(/\.(xls|xlsx)$/i)) {
            alert("엑셀파일만 등록가능합니다."); return false;
        }
        if (file.size > (1024*1024*2)) {
            alert("업로드 파일의 용량은 2M 이하여야 합니다."); return false;
        }
        
        $(".preloader").css("background", "transparent").show();
        $.ajax({
            type: "POST",
            url: rooturl+'/?r='+raccount+'&m=chatbot&a=do_FaqAction',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                checkLogCountdown();
                $(".preloader").hide();
                $("#importFile").remove();
                var result=$.parseJSON(response);
                if(result.error) {
                    alert(result.msg);
                } else {
                    getFaqData();
                }
            }
        });
    });
    
    function getFaqControl(obj) {
        var linkType = obj.attr("data-mod");        
        $("#frmFaq input:hidden[name=linkType]").val(linkType);
        
        if(linkType == "delete" || linkType == "delete_all") {
            if(linkType == "delete") {                
                var aUid = "";
            	$("#tBody [data-role=select-all]:checked").each(function() {
            		if (!$(this).prop("disabled")) aUid += $(this).attr("data-uid") + "|";
            	});
            	if(aUid == "") {
                    alert("삭제할 데이터를 선택해주세요."); return false;
                }
            	$("#frmFaq input:hidden[name=aUid]").val(aUid);
            }
            if(!confirm("삭제하시겠습니까?")) return false;
        }
        
        obj.prop("disabled", true);
        $(".preloader").css("background", "transparent").show();
        
        $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=do_FaqAction', $("#frmFaq").serialize(), 
        function(response) {
            checkLogCountdown();
            obj.prop("disabled", false);
            $(".preloader").hide();
            $("#frmFaq input:hidden[name=aUid]").val("");
            $("[data-role=select-all]").prop("checked", false);
            
            var result=$.parseJSON(response);
            if(result.error) {
                alert(result.msg);
            } else {
                getFaqData();
            }            
        });
    }
    
    getFaqData();
    
</script>


