<link href="<?php echo $g['dir_module_skin']?>/_main.css" rel="stylesheet">
<div class="row">
	 <div class="col-sm-1 hidden-xs pull-left">
	 	   <?php if($g['member']['photo']):?>
	 	       <img src="<?php echo $g['url_root']?>/_var/avatar/<?php echo $g['member']['photo']?>" alt="회원 아바타" class="mbr-simbol" />
         <?php else:?>
              <img src="<?php echo $g['url_root']?>/_var/avatar/180.0.gif" alt="회원 아바타 샘플" class="mbr-simbol" />
	 	  <?php endif?>
	 </div>
	 <div class="col-sm-11">
	 	  <h2 class="view-title">
	 	  	    <?php echo getStrCut($R['subject'],$d['bbs']['sbjcut'],'...')?>
         </h2>
	 	  <div class="col-sm-8 text-muted nopd-left">
	 	  	  <span class="title-ele"><?php echo $R[$_HS['nametype']]?></span>
	 	  	  <span class="title-ele"><?php echo getDateFormat($R['d_regis'],$d['theme']['date_viewf'])?></span>
	 	  	  <span class="title-ele">조회 : <?php echo $R['hit']?></span>
	 	  	   <?php if($d['theme']['show_score1']):?><span class="title-ele hidden-xs">공감 : <span class="num"><?php echo $R['score1']?></span></span> <?php endif?>
				<?php if($d['theme']['show_score2']):?><span class="title-ele hidden-xs">비공감 : <span class="num"><?php echo $R['score2']?></span></span> <?php endif?>
				 <?php if($d['theme']['snsping']):?>
		 	  	<span class="sns-ele" data-toggle="tooltip" title="facebook 보내기" onclick="snsWin('f');"><i class="fa fa-facebook"></i></span>
			   <span class="sns-ele" data-toggle="tooltip" title="twitter  보내기" onclick="snsWin('t');"><i class="fa fa-twitter"></i></span>
			   <span class="sns-ele" data-toggle="tooltip" title="google+ 보내기" onclick="snsWin('g');"><i class="fa fa-google-plus"></i></span>
			  <?php endif?>
		  </div>
	 	  <div class="pull-right hidden-xs" id="tool-icon">
 	  	    <?php if($d['theme']['use_singo']):?>
		 	  	      <a href="<?php echo $g['bbs_action']?>singo&amp;uid=<?php echo $R['uid']?>" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 신고하시겠습니까?');"><i class="fa fa-bell-o"></i>신고</a>
		 	  	  <?php endif?>   
		 	  	  <?php if($d['theme']['use_print']):?>
		 	  	      <a href="javascript:printWindow('<?php echo $g['bbs_print'].$R['uid']?>');" ><i class="fa fa-print"></i>인쇄</a>
		 	  	   <?php endif?>   
		 	  	 	<?php if($d['theme']['use_scrap']):?>
				      <a href="<?php echo $g['bbs_action']?>scrap&amp;uid=<?php echo $R['uid']?>"  target="_action_frame_<?php echo $m?>" onclick="return isLogin2();"><i class="fa fa-paperclip"></i>스크랩</a>
			 <?php endif?>
	 	  </div>
	 </div>
	 <div class="col-sm-12 panel-body post-content">
	    	   <?php echo getContents($R['content'],$R['html'])?>
	 </div>
	 <br>
	 <br>
    <?php if($d['theme']['show_score1']||$d['theme']['show_score2']):?>
	 <div class="text-center panel-body">
			<?php if($d['theme']['show_score1']):?>
				<a href="<?php echo $g['bbs_action']?>score&amp;value=good&amp;uid=<?php echo $R['uid']?>" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 평가하시겠습니까?');" class="btn btn-default">
					 <i class="fa fa-thumbs-o-up"></i> 공감 <span class="text-muted"><em><?php echo $R['score1']?></em></span>
			  	</a>   
			<?php endif?>
			<?php if($d['theme']['show_score2']):?>
             <a href="<?php echo $g['bbs_action']?>score&amp;value=bad&amp;uid=<?php echo $R['uid']?>" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 평가하시겠습니까?');" class="btn btn-default">
				    <i class="fa fa-thumbs-o-down"></i> 비공감 <span class="text-muted"><em><?php echo $R['score2']?></em></span>
		  	    </a>    
		   <?php endif?>
	  </div>
	  <?php endif?>
     <br>
     <?php $last_attach=$d['upload']['count']-$hidden_file_num; // 전체 첨부파일 수에서 숨김 첨부파일 수량을 차감한 실제 보여질 수량 : _view.php 61 라인에 추가 2015. 1. 2 ?>
     <?php if($d['upload']['data']&&$d['theme']['show_upfile']&&$last_attach>0):?> 
     <div class="col-sm-12">
	     <article class="panel panel-default">
	        <header class="panel-heading"><i class="fa fa-paperclip fa-lg"></i> 첨부파일</header>
	        <div class="list-group">
	        	<?php foreach($d['upload']['data'] as $_u):?>
				<?php if($_u['hidden'])continue?>
				<?php 
				   $ext_to_fa=array('xls'=>'excel','xlsx'=>'excel','ppt'=>'powerpoint','pptx'=>'powerpoint','txt'=>'text','pdf'=>'pdf','zip'=>'archive','doc'=>'word');
				   $ext_icon=in_array($_u['ext'],array_keys($ext_to_fa))?'-'.$ext_to_fa[$_u['ext']]:''; 
				 ?>

	            <li class="list-group-item"><i class="fa fa-file<?php echo $ext_icon?>-o"></i> 
	            	<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;a=download&amp;uid=<?php echo $_u['uid']?>" title="<?php echo $_u['caption']?>"><?php echo $_u['name']?></a> 
	            	<small class="text-muted">(<?php echo getSizeFormat($_u['size'],1)?>)</small> <span title="다운로드 수" data-toggle="tooltip" class="badge hidden-xs"><?php echo number_format($_u['down'])?></span>
	            	<?php if($my['admin']):?>
                  <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=bbs&amp;a=delete_attach&amp;uid=<?php echo $_u['uid']?>" class="btn btn-danger btn-xs" onclick="return confirm('정말로 삭제하시겠습니까?');" data-toggle="tooltip" title="삭제"> <i class="fa fa-trash-o fa-lg"></i></a>
	              <?php endif?>
	            </li>
	          <?php endforeach?> 
	          
	        </div>
	    </article>
	 </div>   
	 <?php endif?>
		
	 <?php if($R['tag']&&$d['theme']['show_tag']):?>
	 <div class="col-sm-12">
	    <div class="panel panel-default">
	    	  <div class="panel-body">
	            <div class="col-sm-12 nopd-left">
	                     <?php $_tags=explode(',',$R['tag'])?>
						      <?php $_tagn=count($_tags)?>
						     <?php $i=0;for($i = 0; $i < $_tagn; $i++):?>
						     <?php $_tagk=trim($_tags[$i])?> 
	                    <span class="badge tag-links">
	                    	  <i class="fa fa-tags" title="태그"></i>
	                    	  <a href="<?php echo $g['bbs_orign']?>&amp;where=subject|tag&amp;keyword=<?php echo urlencode($_tagk)?>"> <?php echo $_tagk?></a>
	                    	</span>
	                    <?php endfor?>
	              </div>
	          </div>    
	    </div>
	 </div>             
    <?php endif?>	 
    <div class="panel-body">
	 	<div class="pull-right">
         <div class="btn-group btn-group-sm">
            <?php if($my['admin'] || $my['uid']==$R['mbruid']):?>
	            <a href="<?php echo $g['bbs_modify'].$R['uid']?>" class="btn btn-default">수정</a>
	            <a href="<?php echo $g['bbs_delete'].$R['uid']?>" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 삭제하시겠습니까?');" class="btn btn-default">삭제</a> 
             <?php endif?>
             <?php if($my['admin']&&$d['theme']['use_reply']):?>
                 <a href="<?php echo $g['bbs_reply'].$R['uid']?>" class="btn btn-default">답변</a>
             <?php endif?>
             <a href="<?php echo $g['bbs_list']?>" class="btn btn-default">목록</a>
          </div>
       </div>
	 </div>
    <!-- 댓글 인클루드 -->
	 <?php if(!$d['bbs']['c_hidden']):?>
	    <?php getWidget('default/comment',array('theme'=>'default','parent'=>$m.'-'.$R['uid'],'feed_table'=>$table[$m.'data']));?>
    <?php endif?>
