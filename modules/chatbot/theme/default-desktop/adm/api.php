<?php
$_data = array();
$_data['bot'] = $bot;
$R = $chatbot->getAdmBot($_data);

?>

<input type="hidden" name="mod" value="month" />

<div class="container-fluid">
    <!--
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
<!-         <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> <a href="http://wrappixel.com/templates/pixeladmin/" target="_blank" class="btn btn-danger pull-right m-l-20 btn-rounded btn-outline hidden-xs hidden-sm waves-effect waves-light">Upgrade to Pro</a>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Fontawesome Icons</li>
            </ol>
        </div> -->
        <!-- /.col-lg-12 ->
    </div>
    -->
    <div class="overview">
        <div class="page-title">API 설정</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle?></div>
        </div>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="white-box">
                <form class="form-horizontal form-material" autocomplete="off" data-role="settingsApiForm">
                    <input type="hidden" name="uid" value="<?php echo $bot?>" />
                     <div class="form-group">
                        <label class="col-md-1 input">Console ID</label>
                        <div class="col-md-8">
                            <input type="hidden" name="console_id"  class="form-control form-control-line" disabled value="<?php echo $R['console_id']?>">
                            <div class="form-control input-div"><?php echo $R['console_id']?></div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">Client ID</label>
                        <div class="col-md-8">
                            <input type="hidden" name="client_id"  class="form-control form-control-line" disabled value="<?php echo $R['client_id']?>">
                            <div class="form-control input-div"><?php echo $R['client_id']?></div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">Client secret</label>
                        <div class="col-md-8">
                            <input type="hidden" name="client_secret"  class="form-control form-control-line" disabled value="<?php echo $R['client_secret']?>">
                            <div class="form-control input-div" data-role="client_secret-wrapper"><?php echo $R['client_secret']?></div> 
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-info btn-dark btn-middle" data-role="issue-apiKey" data-name="client_secret">재발급</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">Access Token</label>
                        <div class="col-md-8">
                            <input type="hidden" name="access_token" class="form-control form-control-line" value="<?php echo $R['access_token']?>">
                            <div class="form-control textarea-div"  data-role="access_token-wrapper"><?php echo $R['access_token']?></div>
                        </div>
                        <div class="col-md-3" style="padding-top:20px;">
                            <button class="btn btn-info btn-dark btn-middle" data-role="issue-apiKey" data-name="access_token">재발급</button>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary btn-block" data-role="save-channelSettings" data-sns="botks">저장</button>
                            <!-- bottalks 채널로 간주하고 채널저장 프로세스를 사용한다. -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- row -->   
</div>
