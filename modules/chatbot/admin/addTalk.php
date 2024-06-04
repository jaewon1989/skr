<?php
if (!$_SESSION['upsescode'])
{
  $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
}
$sescode = $_SESSION['upsescode'];
if($uid){
    $R = getUidData($table['bbsdata'],$uid);
    $mbr_info = $R['name'].'('.$R['id'].')';
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
$sort	= $sort ? $sort : 'memberuid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 50;

$member_q ='auth=1';
// 키원드 검색 추가 
if ($keyw)
{
	$member_q .= " and (id like '%".$keyw."%' or name like '%".$keyw."%')";
}

$RCD = getDbArray($table['s_mbrdata'].' left join '.$table['s_mbrid'].' on memberuid=uid',$member_q,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_mbrdata'].' left join '.$table['s_mbrid'].' on memberuid=uid',$member_q);
$TPG = getTotalPage($NUM,$recnum);

?>
<!-- include summernote css/js-->
<?php getImport('summernote','summernote.min','0.8.2','js')?>
<?php getImport('summernote','lang/summernote-ko-KR','0.8.2','js')?>
<?php getImport('summernote','summernote','0.8.2','css')?>

<div class="row">
	<!-- 좌측 내용 시작 -->
	<div class="col-sm-4 col-lg-3"> 
   	    <div class="panel panel-default">  <!-- 메뉴 리스트 패털 시작 -->
   			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-file-text-o fa-2x"></i>
				</div>
				<h4 class="panel-title">
					회원 리스트 
					<span class="pull-right">
						<button type="button" class="btn btn-default btn-xs<?php if(!$_SESSION['sh_ad_member_search']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#panel-search" data-tooltip="tooltip" title="검색필터" onclick="sessionSetting('sh_ad_member_search','1','','1');"><i class="glyphicon glyphicon-search"></i></button>
					</span>
				</h4>
			</div>
			<div id="panel-search" class="collapse<?php if($_SESSION['sh_ad_member_search']):?> in<?php endif?>">
				<form role="form" action="<?php echo $g['s']?>/" method="get">
				<input type="hidden" name="r" value="<?php echo $r?>">
				<input type="hidden" name="m" value="<?php echo $m?>">
				<input type="hidden" name="module" value="<?php echo $module?>">
				<input type="hidden" name="front" value="addAdv">
				<input type="hidden" name="recnum" value="<?php echo $recnum?>">

					<div class="panel-heading rb-search-box">
						<div class="input-group">
							<div class="input-group-addon"><small>출력수</small></div>
							<div class="input-group-btn">
								<select class="form-control" name="recnum" onchange="this.form.submit();">
							    <option value="10"<?php if($recnum==10):?> selected<?php endif?>>10</option>
								<option value="15"<?php if($recnum==15):?> selected<?php endif?>>15</option>
								<option value="30"<?php if($recnum==30):?> selected<?php endif?>>30</option>
								<option value="60"<?php if($recnum==60):?> selected<?php endif?>>60</option>
								<option value="100"<?php if($recnum==100):?> selected<?php endif?>>100</option>
								</select>
							</div>
						</div>
					</div>
					<div class="rb-keyword-search input-group input-group-sm">
						<input type="text" name="keyw" class="form-control" value="<?php echo $keyw?>" placeholder="아이디 or 이름">
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit">검색</button>
						</span>
					</div>
				</form>
			</div>
			<div class="panel-body" style="border-top:1px solid #DEDEDE;height:100%;overflow:auto;">
				<?php if($NUM):?>
				<div class="list-group" id="addAdv-mlist">
				
					<?php while($M = db_fetch_array($RCD)):?>
					<a href="#" class="list-group-item" data-act="sel-mbr" data-uid="<?php echo $M['memberuid']?>" data-info="<?php echo $M['name']?>(<?php echo $M['id']?>)" >
			    		<?php echo $M['name']?>(<?php echo $M['id']?>)
			    	</a>
					<?php endwhile?>
				</div>
				
				<?php else:?>
				<div class="none">등록된 회원이 없습니다.</div>
				<?php endif?>
				
            </div>  
        	<div class="panel-footer rb-panel-footer">
				<ul class="pagination">
				<script>getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
				<?php //echo getPageLink(5,$p,$TPG,'')?>
				</ul>
			</div>
		</div> <!-- 좌측 패널 끝 -->  
	
    </div><!-- 좌측  내용 끝 -->

    <!-- 우측 내용 시작 -->	
	<div class="col-sm-8 col-lg-9">
		<form name="writeForm" method="post" action="<?php echo $g['s']?>/" onsubmit="return writeCheck(this);" class="form-horizontal" role="form" enctype="multipart/form-data">
		    <input type="hidden" name="r" value="<?php echo $r?>" />
		    <input type="hidden" name="m" value="bbs" />
		    <input type="hidden" name="a" value="adm_write" />
		    <input type="hidden" name="c" value="<?php echo $c?>" />
		    <input type="hidden" name="cuid" value="<?php echo $_HM['uid']?>" />
		    <input type="hidden" name="bid" value="talkLog" />
		    <input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
		    <input type="hidden" name="pcode" value="<?php echo $date['totime']?>" />
		    <input type="hidden" name="sess_Code" value="<?php echo $sescode?>_<?php echo $my['uid']?>_<?php echo $B['uid']?>" />
		    <input type="hidden" name="upfiles" id="upfilesValue" value="<?php echo $reply=='Y'?'':$R['upload']?>" />
		    <input type="hidden" name="saveDir" value="<?php echo $g['path_file'].$module.'/'?>" /> <!-- 파일저장 폴더 -->
		    <input type="hidden" name="html" value="HTML" />
		    <input type="hidden" name="mbruid" value="<?php echo $R['mbruid']?>" />
            <div class="form-group">
	           <label for="subject" class="col-xs-1 col-form-label" style="padding-left:0;padding-top:8px">제목</label>
	           <div class="col-xs-11">
	               <input type="text" name="subject" value="<?php echo $R['subject']?>" placeholder="제목을 입력해 주세요." class="form-control" id="subject">
	           </div>
	       </div>
	        <div class="form-group">
	           <label for="mbr_info" class="col-xs-1 col-form-label" style="padding-left:0;padding-top:8px">회원</label>
	           <div class="col-xs-11">
	               <input type="text" name="mbr_info" value="<?php echo $mbr_info?>" placeholder="좌측 회원리스트에서 선택해주세요." class="form-control" id="mbr_info" disabled>
	           </div>
	       </div>
		    <!-- 에디터 -->
	       <div class="form-group" style="margin-right:0">
				<textarea  id="summernote" name ="content" class="form-control" rows="3" onkeyup="resize(this)">
                       <?php echo getContents($R['content'],$R['html'])?>
            	</textarea>
			</div>
		    <!-- /에티터 -->
  		    <div class="form-group">
  		         <input type="submit" class="btn btn-primary btn-block" value="<?php echo $uid?'수정':'신규 등록'?>" />
  		    </div>
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



<script type="text/javascript">
//<![CDATA[

// 회원 선택 이벤트 
$('[data-act="sel-mbr"]').click(function(e){
	e.preventDefault();
	$('[data-act="sel-mbr"]').removeClass('active');
	$(this).addClass('active');
    var mbr_info = $(this).data('info');
    var mbr_uid = $(this).data('uid');

    $('input[name="mbr_info"]').val(mbr_info);
    $('input[name="mbruid"]').val(mbr_uid);

});

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
	       //['style', ['style']],
	       //['fontstyle', ['fontname','bold','strikethrough','italic','underline', 'clear']],
	       ['fontsize', ['fontsize']],
	       ['color', ['color']],
	       ['height', ['height']],
	       //['Layout', ['ul','ol','paragraph']],
	       ['picture', ['picture']]	
	       //['insert', ['link', 'table','hr']] 
		   // ['Misc', ['fullscreen','codeview','help']]		    
		  ],  
         
          callbacks: {
		    // 이미지 바로 넣기 
		    onImageUpload: function(files, editor, welEditable) {
		          Upload_file('img',files[0],editor,welEditable);
		    }
		    <?php if(!$uid):?> 
		    ,onInit: function() {
		         // 이걸 해줘야 editor empty 체크를 할 수 있다.   
                $('.note-editable').html('');		       
		    }
		    <?php endif?>
		   
		  }, 

       // 소스 편집창
		 codemirror: {
			mode: "text/html",
			indentUnit: 4,
			lineNumbers: true,
			matchBrackets: true,
			indentWithTabs: true,
			theme: 'monokai'
	    },
		 
      
     });
     
 });
 
 
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
        url : rooturl+'/?m=bbs&a=user_ajax_upload',
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
                	$('#summernote').summernote('insertImage', source, function($image){
                		$image.css('width','100%');
                	});
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

      // 회원정보 체크 
      if(f.mbruid && f.mbruid.value==''){
      	  alert('회원을 선택해주세요.       ');
          return false;
      }
       
      var content = $('#summernote').summernote('code');
      if(content.length == 0) {
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

//]]>
</script>