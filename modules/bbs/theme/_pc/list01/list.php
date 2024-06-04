<?php getImport('bootstrap-select','bootstrap-select',false,'js')?>
<?php getImport('bootstrap-select','bootstrap-select',false,'css')?>
<link href="<?php echo $g['url_module_skin']?>/_main.css" rel="stylesheet">


<section id="" class="rb-bbs rb-bbs-list">


    <div class="panel panel-default rb-panel-table">
      

        <div class="table-responsive">
            <table class="table table-bordered" summary="번호,제목,작성일,조회수,첨부 항목을 포함한 목록">
                <colgroup>
                    <col width="10%"></col>
                    <col></col>
                    <col width="13%"></col>
                    <col width="15%"></col>
                    <col width="10%"></col>
                </colgroup>
                <thead>
                    <tr class="active">
                        <th class="text-center">번호</th>
                        <th class="text-center">제목</th>
                        <th class="text-center">글쓴이</th>
                        <th class="text-center">작성일</th>
                        <th class="text-center">조회</th>
                    </tr>
                </thead>
                <tbody>

                 <!-- 공지사항 출력부  -->
                <?php foreach($NCD as $R):?> 
                <?php $R['mobile']=isMobileConnect($R['agent'])?>
                <tr class="active">
                    <td class="text-center">
                        <?php if($R['uid'] != $uid):?>
                           <span class="label label-info">공지</span>
                        <?php else:?>
                           <span class="now">&gt;&gt;</span>
                        <?php endif?>   
                    </td>
                    <td>
                        <?php if($R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
                         <?php if($R['category']):?><span class="text-danger">[<?php echo $R['category']?>]</span><?php endif?>
                        <a href="<?php echo $g['bbs_view'].$R['uid']?>"><?php echo getStrCut($R['subject'],$d['bbs']['sbjcut'],'')?></a>
                        <?php if(strstr($R['content'],'.jpg')):?><i class="fa fa-image fa-lg"></i><?php endif?>
                        <?php if($R['upload']):?><i class="glyphicon glyphicon-floppy-disk glyphicon-lg"></i><?php endif?>
                        <?php if($R['hidden']):?><i class="fa fa-lock fa-lg"></i><?php endif?>
                        <?php if($R['comment']):?><span class="badge"><?php echo $R['comment']?><?php echo $R['oneline']?'+'.$R['oneline']:''?></span><?php endif?>
                        <?php if($R['trackback']):?><span class="trackback">[<?php echo $R['trackback']?>]</span><?php endif?>
                         <?php if(getNew($R['d_regis'],24)):?><span class="label label-danger"><small>New</small></span><?php endif?>            
                    </td>
                    <td class="text-center"><?php echo $R[$_HS['nametype']]?></a></td>
                    <td class="text-center"><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>
                    <td class="text-center"><?php echo $R['hit']?></td>
                </tr>
               <?php endforeach?>

                <!-- 일반글 출력부 -->
                <?php foreach($RCD as $R):?> 
                <?php $R['mobile']=isMobileConnect($R['agent'])?>
                <tr>
                    <td class="text-center">
                        <?php if($R['uid'] != $uid):?>
                            <?php echo $NUM-((($p-1)*$recnum)+$_rec++)?>
                       <?php else:$_rec++?>
                           <span class="now">&gt;&gt;</span>
                        <?php endif?>   
                    </td>
                    <td>
                        <?php if($R['mobile']):?><i class="fa fa-mobile fa-lg"></i><?php endif?>
                        <?php if($R['category']):?><span class="text-danger">[<?php echo $R['category']?>]</span><?php endif?>
                        <a href="<?php echo $g['bbs_view'].$R['uid']?>"><?php echo getStrCut($R['subject'],$d['bbs']['sbjcut'],'')?></a>
                         <?php if(strstr($R['content'],'.jpg')):?><i class="fa fa-image fa-lg"></i><?php endif?>
                        <?php if($R['upload']):?><i class="glyphicon glyphicon-floppy-disk glyphicon-lg"></i><?php endif?>
                        <?php if($R['hidden']):?><i class="fa fa-lock fa-lg"></i><?php endif?>
                        <?php if($R['comment']):?><span class="badge"><?php echo $R['comment']?><?php echo $R['oneline']?'+'.$R['oneline']:''?></span><?php endif?>
                        <?php if($R['trackback']):?><span class="trackback">[<?php echo $R['trackback']?>]</span><?php endif?>
                         <?php if(getNew($R['d_regis'],24)):?><span class="label label-danger"><small>New</small></span><?php endif?>            
                    </td>
                    <td class="text-center"><?php echo $R[$_HS['nametype']]?></a></td>
                    <td class="text-center"><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>
                    <td class="text-center"><?php echo $R['hit']?></td>
                </tr>
               <?php endforeach?>
              </tbody>
            </table>
        </div>    
        <div class="panel-footer bg-transparent">
            <div class="row">
                <?php if($my['admin']):?>
                    <div class="col-sm-1 pull-left">
                        <span class="pagination">
                           <a class="btn btn-danger" href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=admin&amp;pickmodule=<?php echo $m?>&amp;front=skin&amp;theme=<?php echo $d['bbs']['skin']?>&panel=Y"><i class="fa fa-cog"></i> 관리</a></span>
                        </span>
                    </div>      
                  <?php endif?>        
                    <div class="col-sm-10 text-center">
                       <span class="pagination pagination-sm"><?php echo getPageLink($d['theme']['pagenum'],$p,$TPG,'')?></span>
                    </div>
                    <div class="col-sm-1 pull-right">
                        <span class="pull-right pagination"> 
                           <a class="btn btn-default" href="<?php echo $g['bbs_write']?>"><i class="fa fa-pencil"></i> 등록</a>
                        </span>
                    </div>
              </div>
        </div> <!-- .panel-footer --> 
    </div> <!-- .panel panel-default rb-panel-table -->
</section>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {
    // bootstrap-select 활성화 
    $('.boot-select').selectpicker();
}); 

//]]>
</script>



