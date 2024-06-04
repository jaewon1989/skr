<?php
include_once $g['path_module'].$module.'/var/var.php';
include_once $g['path_module'].$module.'/_main.php';

$R=array();
$upfile = '';

if ($uid)
{
	$R=getUidData($table[$module.'product'],$uid);
}

// html --> markdown 변환 코드 
spl_autoload_register('getHtmlToMarkdownClass');
$converter = new Markdownify\ConverterExtra;
$mdContent=$converter->parseString(getContents($R['content'],'HTML'));
// use League\HTMLToMarkdown\HtmlConverter;
// $converter = new HtmlConverter(array('strip_tags'=>false));
// $mdContent=$converter->convert(getContents($R['content'],'HTML')); // blog_data 테이블에는 html 값이 없어서 'HTML' 로 넣어준다.

?>
<style>
#rb-body #textarea-wrapper{padding:0;padding-top:15px;border:none;}
</style>
<?php getImport('simplemde','simplemde.min','1.9.0','css')?>
<?php getImport('bootstrap-tagsinput','bootstrap-tagsinput',false,'css') ?><!-- 태그 입력 -->
<div class="page-header">
	 <h4>상품<?php echo $R['uid']?'수정':'등록'?><?php if($uid):?> > <span class="label label-primary"><?php echo $R['name']?></span><?php endif?>
	 <div class="btn-group rb-btn-view pull-right">
			<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;cat=<?php echo $R['category']?>&amp;uid=<?php echo $R['uid']?>" class="btn btn-default">접속하기</a>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<li><a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $module?>&amp;cat=<?php echo $R['category']?>&amp;uid=<?php echo $R['uid']?>" target="_blank"><i class="glyphicon glyphicon-new-window"></i> 새창으로 보기</a></li>
			</ul>
	   </div>
	   </h4>
</div>
<div class="row">
	<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" enctype="multipart/form-data" onsubmit="return saveCheck(this);" class="form-horizontal">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="regis_product" />
	<input type="hidden" name="uid" value="<?php echo $R['uid']?>" />
	<input type="hidden" name="upload" value="<?php echo $R['upload']?>" />
	<input type="hidden" name="addoptions" value="<?php echo $R['addoptions']?>" />
	<input type="hidden" name="icons" value="<?php echo $R['icons']?>" />
	<input type="hidden" name="html" value="HTML" />
	<input type="hidden" name="featured_img" value="<?php echo $R['featured_img']?>" />
	<input type="hidden" name="joint" value="<?php echo $R['joint']?>" />


	      <div class="col-sm-4 col-lg-3">
	   	      <?php include $g['path_module'].$module.'/admin/_regis_left.php';?> <!-- 좌측 사이드메뉴 별도 구분관리  -->
	      </div>	
	       <!-- 우측 내용 시작 -->
	      <div id="tab-content-view" class="col-sm-8 col-lg-9">
	      	<div class="rb-content-padded">
	      		 <ul class="nav nav-tabs" role="tablist">
	      		 	<li<?php if($_COOKIE['regisTap']=='default'||!$_COOKIE['regisTap']):?> class="active"<?php endif?>><a href="#default-settings" role="tab" data-toggle="tab" onclick="_cookieSetting('regisTap','default');">기본정보</a></li>
	      	           <li<?php if($_COOKIE['regisTap']=='content'):?> class="active"<?php endif?>><a href="#content-settings" role="tab" data-toggle="tab" onclick="_cookieSetting('regisTap','content');">상세내역</a></li>
                      </ul>
			     <div class="tab-content" id="textarea-wrapper">
			     	      <div class="tab-pane<?php if($_COOKIE['regisTap']=='default'||!$_COOKIE['regisTap']):?> active<?php endif?>" id="default-settings">
				             <?php include $g['path_module'].$module.'/admin/_regis_default.php';?>
	                       </div>
	                        <div class="tab-pane<?php if($_COOKIE['regisTap']=='content'||!$_COOKIE['regisTap']):?> active<?php endif?>" id="content-settings">
				             <textarea class="form-control" rows="1" id="content" name="content" style="display:none;"><?php echo $mdContent?></textarea>
	                       </div>
	   
	                 </div>      
		      </div>
	            <div class="form-group" style="margin-top:30px;">
				<div class="col-md-offset-2 col-md-9">
					<button type="submit" class="btn btn-primary btn-lg">상품 <?php echo $uid?'수정':'등록'?>하기</button>
				</div>	
			 </div>
	      </div> <!-- 우측내용 끝 --> 
	
	</div>
      </form>
</div> <!-- .row 전체 box --> 

