<!-- 회원추가 모달 -->
<div class="modal fade" id="modal-member-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form name="addForm" id="add-form" class="form-horizontal rb-form" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
			<input type="hidden" name="r" value="<?php echo $r?>">
			<input type="hidden" name="m" value="<?php echo $module?>">
			<input type="hidden" name="a" value="admin_member_add">
			<input type="hidden" name="check_id" value="0">
			<input type="hidden" name="check_nic" value="0">
			<input type="hidden" name="check_email" value="0">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">회원 추가</h4>
			</div>
			<div class="modal-body">
	
				<div class="form-group rb-outside">
					<label for="inputEmail3" class="col-sm-2 control-label">아이디</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="id" placeholder="4~12자의 영문(소문자)과 숫자만 사용" value="" maxlength="12" autofocus onchange="sendCheck('rb-idcheck','id');">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default" id="rb-idcheck" onclick="sendCheck('rb-idcheck','id');">중복확인</button>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">비밀번호</label>
					<div class="col-sm-9">
						<input type="password" class="form-control" name="pw1" placeholder="">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-9">
						<input type="password" class="form-control" name="pw2" placeholder="">
					</div>
				</div>
				<hr>
				<div class="form-group">
					<label for="inputEmail3" class="col-sm-2 control-label">프로필</label>
					<div class="col-sm-9">
						<div class="media">
							<span class="pull-left">
								<img class="media-object img-circle" src="<?php echo $g['s']?>/_var/avatar/0.gif" alt="" style="width:45px">
							</span>
							<div class="media-body">
								<input type="file" name="upfile" class="hidden" id="rb-upfile-avatar" accept="image/jpg" onchange="getId('rb-photo-btn').innerHTML='이미지 파일 선택됨';">
								<button type="button" class="btn btn-default" onclick="$('#rb-upfile-avatar').click();" id="rb-photo-btn">아바타 등록</button>
								<small class="help-block">
									<code>jpg,gif,png </code> 가능하지만 <code>jpg</code> 를 추천합니다. <strong>사이즈</strong>는 <code>180*180</code> <strong>이상</strong>이어야 합니다.
							   </small>
							</div>
						</div>
					</div>		
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">이름</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="name" placeholder="이름을 입력해주세요" value="<?php echo $regis_name?>" maxlength="10">
					</div>
				</div>
				<div class="form-group rb-outside">
					<label class="col-sm-2 control-label">닉네임</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" name="nic" placeholder="닉네임을 입력해주세요" value="" maxlength="20" onchange="sendCheck('rb-nickcheck','nic');">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default" id="rb-nickcheck" onclick="sendCheck('rb-nickcheck','nic');">중복확인</button>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group rb-outside">
					<label class="col-sm-2 control-label">이메일</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="email" class="form-control" name="email" placeholder="이메일을 입력해주세요" value="" onchange="sendCheck('rb-emailcheck','email');">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default" id="rb-emailcheck" onclick="sendCheck('rb-emailcheck','email');">중복확인</button>
							</span>
						</div>
						<p class="form-control-static"><small class="text-muted">비밀번호 분실시에 사용됩니다. 정확하게 입력하세요.</small></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">연락처</label>
					<div class="col-sm-9">
						<input type="tel" class="form-control" name="tel2" placeholder="예) 010-000-0000" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">구분</label>
					<div class="col-sm-9">
						<label style="margin-top:0" class="checkbox">
				           <input type="checkbox" name="comp" value="1" class="form-control"> <i></i>기업회원
				        </label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">취소</button>
				<button type="submit" class="btn btn-primary">등록하기</button>
			</div>
		</form>
		<form name="actionform" action="<?php echo $g['s']?>/" method="post">
			<input type="hidden" name="r" value="<?php echo $r?>">
			<input type="hidden" name="m" value="<?php echo $module?>">
			<input type="hidden" name="a" value="admin_member_add_check">
			<input type="hidden" name="type" value="">
			<input type="hidden" name="fvalue" value="">
		</form>
		</div>
	</div>
</div>

