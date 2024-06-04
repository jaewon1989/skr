<!-- 댓글 관련 함수,스타일, js  인클루드 -->
<?php  include $g['dir_module_skin'].'comment/function.php';?> 
<link rel="stylesheet" href="<?php echo $g['dir_module_skin']?>comment/style.css" >
<script type="text/javascript" src="<?php echo $g['dir_module_skin']?>comment/jquery.form.js"></script>   
<!--// 댓글 관련 함수,스타일, js  인클루드 -->
<?php $_SESSION['wcode']=$date['totime']?>
<a name="cmt-write"></a>
<div class="col-sm-12">
  <h4><i class="fa fa-edit hidden-xs"></i> 댓글쓰기</h4>
    <form name="cmtForm" id="cmtForm" method="post" onsubmit="return writeCheck(this);">
    <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="a" value="comment_regis" />
    <input type="hidden" name="parent" value="<?php echo $R['uid']?>" />
    <input type="hidden" name="uid" value="<?php echo $C['uid']?>" />
    <input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $C['upload']?>" />
    <input type="hidden" name="hidepost" value="<?php echo $hidepost?>" />
    <input type="hidden" name="pcode" value="<?php echo $date['totime']?>" />
    <input type="hidden" name="theme" value="<?php echo $g['dir_module_skin']?>" /><!-- 테마값  -->
    <input type="hidden" name="c_sort" value="<?php echo $d['comment']['sort']?>" /><!-- sort  -->
    <input type="hidden" name="c_orderby" value="<?php echo $d['comment']['orderby2']?>" /><!-- sort  -->
    <input type="hidden" name="c_recnum" value="<?php echo $d['comment']['recnum']?>" /><!-- 출력수  -->
    <input type="hidden" name="c_page" value="1" /><!-- 페이지값 -->
    <input type="hidden" name="html" value="HTML" />
      <div class="panel panel-default">
         <?php $placeholder=$my['uid']?'내용을 입력해주세요':'로그인을 먼저 해주세요'?> 
                     <textarea  id="summernote" name ="content" class="form-control" rows="3" onkeyup="resize(this)" placeholder="<?php echo $placeholder?>"> </textarea>
         <div class="panel-footer">
          <div class="row">
                <div class="col-sm-10">
                  <?php if($my['uid']):?> 
                   <span class="text-muted small">타인을 비방하거나 개인정보를 유출하는 글의 게시를 삼가주세요</span>
                  <?php else:?>
                     <span class="text-muted small text-danger">로그인을 먼저 해주세요.</span>
                 <?php endif?>
                </div> 
                <div class="col-sm-1">
                      <?php if($my['uid']):?>
                         <input type="submit" value="댓글 등록" class="btn btn-primary" id="btn-cmt-regis" >                              
                      <?php else:?>
                         <input type="submit" value="댓글 등록" class="btn btn-primary disabled" disabled > 
                      <?php endif?> 
                    </div>
             </div>
          </div> <!--.panel-footer -->
      </div><!--.panel -->  
    </form>
</div>
<div class="panel-body"></div>
<div class="col-sm-12" id="rb-comments">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
           <h3 class="panel-title pull-left"><i class="fa fa-comments-o fa-lg hidden-xs"></i> 전체 댓글 (<strong class="text-danger" id="cmt_total"><?php echo $R['comment']?></strong>)</h3>
         <div class="pull-right">
            <?php if($d['comment']['show_sort']):?>
            <div data-toggle="buttons" class="btn-group btn-group-sm">
              <label class="btn btn-default active">
                <input type="radio" id="uid-asc" class="cmt-sort" name="options"> 최신순
              </label>
              <label class="btn btn-default">
                <input type="radio" id="score1-desc" class="cmt-sort" name="options"> 공감순
              </label>
              <label class="btn btn-default">
                <input type="radio" id="oneline-desc" class="cmt-sort" name="options"> 답글순
              </label>
             </div>
            <?php endif?>
            <?php if($d['comment']['show_page']):?>
            <span class="page-wrap text-muted small">
                 <span class="page-num text-danger" id="now-page">1</span>
                 <span class="page-num">/</span>
                     <span class="page-num" id="total-page"></span>
             </span>
             <div class="btn-group btn-group-sm" style="margin-left:5px;">
                 <button class="btn btn-default btn-page disabled" id="btn-prev" data-toggle="tooltip" title="Prev"><i class="fa fa-chevron-left"></i></button>
                     <button class="btn btn-default btn-page" id="btn-next" data-toggle="tooltip" title="Next"><i class="fa fa-chevron-right"></i></button>
             </div>
            <?php endif?>
        </div>  
     </div>
            
      <ul class="list-group" id="comment-list-box">
         <?php getCommentList($g['dir_module_skin'],$m.$R['uid'],$_where,$c_recnum,$c_sort,$orderby1,$orderby2,$cp)?>
    </ul>  
      <p class="list-loader text-center" style="display:none;position:absolute;bottom:100px;left:50%;"><i class="fa fa-spinner fa-spin fa-lg"></i></p>
   </div>      