<!-- 태그입력 -->
<?php getImport('bootstrap-tagsinput','bootstrap-tagsinput.min',false,'js') ?>
<!-- 요약부분 글자수 체크 -->
<?php getImport('bootstrap-maxlength','bootstrap-maxlength.min',false,'js') ?>
<!-- 마크다운 에디터 플러그인 : https://github.com/NextStepWebs/simplemde-markdown-editor -->
<?php getImport('simplemde','simplemde','1.9.0','js')?>
<script type="text/javascript">
//<![CDATA[

var simplemde = new SimpleMDE({
        element: $("#content")[0],
    
        placeholder: "Type here...",
        spellChecker: false,
        toolbar: ["bold", "italic", "heading","|",
            "code",
            "quote",
            "unordered-list",
            "ordered-list",
             "|", 
             "link",
             "image",
             "table",
             "horizontal-rule",
            "|", 
            "preview",
            "side-by-side",
            "fullscreen",
            "|",
            "guide"
        ],
 });  



// simplemde 미리보기 기본세팅
//simplemde.toggleSideBySide(simplemde);    

// Bootstrap Button Select : http://bootsnipp.com/snippets/yvAp8
$(document).ready(function () {
       $(".btn-select").each(function (e) {
             var value = $(this).find("ul li.selected").html();
             if (value != undefined) {
                  $(this).find(".btn-select-input").val(value);
                  $(this).find(".btn-select-value").html(value);
            }
      });     
      $('.rb-editor-body').addClass('active');
      $('.editor-preview-side').addClass('rb-article');
      $(".editor-toolbar a").attr("data-toggle","tooltip");
});

$(document).on('click', '.btn-select', function (e) {
      e.preventDefault();
      var ul = $(this).find("ul");
      if ($(this).hasClass("active")) {
	      if (ul.find("li").is(e.target)) {
	            var target = $(e.target);
	            target.addClass("selected").siblings().removeClass("selected");
	            var value = target.html();
	            $(this).find(".btn-select-input").val(value);
	            $(this).find(".btn-select-value").html(value);
            }
            ul.hide();
            $(this).removeClass("active");
      }
      else {
            $('.btn-select').not(this).each(function () {
                  $(this).removeClass("active").find("ul").hide();
            });
            ul.slideDown(300);
            $(this).addClass("active");
      }
});

$(document).on('click', function (e) {
      var target = $(e.target).closest(".btn-select");
      if (!target.length) {
            $(".btn-select").removeClass("active").find("ul").hide();
      }
});

 // 요약 : bootstrap-maxlength : https://github.com/mimo84/bootstrap-maxlength/
 $('#meta-description-content').maxlength({
      alwaysShow: true,
      threshold: 10,
      warningClass: "label label-info",
      limitReachedClass: "label label-danger"
 });

 // 태그 : bootstrap-tagsinput : https://github.com/bootstrap-tagsinput/bootstrap-tagsinput
 $('[data-role="tagsinput"]').tagsinput({
      maxChars: 8
 });

// 태그 추가/삭제 이벤트 
$(function(){
      var tagInput='[data-role="tagsinput"]'; 
	var preview_tag=$('[data-role="preview-tag"]');
      var new_tag;
      
      // 추가 이벤트 
	$(tagInput).on('itemAdded', function(e) {
             var old_tag=$(preview_tag).text(); 
	       var tag='#'+e.item;

	       if(old_tag=='') new_tag=tag;
	       else new_tag=old_tag+', '+tag;
	       
	       $(preview_tag).text(new_tag);
	});

	// 삭제 이벤트 
	$(tagInput).on('itemRemoved', function(e) {
		var old_tag=$(preview_tag).text(); 
		var tag=e.item; // 삭제되는 태그 
		var stag='#'+tag;// 태그앞에 # 추가 
		var tagArr=old_tag.split(', ');
           
           if(tagArr[0]=='') tagArr.splice(0,1); // 태그가 없는 경우 첫번째 빈값은 삭제 
           tagArr.splice($.inArray(stag, tagArr),1); // 삭제된 태그 태그배열에서 삭제 
           new_tag=tagArr.join(', '); 
           $(preview_tag).text(new_tag);
	});
});

