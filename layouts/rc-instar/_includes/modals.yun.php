
<!-- Modal -->
<div id="modal-login" class="modal" style="z-index:50">
	<div class="content">
	  	<!-- 로그인 마크업 Start -->
	  	<script src="<?php echo $g['url_root'];?>/../app/javascript/plugin/ezmark/js/jquery.ezmark.min.js"></script>
	  	<div class="shop_head_bar">
			<h2>로그인</h2>
			<a href="javascript:history.back();" class="backbtn"><img src="/data/skin/mobile_ver2_default/images/custom/shopM_arrowBack.png"></a>
		</div>
		<div class="login_box">
  			<form id="loginForm" name="LayoutLogForm" method="post" onsubmit="login_check();">
				<input type="hidden" name="r" value="">
				<input type="hidden" name="m" value="home">
				<input type="hidden" name="a" value="login_ajax">
				<input type="hidden" name="iframe" value="Y">
				<input type="hidden" name="referer" value="<?php echo $g['url_root']?>">
				<fieldset>
					<div class="input1">
						<input type="text" name="userid" id="userid" value="" placeholder="이메일 ID" tabindex="1" required="required" onKeydown="javascript:if(event.keyCode == 13) login_check();">
					</div>
					<div class="input1" style="margin-top:8px;">
						<input type="password" password="password" name="password" id="password" placeholder="비밀번호" tabindex="2" required="required" onKeydown="javascript:if(event.keyCode == 13) login_check();">
					</div>
					<div class="pdt10 clearbox txt_spacing">
						<div class="fleft"><div class="ez-checkbox"><input type="checkbox" name="idsave" id="idsave" value="checked" class="ez-hide"></div> <label for="idsave"><span>아이디 저장</span></label></div>
						<div class="fright"><a href="../member/find?mode=findid">ID 찾기 </a>/<a href="../member/find?mode=findpw"> 패스워드 찾기</a></div>
					</div>
					<div style="overflow:hidden;height:1px;margin:10px 0px 0px 0px;background-color:#ccc;"></div>
					<div id="message"></div>
					<button type="button" id="ripplelink" class="login_btn" data-action="go_login">로그인</button>
				</fieldset>
			</form>
		</div>

<?php
 include $g['path_module']."slogin/var/var.php";
 include $g['path_module']."slogin/lib/snsfunction.php";

 $naver_api = $slogin['naver']['callapi']; 
 $naver_api = str_replace("%3Dnaver&", "%3Dnaver%26start%3Dplay_m&", $naver_api);
 $naver_api = str_replace("www.", "", $naver_api);
?>
		<div class="snslogin_box">
			<a href="#none" class="sns-login-button" snstype="facebook" data-role="social-login" data-connect="https://graph.facebook.com/oauth/authorize?client_id=1693600344297349&redirect_uri=http%3A%2F%2Fwww.shotping.co.kr%2Fplay%2F%3Fr%3Dinstar%26m%3Dslogin%26a%3Dslogin%26sloginReturn%3Dfacebook%26start%3Dplay_m&scope=email%20user_birthday">
				<img class="bg" src="/data/skin/mobile_ver2_default/images/custom/login_facebook.png">
				<div class="txt">페이스북 로그인</div>
			</a>
			<a href="#none" class="sns-login-button" snstype="naver" data-role="social-login" data-connect="<?php echo $naver_api;?>">
				<img class="bg" src="/data/skin/mobile_ver2_default/images/custom/login_naver.png">
				<div class="txt">네이버 로그인</div>
			</a>
			<!--
			<a href="#none" class="sns-login-button" snstype="twitter">
				<img class="bg" src="/data/skin/mobile_ver2_default/images/custom/login_twitter.png" alt="sign in with twitter" title="트위터">
				<div class="txt">트위터 로그인</div>
			</a>
			<a href="#none" class="sns-login-button" snstype="kakao">
				<img class="bg" src="/data/skin/mobile_ver2_default/images/custom/login_kakao.png" alt="sign in with kakao" title="카카오">
				<div class="txt">카카오 로그인</div>
			</a>
			-->
		</div>


	    <!-- 로그인 마크업 End -->
	</div>   
</div>