</div>
      

  
<!-- 코드미러를 먼저 호출하고 난 후에 summernote 호출해야 코드미러가 적용이 됨-->
<!-- include summernote codemirror-->
 <style>
.CodeMirror {
  font-size: 13px;
  font-family: Menlo,Monaco,Consolas,"Courier New",monospace !important;
}
</style>
<?php getImport('codemirror','codemirror',false,'css')?>
<?php getImport('codemirror','codemirror',false,'js')?>
<?php getImport('codemirror','theme/monokai',false,'css')?>
<?php getImport('codemirror','mode/htmlmixed/htmlmixed',false,'js')?>
<?php getImport('codemirror','mode/xml/xml',false,'js')?>

<!-- include summernote css/js-->
<?php getImport('summernote','dist/summernote.min',false,'js')?>
<?php getImport('summernote','lang/summernote-ko-KR',false,'js')?>
<?php getImport('summernote','dist/summernote',false,'css')?>
<script type="text/javascript">
//<![CDATA[

// 글상자 크기 자동조절
function autosize() {
  // auto adjust the height of
  $('body').on('keyup', 'textarea', function (){
    $(this).height(0);
    $(this).height(this.scrollHeight);
  });
}

// sort 버튼 클릭시 이벤트 
$('.cmt-sort').on('change',function(){
   var id=$(this).attr('id');
   var id_arr=id.split('-');
   var sort=id_arr[0];
   var orderby=id_arr[1];
   $('input[name="c_sort"]').val(sort);
   $('input[name="c_orderby"]').val(orderby);
   $('input[name="c_page"]').val(1); // 페이지 초기화
   $('#btn-prev').addClass('disabled'); // 이전 버튼 초기화
   $('#btn-next').removeClass('disabled'); // 다음 버튼 초기화

   get_CommentList('sorting');

   // 페이지 버튼 리셋 
    get_PbtnSetting();
    
    //페이지 숫자 리셋 
    var TPG=$('input[name="TPG"]').val(); 
    if(TPG) {$('#total-page').text(TPG); $('#now-page').text(1);} // 
    else {$('#total-page').text(0);$('#now-page').text(0);}

});

// 더보기 버튼 이벤트 
$('body').on('click','.btn-more',function(){
   get_CommentList('more');
});

// 다음/이전 페이징 버튼 이벤트 
$('body').on('click','.btn-page',function(){
   var npage=$('input[name="c_page"]').val();
   var id=$(this).attr('id');
   // 페이지 세팅 
   if(id=='btn-next') new_page=Number(npage)+1;
   else new_page=Number(npage)-1;
    
    // 신규 페이지 저장 input  및 보여주기 span
   $('input[name="c_page"]').val(new_page); // 현재 페이지 저장 
   $('#now-page').text(new_page); // 현재 페이지 보여주기 
   
    // 리스트 출력함수 호출 
    get_CommentList('paging');
    
    // 페이지 버튼 리셋 
    get_PbtnSetting();
});

