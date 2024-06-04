<?php
if (!$_SESSION['upsescode'])
{
  $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
}
$sescode = $_SESSION['upsescode'];
if($R['uid']){
    $u_arr = getArrayString($R['upload']);
    $_tmp=array();
    $i=0;
    foreach ($u_arr['data'] as $val) {
       $U=getUidData($table['s_upload'],$val);
       if(!$U['fileonly']) $_tmp[$i]=$val;
       $i++;    
    }
    $insert_array=''; 
    // 중괄로로 재조립 
    foreach ($_tmp as $uid) {
        $insert_array.='['.$uid.']'; 
    }
}
?>
<style>
.rb-bbs-write .form-control {
  border-radius: 0
}
[class^="col-"] label {
    padding-right: 0px;
    padding-left: 0px; 
}
.btn-default {
    color: #373a3c;
    background-color: #fff;
    border-color: #ccc;
}

</style>
<script>
// 에디터 호출 
$(document).ready(function() {

      $('#summernote').summernote({
        tabsize: 2,
        styleWithSpan: false,
        height:<?php echo $d['theme']['edit_height']?>,  //  에디터 높이 : _var.php 에서 설정
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: true,         
        lang : 'ko-KR', // 언어 설정
        toolbar: [       
       //[groupname, [button list]]  : 툴바 그룹 [버튼 id ]  참조 페이지 ==> http://summernote.org/#/features  : 아래 순서대로 노출된다.       
         ['style', ['style']],
         ['fontstyle', ['fontname','bold','strikethrough','italic','underline', 'clear']],
         ['fontsize', ['fontsize']],
         ['color', ['color']],
         ['height', ['height']],
         ['Layout', ['ul','ol','paragraph']],
         ['insert', ['link', 'video', 'table','hr']], 
         ['Misc', ['fullscreen','codeview','help']]
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
      <?php if($d['theme']['perm_photo']<=$my['level']):?> 
      // 커스텀 첨부파일 버튼 추가 
      ,oninit: function() {
            var attachBtn = '<div class="note-picture btn-group"><button type="button" data-role="attach-handler-photo" data-type="photo" class="btn btn-default btn-sm btn-small" title="이미지첨부"><i class="fa fa-picture-o fa-lg fa-fw"></i> 이미지추가</button></div><div class="note-picture btn-group"><button type="button" data-role="attach-handler-file" data-type="file" class="btn btn-default btn-sm btn-small" title="파일첨부"><i class="fa fa-floppy-o fa-lg"></i> 파일추가</button></div>';
            $(attachBtn).appendTo($('.note-toolbar'));
            // Button tooltips
            $('#attachBtn').tooltip({container: 'body', placement: 'bottom'});
            // Button events
            $('#attachBtn').click(function(event) {
                $('#open-file').click();
            });
      }
      <?php endif?>     
   
      
     });
 });
</script>

<!-- 템플릿 시작 -->
<section class="rb-bbs rb-bbs-write">
   <form name="writeForm" method="post" action="<?php echo $g['s']?>/" onsubmit="return writeCheck(this);" class="form-horizontal" enctype="multipart/form-data">
      <input type="hidden" name="r" value="<?php echo $r?>" />
    <input type="hidden" name="a" value="write" />
    <input type="hidden" name="c" value="<?php echo $c?>" />
    <input type="hidden" name="cuid" value="<?php echo $_HM['uid']?>" />
    <input type="hidden" name="m" value="<?php echo $m?>" />
    <input type="hidden" name="bid" value="<?php echo $R['bbsid']?$R['bbsid']:$bid?>" />
    <input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
    <input type="hidden" name="reply" value="<?php echo $reply?>" />
    <input type="hidden" name="nlist" value="<?php echo $g['bbs_list']?>" />
    <input type="hidden" name="pcode" value="<?php echo $date['totime']?>" />
    <input type="hidden" name="sess_Code" value="<?php echo $sescode?>_<?php echo $my['uid']?>_<?php echo $B['uid']?>" />
    <input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $reply=='Y'?'':$R['upload']?>" />
    <input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$m.'/'?>" /> <!-- 파일저장 폴더 -->
    <input type="hidden" name="html" value="HTML" />
    <input type="hidden" name="featured_img" value="<?php echo $R['featured_img']?>" />  
      
      <?php if(!$my['uid']):?>
        <div class="form-group row">
           <label for="user_name" class="col-xs-1 col-form-label text-xs-right">이름</label>
           <div class="col-xs-11">
              <input type="text" name="name" id="user_name" class="form-control" value="<?php echo $R['name']?>" placeholder="이름을 입력하세요.">
           </div>
         </div>
         <?php if(!$R['uid']||$reply=='Y'):?>
         <div class="form-group row">
            <label for="password" class="col-xs-1 col-form-label text-xs-right">암호</label>
            <div class="col-xs-11">
               <input type="password" name="pw" id="password" class="form-control" value="<?php echo $R['pw']?>" placeholder="암호는 게시글 수정 및 삭제에 필요합니다.">
               <span class="help-block">비밀답변은 비번을 수정하지 않아야 원게시자가 열람할 수 있습니다.</span>
           <span class="glyphicon glyphicon-remove form-control-feedback"></span>
            </div>
         </div>
         <?php endif?>
      <?php endif?> 

    <?php if($B['category']):$_catexp = explode(',',$B['category']);$_catnum=count($_catexp)?>
       <div class="form-group rb-category">
           <label for="" class="col-xs-1 col-form-label text-xs-right">분류</label>
           <div class="col-xs-11">
               <span class="rb-category">
                   <select name="category" class="selectpicker" title='선택하세요'>
                       <option value="">&nbsp;+ <?php echo $_catexp[0]?>선택</option>
                      <?php for($i = 1; $i < $_catnum; $i++):if(!$_catexp[$i])continue;?>
                        <option value="<?php echo $_catexp[$i]?>"<?php if($_catexp[$i]==$R['category']||$_catexp[$i]==$cat):?> selected="selected"<?php endif?>>ㆍ<?php echo $_catexp[$i]?><?php if($d['theme']['show_catnum']):?>(<?php echo getDbRows($table[$m.'data'],'site='.$s.' and notice=0 and bbs='.$B['uid']." and category='".$_catexp[$i]."'")?>)<?php endif?></option>
                      <?php endfor?>
                   </select>
               </span>
           </div>
       </div>
      <?php endif?>
       <div class="form-group row">
           <label for="subject" class="col-xs-1 col-form-label text-xs-right">제목</label>
           <div class="col-xs-11">
               <input type="text" name="subject" value="<?php echo $R['subject']?>" placeholder="제목을 입력해 주세요." class="form-control" id="subject">
           </div>
       </div>
       <div class="form-group row">
           <div class="col-xs-offset-1 col-xs-11">
              <?php if($my['admin']):?>
               <label class="checkbox-inline">
                   <input type="checkbox" name="notice" value="1"<?php if($R['notice']):?> checked="checked"<?php endif?> > 공지글
               </label>
              <?php endif?>
              <?php if($d['theme']['use_hidden']==1):?>
               <label class="checkbox-inline">
                   <input type="checkbox" name="hidden" value="1"<?php if($R['hidden']):?> checked<?php endif?>> 비밀글
               </label>
              <?php elseif($d['theme']['use_hidden']==2):?>
              <input type="hidden" name="hidden" value="1" />
            <?php endif?>
           </div>
       </div>
       <div class="form-group row">
           <label class="sr-only">내용</label>
           <div class="col-xs-12">
               <div class="panel panel-default rb-editor">
                   <div class="rb-editor-body">
                       <textarea id="summernote" name ="content" class="form-control" rows="15">
                        <?php if($uid):?>
                           <?php echo getContents($R['content'],$R['html'])?>
                        <?php endif?>
                       </textarea>
                   </div>
               </div>
                <!-- module : 첨부파일 사용 모듈 , theme : 첨부파일 테마 , attach_handler_file : 파일첨부 실행 엘리먼트 , attach_handler_photo : 사진첨부 실행 엘리먼트 ,parent_data : 수정시 필요한 해당 포스트 데이타 배열 변수, attach_handler_getModalList : 업로드 리스트 모달로 호출용 엘리먼트 (class 인 경우 . 까지 넘긴다.)  --> 
                  <?php getWidget('default/attach',array('parent_module'=>$m,'theme'=>'bs4-summernote','attach_handler_file'=>'[data-role="attach-handler-file"]','attach_handler_photo'=>'[data-role="attach-handler-photo"]','attach_handler_getModalList'=>'.getModalList','parent_data'=>$R));?>
           </div>
       </div>
       <?php if($d['theme']['show_wtag']):?>
       <div class="form-group row">
           <label for="tag" class="col-xs-1 col-form-label text-xs-right">태그</label>
           <div class="col-xs-11">
               <div class="input-group input-group-sm">
                   <input type="text" name="tag" value="<?php echo $R['tag']?>" id="tag" class="form-control" placeholder="콤마(,)로 구분해서 입력해 주세요.">
                   <span class="input-group-btn">
                      <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#help-tag"><i class="fa fa-question fa-lg"></i></button>
                   </span>
               </div>
               <span class="collapse help-block" id="help-tag">
                   이 게시물을 가장 잘 표현할 수 있는 단어를 콤마(,)로 구분해서 입력해 주세요.
               </span>
           </div>
       </div>
       <?php endif?>

       <?php if($d['theme']['show_trackback']):?>
        <div class="form-group row">
           <label for="trackback" class="col-xs-1 col-form-label text-xs-right">트랙백</label>
           <div class="col-xs-11">
               <div class="input-group input-group-sm">
                   <input type="text" name="trackback" value="<?php echo $_SESSION['trackback']?>" id="trackback" class="form-control" placeholder="엮을주소를 입력해 주세요.">
                   <span class="input-group-btn">
                       <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#help-trackback"><i class="fa fa-question fa-lg"></i></button>
                   </span>
               </div>
               <span class="collapse help-block" id="help-trackback">
                   이 게시물을 보낼 트랙백주소를 입력해 주세요.
               </span>
           </div>
       </div>
       <?php endif?>
       <?php if((!$R['uid']||$reply=='Y')&&is_file($g['path_module'].$d['bbs']['snsconnect'])):?>
       <div class="form-group row">
           <label for="snsconnect" class="col-xs-1 col-form-label text-xs-right">SNS</label>
           <div class="col-xs-11">
              <?php include_once $g['path_module'].$d['bbs']['snsconnect'];?> 에도 게시물을 등록합니다.
           </div>
       </div>
       <?php endif?>
       <hr>
       <div class="form-group row">
           <div class="col-xs-offset-1 col-xs-11">
               <p class="form-control-static"><span class="text-muted">게시물 등록(수정/답변) 후</span></p>
               <label class="radio-inline">
                   <input type="radio" name="backtype" id="backtype1" value="list" <?php if(!$_SESSION['bbsback'] || $_SESSION['bbsback']=='list'):?> checked<?php endif?>> 목록으로 이동
               </label>
               <label class="radio-inline">
                   <input type="radio" name="backtype"  id="backtype2" value="view" <?php if($_SESSION['bbsback']=='view'):?> checked<?php endif?>> 본문으로 이동
               </label>
               <label class="radio-inline">
                   <input type="radio" name="backtype" id="backtype3" value="now" <?php if($_SESSION['bbsback']=='now'):?> checked<?php endif?>> 이 화면 유지
               </label>
           </div>
       </div>
       <hr>
       <div class="form-group row">
           <div class="col-xs-12 text-center">
               <button type="button" class="btn btn-secondary" onclick="cancelCheck();">취소하기</button>
               <button type="submit" class="btn btn-primary"><?php echo $uid?'수정':'등록'?>하기</button>
               <a href="<?php echo $g['bbs_list']?>" class="btn btn-secondary pull-right">목록</a>
           </div>
       </div>
   </form>
</section>
<!-- 템플릿 끝 -->
<?php getImport('bootstrap-select','bootstrap-select',false,'js')?>

<!-- 코드미러를 먼저 호출하고 난 후에 summernote 호출해야 코드미러가 적용이 됨-->
<!-- include summernote codemirror-->
 <style>
.CodeMirror {
  font-size: 13px;
  font-family: Menlo,Monaco,Consolas,"Courier New",monospace !important;
}
/* 첨부파일 : input-file*/
.btn-file {
  position: relative;
  overflow: hidden;
}
.btn-file input[type=file] {
  position: absolute;
  top: 0;
  right: 0;
  min-width: 100%;
  min-height: 100%;
  font-size: 100px;
  text-align: right;
  filter: alpha(opacity=0);
  opacity: 0;
  background: red;
  cursor: inherit;
  display: block;
}
input[readonly] {
  background-color: white !important;
  cursor: text !important;
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
// 툴팁 이벤트 
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
}); 
 // bootstrap 셀렉트 
 $('.selectpicker').selectpicker();

