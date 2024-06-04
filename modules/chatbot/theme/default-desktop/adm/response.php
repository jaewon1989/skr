<?php
$data = array();
$data['vendor'] = $V['uid'];
$data['bot'] = $bot;
$data['tempBot'] = $B['o_uid'];
$data['id'] = $B['id'];
$data['act'] = 'get';
$getVendorResponse = $chatbot->controlVendorResponse($data);
$botContract = $chatbot->getBotContractInfo($data);
?>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-4" id="bg-outWrapper">
            <div class="white-box" id="resBg-wrapper">
                <h3 class="res-title">
                    쉬운 인공지능, <br/>
                    <strong class="res-strong">간단한 답변</strong> 설정으로 <strong>끝!</strong>
                </h3>
                <div id="bg-response">
                    <img src="<?php echo $g['img_layout']?>/bg_response.png?>" />
                </div>

            </div>   
        </div>
        <div class="col-md-8">
            <div class="white-box" id="resList-wrapper" style="min-height:870px;">
                <?php if(!$botContract['template_uid']):?>
                <div id="noTempRes-wrapper">
                    <h3 class="res-title">
                        응답설정 기능이란 템플릿을 구매한 회원들에게 제공되는 기능으로 템플릿에 설정된 질문을 답변만 입력하여 간단하게 챗봇을 만드는 기능입니다. 
                        <br/><br/>'처음부터 제작하기‘ 의 경우에는 응답설정 기능이 제공되지 않습니다.
                    </h3>
                </div>
                <?php else:?>
                <form class="form-horizontal form-material" autocomplete="off" data-role="configVRForm">
                    <input type="hidden" name="uid" value="<?php echo $bot?>" />
                    <?php echo $getVendorResponse?>
                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary btn-block" data-role="save-vendorResponse">저장</button>
                        </div>
                    </div>
                </form>
                <?php endif?>
            </div>
        </div>
    </div><!-- row -->   
</div>
