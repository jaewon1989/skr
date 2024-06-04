<?
    // CSRF 토큰 생성 함수
    function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            if (function_exists('random_bytes')) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } elseif (function_exists('mcrypt_create_iv')) {
                $_SESSION['csrf_token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            } else {
                $_SESSION['csrf_token'] = bin2hex(uniqid());
            }
        }
        return $_SESSION['csrf_token'];
    }

    // CSRF 토큰 생성 및 세션 저장
    $csrfToken = generateCSRFToken();

    $chatbot->vendor = $V['uid'];
    $botuid = $bot?$bot:'';
    //$isDev = ($_SERVER["REMOTE_ADDR"] == '192.168.50.101')? true : false;
    $isDev = true;

    $dToday = date("Y-m-d", time());
    $aDate = explode("-", $dToday);
    $dYear = $aDate[0];
    $dMonth = $aDate[1];
    $dDay = $aDate[2];
    $aLogDate = array();
    $aLogDate[] = array("어제", date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-1,$dYear)), date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-1,$dYear)));
    $aLogDate[] = array("오늘", $dToday, $dToday);
    $aLogDate[] = array("일주", date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-7,$dYear)), $dToday);
    $aLogDate[] = array("한달", date("Y-m-d",mktime(0,0,0,$dMonth-1,$dDay,$dYear)), $dToday);
    $aLogDate[] = array("당월", date('Y-m')."-01", $dToday);
    $aLogDate[] = array("전체", "", "");
    $d_start = date("Y-m-d",mktime(0,0,0,$dMonth,$dDay-30,$dYear));
    $d_end = $dToday;

?>
<link rel="stylesheet" href="<?php echo $g['url_layout']?>/_css/jqcloud.css">
<?php getImport('bootstrap-datepicker','css/datepicker3',false,'css')?>
<?php getImport('bootstrap-datepicker','js/bootstrap-datepicker',false,'js')?>
<?php getImport('bootstrap-datepicker','js/locales/bootstrap-datepicker.kr',false,'js')?>
<script src="<?php echo $g['url_layout']?>/_js/FileSaver.js"></script> <!-- docx  -->
<script src="<?php echo $g['url_layout']?>/_js/docx.js"></script> <!-- docx  -->
<script src="<?php echo $g['url_layout']?>/_js/jszip.min.js"></script> <!-- pptxgen -->
<script src="<?php echo $g['url_layout']?>/_js/pptxgen.min.js"></script> <!-- pptxgen -->
<script src="<?php echo $g['url_layout']?>/_js/jspdf.umd.min.js"></script> <!-- jspdf  -->
<script src="<?php echo $g['url_layout']?>/_js/jspdf.plugin.autotable.js"></script> <!-- jspdf  -->
<script src="<?php echo $g['url_layout']?>/_js/font-NanumGothic-Regular.js"></script> <!-- jspdf font -->
<script src="<?php echo $g['url_layout']?>/_js/html2canvas.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jquery.waypoints.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/sunburst-chart.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/d3.v3.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/sankey.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/d3.chart.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/d3.chart.sankey.min.js"></script>
<script src="<?php echo $g['url_layout']?>/_js/jqcloud.min.js"></script>
<!--script src="<?php echo $g['url_layout']?>/_js/jqwcloud.js"></script-->

