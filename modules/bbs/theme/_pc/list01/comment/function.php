<?php
if(!defined('__KIMS__')) exit;
function getCMNTUpfiles($R)
{
	if (!$R['upload']) return array();
	else
	{
		global $table,$m;
		$d['upload'] = array();
		$d['upload']['tmp'] = $R['upload'];
		$d['_pload'] = getArrayString($R['upload']);
		foreach($d['_pload']['data'] as $_val)
		{
			$U = getUidData($table['s_upload'],$_val);
			if (!$U['uid'])
			{
				$R['upload'] = str_replace('['.$_val.']','',$R['upload']);
				$d['_pload']['count']--;
			}
			else {
				$d['upload']['data'][] = $U;
			}
			if (!$U['cync'])
			{
				$cyncArr = getArrayString($R['cync']);
				$_CYNC = "cync='[".$m."][".$R['uid']."][uid,down][".$table['s_comment']."][".$R['mbruid']."][".$cyncArr['data'][5].",CMT:".$R['uid']."#CMT]'";
				getDbUpdate($table['s_upload'],$_CYNC,'uid='.$U['uid']);
			}
		}
		if ($R['upload'] != $d['upload']['tmp'])
		{
			getDbUpdate($table['s_comment'],"upload='".$R['upload']."'",'uid='.$R['uid']);
		}
		$d['upload']['count'] = $d['_pload']['count'];
		return $d['upload'];
	}
}

// 지난 시간 얻기 함수 
function getJNTime($d_regis)
{
	 global $g;
	 //include $g['path_core'].'function/sys.func.php';
   // 최근 이슈 경과 시간 추출
	 $dnowdate=date("Y-m-j G:i:s");// 오늘 날짜 
	 $ddate=getDateFormat($d_regis,'Y-m-j G:i:s');//Last-ay
	 $timediffer=strtotime($dnowdate) - strtotime("$ddate GMT"); // 기준일과 오늘의 시간(초) 차이
	 $dval=$timediffer+32400;

    if((60>$dval && $dval>0)||!$dval){
    	 $JN_time=date('s 초 전',$timediffer);
    }elseif(3600>$dval&& $dval>60){
                   $JN_time=date('i 분 전',$timediffer);
    }elseif(86400>$dval && $dval>3600){
    	 $JN_time=date('G 시간 전',$timediffer);
    }elseif(2592000>$dval && $dval>86400){
                   $JN_time=date('j 일 전',$timediffer);
    }elseif(31104000>$dval && $dval>2592000){
    	 $JN_time=date('n 개월 전',$timediffer);
    }else{
    	$JN_time='-';
    }  
    return $JN_time;
}

// 한 줄 의견 리스트 출력함수  
function getOnelineList($theme,$parent)
{
    global $g,$table,$_HS,$m,$my;

     include $theme.'_var.php';// 설정파일 인클루드 
    $g['img_module_skin']=$theme.'image/';

    $TCD = getDbArray($table['s_oneline'],'parent='.$parent,'*',$d['oneline']['sort'],$d['oneline']['orderby'],0,0);
    while($O=db_fetch_array($TCD))
     {
     	    echo '<div class="media one-list">
		                  <a target="_blank" title="트위터 프로필 새창 열림" data-toggle="tooltip" href="" class="pull-left hidden-xs rb-avatar">
								     <img src="'.$g['img_module_skin'].'/mbrimg_blank.png" class="media-object img-rounded" alt="32x32" data-src="holder.js/32x32" style="width: 32px; height: 32px;">
								</a>									 
								 <div class="media-body">
								      <h6 class="media-heading">
				  		                <a class="a-muted" data-toggle="popover" href="">'.($O[$_HS['nametype']]?$O[$_HS['nametype']]:$O['name']).'</a>
				  		                <span class="text-muted small time-wrap"><time title="'.getDateFormat($O['d_regis'],'Y년 m월 d일  H시 i분').'" data-toggle="tooltip" class="timeago live-tooltip">'.getJNTime($O['d_regis']).'</time></span>
				  	                   <span class="pull-right">';
				  	                      echo '<input type="hidden" name="theme" class="theme" value="'.$theme.'" />';
				  	                      echo '<input type="hidden" name="parent" class="parent" value="'.$parent.'" />';
				  	                      echo '<input type="hidden" name="o_content" class="o_content" value="'.getContents($O['content'],$O['html'],$keyword).'" />';

				  	                     if($my['admin']||$O['mbruid']==$my['uid'])
                                     {
                                      	  echo '<span class="text-muted"><a href="#" id="delete-'.$O['uid'].'" class="live-tooltip one-tools" title="삭제" ><i class="fa fa-trash-o"></i></a></span>';
                                      	  echo '<span class="text-muted"><a href="#" id="edit-'.$O['uid'].'" class="live-tooltip one-tools" title="수정" ><i class="fa fa-edit"></i></a></span>';
                                      }else{
                                          echo '<span class="text-muted"><a href="#" id="report-'.$O['uid'].'" class="live-tooltip one-tools" title="신고" ><i class="fa fa-frown-o"></i></a></span>';      
                                     }
				  	              echo '</span>
				  	               </h6>
										<article class="small">'.getContents($O['content'],$O['html'],$keyword).'</article>
						       </div>';
				echo '</div>'; // media : 반복되는 리스트 

     } // while
}


