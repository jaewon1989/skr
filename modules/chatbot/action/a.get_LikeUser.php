<style type="text/css">

.modal-body {
	padding: 0;
	overflow-y: scroll;
	overflow-x: hidden;
}
.modal-body .rb-friend {
	float: right;
	line-height: 50px
}
.modal-body .rb-friend .btn {
	padding-left: 8px;
	padding-right: 8px;
	line-height: 22px;
	font-size: 12px
}
.modal-body .rb-friend .dropdown-menu {
	min-width: 100px;
		margin-top: -10px;
		font-size: 12px
}
.modal-body .list-group {
	margin-bottom: 0
}
.modal-body .list-group-item:first-child {
	border-top: none;
		border-top-left-radius: 0;
		border-top-right-radius: 0;
}
.modal-body .list-group-item {
	border-left: none;
	border-right: none
}
.modal-body .media {
	margin-top: 0
} 
.modal-body .media-body a {
	color: #9197a3;
	font-size: 12px;
}
.modal-body .media-object {
	width: 50px;
	height: 50px
}
.modal-body .media-heading {
	margin-bottom: 0;
	padding-top: 5px
}
.modal-body .media-heading a {
	font-size: 14px;
	color: #232937;
}

</style>
<?php
if(!defined('__KIMS__')) exit;
require_once $theme.'/function.php';
// 친구관련 버튼 세팅 
$mod='like-user';
$btn_size='sm';

$recnum='';
$orderby='asc';
$sort='uid';
$GCD=getDbArray($table['s_opinion'],"module='".$module."' and entry='".$entry."'",'*',$sort,$orderby,$recnum,1);
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">이 게시물을 좋아하는 사람들</h4>
</div>
<div class="modal-body" style="height: 400px">
	<ul class="list-group">
		<?php while($R=db_fetch_array($GCD)):?>
		<?php
		 $M=getDbData($table['s_mbrdata'],'memberuid='.$R['mbruid'],'memberuid,name,nic,photo');
		 // 아바타 사진 url 세팅
		 if($M['photo']) $avatar_img=$g['url_root'].'/avatar/'.getAvatarResize($M['photo'],'m');
		 else  $avatar_img=$g['url_root'].'/_var/avatar/0.gif';			 
		 ?>
			 <li class="list-group-item">
			<div class="rb-friend">
                      <?php if($my['uid']!=$R['mbruid']):?>
			       <?php echo getFriendBtn($my['uid'],$M['memberuid'],$mod,$btn_size)?> <!-- function.php 해당 함수 참조 -->
                      <?php endif?>
			</div>
			<div class="media">
				<div class="media-left">
						<img class="media-object" src="<?php echo $avatar_img?>" alt="<?php echo $M[$_HS['nametype']]?> 아바타">
				</div>
				<div class="media-body">
					<h4 class="media-heading"><a href="#" class="rb-user-popover"><?php echo $M[$_HS['nametype']]?></a></h4>
					<?php echo getTGTfriend($my['uid'],$M['memberuid'])?>
				</div>
			</div>
		</li>
		<?php endwhile?>
	</ul>

</div>
<?php
exit;
?>


	 