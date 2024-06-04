<style>
.rb-bbs-view .card {
  border-radius: 0;
  border-left: none;
  border-right: none;
  border-bottom: none;
  border-top: 1px solid #1a6dba
}
.rb-bbs-view .card-header {
  font-size: 17px;
  background-color: #f9f9f9
}
.rb-bbs-view .card-header .list-inline-item,
.rb-bbs-view .card-header .nav-link {
  font-size: 12px;
  color: #666
}

.rb-bbs-view .card-header .list-inline-item::after,
.rb-bbs-view .card-header .nav-link::after {
  content: '|';
  color: #d7d7d7
}
.rb-bbs-view .card-header .list-inline-item::after {
  padding-left: .7rem;
}
.rb-bbs-view .card-header .nav-link::after {
  padding-left: .5rem;
}
.rb-bbs-view .card-header .list-inline-item:last-child::after,
.rb-bbs-view .card-header .nav-link:last-child::after {
  content: ''
}
.rb-bbs-view .card-block {
  font-size: 14px;
  line-height: 1.7
}
.rb-bbs-view .card-footer {
  background-color: #fff
}

/*댓글*/
.rb-comments .list-group {
  margin-top: 1rem
}
.rb-comments .list-group-item {
  padding: 15px 10px;
  border-left: 0;
  border-right: 0;
  font-size: 13px
}
.rb-comments .list-group-item:first-child {
  border-top-right-radius: 0;
  border-top-left-radius: 0;
}
.rb-comments .list-group-item:last-child {
  border-bottom: none;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.rb-comments-write .form-inline {
  margin-top: -.5rem
}
.rb-comments-write .form-control {
  width: 602px;
  height: 89px
}
.rb-comments-write .btn {
  width: 80px;
  height: 89px;
  font-size: 18px
}
</style>
<?php
// 이전글 다음글 구하기
$_bbsque ="site='".$s."' and bbs='".$B['uid']."' and category='".$cat."'";
$prev_uid=getDbCnt($table[$m.'data'],'max(uid)',$_bbsque.' and uid<'.$uid);
$next_uid=getDbCnt($table[$m.'data'],'min(uid)',$_bbsque.' and uid>'.$uid);
$prev_link=$g['bbs_view'].$prev_uid;
$next_link=$g['bbs_view'].$next_uid;
$subject = getStrCut($R['subject'],$d['bbs']['sbjcut'],'...');

?>
<section class="rb-bbs rb-bbs-view mt-1">

  <div class="bbs-header clearfix">
    <h2 class="float-xs-left"><?php echo $B['name']?></h2>
    <a href="<?php echo $g['bbs_list']?>" class="btn btn-primary float-xs-right">목록</a>
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
          <nav class="nav mb-0">
            <a class="nav-link" href="#">이 작성자의 게시물 더보기</a>
            <a class="nav-link" href="#">인쇄</a>
            <a class="nav-link" href="#modal-report" data-toggle="modal" data-title="<?php echo $subject?>" data-user="<?php echo $R[$_HS['nametype']]?>" data-module="chanel" data-entry="<?php echo $uid?>" data-mbruid="<?php echo $R['mbruid']?>" data-bbsName="<?php echo $bbs_title?>">신고</a>
          </nav>
        </div>
      </div><!-- /.row -->
    </div><!-- /.card-header -->

    <div class="card-block">
     <?php echo getContents($R['content'],$R['html'])?>
    </div><!-- /.card-block -->
    <?php $last_attach=$d['upload']['count']-$hidden_file_num; //  _view.php 61 라인에 추가 2015. 1. 2 ?>
    <?php if($d['upload']['data']&&$d['theme']['show_upfile']&&$last_attach>0):?> 
    <div class="card-block">
        <section class="rb-comments mt-3">
            <h1 class="h5">첨부 (<span class="text-danger"><?php echo $last_attach?></span>)</h1>
            <ul class="list-group">
                <?php foreach($d['upload']['data'] as $_u):?>
                <?php if($_u['hidden'])continue?>
                <?php 
                   $ext_to_fa=array('xls'=>'excel','xlsx'=>'excel','ppt'=>'powerpoint','pptx'=>'powerpoint','txt'=>'text','pdf'=>'pdf','zip'=>'archive','doc'=>'word');
                   $ext_icon=in_array($_u['ext'],array_keys($ext_to_fa))?'-'.$ext_to_fa[$_u['ext']]:''; 
                 ?>
                   <li class="list-group-item"><i class="fa fa-file<?php echo $ext_icon?>-o"></i> 
                    <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=attach&amp;a=download&amp;uid=<?php echo $_u['uid']?>" title="<?php echo $_u['caption']?>"><?php echo $_u['name']?></a> 
                    <small class="text-muted">(<?php echo getSizeFormat($_u['size'],1)?>)</small> <span title="다운로드 수" data-toggle="tooltip" class="badge hidden-xs"><?php echo number_format($_u['down'])?></span>
                    <?php if($my['admin']):?>
                  <a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=bbs&amp;a=delete_attach&amp;uid=<?php echo $_u['uid']?>" class="btn btn-danger btn-xs" onclick="return confirm('정말로 삭제하시겠습니까?');" data-toggle="tooltip" title="삭제"> <i class="fa fa-trash-o fa-lg"></i></a>
                  <?php endif?>
                </li>
              <?php endforeach?> 

        </section>        
    </div>
    <?php endif?>

    <div class="card-footer">

      <div class="row">
        <div class="col-xs-4">

        <?php if($my['admin'] || $my['uid']==$R['mbruid']):?>
          <a href="<?php echo $g['bbs_modify'].$R['uid']?>" class="btn btn-secondary">수정</a>
          <a href="<?php echo $g['bbs_delete'].$R['uid']?>" target="_action_frame_<?php echo $m?>" onclick="return confirm('정말로 삭제하시겠습니까?');" class="btn btn-secondary">삭제</a>
         <?php endif?>

        </div>
        <div class="col-xs-4 text-xs-center">
          <a href="<?php echo $prev_link?>" class="btn btn-secondary<?php echo !$prev_uid?' disabled':''?>">이전글</a>
          <a href="<?php echo $next_link?>" class="btn btn-secondary<?php echo !$next_uid?' disabled':''?>">다음글</a>
        </div>
        <div class="col-xs-4 text-xs-right">
          <a href="<?php echo $g['bbs_list']?>" class="btn btn-secondary">목록</a>
        </div>
      </div>

    </div><!-- /.carfooter -->

  </div><!-- /.card -->

</section>
<?php getWidget('default/comment',array('theme'=>'default-bs4','parent'=>$m.'-'.$R['uid'],'feed_table'=>$table[$m.'data']));?>

<!-- 광고섹션 시작 -->
<section class="row mt-3">
  <div class="col-xs-5 offset-xs-1 text-xs-center">
    <a href="" target="_blank"><img src="/layouts/bs4-program/_images/banner-265-110-01.png" alt=""></a>
  </div>
  <div class="col-xs-5 text-xs-center">
    <a href="" target="_blank"><img src="/layouts/bs4-program/_images/banner-265-110-02.png" alt=""></a>
  </div>
</section>

<!-- autosize : http://www.jacklmoore.com/autosize/ -->
<?php getImport('autosize','autosize.min',false,'js') ?>

<script>
$(document).ready(function(){
    autosize($('textarea'));
    $("body").append('<div class="modal fade" id="modal-report"></div>'); // 신고모달 생성

});

// 신고모달 관련 스크립트
$(function(){
    // 모달 오픈시 이벤트
    $('#modal-report').on('show.bs.modal',function(e){
        var triger = e.relatedTarget;
        var title = $(triger).data('title');
        var user = $(triger).data('user');
        var bbs_name = $(triger).attr('data-bbsName');
        var my_mbruid = $(triger).data('mbruid'); // 작성자 uid
        var module = $(triger).data('module'); // chanel,bbs,blog,comment...
        var entry = $(triger).data('entry'); // 댓글 or 게시글 uid


        var modal = $(this);
        $(modal).load("<?php echo $g['dir_module_skin']?>/component/modal-report.php",function(){
            var form =$('#form-post-report');
            var radioChk = $(form).find('.custom-control-input');
            $(modal).find('[data-role="title"]').text(title);
            $(modal).find('[data-role="user"]').text(user);
            $(modal).find('[data-role="bbs_name"]').text(bbs_name);
            $(modal).find('input[name="module"]').val(module);
            $(modal).find('input[name="my_mbruid"]').val(my_mbruid);
            $(modal).find('input[name="entry"]').val(entry);

            // 신고사유 선택 이벤트(기타사유)
            $(radioChk).on('click',function(){
               var val = $(this).val();
               if(val==4) $('#report-message').show();
               else $('#report-message').hide();
            });

            // 신고내용 전송 이벤트
            $('[data-role="btn-report"]').on('click',function(){
                var m = '<?php echo $m?>';
                var postData = $(form).serializeArray();
                var formURL = rooturl+'/?m='+m+'&a=regis_report';
                $.ajax({
                    url : formURL,
                    type: "POST",
                    data : postData,
                    success:function(response, textStatus, jqXHR){
                        var result = $.parseJSON(response);
                        var error = result.error;
                        if(!error){
                           feedback.show("신고가 접수되었습니다.");
                           $('#modal-report').modal('hide');
                        }else{
                           feedback.show('관리자에게 문의해주시기 바랍니다.');
                        }
                      },
                        error: function(jqXHR, textStatus, errorThrown){
                      }
                 });
            });
        });
    });

    // 모달 닫을시 이벤트
    $('#modal-report').on('hidden.bs.modal',function(e){
        $(this).empty()
    });
});

</script>
