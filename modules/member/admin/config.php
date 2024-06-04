<?php include_once $g['path_module'].$module.'/var/var.join.php';?>
<style type="text/css">
#rb-body #setting-content {border: 0;}
.accordion-toggle {cursor:pointer;}
.add-field-chk input[type="checkbox"] {position: relative;margin-left: -10px;}
#add-save-btn {float:left;margin-left:40%;}

</style>
<form name="procForm" class="form-horizontal" role="form" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $module?>" />
	<input type="hidden" name="a" value="" />
	<input type="hidden" name="_join_menu" value="<?php echo $_SESSION['_join_menu']?$_SESSION['_join_menu']:1?>" />	
	<input type="hidden" name="_join_tab" value="<?php echo $_SESSION['_join_tab']?$_SESSION['_join_tab']:'terms-1'?>" />	

<div class="btn-group tab-div" data-toggle="buttons" id="join-setting-tab">
	 <a href="#signup-config" data-toggle="tab" class="btn btn-default active tab-a" id="join_1">
		<input type="radio" name="options" id="option1"> <i class="fa fa-cog fa-lg"></i>  회원가입 설정
	 </a>
	 <a href="#signup-form-config" data-toggle="tab" class="btn btn-default tab-a" id="join_2">
		<input type="radio" name="options" id="option2"> <i class="fa fa-file-text-o fa-lg"></i> 가입양식 관리
	</a>
	<a href="#signup-form-add" data-toggle="tab" class="btn btn-default tab-a" id="join_3">
		<input type="radio" name="options" id="option3"> <i class="fa fa-plus-circle fa-lg"></i> 가입항목 추가
	</a>
	<a href="#profile" data-toggle="tab" class="btn btn-default tab-a" id="join_4">
		<input type="radio" name="options" id="option3"> <i class="fa fa-check-square fa-lg"></i> 로그인/마이페이지
	</a>
	<a href="#terms" data-toggle="tab" class="btn btn-default tab-a" id="join_5">
		<input type="radio" name="options" id="option3"> <i class="fa fa-bullhorn fa-lg"></i> 약관/안내메시지
	</a>	 	
</div>
<hr>

