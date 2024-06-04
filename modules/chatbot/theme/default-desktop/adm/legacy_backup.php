<?php
$_data = array();
$_data['bot'] = $bot;
$_data['mod'] ='form';
$getFormBot = $chatbot->getAdmBot($_data);
?>

<!-- bootstrap css -->
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>

<input type="hidden" name="mod" value="month" />

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
<!--         <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> <a href="http://wrappixel.com/templates/pixeladmin/" target="_blank" class="btn btn-danger pull-right m-l-20 btn-rounded btn-outline hidden-xs hidden-sm waves-effect waves-light">Upgrade to Pro</a>
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Fontawesome Icons</li>
            </ol>
        </div> -->
        <!-- /.col-lg-12 -->
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="white-box">
                <form class="form-horizontal form-material" data-role="configBotForm">
                    <input type="hidden" name="uid" value="<?php echo $bot?>" />
                    <div class="form-group">
                        <label class="col-md-1 input">아이디</label>
                        <div class="col-md-11">
                            <input type="text" name="name"  class="form-control form-control-line"> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">시크릿코드</label>
                        <div class="col-md-11">
                            <input type="text" name="name"  class="form-control form-control-line"> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">도메인</label>
                        <div class="col-md-11">
                            <input type="text" name="name"  placeholder="bottalks.co.kr" class="form-control form-control-line"> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">버전</label>
                        <div class="col-md-11">
                            <select style="padding: 0 7px;">
                                <option>버전을 선택해주세요</option>
                                <option>v1</option>
                                <option>v2</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1">사용용도</label>
                        <div class="col-md-11">
                            <textarea rows="5" name="intro" class="form-control form-control-line"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 input">웹사이트</label>
                        <div class="col-md-11">
                                <input type="text" placeholder="http://www.bottalks.co.kr" class="form-control form-control-line" name="website" value="">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary btn-block" data-role="btn-updateBot">저장</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- row -->   
</div>