// 에디터 입력내용 소스창에 적용
function InserHTMLtoEditor(sHTML)
{
  var nHTML = $('#summernote').code();
  $('#summernote').code(nHTML+sHTML);
}

// 첨부파일 업로드 이벤트 
$('.open-file').on('change',function(){
    var file=$(this)[0].files[0];
    Upload_file('',file,'',''); // 아래 파일 업로두 함수 호출
});

/* 파일 업로드 함수
     type : 파일 타입(이미지, 워드,엑셀 등) 
*/ 
 function Upload_file(type,file,editor,welEditable) 
 {
   var sess_Code=$('input[name="sess_Code"]').val();
   var saveDir=$('input[name="saveDir"]').val();
   data = new FormData();
   data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
   data.append("sess_Code",sess_Code);
   data.append("saveDir",saveDir);
   $.ajax({
        type: "POST",
        url : rooturl+'/?r=<?php echo $r?>&m=<?php echo $m?>&a=user_ajax_upload',
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
                }else if(type=='file'){

                }
            }else{
                var msg=val[1];
                alert(msg);
                return false;
            }  

       } // success
    }); // ajax
 } // function

// 글 등록 함수 
var submitFlag = false;

function writeCheck(f)
{
      if (submitFlag == true)
      {
        alert('게시물을 등록하고 있습니다. 잠시만 기다려 주세요.');
        return false;
      }
      if (f.name && f.name.value == '')
      {
        alert('이름을 입력해 주세요. ');
        f.name.focus();
        return false;
      }
      if (f.pw && f.pw.value == '')
      {
        alert('암호를 입력해 주세요. ');
        f.pw.focus();
        return false;
      }
      if (f.category && f.category.value == '')
      {
        alert('카테고리를 선택해 주세요. ');
        f.category.focus();
        return false;
      }
      if (f.subject.value == '')
      {
        alert('제목을 입력해 주세요.      ');
        f.subject.focus();
        return false;
      }
      if (f.notice && f.hidden)
      {
        if (f.notice.checked == true && f.hidden.checked == true)
        {
          alert('공지글은 비밀글로 등록할 수 없습니다.  ');
          f.hidden.checked = false;
          return false;
        }
      }
       
      // 내용 체크 및 포커싱  ie 에서는 안됨 
      var content = $('#summernote').code();
      if (content =='')
      {
        $('.note-editable').focus();
          alert('내용을 입력해 주세요.       ');
          return false;
      } 

      // 첨부파일 uid 를 upfiles 값에 추가하기  
      var attachfiles=$('input[name="attachfiles[]"]').map(function(){return $(this).val()}).get();
      var new_upfiles='';
      if(attachfiles){
            for(var i=0;i<attachfiles.length;i++) 
            {  
                 new_upfiles+=attachfiles[i];
            }
            $('input[name="upfiles"]').val(new_upfiles);
      }  
      getIframeForAction(f);
      f.submit(); 
      submitFlag = true;
}

function cancelCheck()
{
  if (confirm('정말 취소하시겠습니까?    '))
  {
    history.back();
  }
}


//]]>
</script>

