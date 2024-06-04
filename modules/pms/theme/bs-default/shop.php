<?php
include_once $g['path_module'].$m.'/_main.php';
$ISCAT = getDbRows($table[$m.'category'],'');
if($cat)
{
    $CINFO = getUidData($table[$m.'category'],$cat);
    $ctarr = getShopCategoryCodeToPath($table[$m.'category'],$cat,0);
    $ctnum = count($ctarr);
    $CINFO['code'] = '';

    for ($i = 0; $i < $ctnum; $i++)
    {
        $CXA[] = $ctarr[$i]['uid'];
        $CINFO['code'] .= $ctarr[$i]['id'].($i < $ctnum-1 ? '/' : '');
        $_code .= $ctarr[$i]['uid'].($i < $ctnum-1 ? '/' : '');
    }
    $code = $code ? $code : $_code;

    for ($i = 0; $i < $ctnum; $i++) $CXA[] = $ctarr[$i]['uid'];
}
$catcode = '';
$is_fcategory =  $CINFO['uid'] && $vtype != 'sub';
$is_regismode = !$CINFO['uid'] || $vtype == 'sub';
if ($is_regismode)
{
    $CINFO['name']     = '';
    $CINFO['hidden']   = '';
    $CINFO['imghead']  = '';
    $CINFO['imgfoot']  = '';
}
if($CINFO['isson']) $_WHERE2 = "display=0 and category like '%/".$cat."/%'";
else $_WHERE2 = "display=0 and category like '%/".$cat."%'";

$CCD = array();
$_CCD = getDbArray($table[$m.'product'],$_WHERE2,'*',$sort,$orderby,$recnum,$p);
while($_C=db_fetch_array($_CCD)) $CCD[] = $_C;
?>

<style>
.checkbox, .checkbox-inline, .radio-inline {margin: 0px !important ;}
.radio-inline input {position:relative !important;left:0 !important;}
</style>
<div id="catebody" class="row">
    <div id="category" class="col-md-2">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default" style="position:fixed;border-bottom:none;">
                <div class="panel-heading rb-icon">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapmetane">카테고리 분류</a>
                    </h4>
                </div>
                <div class="panel-collapse collapse in" id="collapmetane">
                    <div class="panel-body">
                        <div style="min-height:300px;">
                            <?php if($ISCAT):?>
                                <link href="<?php echo $g['s']?>/_core/css/tree.css" rel="stylesheet">
                                <?php $_treeOptions=array('table'=>$table[$m.'category'],'dispNum'=>false,'dispHidden'=>false,'dispCheckbox'=>false,'allOpen'=>false)?>
                                <?php $_treeOptions['link'] = $g['s'].'/?m='.$m.'&amp;cat='?>
                                <?php echo getTreeCategory($_treeOptions,$code,0,0,'')?>
                            <?php else:?>
                                  <div class="rb-blank"> 등록된 카테고리가 없습니다. </div>
                            <?php endif?>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="catinfo" class="col-md-10">
        <div class="rb-viewtype-gallery" style="padding-top:50px;">
            <div class="row">
               <?php $i=1;foreach ($CCD as $R):?>
               <?php 
                 if(strstr($R['category'],'1/')){ // 데스크탑 
                    $img_width='100%'; 
                    $pb_mod='desktop';
                 }else{
                    $img_width='50%'; 
                    $pb_mod ='mobile';
                 }               
                 $pb_url ='/publish/'. $pb_mod; 
                 $pb_file_arr=explode(',',$R['review']); // 파일 path 정보 배열 
               ?>
                <div class="col-md-12">
                    <ul class="nav nav-tabs" role="tablist">
                       <li class="active">
                           <a href="#guide-settings-<?php echo $i?>" role="tab" data-toggle="tab">가이드</a>
                       </li>
                        <li>
                           <a href="#publish-settings-<?php echo $i?>" role="tab" data-toggle="tab" >퍼블리싱</a>
                       </li>            
                    </ul>
                    <div class="tab-content" style="margin:50px 0">
                          <div class="tab-pane active" id="guide-settings-<?php echo $i?>">
                               <div class="rb-img-wrapper" style="width:90%;height:700px;overflow:auto;">
                                    <img class="img-responsive" src="<?php echo getPic($R,'m')?>" alt="<?php echo $U['name']?>" style="width: 100%;">
                                </div>
                           </div>
                           <div class="tab-pane" id="publish-settings-<?php echo $i?>">
                                 <?php foreach ($pb_file_arr as $pb_file):?>
                                 <div style="height:100%;">
                                    <?php if($pb_file):?>
                                    <p><?php echo '▼ '.$pb_file?></p>
                                    <iframe src="<?php echo $pb_url.'/'.$pb_file?>" width="90%" height="700px"></iframe>  
                                    <?php else:?>
                                       <div style="padding-top:0px;">파일이 없습니다.</div>
                                    <?php endif?>

                                 </div>                                
                                 <?php endforeach?>
                         
                            </div>       
                     </div>                    
                </div>
                <?php $i++;endforeach?>
            </div>               
        </div>
    </div>  
</div>

<!-- 태그입력 -->

<script> 
 
function saveCheck(f)
{
    if (f.name.value == '')
    {
        alert('카테고리 명칭을 입력해 주세요.      ');
        f.name.focus();
        return false;
    }
    if (f.id)
    {
        if (f.id.value == '')
        {
            alert('카테고리 코드를 입력해 주세요.      ');
            f.id.focus();
            return false;
        }
        if (!chkFnameValue(f.id.value))
        {
            alert('카테고리 코드는 영문대소문자/숫자/_/- 만 사용할 수 있습니다.      ');
            f.id.focus();
            return false;
        }
    }
    if(confirm('정말로 실행하시겠습니까?         '))
     {
            getIframeForAction(f);
            f.submit();
    }
}

</script>
