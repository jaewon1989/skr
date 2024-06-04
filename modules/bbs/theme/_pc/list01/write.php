<?php getImport('bootstrap-select','bootstrap-select',false,'js')?>
<?php getImport('bootstrap-select','bootstrap-select',false,'css')?>

 <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="kf-bbs"></i> <?php echo $B['name']?> 게시판</h3>
		</div>
		<div class="panel-body">
			<form name="writeForm" method="post" action="<?php echo $g['s']?>/" target="_action_frame_<?php echo $m?>" onsubmit="return writeCheck(this);" role="form" class="form-horizontal">
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
			<input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $reply=='Y'?'':$R['upload']?>" />
			<input type="hidden" name="html" value="HTML" />
				<fieldset>
			      <?php if(!$my['id']):?>
							<div class="form-group">
								<label class="col-sm-2 text-center" for="">이름<span class="rb-form-required text-danger"></span></label>
								<div class="col-sm-10">
									<input type="text" name="name" placeholder="이름을 입력해 주세요." value="<?php echo $R['name']?>" id="" class="form-control">
									<span class="help-block"></span>
								</div>
							</div>					
							<?php if(!$R['uid']||$reply=='Y'):?>
							<div class="form-group has-error has-feedback">
								<label class="col-sm-2 text-center" for="">암호<span class="rb-form-required text-danger"></span></label>
								<div class="col-sm-10">
									<input type="password" name="pw" placeholder="암호는 게시글 수정 및 삭제에 필요합니다." value="<?php echo $R['pw']?>" id="" class="form-control">
									<span class="help-block">비밀답변은 비번을 수정하지 않아야 원게시자가 열람할 수 있습니다.</span>
									<span class="glyphicon glyphicon-remove form-control-feedback"></span>
								</div>
							</div>
						  <?php endif?>
				   <?php endif?>	 

  	          <?php if($B['category']):$_catexp = explode(',',$B['category']);$_catnum=count($_catexp)?>					
					<div class="form-group">
						<label class="col-sm-2 text-center" for="">카테고리</label>
						<div class="col-sm-10">
				           <select name="category" class="boot-select" data-width="auto" data-style="btn-default btn-sm">
				           		<option value="">&nbsp;+ <?php echo $_catexp[0]?>선택</option>
                            <?php for($i = 1; $i < $_catnum; $i++):if(!$_catexp[$i])continue;?>
                                <option value="<?php echo $_catexp[$i]?>"<?php if($_catexp[$i]==$R['category']||$_catexp[$i]==$cat):?> selected="selected"<?php endif?>>ㆍ<?php echo $_catexp[$i]?><?php if($d['theme']['show_catnum']):?>(<?php echo getDbRows($table[$m.'data'],'site='.$s.' and notice=0 and bbs='.$B['uid']." and category='".$_catexp[$i]."'")?>)<?php endif?></option>
                           <?php endfor?>
                        </select>
						</div>
					 </div>
					 <?php endif?>
				    <div class="form-group">
								<label class="col-sm-2 text-center" for="">제목<span class="rb-form-required text-danger"></span></label>
								<div class="col-sm-8">
									<input type="text" name="subject" placeholder="제목을 입력해 주세요." value="<?php echo $R['subject']?>" id="" class="form-control">
								</div>
								<?php if($my['admin']):?>
									<div class="checkbox col-sm-1">
									    <label>
									      <input type="checkbox" name="notice" value="1"<?php if($R['notice']):?> checked="checked"<?php endif?>> 
									      <span data-toggle="tooltip" title="공지"><i class="fa fa-volume-up fa-lg"></i></span>
									    </label>
									 </div>
							  	<?php endif?>
							
								<div class="checkbox col-sm-1">
									<?php if($d['theme']['use_hidden']==1):?>
									   <label>
										   <input type="checkbox" name="hidden" value="1"<?php if($R['hidden']):?> checked<?php endif?> />
										   <span data-toggle="tooltip" title="비밀"><i class="fa fa-lock fa-lg"></i></span>
									   </label>
									<?php elseif($d['theme']['use_hidden']==2):?>
										    <input type="hidden" name="hidden" value="1" />
									<?php endif?>
							 </div>

					 </div>	
                 <div class="form-group">
				        <div class="col-sm-12">
				            <textarea  id="summernote" name ="content" class="form-control" rows="3" onkeyup="resize(this)" placeholder="내용을 입력하세요..."> <?php echo getContents($R['content'],$R['html'])?></textarea>
				        </div>
				    </div>
				    
				    <?php if($d['theme']['file_upload_show']&&$d['theme']['perm_upload']<=$my['level']):?>
					    <?php for($i=1;$i<$d['theme']['file_upload_qty']+1;$i++):?>
						 <div class="form-group">
								<label class="col-sm-2 text-center" for="">첨부파일<?php echo $i?><span class="rb-form-required text-danger"></span></label>
								<div class="col-sm-10">
									<div class="input-group">
					                <span class="input-group-btn">
					                    <span class="btn btn-primary btn-file">
					                        찾아보기<input type="file" class="file-upload">
					                    </span>
					                </span>
					                <input type="text" readonly="" class="form-control">
                           </div>									
								</div>
						 </div>
						 <?php endfor?>					
		          <?php endif?>

				    <?php if($d['theme']['show_wtag']):?>
					 <div class="form-group">
							<label class="col-sm-2 text-center" for="">태그<span class="rb-form-required text-danger"></span></label>
							<div class="col-sm-10">
								<input type="text" name="tag" placeholder="검색태그를 입력해 주세요." value="<?php echo $R['tag']?>" id="" class="form-control">
								<span class="help-block">이 게시물을 가장 잘 표현할 수 있는 단어를 콤마(,)로 구분해서 입력해 주세요.</span>
							</div>
					 </div>					
		          <?php endif?>
		          
		          <?php if($d['theme']['show_trackback']):?>
			        <div class="form-group">
							<label class="col-sm-2 text-center" for="">트랙백<span class="rb-form-required text-danger"></span></label>
							<div class="col-sm-10">
								<input type="text" name="trackback" placeholder="엮을주소를 입력해 주세요." value="<?php echo $_SESSION['trackback']?>" id="" class="form-control">
								<span class="help-block">이 게시물을 보낼 트랙백주소를 입력해 주세요.</span>
							</div>
					 </div>					
		          <?php endif?>
		          <?php if((!$R['uid']||$reply=='Y')&&is_file($g['path_module'].$d['bbs']['snsconnect'])):?>
					    <div class="form-group">
							<label class="col-sm-2 text-center" for="">소셜연동<span class="rb-form-required text-danger"></span></label>
							<div class="col-sm-10">
								<?php include_once $g['path_module'].$d['bbs']['snsconnect']?> 에도 게시물을 등록합니다.
							</div>
							
					 </div>
		          <?php endif?>
		           
		           <div class="form-group">
							<label class="col-sm-2 text-center" for="">등록 후<span class="rb-form-required text-danger"></span></label>
							<div class="col-sm-10">								 
								<label class="radio-inline"><i></i><input type="radio" name="backtype"  id="backtype1" value="list" <?php if(!$_SESSION['bbsback'] || $_SESSION['bbsback']=='list'):?> checked<?php endif?>>목록으로 이동</label>
								<label class="radio-inline"><i></i><input type="radio" name="backtype"  id="backtype2" value="view" <?php if($_SESSION['bbsback']=='view'):?> checked<?php endif?>>본문으로 이동</label>
								<label class="radio-inline"><i></i><input type="radio" name="backtype"  id="backtype3" value="now" <?php if($_SESSION['bbsback']=='now'):?> checked<?php endif?>>이 화면 유지</label>
						   </div>
						</div>
				</fieldset>
             <div class="col-sm-offset-2 col-sm-10">
					  <input type="button" value="취소" class="btn btn-default" onclick="cancelCheck();" />
		           <input type="submit" value="확인" class="btn btn-primary" />
		           <a href="<?php echo $g['bbs_list']?>" class="btn btn-default pull-right" href="">목록</a>
				 </div>
			</form>	
		</div>
	</div><!-- panel panel-default-->

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
 $('.boot-select').selectpicker();

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
	       ['style', ['style']],
	       ['fontstyle', ['fontname','bold','strikethrough','italic','underline', 'clear']],
	       ['fontsize', ['fontsize']],
	       ['color', ['color']],
	       ['height', ['height']],
	       ['Layout', ['ul','ol','paragraph']],
	       <?php if($d['theme']['perm_photo']<=$my['level']):?>
	       ['picture', ['picture']],
	       <?php endif?>
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