// 페이징 버튼 세팅 
function get_PbtnSetting()
{
   var is_namegi=$('input[name="is_namegi"]').val(); // 나머지가 있는지 여부 체크값
   var npage=$('input[name="c_page"]').val();

   //다음 페이지 버튼 체크 
    if(Number(is_namegi)==0 || !is_namegi)  $('#btn-next').addClass('disabled');
    else $('#btn-next').removeClass('disabled');
   
   // 이전 페이지 버튼 체크 
   if(Number(npage)>1) $('#btn-prev').removeClass('disabled');
   else $('#btn-prev').addClass('disabled');     
}
/*
  댓글 리스트 출력 함수
   parent : 게시글 uidi , c_sort : 댓글 sort ...
   type : sorting, more(더보기)  
*/  
function get_CommentList(type)
{
     // loder 생성 
   $('.list-loader').fadeIn('fast');
   var theme=$('input[name="theme"]').val();
   var parent=$('input[name="parent"]').val();
   var sort=$('input[name="c_sort"]').val();
   var recnum=$('input[name="c_recnum"]').val();
   var page=$('input[name="c_page"]').val();
   var orderby=$('input[name="c_orderby"]').val();
   if(type=='more')
   {
    var last_list=$('.comment-list:last'); // 댓글 마지막 리스트 
      var last_id=$(last_list).attr('id');
      var last_id_arr=last_id.split('-');
      var sort_arr={"uid":0,"score1":1,"oneline":2};
      var last_sort=last_id_arr[sort_arr[sort]]; // sort 에 따라서 last 값이 달라진다.
   }else{
      var last_sort='';
   }  
   var data=theme+'^^'+parent+'^^'+sort+'^^'+recnum+'^^'+page+'^^'+orderby+'^^'+last_sort;
    // 액션 페이지로 보내기  
   var ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=get_CommentList&type='+type+'&data='+data,''); 
   //결과값 세팅  
   var result=getAjaxFilterString(ajax,'RESULT');
   if(result) $('.list-loader').fadeOut(100);
   if(type=='more'){
      $('.btn-more').remove();
      $('.comment-list:last').after(result); // 더보기일 경우 마지막 리스트 아래에 출력 
   }else{
      $('#comment-list-box').html(result); // 기존 리스트 덮어쓰우기 출력 
   }

}

function showButton(id) {
  $('#comment-btn-'+id).fadeIn('fast');
}

// 툴팁 이벤트 
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
}); 

// live 툴팁 이벤트 
$('body').tooltip({
    selector :'.live-tooltip'
});

// 댓글 등록 함수 
function writeCheck(f)
{
   // 내용 체크 및 포커싱  ie 에서는 안됨 
  var content =$('.note-editable').text();
  var cmt_total=$('#cmt_total').text(); // 댓글 총 수량
  var new_total=Number(cmt_total)+1; // 댓글 총 수량 +1개 추가 
  var uid=f.uid.value; // 해당 댓글 uid 값 
  if (content.trim() =='')
  {
      $('.note-editable').focus();
             alert('내용을 입력해 주세요.       ');
      return false;
  }else{
    $(f).ajaxForm({
            success: function (response) {
                 if(!uid) $('#cmt_total').text(new_total); // uid 가 없는 경우 즉, 신규인 경우 
                  $('.note-editable').text(''); // 글 내용 비우기 
                  $('input[name="uid"]').val(''); // 기존 uid 값 비우기
                  $('#btn-cmt-regis').val('댓글 등록'); 
                  $('#comment-list-box').html(response);
            }
      });
    }  
}