<style>
    /*.stat_to_gpt > div { display: flex; justify-content: flex-start; align-items: center; }*/
    .stat_to_gpt h6 { min-width: 100px; }
    .stat_to_gpt > div.white-box:nth-child(1) > div:nth-child(1) > div { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
    .stat_to_gpt > div.white-box:nth-child(2) > div { margin-bottom: 25px; display: flex; justify-content: flex-start; align-items: center; }
    .stat_to_gpt > div.white-box div.input_box { display: flex; justify-content: flex-start; align-items: center; }
    .stat_to_gpt button.btn_prompt { font-weight: Bold; }
    .stat_to_gpt .form-group { margin: 0px; }
    .stat_to_gpt .form-group > div:first-child { width: 40%; display: inline-block; }
    .stat_to_gpt .form-group > div:last-child { width: 20%; display: inline-block; }
    .stat_to_gpt .form-check { display: flex; justify-content: flex-start; align-items: flex-start; margin-right: 15px; }
    .stat_to_gpt .form-check:last-child { margin-right: 0px; }
    .stat_to_gpt .form-check .radio-inline,
    .stat_to_gpt .form-check .checkbox-inline { position: relative; display: inline-block; padding-left: 5px; margin-bottom: 0; font-weight: normal; vertical-align: middle; cursor: pointer; }
    #screenshotTarget { height: 100%; overflow-x: hidden; background-color: #edf1f5; }
    @media print {
        #screenshotTarget .temp_html { page-break-after: always; }
        #screenshotTarget .temp_html:last-child { page-break-after: avoid; }
    }

    .stat_to_gpt .prompt_history { background-color: #F4F6FA; width: 100%; overflow: auto; max-height: 450px; letter-spacing: -1px; margin: 20px 0px; }
    .stat_to_gpt .prompt_history .prompt_data .prompt_send { position: relative; width: calc(100% - 80px); padding: 15px; background-color: #fff; border-radius: 15px; margin: 20px 0px 20px 60px; line-height: 26px; }
    .stat_to_gpt .prompt_history .prompt_data .prompt_send:before { content: ' '; width: 30px; height: 30px; position: absolute; left: -40px; top: 12px; background-image: url("/_core/skin/images/ico_user.png"); background-size: 100% 100%; background-repeat: no-repeat; }
    .stat_to_gpt .prompt_history .prompt_data .prompt_receive { position: relative; width: calc(100% - 80px); padding: 15px; background-color: #fff; border-radius: 15px; margin: 20px 0px 20px 60px; line-height: 26px; min-height: 60px; }
    .stat_to_gpt .prompt_history .prompt_data .prompt_receive:before { content: ' '; background-color: transparent; width: 30px; height: 30px; position: absolute; left: -40px; top: 7px; border-radius: 100%; background-image: url('/_core/skin/images/sym.svg'); background-size: 100% 100%; background-repeat: no-repeat; }
    .stat_to_gpt .prompt_history .prompt_data .prompt_receive > a { text-decoration: underline; }
    .stat_to_gpt .prompt_history .prompt_data .prompt_receive .text-primary { color: #337ab7; font-weight: Bold; }
    .stat_to_gpt .prompt_history .prompt_data .btn_box { position: absolute; right: 15px; top: 0px; height: 100%; display: flex; justify-content: flex-start; align-items: center; gap: 10px; }
    .stat_to_gpt .temp_html .stat_title { font-size: 24px; font-weight: bold; color: #337ab7; }
    .stat_to_gpt .stat_3 .table-fluid .table-wrapper { position: relative; }
    .stat_to_gpt .stat_3 .table-fluid .intEntTable-wrapper .table-wrapper { height: 100%; overflow-y: none; }

    .stat_to_gpt .stat_4 .btn-info {padding:6px 10px; font-size:13px;}
    .stat_to_gpt .stat_4 .pd15 {padding:15px !important;}
    .stat_to_gpt .stat_4 .bottom0 {padding-bottom:0 !important; margin-bottom:0 !important;}
    .stat_to_gpt .stat_4 .col-in {padding: 0px;}
    .stat_to_gpt .stat_4 .col-in h3 {font-size: 30px; text-align:center;}
    .stat_to_gpt .stat_4 .text-muted {color: #8d9ea7; text-align:center;}
    .stat_to_gpt .stat_4 .aleft {text-align:left !important;}
    .stat_to_gpt .stat_4 .ul_info {display:block; text-align:center; padding-bottom:10px !important; border-bottom:1px solid #e4e7ea;}
    .stat_to_gpt .stat_4 .ul_info li {display:inline-block; text-align:center; width:19%;}
    .stat_to_gpt .stat_4 .ul_info li span {display:block; text-align:center;}
    .stat_to_gpt .stat_4 .ul_info li span.info_num {font-size:30px; font-weight:bold; color:#009efb;}
    .stat_to_gpt .stat_4 .node_box_wrap {padding-bottom:25px !important;}
    .stat_to_gpt .stat_4 .node_box {clear:both; position:relative; padding:10px; border:1px solid #e4e7ea;}
    .stat_to_gpt .stat_4 .node_box_guide {display:block; text-align:center; color:#bbb; line-height:30px;}
    .stat_to_gpt .stat_4 .nodeInfoWrap {min-height:400px;}
    .stat_to_gpt .stat_4 .conv_btn_wrap {margin-top:15px; text-align:center;}
    .stat_to_gpt .stat_4 #stateWrap {position:relative; overflow:hidden; overflow-y:auto;}
    .stat_to_gpt .stat_4 .ul_node_box {display:none;}
    .stat_to_gpt .stat_4 .ul_node_box li {float:left; position:relative; margin-right:25px;}
    .stat_to_gpt .stat_4 .ul_node_box li:last-child {margin-right:0;}
    .stat_to_gpt .stat_4 .ul_node_box li span {display:block; line-height:30px; background:#68839a; color:#fff; border-radius:5px; padding:0 10px; cursor:pointer;}
    .stat_to_gpt .stat_4 .ul_node_box li span:after {position:absolute; right:-20px; color:#333; font-size:12px; content:'▶'}
    .stat_to_gpt .stat_4 .ul_node_box li:last-child span:after {content:'';}
    .stat_to_gpt .stat_4 .ul_node_box li span.off {background:#dcdcdc; color:#999;}
    .stat_to_gpt .stat_4 .ul_node_box:after {clear:both; display:block; content:'';}

    .stat_to_gpt .stat_5 .nodeInfoWrap {height:500px;}
    .stat_to_gpt .stat_5 .node rect {fill-opacity:.9; shape-rendering:crispEdges; stroke-width:0; cursor:pointer;}
    .stat_to_gpt .stat_5 .node text {text-shadow:0 1px 0 rgba(255,255,255,0.5);}
    .stat_to_gpt .stat_5 .link {fill:none; stroke:#000; stroke-opacity:.1;}
    .stat_to_gpt .stat_5 .link:hover {stroke-opacity:.5 !important;}

    #modal-filelist .filelist h6 { font-size: 18px; line-height: 30px; border-bottom: 1px solid #ccc; }
    #modal-filelist .filelist table { width: 100%; }
    #modal-filelist .filelist table tr th { padding: 5px; }
    #modal-filelist .filelist table tr th:nth-child(1) { width: 20%; }
    #modal-filelist .filelist table tr th:nth-child(2) { width: 60%; }
    #modal-filelist .filelist table tr th:nth-child(3) { width: 20%; }
    #modal-filelist .filelist table tr td { padding: 5px 2px; }
    #modal-filelist .filelist table tr td:nth-child(3) { text-align: center; }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">통계/분석 > ChatGPT 통계</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="stat_to_gpt">
                <div class="white-box">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" name="prompt" class="form-control" placeholder="PPT로 9장으로 만들어주고 23년 3월부터 4월까지로 뽑아줘" autocomplete='off' >
                            <button type="button" class="btn btn-primary btn_prompt">생성하기</button>
                            <button type="button" class="btn btn-info btn_file_history">내역보기</button>
                        </div>
                    </div>
                    <div class="row prompt_history">
                    </div>
                </div>
                <div class="white-box">
                    <div class="row">
                        <h6>기간</h6>
                        <div class="form-group">
                            <div>
                                <div id="datepicker" class="input-daterange input-group bot_log">
                                    <input type="text" class="form-control" id="d_start" name="d_start" placeholder="시작일 선택" autocomplete="off" value="<?=$d_start ?>">
                                    <span class="input-group-addon">~</span>
                                    <input type="text" class="form-control" id="d_end" name="d_end" placeholder="종료일 선택" autocomplete="off" value="<?=$d_end ?>">
                                </div>
                            </div>
                            <div>
                                <span class="input-group-btn log_btn">
                                    <?foreach($aLogDate as $aDate) {?>
                                    <button type="button" class="btn btn-default <?=($aDate[0]=='한달' ? 'btn-primary' : '')?>" sDate="<?=$aDate[1]?>" eDate="<?=$aDate[2]?>"><?=$aDate[0]?></button>
                                    <?}?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <h6>문서종류</h6>
                        <div class="input_box">
                            <div class="form-check">
                                <input type="radio" id="filetype_ppt" name="filetype" value="ppt" checked >
                                <label for="filetype_ppt" class="radio-inline">파워포인트</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="filetype_pdf" name="filetype" value="pdf">
                                <label for="filetype_pdf" class="radio-inline">PDF</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="filetype_doc" name="filetype" value="doc">
                                <label for="filetype_doc" class="radio-inline">워드(DOC)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <h6>페이지 수</h6>
                        <div class="input_box col-md-2">
                            <input type="number" name="maxpage" class="form-control" placeholder="1" value="9">
                        </div>
                    </div>
                    <div class="row">
                        <h6>통계선택</h6>
                        <div class="input_box aSidemenu">
                            <!-- aSidemenu -->
                        </div>
                    </div>
                    <div class="row">
                        <h6>파일명</h6>
                        <div class="input_box col-md-4">
                            <input type="text" name="filename" class="form-control"  placeholder="파일명을 입력하세요">
                        </div>
                    </div>
                </div>
                <div id="screenshotTarget"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-filelist" class="modal fade">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <!-- to do -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const pdf = new jspdf.jsPDF({orientation: 'p', unit: 'px', format: 'a4'});
    const pptx = new PptxGenJS();
    const docx = window.docx; // docx 모듈 가져오기
    const { Document, Packer, Paragraph, Text, Media, MediaData, ImageRun } = docx;
    const w_this = $('.stat_to_gpt');
    const w_aSideMenu = $.parseJSON('<?=json_encode($aSideMenu) ?>');
    const w_maxFileSize = 2 * 1024 * 1024; // 2MB
    const w_dev = '<?=$isDev ?>';
    const csrfToken = '<?=$csrfToken ?>'; // CSRF 토큰 가져오기
    // 그래프 팔래트
    //const color = d3.scaleOrdinal(d3.schemeCategory10); //schemePaired, schemeCategory10, schemeTableau10, schemeAccent, schemeDark2, schemeSet1, schemeSet2, schemeSet3
    const color = d3.scale.category10(); //schemePaired, schemeCategory10, schemeTableau10, schemeAccent, schemeDark2, schemeSet1, schemeSet2, schemeSet3
    let w_filename = '통계 분석 보고서';
    let w_maxpage = 9;
    let w_loading = false;
    let w_blockHeight = 1240;
    let w_process_idx = 0;
    let w_prompt = '';
    let w_prompt_morph = [];
    let _log = function(data){
        if(!w_dev){ return false; }
        console.log(data);
    }

    // Ajax 호출에 CSRF 토큰 추가
    $.ajaxSetup({
      beforeSend: function(xhr, settings) {
        if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
          xhr.setRequestHeader('X-CSRFToken', csrfToken);
        }
      }
    });

    $(function(){
        init();
        _log('dev_chk');
    });

    w_this.find('.input-daterange').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "kr",
        todayHighlight: true,
        autoclose: true
    }).on('changeDate', function(e) {
        $('.btn_search').addClass("btn-primary");
        $('.log_btn .btn').removeClass("btn-primary");

        import_prompt('input[name="prompt"]');
    });

    w_this.on('click', 'input[type="radio"]', function(){
        import_prompt('input[name="prompt"]');
    });

    w_this.on('keyup click', 'input[name="maxpage"]', function(){
        w_maxpage = ($(this).val())? $(this).val() : 9;
        import_prompt('input[name="prompt"]');
    });

    w_this.on('click', 'input[type="checkbox"]', function(){
        let _this = $(this);
        let _color = (_this.is(':checked'))? '#009efb' : 'unset';
        _this.find('+label').css('color', _color);

        import_prompt('input[name="prompt"]');
    });

    w_this.on('keyup', 'input[name="filename"]', function(){

        import_prompt('input[name="prompt"]');
    });

    w_this.on('click', '.log_btn button', function(){
        let _this = $(this);
        w_this.find('input[name="d_start"]').val(_this.attr('sdate'));
        w_this.find('input[name="d_end"]').val(_this.attr('edate'));
        w_this.find('.log_btn button').removeClass('btn-primary');
        _this.addClass('btn-primary');

        import_prompt('input[name="prompt"]');
    });

    w_this.on('click', '.btn_prompt', function(){
        _log('prompt', w_this.find('input[name="prompt"]').val());
        let _this = $(this);
        if(w_this.find('input[name="prompt"]').val() == ''){ return false; }
        toggle_print_layout(w_loading = true);

        prompt_morph(w_this.find('input[name="prompt"]').val());
    });


    w_this.on('click', 'button.btn_file_agree', function(){
        let _html = prompt_msg('');
        w_this.find('.prompt_history .prompt_data[data-idx="'+w_process_idx+'"] .prompt_receive:last-child').html(_html);
    });

    w_this.on('click', 'button.btn_file_disagree', function(){
        let _html = prompt_msg('cancel_print');
        w_this.find('.prompt_history .prompt_data[data-idx="'+w_process_idx+'"] .prompt_receive:last-child').html(_html);
    });

    w_this.on('keyup', 'input[name="prompt"]', function(e){
        e.preventDefault();
        let _key = e.key || e.keyCode;
        let _this = $(this);

        if(_this.val() == ''){ return false; }

        if(_key === 'Enter' || _key === 13){
            _log('submit');
            toggle_print_layout(w_loading = true);
            prompt_morph(_this.val());
        }
    });

    w_this.on('click', '.btn_file_history', function(){
        // 내용 추가
        get_download_filelist().then((data) => {
            //_log('1@!@');
            let _html = `
                <div class="filelist">
                    <div class="filelist_wrap">
                        <h6>파일 다운로드 이력</h6>
                        <table>
                            <thead>
                                <tr>
                                    <th>일시</th>
                                    <th>파일명</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- to do -->
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            $('#modal-filelist .modal-body').html('').append(_html);
            $.each(data.filelist, function(i, v){
                //_log(v);
                _html = '';
                _html += '<tr>';
                _html += '    <td>'+formatDateString(v.d_regis, 'yyyy-mm-dd')+'</td>';
                _html += '    <td>'+v.name+'</td>';
                _html += '    <td><button type="button" class="btn btn_filedownload" data-filename="'+v.name+'" data-link="'+v.url+v.folder+'/'+v.tmpname+'">다운로드</button></td>';
                _html += '</tr>';
                $('#modal-filelist .filelist table > tbody').append(_html);
            });
            
            $('#modal-filelist').modal();
        });
    });

    w_this.on('click', '.btn_file_download', function(){
        $(this).prop('disabled', true);
        $('.preloader').show();
        file_download();
    });

    $('#modal-filelist').on('click', 'button.btn_filedownload', function(){
        let _this = $(this);
        let _url = window.location.protocol+'//'+window.location.hostname+_this.data('link');
        let _filename = _this.data('filename');
        if(_url == '' || _filename == ''){ return false; }
        
        _log(_url, _filename);
        download(_url, _filename);
    });

    function toggle_print_layout(){
        _log('toggle_print_layout : '+w_loading);
        if(w_loading){
            _log('~ loading ~ ');
            w_this.find('.btn_file_download').prop('disabled', true);
            $('#screenshotTarget').show();
            $('.preloader').css('background', 'transparent').show();
        }else{
            _log('~ loading End ~ ');
            w_this.find('.btn_file_download').prop('disabled', false);
            $('#screenshotTarget').hide();
            $('.preloader').css('background', '#fff').hide();
        }

        // $('#screenshotTarget').show(); // test
    }

    function download(url, filename) {
        // a 태그를 동적으로 생성합니다.
        var a = document.createElement('a');
        a.href = url;

        // 다운로드 속성을 설정합니다.
        a.download = filename;

        // a 태그를 클릭하여 자동으로 다운로드합니다.
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function file_setting(){
        let _flag = 0;
        let _dataList = [];

        w_filename = (w_this.find('input[name="filename"]').val() != '')? w_this.find('input[name="filename"]').val() : w_filename;
        w_maxpage = (w_this.find('input[name="maxpage"]').val() != '')? Number(w_this.find('input[name="maxpage"]').val()) : w_maxpage;
        w_this.find('#screenshotTarget div.temp_html').remove(); // init

        $.each(w_this.find('input[name="stat[]"]'), function(i, e){
            let _this = $(this);
            if(_this.is(':checked')){
                $.each(w_aSideMenu, function(i, e){
                    if(e.id == _this.val() && typeof window['Load_BotChart'+i] == 'function'){
                        _flag++;
                        _dataList.push({ 'i' : i });
                    }
                });
            }
        });

        sequentialAjaxCalls(_dataList).then(() => {
            _log('All AJAX calls have been completed.');

            let _html = '';
            let _print_type = '';
            let _print_page = Math.ceil(w_this.find('#screenshotTarget').height() / w_blockHeight);
            if(_print_page > w_maxpage){
                _print_type = 'over_print';
            }else{
                // default ''
            }
            _html = '<div class="prompt_receive">'+prompt_msg(_print_type)+'</div>';
            w_this.find('.prompt_history .prompt_data[data-idx="'+w_process_idx+'"] .prompt_receive').after(_html);
            toggle_print_layout(w_loading = false);
        }).catch((error) => {
            console.error('Error occurred during AJAX calls:', error);
            toggle_print_layout(w_loading = false);
        });
    }

    function prompt_msg(_type){
        let _html = '';

        switch(_type) {
            case 'over_print':
                let _print_page = Math.ceil(w_this.find('#screenshotTarget').height() / w_blockHeight);
                _html += '    생성 페이지 수 / 최대 페이지 수 : '+_print_page+' / '+w_maxpage+'<br/>';
                _html += '    요청하신 페이지 보다 더 많은 페이지가 생성 되었습니다. 출력 하시겠습니까?<br/>';
                _html += '    <div class="btn_box">';
                _html += '        <button type="button" class="btn btn-primary btn_file_agree">출력하기</button>';
                _html += '        <button type="button" class="btn btn-default btn_file_disagree">취소</button>';
                _html += '    </div>';
                break;
            case 'cancel_print':
                _html += '    출력을 취소했습니다.';
                break;
            default:
                _html += '    <span class="text-primary">이제 완료하였습니다. 다운로드를 눌러서 받으시고 내역보기에서도 과거 만든 내역까지 더 보실 수 있어요.</span><br/>';
                _html += '    <div class="btn_box">';
                //_html += '        <button type="button" class="btn btn-info btn_file_history">내역보기</button>';
                _html += '        <button type="button" class="btn btn-success btn_file_download">다운로드</button>';
                _html += '    </div>';
                break;
        }

        return _html;
    }

    function prompt_morph(_prompt){
        let module = 'chatbot';
        let botuid='<?php echo $botuid?>';

        $.ajax({
                type: "POST",
                url: rooturl,
                dataType:"json",
                data: {
                    "r" : raccount,
                    "m" : module,
                    "a" : "statgpt",
                    linkType : 'prompt_morph',
                    prompt : _prompt,
                    botuid : botuid,
                },
                success: function(msg){
                    w_prompt_morph = msg;
                    _log("as\as :", w_prompt_morph);

                    if(msg.error){
                        alert(msg.message);
                        toggle_print_layout(w_loading = false);
                        return false;
                    }

                    w_process_idx = generateRandomNumber();

                    if(msg.prompt.process_flag <= 0){
                        let _html = '';
                        _html += '<div class="prompt_data" data-idx="'+w_process_idx+'">';
                        _html += '  <div class="prompt_send">'+_prompt+'</div>';
                        _html += '  <div class="prompt_receive">'+msg.prompt.prompt+'</div>';
                        _html += '</div>';
                        w_this.find('.prompt_history').prepend(_html);
                        w_this.find('input[name="prompt"]').val('').focus();
                        toggle_print_layout(w_loading = false);
                        _log("!");
                        return false;
                    }

                    _log("!asdsdads");
                    //_log(typeof msg.prompt.type);
                    if(typeof msg.prompt.type != 'undefined'){
                        w_this.find('input[name="stat[]"]').prop('checked', false);
                        $.each(msg.prompt.type, function(i, e){
                            _log('asddd - '+e);
                            w_this.find('input[name="stat[]"][value="'+e+'"]').prop('checked', true);
                        });
                    }else{
                        w_this.find('input[name="stat[]"]').prop('checked', true);
                    }
                    //_log(msg.prompt.docu);
                    w_this.find('input[name="filetype"][value="'+msg.prompt.docu+'"]').prop('checked', true);
                    w_maxpage = (msg.prompt.page > 0)? Number(msg.prompt.page) : 9; // default 9
                    w_this.find('input[name="maxpage"]').val(w_maxpage);
                    if(typeof msg.prompt.datetype == 'undefined' || typeof msg.prompt.datetype == null || msg.prompt.datetype == ''){
                        if(msg.prompt.date_match.length > 0){
                            w_this.find('input[name="d_start"]').val(msg.prompt.start_date);
                            w_this.find('input[name="d_end"]').val(msg.prompt.end_date);
                        }
                    }else{
                        if(msg.prompt.date_match.datetype > 0){
                            w_this.find('.log_btn button:nth-child('+msg.prompt.datetype+')').click();
                        }
                    }
                    
                    let _html = '';
                    _html += '<div class="prompt_data" data-idx="'+w_process_idx+'">';
                    _html += '  <div class="prompt_send">'+_prompt+'</div>';
                    _html += '  <div class="prompt_receive">'+msg.prompt.prompt+'</div>';
                    _html += '</div>';
                    w_this.find('.prompt_history').prepend(_html);
                    w_this.find('input[name="prompt"]').val('').focus();

                    //if(typeof msg.prompt.type != 'undefined'){
                        file_setting();
                    //}
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    toggle_print_layout(w_loading = false);
                }
            });
    }

    function sequentialAjaxCalls(dataList) {
        // Promise를 연속적으로 실행하여 AJAX 호출을 동기식으로 처리합니다.
        return dataList.reduce((promiseChain, data) => {
            return promiseChain.then(() => {
                return window['Load_BotChart'+data.i](); // call
                //return ajaxCall(data);
            });
        }, Promise.resolve());
    }

    function init(){
        $.each(w_aSideMenu, function(i, e){
            if(e.id == 'statgpt'){
                return true;
            }
            let _html = '';
            _html += '<div class="form-check">';
            _html += '    <input type="checkbox" id="stat_'+e.id+'" name="stat[]" value="'+e.id+'" checked >';
            _html += '    <label for="stat_'+e.id+'" class="checkbox-inline">'+e.name+'</label>';
            _html += '</div>';
            w_this.find('.aSidemenu').append(_html);
        });
    }

    function import_prompt(target){
        w_this.find(target).val(create_prompt());
    }

    function create_prompt(){
        // ex) 한달 데이터를 파워포인트 9장으로 사용자 현황, 통계관리, 대화 현황, 대화 로그, 대화 분석, 대화 흐름 분석, 군집 분석, 학습을 출력해줘
        w_prompt = ''; // init
        let _prompt_data = [];
        
        // date
        let _date = w_this.find('.log_btn button.btn-primary');
        if(_date.length > 0){
            _prompt_data.push(_date.text()+' 데이터를');
        }else{
            if(w_this.find('#d_start').val() || w_this.find('#d_end').val()){
                if(w_this.find('#d_start').val()){
                    _prompt_data.push(w_this.find('#d_start').val()+'부터');
                }else{
                    _prompt_data.push('처음부터');
                }
                if(w_this.find('#d_end').val()){
                    _prompt_data.push(w_this.find('#d_end').val()+'까지');
                }else{
                    _prompt_data.push('지금까지');
                }
            }
        }

        // type
        let _type = w_this.find('input[name="filetype"]:checked');
        //_log(_type);
        _prompt_data.push(_type.find('+label').text());

        // max_page
        _prompt_data.push(w_maxpage+'장에');

        // stat
        let _stat = [];
        w_this.find('input[name="stat[]"]:checked').each(function(i, v){
            //_log($(this), i, v, $(this).find('+label').text());
            _stat.push($(this).find('+label').text());
        });
        if(_stat.length > 0){
            _prompt_data.push(_stat.join(',')+'을');
        }

        // filename
        let _filename = w_this.find('input[name="filename"]');
        if(_filename.val() != ''){
            _prompt_data.push('파일명은 '+addParticle(_filename.val()));
        }
        
        $.each(_prompt_data, function(i, v){
            //_log(i, v);
            w_prompt += v+' ';
        });
        //_log(_prompt_data, w_prompt);
        w_prompt += '출력해줘';

        return w_prompt;
    }

    function addParticle(word) {
        _log(typeof word, word);
        if(typeof word !== 'string' || word == ""){
            return '';
        }
        const lastChar = word[word.length - 1];
        const code = lastChar.charCodeAt(0) - 0xac00; // 한글 유니코드 값 계산
        const consonant = code % 28 !== 0; // 받침 여부 계산
        const particle = consonant? '으로' : '로';
        _log(code, consonant);
        return `${word}${particle}`;
    }

    var Load_BotChart0 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            사용자 현황
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i data-icon="E" class="linea-icon linea-basic"></i>
                                    <h5 class="text-muted vb">총 누적 접속 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalAccess0" class="text-right m-t-15 text-danger"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">총 누적 세션수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalUser0" class="text-right m-t-15 text-megna"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-megna" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">총 누적 대화 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalChat0" class="text-right m-t-15 text-primary"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">인당 대화 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="perChat0" class="text-right m-t-15 text-primary"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 사용현황 끝 -->
                
                <!--row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                            <h3 class="box-title">접속통계</h3>
                            <div id="total-wrap0">         
                               <!-- load 접속통계 차트 -->       
                            </div>
                        </div>
                    </div>
                </div>
                <!--row -->
                
                <!--row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box h_400">
                            <div class="col-in row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">일자</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">누적 사용자</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">재방문</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">신규</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">대화수</th>
                                        </tr>
                                        <tbody id="total-list0"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--row -->
            `;
            var module='chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var mod = $('input[name="mod"]').val();
            var d_start = $('input[name="d_start"]').val();
            var d_end = $('input[name="d_end"]').val();
            
            w_this.find('#screenshotTarget').append('<div class="temp_html stat_0">'+_html+'</div>');

            $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
                linkType : 'user',
                vendor : vendor,
                botuid : botuid,
                mod : mod,
                d_start : d_start,
                d_end : d_end
            },function(response){
                //checkLogCountdown();
                var result=$.parseJSON(response);
                var total_chart=result.total_chart;
                // insert chart 
                $('#totalAccess0').html(result.totalAccess);
                $('#totalUser0').html(result.totalUser);
                $('#totalChat0').html(result.totalChat);  
                $('#perChat0').html(result.perChat);          
                /*
                $(".counter").counterUp({
                    delay: 100, time: 1200
                });            
                */
                $('#total-wrap0').html(total_chart);
                $('#total-wrap0').html('<canvas id="chart-conversation0" style="height:100%;width:100%;"></canvas>');
                var ctx02 = document.getElementById("chart-conversation0");
                ctx02.height = 80;
                let _page_date = result.tmpl.page_date.split(',').map(str => str.replace(/'/g, ''));
                let _page_all = result.tmpl.page_all.split(',').map(str => str.replace(/'/g, ''));
                let _page_f = result.tmpl.page_f.split(',').map(str => str.replace(/'/g, ''));
                let _page_s = result.tmpl.page_s.split(',').map(str => str.replace(/'/g, ''));
                //_log(_page_date, _page_all, _page_f, _page_s);
                var gigan_data = {
                    labels: _page_date,
                    datasets: [
                        {
                            label: "총 접속",
                            backgroundColor: "#218eff",
                            borderColor: "#218eff",
                            data: _page_all,
                            fill: false,
                        },
                        {
                            label: "순방문",
                            backgroundColor: "#a5a3a3",
                            borderColor: "#a5a3a3",
                            data: _page_f,
                            fill: false,
                        },
                        {
                            label: "재방문",
                            backgroundColor: "#ec7208",
                            borderColor: "#ec7208",
                            data: _page_s,
                            fill: false,
                        }
                    ]
                };
                var myChart = new Chart(ctx02, {
                    type: 'line',
                    data: gigan_data,
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
                $('#total-list0').html(result.totalList);
                resolve(response);
            }); 
        });
    }

    var Load_BotChart1 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            통계 관리
                        </div>
                    </div>
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-4 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">챗봇명</h5> 
                                </div>
                                <div class="col-md-12 col-sm-6 col-xs-6">
                                    <h3 class="text-right" style="width:100%;font-size:20px;font-weight:400;"><?=$getListBot['name']?></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">의도 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalIntent1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">엔터티 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalEntity1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">대화상자 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalNode1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 사용현황 -->
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">총 누적 접속 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalAccess1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">총 누적 세션 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalUser1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">총 누적 대화 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalChat1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box" style="padding:15px 25px;">
                            <div class="col-in row" style="padding:10px 20px;">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h5 class="text-muted vb">인당 대화 수</h5> 
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="perChat1" class="text-right m-t-15 text-danger"></h3> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 사용현황 끝 -->
                
                <!-- /.row -->
                <div class="row">
                    <div class="col-sm-4 box_1">
                        <div class="white-box" style="min-height:665px;">                
                            <h3 class="box-title" style="float:left;width:60%;">대화상자 순위</h3>
                            <h3 class="box-title" id="nodeBtn1" style="float:right;width:40%;text-align:right;"></h3>
                            <input type="hidden" value="1" data-role="nodePage-input"/>
                            <div class="table-responsive" style="clear:both;">
                                <table class="table">
                                    <tr>
                                        <th style="border-top:1px solid #333;">대화상자</th>
                                        <th style="border-top:1px solid #333;">횟수</th>
                                    </tr>
                                    <tbody id="nodeHtml1"></tbody>
                                </table>
                            </div>                
                        </div>
                    </div>
                    <div class="col-sm-4 box_2">
                        <div class="white-box" style="min-height:665px;">
                            <h3 class="box-title" style="float:left;width:60%;">많이 한 질문</h3>
                            <h3 class="box-title" id="questionBtn1" style="float:right;width:40%;text-align:right;"></h3>
                            <input type="hidden" value="1" data-role="questionPage-input"/>
                            <div class="table-responsive" style="clear:both;">
                                <table class="table">
                                    <tr>
                                        <th style="border-top:1px solid #333;">질문내용</th>
                                        <th style="border-top:1px solid #333;">횟수</th>
                                    </tr>
                                    <tbody id="questionHtml1"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-4 box_3">
                        <div class="white-box" style="min-height:665px;">
                            <h3 class="box-title" style="float:left;width:60%;">많이 사용한 단어</h3>
                            <h3 class="box-title" id="wordBtn1" style="float:right;width:40%;text-align:right;"></h3>
                            <input type="hidden" value="1" data-role="wordPage-input"/>
                            <div class="table-responsive" style="clear:both;">
                                <table class="table">
                                    <tr>
                                        <th style="border-top:1px solid #333;">단어</th>
                                        <th style="border-top:1px solid #333;">횟수</th>
                                    </tr>
                                    <tbody id="wordHtml1"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                
                <!--사용자 현황 -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                            <h3 class="box-title">사용자 현황</h3>
                            <div id="total-wrap1">         
                               <!-- load 접속통계 차트 -->       
                            </div>
                        </div>
                    </div>
                </div>
                <!--row -->
                
                <!--대화 현황 -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                            <h3 class="box-title">대화 현황</h3>
                            <div id="conversation-wrap1">         
                               <!-- load 접속통계 차트 -->       
                            </div>
                        </div>
                    </div>
                </div>
                <!--row -->
                
                <!--row -->
                <div class="row">
                    <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box h_400">
                            <div class="col-in row">
                                <h3 class="box-title">많이 클릭한 버튼</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="border-top:1px solid #333;">버튼명</th>
                                            <th style="border-top:1px solid #333;">횟수</th>
                                        </tr>
                                        <tbody id="btnLogHtml11"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box h_400">
                            <div class="col-in row">
                                <h3 class="box-title">&nbsp;</h3>
                                <div class="table-responsive">
                                    <table class="table ">
                                        <tr>
                                            <th style="border-top:1px solid #333;">버튼명</th>
                                            <th style="border-top:1px solid #333;">횟수</th>
                                        </tr>
                                        <tbody id="btnLogHtml12"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                </div>
                <!--row -->
            `;
            var module='chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var mod = $('input[name="mod"]').val();
            var d_start = $('input[name="d_start"]').val();
            var d_end = $('input[name="d_end"]').val();

            w_this.find('#screenshotTarget').append('<div class="temp_html stat_1">'+_html+'</div>');

            if(typeof w_prompt_morph.prompt.only_content != 'undefined' && w_prompt_morph.prompt.only_content.length > 0){ // ~만 출력
                $.each(w_prompt_morph.prompt.only_content, function(i, v){
                    if(v.split('|')[0] == "통계관리"){
                        let _target = v.split('|')[1].replace(/\s/g, '');
                        if(_target == "대화상자순위"){
                            w_this.find('#screenshotTarget .stat_1 .box_1').removeClass('col-sm-4').addClass('col-sm-12');
                            w_this.find('#screenshotTarget .stat_1 .box_2').remove();
                            w_this.find('#screenshotTarget .stat_1 .box_3').remove();
                        }else if(_target == "많이한질문"){
                            w_this.find('#screenshotTarget .stat_1 .box_1').remove();
                            w_this.find('#screenshotTarget .stat_1 .box_2').removeClass('col-sm-4').addClass('col-sm-12');
                            w_this.find('#screenshotTarget .stat_1 .box_3').remove();
                        }else if(_target == "많이사용한단어"){
                            w_this.find('#screenshotTarget .stat_1 .box_1').remove();
                            w_this.find('#screenshotTarget .stat_1 .box_2').remove();
                            w_this.find('#screenshotTarget .stat_1 .box_3').removeClass('col-sm-4').addClass('col-sm-12');
                        }
                    }
                });
            }else if(typeof w_prompt_morph.prompt.del_content != 'undefined' && w_prompt_morph.prompt.del_content.length > 0){ // ~빼고 출력
                $.each(w_prompt_morph.prompt.del_content, function(i, v){
                    if(v.split('|')[0] == "통계관리"){
                        let _target = v.split('|')[1].replace(/\s/g, '');
                        if(_target == "대화상자순위"){
                            w_this.find('#screenshotTarget .stat_1 .box_1').remove();
                            w_this.find('#screenshotTarget .stat_1 .box_2').removeClass('col-sm-4').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_1 .box_3').removeClass('col-sm-4').addClass('col-sm-6');
                        }else if(_target == "많이한질문"){
                            w_this.find('#screenshotTarget .stat_1 .box_1').removeClass('col-sm-4').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_1 .box_2').remove();
                            w_this.find('#screenshotTarget .stat_1 .box_3').removeClass('col-sm-4').addClass('col-sm-6');
                        }else if(_target == "많이사용한단어"){
                            w_this.find('#screenshotTarget .stat_1 .box_1').removeClass('col-sm-4').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_1 .box_2').removeClass('col-sm-4').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_1 .box_3').remove();
                        }
                    }
                });
            }

            $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
                linkType : 'all_data',
                vendor : vendor,
                botuid : botuid,
                mod : mod,
                d_start : d_start,
                d_end : d_end
            },function(response){
                //checkLogCountdown();
                var result=$.parseJSON(response);//$.parseJSON(response);
                var total_chart=result.total_chart;
                
                // insert chart 
                $('#totalIntent1').html(result.totalIntent);
                $('#totalEntity1').html(result.totalEntity);
                $('#totalNode1').html(result.totalNode);
                $('#totalAccess1').html(result.totalAccess);
                $('#totalUser1').html(result.totalUser);
                $('#totalChat1').html(result.totalChat);  
                $('#perChat1').html(result.perChat);          
                /*
                $(".counter").counterUp({
                    delay: 100, time: 1200
                });
                */
                $('#nodeHtml1').html(result.nodeHtml);
                $('#nodeBtn1').html(result.nodeBtn);
                $('#questionHtml1').html(result.questionHtml);
                $('#questionBtn1').html(result.questionBtn);
                $('#wordHtml1').html(result.wordHtml);
                $('#wordBtn1').html(result.wordBtn);
                $('#btnLogHtml11').html(result.btnLogHtml1);
                $('#btnLogHtml12').html(result.btnLogHtml2);
                $('#total-wrap1').html(result.total_chart);
                $('#conversation-wrap1').html('<canvas id="chart-conversation1" style="height:100%;width:100%;"></canvas>');
                var ctx12 = document.getElementById("chart-conversation1");
                ctx12.height = 80;
                let _page_date = result.tmpl.page_date.split(',').map(str => str.replace(/'/g, ''));
                let _page_data = result.tmpl.page_data.split(',').map(str => str.replace(/'/g, ''));
                let _fall_data = result.tmpl.fall_data.split(',').map(str => str.replace(/'/g, ''));
                //_log(_page_date, _page_data, _fall_data);
                var gigan_data = {
                    labels: _page_date,
                    datasets: [
                        {
                            label: "전체 대화",
                            fill: false,
                            backgroundColor: "#218eff",
                            borderColor: "#218eff",
                            data: _page_data,
                        },
                        {
                            label: "Fallback",
                            fill: false,
                            backgroundColor: "#ec7208",
                            borderColor: "#ec7208",
                            data: _fall_data,
                        }
                    ]
                };
                var myChart = new Chart(ctx12, {
                    type: 'line',
                    data: gigan_data,
                    options: {
                        title: {display:false},
                        tooltip: {mode: 'index', intersect: false},
                        hover: {mode: 'nearest', intersect: true},
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        },
                        onComplete: function() {
                            isChartRendered = true
                        }
                    }
                });
                resolve(response);
            });
        });
    }

    var Load_BotChart2 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            대화 현황
                        </div>
                    </div>
                </div>
                <!-- 사용현황 -->
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i data-icon="E" class="linea-icon linea-basic"></i>
                                    <h5 class="text-muted vb">총 누적 접속 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalAccess2" class="text-right m-t-15 text-danger"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">총 누적 세션수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalUser2" class="text-right m-t-15 text-megna"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-megna" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">총 누적 대화 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalChat2" class="text-right m-t-15 text-primary"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">인당 대화 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="perChat2" class="text-right m-t-15 text-primary"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 사용현황 끝 -->
                
                <!--row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                            <h3 class="box-title">대화현황</h3>
                            <div id="conversation-wrap2">         
                               <!-- load 접속통계 차트 -->       
                            </div>
                        </div>
                    </div>
                </div>
                <!--row -->
                
                <!--row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box h_400">
                            <div class="col-in row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">일자</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">전체 세션</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">전체 대화</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">인당 대화 수</th>
                                            <th style="width:20%;border-top:1px solid #333;text-align:center;">Fallback</th>
                                        </tr>
                                        <tbody id="total-list2"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>            
                </div>
                <!--row -->
            `;
            var module='chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var mod = $('input[name="mod"]').val();
            var d_start = $('input[name="d_start"]').val();
            var d_end = $('input[name="d_end"]').val();

            w_this.find('#screenshotTarget').append('<div class="temp_html stat_2">'+_html+'</div>');

            $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
                linkType : 'conversation',
                vendor : vendor,
                botuid : botuid,
                mod : mod,
                d_start : d_start,
                d_end : d_end
            },function(response){
                //checkLogCountdown();
                var result=$.parseJSON(response);//$.parseJSON(response);
                var total_chart=result.total_chart;
                
                // insert chart 
                $('#totalAccess2').html(result.totalAccess);
                $('#totalUser2').html(result.totalUser);
                $('#totalChat2').html(result.totalChat);  
                $('#perChat2').html(result.perChat);          
                /*
                $(".counter").counterUp({
                    delay: 100, time: 1200
                });            
                */
                $('#conversation-wrap2').html('<canvas id="chart-conversation2" style="height:100%;width:100%;"></canvas>');
                var ctx22 = document.getElementById("chart-conversation2");
                ctx22.height = 80;
                let _page_date = result.tmpl.page_date.split(',').map(str => str.replace(/'/g, ''));
                let _page_data = result.tmpl.page_data.split(',').map(str => str.replace(/'/g, ''));
                let _fall_data = result.tmpl.fall_data.split(',').map(str => str.replace(/'/g, ''));
                //_log(_page_date, _page_data, _fall_data);
                var gigan_data = {
                    labels: _page_date,
                    datasets: [
                        {
                            label: "전체 대화",
                            fill: false,
                            backgroundColor: "#218eff",
                            borderColor: "#218eff",
                            data: _page_data,
                        },
                        {
                            label: "Fallback",
                            fill: false,
                            backgroundColor: "#ec7208",
                            borderColor: "#ec7208",
                            data: _fall_data,
                        }
                    ]
                };
                var myChart = new Chart(ctx22, {
                    type: 'line',
                    data: gigan_data,
                    options: {
                        title: {display:false},
                        tooltip: {mode: 'index', intersect: false},
                        hover: {mode: 'nearest', intersect: true},
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        },
                        onComplete: function() {
                            isChartRendered = true
                        }
                    }
                });

                $('#total-list2').html(result.totalList);
                resolve(response);
            });
        });
    }

    var Load_BotChart3 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            대화 로그
                        </div>
                    </div>
                </div>
                <div class="container-fluid table-fluid">
                    <!-- /.row -->
                    <div class="table-container">
                        <div class="intEntTable-wrapper"> 
                            <div class="table-responsive table-wrapper" data-role="table-wrapper">
                                <table class="table table-striped table-full" id="tbl-conversation" data-role="tbl-conversation">
                                    <thead>
                                        <tr class="table-header">
                                            <th class="intEnt-name">아이디</th>
                                            <th class="intEnt-des">일자</th>
                                            <th class="intEnt-ex">대화흐름 수</th>
                                            <th class="intEnt-ex">응답못한 대화</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- import data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            let module = 'chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var mod = $('input[name="mod"]').val();
            var d_start = $('input[name="d_start"]').val();
            var d_end = $('input[name="d_end"]').val();
            _log(vendor, botuid, mod, d_start, d_end);

            w_this.find('#screenshotTarget').append('<div class="temp_html stat_3">'+_html+'</div>');

            $.ajax({
                type: "POST",
                url: rooturl,
                dataType:"json",
                data: {
                    "r" : raccount,
                    "m" : module,
                    "a" : "statgpt",
                    linkType : 'conversation',
                    vendor : vendor,
                    botuid : botuid,
                    mod : mod,
                    d_start : d_start,
                    d_end : d_end
                },
                success: function(msg){
                    _log("asdasdasdas :", msg);

                    let _html = '';
                    $.each(msg.conversation, function(i, e){
                        let _username = (e.userName)? e.userName : e.roomToken;
                        _html += '<tr>';
                        _html += '    <td class="txt-oflo intEnt-name">';
                        _html += '        <img src="'+e.userPic+'" alt="viewchat search result" class="conver-userPic" />';
                        _html += '        <span class="cb-name">'+e.userNameInChat+'('+_username+')</span>';
                        _html += '    </td>';
                        _html += '    <td class="txt-oflo">'+e.d_regis+'</td>';
                        _html += '    <td class="txt-oflo">'+e.nCntChat+'</td>';
                        _html += '    <td class="txt-oflo">'+e.nCntUnknown+'</td>';
                        _html += '</tr>';
                    });

                    w_this.find('#screenshotTarget .stat_3 #tbl-conversation tbody').html(_html);
                    resolve(msg);
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    reject(error);
                }
            });
        });
    }

    var Load_BotChart4 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            대화 분석
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <!-- 그래프 -->
                        <div id="node_chart" class="white-box bottom0 nodeInfoWrap" style="padding:0;">            
                        </div>

                        <!-- 대화상자 현황 -->
                        <div id="stateWrap" class="white-box bottom0 nodeInfoWrap">
                            <div class="top_info">
                                <ul class="ul_info">
                                    <li>
                                        <span>총 대화상자 수</span>
                                        <span id="totalNode" class="info_num">0</span>
                                    </li>
                                    <li>
                                        <span>총 대화 수</span>
                                        <span id="totalChat" class="info_num">0</span>
                                    </li>
                                    <li>
                                        <span>인당 대화 수</span>
                                        <span id="perChat" class="info_num">0</span>
                                    </li>
                                    <li>
                                        <span>답변 못한 대화</span>
                                        <span id="totalUnknown" class="info_num">0</span>
                                    </li>
                                    <li>
                                        <span>총 누적 접속 수</span>
                                        <span id="totalAccess" class="info_num">0</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- 대화상자 현황 리스트 -->
                            <div class="listWrap" style="margin-top:25px;">
                                <h3 class="box-title" style="float:left;width:20%;">대화상자</h3>
                                <div class="fixed_table_container" style="width:100%;">
                                    <div class="fixed_table_header_bg"></div>
                                    <div class="fixed_table_wrapper">
                                        <table class="fixed_table" id="tbl-wordgroup">
                                            <thead>                            
                                                <tr>
                                                    <th style="width:10%;"><div class="th_text">No</div></th>
                                                    <th style="width:40%;"><div class="th_text">대화상자</div></th>
                                                    <th style="width:25%;"><div class="th_text">질문수</div></th>
                                                    <th style="width:25%;"><div class="th_text">질문율</div></th>
                                                </tr>                            
                                            </thead>
                                            <tbody id="stateHtml">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            var linkType = linkType == undefined || linkType == "" ? "node_analysis" : linkType;
            var nodeNames = '', page = '';
            if(linkType == 'node_conversation') {
                nodeNames = $('.ul_node_box li span').not('.off').map(function() {return $(this).text();}).get().join('|');
                page = $(":input:hidden[name=conv_page]").val();
            }
            
            w_this.find('#screenshotTarget').append('<div class="temp_html stat_4">'+_html+'</div>');

            $.post(rooturl+'/?r='+raccount+'&m=<?=$m?>&a=get_StatisticsChart',{
                linkType : linkType,
                vendor : '<?=$vendor?>',
                botuid : '<?=$botuid?>',
                d_start : $('input[name="d_start"]').val(),
                d_end : $('input[name="d_end"]').val(),
                nodeNames : nodeNames,
                page: page
            },function(response){
                //checkLogCountdown();
                var result=$.parseJSON(response);
                
                if(linkType == 'node_analysis') {
                    $.each(result.total, function(key, value) {
                        $('#'+key).text(value);
                    });
                    //$(".counter").counterUp({delay: 100, time: 1200});                
                    w_this.find('.stat_4 #stateHtml').html(result.stateHtml);                
                    getChartView(result.nodes);
                    
                } else if(linkType == 'node_conversation') {
                    w_this.find('.stat_4 #convHtml').html(result.convHtml);
                    w_this.find('.stat_4 #pageHtml').html(result.pageHtml);
                }

                resolve(response);
            });
        });
    }

    var Load_BotChart5 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            대화 흐름 분석
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 white-box">
                        <!-- 그래프 -->
                        <div id="node_chart5" class="nodeInfoWrap" style="padding:0;">            
                        </div>
                        <!-- 리스트 -->
                        <div class="listWrap" style="margin-top:25px;">
                            <h3 class="box-title" style="float:left;width:20%;">
                                대화 흐름
                                <span id="pageHtml5" style="display:inline-block; margin-left:30px;"></span>
                                <input type="hidden" name="conv_page" value="1" /> 
                                <input type="hidden" name="hTokens" value="" /> 
                            </h3>
                            <div class="fixed_table_container" style="width:100%;">
                                <div class="fixed_table_header_bg"></div>
                                <div class="fixed_table_wrapper">
                                    <table class="fixed_table">
                                        <thead>                            
                                            <tr>
                                                <th style="width:9%;"><div class="th_text">룸토큰</div></th>
                                                <th style="width:8%;"><div class="th_text">채널</div></th>
                                                <th style="width:8%;"><div class="th_text">날짜</div></th>
                                                <th style="width:8%;"><div class="th_text">시작</div></th>
                                                <th style="width:8%;"><div class="th_text">종료</div></th>
                                                <th style="width:8%;"><div class="th_text">이용</div></th>
                                                <th style="width:8%;"><div class="th_text">종료구분</div></th>
                                                <th style="width:43%;"><div class="th_text">대화흐름</div></th>                                        
                                            </tr>                            
                                        </thead>
                                        <tbody id="convHtml5"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            w_this.find('#screenshotTarget').append('<div class="temp_html stat_5">'+_html+'</div>');

            var linkType = "node_flow";
            var page = $(":input:hidden[name=conv_page]").val();
            var hTokens = $(":input:hidden[name=hTokens]").val();

            $.post(rooturl+'/?r='+raccount+'&m=<?=$m?>&a=get_StatisticsChart',{
                linkType : linkType,
                vendor : '<?=$vendor?>',
                botuid : '<?=$botuid?>',
                d_start : w_this.find('input[name="d_start"]').val(),
                d_end : w_this.find('input[name="d_end"]').val(),
                hTokens : hTokens,
                page: page
            },function(response){
                //checkLogCountdown();
                var result=$.parseJSON(response);
                _log(hTokens, result.hasOwnProperty('node_json'), result);
                _log(Object.keys(result.node_json.links).length);
                if(hTokens == "" && result.hasOwnProperty('node_json') && Object.keys(result.node_json.links).length > 0) {
                    getChartView2(result.node_json);
                }
                w_this.find('.stat_5 #convHtml5').html(result.convHtml);
                w_this.find('.stat_5 #pageHtml5').html(result.pageHtml);

                resolve(response);
            });
        });
    }

    var Load_BotChart6 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            군집 분석
                        </div>
                    </div>
                </div>
                <!-- 사용현황 -->
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i data-icon="E" class="linea-icon linea-basic"></i>
                                    <h5 class="text-muted vb">총 누적 접속 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalAccess6" class="text-right m-t-15 text-danger"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">총 누적 세션수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalUser6" class="text-right m-t-15 text-megna"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-megna" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">총 누적 대화 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="totalChat6" class="text-right m-t-15 text-primary"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <div class="col-in row">
                                <div class="col-md-6 col-sm-6 col-xs-6"> <i class="linea-icon linea-basic" data-icon=""></i>
                                    <h5 class="text-muted vb">인당 대화 수</h5> </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <h3 id="perChat6" class="text-right m-t-15 text-primary"></h3> </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 사용현황 끝 -->

                <div class="row">
                    <div class="col-sm-6">
                        <div id="word_cloud6" class="white-box" style="position:relative; min-height:720px;">

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="white-box" style="min-height:720px;">
                            <input type="hidden" value="1" data-role="wordgroupPage-input"/>
                            <h3 class="box-title" style="float:left;width:20%;">사용자 질문</h3>
                            <h3 class="box-title" style="float:left;width:30%;">
                                <button class="btn btn-info" data-role="change-learnState" data-state="intent">인텐트 지정</button>
                            </h3>
                            <h3 id="wordBtn6" class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;">
                            </h3>
                            <div class="table-responsive" style="clear:both;">
                                <table class="table" id="tbl-wordgroup6">
                                    <colgroup>
                                        <col width="5%">
                                        <col width="*">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" data-role="select-all" data-parent="#tbl-wordgroup"/></th>
                                            <th>질문내용</th>
                                        </tr>
                                    </thead>
                                    <tbody id="wordHtml6">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            var module='chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var mod = 'wordgroup';
            var d_start = $('input[name="d_start"]').val();
            var d_end = $('input[name="d_end"]').val();

            w_this.find('#screenshotTarget').append('<div class="temp_html stat_6">'+_html+'</div>');

            $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=get_StatisticsChart',{
                linkType : 'wordgroup',
                vendor : vendor,
                botuid : botuid,
                mod : mod,
                d_start : d_start,
                d_end : d_end
            },function(response){
                //checkLogCountdown();
                var result=$.parseJSON(response);//$.parseJSON(response);
                var total_chart=result.total_chart;

                _log(result);

                // insert chart
                $('#totalAccess6').html(result.totalAccess);
                $('#totalUser6').html(result.totalUser);
                $('#totalChat6').html(result.totalChat);
                $('#perChat6').html(result.perChat);
                /*
                $(".counter").counterUp({
                    delay: 100, time: 1200
                });
                */
                $("#word_cloud6").jQCloud('destroy');
                $("#word_cloud6").jQCloud(result.aWordJson, {
                    width: $("#word_cloud6").width(),
                    height: $("#word_cloud6").height(),
                    autoResize: true,
                    afterCloudRender: function() {
                        $("#word_cloud6 a:eq(0)").css("color", "#ff7600");
                    }
                });

                $('#wordHtml6').html(result.wordHtml);
                $('#wordBtn6').html(result.wordBtn);

                resolve(response);
            });
        });
    }

    var Load_BotChart7 = function(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            _log("kkkkkkk");
            _log(w_prompt_morph.prompt.del_content);

            let _html = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box stat_title">
                            학습
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-sm-5 box_1">
                        <div class="white-box" style="min-height:720px;">
                            <input type="hidden" value="wait" data-role="unknownMod-input"/>
                            <input type="hidden" value="1" data-role="unknownPage-input"/> 
                            <h3 class="box-title" style="float:left;width:70%;">
                                <span>답변 못한 문장</span>
                            </h3>
                            <h3 class="box-title" style="float:right;width:30%;text-align:right;" data-role="unknownPage-wrapper">
                                <!-- import data $unKnownPageBtn -->
                            </h3>
                             <ul class="nav nav-tabs unknown-state">
                                 <li class="active unknown-mod" style="cursor:pointer;"><a data-toggle="tab" data-role="change-unknownMod" data-mod="wait" id="learn-wait">학습대기</a></li>
                                 <li class="unknown-mod" style="cursor:pointer;"><a data-toggle="tab" data-role="change-unknownMod" data-mod="done">학습완료</a></li>
                             </ul>

                            <div class="table-responsive clearfix" style="position:relative;margin-top:10px;padding-bottom:20px;clear:both;">
                                <table class="table" id="tbl-unknown" style="margin-bottom:0;">
                                    <colgroup>
                                        <col width="5%">
                                        <col width="77%">
                                        <col width="18%">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" data-role="select-all" data-parent="#tbl-unknown" style="margin:0;" /></th>
                                            <th>질문내용</th>
                                            <th data-role="unknown-dateLabel">등록일 </th>
                                        </tr>
                                    </thead>
                                    <tbody data-role="unknownList-wrapper">
                                        <!-- import data $unKnownList -->
                                    </tbody>
                                </table>
                                <div class="col-sm-12">
                                    <button id="btn_done" class="btn btn-success" data-role="change-learnState" data-state="done" data-msg="학습완료">학습완료 처리</button>
                                    <button id="btn_wait" class="btn btn-danger" data-role="change-learnState" data-state="wait" data-msg="학습대기" style="display:none; margin-left:5px">학습대기 처리</button>
                                    <button id="btn_intent" class="btn btn-info" data-role="change-learnState" data-state="intent" style="margin-left:5px"><?php echo $chatbot->callIntent?> 지정</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="col-sm-4 box_2">
                        <div class="white-box" style="min-height:720px;">
                            <h3 class="box-title" style="float:left;width:60%;">많이 한 질문</h3> 
                            <h3 class="box-title" style="float:right;width:40%;text-align:right;font-size:15px;" data-role="questionPage-wrapper">
                                <!-- import data $questionData[2] -->
                            </h3>
                            <div class="table-responsive" style="clear:both;">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th style="width:80%">질문내용</th>                                
                                            <th>횟수</th>
                                        </tr>
                                    </thead>
                                    <tbody data-role="questionList-wrapper">
                                        <!-- import data $questionData[1] -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-3 box_3">
                        <div class="white-box" style="min-height:720px;">
                            <h3 class="box-title" style="float:left;width:50%;">많이 사용한 단어</h3>
                            <h3 class="box-title" style="float:right;width:50%;text-align:right;font-size:15px;" data-role="wordPage-wrapper">
                                <!-- import data $wordData[2] -->
                            </h3>
                            <div class="table-responsive" style="clear:both;">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th style="width:80%">단어</th>                                
                                            <th>횟수</th>
                                        </tr>
                                    </thead>
                                    <tbody data-role="wordList-wrapper">
                                        <!-- import data $wordData[1] -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            `;

            let module = 'chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var mod = 'wait';
            var d_start = $('input[name="d_start"]').val();
            var d_end = $('input[name="d_end"]').val();
            _log(vendor, botuid, mod, d_start, d_end);

            w_this.find('#screenshotTarget').append('<div class="temp_html stat_7">'+_html+'</div>');
            
            if(typeof w_prompt_morph.prompt.only_content != 'undefined' && w_prompt_morph.prompt.only_content.length > 0){
                $.each(w_prompt_morph.prompt.only_content, function(i, v){
                    if(v.split('|')[0] == "학습"){
                        let _target = v.split('|')[1].replace(/\s/g, '');
                        if(_target == "답변못한문장"){
                            w_this.find('#screenshotTarget .stat_7 .box_1').removeClass('col-sm-5').addClass('col-sm-12');
                            w_this.find('#screenshotTarget .stat_7 .box_2').remove();
                            w_this.find('#screenshotTarget .stat_7 .box_3').remove();
                        }else if(_target == "많이한질문"){
                            w_this.find('#screenshotTarget .stat_7 .box_1').remove();
                            w_this.find('#screenshotTarget .stat_7 .box_2').removeClass('col-sm-4').addClass('col-sm-12');
                            w_this.find('#screenshotTarget .stat_7 .box_3').remove();
                        }else if(_target == "많이사용한단어"){
                            w_this.find('#screenshotTarget .stat_7 .box_1').remove();
                            w_this.find('#screenshotTarget .stat_7 .box_2').remove();
                            w_this.find('#screenshotTarget .stat_7 .box_3').removeClass('col-sm-3').addClass('col-sm-12');
                        }
                    }
                });
            }else if(typeof w_prompt_morph.prompt.del_content != 'undefined' && w_prompt_morph.prompt.del_content.length > 0){
                $.each(w_prompt_morph.prompt.del_content, function(i, v){
                    if(v.split('|')[0] == "학습"){
                        let _target = v.split('|')[1].replace(/\s/g, '');
                        if(_target == "답변못한문장"){
                            w_this.find('#screenshotTarget .stat_7 .box_1').remove();
                            w_this.find('#screenshotTarget .stat_7 .box_2').removeClass('col-sm-4').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_7 .box_3').removeClass('col-sm-3').addClass('col-sm-6');
                        }else if(_target == "많이한질문"){
                            w_this.find('#screenshotTarget .stat_7 .box_1').removeClass('col-sm-5').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_7 .box_2').remove();
                            w_this.find('#screenshotTarget .stat_7 .box_3').removeClass('col-sm-3').addClass('col-sm-6');
                        }else if(_target == "많이사용한단어"){
                            w_this.find('#screenshotTarget .stat_7 .box_1').removeClass('col-sm-5').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_7 .box_2').removeClass('col-sm-4').addClass('col-sm-6');
                            w_this.find('#screenshotTarget .stat_7 .box_3').remove();
                        }
                    }
                });
            }

            $.ajax({
                type: "POST",
                url: rooturl,
                dataType:"json",
                data: {
                    "r" : raccount,
                    "m" : module,
                    "a" : "statgpt",
                    linkType : 'learning',
                    vendor : vendor,
                    botuid : botuid,
                    mod : mod,
                    d_start : d_start,
                    d_end : d_end
                },
                success: function(msg){
                    _log("asdasdasdas :", msg);
                    w_this.find('#screenshotTarget .stat_7 [data-role="unknownPage-wrapper"]').html(msg.learning.unKnownPageBtn);
                    w_this.find('#screenshotTarget .stat_7 [data-role="unknownList-wrapper"]').html(msg.learning.unKnownList);
                    w_this.find('#screenshotTarget .stat_7 [data-role="questionPage-wrapper"]').html(msg.learning.questionData[2]);
                    w_this.find('#screenshotTarget .stat_7 [data-role="questionList-wrapper"]').html(msg.learning.questionData[1]);
                    w_this.find('#screenshotTarget .stat_7 [data-role="wordPage-wrapper"]').html(msg.learning.wordData[2]);
                    w_this.find('#screenshotTarget .stat_7 [data-role="wordList-wrapper"]').html(msg.learning.wordData[1]);
                    resolve(msg);
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    reject(error);
                }
            });
        });
    }

    // 그래프 그리기
    function getChartView(data) {
        $("#node_chart").empty();
        var myChart = Sunburst();
        myChart
            .data(data)
            .label('name')
            .size('size')
            .width(($("#node_chart").height()))
            .height(($("#node_chart").height()))        
            .color((d, parent) => color(parent ? parent.data.name : null))
            //.color('color')
            .tooltipContent((d, node) => {return '';})
            .onNodeClick(node => {
                getNodeBoxView(node);
            })
        (document.getElementById('node_chart'));
    }

    // 그래프 그리기
    function getChartView2(json) {
        var margin = {top: 1, right: 1, bottom: 6, left: 1},
            width = $("#node_chart5").width() - margin.left - margin.right,
            height = $("#node_chart5").height() - margin.top - margin.bottom;
            
        var formatNumber = d3.format(",.0f"),
            format = function(d) { return formatNumber(d); },
            color = d3.scale.category20();
            
        d3.select("#node_chart5").selectAll("svg").remove();
        var svg = d3.select("#node_chart5").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
                
        var sankey = d3.sankey()
            .nodeWidth(15)
            .nodePadding(10)
            .size([width, height]);
            
        var path = sankey.link();
        sankey
            .nodes(json.nodes)
            .links(json.links)
            .layout(32);
            
        var link = svg.append("g").selectAll(".link")
            .data(json.links)
            .enter().append("path")
            .attr("class", "link")
            .attr("d", path)
            .attr("id", function(d,i){
                d.id = i;
                return "link-"+i;
            })
            .style("stroke-width", function(d) { return Math.max(1, d.dy); })
            .sort(function(a, b) { return b.dy - a.dy; });
            
        link.append("title")
            .text(function(d) { return d.source.name + "->" + d.target.name; });
        link.on("click", function(d) { _log(d.source.name + "->" + d.target.name);});
            
        var node = svg.append("g").selectAll(".node")
            .data(json.nodes)
            .enter().append("g")
            .attr("class", "node")
            .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
            .on("click",highlight_node_links)
            .call(d3.behavior.drag()
            .origin(function(d) { return d; })
            // interfering with click .on("dragstart", function() { this.parentNode.appendChild(this); })
            .on("drag", dragmove));
            
        node.append("rect")
            .attr("height", function(d) { return d.dy; })
            .attr("width", sankey.nodeWidth())
            .style("fill", function(d) { return d.color = color(d.name.replace(/ .*/, "")); })
            .style("stroke", function(d) { return d3.rgb(d.color).darker(2); })
            .append("title")
            .text(function(d) { return d.name; });
            
        node.append("text")
            .attr("x", -6)
            .attr("y", function(d) { return d.dy / 2; })
            .attr("dy", ".35em")
            .attr("text-anchor", "end")
            .attr("transform", null)
            .text(function(d) { return d.name; })
            .filter(function(d) { return d.x < width / 2; })
            .attr("x", 6 + sankey.nodeWidth())
            .attr("text-anchor", "start");
            
        function dragmove(d) {
            d3.select(this).attr("transform", "translate(" + d.x + "," + (d.y = Math.max(0, Math.min(height - d.dy, d3.event.y))) + ")");
            sankey.relayout();
            link.attr("d", path);
        }
        
        function highlight_node_links(node,i){
            var remainingNodes=[],
            nextNodes=[];
            
            $("svg .node").attr("data-clicked", "0");
            $("svg path.link").css("stroke-opacity", "0.1");
    
            d3.select(this).attr("data-clicked","1");
            var stroke_opacity = 0.5;
            
            var traverse = [{
                linkType : "sourceLinks",
                nodeType : "target"
            },{
                linkType : "targetLinks",
                nodeType : "source"
            }];
            
            var aLinkRoomToken = [];
            
            traverse.forEach(function(step){
                node[step.linkType].forEach(function(link) {
                    remainingNodes.push(link[step.nodeType]);
                    highlight_link(link.id, stroke_opacity);
                });
                
                while (remainingNodes.length) {
                    nextNodes = [];
                    remainingNodes.forEach(function(node) {
                        node[step.linkType].forEach(function(link) {
                            nextNodes.push(link[step.nodeType]);
                            highlight_link(link.id, stroke_opacity);
                            if(aLinkRoomToken.indexOf(link.token) == -1) {
                                aLinkRoomToken.push(link.token);
                            }
                        });
                    });
                    remainingNodes = nextNodes;
                }
            });
            
            // 노드 클릭시 해당 노드 관련 roomToken 조회
            if( d3.select(this).attr("data-clicked") == "1" ){
                $(":input:hidden[name=conv_page]").val(1);
                $(":input:hidden[name=hTokens]").val(aLinkRoomToken.toString());                
            } else {
                $(":input:hidden[name=hTokens]").val("");
            }
            Load_BotChart5();
        }
        
        function highlight_link(id,opacity){
            d3.select("#link-"+id).style("stroke-opacity", opacity);
        }
    }

    function file_download(){
        const _typeArr = ['pdf','ppt','doc'];
        let _type = w_this.find('input[name="filetype"]:checked').val();
        //_log(_typeArr.indexOf(_type));
        toggle_print_layout(w_loading = true);
        if(_type == '' || _typeArr.indexOf(_type) === -1){
            _log('exit');
            return false;
        }

        switch(_typeArr.indexOf(_type)) {
            case 0:
                w_blockHeight = 1754;
                $.each(w_this.find('#screenshotTarget .temp_html'), function(i, e){
                    let _this = $(this);
                    let _newHeight = w_blockHeight * Math.ceil(e.scrollHeight / w_blockHeight);
                    _this.css('height', _newHeight);
                });
                w_this.find('#screenshotTarget').css('width', '1240px');
                w_this.find('#screenshotTarget').css('height', '1754px');
                w_this.find('#screenshotTarget .temp_html').css('min-height', '1754px');
                doDownPdf();
                break;
            case 1:
                w_blockHeight = 1240;
                $.each(w_this.find('#screenshotTarget .temp_html'), function(i, e){
                    let _this = $(this);
                    _log('before: '+e.scrollHeight);
                    let _newHeight = w_blockHeight * Math.ceil(e.scrollHeight / w_blockHeight);
                    _this.css('height', _newHeight);
                    _log('after : '+e.scrollHeight);
                });
                w_this.find('#screenshotTarget').css('width', '1754px');
                w_this.find('#screenshotTarget').css('height', '1240px');
                w_this.find('#screenshotTarget .temp_html').css('min-height', '1240px');
                doDownPpt();
                break;
            case 2:
                w_blockHeight = 1754;
                $.each(w_this.find('#screenshotTarget .temp_html'), function(i, e){
                    let _this = $(this);
                    let _newHeight = w_blockHeight * Math.ceil(e.scrollHeight / w_blockHeight);
                    _this.css('height', _newHeight);
                });
                w_this.find('#screenshotTarget').css('width', '1240px');
                w_this.find('#screenshotTarget').css('height', '1754px');
                w_this.find('#screenshotTarget .temp_html').css('min-height', '1754px');
                doDownDoc();
                break;
            default:
                break;
        }
    }

    // pdf
    window.doDownPdf = function jspdf_download(){
        //pdf.autoTable({ html: '#my-table' });
        pdf.addFileToVFS("NanumGothic-Regular.ttf", fontNanumGothicRegular);
        pdf.addFont("NanumGothic-Regular.ttf", "NanumGothic-Regular", "normal");
        pdf.setFont("NanumGothic-Regular");

        let title = '통계/분석 보고서';
        let xOffset = (pdf.internal.pageSize.width / 2) - (pdf.getStringUnitWidth(title) * pdf.internal.getFontSize() / 2); 
        const blocks = [];
        let _tick = 0;
        let _y = 0;
        let _timer = 0;
        let pdfImageScale = 0.39;
        let _d_start = w_this.find('input[name="d_start"]').val();
        let _d_end = w_this.find('input[name="d_end"]').val();

        _log(xOffset);
        pdf.text(title, xOffset, 10, 'center');
        pdf.text(_d_start+' ~ '+_d_end, xOffset, 20, 'center');

        _timer = setInterval(async function(){
            _tick++;
            if(_tick < 3){
                _log("tick : !");
                return false;
            }
            clearInterval(_timer);

            // 화면을 블록으로 나누기
            while (_y < document.querySelector('#screenshotTarget').scrollHeight) {
                blocks.push({ y: _y, dataUrl: '', });
                _y += w_blockHeight;
            }

            // 블록을 캡처하고 이미지 파일로 저장하기
            Promise.all(
                blocks.map((block) => {
                    document.querySelector('#screenshotTarget').scrollTo(0, block.y);
                    return html2canvas(document.querySelector('#screenshotTarget'), {
                        scrollY: -block.y,
                        height: w_blockHeight,
                        removeContainer: false,
                        preserveDrawingBuffer: true,
                        willReadFrequently: true
                    }).then((canvas) => {
                        //_log(-block.y, w_blockHeight);
                        block.dataUrl = canvas.toDataURL('image/jpeg', 1.0);
                    });
                })
            ).then(() => {
                //_log(blocks);
                //return false;
                // 모든 블록이 캡처되었을 때, 이미지 파일로 저장하기
                blocks.forEach((block, index) => {
                    if((index+1) > w_maxpage){ return false; }

                    pdf.addPage();
                    pdf.addImage(block.dataUrl, 'jpeg', 25, 25, (pdf.internal.pageSize.getWidth() - 50), (pdf.internal.pageSize.getHeight() - 50));
                    //pdf.addImage(block.dataUrl, 'jpeg', 0, 0, (1240 * pdfImageScale), (w_blockHeight * pdfImageScale));
                });

                let data = pdf.output('blob');
                file_upload(data, w_filename+'.pdf', 'pdf').then((returnData) => {
                    pdf.save(w_filename+'.pdf');
                    toggle_print_layout(w_loading = false);
                });
            });
        }, 1000);
    }

    // ppt
    window.doDownPpt = function() {
        const blocks = [];
        let _y = 0;
        let _slide = [];
        let _pptx = new PptxGenJS();
        let _tick = 0;
        let _timer = 0;
        
        // init
        _pptx.defineLayout({name: 'A4', width: 27.517, height: 19.05});
        _pptx.layout = 'A4';

        _timer = setInterval(async function(){ // canvas 호출 대기
            _tick++;
            if(_tick < 3){
                _log("tick : !");
                return false;
            }
            clearInterval(_timer);

            let _d_start = w_this.find('input[name="d_start"]').val();
            let _d_end = w_this.find('input[name="d_end"]').val();

            // title
            _slide[0] = _pptx.addSlide();
            _slide[0].addText('통계/분석 보고서', { x: 0.0, y: 4.25, w: '100%', h: 8.5, align: 'center', fontSize: 56, color: '0088CC', fill: 'F1F1F1' });
            _slide[0].addText(_d_start+' ~ '+_d_end, { x: 0.0, y: 12.75, w: '100%', h: 4.25, align: 'center', fontSize: 28, color: '000' });

            // 화면을 블록으로 나누기
            while (_y < document.querySelector('#screenshotTarget').scrollHeight) {
                blocks.push({ y: _y, dataUrl: '', });
                _y += w_blockHeight;
            }

            // 블록을 캡처하고 이미지 파일로 저장하기
            Promise.all(
                blocks.map((block) => {
                    document.querySelector('#screenshotTarget').scrollTo(0, block.y);
                    return html2canvas(document.querySelector('#screenshotTarget'), {
                        scrollY: -block.y,
                        height: w_blockHeight,
                        removeContainer: false,
                        preserveDrawingBuffer: true,
                        willReadFrequently: true
                    }).then((canvas) => {
                        //_log(-block.y, w_blockHeight);
                        block.dataUrl = canvas.toDataURL('image/png');
                    });
                })
            ).then(() => {
                //_log(blocks);
                //return false;
                // 모든 블록이 캡처되었을 때, 이미지 파일로 저장하기
                blocks.forEach((block, index) => {
                    if((index+1) > w_maxpage){ return false; }

                    _slide[(index+1)] = _pptx.addSlide();
                    _slide[(index+1)].addImage({data:block.dataUrl, x: 1.75, y: 1.75, w:24.017, h: 15.55}, {align: _pptx.AlignV.middle});
                });

                //
                _pptx.write("blob")
                    .then((data) => {
                        _log("write as blob: \n");
                        _log(data);
                        file_upload(data, w_filename+'.pptx', 'ppt').then((returnData) => {
                            _log('then play');
                            _pptx.writeFile({ fileName: w_filename+'.pptx' });
                            /*
                            const link = document.createElement('a');
                            // create a blobURI pointing to our Blob
                            link.href = URL.createObjectURL(data);
                            link.download = fileName;
                            // some browser needs the anchor to be in the doc
                            document.body.append(link);
                            link.click();
                            link.remove();
                            // in case the Blob uses a lot of memory
                            setTimeout(() => URL.revokeObjectURL(link.href), 7000);
                            */
                            toggle_print_layout(w_loading = false);
                        });
                    })
                    .catch((err) => {
                        console.error(err);
                        toggle_print_layout(w_loading = false);
                    });
                //_log(w_this.find('.temp_html').length, blocks.length);
            }).catch((err) => {
                console.error(err);
                toggle_print_layout(w_loading = false);
            });
        }, 1000);
    }

    // doc
    window.doDownDoc = function generate() {
        const doc = new Document({ sections: [] });
        const blocks = [];
        let _tick = 0;
        let _y = 0;
        let _timer = '';
        let _d_start = w_this.find('input[name="d_start"]').val();
        let _d_end = w_this.find('input[name="d_end"]').val();

        doc.addSection({
            frame: {
                position: {
                    x: 0,
                    y: 0,
                },
                width: 100,
                height: 100,
                alignment: {
                    x: docx.HorizontalPositionAlign.CENTER,
                    y: docx.VerticalPositionAlign.CENTER,
                },
            },
            children: [
                new Paragraph({ 
                    text: "통계/분석 보고서", 
                    heading: docx.HeadingLevel.HEADING_1,
                    alignment: docx.AlignmentType.CENTER,
                }),
                new Paragraph({ 
                    text: _d_start+' ~ '+_d_end, 
                    alignment: docx.AlignmentType.CENTER,
                }),
            ], 
        });

        _timer = setInterval(async function(){
            _tick++;
            if(_tick < 3){
                _log("tick : !");
                return false;
            }
            clearInterval(_timer);

            // 화면을 블록으로 나누기
            while (_y < document.querySelector('#screenshotTarget').scrollHeight) {
                blocks.push({ y: _y, dataUrl: '', });
                _y += w_blockHeight;
            }

            // 블록을 캡처하고 이미지 파일로 저장하기
            Promise.all(
                blocks.map((block) => {
                    document.querySelector('#screenshotTarget').scrollTo(0, block.y);
                    return html2canvas(document.querySelector('#screenshotTarget'), {
                        scrollY: -block.y,
                        height: w_blockHeight,
                        removeContainer: false,
                        preserveDrawingBuffer: true,
                        willReadFrequently: true
                    }).then((canvas) => {
                        //_log(-block.y, w_blockHeight);
                        block.dataUrl = canvas.toDataURL('image/png');
                    });
                })
            ).then(() => {
                // 모든 블록이 캡처되었을 때, 이미지 파일로 저장하기
                blocks.forEach((block, index) => {
                    if((index+1) > w_maxpage){ return false; }

                    doc.addSection({
                        children: [
                            new Paragraph({
                                children: [
                                    new ImageRun({
                                        data: block.dataUrl,
                                        transformation: {
                                            width: 600,
                                            height: 832,
                                        },
                                    }),
                                ],
                            }),
                        ],
                    });
                });
                
                Packer.toBlob(doc).then((blob) => {
                    //_log(blob);
                    file_upload(blob, w_filename+'.docx', 'doc').then((returnData) => {
                        saveAs(blob, w_filename+'.docx');
                        /*
                        saveAs(blob, w_filename+'.docx');
                        _log("Document created successfully");
                        _log(w_this.find('.temp_html').length);
                        */
                        toggle_print_layout(w_loading = false);
                    }).catch((err) => {
                        console.error(err);
                        toggle_print_layout(w_loading = false);
                    });
                });
            }).catch((err) => {
                console.error(err);
                toggle_print_layout(w_loading = false);
            });
        }, 1000);
    }

    function file_upload(_blob, _filename, _type){
        return new Promise((resolve, reject) => {
            let module = 'chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var data = new FormData();
            let _type_arr = {
                'ppt' : 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'pdf' : 'application/pdf',
                'doc' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            };
            
            const fileBlob = new Blob([_blob], { type: _type_arr[_type] });
            if(fileBlob.size > w_maxFileSize){
                alert('파일의 용량이 너무 큽니다. ('+fileBlob.size+' / '+w_maxFileSize+')');
                toggle_print_layout(w_loading = false);
                return false;
            }

            data.append('file', fileBlob, _filename);
            data.append('r', raccount);
            data.append('m', module);
            data.append('a', 'statgpt');
            data.append('linkType', 'file_upload');
            data.append('vendor', vendor);
            data.append('botuid', botuid);

            $.ajax({
                url: rooturl,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                    _log(data);
                    resolve(data);
                },
                error : function(err){
                    alert("파일 업로드를 할 수 없습니다. 잠시 후 다시 시도해 주세요.");
                    w_this.find('.btn_file_download').prop('disabled', false);
                    toggle_print_layout(w_loading = false);
                    reject(err);
                }
            });
        });
    }

    function get_download_filelist(){
        // Promise를 반환하는 AJAX 호출 함수를 정의합니다.
        return new Promise((resolve, reject) => {
            let module = 'chatbot';
            var vendor='<?php echo $V['uid']?>';
            var botuid='<?php echo $botuid?>';
            var extType = '6';
            var d_start = '<?php echo date('YmdHis', strtotime('-7 days', strtotime($date['totime']))) ?>';
            var d_end = '<?php echo $date['totime']; ?>';
            _log([vendor, botuid, extType, d_start, d_end]);

            $.ajax({
                type: "POST",
                url: rooturl,
                dataType:"json",
                data: {
                    "r" : raccount,
                    "m" : module,
                    "a" : "statgpt",
                    linkType : 'get_download_filelist',
                    vendor : vendor,
                    botuid : botuid,
                    exttype : extType,
                    d_start : d_start,
                    d_end : d_end
                },
                success: function(msg){
                    _log(["asdasdasdas :", msg]);
                    resolve(msg);
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    w_this.find('.btn_file_download').prop('disabled', false);
                    toggle_print_layout(w_loading = false);
                    reject(error);
                }
            });
        });
    }

    function formatDateString(dateString, format) {
        const year = dateString.slice(0, 4);
        const month = dateString.slice(4, 6);
        const day = dateString.slice(6, 8);
        const hour = dateString.slice(8, 10);
        const minute = dateString.slice(10, 12);
        const second = dateString.slice(12, 14);
        const date = new Date(year+'/'+month+'/'+day+' '+hour+':'+minute+':'+second);
        if (isNaN(date.getTime())) {
            return 'Invalid date';
        }        
        const fullyear = date.getFullYear();
        const fullmonth = date.getMonth() + 1;
        const fullday = date.getDate();

        const regexResult = format.match(/[.-\/]/);
        const delimiter = regexResult ? regexResult[0] : '-';
        const formatted = format
        .replace(/yyyy/g, fullyear)
        .replace(/yy/g, fullyear.toString().slice(-2))
        .replace(/mm/g, (fullmonth < 10 ? '0' : '') + fullmonth)
        .replace(/m/g, fullmonth)
        .replace(/dd/g, (fullday < 10 ? '0' : '') + fullday)
        .replace(/d/g, fullday);

        return formatted;
    }

    // common
    function generateRandomNumber() {
        let randomNumber = "";
        for (let i = 0; i < 8; i++) {
            randomNumber += Math.floor(Math.random() * 10);
        }
        return randomNumber;
    }

</script>