<!-- Tab panes -->
<div class="tab-content" id="setting-content">
	 <div class="tab-pane active" id="signup-config">
		<!-- 회원가입 설정 -->
	
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">회원가입 작동상태</label>
						<div class="col-sm-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php if($d['member']['join_enable']):?>active<?php endif?>">
									<input type="radio" name="join_enable" value="1" id="option1" <?php if($d['member']['join_enable']):?>checked<?php endif?>/> 작동
								</label>
								<label class="btn btn-default <?php if(!$d['member']['join_enable']):?>active<?php endif?>">
									<input type="radio" name="join_enable" value="0" <?php if(!$d['member']['join_enable']):?>checke<?php endif?> id="option2" /> 중단
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">모바일 회원가입</label>
						<div class="col-sm-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php if($d['member']['join_mobile']):?>active<?php endif?>">
									<input type="radio" name="join_mobile" value="1"  <?php if($d['member']['join_mobile']):?>checked<?php endif?> id="option3" /> 지원함
								</label>
								<label class="btn btn-default <?php if(!$d['member']['join_mobile']):?>active<?php endif?>">
									<input type="radio" name="join_mobile" value="0" <?php if(!$d['member']['join_mobile']):?>checked<?php endif?> id="option4" /> 지원 안함
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">가입시 소속그룹</label>
						<div class="col-sm-8">
							<select name="join_group" class="form-control">
								  <?php $_SOSOK=getDbArray($table['s_mbrgroup'],'','*','gid','asc',0,1)?>
						        <?php while($_S=db_fetch_array($_SOSOK)):?>
						            <option value="<?php echo $_S['uid']?>"<?php if($_S['uid']==$d['member']['join_group']):?> selected="selected"<?php endif?>><?php echo $_S['name']?>(<?php echo number_format($_S['num'])?>)</option>
						        <?php endwhile?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">가입시 회원등급</label>
						<div class="col-sm-8">
							<select name="join_level" class="form-control">		
                        <?php $_LEVEL=getDbArray($table['s_mbrlevel'],'','*','uid','asc',0,1)?>
								<?php while($_L=db_fetch_array($_LEVEL)):?>
								<option value="<?php echo $_L['uid']?>"<?php if($_L['uid']==$d['member']['join_level']):?> selected="selected"<?php endif?>><?php echo $_L['name']?>(<?php echo number_format($_L['num'])?>)</option>
								<?php if($_L['gid'])break; endwhile?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">탈퇴데이터 처리</label>
						<div class="col-sm-8">
							<label class="radio-inline">
							  <input type="radio" name="join_out" value="1" <?php if($d['member']['join_out']==1):?>checked<?php endif?> id="inlineCheckbox1" /> 즉시삭제
							</label>
							<label class="radio-inline">
							  <input type="radio" name="join_out" value="2" <?php if($d['member']['join_out']==2):?>checked<?php endif?> id="inlineCheckbox2"/> 관리자 확인 후 삭제
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">탈퇴후 재가입</label>
						<div class="col-sm-8">
							<label class="radio-inline">
							  <input type="radio" name="join_rejoin" value="1" <?php if($d['member']['join_rejoin']):?>checked<?php endif?> id="inlineCheckbox1"  /> 허용함
							</label>
							<label class="radio-inline">
							  <input type="radio" name="join_rejoin" value="0" <?php if(!$d['member']['join_rejoin']):?>checked<?php endif?> id="inlineCheckbox2" /> 허용 안함
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">가입시 승인처리</label>
						<div class="col-sm-8">
							<select name="join_auth" class="form-control">
								<option value="1"<?php if($d['member']['join_auth']==1):?> selected="selected"<?php endif?>>즉시승인</option>
								<option value="2"<?php if($d['member']['join_auth']==2):?> selected="selected"<?php endif?>>관리자확인 후 승인</option>
								<option value="3"<?php if($d['member']['join_auth']==3):?> selected="selected"<?php endif?>>이메일인증 후 승인</option>
						   </select>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">가입시 지급포인트</label>
						<div class="col-sm-8">
							<input type="number" name="join_point" value="<?php echo $d['member']['join_point']?>" class="form-control" placeholder="" />
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">대표 이메일</label>
						<div class="col-sm-8">
							<input type="email" name="join_email" value="<?php echo $d['member']['join_email']?>" class="form-control" />
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">가입 이메일</label>
						<div class="col-sm-8">
							<div class="checkbox">
							  <label>
							    <input type="checkbox" name="join_email_send" value="1"<?php if($d['member']['join_email_send']):?> checked="checked"<?php endif?> /> 가입안내 이메일 발송
							  </label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">회원가입 레이아웃</label>
						<div class="col-sm-8">
							<select name="layout_join" class="col-sm-12 form-control" id="" tabindex="-1">
								<?php $dirs = opendir($g['path_layout'])?>
								<?php while(false !== ($tpl = readdir($dirs))):?>
								<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
								<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
							    <optgroup label="<?php echo $tpl?>">		
										<?php while(false !== ($tpl1 = readdir($dirs1))):?>
										<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
									   <option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['member']['layout_join']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>><?php echo getFolderName($g['path_layout'].$tpl)?>-<?php echo str_replace('.php','',$tpl1)?></option>
										<?php endwhile?>
								 </optgroup>
								<?php closedir($dirs1)?>
								<?php endwhile?>
								<?php closedir($dirs)?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">로그인 레이아웃</label>
						<div class="col-sm-8">
							<select name="layout_login" class="col-sm-12 form-control" id="" tabindex="-1">
								<?php $dirs = opendir($g['path_layout'])?>
								<?php while(false !== ($tpl = readdir($dirs))):?>
								<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
								<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
							    <optgroup label="<?php echo $tpl?>">		
										<?php while(false !== ($tpl1 = readdir($dirs1))):?>
										<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
									   <option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['member']['layout_login']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>><?php echo getFolderName($g['path_layout'].$tpl)?>-<?php echo str_replace('.php','',$tpl1)?></option>
										<?php endwhile?>
								 </optgroup>
								<?php closedir($dirs1)?>
								<?php endwhile?>
								<?php closedir($dirs)?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">마이페이지 레이아웃</label>
						<div class="col-sm-8">
							<select name="layout_mypage" class="col-sm-12 form-control" id="" tabindex="-1">
								<?php $dirs = opendir($g['path_layout'])?>
								<?php while(false !== ($tpl = readdir($dirs))):?>
								<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
								<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
							    <optgroup label="<?php echo $tpl?>">		
										<?php while(false !== ($tpl1 = readdir($dirs1))):?>
										<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
									   <option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['member']['layout_mypage']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>><?php echo getFolderName($g['path_layout'].$tpl)?>-<?php echo str_replace('.php','',$tpl1)?></option>
										<?php endwhile?>
								 </optgroup>
								<?php closedir($dirs1)?>
								<?php endwhile?>
								<?php closedir($dirs)?>
							</select>
					</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">소속메뉴</label>
						<div class="col-sm-8">
							<select name="sosokmenu" class="form-control">
								<option value="">&nbsp;+ 사용안함</option>
								<option value="">--------------------------------</option>
								<?php include_once $g['path_core'].'function/menu1.func.php'?>
								<?php $cat=$d['member']['sosokmenu']?>
								<?php getMenuShowSelect($s,$table['s_menu'],0,0,0,0,0,'')?>
						  </select>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
            <div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">사용제한 닉네임</label>
						<div class="col-sm-8">
							<input type="text" name="join_cutnic" value="<?php echo $d['member']['join_cutnic']?>" class="form-control">
							<span class="help-block small text-muted">사용을 제한하려는 닉네임을 콤마(,)로 구분해서 입력해 주세요.</span>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group error">
						<label class="col-sm-4 control-label">사용제한 아이디</label>
						<div class="col-sm-8">
							<input type="text" name="join_cutid" value="<?php echo $d['member']['join_cutid']?>" class="form-control">
							<span class="help-block small text-muted">사용을 제한하려는 아이디를 콤마(,)로 구분해서 입력해 주세요.</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">포인트지급 메세지</label>
						<div class="col-sm-8">
							<input type="text" name="join_pointmsg" value="<?php echo $d['member']['join_pointmsg']?>" class="form-control">
						</div>
					</div>
				</div>				
				<div class="col-sm-6">
				</div>
			</div>
			<hr >
			<div class="text-center">
					<button type="submit" class="btn btn-primary hidden-xs hidden-sm"><i class="fa fa-check"></i> 정보저장</button>
					<button type="submit" class="btn btn-primary btn-lg btn-block visible-xs visible-sm"><i class="fa fa-check"></i> 정보저장</button>
			</div>
	</div>
	<!-- /회원가입 설정 -->

   <!-- 가입양식 관리 -->
	<div class="tab-pane" id="signup-form-config">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">이용약관/개인정보</label>
						<div class="col-sm-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php if(!$d['member']['form_agree']):?>active<?php endif?>">
									<input type="radio" name="form_agree" value="0"  <?php if(!$d['member']['form_agree']):?>checked<?php endif?> id="fa-0"> 생략
								</label>
								<label class="btn btn-default <?php if($d['member']['form_agree']):?>active<?php endif?>">
								<input type="radio" name="form_agree" value="1" <?php if($d['member']['form_agree']):?>checked<?php endif?> id="fa-1"> 동의얻음
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">회원가입 연령제한</label>
						<div class="col-sm-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php if(!$d['member']['form_age']):?>active<?php endif?>">
									<input type="radio" name="form_age" value="0" <?php if(!$d['member']['form_age']):?>checked<?php endif?> id="age-0"> 연령 제한없음
								</label>
							   <label class="btn btn-default <?php if($d['member']['form_age']):?>active<?php endif?>">
									<input type="radio" name="form_age" value="1" <?php if($d['member']['form_age']):?>checked<?php endif?> id="age-1"> 14세이하 제한
								</label>
							</div>
						</div>
					</div>
			   </div>
			</div>
   		<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">외국인가입</label>
						<div class="col-sm-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php if(!$d['member']['form_foreign']):?>active<?php endif?>">
									<input type="radio" name="form_foreign" value="0" <?php if(!$d['member']['form_foreign']):?>checked<?php endif?> id="en-0"> 허용안함
								</label>
								<label class="btn btn-default <?php if($d['member']['form_foreign']):?>active<?php endif?>">
									<input type="radio" name="form_foreign" value="1" <?php if($d['member']['form_foreign']):?>checked<?php endif?> id="en-1"> 허용함
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">기업회원가입</label>
						<div class="col-sm-8">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default <?php if(!$d['member']['form_comp']):?>active<?php endif?>">
									<input type="radio" name="form_comp" value="0" <?php if(!$d['member']['form_comp']):?>checked<?php endif?> id="com-0"> 사용안함
								</label>
								<label class="btn btn-default <?php if($d['member']['form_comp']):?>active<?php endif?>">
									<input type="radio" name="form_comp" value="1" <?php if($d['member']['form_comp']):?>checked<?php endif?> id="com-1"> 사용함
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">패스워드찾기 질문</label>
						<div class="col-sm-8">
							<textarea name="pw_question"  class="form-control" rows="18"><?php readfile($g['path_module'].$module.'/var/pw_question.txt')?></textarea>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">직업군</label>
						<div class="col-sm-8">
							<textarea name="job" class="form-control" rows="18"><?php readfile($g['path_module'].$module.'/var/job.txt')?></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4 control-label">노출항목 및 옵션</label>
						<div class="col-sm-8">
							 <?php $opset = array('id'=>'아이디','email'=>'이메일','password'=>'패스워드','name'=>'이름','nic'=>'닉네임','birth'=>'생년월일','sex'=>'성별')?>
						    
						    <?php $i=0;foreach($opset as $_key => $_val):?>
							 <fieldset <?php echo $i<4?'disabled':''?>>
							 	 <?php if($i<4):?>
							 	 <label class="checkbox-inline">
										<input type="checkbox" id="inlineCheckbox1"  checked><?php echo $_val?> &nbsp;&nbsp;<i class="fa fa-long-arrow-right fa-lg text-muted"></i></label>
								  </label>
								   <label class="checkbox-inline">
											<input type="checkbox" id="inlineCheckbox2" checked> 필수입력
									</label>
								 <?php else:?>
								   <label class="checkbox-inline">
										<input type="checkbox" id="inlineCheckbox1"  name="form_<?php echo $_key?>" value="1"<?php if($d['member']['form_'.$_key]):?> checked<?php endif?>><?php echo $_val?> &nbsp;&nbsp;<i class="fa fa-long-arrow-right fa-lg text-muted"></i></label>
								   </label>
								   <label class="checkbox-inline">
											<input type="checkbox" id="inlineCheckbox2" name="form_<?php echo $_key?>_p" value="1"<?php if($d['member']['form_'.$_key.'_p']):?> checked<?php endif?>> 필수입력
									</label>
								 <?php endif?>	
							 </fieldset>	
							 <?php $i++;endforeach?>		
						 </div>
					</div>
				</div>
				<div class="col-sm-6">
				   <?php $opset = array('qa'=>'패스워드찾기 질답','home'=>'홈페이지','tel1'=>'집전화','tel2'=>'휴대폰','job'=>'직업','marr'=>'결혼기념일','addr'=>'주소')?>
				   <?php foreach($opset as $_key => $_val):?>
					 <fieldset>
					 	 <label class="checkbox-inline">
								<input type="checkbox" id="inlineCheckbox1"  name="form_<?php echo $_key?>" value="1"<?php if($d['member']['form_'.$_key]):?> checked<?php endif?>><?php echo $_val?> &nbsp;&nbsp;<i class="fa fa-long-arrow-right fa-lg text-muted"></i></label>
						  </label>
						   <label class="checkbox-inline">
									<input type="checkbox" id="inlineCheckbox2" name="form_<?php echo $_key?>_p" value="1"<?php if($d['member']['form_'.$_key.'_p']):?> checked<?php endif?>> 필수입력
							</label>
					 </fieldset>	
					 <?php endforeach?>			
				</div>
			</div>
			<hr>
			<div class="text-center">
				<button type="submit" class="btn btn-primary hidden-xs hidden-sm"><i class="fa fa-check"></i> 정보저장</button>
				<button type="submit" class="btn btn-primary btn-lg btn-block visible-xs visible-sm"><i class="fa fa-check"></i> 정보저장</button>
			</div>
	</div>
   <!-- /가입양식 관리 -->
	
   <!-- 가입항목 추가 -->
	 <div class="tab-pane" id="signup-form-add">
	 	 <div class="well">
	 	 	  <ul class="help-block">
					<li>회원가입 폼에 기본양식외의 필요한 입력양식이 있을 경우 추가해 주세요.</li>
					<li>입력양식 추가는 <span class="bg-danger">반드시 회원가입 서비스를 정식으로 오픈하기 전에 셋팅해 주세요.</span></li>
					<li>서비스도중 양식을 추가하면 이미 가입한 회원에 대해서는 반영되지 않습니다.</li>
					<li><span class="bg-danger">회원검색용도로 양식을 추가하는 것은 권장하지 않습니다.</span></li>
		     </ul> 
	 	 </div>		 
		 <div class="table-responsive">
		 	 <table class="table table-bordered" >
				<thead>
					<tr>
						<th colspan="2" class="text-center">명칭</th>
						<th class="text-center">형식</th>
						<th class="text-center">값/속성 <a href="#value-guide" data-toggle="collapse"><i class="fa fa-question-circle"></i></a></th>
						<th class="text-center" width="50px">필수</th>
						<th class="text-center" width="50px">숨김</th>
					</tr>
				</thead>
				<tbody>
					<?php $_add = file($g['path_module'].$module.'/var/add_field.txt')?>
					<?php foreach($_add as $_key):?>
					<?php $_val = explode('|',trim($_key))?>

					<tr>
					<td><input type="button" value="삭제" class="btn btn-danger" onclick="delField(this.form,'<?php echo $_val[0]?>');" /></td>
					<td><input type="text" name="add_name_<?php echo $_val[0]?>" size="13" value="<?php echo $_val[1]?>" class="form-control" /></td>
					<td>
						<input type="checkbox" name="addFieldMembers[]" value="<?php echo $_val[0]?>" checked="checked" class="hidden"/>
						<select name="add_type_<?php echo $_val[0]?>" class="form-control">
						<option value="text"<?php if($_val[2]=='text'):?> selected="selected"<?php endif?>>TEXT</option>
						<option value="password"<?php if($_val[2]=='password'):?> selected="selected"<?php endif?>>PASSWORD</option>
						<option value="select"<?php if($_val[2]=='select'):?> selected="selected"<?php endif?>>SELECT</option>
						<option value="radio"<?php if($_val[2]=='radio'):?> selected="selected"<?php endif?>>RADIO</option>
						<option value="checkbox"<?php if($_val[2]=='checkbox'):?> selected="selected"<?php endif?>>CHECKBOX</option>
						<option value="textarea"<?php if($_val[2]=='textarea'):?> selected="selected"<?php endif?>>TEXTAREA</option>
						</select>
					</td>
					<td><input type="text" name="add_value_<?php echo $_val[0]?>" size="30" value="<?php echo $_val[3]?>" class="form-control"/></td>
				<!-- 	<td><input type="text" name="add_size_<?php echo $_val[0]?>" size="4" value="<?php echo $_val[4]?>" class="form-control" /></td>
				 필요할 경우 주석제거-->	<td>
						<div class="checkbox add-field-chk">
							<label>  
						       <input type="checkbox" name="add_pilsu_<?php echo $_val[0]?>" value="1"<?php if($_val[5]):?> checked="checked"<?php endif?> /></td>
					      </label>
					   </div>   
					<td>
						<div class="checkbox add-field-chk">
							<label>
						     <input type="checkbox" name="add_hidden_<?php echo $_val[0]?>" value="1"<?php if($_val[6]):?> checked="checked"<?php endif?> /></td>
					      </label>
					   </div>   
					</tr>					
					<?php endforeach?>
					<tr class="active">
						<td><button type="button" class="btn btn-primary"  onclick="addField(this.form);">추가</button></td>
						<td><input type="text" name="add_name" class="form-control" placeholder=""></td>
						<td>
							<select name="add_type" class="form-control">
							<option value="text">TEXT</option>
							<option value="password">PASSWORD</option>
							<option value="select">SELECT</option>
							<option value="radio">RADIO</option>
							<option value="checkbox">CHECKBOX</option>
							<option value="textarea">TEXTAREA</option>
							</select>
						</td>
						<td><input type="text" name="add_value" class="form-control" placeholder=""></td>
            <!-- <td><input type="text" name="add_size" class="form-control" placeholder=""></td>  필요할 경우 주석제거-->
						<td>
							<div class="checkbox add-field-chk">
								<label>
									<input type="checkbox" name="add_pilsu" >
								</label>
							</div>
						</td>
						<td>
							<div class="checkbox add-field-chk">
								<label>
									<input type="checkbox" name="add_hidden">
								</label>
							</div>
						</td>
					</tr>	
				</tbody>
			 </table>
			 <p class="help-block collapse alert alert-warning" id="value-guide">
			 	<small>
			 	  input 의 경우 해당 값이 되므로 입력하지 않는 것이 일반적입니다. <br />
			 	  select,radio,checkbox 의 경우 선택항목이 되며 콤마(,)로 구분하시면 됩니다.
			   </small>
			 </p>
		 </div>
		 <div id="preview">
		 	<div class="well well-default text-muted">
             <p class="col-sm-12" style="padding-left:0">
                 <span class="text-muted">미리보기</span> 
             </p>
		 	  <!-- 추가필드 시작 -->
					 <?php foreach($_add as $_key):?>
					 <?php $_val = explode('|',trim($_key))?>
					 <?php if(!$_val[0]) continue?>					
							 <div class="form-group">
								   <label  for="<?php echo $_val[0]?>" class="col-sm-3 control-label"><?php echo $_val[1]?></label>
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
							  
		      </div> <!-- .well -->
		       <p class="help-block">
                 <span class="text-danger small">* 숨김처리한 것은 실제 가입폼에서는 안보입니다.</span> 
             </p>  
		 </div>  <!-- #preview -->
		  <hr>
			<div class="text-center">
				<button type="submit" class="btn btn-primary hidden-xs hidden-sm"><i class="fa fa-check"></i> 정보저장</button>
				<button type="submit" class="btn btn-primary btn-lg btn-block visible-xs visible-sm"><i class="fa fa-check"></i> 정보저장</button>
			</div>
	</div>
   <!-- /가입항목 추가 -->
   
   <!-- 로그인/프로필 -->
	<div class="tab-pane" id="profile">
			<div class="form-group">
				<label for="inputEmail3" class="col-sm-2 control-label">로그인 포인트지급</label>
				<div class="col-sm-2">
					<div class="input-group">
						<input type="text" name="login_point" value="<?php echo $d['member']['login_point']?>" class="form-control">
						<span class="input-group-addon">포인트</span>
					</div>
					<span class="help-block">1일 1회에 한함</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">로그인 페이지 옵션</label>
				<div class="col-sm-4">
					<div class="checkbox">
						<label><input type="checkbox" name="login_ssl" value="1"<?php if($d['member']['login_ssl']):?> checked="checked"<?php endif?> /> 보안접속(SSL) 사용 </label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="login_emailid" value="1"<?php if($d['member']['login_emailid']):?> checked="checked"<?php endif?> /> 이메일 아이디 사용 </label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="login_openid" value="1"<?php if($d['member']['login_openid']):?> checked="checked"<?php endif?> /> 오픈아이디(OpenID) 사용 </label>
					</div>
				</div>
			</div>
         <div class="form-group">
				<label for="mytab_recnum" class="col-sm-2 control-label">마이페이지 출력수</label>
				<div class="col-sm-2">
					<div class="input-group">
						<input type="text" name="mytab_recnum" id="mytab_recnum" value="<?php echo $d['member']['mytab_recnum']?>" class="form-control">
						<span class="input-group-addon">개</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">마이페이지  메뉴</label>
				<div class="col-sm-4">
					<?php $mytab=array('post'=>'게시물','comment'=>'댓글','oneline'=>'한줄의견','avatar'=>'아바타 변경기능','covimg'=>'커버 이미지 변경기능 ','scrap'=>'스크랩','paper'=>'쪽지','point'=>'포인트','log'=>'접속기록','info'=>'정보수정','pw'=>'비번변경','out'=>'회원탈퇴',)?>
					<?php foreach($mytab as $_key=>$_val):?> 
					<div class="checkbox">
						<label><input type="checkbox" name="mytab_<?php echo $_key?>" value="1" <?php if($d['member']['mytab_'.$_key]):?>checked<?php endif?>><?php echo $_val?></label>
					</div>
				  <?php endforeach?>				
				</div>
			</div>
			<hr>
			<div class="text-center">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary hidden-xs hidden-sm"><i class="fa fa-check"></i> 정보저장</button>
					<button type="submit" class="btn btn-primary btn-lg btn-block visible-xs visible-sm"><i class="fa fa-check"></i> 정보저장</button>
				</div>
			</div>
	</div>
   <!-- /로그인/프로필 -->

   <!-- 약관/안내메시지 -->
	<div class="tab-pane" id="terms">
			<div class="panel-group" id="accordion">
				<div class="panel panel-default">
					<div class="panel-heading rb-icon">
						<div class="icon">
							<i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h4 class="panel-title accordion-toggle agree-tab" data-parent="#accordion" data-toggle="collapse" href="#terms-1">
							 홈페이지 이용약관
						</h4>
					</div>
					<div class="panel-collapse collapse in" id="terms-1">
						<div>
							 <textarea name="agree1" class="form-control" rows="15"><?php readfile($g['path_module'].$module.'/var/agree1.txt')?></textarea>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading rb-icon">
						<div class="icon">
							<i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h4 class="panel-title accordion-toggle collapsed agree-tab" data-parent="#accordion" data-toggle="collapse" href="#terms-2">
						 	정보수집/이용목적
						</h4>
					</div>
					<div class="panel-collapse collapse" id="terms-2">
						<div>
							<textarea name="agree2" class="form-control" rows="15"><?php readfile($g['path_module'].$module.'/var/agree2.txt')?></textarea>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading rb-icon">
						<div class="icon">
							<i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h4 class="panel-title accordion-toggle collapsed agree-tab" data-parent="#accordion" data-toggle="collapse" href="#terms-3">
							  개인정보수집항목
						</h4>
					</div>
					<div class="panel-collapse collapse" id="terms-3">
						<div>
                   <textarea name="agree3" class="form-control" rows="8"><?php readfile($g['path_module'].$module.'/var/agree3.txt')?></textarea>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading rb-icon">
						<div class="icon">
							<i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h4 class="panel-title accordion-toggle collapsed agree-tab" data-parent="#accordion" data-toggle="collapse" href="#terms-4">
								정보보유/이용기간
						</h4>
					</div>
					<div class="panel-collapse collapse" id="terms-4">
						<div>
							 <textarea name="agree4" class="form-control" rows="15"><?php readfile($g['path_module'].$module.'/var/agree4.txt')?></textarea>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading rb-icon">
						<div class="icon">
							<i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h4 class="panel-title accordion-toggle collapsed agree-tab" data-parent="#accordion" data-toggle="collapse" href="#terms-5">
								개인정보위탁처리
						</h4>
					</div>
					<div class="panel-collapse collapse" id="terms-5">
						<div>
							<textarea name="agree5" class="form-control" rows="15"><?php readfile($g['path_module'].$module.'/var/agree5.txt')?></textarea>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="text-center">
				<button type="submit" class="btn btn-primary hidden-xs hidden-sm"><i class="fa fa-check"></i> 정보저장</button>
				<button type="submit" class="btn btn-primary btn-lg btn-block visible-xs visible-sm"><i class="fa fa-check"></i> 정보저장</button>
			</div>
	</div>
	<!-- 약관/안내메시지 -->

