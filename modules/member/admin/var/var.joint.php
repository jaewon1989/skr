

<div class="panel panel-default">

	<div class="panel-heading">
		회원관리 모듈은 <strong>회원가입/로그인/마이페이지</strong>를 포함하고 있습니다.<br />
		연결할 페이지를 선택해 주세요.
	</div>
	<div class="panel-body">
	    <input type="button" value="회원가입" class="btn btn-default" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&front=join');" />
	    <input type="button" value="로그인" class="btn btn-default" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&front=login');" />
	    <input type="button" value="마이페이지" class="btn btn-default" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&front=mypage');" />
   </div>

</div>




