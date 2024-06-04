<div class="row">
			<div class="col-sm-3 col-lg-3 text-center">
				<br><br>
				<img alt="User Pic" src="<?php echo $g['s']?>/_var/avatar/<?php echo $_M['photo']?'180.'.$_M['photo']:'180.0.gif'?>" width="120" height="120" class="img-circle">
			</div>
			<div class="col-sm-9 col-lg-9"> 
				<table class="table rb-table-user">
					<tbody>
						<tr>
							<td>아이디</td>
							<td><?php echo $_M['id']?></td>
						</tr>
						<tr>
							<td>이름</td>
							<td><?php echo $_M['name']?></td>
						</tr>
						<tr>
							<td>닉네임</td>
							<td><?php echo $_M['nic']?></td>
						</tr>
						<tr>
							<td>이메일</td>
							<td><a href="mailto:<?php echo $_M['email']?>"><?php echo $_M['email']?></a></td>
						</tr>
						<tr>
							<td>연락처</td>
							<td><?php echo $_M['tel2']?$_M['tel2']:($_M['tel1']?$_M['tel1']:'<small>미등록</small>')?></td>
						</tr>
						<tr>
							<td>최근접속</td>
							<td><?php if($_M['last_log']):?><?php echo getDateFormat($_M['last_log'],'Y.m.d')?> (<?php echo sprintf('%d일전',-getRemainDate($_M['last_log']))?>)<?php else:?><small>기록없음</small><?php endif?></td>
						</tr>
						<tr>
							<td>등록일</td>
							<td><?php echo getDateFormat($_M['d_regis'],$lang['admin']['ad011'])?> (<?php echo sprintf('%d일전',-getRemainDate($_M['d_regis']))?>)</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>