/*
   댓글 리스트 출력 함수
   $puid : parent uid - 게시글 uid
*/ 

function getCommentList($theme,$parent,$_where,$recnum,$sort,$orderby1,$orderby2,$cp)
{
    global $g,$table,$_HS,$m,$my;

    include $theme.'_var.php';// 설정파일 인클루드 
    $g['img_module_skin']=$theme.'image/';
    $NCD = array();
	 $RCD = array();
	 $cp = $cp ? $cp : 1;
	 $sort= $sort? $sort : 'uid';
	 $orderby1 = $orderby1 ? $orderby1 : $d['comment']['orderby1'];
	 $orderby2 = $orderby2 ? $orderby2 : $d['comment']['orderby2'];
    $recnum = $recnum ? $recnum : $d['comment']['recnum'];
    $cmentque = " and parent='".$parent."'";
    if($_where) $cmentque .=" and ".$_where;

	 $PCD = getDbArray($table['s_comment'],'notice=1'.$cmentque,'*',$sort,$orderby1,0,0);
	 $TCD = getDbArray($table['s_comment'],'notice=0'.$cmentque,'*',$sort,$orderby2,$recnum,$cp);
	 $NUM = getDbRows($table['s_comment'],'notice=0'.$cmentque);
	 $TPG = getTotalPage($NUM,$recnum);
	 while($_R = db_fetch_array($PCD)) $NCD[] = $_R;
	 while($_R = db_fetch_array($TCD)) $RCD[] = $_R;
    //echo $cmentque;
    $RCD=$NCD+$RCD;
    $i=1;
    $namegi=$NUM-($cp*$recnum);
    if($namegi>0){
    	$namegi_ment='총 <span class="text-danger namegi">'.$namegi.'</span> 개의 댓글이 더 있습니다.';
    	$btn_more='btn-more';
    	$is_namegi=1;
    }else{
    	$namegi_ment='더이상 댓글이 없습니다.';
      $btn_more='disabled';
      $is_namegi=0;
    } 

    foreach($RCD as $C) 
    {
    	 $C['mobile']=isMobileConnect($C['agent']);
	    if($C['mbruid']) $M=getDbData($table['s_mbrdata'],'memberuid='.$C['mbruid'],'*');else $M=array();
	    $isSECRETCHECK=true;
		 $JN_time=getJNTime($C['d_regis']); // 지난시간 얻기 함수 호출
       
		 echo '
		   <a name="one-write2-'.$C['uid'].'"></a>
	   	<li id="'.$C['uid'].'-'.$C['score1'].'-'.$C['oneline'].'" class="list-group-item comment-list">
				<div class="media" id="cuid-'.$C['uid'].'">
				   <input type="hidden" name="is_namegi" value="'.$is_namegi.'"/>
				   <input type="hidden" name="TPG" value="'.$TPG.'"/>
				   <input type="hidden" name="theme" class="theme" value="'.$theme.'" />
	  	         <input type="hidden" name="parent" class="parent" value="'.$parent.'" />
	  	         <input type="hidden" name="c_content" class="c_content" value="'.htmlspecialchars(getContents($C['content'],$C['html'],$keyword)).'" />

					<a target="_blank" title="페이스북 프로필 새창 열림" data-toggle="tooltip" href="#" class="pull-left hidden-xs rb-avatar">
						<img src="'.$g['img_module_skin'].'/mbrimg_blank.png" class="media-object img-rounded" alt="48x48" data-src="holder.js/48x48" style="width: 48px; height: 48px;">
					</a>
					<div class="media-body">
					  	<h5 class="media-heading">
					  		 <a class="a-muted" data-toggle="popover" href="">'.($C[$_HS['nametype']]?$C[$_HS['nametype']]:$C['name']).'</a>
					  		 <span class="text-muted small time-wrap"><time title="'.getDateFormat($C['d_regis'],'Y년 m월 d일  H시 i분').'" data-toggle="tooltip" class="timeago live-tooltip">'.$JN_time.'</time></span>
					  	    <span class="pull-right">';
	  	             
				  	                     if($my['admin']||$C['mbruid']==$my['uid'])
                                    {
                                      	  echo '<span class="text-muted"><a href="#" id="delete-'.$C['uid'].'" class="live-tooltip cmt-tools" title="삭제" ><i class="fa fa-trash-o"></i></a></span>';
                                      	  echo '<span class="text-muted"><a href="#" id="edit-'.$C['uid'].'" class="live-tooltip cmt-tools" title="수정" ><i class="fa fa-edit"></i></a></span>';
                                    }else{
                                          echo '<span class="text-muted"><a href="#" id="report-'.$C['uid'].'" class="live-tooltip cmt-tools" title="신고" ><i class="fa fa-frown-o"></i></a></span>';      
                                    }
				  	           echo '</span>
					  	</h5>
					  	<hr class="cmt-hr">';
					  	echo '<div class="panel-body nopd-left">';
					    	if(!$C['hidden'] || $my['admin'] || ($my['id']&&$my['id']==$C['id']) || strstr($_SESSION['module_'.$m.'_view'],'['.$C['uid'].']')){
					   	   echo 	getContents($C['content'],$C['html'],$keyword);
					    	}else{
                        $isSECRETCHECK=false;
						    echo '<i class="fa fa-lock-o"></i> <a href="'.$g['cment_modify'].$C['uid'].'" onclick="return cmentHidden(\''.$C['id'].'\',\''.$C['uid'].'\',event);">비공개 댓글입니다.</a>';
					  	    }
	               echo '</div>
						<div class="clearfix">
							<div class="pull-left" style="margin-top:4px;">
								<a class="small live-tooltip" href="#comments-'.$C['uid'].'-reply" data-parent="#comments-'.$C['uid'].'" data-toggle="collapse">답글 <span id="one-num-'.$C['uid'].'">'.$C['oneline'].'</span> <i class="fa fa-sort-desc"></i></a>
							</div>
							<div class="pull-right">
								<div class="btn-group btn-group-sm">
									<button title="공감" data-toggle="tooltip" class="btn btn-default btn-sm btn-cop live-tooltip cmt-tools" type="button" id="good-'.$C['uid'].'"><i class="fa fa-thumbs-o-up fa-sm pull-left"></i> <span class="pull-right cop-up" id="good-num-'.$C['uid'].'">'.$C['score1'].'</span></button>									
									<button title="비공감" data-toggle="tooltip" class="btn btn-default btn-sm btn-cop live-tooltip cmt-tools" type="button" id="bad-'.$C['uid'].'"><i class="fa fa-thumbs-o-down fa-sm pull-left"></i><span class="pull-right" id="bad-num-'.$C['uid'].'">'.$C['score2'].'</span></button>
								</div>
							</div>
						</div>
						<!-- nested -->
						<div class="rb-comment-reply collapse" id="comments-'.$C['uid'].'-reply">

							<!-- 의견쓰기 -->
							<div class="media">								
								<div class="media-body">
									   <input type="hidden" name="parent" value="'.$C['uid'].'" />
										<input type="hidden" name="theme" value="'.$theme.'" />
										<input type="hidden" name="ouid" value="" />
										<fieldset>
										 	<p class="col-sm-11 nopd-left">
												<textarea placeholder="답글을 입력하세요..." name="oneline"  rows="1" onkeyup="autosize(this);" class="form-control rb-commnet-reply"></textarea>
											</p>
											<div class="col-sm-1 nopd-left pull-right">
								 				 <div><button class="btn btn-primary btn-md one-regis" id="one-regis-'.$C['uid'].'">등록</button>
											</div>	
										</fieldset>
								</div>
							</div>';
						 	echo '<div id="oneline-box-'.$C['uid'].'" class="one-list-wrap">';
						          // 한줄 의견 리스트 출력함수 호출 
						          getOnelineList($theme,$C['uid']);
						   echo  '</div>'; // 한 줄 의견 출력 박스 	 															 	
			echo '</div>
					</div>
				</div>
			</li>';
		 $i++;
		} //endforeach
	   $R=array(); 

	    // 더보기 버튼 설정시 
	   if($d['comment']['show_more'])
	   {
	   	 echo '<button class="text-center btn btn-default btn-block '.$btn_more.'"><i class="fa fa-sort-desc fa-lg"></i><span class="text-muted small"> 더 보기 ( '.$namegi_ment.' ) </span></button>';
	   }	
    
}	   
