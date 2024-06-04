<?php
  if(!$my['admin']) getLink($g['s'],'','접근권한이 없습니다.','');
?>
<?php getImport('bootstrap','css/bootstrap',false,'css')?>
<link href="<?php echo $g['url_module_skin']?>/_main.css" rel="stylesheet">

<section class="rb-bbs rb-bbs-list mt-1 content-right">

  <!-- 제목,출력갯수,글쓰기버튼  -->
  <?php include $g['dir_module_skin'].'_list_head.php';?> 

  <table class="table mt-1">
    <colgroup>
      <col width="9%">
      <col width="3%">
      <col>
      <col width="10%">
      <col width="10%">
      <col width="10%">
      <col width="10%">
    </colgroup>
    <thead>
      <tr>
        <th>번호</th>
        <th colspan="2">제목</th>
        <th>등록자</th>
        <th>연락처</th>
        <th>이메일</th>        
        <th>등록일</th>
        <th>조회수</th>
      </tr>
    </thead>
    <tbody>
        <?php foreach($NCD as $R):?> 
        <?php $R['mobile']=isMobileConnect($R['agent'])?>
        <tr>
            <td scope="row" class="text-muted"><span class="label">공지</span></td>
            <td>
                <?php if($R['upload']):?>
                <a href="#"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                <?php endif?>
            </td>
            <td class="text-xs-left"> 
                <a href="<?php echo $g['bbs_view'].$R['uid']?>">
                    <?php echo getStrCut($R['subject'],$d['bbs']['sbjcut'],'')?>
                </a>
            </td>
            <td><?php echo getDateFormat($R['d_regis'],'Y-m-d')?></td>
            <td class="text-muted"><?php echo $R['hit']?></td>
        </tr>
        <?php endforeach?>

        <?php foreach($RCD as $R):?> 
        <?php 
           $adddata = explode('^^',$R['adddata']);
           $tel = $adddata[0];
           $email = $adddata[1];
        ?>
        <?php $R['mobile']=isMobileConnect($R['agent'])?>
        <tr>
            <td scope="row" class="text-muted"><?php if($R['uid'] != $uid):?>
                <?php echo $NUM-((($p-1)*$recnum)+$_rec++)?>
                <?php else:$_rec++?>
                <i class="fa fa-angle-double-right"></i>
                <?php endif?>
            </td>
            <td>
                <?php if($R['upload']):?>
                <a href="#"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                <?php endif?>
            </td>
            <td class="text-xs-left"> 
                <a href="<?php echo str_replace('list', 'view',$g['bbs_view']).$R['uid']?>">
                    <?php echo getStrCut($R['subject'],$d['bbs']['sbjcut'],'')?>
                </a>
            </td>
            <td><?php echo $R[$_HS['nametype']]?></td>
            <td><?php echo $tel?></td>
            <td><?php echo $email?></td>
            <td><?php echo getDateFormat($R['d_regis'],'Y.m.d')?></td>
            <td class="text-muted"><?php echo $R['hit']?></td>
        </tr>
        <?php endforeach?>
    
    </tbody>
  </table>
  <!-- 페이징 & 검색창 -->
  <?php include $g['dir_module_skin'].'_list_foot.php';?> 

</section>