//카테고리 선택되는 순간 이벤트 
 $('[data-role="category-checkbox"]').click(function() {
 	 var preview_cat=$('[data-role="preview-category"]');
 	 var preview_cat_text=$('[data-role="preview-category"]').text();
       var cat_arr=preview_cat_text.split(', '); // 콤마로 분리해서 배열 생성 
       if(cat_arr[0]=='')  cat_arr.splice(0,1); // 카테고리 없는 경우 공백 삭제         
      
       var cat=$(this).data('name');
       var is_selected,new_cat='';

       // 체크되었는지 체크 
       var is_checked=$(this).prop('checked');
       
      // 기존에 선택되었는지 체크 
      if ($.inArray(cat, cat_arr) !='-1') is_selected=true;
      else is_selected=false;

      // 체크 선택/비선택 여부 및 기존에 체크되었는지 체크 
      if(is_checked){
            if(is_selected==false) cat_arr.push(cat);
      }else{
      	 if(is_selected==true){
      	 	cat_arr.splice($.inArray(cat, cat_arr),1);
      	 }
      } 
      // 배열 콤마로 분리 
      var cat_text=cat_arr.join(', '); 

       // 카테고리 미리보기 업데이트 
      $(preview_cat).text(cat_text);

});

function saveCheck(f)
{
    if (f.name.value == '')
	{
		alert('블로그 이름을 입력해 주세요.     ');
		f.name.focus();
		return false;
	}
	if (f.blog.value == '')
	{
		if (f.id.value == '')
		{
			alert('블로그 아이디를 입력해 주세요.      ');
			f.id.focus();
			return false;
		}
		if (!chkFnameValue(f.id.value))
		{
			alert('블로그 아이디는 영문 대소문자/숫자/_ 만 사용가능합니다.      ');
			f.id.value = '';
			f.id.focus();
			return false;
		}
	}
	
     if (confirm('정말로 실행하시겠습니까?         '))
     {
	      getIframeForAction(f);
		f.submit();
	}
			
}

// system script
function _cookieSetting(name,tab)
{
	setCookie(name,tab,1);
}

