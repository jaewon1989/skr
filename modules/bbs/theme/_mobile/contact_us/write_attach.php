<?php
if (!$_SESSION['upsescode'])
{
 $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
}
$sescode = $_SESSION['upsescode'];

?>
<?php getImport('bootstrap-select','bootstrap-select',false,'css')?>
<link href="<?php echo $g['url_module_skin']?>/style.css" rel="stylesheet">
<script src="<?php echo $g['url_module_skin']?>/js/jquery.fileuploadmulti.min.js"></script> 

<!-- 템플릿 시작 -->
<section class="rb-bbs rb-bbs-write">
   <form name="writeForm" method="post" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>" onsubmit="return writeCheck(this);" class="form-horizontal" enctype="multipart/form-data">
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
      
      <?php if(!$my['uid']):?>
       <div class="form-group">
          <label for="user_name" class="col-sm-1 control-label">이름</label>
          <div class="col-sm-11">
             <input type="text" name="name" id="user_name" class="form-control" value="<?php echo $R['name']?>" placeholder="이름을 입력하세요.">
          </div>
        </div>
        <?php if(!$R['uid']||$reply=='Y'):?>
        <div class="form-group has-error has-feedback">
           <label for="password" class="col-sm-1 control-label">암호</label>
           <div class="col-sm-11">
              <input type="password" name="pw" id="password" class="form-control" value="<?php echo $R['pw']?>" placeholder="암호는 게시글 수정 및 삭제에 필요합니다.">
              <span class="help-block">비밀답변은 비번을 수정하지 않아야 원게시자가 열람할 수 있습니다.</span>
      <span class="glyphicon glyphicon-remove form-control-feedback"></span>
           </div>
        </div>
        <?php endif?>
      <?php endif?> 

  <?php if($B['category']):$_catexp = explode(',',$B['category']);$_catnum=count($_catexp)?>
       <div class="form-group rb-category">
           <label for="" class="col-sm-1 control-label">분류</label>
           <div class="col-sm-11">
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
       <div class="form-group">
           <label for="subject" class="col-sm-1 control-label">제목</label>
           <div class="col-sm-11">
               <input type="text" name="subject" value="<?php echo $R['subject']?>" placeholder="제목을 입력해 주세요." class="form-control" id="subject">
           </div>
       </div>
       <div class="form-group">
           <div class="col-sm-offset-1 col-sm-11">
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
       <div class="form-group">
           <label class="sr-only">내용</label>
           <div class="col-sm-12">
               <div class="panel panel-default rb-editor">
                   <div class="rb-editor-body">
                       <textarea id="summernote" name ="content" class="form-control" rows="15"> <?php echo getContents($R['content'],$R['html'])?></textarea>
                   </div>
               </div>
               <div id="files" class="files"><!-- 파일폼 출력 --></div> 
               <div id="file-row"><!-- 업로드 이미지 출력 --></div>
           </div>
       </div>

       <?php if($d['theme']['show_wtag']):?>
       <div class="form-group">
           <label for="tag" class="col-sm-1 control-label">태그</label>
           <div class="col-sm-11">
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
        <div class="form-group">
           <label for="trackback" class="col-sm-1 control-label">트랙백</label>
           <div class="col-sm-11">
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
       <div class="form-group">
           <label for="snsconnect" class="col-sm-1 control-label">SNS</label>
           <div class="col-sm-11">
              <?php include_once $g['path_module'].$d['bbs']['snsconnect'];?> 에도 게시물을 등록합니다.
           </div>
       </div>
       <?php endif?>
       <hr>
       <div class="form-group">
           <div class="col-sm-offset-1 col-sm-11">
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
       <div class="form-group">
           <div class="col-sm-12 text-center">
               <button type="button" class="btn btn-default" onclick="cancelCheck();">취소</button>
               <button type="submit" class="btn btn-primary">확인</button>
               <a href="<?php echo $g['bbs_list']?>" class="btn btn-default pull-right" href="">목록</a>
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
        <?php if($d['theme']['perm_photo']<=$my['level']):?>
             ['picture', ['picture']],
            <?php endif?>
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
     //       // 커스텀 첨부파일 버튼 추가 
     //      oninit: function() {
     //        var attachBtn = '<div class="note-picture btn-group"><button type="button" id="attachBtn" class="btn btn-default btn-sm btn-small" title="파일첨부"><i class="fa fa-floppy-o fa-lg "></i> </button></div>';
     //        $(attachBtn).prependTo($('.note-toolbar'));
     //        // Button tooltips
     //        $('#attachBtn').tooltip({container: 'body', placement: 'bottom'});
     //        // Button events
     //        $('#attachBtn').click(function(event) {
     //            $('#open-file').click();
     //        });
     //      },
     //  // 이미지 바로 넣기 
     // onImageUpload: function(files, editor, welEditable) {
     //      Upload_file('img',files[0],editor,welEditable);
     //   } 
      
     });
 });

// 파일첨부 기능 추가 
$(document).ready(function()
{
    var themeUrl='<?php echo $g['url_comment_skin']?>';
    var saveDir='<?php echo $g['path_file']?>comment/';
    var path_core='<?php echo $g['path_core']?>';
    var path_module='<?php echo $g['path_module']?>';
    var sess_Code='<?php echo $sess_Code?>';
    var params=themeUrl+'@'+saveDir+'@'+path_core+'@'+path_module+'@'+sess_Code;
     var settings = {
        url: rooturl+'/?r=<?php echo $r?>&m=comment&a=multi_upload&params='+encodeURI(params),
        method: "POST",
        allowedTypes:"jpg,png,gif,doc,pdf,zip",
        fileName: "files",
        multiple: true,
        // 개별 파일 업로드 완료 후
        onSuccess:function(files,response,xhr)
        {
    
            //var result=$.parseJSON(response);
            //var img=printThumb(result.src);
            var img=response;
           $(img).appendTo('#file-row');
        }
    }
    $("#files").uploadFile(settings); // 아작스 폼+input=file 엘리먼트 세팅
});

// 첨부파일 업로드 이벤트 
$('.file-upload').on('change',function(){
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
      url : rooturl+'/?r=<?php echo $r?>&m=mediaset&a=user_ajax_upload',
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

// 첨부파일 : input-file onchange 이벤트시 해당 값 보여주기 
$(document).on('change', '.btn-file :file', function() {
  var input = $(this),
      numFiles = input.get(0).files ? input.get(0).files.length : 1,
      label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
  input.trigger('fileselect', [numFiles, label]);
});

$(document).ready( function() {
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        
        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }
        
    });
});
// 첨부파일 : input-file onchange 이벤트시 해당 값 보여주기 

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
  bootbox.alert('카테고리를 선택해 주세요. ');
  f.category.focus();
  return false;
 }
 if (f.subject.value == '')
 {
  bootbox.alert('제목을 입력해 주세요.      ');
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
 if (content ==' ')
 {
  $('.note-editable').focus();
      alert('내용을 입력해 주세요.       ');
      return false;
 }  
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

