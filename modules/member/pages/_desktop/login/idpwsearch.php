<?php
if($my['photo']){
  $avatar_src = $g['url_root'].'/_var/avatar/'.$my['photo'];
  $avatar_bg = 'style="background: url('.$avatar_src.') center center no-repeat;background-size:150px 150px;"';   
} 
?>
<section id="cb-lost-found">
    <div class="cb-lost-found-wrapper" id="photo-container" style="position:relative;">
        <h1>아이디/비밀번호 찾기</h1>
 
        <div class="cb-lost-found-tab">
            <ul class="cb-cell-layout nav nav-tabs" role="tablist">
                <li class="cb-cell cb-tab-item cb-userinfo-change active">
                    <a href="#settings-info" role="tab" data-toggle="tab">아이디 찾기</a>
                </li>
                <li class="cb-cell cb-tab-item cb-password-change">
                    <a href="#settings-pw" role="tab" data-toggle="tab">비밀번호 찾기</a>
                </li>             
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="settings-info">
            <form name="idsearchForm" class="form-horizontal" role="form" onsubmit="return idsearchCheck(this);">      
                <div class="cb-inputnaked">
                    <input type="text" placeholder="이메일 계정" name="id">
                </div>
                <input type="submit" value="확인 하기" id="btn-idsearch">
                <p class="user-guide" id="idsearch-result">
                   보톡스의 아이디는 이메일 주소입니다.<br/>
                   이메일 주소를 입력해주시면 가입여부를 알려드립니다. 
                </p>              

            </form> 
        </div>
        <div class="tab-pane fade" id="settings-pw"> 
            <form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" method="post" onsubmit="return idCheck(this);">
            <input type="hidden" name="r" value="<?php echo $r?>" />
            <input type="hidden" name="m" value="<?php echo $m?>" />
            <input type="hidden" name="mod" value="<?php echo $mod?>" />
            <input type="hidden" name="a" value="id_auth" />
                <div class="cb-inputnaked">
                    <input type="text" placeholder="이름" name="name" >
                </div>
                <div class="cb-inputnaked">
                    <input type="text" placeholder="아이디" name="email">
                </div>
             
                <input type="submit" value="비밀번호 요청">
                <ul class="user-guide">
                   <li><span class="danggu">※</span> 메일수신이 안되었을 경우 스펨함을 확인해 보시고 기타사항에 대해서는<br/>
                       <span class="danggu"></span> 관리자에게 문의해주세요. </li>
                   <li><span class="danggu">※</span> 임시비밀번호로 로그인한 후 비밀번호를 변경해주세요.</li> 
                </ul>  
            </form>
        </div>
      
     </div>        
</section>
<script type="text/javascript">
//<![CDATA[

// 아이디 찾기폼 체크  
function idsearchCheck(f)
{
	var obj = f.id;
    if (obj.value == '')
    {
        alert('이메일 계정을 입력해 주세요.');
        setTimeout(function(){
           obj.focus();           
        },10);
        return false;	        
    }else{
    	if (!chkEmailAddr(obj.value))
        {
              alert('이메일 형식이 아닙니다.');
		      setTimeout(function(){
		           obj.focus();
		      },10);
		    return false;  
        }
    } 
    var id = obj.value;
    $.post(rooturl+'/?r='+raccount+'&m=member&a=search_id',{
        id : id
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        var content = result.content;
        var message = result.message;
        $('#idsearch-result').html(content);
        if(message=='200'){
        	$('#btn-idsearch').val('다시 확인 하기');
        }
    });  

    return false; 
}
function idCheck(f)
{
	if (f.name.value == '')
	{
		alert('이름을 입력해 주세요.   ');
		f.name.focus();
		return false;
	}
	if (f.email.value == '')
	{
		alert('이메일을 입력해 주세요.   ');
		f.email.focus();
		return false;
	}

	getIframeForAction(f);
    f.submit();

}
//]]>
</script>