<!-- 필터 모달 Start -->
<div id="modal-filters" class="modal" data-toggle="filterbox">
	<div class="content">
	  <!-- 필터 페이지 마크업 Start -->
  	  	<div class="shop_head_bar">
			<h2>필터</h2>
			<a href="javascript:history.back();" class="backbtn"><img src="/data/skin/mobile_ver2_default/images/custom/shopM_arrowBack.png"></a>
		</div>
	    <div class="dm-actual-body">
	        <section class="dm-content">
	            <div class="dm-filter-area">
	                <div class="dm-filter-box">
	                    <div class="dm-takeup-space">
	                        <div>
	                            <h3 class="dm-h3 dm-ft-default">성별</h3>
	                        </div>
	                        <div class="dm-filter-buttons">
	                            <label for="dm-sex-all"><div class="dm-filter-button balanced selected" data-toggle="select_sex">모두<input type="radio" id="dm-sex-all" name="mbr_sex" value="" checked ></div></label>
	                            <label for="dm-sex-male" ><div class="dm-filter-button balanced" data-toggle="select_sex">남성<input type="radio" id="dm-sex-male" name="mbr_sex" value="1"></div></label>
	                            <label for="dm-sex-female"><div class="dm-filter-button balanced" data-toggle="select_sex">여성<input type="radio" id="dm-sex-female" name="mbr_sex" value="2"></div></label>
	                        </div>
	                    </div>
	                </div>
	                <div class="dm-filter-box">
	                    <div class="dm-takeup-space">
	                        <div>
	                            <h3 class="dm-h3 dm-ft-default">국가</h3>
	                        </div>
	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-asia" data-toggle="select-continent" data-continent="asia">
	                                    아시아
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                            	<div data-continentbox="asia">
	                            		<?php echo getNationSelector('html_mobile',array('KOR','JPN','TWN','CHN','HKG','MAC','MYS','PHL','VNM'),array('1','','','','','','','',''));?>
	                            	</div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-europe" data-toggle="select-continent" data-continent="euro">
	                                    유럽
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="euro">
	                                    <?php echo getNationSelector('html_mobile',array('GBR','NLD','SWE','CHE','DEU','ITA','FRA','TUR','RUS'),array('','','','','','','','',''));?>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-northamerica" data-toggle="select-continent" data-continent="na">
	                                    북미
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="na">
                                        <?php echo getNationSelector('html_mobile',array('USA','CAN'),array('',''));?>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-centralamerica" data-toggle="select-continent" data-continent="la">
	                                    중남미
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="la">
                                        <?php echo getNationSelector('html_mobile',array('CHL','MEX','BRA'),array('','',''));?>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="dm-filter-layout">
	                            <div class="dm-left">
	                                <div class="dm-filter-button dm-oceania" data-toggle="select-continent" data-continent="oce">
	                                    오세아니아
	                                </div>
	                            </div>
	                            <div class="dm-right">
	                                <div data-continentbox="oce">
                                        <?php echo getNationSelector('html_mobile',array('NZL','AUS'),array('',''));?>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>

	                <div class="dm-filter-save">
	                    <div>
	                        <label for="dm-filter-save"><span class="dm-icon dm-icon-checkbullet"></span><span class="dm-label">이 설정을 저장하기</span><input type="checkbox" class="hidden" id="dm-filter-save" value="save" data-toggle="dm-filter-save"></label>
	                    </div>
	                    <div>
	                        <span class="dm-filter-button nation-reset" data-toggle="reset-nation">다시 선택</span>
	                        <span class="dm-filter-button nation-ok" data-role="feedSearch" data-mod="">확인</span>
	                    </div>
	                </div>
	            </div>
	        </section>
	    </div>
	    <!-- 필터 페이지 마크업 End -->
	</div>   
</div>


<script type="text/javascript">
/*
$(document).ready(function() {
  $("#ripplelink").rippler({
    effectClass      :  'rippler-effect'
    ,effectSize      :  1      // Default size (width & height)
    ,addElement      :  'svg'   // e.g. 'svg'(feature)
    ,duration        :  1000
  });
}); 
*/
	// 로그인 검사
	function login_check() {
		var f = document.LayoutLogForm;
		if (!f.userid.value || f.userid.value == '')
		{
			$("#message").html("<p style='color:red'>아이디나 이메일주소를 입력해 주세요.</p>");	
			f.userid.focus();
			return false;
		}
		if (!f.password.value || f.password.value == '')
		{
			$("#message").html("<p style='color:red'>패스워드를 입력해 주세요.</p>");	
			f.password.focus();
			return false;
		}

		var action = "<?php echo $g['s']?>/";
		var form_data = {
			r: '<?php echo $r?>',
			m: 'home',
			a: 'login_ajax',
			iframe: '<?php echo $r?>',
			referer: '<?php echo $g['url_root']?>',
			id: $("#userid").val(),
			pw: $("#password").val(),
			idsave: $("#idsave").val(),
			is_ajax: 1
		};
		$.ajax({
			type: "POST",
			url: action,
			data: form_data,
			success: function(response) {
				if(response == "success") {
					window.location.reload(true);
				}
				else {
					$("#message").html("<p style='color:red'>아이디 또는 비밀번호가 잘못되었습니다.</p>");	
					return false;
				}
			}
		});
		return false;
	}

	// 로그인 버튼을 클릭시 login_check를 실행합니다.
	$('[data-action="go_login"]').on('click', function(){
		login_check();
	});
	// 모달을 닫게 될 경우 로그인 에러 메시지칸을 초기화합니다.
	$('.backbtn').on('click', function(){
		$("#message").empty();	
	});
		// checkbox -> image mobile2 @2014-01-17
	 // to apply only to checkbox use:
	 $('input#idsave[type="checkbox"]').ezMark({
	  checkedCls: 'ez-checkbox-on'
	 });
</script>