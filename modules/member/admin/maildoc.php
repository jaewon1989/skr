<?php
function getMDname($id)
{
	global $typeset;
	if ($typeset[$id]) return $typeset[$id];
	else return $id;
}
$typeset = array
(
	'_join'=>'회원가입축하 양식',
	'_auth'=>'이메일인증 양식',
	'_pw'=>'비밀번호요청 양식',
);
$type = $type ? $type : '_join';
?>
<div class="row">
	<div class="col-md-4" id="tab-content-list">
		<div class="panel panel-default">
			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-envelope fa-2x"></i>
				</div>
				<h4>
					이메일 양식
				</h4>
			</div>
			<div class="list-group">
				<?php $tdir = $g['path_module'].$module.'/doc/'?>
				<?php $dirs = opendir($tdir)?>
				<?php while(false !== ($skin = readdir($dirs))):?>
				<?php if($skin=='.' || $skin == '..')continue?>
				<?php $_type = str_replace('.txt','',$skin)?>
					<a href="<?php echo $g['adm_href']?>&amp;type=<?php echo $_type?>" class="list-group-item <?php if($_type==$type):?>active<?php endif?> doc-style">
						<i class="fa fa-envelope-o"></i> <?php echo getMDname($_type)?>
					   <small>(<?php echo $_type?>)</small>
					</a>
				<?php endwhile?>
				<?php closedir($dirs)?>
			</div>
			<div class="panel-footer">
			</div>
		</div>
	</div>
	<div class="col-md-8" id="tab-content-view">

		<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="a" value="maildoc_regis" />
		<input type="hidden" name="type" value="<?php echo $type?>" />

		<div class="page-header">
			<h4>
				<i class="fa fa-cog fa-lg"></i> &nbsp;이메일 양식 등록정보
				<span class="text-muted">( <?php echo getMDname($type)?> )</span></h4>
		</div>
		<div class="well">
			<ul>
				<li>내용에는 다음과 같은 치환문자를 사용할 수 있습니다.</li>
				<li>회원이름 : <code>{NAME}</code> / 닉네임 <code>{NICK}</code> / 아이디 <code>{ID}</code> / 이메일 <code>{EMAIL}</code></li>
			</ul>
		</div>
		<!-- 에디터 -->
	   <div class="editor">
			<textarea  id="summernote" name ="content" class="form-control" rows="3" onkeyup="resize(this)"><?php echo htmlspecialchars(implode('',file($g['path_module'].$module.'/doc/'.$type.'.txt')))?></textarea>
		</div>
		<!-- /에티터 -->
		<br>
		<div class="input-group">
			<?php if(!$typeset[$type]):?>
			<span class="input-group-btn">
			   <button class="btn btn-danger" onclick="delCheck('<?php echo $type?>');" />삭제</button>
			</span>
			<?php endif?>
			<span class="input-group-btn">
			  <button type="submit" class="btn btn-primary ">수정</button>
			</span>
			<input type="text" name="newdoc" value="" size="15" class="form-control" placeholder="신규양식 이름.." />
			<span class="input-group-btn">
			   <input type="submit" class="btn btn-primary" value="신규 등록" />
		   </span>
		   <span class="input-group-btn"> 
		      <button title="" data-tooltip="tooltip" data-target="#new_maildoc-guide" data-toggle="collapse" type="button" class="btn btn-default rb-help-btn" data-original-title="도움말">
		      	<i class="fa fa-question-circle fa-lg"></i>
		      </button>
		   </span>
		</div>
		<p id="new_maildoc-guide" class="help-block collapse alert alert-warning">
			<small> 
				이 양식으로 새로운 양식을 생성할 수 있습니다. <br>
			   새로운 양식명 (영문소문자+숫자+_ 조합)을 입력하신 후 [신규등록] 버튼을 눌러주세요.<br>
			</small>
		</p>

	  </form>
	</div>
</div>

<br><br><br>
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
        height:450,  //  에디터 높이 
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
	       ['picture', ['picture']],	
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
      
     });
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
 	 data = new FormData();
	 data.append("file",file); // 가상의 "file" 이라는 오브젝트를 만들어서 전송한다.
	 data.append("mbruid","<?php echo $my['uid']?>");
	 data.append("s","<?php echo $s?>");
	 $.ajax({
	     type: "POST",
	     url : rooturl+'/modules/<?php echo $module?>/action/a.upload.php',
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

function ToolCheck(compo)
{
	frames.editFrame.showCompo();
	frames.editFrame.EditBox(compo);
}
function delCheck(t)
{
	if (confirm('정말로 삭제하시겠습니까?   '))
	{
		frames._action_frame_<?php echo $m?>.location.href = '<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $module?>&a=maildoc_delete&type=' + t;
	}
}
function saveCheck(f)
{
  	// 내용 체크 및 포커싱  ie 에서는 안됨 
	var content = $('#summernote').code();

	if (f.content.value == '')
	{
		$('.note-editable').focus();
      alert('내용을 입력해 주세요.       ');
      return false;
	}
	if (f.newdoc.value != '')
	{
		if (!chkIdValue(f.newdoc.value))
		{
			alert('양식명은 영문소문자/숫자/_ 만 사용가능합니다.      ');
			f.newdoc.value = '';
			f.newdoc.focus();
			return false;
		}
	}

	return confirm('정말로 실행하시겠습니까?         ');
}
//]]>
</script>