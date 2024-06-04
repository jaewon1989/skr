
<div id="page-profile">
	<!-- 마이페이지 헤더 인클루드 -->
	<?php include_once $g['dir_module_skin'].'_cover.php';?>
	
	<div class="row">
		<div class="col-md-8  col-md-push-4 text-muted" id="sidebar">
			<div class="panel panel-default">
				 <div class="panel-heading">
				  	 <h3 class="panel-title"><i class="fa fa-edit"></i> 내가 등록한 게시물 
                    <span class="pull-right"><a href="<?php echo $g['url_reset']?>&amp;page=post" data-toggle="tooltip" title="더보기"><i class="fa fa-plus text-muted"></i></a></span>
				  	 </h3>
				 </div>
				 <ul class="list-group">
						<?php $_POST = getDbArray($table['bbsdata'],'site='.$s.' and mbruid='.$my['uid'],'*','gid','asc',5,1)?>
						<?php while($_R=db_fetch_array($_POST)):?>
						<?php $_R['mobile']=isMobileConnect($_R['agent'])?>
						<li class="list-group-item link_a">
							<a href="<?php echo getPostLink($_R)?>" target="_blank" class="link_txt">
						      <?php if($_R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
                        <?php if($_R['category']):?>[<?php echo $_R['category']?>] <?php endif?>
                       <?php echo getStrCut($_R['subject'],$d['bbs']['sbjcut'],'')?>
                       <?php if(strstr($_R['content'],'.jpg')):?><i class="fa fa-image fa-lg"></i><?php endif?>
                       <?php if($_R['upload']):?><i class="glyphicon glyphicon-floppy-disk glyphicon-lg"></i><?php endif?>
                       <?php if($_R['hidden']):?><i class="fa fa-lock fa-lg"></i><?php endif?>
                       <?php if($_R['comment']):?><span class="badge"><?php echo $_R['comment']?><?php echo $_R['oneline']?'+'.$_R['oneline']:''?></span><?php endif?>
                       <?php if($_R['trackback']):?><span class="trackback">[<?php echo $_R['trackback']?>]</span><?php endif?>
                        <?php if(getNew($_R['d_regis'],24)):?><span class="label label-danger"><small>N</small></span><?php endif?>  
                      </a>  
						</li>
						<?php endwhile?>
						<?php if(!db_num_rows($_POST)):?>
						<li class="list-group-item">등록된 데이타가 없습니다.</li>
						<?php endif?>
				 </ul>
			</div> <!--.panel : 게시글 -->
			<div class="panel panel-default">
				 <div class="panel-heading">
				  	 <h3 class="panel-title"><i class="fa fa-comment-o"></i> 내가 등록한 댓글 
                     <span class="pull-right"><a href="<?php echo $g['url_reset']?>&amp;page=comment" data-toggle="tooltip" title="더보기"><i class="fa fa-plus text-muted"></i></a></span>  
				  	 </h3>
				 </div>
				 <ul class="list-group">
						<?php $_POST = getDbArray($table['s_comment'],'site='.$s.' and mbruid='.$my['uid'],'*','uid','asc',5,1)?>
		     			<?php while($_R=db_fetch_array($_POST)):?>
						<?php $_R['mobile']=isMobileConnect($_R['agent'])?>
						<li class="list-group-item link_a" >
						    <a href="<?php echo getCyncUrl($_R['sync'].',CMT:'.$_R['uid'])?>" target="_blank" class="link_txt"> 
						      <?php if($_R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
                        <?php if($_R['category']):?>[<?php echo $_R['category']?>] <?php endif?>
                       <?php echo $_R['subject']?>
                       <?php if(strstr($_R['content'],'.jpg')):?><ass="fa fa-image fa-lg"></i><?php endif?>
                       <?php if($_R['upload']):?><i class="glyphicon glyphicon-floppy-disk glyphicon-lg"></i><?php endif?>
                       <?php if($_R['hidden']):?><i class="fa fa-lock fa-lg"></i><?php endif?>
                        <?php if($_R['oneline']):?><span class="badge"><?php echo $_R['oneline']?></span><?php endif?>
                       <?php if(getNew($_R['d_regis'],24)):?><span class="label label-danger"><small>N</small></span><?php endif?>  
                     </a>   
						</li>
						<?php endwhile?>
						<?php if(!db_num_rows($_POST)):?>
						<li class="list-group-item">등록된 데이타가 없습니다.</li>
						<?php endif?>
				 </ul>
			</div> <!--.panel : 댓글 -->
			<div class="panel panel-default">
				 <div class="panel-heading">
				  	 <h3 class="panel-title"><i class="fa fa-comment"></i> 내 게시물에 달린 댓글                  
				  	 </h3>
				 </div>
				 <ul class="list-group">
						<?php $_POST = getDbArray($table['s_comment'],'site='.$s.' and parentmbr='.$my['uid'].' and mbruid<>'.$my['uid'],'*','uid','asc',10,1)?>
			         <?php while($_R=db_fetch_array($_POST)):?>
						<?php $_R['mobile']=isMobileConnect($_R['agent'])?>
						<li class="list-group-item link_a" >
						    <a href="<?php echo getCyncUrl($_R['sync'].',CMT:'.$_R['uid'])?>" target="_blank" class="link_txt"> 
						      <?php if($_R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
                        <?php if($_R['category']):?>[<?php echo $_R['category']?>] <?php endif?>
                       <?php echo $_R['subject']?>
                       <?php if(strstr($_R['content'],'.jpg')):?><ass="fa fa-image fa-lg"></i><?php endif?>
                       <?php if($_R['upload']):?><i class="glyphicon glyphicon-floppy-disk glyphicon-lg"></i><?php endif?>
                       <?php if($_R['hidden']):?><i class="fa fa-lock fa-lg"></i><?php endif?>
                        <?php if($_R['oneline']):?><span class="badge"><?php echo $_R['oneline']?></span><?php endif?>
                       <?php if(getNew($_R['d_regis'],24)):?><span class="label label-danger"><small>N</small></span><?php endif?>  
                     </a>   
						</li>
						<?php endwhile?>
						<?php if(!db_num_rows($_POST)):?>
						<li class="list-group-item">등록된 데이타가 없습니다.</li>
						<?php endif?>
				 </ul>
			</div> <!--.panel : 내 게시물에 달린 댓글  -->
			<div class="panel panel-default">
				 <div class="panel-heading">
				  	 <h3 class="panel-title"><i class="fa fa-comments-o"></i> 내 댓글에 달린 한줄 의견 </h3>
				 </div>
				 <ul class="list-group">
							<?php $_POST = getDbArray($table['s_oneline'],'site='.$s.' and parentmbr='.$my['uid'].' and mbruid<>'.$my['uid'],'*','uid','desc',10,1)?>
							<?php while($_O=db_fetch_array($_POST)):?>
							<?php $_R=getUidData($table['s_comment'],$_O['parent'])?>
							<?php $_O['mobile']=isMobileConnect($_O['agent'])?>
						<li class="list-group-item link_a" >
						    <a href="<?php echo getCyncUrl($_R['sync'].',CMT:'.$_R['uid'])?>" target="_blank" class="link_txt"> 
						      <?php if($_R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
                        <?php if($_R['category']):?>[<?php echo $_R['category']?>] <?php endif?>
                       <?php echo $_R['subject']?>
                       <?php if(getNew($_R['d_regis'],24)):?><span class="label label-danger"><small>N</small></span><?php endif?>  
                     </a>   
						</li>
						<?php endwhile?>
						<?php if(!db_num_rows($_POST)):?>
						<li class="list-group-item">등록된 데이타가 없습니다.</li>
						<?php endif?>
				 </ul>
			</div> <!--.panel : 내 게시물에 달린 댓글  -->

		</div> <!--.col-md-7-->
		<div class="col-md-4 col-md-pull-8" id="content">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-user"></i> 내 정보 
					    <span class="pull-right"><a href="<?php echo $g['url_reset']?>&amp;page=info" data-toggle="tooltip" title="정보수정"><i class="fa fa-pencil text-muted"></i></a></span>
					</h3>
				</div>
				<ul class="list-group">
					<li class="list-group-item">이름/닉네임 : <?php echo $my['name']?>/<?php echo $my['nic']?$my['nic']:'없음'?></li>
					<li class="list-group-item">생년월일  : <?php if($my['birth1']):?><?php echo $my['birth1']?>/<?php echo substr($my['birth2'],0,2)?>/<?php echo substr($my['birth2'],2,2)?><?php endif?> </li>					
					<li class="list-group-item">나이/성별 : <?php if($my['birth1']):?><?php echo getAge($my['birth1'])?>세<?php endif?><?php if($my['birth1']&&$my['sex']):?> / <?php endif?><?php if($my['sex']):?><?php echo getSex($my['sex'])?>성<?php endif?></li>
					<li class="list-group-item">가입일자 : <?php echo getDateFormat($my['d_regis'],'Y/m/d H:i')?></li>
					<li class="list-group-item">최근접속 : <?php echo getDateFormat($my['last_log'],'Y/m/d H:i')?> (<?php echo $lastlogdate?$lastlogdate.'일전':'오늘'?>)</li>
					<li class="list-group-item">회원등급 : <?php echo $levelname['name']?> (<?php echo $my['level']?> / <?php echo $levelnum['uid']?>)</li>
					<li class="list-group-item">거주지역 : <?php echo $my['addr0']?></li>
					<li class="list-group-item">홈페이지 : <?php echo $my['home']?$my['home']:'미등록'?></li>
				</ul>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-camera"></i> 내가 등록한 이미지</h3>
				</div>
				<div class="timeline friends">
					<ul class="list-inline">
					 <?php $up_img=array()?>
					 <?php $caption=array()?>
					 <?php $href=array()?>
					 <?php $ran_img=array()?>
					 <?php $MCD=getDbArray($table['bbsupload'],'mbruid='.$my['uid'].' and type=2','*','uid','desc',9,1)?>
                <?php $i=1;while($U=db_fetch_array($MCD)):?>
                <?php $d_regis=getDateFormat($U['d_regis'],'Y.m.d')?>
                <?php $up_img[$i]='./modules/bbs/upload/'.$U['folder'].'/'.$U['tmpname']?>
                <?php $href[$i]=getCyncUrl($U['cync'])?>
                <?php $caption[$i]=$U['caption']?$U['caption']:$d_regis?>               
                <?php $i++;endwhile?>
                <?php for($i=1;$i<10;$i++):?>
                <?php $ran_img[$i]=$g['dir_module_skin'].'image/post_'.$i.'.gif'?>
					 <li><a href="<?php echo $href[$i]?$href[$i]:'##'?>"><img src="<?php echo $up_img[$i]?$up_img[$i]:$ran_img[$i]?>" width="91" height="91"><span class="name small"><?php echo $caption[$i]?$caption[$i]:'sample'?></span></a></li>
				    <?php endfor?>
					</ul>					
				</div>
			</div>
		</div>
	</div> <!-- .row : 해당 페이지 내용 -->
</div>  <!-- #page-profile : 모든 페이지에 공통으로 적용되야 하는 id -->
<!-- 공통 스크립트 -->
<?php include $g['dir_module_skin'].'_common_script.php'?>