</div>
</form>


<br><br><br>

<script type="text/javascript">
//<![CDATA[
// 툴팁 이벤트 
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
}); 

function addField(f)
{
	if (f.add_name.value == '')
	{
		alert('명칭을 입력해 주세요.  ');
		f.add_name.focus();
		return false;
	}
	saveCheck(f);
}
function delField(f,dval)
{
	if (confirm('정말로 삭제하시겠습니까?   '))
	{
		var l = document.getElementsByName('addFieldMembers[]');
		var n = l.length;
		var i;

		for (i = 0; i < n; i++)
		{
			if (dval == l[i].value)
			{
				l[i].checked = false;
			}
		}
		saveCheck(f);
	}
}

// 환경설정 탭 클릭시 이베트 _join_menu 값을 변경한다. 
$('.tab-a').on('click',function(){
	 var id=$(this).attr('id');
	 var id_arr=id.split('_');
	 var _join_menu=id_arr[1];
	 $('input[name="_join_menu"]').val(_join_menu);
});

// 약관/안내메세지 클릭시 이벤트 _join_tab 값을 변경한다. 
$('.agree-tab').on('click',function(){
    var href=$(this).attr('href');
    var _join_tab=href.replace('#','');
	 $('input[name="_join_tab"]').val(_join_tab);        
});

