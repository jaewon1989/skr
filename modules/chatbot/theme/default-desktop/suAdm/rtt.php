<link href="<?php echo $g['url_module_skin']?>/css/voice.recog.css" rel="stylesheet">


<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="white-box">
                <h3 class="no-browser-support">죄송합니다. 귀하의 브라우저가 음성인식 지원하지 않습니다.</h3>
                <div class="form-horizontal form-material">
                    <div class="form-group btn-block btn-outline btn-info" style="padding: 10px 12px;">
                        <input type="file" accept="audio/*" capture="microphone" id="recorder">
                        <audio id="player" controls style="display:none;"></audio>
                        <span>파일을 선택한 후 [텍스트 변환] 버튼을 클릭해주세요.</span>
                    </div>
                    
                    
                    <div class="form-group">
                        <div class="col-md-12">
                            <textarea rows="10" id="note-textarea" class="form-control form-control-line" placeholder="녹음내용이 출력됩니다."></textarea>
                        </div>
                    </div>
                     <div class="form-group">
                        <div class="col-md-2">
                            <button class="btn btn-block btn-outline btn-rounded btn-info" id="start-rtt">
                                <i class="fa fa-play" aria-hidden="true"></i> 텍스트 변환 시작
                            </button>
                        </div>
                    </div>
                    <p id="recording-instructions"></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/voice.recog.js'?>"></script>