// 댓글 액션 처리 이벤트 
$('body').on('click','.cmt-tools',function(e){
   e.preventDefault();
   var id=$(this).attr('id');
   var id_arr=id.split('-');
   var act=id_arr[0]; // 이벤트 종류 
   var uid=id_arr[1]; // 해당 한줄의견 uid
   var box=$('#cuid-'+uid); // 해당 댓글 uid media div
   var theme=$(box).find('input[name="theme"]').val(); // 게시판 테마
   var parent=$(box).find('input[name="parent"]').val(); // 부모 게시글 uid - 리스트 출력시 필요
   var old_comment=$(box).find('input[name="c_content"]').val(); // 해당 댓글 내용 
   var act_name={"good":"평가","bad":"평가","delete":"삭제","report":"신고"}; // 액션 전 확인용  
   var ajax;
   var old_cmt_total=$('#cmt_total').text(); // 기존 댓글 수 
   var old_good_total=$('#good-num-'+uid).text(); // 기존 공감 수
   var old_bad_total=$('#bad-num-'+uid).text(); // 기존 공감 수

   if(act=='edit'){
      scrollToAnchor('cmt-write'); // 글쓰기 창으로 이동 아래 함수 참조 
    $('#summernote').code(old_comment); // 에디터 창에 기존 내용 입력하기 
    $('input[name="uid"]').val(uid);
      $('.note-editable').focus();    
      $('#btn-cmt-regis').val('댓글 수정');
   }else{
      if(confirm('정말로 '+act_name[act]+'하시겠습니까?     ')){
        
         // 액션 파일에 전송 및 결과값 세팅 
         if(act=='good' || act=='bad')  ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=comment_score&uid='+uid+'&value='+act,''); // 평가인 경우 
         else ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=comment_'+act+'&uid='+uid,''); 
          var msg=getAjaxFilterString(ajax,'RESULT'); // 결과 메세지 
         
          // 액션처리 후 
          switch(act)
          {
           case 'report':
                alert(msg);
           break;
           case 'delete':
                if(msg=='ok'){
                 var new_cmt_total=Number(old_cmt_total)-1;
                  $('#cmt_total').text(new_cmt_total);
                get_CommentList('basic');// 댓글 리스트 리셋  
                }else{
                   alert(msg);
                }           
           break;
           case 'good': 
           case 'bad':
                if(msg=='ok'){
                  if(act=='good'){
                     var new_good_total=Number(old_good_total)+1; // 공감수 +1
                     $('#good-num-'+uid).text(new_good_total);
                   }else{
                      var new_bad_total=Number(old_bad_total)+1; // 비공감 수 +1 
                    $('#bad-num-'+uid).text(new_bad_total);
                   }
                   alert('반영되었습니다.   '); 
                }else{
                   alert(msg);
                }
           break;
          }
           
      }else{  // 확인한 경우 
         return false;
      }
   }// act !='edit' 인 경우 
  
});

// 한줄의견 등록 이벤트  
$('body').on('click','.one-regis',function(){
    var box=$(this).parent().parent().parent().parent();
    var post=$(box).find('input[name="post"]').val();// 게시글 uid 
    var parent=$(box).find('input[name="parent"]').val();// 댓글 uid 
    var theme=$(box).find('input[name="theme"]').val(); // 게시판 테마
    var uid=$(box).find('input[name="ouid"]').val(); // 한줄의견 uid    
    var content=$(box).find('textarea[name="oneline"]').val(); // 한줄의견 내용 
    var old_one_num=$('#one-num-'+parent).text(); // 기존 한줄의견 갯수  
    if(content==''){
        alert('내용을 입력해주세요.    ');
        $(box).find('textarea[name="oneline"]').focus();
        return false;
    }else{
        content=encodeURI(content); // encode
        $(box).find('textarea[name="oneline"]').val(''); // 입력내용 초기화
      var ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=oneline_regis&post='+post+'&parent='+parent+'&content='+content+'&uid='+uid,''); // 액션 페이지로 보내기  
      var msg=getAjaxFilterString(ajax,'RESULT');
      if(msg=='ok')
      {
          var new_one_num=Number(old_one_num)+1;
           if(!uid) $('#one-num-'+parent).text(new_one_num);// 수정이 아닌경우 신규 한줄의견 수량 갱신
           get_OnelineList(theme,parent) // 한줄의견 리스트 출력 함수 호출 
      }else{
          alert(msg);
      }
    }
   
});

// 한줄의견 액션 처리 이벤트 
$('body').on('click','a.one-tools',function(e){
   e.preventDefault();
   var box=$(this).parent().parent();
   var theme=$(box).find('input[name="theme"]').val(); // 게시판 테마
   var parent=$(box).find('input[name="parent"]').val(); // 댓글 uid
   var old_oneline=$(box).find('input[name="o_content"]').val(); // 해당 한줄의견 내용 
    var id=$(this).attr('id');
   var id_arr=id.split('-');
   var act=id_arr[0]; // 이벤트 종류 
   var uid=id_arr[1]; // 해당 한줄의견 uid
   var act_name={"delete":"삭제","report":"신고"};
   var ajax;
   var old_one_num=$('#one-num-'+parent).text(); // 기존 한줄의견 갯수 

   if(act=='edit'){
    scrollToAnchor('one-write2-'+parent); // 글쓰기 창으로 이동 아래 함수 참조  
    $('textarea[name="oneline"]').val(old_oneline);
    $('input[name="ouid"]').val(uid);
      $('textarea[name="oneline"]').focus();    
      $('#one-regis-'+parent).text('수정');
   }else{
      if(confirm('정말로 '+act_name[act]+'하시겠습니까?     ')){
        ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=oneline_'+act+'&uid='+uid,''); 
         var msg=getAjaxFilterString(ajax,'RESULT'); // 결과 메세지 
         // 액션처리 후 
         if(act=='report'){
            alert(msg); 
         }else if(act=='delete'){
            if(msg=='ok'){
               var new_one_num=Number(old_one_num)-1;
              $('#one-num-'+parent).text(new_one_num);
            get_OnelineList(theme,parent);
            }else{
               alert(msg);
            }            
         } 
      }else{
         return false;
      }
   }
  
});

