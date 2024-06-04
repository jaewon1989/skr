<?php
  if(!$my['admin']) getLink($g['s'],'','접근권한이 없습니다.','');
?>

<?php
// 이전글 다음글 구하기
$_bbsque ="site='".$s."' and bbs='".$B['uid']."' and category='".$cat."'";
$prev_uid=getDbCnt($table[$m.'data'],'max(uid)',$_bbsque.' and uid<'.$uid);
$next_uid=getDbCnt($table[$m.'data'],'min(uid)',$_bbsque.' and uid>'.$uid);
$prev_link=$g['bbs_view'].$prev_uid;
$next_link=$g['bbs_view'].$next_uid;
$subject = getStrCut($R['subject'],$d['bbs']['sbjcut'],'...');

// 연락처/이메일 추출 
$adddata = explode('^^',$R['adddata']);
$tel = $adddata[0];
$email = $adddata[1];

?>
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<link href="<?php echo $g['url_module_skin']?>/_main.css" rel="stylesheet">
<section class="rb-bbs rb-bbs-view mt-1 content-right">

  <div class="bbs-header clearfix">
    <h2 class="float-xs-left content-title"><i class="fa fa-circle-o text-primary" aria-hidden="true"></i> <?php echo $B['name']?></h2>
  </div>

  <div class="card mt-1">
    <div class="card-header">
      <strong><?php echo $subject?></strong>
      <div class="row">
        <div class="col-xs-6">
          <ul class="list-inline mb-0">
            <li class="list-inline-item"><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></li>
            <li class="list-inline-item">작성자 : <?php echo $R[$_HS['nametype']]?></li>
          </ul>
        </div>
        <div class="col-xs-6 text-xs-right">
           <ul class="list-inline mb-0">
             <li class="list-inline-item"><?php echo $tel?></li>
             <li class="list-inline-item"><?php echo $email?></li>
           </ul>
        </div>
      </div><!-- /.row -->
    </div><!-- /.card-header -->

    <div class="card-block">
     <?php echo getContents($R['content'],$R['html'])?>
    </div><!-- /.card-block -->
  
    <?php if($d['upload']['data']&&$d['theme']['show_upfile']&&$attach_file_num>0):?> 
    <?php
       $files_group = array();
       $img_files = array();
       $sound_files = array();
       $mov_files = array();
       $doc_files = array();
       $zip_files = array();
       foreach($d['upload']['data'] as $_u){
          if($_u['type']==2) array_push($img_files,$_u);
          else if($_u['type']==4) array_push($sound_files,$_u);
          else if($_u['type']==5) array_push($mov_files,$_u);
          else if($_u['type']==6 || $_u['type']==1) array_push($doc_files,$_u);
          else if($_u['type']==7) array_push($zip_files,$_u);
       }

       $files_group['img'] = $img_files;
       $files_group['sound'] = $sound_files;
       $files_group['mov'] = $mov_files;
       $files_group['doc'] = $doc_files;
       $files_group['zip'] = $zip_files;
 
    ?>
    <div class="card-block">
        <section class="rb-comments mt-3">
            <h1 class="h5">첨부 (<span class="text-danger"><?php echo $attach_file_num?></span>)</h1>
            <?php foreach($files_group as $type => $fgroup):?>
                <?php if(count($fgroup)):?>
                         <ul class="list-group" id="fgroup-<?php echo $type?>">
                         <?php foreach($fgroup as $_u):?>
                            <?php
                               $ext_to_fa=array('xls'=>'excel','xlsx'=>'excel','ppt'=>'powerpoint','pptx'=>'powerpoint','txt'=>'text','pdf'=>'pdf','zip'=>'archive','doc'=>'word');
                               $ext_icon=in_array($_u['ext'],array_keys($ext_to_fa))?'-'.$ext_to_fa[$_u['ext']]:'';
                             ?>
                              <?php if(!$_u['hidden']):?>
                                <li class="list-group-item">
                                    <i class="fa fa-file<?php echo $ext_icon?>-o"></i>
                                    <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;a=download&amp;uid=<?php echo $_u['uid']?>" title="<?php echo $_u['caption']?>">
                                        <?php echo $_u['name']?>
                                    </a>
                                    <small class="text-muted">(<?php echo getSizeFormat($_u['size'],1)?>)</small> 
                                    <span title="다운로드 수" data-toggle="tooltip" class="badge hidden-xs"><?php echo number_format($_u['down'])?></span>
                                    <?php if($my['admin']):?>
                                        <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $m?>&amp;a=delete_attach&amp;uid=<?php echo $_u['uid']?>" class="btn btn-danger btn-xs" onclick="return confirm('정말로 삭제하시겠습니까?');" data-toggle="tooltip" title="삭제"> <i class="fa fa-trash-o fa-lg"></i></a>
                                    <?php endif?>
                                </li>
                            <?php endif?>
                        <?php endforeach?>
                    </ul>  
                <?php endif?>
            <?php endforeach?>
        </section>
    </div>
    <?php endif?>

    <div class="card-footer">

      <div class="row">
        <div class="col-xs-4">

        <?php if($my['admin'] || $my['uid']==$R['mbruid']):?>
          <a href="<?php echo $g['bbs_modify'].$R['uid']?>" class="btn btn-default">수정</a>
          <a href="<?php echo $g['bbs_delete'].$R['uid']?>" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 삭제하시겠습니까?');" class="btn btn-default">삭제</a>
         <?php endif?>

        </div>
        <div class="col-xs-4 text-xs-center">
          <a href="<?php echo $prev_link?>" class="btn btn-default<?php echo !$prev_uid?' disabled':''?>">이전글</a>
          <a href="<?php echo $next_link?>" class="btn btn-default<?php echo !$next_uid?' disabled':''?>">다음글</a>
        </div>
        <div class="col-xs-4 text-xs-right">
          <a href="<?php echo $g['bbs_list']?>" class="btn btn-default">목록</a>
        </div>
      </div>

    </div><!-- /.carfooter -->

  </div><!-- /.card -->

</section>