// shop script 
function OpenWindowX(url)
{
	window.open(url,'','top=0,left=0,width=100px,height=100px,status=yes,resizable=no,scrollbars=yes');
}
function ToolCheck(compo)
{
	frames.editFrame.showCompo();
	frames.editFrame.EditBox(compo);
}
function copyOption(xvalue)
{
	var f = document.procForm;
	var i,val,exp,sub;
	var v1='';
	var v2='';
	var v3='';
	var v4='';
	if (xvalue != '')
	{
		val = xvalue.split('^');
		exp = val[1].split(',');
		f._op_name.value = val[0];
		f._op_type.value = val[2];
		f._op_pilsu.checked = val[3] == 'checked' ? true : false;
		for (i = 0; i < exp.length; i++)
		{
			if (exp[i] == '') continue
			
			sub = exp[i].split('=');
			v1 += sub[0] + ',';
			v2 += sub[1] + ',';
			v3 += sub[2] + ',';
			v4 += sub[3] + ',';
		}
		f._op_value.value = v1;
		f._op_price.value = v2;
		f._op_stock.value = v3;
		f._op_point.value = v4;
		f._op_value.focus();
		if (v2.replace(/,/g,''))
		{
			f._ck_price.checked = true;
			getId('op_price').style.display = 'block';
		}
		else {
			f._ck_price.checked = false;
			getId('op_price').style.display = 'none';
		}
		if (v3.replace(/,/g,''))
		{
			f._ck_stock.checked = true;
			getId('op_stock').style.display = 'block';
		}
		else {
			f._ck_stock.checked = false;
			getId('op_stock').style.display = 'none';
		}
		if (v4.replace(/,/g,''))
		{
			f._ck_point.checked = true;
			getId('op_point').style.display = 'block';
		}
		else {
			f._ck_point.checked = false;
			getId('op_point').style.display = 'none';
		}
	}
	else {
		f._op_name.value = '';
		f._op_type.value = 'select';
		f._op_pilsu.checked = true;
		f._op_value.value = '';
		f._op_price.value = '';
		f._op_stock.value = '';
		f._op_point.value = '';
		f._ck_price.checked = false;
		getId('op_price').style.display = 'none';
		f._ck_stock.checked = false;
		getId('op_stock').style.display = 'none';
		getId('op_point').style.display = 'none';
		f._op_name.focus();
	}
}
function addOption()
{
	moveOption();
	var f = document.procForm;
	var i;
	var val = '';
	var valx,valy,valz,vala;
	if (f._op_name.value == '')
	{
		alert('옵션명을 입력해 주세요.    ');
		f._op_name.focus();
		return false;
	}
	if (f._op_value.value == '')
	{
		alert('옵션항목을 입력해 주세요.    ');
		f._op_value.focus();
		return false;
	}
	valx = f._op_value.value.split(',');
	valy = f._op_price.value.split(',');
	valz = f._op_stock.value.split(',');
	vala = f._op_point.value.split(',');
	for (i = 0; i < valx.length; i++)
	{
		if (valx[i] == '') continue;
		val += valx[i];
		val += '=' + (valy[i]?valy[i]:'');
		val += '=' + (valz[i]?valz[i]:'');
		val += '=' + (vala[i]?vala[i]:'');
		val += ',';
	}
	if (getId('opaddbtn').value == '옵션추가')
	{
		f.addoptions.value += f._op_name.value + '^' + val + '^' + f._op_type.value + '^' + (f._op_pilsu.checked == true ? 'checked' : '') + '^|';
	}
	else {
		var l = f.addoptions.value.split('|');
		var tmp = '';
		for (i = 0; i < l.length; i++)
		{
			if (l[i] == '') continue;
			if (opi == i)
			{
				tmp += f._op_name.value + '^' + val + '^' + f._op_type.value + '^' + (f._op_pilsu.checked == true ? 'checked' : '') + '^|';
			}
			else {
				tmp += l[i] + '|';
			}
		}
		f.addoptions.value = tmp;
		getId('opaddbtn').value = '옵션추가';
	}
	
	getOption();
	f._op_name.value = '';
	f._op_type.value = 'select';
	f._op_pilsu.checked = true;
	f._op_value.value = '';
	f._op_price.value = '';
	f._op_stock.value = '';
	f._op_point.value = '';
	f._op_name.focus();
}
function chkOption(obj,layer)
{
	var f = document.procForm;
	if (obj.checked == true)
	{
		getId(layer).style.display = 'block';
		eval('f._'+layer).focus();
	}
	else {
		getId(layer).style.display = 'none';
	}
}
function optionChk(n)
{
	var ol = getId('option_layer');
	if (n == 1)
	{
		ol.style.display = 'block';
	}
	else {
		ol.style.display = 'none';
	}
}
function getOption()
{
	var f = document.procForm;
	var option = f.addoptions.value;
	var valx,valy,valz,valk;
	var result = '<ul id="optionul" class="optionul">';
	var i,y;
	var j=0;
	if (option != '')
	{
		var oparr = option.split('|');
		for (i = 0; i < oparr.length; i++)
		{
			if (oparr[i] == '') continue;
			j++;
			valx = oparr[i].split('^');
			valy= valx[1].split(',');
			result += '<li>';
			result += '<table cellspacing="0" cellpadding="0"><tr>';
			result += '<td class="td5">';
			result += '<img src="<?php echo $g['img_module_admin']?>/btn_del_02.gif" width="30" height="16" alt="제외" class="hand" onclick="delOption('+i+');" /> ';
			result += '<img src="<?php echo $g['img_module_admin']?>/btn_modify_02.gif" width="30" height="16" class="hand" alt="수정" onclick="modifyOption('+i+');" /> ';
			result += '</td>';
			result += '<td class="td6">';
			result += '<img src="<?php echo $g['img_core']?>/_public/ico_drag.gif" alt="위치이동" class="move" />';
			result += '</td>';
			result += '<td class="td7"><div title="'+valx[0]+'">'+valx[0]+'</div></td>';
			result += '<td class="td8">';
			result += '<input type="checkbox" name="opcart[]" class="opcart" value="'+i+'" />';
			if (valx[2]!='select'&&valx[2]!='input') result += '<div>';
			if (valx[2]=='select')
			{
				result += '<select>';
				result += '<option value="">-선택하세요-</option>';
				for	(y = 0; y < valy.length; y++)
				{
					if (valy[y])
					{
						valz = valy[y].split('=');
						result += '<option value="">'+valz[0];
						if (valz[1])
						{
							if (valz[1] > 0)
							{
								result += ' (+'+commaSplit(valz[1])+')';
							}
							if (valz[1] < 0)
							{
								result += ' (-'+commaSplit(-valz[1])+')';
							}
						}
						if (valz[3])
						{
							result += ' (추가적립:'+commaSplit(valz[3])+')';
						}
						if (valz[2] == '0')
						{
							result += ' (품절)';
						}
						result += '</option>';
					}
				}
				result += '</select>';
			}
			else if (valx[2]=='input')
			{
				valz = valy[0].split('=');
				valk = valy[1].split('=');
				result += '<input type="text" name=""'+(valz[0]?' style="width:'+valz[0]+'px"':'')+' />' + valk[0];
			}
			else
			{
				for	(y = 0; y < valy.length; y++)
				{
					if (valy[y])
					{
						valz = valy[y].split('=');
						result += '<input type="'+valx[2].replace('br','')+'" name="" value="">'+valz[0];
						if (valz[1])
						{
							if (valz[1] > 0)
							{
								result += ' <span class="opprice1">(+'+commaSplit(valz[1])+')</span>';
							}
							if (valz[1] < 0)
							{
								result += ' <span class="opprice2">(-'+commaSplit(-valz[1])+')</span>';
							}
						}
						if (valz[3])
						{
							result += ' (추가적립:'+commaSplit(valz[3])+')';
						}
						if (valz[2] == '0')
						{
							result += ' <span class="opprice1">(품절)</span>';
						}
						if (valx[2].indexOf('br') != -1)
						{
							result += '<br />';
						}
					}
				}
			}
			if (valx[2]!='select'&&valx[2]!='input') result += '</div>';
			result += '</td>';
			result += '</tr>';
			result += '</table>';
			result += '</li>';
		}
	}
	result += '</ul>';
	if (j) getId('ophtml').innerHTML = result;
	else getId('ophtml').innerHTML = '<ul id="optionul" class="optionul"><li class="none">추가된 옵션이 없습니다.</li></ul>';
	dragsort.makeListSortable(getId("optionul"));
}
function priceTopoint(obj,price1,price2,per,fcs)
{
	var ENABLE = '0123456789,';
	var priceF = eval('obj.form.'+price1);
	var pointF = eval('obj.form.'+price2);
	var pointP = eval('obj.form.'+per);
	var priceV = filterNum(priceF.value);
	var _point = filterNum(pointP.value);
	var _round = 10;
	if (getTypeCheck(priceF.value,ENABLE) && getTypeCheck(pointP.value,ENABLE+'.') && pointP.value)
	{
		var xprice = parseInt(priceV * _point / 100);
		//pointF.value = commaSplit(xprice - (xprice%_round));
		pointF.value = (xprice - (xprice%_round));
		if (fcs)
		{
			eval('obj.form.'+fcs).focus();
		}
	}
}
function delOption(n)
{
	if (!confirm('정말로 제외하시겠습니까?     '))
	{
		return false;
	}
	var f = document.procForm;
	var l = f.addoptions.value.split('|');
	var i;
	var val = '';
	for (i = 0; i < l.length; i++)
	{
		if (l[i] == '') continue;
		if (n != i)
		{
			val += l[i] + '|';
		}
	}
	f.addoptions.value = val;
	getOption();
}
function modifyOption(n)
{
	var f = document.procForm;
	var l = f.addoptions.value.split('|');
	copyOption(l[n]);
	getId('opaddbtn').value = '옵션수정';
	opi = n;
}
function moveOption()
{
	var f = document.procForm;
	var l = f.addoptions.value.split('|');
    var c = document.getElementsByName('opcart[]');
	var i;
	var val = '';
	for (i = 0; i < c.length; i++)
	{
		val += l[c[i].value] + '|';
	}
	f.addoptions.value = val;
}
function useOptionFlag(n)
{
	var ol = getId('option_layer');
	if (n == 1)
	{
		ol.style.display = 'block';
	}
	else {
		ol.style.display = 'none';
	}
}
function printStr(obj)
{
	getId('printStr').innerHTML = '('+obj.title+')';
}
function hideStr()
{
	getId('printStr').innerHTML = '';
}