// 수정시 이동 스크립트
function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'fast');
}

// 한줄의견 리스트 출력 호출 함수
function get_OnelineList(theme,parent)
{
   var ajax=getHttprequest(rooturl+'/?r='+raccount+'&m=<?php echo $m?>&a=get_OnelineList&theme='+theme+'&parent='+parent,''); 
   var result=getAjaxFilterString(ajax,'RESULT');
   $('#oneline-box-'+parent).html(result); // 한줄의견 리스트 출력 
   $('input[name="ouid"]').val('');// 입력된 한줄의견 uid 값을 지운다
   $('textarea[name="oneline"]').val('');// 입력된 내용 삭제 
   $('#one-regis-'+parent).text('등록'); // 등록버튼 초기화
}

// 에디터 입력내용 소스창에 적용
function InserHTMLtoEditor(sHTML)
{
  var nHTML = $('#summernote').code();
  $('#summernote').code(nHTML+sHTML);
}

// 에디터 호출 
$(document).ready(function() {

      $('#summernote').summernote({
        tabsize: 2,
        styleWithSpan: false,
        height: 60,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,         
        lang : 'ko-KR', // 언어 설정
        toolbar: [       
       //[groupname, [button list]]  : 툴바 그룹 [버튼 id ]  참조 페이지 ==> http://summernote.org/#/features  : 아래 순서대로 노출된다.       
       // 아래 항목이 필요한 경우 주석 제거해주세요.
         // ['style', ['style']],
         // ['fontstyle', ['fontname','bold','strikethrough','italic','underline', 'clear']],
         // ['fontsize', ['fontsize']],
         // ['color', ['color']],
         // ['height', ['height']],
         // ['Layout', ['ul','ol','paragraph']], 
         ['insert', ['link', 'video', 'codeview'<?php if($d['theme']['cmt_perm_photo']<=$my['level']):?>,'picture'<?php endif?>]],
        //['Misc', ['fullscreen','codeview','help']]        
      ],  
      
       // 소스 편집창
     codemirror: {
      mode: "text/html",
      indentUnit: 4,
      lineNumbers: true,
      matchBrackets: true,
      indentWithTabs: true,
      theme: 'monokai'
      },
      // 이미지 바로 넣기 
      onImageUpload: function(files, editor, welEditable) {
          Upload_file('img',files[0],editor,welEditable);
       } 
      
     });
 });

/* 파일 업로드 함수
     type : 파일 타입(이미지, 워드,엑셀 등) 
*/ 
 function Upload_file(type,file,editor,welEditable) 
 {
   data = new FormData();
   data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
   data.append("mbruid","<?php echo $my['uid']?>");
   data.append("s","<?php echo $s?>");
   $.ajax({
       type: "POST",
       url : rooturl+'/modules/bbs/action/a.upload.php',
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
           var up_val=$('input[name="upfiles"]').val(); // 현재 upfiles 값 

           if(up_val=='') $('input[name="upfiles"]').val('['+upuid+']'); // 처음이면 uid 값만...
           else $('input[name="upfiles"]').val(up_val+'['+upuid+']'); // 처음이 아니면 콤마 추가 
           
           // 파일 타입이 이미지인 경우에만 에디터에 이미지 삽입
           if(type=='img') {
               editor.insertImage(welEditable, source); 
          }
         }else{
            var msg=val[1];
            alert(msg);
            return false;
         }  

       } // success
    }); // ajax
 } // function

// 전체 페이지 초기화
window.onload=function(){
  var TPG=$('input[name="TPG"]').val();
  $('#total-page').text(TPG); // 전체 페이지 초기화 
}

//]]>
</script>


