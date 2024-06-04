<?php
// 모든 액션은 layouts/vendor-desktop/_js/layout.js 참조
$sns = array(
    "kakao"=>"카카오톡",
    "fb"=>"페이스북메신저",
    "line"=>"라인",
    "ntok"=>"네이버 톡톡"
 );

?>

<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>-->
    <div class="overview">
        <div class="page-title">채널설정</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <div class="row">
       <?php foreach($sns as $ename => $name):?>
        <div class="col-xs-6 col-sm-6 col-md-3 chanel-wrapper" >
            <div class="thumbnail">
                <div class="chanel-box">
                    <img src="<?php echo $g['img_layout'].'/'.$ename?>.png" class="chanelBox-img">
                    <h5 class="text-center chanel-title"><?php echo $name?></h5>
                </div>
                <div class="soc-content">
                    <div class="col-12">
                        <a href="#" data-role="open-settingChannelModal" data-sns="<?php echo $ename?>">
                            <h5 class="font-medium">메신져봇 생성</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
       <?php endforeach?>
    </div>   
    
</div>
<!-- Modal -->
<div id="modal-settingChannel" class="modal fade" data-role="settingChannelModal">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body" data-role="settingChannelContent">
                <!-- ajax 로 가져오기 : layouts/vendor-desktop/_js/layout.js (openSettingChannelModal) 참조 -->
            </div>
            <div class="modal-footer">
                 <!-- data-sns 값은 상기 openSettingChannelModal 에서 세팅  -->
                <button type="button" class="btn btn-primary" data-role="save-channelSettings" data-sns="">저장</button> 
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