function saveCheck(f)
{
	if (f.name.value == '')
	{
		alert('상품명을 입력해 주세요.       ');
		f.name.focus();
		return false;
	}
	if (f.category.value == '')
	{
		alert('등록할 분류(카테고리)를 선택해 주세요.       ');
		f.category.focus();
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
           $('input[name="upload"]').val(new_upfiles);
      }  
      
      // 관련상품 
      var joints=$('input[name="selected_product[]"]').map(function(){return $(this).val()}).get();
      var new_joints='';
      if(joints){
           for(var i=0;i<joints.length;i++) 
           {  
                 new_joints+=joints[i];
            }
           $('input[name="joint"]').val(new_joints);
      }  
      console.log(new_joints);

      // 내용 체크 및 포커싱  ie 에서는 안됨 
      var content=simplemde.value();
	if (content ==' ')
	{
	      alert('상품 상세내역을 입력해 주세요.       ');
            return false;
	}

    var l = document.getElementsByName('iconmembers[]');
    var n = l.length;
    var i;
	var s = '';
    for (i = 0; i < n; i++)
    {
        if(l[i].checked == true)
		{
			s += l[i].value + ',';
		}
    }
	f.icons.value = s;
	moveOption();
	return confirm('정말로 실행하시겠습니까?         ');
}

function eventChk(n)
{
	if ( n == 1)
	{
		getId('halin_layer').style.display = 'block';
	}
	else {
		getId('halin_layer').style.display = 'none';
	}
}

//]]>
</script>

