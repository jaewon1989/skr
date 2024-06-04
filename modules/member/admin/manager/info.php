	<form name="addForm" class="form-horizontal" action="<?php echo $g['s']?>/" method="post" enctype="multipart/form-data" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>">
		<input type="hidden" name="m" value="<?php echo $module?>">
		<input type="hidden" name="a" value="admin_member_add">
		<input type="hidden" name="id" value="<?php echo $_M['id']?>">
		<input type="hidden" name="uid" value="<?php echo $_M['uid']?>">
		<input type="hidden" name="avatar" value="<?php echo $_M['photo']?>">
		<input type="hidden" name="check_id" value="1">
		<input type="hidden" name="check_nic" value="1">
		<input type="hidden" name="check_email" value="1">
		<input type="submit" style="position:absolute;left:-1000px;">
		<div class="form-group">
			<label for="inputEmail3" class="col-sm-2 control-label">아이디</label>
			<div class="col-sm-9">
				<p class="form-control-static"><?php echo $_M['id']?></p>
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
						<img class="media-object img-circle" src="<?php echo $g['s']?>/_var/avatar/<?php echo $_M['photo']?$_M['photo']:'0.gif'?>" alt="" style="width:45px">
					</span>
					<div class="media-body">
						<input type="file" name="upfile" class="hidden" id="rb-upfile-avatar" accept="image/jpg" onchange="getId('rb-photo-btn').innerHTML='이미지 파일 선택됨';">
						<button type="button" class="btn btn-default" onclick="$('#rb-upfile-avatar').click();" id="rb-photo-btn">아바타 등록</button>
						<small class="help-block">
							<code>jpg,gif,png </code> 가능하지만 <code>jpg</code> 를 추천합니다. <strong>사이즈</strong>는 <code>180*180</code> <strong>이상</strong>이어야 합니다.
							<?php if($_M['photo']):?> <label>( <input type="checkbox" name="avatar_delete" value="1"> 현재 아바타 삭제 )</label><?php endif?>
						</small>
					</div>
				</div>
			</div>		
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">이름</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="name" placeholder="" value="<?php echo $_M['name']?>" maxlength="10">
			</div>
		</div>
		<div class="form-group rb-outside">
			<label class="col-sm-2 control-label">닉네임</label>
			<div class="col-sm-9">
				<div class="input-group">
					<input type="text" class="form-control" name="nic" placeholder="" value="<?php echo $_M['nic']?>" maxlength="20" onchange="sendCheck('rb-nickcheck','nic');">
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
					<input type="email" class="form-control" name="email" placeholder="" value="<?php echo $_M['email']?>" onchange="sendCheck('rb-emailcheck','email');">
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
				<input type="tel" class="form-control" name="tel2" placeholder="예) 010-000-0000" value="<?php echo $_M['tel2']?$_M['tel2']:$_M['tel1']?>">
			</div>
		</div>
		</form>
		<form name="actionform" action="<?php echo $g['s']?>/" method="post">
			<input type="hidden" name="r" value="<?php echo $r?>">
			<input type="hidden" name="m" value="<?php echo $module?>">
			<input type="hidden" name="a" value="admin_member_add_check">
			<input type="hidden" name="type" value="">
			<input type="hidden" name="fvalue" value="">
		</form>