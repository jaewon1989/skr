<article id="pages-signup">
	<form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="c" value="<?php echo $c?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	<input type="hidden" name="a" value="join" />
	<input type="hidden" name="check_id" value="0" />
	<input type="hidden" name="check_nic" value="0" />
	<input type="hidden" name="check_email" value="1" />
	<input type="hidden" name="comp" value="<?php echo $comp?>" />





	<div class="row">
		<div class="col-xs-8 offset-xs-2">

			<div class="page-header">
				<h2>가입정보 입력 <small>가입양식을 작성 후 '확인' 버튼을 클릭해 주세요. </small></h2>
			</div>

			<p><span class="text-danger">*</span> 표시가 있는 항목은 반드시 입력해야 합니다.</p>

			<div class="panel panel-default">
			    <div class="panel-heading">

			    </div>
			    <div class="panel-body">


						<div class="form-group row">
						  <label for="id" class="col-xs-3 col-form-label rb-form-required"><span class="text-danger">*</span> 아이디</label>
						  <div class="col-xs-8">
								<div class="input-group">
						      <input type="text" class="form-control" name="id" id="id" placeholder="4~12자의 영문(소문자)과 숫자만 사용가능." onblur="sameCheck(this,'hLayerid');">
						      <span class="input-group-btn">
						        <button class="btn btn-secondary" type="button" id="rb-idcheck" disabled><span id="hLayerid">유효성 결과</span></button>
						      </span>
						    </div><!-- /.input-group -->
						  </div>
						</div>


						<hr>

						<div class="form-group">
							<label for="id" class="col-sm-3 control-label"><span class="text-danger">*</span> 아이디<span class=""></span></label>
							<div class="col-sm-8">
								<div class="input-group">
								    <input type="text" class="form-control" name="id" id="id"  placeholder="4~12자의 영문(소문자)과 숫자만 사용할 수 있습니다." onblur="sameCheck(this,'hLayerid');">
								    <span class="input-group-btn">
								    	 <button type="button" class="btn btn-default" id="rb-idcheck" disabled ><span id="hLayerid">유효성 결과</span></button>
							        </span>
							   </div>
							</div>
						</div>
						<div class="form-group">
							<label for="pw1" class="col-sm-3 control-label"><span class="text-danger">*</span> 비밀번호<span class="rb-form-required"></span></label>
							<div class="col-sm-8">
								<input type="password" class="form-control" name="pw1" id="pw1" placeholder="4~12자의 영문과 숫자만 사용할 수 있습니다.">
							</div>
						</div>
						<div class="form-group">
							<label for="pw2" class="col-sm-3 control-label"><span class="text-danger">*</span> 비밀번호 확인</label>
							<div class="col-sm-8">
								<input type="password" class="form-control" name="pw2" id="pw2" placeholder="비밀번호를 한번 더 입력하세요.">
							</div>
						</div>
				<?php if($d['member']['form_qa']):?>
						<div class="form-group">
							<label for="pw_q" class="col-sm-3 control-label"><?php if($d['member']['form_qa_p']):?><span class="text-danger">*</span><?php endif?> 비번찾기 질문</label>
							<div class="col-sm-8">
								<select name="pw_q" class="form-control" >
									<option>질문을 선택하십시오</option>
									<option value=""></option>
									<?php $_pw_question=file($g['dir_module'].'var/pw_question.txt')?>
									<?php foreach($_pw_question as $_val):?>
									<option value="<?php echo trim($_val)?>"><?php echo trim($_val)?></option>
									<?php endforeach?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="pw_a" class="col-sm-3 control-label"><?php if($d['member']['form_qa_p']):?><span class="text-danger">*</span><?php endif?> 비번찾기 답변</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="pw_a" placeholder="답변을 입력해 주세요">
								<span class="help-block">
									비밀번호찾기 질문에 대한 답변을 혼자만 알 수 있는 단어나 기호로 입력해 주세요.
									비밀번호를 찾을 때 필요하므로 반드시 기억해 주세요.
									</span>
							</div>
						</div>
					<?php endif?>

						<div class="form-group">
							<label for="name" class="col-sm-3 control-label"><span class="text-danger">*</span> 이름<span class="rb-form-required"></span></label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="name" value="<?php echo $regis_name?>" placeholder="실명을 입력해 주세요.">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="email" class="col-sm-3 control-label"><span class="text-danger">*</span> 이메일<span class="rb-form-required"></span></label>
							<div class="col-sm-8">
								<div class="input-group">
								    <input type="email" class="form-control" name="email" id="email" value="" onblur="sameCheck(this,'hLayeremail');" placeholder="비밀번호 잊어버렸을 때 확인 받을 수 있습니다.">
  								    <span class="input-group-btn">
                                 <button class="btn btn-default" disabled><span id="hLayeremail">유효성 결과</span></button>
								    </span>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remail" value="1" checked="checked" > 뉴스레터나 공지메일을 수신 하겠습니다.
									</label>
								</div>
								<?php if($d['member']['join_auth']==3):?>
								   	<div class="alert alert-danger alert-dismissable small">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<i class="fa fa-info-circle fa-lg"></i> 가입후 입력하신 이메일로 인증메일이 발송되며 인증을 거쳐야만 가입승인이 이루어집니다.
										</div>
		                   <?php endif?>
							 </div>
						</div>
				<?php if($d['member']['form_tel2']):?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_tel2_p']):?><span class="text-danger">*</span><?php endif?> 휴대폰</label>
							<div class="col-sm-8">
								<div class="clearfix">
									<input type="tel" name="tel2_1" class="form-control input-sm rb-tel-num-1" maxlength="3">
									<span class="rb-divider"></span>
									<input type="tel" name="tel2_2" class="form-control input-sm rb-tel-num-2" maxlength="4">
									<span class="rb-divider"></span>
									<input type="tel" name="tel2_3" class="form-control input-sm rb-tel-num-3" maxlength="4">
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="sms" value="1" checked="checked"> 알림 SMS를 받겠습니다.
									</label>
								</div>
							</div>
						</div>
				 <?php endif?>

             <?php if($d['member']['form_nic']):?>
						<div class="form-group">
							<label for="for" class="col-sm-3 control-label"><?php if($d['member']['form_nic_p']):?><span class="text-danger">*</span><?php endif?> 닉네임</label>
							<div class="col-sm-8">
								<div class="input-group">
								   <input type="text" name="nic" class="form-control" id="nic" onblur="sameCheck(this,'hLayernic');" placeholder="자신을 표현할 수 있는 단어로 20자까지 자유롭게 입력">
								   <span class="input-group-btn">
                                <button class="btn btn-default" disabled><span id="hLayernic">유효성 결과</span></button>
								   </span>
								</div>
							</div>
						</div>
					<?php endif?>

					<?php if($d['member']['form_sex']):?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_sex_p']):?><span class="text-danger">*</span><?php endif?> 성별</label>
							<div class="col-sm-8">
							  <label class="radio-inline">
								   <input type="radio" name="sex" value="1"<?php if($regis_jumin2&&(substr($regis_jumin2,0,1)%2)==1):?> checked="checked"<?php endif?> />남성
		                  </label>
		                  <label class="radio-inline">
		                     <input type="radio" name="sex" value="2"<?php if($regis_jumin2&&(substr($regis_jumin2,0,1)%2)==0):?> checked="checked"<?php endif?> />여성
		                   </label>
							</div>
						</div>
					<?php endif?>

					<?php if($d['member']['form_birth']):?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_birth_p']):?><span class="text-danger">*</span><?php endif?> 생년월일</label>
							<div class="col-sm-8 clearfix">
								<select name="birth_1" class="form-control input-sm pull-left" style="width:80px">
									<option value="">년도</option>
						         <?php for($i = substr($date['today'],0,4); $i > 1930; $i--):?>
									   <option value="<?php echo $i?>"><?php echo $i?></option>
									<?php endfor?>
						     	</select>
								<select name="birth_2" class="form-control input-sm pull-left" style="width:80px;margin-left:5px">
									<option value="">월</option>
									<?php for($i = 1; $i < 13; $i++):?>
									<option value="<?php echo sprintf('%02d',$i)?>"<?php if($i==substr($regis_jumin1,2,2)):?> selected="selected"<?php endif?>><?php echo $i?></option>
									<?php endfor?>
								</select>
								<select name="birth_3" class="form-control input-sm pull-left" style="width:60px;margin-left:5px">
									<option value="">일</option>
									<?php for($i = 1; $i < 32; $i++):?>
									<option value="<?php echo sprintf('%02d',$i)?>"<?php if($i==substr($regis_jumin1,4,2)):?> selected="selected"<?php endif?>><?php echo $i?></option>
									<?php endfor?>
								</select>
								<div class="checkbox pull-left" style="margin-left:10px">
									<label>
										<input type="checkbox" name="birthtype" value="1" >
										음력
									</label>
								</div>
							</div>
						</div>
					<?php endif?>
					<?php if($d['member']['form_home']):?>
						<div class="form-group">
							<label for="home" class="col-sm-3 control-label"><?php if($d['member']['form_home_p']):?><span class="text-danger">*</span><?php endif?> 홈페이지</label>
							<div class="col-sm-8">
							    <input type="text" class="form-control" id="home" name="home"  placeholder="홈페이지를 입력해 주세요.">
							</div>
						</div>
					<?php endif?>
					<?php if($d['member']['form_tel1']):?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_tel1_p']):?><span class="text-danger">*</span><?php endif?> 전화번호</label>
							<div class="col-sm-8">
								<input type="tel" name="tel1_1" class="form-control input-sm rb-tel-num-1" maxlength="4">
								<span class="rb-divider"></span>
								<input type="tel" name="tel1_2" class="form-control input-sm rb-tel-num-2" maxlength="4">
								<span class="rb-divider"></span>
								<input type="tel" name="tel1_3" class="form-control input-sm rb-tel-num-3" maxlength="4">
								<span class="help-block"></span>
							</div>
						</div>
					<?php endif?>
					<?php if($d['member']['form_addr']):?>
					 <div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_addr_p']):?><span class="text-danger">*</span><?php endif?> 주소</label>
							<div class="col-sm-8">
								<div id="addrbox">
										<div class="clearfix" style="margin-bottom: 5px">
											<input type="text" name="zip_1" id="zip1" maxlength="3" size="3" class="form-control input-sm pull-left" readonly="" style="width:50px">
											<span class="rb-divider"></span>
											<input type="text" name="zip_2" id="zip2" maxlength="3" size="3" class="form-control input-sm pull-left" readonly="" style="width:50px;">
											<button type="button" class="btn btn-default btn-sm pull-left rb-zipsearch" onclick="openDaumPostcode();"><i class="fa fa-search"></i> 우편번호</button>
										</div>
										<input type="text" class="form-control" name="addr1" id="addr1" value="" readonly="">
										<input type="text" class="form-control" name="addr2" id="addr2" placeholder="우편번호를 먼저 선택해주세요.">
							   </div>
								<?php if($d['member']['form_foreign']):?>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="foreign" valule="1" onclick="foreignChk(this);"><span id="foreign_ment">해외거주자일 경우 체크해 주세요.</span>
									</label>
								 </div>
							    <?php endif?>
							</div>
						</div>
						<script src="http://dmaps.daum.net/map_js_init/postcode.js"></script>
						<script>
						    function openDaumPostcode() {
						        new daum.Postcode({
						            oncomplete: function(data) {
						                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
						                // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
						                document.getElementById('zip1').value = data.postcode1;
						                document.getElementById('zip2').value = data.postcode2;
						                document.getElementById('addr1').value = data.address;

						                //전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
						                //아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
						                //var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
						                //document.getElementById('addr').value = addr;

						                document.getElementById('addr2').focus();
						            }
						        }).open();
						    }
						</script>
					<?php endif?>
					<?php if($d['member']['form_job']):?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_job_p']):?><span class="text-danger">*</span><?php endif?> 직업</label>
							<div class="col-sm-8">
								<select name="job" class="form-control">
									<option value="">&nbsp;+ 선택하세요</option>
									<option value="">------------------</option>
									<?php $_job=file($g['dir_module'].'var/job.txt')?>
									<?php foreach($_job as $_val):?>
									<option value="<?php echo trim($_val)?>">ㆍ<?php echo trim($_val)?></option>
									<?php endforeach?>
									</select>
							</div>
						</div>
					<?php endif?>
					<?php if($d['member']['form_marr']):?>
					   <div class="form-group">
							<label for="" class="col-sm-3 control-label"><?php if($d['member']['form_marr_p']):?><span class="text-danger">*</span><?php endif?> 결혼 기념일</label>
							<div class="col-sm-8">
								<select name="marr_1" class="form-control input-sm pull-left" style="width:80px">
									<option value="">년도</option>
						         <?php for($i = substr($date['today'],0,4); $i > 1930; $i--):?>
									   <option value="<?php echo $i?>"><?php echo $i?></option>
									<?php endfor?>
						     	</select>
								<select name="marr_2" class="form-control input-sm pull-left" style="width:80px;margin-left:5px">
									<option value="">월</option>
									<?php for($i = 1; $i < 13; $i++):?>
									<option value="<?php echo sprintf('%02d',$i)?>"<?php if($i==substr($regis_jumin1,2,2)):?> selected="selected"<?php endif?>><?php echo $i?></option>
									<?php endfor?>
								</select>
								<select name="marr_3" class="form-control input-sm pull-left" style="width:60px;margin-left:5px">
									<option value="">일</option>
									<?php for($i = 1; $i < 32; $i++):?>
									<option value="<?php echo sprintf('%02d',$i)?>"<?php if($i==substr($regis_jumin1,4,2)):?> selected="selected"<?php endif?>><?php echo $i?></option>
									<?php endfor?>
								</select>
							</div>
						</div>
					<?php endif?>

				   <!-- 추가필드 시작 -->
					 <?php $_add = file($g['dir_module'].'var/add_field.txt')?>
					 <?php foreach($_add as $_key):?>
					 <?php $_val = explode('|',trim($_key))?>
					 <?php if($_val[6]) continue?>
					 <div class="form-group">
					   <label  for="<?php echo $_val[0]?>" class="col-sm-3 control-label"><?php if($_val[5]):?><span class="text-danger">*</span><?php endif?> <?php echo $_val[1]?></label>
						<div class="col-sm-8">
							    <!-- 일반 input=text -->
								<?php if($_val[2]=='text'):?>
								    <input type="text" id="<?php echo $_val[0]?>" name="add_<?php echo $_val[0]?>" value="<?php echo $_val[3]?>" class="form-control"/>
								<?php endif?>

								<!-- password input=text -->
								<?php if($_val[2]=='password'):?>
								     <input type="password" id="<?php echo $_val[0]?>" name="add_<?php echo $_val[0]?>" value="<?php echo $_val[3]?>" class="form-control" />
								<?php endif?>

                         <!-- select box -->
								<?php if($_val[2]=='select'): $_skey=explode(',',$_val[3])?>
									<select name="add_<?php echo $_val[0]?>" id="<?php echo $_val[0]?>" class="form-control">
										<option value="">&nbsp;+ 선택하세요</option>
										<?php foreach($_skey as $_sval):?>
										<option value="<?php echo trim($_sval)?>">ㆍ<?php echo trim($_sval)?></option>
										<?php endforeach?>
									</select>
								<?php endif?>

								<!-- input=radio -->
								<?php if($_val[2]=='radio'): $_skey=explode(',',$_val[3])?>
									<?php foreach($_skey as $_sval):?>
									    <label class="radio-inline">
										   	<input type="radio" name="add_<?php echo $_val[0]?>" value="<?php echo trim($_sval)?>" /><?php echo trim($_sval)?>
							           </label>
									 <?php endforeach?>
								<?php endif?>

								<!-- input=checkbox -->
								<?php if($_val[2]=='checkbox'): $_skey=explode(',',$_val[3])?>
										<?php foreach($_skey as $_sval):?>
								       <label class="checkbox-inline">
								           <input type="checkbox" name="add_<?php echo $_val[0]?>[]" value="<?php echo trim($_sval)?>" /><?php echo trim($_sval)?>
								       </label>
								      <?php endforeach?>
								<?php endif?>

								<!-- textarea -->
								<?php if($_val[2]=='textarea'):?>
								<textarea id="<?php echo $_val[0]?>" name="add_<?php echo $_val[0]?>" rows="5" class="form-control"><?php echo $_val[3]?></textarea>
								<?php endif?>

                     </div> <!-- .col-sm-8 -->
						</div> <!-- .form-group -->
					  <?php endforeach?>
                 <!-- 추가필드 끝 -->

					<?php if($d['member']['form_comp'] && $comp):?>
						<hr>
						<div class="page-header">
							<h4><i class="fa fa-building-o"></i> 기업정보  <small>소속된 기업명을 입력해 주세요.</small></h4>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><span class="text-danger">*</span> 사업자등록번호</label>
							<div class="col-sm-8">
								<input type="text" maxlength="3" class="form-control input-sm rb-comp-num-1" name="comp_num_1">
								<span class="rb-divider"></span>
								<input type="text" maxlength="2" class="form-control input-sm rb-comp-num-2" name="comp_num_2">
								<span class="rb-divider"></span>
								<input type="text" maxlength="5" class="form-control input-sm rb-comp-num-3" name="comp_num_3">
								<div class="rb-comp-type">
									<label class="radio-inline">
										<input type="radio" id="" name="comp_type" value="1"> 개인
									</label>
									<label class="radio-inline">
										<input type="radio" id="" name="comp_type" value="2"> 법인
									</label>
								</div>
								<span class="help-block"></span>
							</div>
						</div>

						<div class="form-group">
							<label for="comp_name" class="col-sm-3 control-label"><span class="text-danger">*</span> 회사명</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="comp_name" id="comp_name" placeholder="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_ceo" class="col-sm-3 control-label"><span class="text-danger">*</span> 대표자명</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="comp_ceo" id="comp_ceo" value="" placeholder="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_condition" class="col-sm-3 control-label"><span class="text-danger">*</span> 업태</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="comp_condition" id="comp_condition" value="" placeholder="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_item" class="col-sm-3 control-label"><span class="text-danger">*</span> 종목</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="comp_item" id="comp_item" value="" placeholder="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_tel" class="col-sm-3 control-label"><span class="text-danger">*</span> 대표전화</label>
							<div class="col-sm-8">
								<input type="tel" name="comp_tel_1" id="comp_tel_1" class="form-control input-sm rb-tel-num-1" maxlength="4">
								<span class="rb-divider"></span>
								<input type="tel" name="comp_tel_2" id="comp_tel_2" class="form-control input-sm rb-tel-num-2" maxlength="4">
								<span class="rb-divider"></span>
								<input type="tel" name="comp_tel_2" id="comp_tel_2" class="form-control input-sm rb-tel-num-3" maxlength="4">
								<span class="help-block" name="comp_tel_1" id="comp_tel_1"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_fax" class="col-sm-3 control-label">팩스</label>
							<div class="col-sm-8">
								<input type="tel"  name="comp_fax_1" id="comp_fax_1" class="form-control input-sm rb-tel-num-1" maxlength="4">
								<span class="rb-divider"></span>
								<input type="tel" name="comp_fax_2" id="comp_fax_2" class="form-control input-sm rb-tel-num-2" maxlength="4">
								<span class="rb-divider"></span>
								<input type="tel" name="comp_fax_3" id="comp_fax_3" class="form-control input-sm rb-tel-num-3" maxlength="4">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_part" class="col-sm-3 control-label">소속부서</label>
							<div class="col-sm-8">
								<input type="text" name="comp_part" id="comp_part" class="form-control" placeholder="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="comp_level" class="col-sm-3 control-label">직책</label>
							<div class="col-sm-8">
								<input type="text" name="comp_level" id="comp_level" class="form-control" placeholder="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"><span class="text-danger">*</span> 사업장 주소</label>
							<div class="col-sm-8">
								<div class="clearfix" style="margin-bottom: 5px">
									<input type="text" name="comp_zip_1" id="comp_zip1" class="form-control input-sm pull-left" readonly="" style="width:50px" value="">
									<span class="rb-divider"></span>
									<input type="text" name="comp_zip_2" id="comp_zip2"  class="form-control input-sm pull-left" readonly="" style="width:50px;" value="">
									<span class="separator"></span>
									<button type="button" class="btn btn-default btn-sm pull-left rb-zipsearch" onclick="openDaumPostcode2();"><i class="fa fa-search"></i> 우편번호</button>
								</div>
								<input type="text" name="comp_addr1" id="comp_addr1" class="form-control" readonly="">
								<input type="text" name="comp_addr2" id="comp_addr2" class="form-control" placeholder="우편번호를 먼저 선택해주세요.">
							</div>
							<script src="http://dmaps.daum.net/map_js_init/postcode.js"></script>
							<script>
							    function openDaumPostcode2() {
							        new daum.Postcode({
							            oncomplete: function(data) {
							                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
							                // 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
							                document.getElementById('comp_zip1').value = data.postcode1;
							                document.getElementById('comp_zip2').value = data.postcode2;
							                document.getElementById('comp_addr1').value = data.address;

							                //전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
							                //아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
							                //var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
							                //document.getElementById('addr').value = addr;

							                document.getElementById('comp_addr2').focus();
							            }
							        }).open();
							    }
							</script>
						</div>
              <?php endif?>
                 <hr>
						<div class="rb-form-footer text-center">
							<button type="button" class="btn btn-default" onclick="goHref('<?php echo $g['r']?>/?r=<?php echo $r?>&amp;m=member&amp;front=join&amp;page=step1');"><i class="fa fa-caret-left fa-lg"></i> 이전</button>
							<button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i> 회원가입</button>
						</div>
			    </div>
		</div>
	</div>
