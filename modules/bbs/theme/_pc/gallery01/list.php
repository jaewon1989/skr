<?php getImport('bootstrap-select','bootstrap-select',false,'js')?>
<?php getImport('bootstrap-select','bootstrap-select',false,'css')?>
<link href="<?php echo $g['dir_module_skin']?>/_main.css" rel="stylesheet">
<section id="rb-forum" class="rb-forum-list">
    <div class="panel panel-default rb-panel-table">
      
        <!-- 총게시물, th, 검색창 출력부  -->
        <div class="panel-body">
            <div class="row rb-search">
                <div class="col-sm-4">
                    <span class="rb-search-result text-muted">
                        <small>총게시물 : <strong><?php echo number_format($NUM+count($NCD))?></strong> 건  (<?php echo $p?>/<?php echo $TPG?> page ) </small>
                    </span>
                </div>
                
                <!-- 검색창 출력부  -->
                <div class="col-sm-8">
                       <?php if($d['theme']['search']):?>
                       <form name="bbssearchf" action="<?php echo $g['s']?>/">
                        <input type="hidden" name="r" value="<?php echo $r?>" />
                        <input type="hidden" name="c" value="<?php echo $c?>" />
                        <input type="hidden" name="m" value="<?php echo $m?>" />
                        <input type="hidden" name="bid" value="<?php echo $bid?>" />
                        <input type="hidden" name="cat" value="<?php echo $cat?>" />
                        <input type="hidden" name="sort" value="<?php echo $sort?>" />
                        <input type="hidden" name="orderby" value="<?php echo $orderby?>" />
                        <input type="hidden" name="recnum" value="<?php echo $recnum?>" />
                        <input type="hidden" name="type" value="<?php echo $type?>" />
                        <input type="hidden" name="iframe" value="<?php echo $iframe?>" />
                        <input type="hidden" name="skin" value="<?php echo $skin?>" />
                        
                        <!-- 카테고리 출력부  -->
                        <?php if($B['category']):$_catexp = explode(',',$B['category']);$_catnum=count($_catexp)?>                   
                        <div class="col-sm-1" style="padding-left:0;">
                            <select name="category" class="boot-select" data-width="auto" data-header="" data-style="btn-default btn-sm" onchange="document.bbssearchf.cat.value=this.value;document.bbssearchf.submit();">
                                <option value=""><?php echo $_catexp[0]?></option>
                                <?php for($i = 1; $i < $_catnum; $i++):if(!$_catexp[$i])continue;?>
                                    <option value="<?php echo $_catexp[$i]?>"<?php if($_catexp[$i]==$cat):?> selected="selected"<?php endif?>>ㆍ<?php echo $_catexp[$i]?><?php if($d['theme']['show_catnum']):?>(<?php echo getDbRows($table[$m.'data'],'site='.$s.' and notice=0 and bbs='.$B['uid']." and category='".$_catexp[$i]."'")?>)<?php endif?></option>
                               <?php endfor?>
                            </select>
                        </div>
                        <?php else:?>
                        <div class="col-sm-1">
                        </div>
                        <?php endif?>
                        
                         <div class="input-group input-group-sm col-sm-7 pull-right">
                            <span class="input-group-btn">
                                <select name="where" class="boot-select" data-width="auto" data-style="btn-default btn-sm">
                                    <option value="subject|tag"<?php if($where=='subject|tag'):?> selected="selected"<?php endif?>>제목+태그</option>
                                    <option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>본문</option>
                                    <option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>이름</option>
                                    <option value="nic"<?php if($where=='nic'):?> selected="selected"<?php endif?>>닉네임</option>
                                    <option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>아이디</option>
                                    <option value="term"<?php if($where=='term'):?> selected="selected"<?php endif?>>등록일</option>
                                </select>                         
                            </span>
                            <input type="search" class="form-control" name="keyword" value="<?php echo $_keyword?>" placeholder="검색어를 입력해주세요">
                            <span  class="input-group-btn va-top">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </span>
                        </div> <!-- 검색 input-group -->

                </div> <!-- .col-sm-8 -->
             </form> 
             <?php endif?>
            </div><!-- .row rb-search -->
        </div> <!-- .panel-body -->

        <div class="panel-body">
          <div class="row rb-gallery">
              <?php foreach($RCD as $R):?> 
                 <?php $post_link=$g['s'].'/?r='.$r.'&c='.$c.'&uid='.$R['uid']?>
                 <?php $UP=getArrayString($R['upload'])?> <!-- 첫번째 이미지 uid 찾기-->
                 <?php $FU=$UP['data'][0]?>
                 <?php $f_data=getDbData($table[$m.'upload'],'uid='.$FU,'*')?>
                 <?php $f_img='/modules/bbs/upload/'.$f_data['folder'].'/'.$f_data['tmpname']?>
                  <div class="col-xs-6 col-md-3 rb-gallery-item">
                      <a href="<?php echo $post_link?>" class="thumbnail">
                         <img  src="/_core/opensrc/thumb/image.php?width=171&amp;height=&amp;cropratio=2:1.4&amp;image=<?php echo $f_img?>" alt="<?php echo $R['subject']?>">
                      </a>
                   </div>
             <?php endforeach?>
         </div>
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