function saveCheck(f)
{

	if (f.join_auth && f.join_auth.value == '3')
	{
		if (f.join_email.value == '')
		{
			alert('이메일인증을 설정하시려면 대표이메일을 반드시 등록해야 합니다.   ');
			f.join_email.focus();
			return false;
		}
	}
	if (f.join_email_send && f.join_email_send.checked == true)
	{
		if (f.join_email.value == '')
		{
			alert('가입이메일을 발송하시려면 대표이메일을 반드시 등록해야 합니다.   ');
			f.join_email.focus();
			return false;
		}
	}
	if (confirm('변경내용을 저장하시겠습니까?       '))
	 {
         var _join_menu=$('input[name="_join_menu"]').val();
         if(_join_menu==5) f.a.value='agreesave';
         else f.a.value='member_config'; 
	 	   getIframeForAction(f);
			f.submit();
	 }else{
	 	  return false;
	 }
}

// 폼 전송 환경설정 탭/컨텐츠 보여주기  & 약관/안내메시지 탭/컨텐츠 보여주기
$(document).ready(function() {
    var _join_menu='<?php echo $_SESSION['_join_menu']?>'; // 환경설정 메뉴 값
    var _join_tab='<?php echo $_SESSION['_join_tab']?>'; // 약관/안내메세지 값
    var tab_arr={"1":"signup-config","2":"signup-form-config","3":"signup-form-add","4":"profile","5":"terms"}; // 환경설정 메뉴탭 배열
   
    // 환경설정 메뉴탭 
    $('.tab-div a').each(function(){
         $(this).removeClass('active'); // 모든 탭 active 초기화
    });  
    $('.tab-div a[href="#'+tab_arr[_join_menu]+'"]').addClass('active'); // 해당 탭 active 추가   
    $('.tab-div a[href="#'+tab_arr[_join_menu]+'"]').tab('show'); // 해당 탭 컨텐츠 보여주기 
   
    // 약관/안내 메세지 탭  
     $('.agree-tab').each(function(){
     	    var content=$(this).attr('href');
          $(this).addClass('collapsed'); // 전체 탭 초기화
          $(content).removeClass('in'); // 전체 탭 컨텐츠 초기화
     });
    $('#'+_join_tab).removeClass('collapsed'); // 해당 탭 활성화   
    $('#'+_join_tab).addClass('in'); // 해당 탭 컨텐츠 보여주기 

});


//]]>
</script>