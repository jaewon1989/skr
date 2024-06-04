<?php
//s$chatbot->vendor = $V['uid'];

$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;
$data['linkType'] ='get-legacyList';
$dataArray = $chatbot->controlLegacyData($data);
$itemName = '레거시';

;
?>
<link href="<?php echo $g['url_module_skin']?>/css/legacy.css" rel="stylesheet">
<!-- jsonEditor 리소스 -->
<link href="<?php echo $g['url_module_skin']?>/css/jsoneditor.min.css" rel="stylesheet">
<script src="<?php echo $g['url_module']?>/lib/js/jsoneditor.min.js"></script>

<div class="container-fluid table-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    -->
    <div class="overview">
        <div class="page-title">레거시 설정</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <!-- /.row -->
    <div class="table-container">
        <div class="row table-tool">
            <div class="col-lg-12 col-md-12 hidden-sm">
                <button class="btn btn-primary btn-rounded waves-effect waves-light tool-btn" data-role="open-legacySettingsModal">
                    <i class="fa fa-plus"></i> 추가하기
                </button>
                 <button class="btn btn-danger btn-rounded btn-outline waves-effect waves-light tool-btn" data-role="delete-legacy" data-type="api" data-mod="page">
                    <i class="fa fa-trash"></i> 삭제하기 
                </button>
             </div>

            <!-- /.col-lg-12 -->
        </div>
        <div class="intEntTable-wrapper"> 
            <div class="table-responsive table-wraper table-container table-aicc-skin" data-role="table-wrapper" style="margin: 0px 6px 0px 8px;">
                <table class="table table-striped table-full" id="tbl-legacyList" data-role="tbl-legacyList">
                    <thead>
                        <tr class="table-header">
                            <th class="intEnt-chk"><input type="checkbox" data-role="select-all" data-parent="#tbl-legacyList"/></th>
                            <th class="intEnt-name">레거시명</th>
                            <th class="intEnt-des">설명</th>
                            <th class="intEnt-des">기본 URI</th>
                            <th class="intEnt-des">API 수</th>
                            <th class="intEnt-ex">API 관리</th>
                        </tr>
                    </thead> 
                    <tbody>    
                        <?php foreach ($dataArray as $row):?>
                        <tr class="legacy-tr">
                            <?php $req_num = getDbRows($table[$m.'apiReq'],'api='.$row['uid'],'uid')?>
                            <td class="intEnt-chk"><input type="checkbox" name="legacy_uid[]" data-role="legacy-uid" data-uid="<?php echo $row['uid']?>" value="<?php echo $row['uid']?>"/></td>
                            <td 
                                class="txt-oflo intEnt-name"
                            data-role = "open-legacySettingsModal" 
                            data-uid="<?php echo $row['uid']?>"
                            data-name="<?php echo $row['name']?>"
                            data-description="<?php echo $row['description']?>"
                            data-url="<?php echo $row['url']?>"
                            >
                                <span 
                                class="entityName page-item"
                                data-role = "open-legacySettingsModal" 
                                data-uid="<?php echo $row['uid']?>"
                                data-name="<?php echo $row['name']?>"
                                data-description="<?php echo $row['description']?>"
                                data-url="<?php echo $row['url']?>"
                                data-tooltip="tooltip" title="레거시 상세정보"
                                >
                                    <?php echo $row['name']?>
                                </span>
                            </td>
                            <td 
                               class="txt-oflo intEnt-des"
                               data-role = "open-legacySettingsModal" 
                            data-uid="<?php echo $row['uid']?>"
                            data-name="<?php echo $row['name']?>"
                            data-description="<?php echo $row['description']?>"
                            data-url="<?php echo $row['url']?>"    

                            ><?php echo $row['description']?></td>
                            <td 
                            class="txt-oflo intEnt-des"
                            data-role = "open-legacySettingsModal" 
                            data-uid="<?php echo $row['uid']?>"
                            data-name="<?php echo $row['name']?>"
                            data-description="<?php echo $row['description']?>"
                            data-url="<?php echo $row['url']?>"

                            ><?php echo $row['url']?></td>
                            <td class="txt-oflo intEnt-ex text-center"
                            data-role = "open-legacySettingsModal" 
                            data-uid="<?php echo $row['uid']?>"
                            data-name="<?php echo $row['name']?>"
                            data-description="<?php echo $row['description']?>"
                            data-url="<?php echo $row['url']?>"
                            ><?php echo $req_num?></td>
                            <td class="txt-oflo intEnt-ex">
                                <a 
                                href="#" 
                                data-role="showHide-ApiList" 
                                data-api="<?php echo $row['uid']?>" 
                                data-vendor="<?php echo $vendor?>" 
                                data-tooltip="tooltip" 
                                data-reqnum ="<?php echo $req_num?>" 
                                title="API 리스트" 
                                >
                                    <i class="fa fa-list fa-fw" aria-hidden="true"></i>
                                </a>
                                <a 
                                href="#" 
                                data-role="open-setApiPanel" 
                                data-api="<?php echo $row['uid']?>" 
                                data-vendor="<?php echo $vendor?>" 
                                data-url="<?php echo $row['url']?>"
                                data-tooltip="tooltip" 
                                title="API 추가" 
                                >
                                    <i class="fa fa-plus-circle fa-fw" aria-hidden="true"></i>
                                </a>
                            </td>    
                        </tr>
                        <?php if($req_num):?>
                        <tr data-role="apiListWrapper-<?php echo $row['uid']?>" class="apiList-wrapper">
                            <td colspan="6" class="apiList-innerWrapper">
                                <div>
                                    <ul>
                                    <?php 
                                      $ACD = getDbSelect($table[$m.'apiReq'],'api='.$row['uid'],'*'); 
                                      while($A = db_fetch_array($ACD)){
                                        $mdLabel = $chatbot->getMethodLabel($A['method']); 
                                         echo '
                                         <li class="apiList-li">
                                             <span class="api-icon label label-'.$mdLabel.'">
                                                 '.$A['method'].'
                                             </span>
                                             <span class="api-content">
                                                 <span class="name">'.$A['name'].'</span>
                                             </span>
                                             <span class="api-settings">
                                                 <a 
                                                    href="#" 
                                                    data-role="open-setApiPanel" 
                                                    data-api="'.$row['uid'].'" 
                                                    data-vendor="'.$vendor.'" 
                                                    data-req="'.$A['uid'].'"
                                                    data-tooltip="tooltip" 
                                                    title="API 설정" 
                                                    >
                                                        <i class="fa fa-gear fa-fw" aria-hidden="true"></i>
                                                 </a>  
                                                  <a 
                                                    href="#" 
                                                    data-role="delete-legacy" 
                                                    data-type="req"
                                                    data-api="'.$row['uid'].'" 
                                                    data-vendor="'.$vendor.'" 
                                                    data-req="'.$A['uid'].'"
                                                    data-tooltip="tooltip" 
                                                    title="API 삭제" 
                                                    >
                                                        <i class="fa fa-trash fa-fw" aria-hidden="true"></i>
                                                 </a>  
                                             </span>
                                         </li>';
                                      }
                                    ?> 
                                    </ul>
                                </div>
                            </td> 
                        </tr>
                        <?php endif?>

                        <?php endforeach?>                          
                    </tbody>
                </table>
            </div>
            <div class="right-panel" data-role="rightPanel">
                <?php include($g['dir_module_skin'].'adm/_settingApiPanel.php');?>           
            </div> <!-- // right panel-->
        </div>         
    </div>
</div>
<?php include($g['dir_module_skin'].'adm/_legacyModal.php');?>

<script>
 $('body').tooltip({
    selector: '[data-tooltip=tooltip]',
    container: 'body'
}); 

</script>