</form>
</article>


<!-- Modal : 우편번호 검색 : 현재 사용안함 추후 모달 사용가능성을 위해 남겨둠 -->
<div class="modal fade" id="modal-zipsearch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-search fa-lg"></i> 우편번호 검색</h4>
			</div>
			<div class="modal-body">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="input-group">
							<span class="input-group-addon">지역명</span>
							<input type="search" class="form-control" placeholder="찾고자하는 주소의 동(읍/면/리)을 입력하세요.">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">검색</button>
							</span>
						</div>
					</div>
					<div class="list-group resultbox">
						<a href="#" class="list-group-item">서울 종로구 서린동 광화문우체국 <span class="badge">110-110</span></a>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
});

function foreignChk(obj)
{
	if (obj.checked == true)
	{
		getId('addrbox').style.display = 'none';
		getId('foreign_ment').innerHTML= '해외거주자 입니다.';
	}
	else {
		getId('addrbox').style.display = 'block';
		getId('foreign_ment').innerHTML= '해외거주자일 경우 체크해 주세요.';
	}
}
function sameCheck(obj,layer)
{

	if (!obj.value)
	{
		eval('obj.form.check_'+obj.name).value = '0';
		getId(layer).innerHTML = '유효성 결과';
	}
	else
	{
		if (obj.name == 'id')
		{
			if (obj.value.length < 4 || obj.value.length > 12 || !chkIdValue(obj.value))
			{
				obj.form.check_id.value = '0';
				setTimeout(function() {
			        obj.focus();
			    }, 0);
				getId(layer).innerHTML = '<span class="text-danger">사용할 수 없는 아이디입니다</span>';
				return false;
			}
		}
		if (obj.name == 'email')
		{
			if (!chkEmailAddr(obj.value))
			{
				obj.form.check_email.value = '0';
				setTimeout(function() {
			        obj.focus();
			    }, 0);
				getId(layer).innerHTML = '<span class="text-danger">이메일형식이 아닙니다</span>';
				return false;
			}
		}

		frames._action_frame_<?php echo $m?>.location.href = '<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&a=same_check&fname=' + obj.name + '&fvalue=' + obj.value + '&flayer=' + layer;
	}
}
function saveCheck(f)
{
	if (f.check_id.value == '0')
	{
		alert('아이디를 확인해 주세요.');
		f.id.focus();
		return false;
	}

	if (f.pw1.value == '')
	{
		alert('패스워드를 입력해 주세요.');
		f.pw1.focus();
		return false;
	}
	if (f.pw2.value == '')
	{
		alert('패스워드를 한번더 입력해 주세요.');
		f.pw2.focus();
		return false;
	}
	if (f.pw1.value != f.pw2.value)
	{
		alert('패스워드가 일치하지 않습니다.');
		f.pw1.focus();
		return false;
	}

	if (f.name.value == '')
	{
		alert('이름을 입력해 주세요.');
		f.name.focus();
		return false;
	}
	<?php if($d['member']['form_nic_p']):?>
	if (f.check_nic.value == '0')
	{
		alert('닉네임을 확인해 주세요.');
		f.nic.focus();
		return false;
	}
	<?php endif?>
	<?php if($d['member']['form_birth']&&$d['member']['form_birth_p']):?>
	if (f.birth_1.value == '')
	{
		alert('생년월일을 지정해 주세요.');
		f.birth_1.focus();
		return false;
	}
	if (f.birth_2.value == '')
	{
		alert('생년월일을 지정해 주세요.');
		f.birth_2.focus();
		return false;
	}
	if (f.birth_3.value == '')
	{
		alert('생년월일을 지정해 주세요.');
		f.birth_3.focus();
		return false;
	}
	<?php endif?>
	<?php if($d['member']['form_sex']&&$d['member']['form_sex_p']):?>
	if (f.sex[0].checked == false && f.sex[1].checked == false)
	{
		alert('성별을 선택해 주세요.  ');
		return false;
	}
	<?php endif?>


	<?php if($d['member']['form_qa']&&$d['member']['form_qa_p']):?>
	if (f.pw_q.value == '')
	{
		alert('비밀번호 찾기 질문을 입력해 주세요.');
		f.pw_q.focus();
		return false;
	}
	if (f.pw_a.value == '')
	{
		alert('비밀번호 찾기 답변을 입력해 주세요.');
		f.pw_a.focus();
		return false;
	}
	<?php endif?>


	if (f.check_email.value == '0')
	{
		alert('이메일을 확인해 주세요.');
		f.email.focus();
		return false;
	}

	<?php if($d['member']['form_home']&&$d['member']['form_home_p']):?>
	if (f.home.value == '')
	{
		alert('홈페이지 주소를 입력해 주세요.');
		f.home.focus();
		return false;
	}
	<?php endif?>


	<?php if($d['member']['form_tel2']&&$d['member']['form_tel2_p']):?>
	if (f.tel2_1.value == '')
	{
		alert('휴대폰번호를 입력해 주세요.');
		f.tel2_1.focus();
		return false;
	}
	if (f.tel2_2.value == '')
	{
		alert('휴대폰번호를 입력해 주세요.');
		f.tel2_2.focus();
		return false;
	}
	if (f.tel2_3.value == '')
	{
		alert('휴대폰번호를 입력해 주세요.');
		f.tel2_3.focus();
		return false;
	}
	<?php endif?>

	<?php if($d['member']['form_tel1']&&$d['member']['form_tel1_p']):?>
	if (f.tel1_1.value == '')
	{
		alert('전화번호를 입력해 주세요.');
		f.tel1_1.focus();
		return false;
	}
	if (f.tel1_2.value == '')
	{
		alert('전화번호를 입력해 주세요.');
		f.tel1_2.focus();
		return false;
	}
	if (f.tel1_3.value == '')
	{
		alert('전화번호를 입력해 주세요.');
		f.tel1_3.focus();
		return false;
	}
	<?php endif?>

	<?php if($d['member']['form_addr']&&$d['member']['form_addr_p']):?>
	if (!f.foreign || f.foreign.checked == false)
	{
		if (f.addr1.value == ''||f.addr2.value == '')
		{
			alert('주소를 입력해 주세요.');
			f.addr2.focus();
			return false;
		}
	}
	<?php endif?>


	<?php if($d['member']['form_job']&&$d['member']['form_job_p']):?>
	if (f.job.value == '')
	{
		alert('직업을 선택해 주세요.');
		f.job.focus();
		return false;
	}
	<?php endif?>

	<?php if($d['member']['form_marr']&&$d['member']['form_marr_p']):?>
	if (f.marr_1.value == '')
	{
		alert('결혼기념일을 지정해 주세요.');
		f.marr_1.focus();
		return false;
	}
	if (f.marr_2.value == '')
	{
		alert('결혼기념일을 지정해 주세요.');
		f.marr_2.focus();
		return false;
	}
	if (f.marr_3.value == '')
	{
		alert('결혼기념일을 지정해 주세요.');
		f.marr_3.focus();
		return false;
	}
	<?php endif?>


	<?php if($d['member']['form_comp'] && $comp):?>
	if (f.comp_num_1.value == '')
	{
		alert('사업자등록번호를 입력해 주세요.     ');
		f.comp_num_1.focus();
		return false;
	}
	if (f.comp_num_2.value == '')
	{
		alert('사업자등록번호를 입력해 주세요.     ');
		f.comp_num_2.focus();
		return false;
	}
	if (f.comp_num_3.value == '')
	{
		alert('사업자등록번호를 입력해 주세요.     ');
		f.comp_num_3.focus();
		return false;
	}

	if (f.comp_name.value == '')
	{
		alert('회사명을 입력해 주세요.     ');
		f.comp_name.focus();
		return false;
	}
	if (f.comp_ceo.value == '')
	{
		alert('대표자명을 입력해 주세요.     ');
		f.comp_ceo.focus();
		return false;
	}
	if (f.comp_condition.value == '')
	{
		alert('업태를 입력해 주세요.     ');
		f.comp_condition.focus();
		return false;
	}
	if (f.comp_item.value == '')
	{
		alert('종목을 입력해 주세요.     ');
		f.comp_item.focus();
		return false;
	}
	if (f.comp_tel_1.value == '')
	{
		alert('대표전화번호를 입력해 주세요.');
		f.comp_tel_1.focus();
		return false;
	}
	if (f.comp_tel_2.value == '')
	{
		alert('대표전화번호를 입력해 주세요.');
		f.comp_tel_2.focus();
		return false;
	}

	if (f.comp_addr1.value == '')
	{
		alert('사업장주소를 입력해 주세요.');
		f.comp_addr2.focus();
		return false;
	}
	<?php endif?>

	return confirm('정말로 가입하시겠습니까?       ');
}
//]]>
</script>