</div> <!--.row-->

<script type="text/javascript">
//<![CDATA[

// 툴팁 이벤트 
$(document).ready(function() {
    $('[data-toggle=tooltip]').tooltip();
}); 

<?php if($d['theme']['snsping']):?>
function snsWin(sns)
{
	var snsset = new Array();
	var enc_tit = "<?php echo urlencode($_HS['title'])?>";
	var enc_sbj = "<?php echo urlencode($R['subject'])?>";
	var enc_url = "<?php echo urlencode($g['url_root'].($_HS['rewrite']?($_HS['usescode']?'/'.$r:'').'/b/'.$R['bbsid'].'/'.$R['uid']:'/?'.($_HS['usescode']?'r='.$r.'&':'').'m='.$m.'&bid='.$R['bbsid'].'&uid='.$R['uid']))?>";
	var enc_tag = "<?php echo urlencode(str_replace(',',' ',$R['tag']))?>";
	snsset['t'] = 'http://twitter.com/home/?status=' + enc_sbj + '+++' + enc_url;
	snsset['f'] = 'http://www.facebook.com/sharer.php?u=' + enc_url + '&t=' + enc_sbj;
	snsset['g'] = 'https://plus.google.com/share?url=' + enc_url;
	window.open(snsset[sns]);
}
<?php endif?>

//로그인체크
function isLogin2()
{
	if (memberid == '')
	{
		alert('로그인을 먼저 해주세요.  ');
		return false;
	}
	return true;
}

function printWindow(url) 
{
	window.open(url,'printw','left=0,top=0,width=700px,height=600px,statusbar=no,scrollbars=yes,toolbar=yes');
}
//]]>
</script>

<?php if($d['theme']['show_list']&&$print!='Y'):?>
<?php include_once $g['dir_module'].'mod/_list.php'?>
<?php include_once $g['dir_module_skin'].'list.php'?>
<?php endif?>

