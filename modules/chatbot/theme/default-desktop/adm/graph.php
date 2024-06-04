<?php
if ($bot) {
    $Bot = getDbData($table[$m . 'bot'], 'uid=' . $bot, '*');
    $botId = $Bot['id'];
    $roleType = $Bot['role'];
    $shopTemplete = $Bot['induCat'] == 14 ? true : false;
}
// 스펨방지 코드
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.', '', $g['time_start']);
$sescode = $_SESSION['upsescode'];

$callNode = '대화자상자';
$callIntent = '인텐트'; // html/inputFilter_inputBox  placeholder  부분 별도 수정
$callEntity = '엔터티'; // html/inputFilter_inputBox  placeholder  부분 별도 수정
$callContext = '컨텍스트';
$dialogType = $_GET['type'] ? $_GET['type'] : 'default';

// dialog 타입 > 토픽인 경우
if ($dialogType == 'topic' || $roleType == 'topic') {
    $unknownBtnStyle = 'style="display:none;"';
} else {
    $unknownBtnStyle = '';
    $startNodeName = 'Welcome';
}

?>
<link href="<?php echo $g['url_root'] ?>/plugins/jquery-ui/1.9.2/jquery-ui.css" rel="stylesheet">
<script src="<?php echo $g['url_root'] ?>/plugins/jquery-ui/1.9.2/jquery-ui.min.js"></script>
<link href="<?php echo $g['url_layout'] ?>/_css/jquery.tagit.css" rel="stylesheet">
<script src="<?php echo $g['url_layout'] ?>/_js/tag-it.min.js"></script>
<link href="/_core/css/powerange.min.css" rel="stylesheet">
<script src="/_core/js/powerange.min.js"></script>

<link href="<?php echo $g['url_module_skin'] ?>/css/perfect-scrollbar.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin'] ?>/css/dialog.css?<?= date("YmdHi") ?>" rel="stylesheet">
<link href="<?php echo $g['url_module_skin'] ?>/css/jquery.letterfx.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin'] ?>/css/atwho.css" rel="stylesheet">
<!-- jsonEditor 리소스 -->
<link href="<?php echo $g['url_module_skin'] ?>/css/jsoneditor.min.css" rel="stylesheet">
<script src="<?php echo $g['url_module'] ?>/lib/js/jsoneditor.min.js"></script>
<script src="/_core/js/jquery.mask.min.js"></script>

<div id="dialog-workspace" class="graph-workspace">

    <div id="header">
        <!--
		<div id="headerTitle">
			<h4 id="title" style=""><?php echo $pageTitle ?></h4>
		</div>
        -->
        <div class="overview">
            <div class="page-title">대화그래프</div>
            <div class="sub-frame">
                <div class="sub-title">SK telecom AICC / <?php echo $pageTitle ?></div>
            </div>
        </div>
        <ul id="headerBtnBox" class="graph-headerBtn" data-role="graph-headerBtn">
            <li style="display: inline-flex; align-items: center">
                <span style="margin-right: 10px">버전관리</span>
                <select class="form-control" id="dialogSelectList" style="height: 34px !important;">
                </select>
                <button type="button" id="btn-deleteVersion" class="btn btn-default waves-effect" data-tooltip="tooltip"
                        style="margin-left: 5px; vertical-align: top">삭제
                </button>
                <button type="button" id="btn-loadVersion" class="btn btn-default waves-effect" data-tooltip="tooltip"
                        style="margin-left: 5px; vertical-align: top">불러오기
                </button>
            </li>
            <li data-role="change-panelMod" data-type="regVersion"
                style="padding-left: 10px; border-left: 1px solid black">
                <button type="button" id="btn-regVersion" class="btn btn-default waves-effect" data-tooltip="tooltip"
                        title="그래프 신규 등록">버전등록
                </button>
            </li>
            <li data-role="change-panelMod" data-type="copyVersion">
                <button type="button" id="btn-copyVersion" class="btn btn-default waves-effect" data-tooltip="tooltip"
                        title="그래프 복사">버전복사
                </button>
            </li>
            <li data-role="change-panelMod" data-type="specifyVersion">
                <button type="button" id="btn-specifyVersion" class="btn btn-default waves-effect"
                        data-tooltip="tooltip" title="운영버전 지정">운영버전 지정
                </button>
            </li>
        </ul>
        <ul id="headerBtnBox" class="graph-headerBtn" data-role="graph-headerBtn" style="top: 100px;">
            <li data-role="change-panelMod" data-type="intent">
                <button type="button" id="btn-intent" class="btn btn-default waves-effect" data-tooltip="tooltip"
                        title="문장에 포함된 사용자 인텐트"><?php echo $callIntent ?></button>
            </li>
            <li data-role="change-panelMod" data-type="entity">
                <button type="button" id="btn-entity" class="btn btn-default waves-effect" data-tooltip="tooltip"
                        title="인텐트와 관련된 핵심 엔터티"><?php echo $callEntity ?></button>
            </li>
            <li data-role="change-panelMod" data-type="learning">
                <button type="button" id="btn-learning-intent" class="btn btn-default waves-effect"
                        data-tooltip="tooltip" title="인텐트 학습">인텐트 학습 <i class="fa fa-share-alt"></i></button>
            </li>
            <li data-role="change-panelMod" data-type="chat">
                <span class="header-btn icon-comment" data-tooltip="tooltip" title="테스트"/>
                <!-- background image-->
                </span>
            </li>
        </ul>
        <div id="modal-wrapper" style="display: none"></div>
        <div id="topicTabs-wrapper" data-role="topicTabs-wrapper">
            <div id="subTopic-wrapper">
                <div id="defaultTopic-wrapper" style="border-bottom: none;">
                    <ul id="topicTabDeafult-ul" data-role="topicTabsDefault-ul" class="nav nav-tabs topic-tab"
                        style="border-bottom: none;">
                        <!-- 메인 그래프 tabs 동적 할당 -->
                    </ul>
                </div>
                <!--서브 그래프 tabs 동적 할당 사용안함처리-->
                <!--				<ul id="topicTab-ul" data-role="topicTabs-ul" class="nav nav-tabs topic-tab">-->
                <!--				</ul>-->
            </div>
        </div>
    </div>
    <div class="graph-box" style="position: relative;" data-role="dialogSpace">
        <div id="control-box">
            <div id="control-boxInner" style="position:relative;">
                <span class="control-item" id="zoomIn" data-tooltip="tooltip" title="확대"/>
                <i class="fa fa-search-plus" aria-hidden="true"></i>
                </span>
                <span class="control-item" id="zoomOut" data-tooltip="tooltip" title="축소"/>
                <i class="fa fa-search-minus" aria-hidden="true"></i>
                </span>
                <span class="control-item" id="zoomSearch" data-tooltip="tooltip" title="검색"/>
                <i class="fa fa-search" aria-hidden="true"></i>
                </span>
                <span class="control-item" id="zoomCenter" data-tooltip="tooltip" title="가운데 이동"/>
                <i class="fa fa-align-center" aria-hidden="true"></i>
                </span>
                <span class="control-item" id="zoomExcel" data-tooltip="tooltip" title="다운로드"/>
                <i class="fa fa-download" aria-hidden="true"></i>
                </span>
            </div>
        </div>
        <div id="graphContainer-wrapper">
            <div id="graphContainer">
                <!-- 그래프 출력 -->
            </div>
        </div>

        <div id="graphSearch" class="graphSearch">
            <div class="input-group">
                <input type="text" class="form-control input_search" data-role="cell-search">
                <span class="input-group-addon" data-role="btn-cell-search" style="cursor:pointer;">
                    <i class="fa fa-search"></i></span>
            </div>
            <div id="cell_slist" class="cell_slist">
                <ul id="ul_cell_slist"></ul>
            </div>
            <div>
                <button id="nodeSClose" class="btn btn-default" aria-hidden="true" style="margin-top: 5px;">닫기</button>
            </div>
        </div>

    </div>
    <div id="configPanel" data-role="configPanel">
        <?php include($g['dir_module_skin'] . 'adm/_configPanel.php'); ?>
    </div>
    <div id="setNodePanel" data-role="setNodePanel">
        <?php include($g['dir_module_skin'] . 'adm/_settingPanel.php'); ?>
    </div>
    <div id="setApiPanel" data-role="setApiPanel">
        <?php include($g['dir_module_skin'] . 'adm/_apiPanel.php'); ?>
    </div>
    <div id="chatbotPanel" data-role="chatbotPanel">
        <?php include($g['dir_module_skin'] . 'adm/_chatbotPanel.php'); ?>
    </div>
    <div id="intentPanel-left" data-role="intentPanel-left" class="intentPanel panel-left">
        <?php include($g['dir_module_skin'] . 'adm/_intentLeft.php'); ?>
    </div>
    <div id="intentPanel-right" data-role="intentPanel-right" class="intentPanel">
        <?php include($g['dir_module_skin'] . 'adm/_intentRight.php'); ?>
    </div>
    <div id="entityPanel-left" data-role="entityPanel-left" class="entityPanel panel-left">
        <?php include($g['dir_module_skin'] . 'adm/_entityLeft.php'); ?>
    </div>
    <div id="entityPanel-right" data-role="entityPanel-right" class="entityPanel">
        <?php include($g['dir_module_skin'] . 'adm/_entityRight.php'); ?>
    </div>
    <div id="recommend-Panel" data-role="recommend-Panel" class="recommendPanel">
        <?php include($g['dir_module_skin'] . 'adm/_recommendPanel.php'); ?>
    </div>
    <div id="testLogPanel" data-role="testLogPanel" class="testLogPanel">
        <?php include($g['dir_module_skin'] . 'adm/_testLogPanel.php'); ?>
    </div>
    <div id="addTopicPanel" data-role="addTopicPanel" class="addTopicPanel">
        <?php include($g['dir_module_skin'] . 'adm/_addTopicPanel.php'); ?>
    </div>
    <div id="dataSetPanel" data-role="dataSetPanel" class="dataSetPanel">
        <?php include($g['dir_module_skin'] . 'adm/_dataSetPanel.php'); ?>
    </div>


</div>

<script type="text/javascript">
let newVersionName = '';

// dialogList 조회 및 select 태그 내에 option 생성하는 함수
function getDialogList() {

    $.post(dialogPath, {
        mode: 'getDialogList',
        botUid: bot
    }, function(response) {
        const res = JSON.parse(response);

        if (!res.error) {
            $('#dialogSelectList').empty().append($('<option>').val('').text('선택'));

            res.result.map(function(item) {
                const name = item.name,
                    dialogUid = item.uid;

                return $('<option>').val(dialogUid).text(name);
            }).forEach(function(option) {
                $('#dialogSelectList').append(option);
            });
        }
    });
}

// 모달 닫기 이벤트 함수
function closeDialogModal(event) {
    const target = event ? event.target.id : 'modal-close',
        headerBtn = $('[data-role="graph-headerBtn"]');

    if ('modal-wrapper' === target || 'modal-close' === target) {
        $('#modal-wrapper').hide().empty();
        headerBtn.find('button').removeClass('btn-primary').addClass('btn-default');
        headerBtn.find('span.icon-comment').removeClass('active');
        $('#selectedDialogVal').val('');
        newVersionName = '';
    }
}

// 모달 닫기 이벤트 발생
$('#modal-wrapper, #modal-close').on('click', closeDialogModal);

// 모달 내 select 태그 옵션 선택 이벤트
$(document).on('change', '#modal-select', function() {
    $('#selectedDialogVal').val($(this).val());
});

// copyDialog 새로운 버전명 입력값 이벤트
$(document).on('input', '#input-copy-name', function() {
    newVersionName = $(this).val();
});

// 목록버전 불러오기 btn 클릭 이벤트
$(document).on('click', '#btn-loadVersion', function() {
    const dialogVal = $('#dialogSelectList').val();

    if (!dialogVal) {
        alert('불러올 버전을 선택하세요.');
        return;
    }
    if (dialog === dialogVal) {
        alert('현재 불러온 그래프입니다.');
        return;
    }

    window.location.href = '/adm/graph?dialog=' + dialogVal;
});

let isHandlingAction = false;

// 모달 post 요청 함수
function handleModalAction(mode, dialogVal) {
    const dialogKey = ('createDialog' === mode || 'chkDupDialogName' === mode) ? 'dialogName' : 'dialogUid',
        alertInfo = {
            'createDialog': '등록되었습니다.',
            'deleteDialog': '삭제되었습니다.',
            'copyDialog': '복사되었습니다.',
            'updateActiveDialog': '운영버전이 변경되었습니다.',
            'chkDupDialogName': '중복되는 버전명입니다.'
        };
    let postData =
        {
            mode: mode,
            botUid: bot,
            [dialogKey]: dialogVal
        };

    if (newVersionName) {
        postData.dialogName = newVersionName;
    }

    // 함수가 실행중일때 버튼을 클릭하면 추가 post 요청 막기 위함
    if (isHandlingAction) {
        return;
    }

    isHandlingAction = true;
    $.post(dialogPath, postData, function(response) {
        isHandlingAction = false;

        if (response.error) {
            alert('문제가 발생했습니다.');
        } else {
            if ('chkDupDialogName' === mode) {
                if ('true' === response.result) {
                    alert(alertInfo[mode]);
                    $('button[name="btn-dialog-reg"]').prop('disabled', true);
                    $('button[name="btn-dialog-save"]').prop('disabled', true);
                } else {
                    $('button[name="btn-dialog-reg"]').prop('disabled', false);
                    $('button[name="btn-dialog-save"]').prop('disabled', false);
                }
                return;
            }

            alert(alertInfo[mode]);

            if ('updateActiveDialog' === mode) {
                window.location.href = '/adm/graph';
            } else {
                closeDialogModal();
            }
        }

        getDialogList(); // dialogList 최신화
    }, 'json');
}

function chkDupDialogName(dialogName) {
    if ('' !== dialogName) {
        handleModalAction('chkDupDialogName', dialogName);
    }
}

$(document).on('focusout', '#input-dialog-reg', function() {
    chkDupDialogName($(this).val());
});

$(document).on('focusout', '#input-copy-name', function() {
    chkDupDialogName($(this).val());
});

// 등록 btn 클릭 이벤트
$(document).on('click', 'button[name="btn-dialog-reg"]', function() {
    const dialogVal = $('#input-dialog-reg').val();

    if ('' === dialogVal) {
        alert('버전명을 입력하세요.');
        return;
    }

    handleModalAction('createDialog', dialogVal);
});

// 삭제 btn 클릭 이벤트
$(document).on('click', '#btn-deleteVersion', function() {
    const dialogVal = $('#dialogSelectList').val(),
        selectedDialogText = $(this).closest('#headerBtnBox').find('select option:selected').text();
    let isRealDeleteMsg = '';

    if (!dialogVal) {
        alert('삭제할 버전을 선택하세요.');
        return;
    }

    if (selectedDialogText.includes('(운영버전)')) {
        alert('운영중인 버전은 삭제할 수 없습니다.');
        return;
    }

    if (selectedDialogText.includes('(X)')) {
        isRealDeleteMsg = '완전히 ';
    }

    if (confirm(selectedDialogText + ' 버전을 ' + isRealDeleteMsg + '삭제하시겠습니까?')) {
        if (dialog === dialogVal) {
            handleModalAction('deleteDialog', dialogVal);
            window.location.href = '/adm/graph';
        } else {
            handleModalAction('deleteDialog', dialogVal);
        }
    }
});

// 저장 btn 클릭 이벤트 - mode 가 updateActiveDialog 와 copyDialog 일 때 발생
$(document).on('click', 'button[name="btn-dialog-save"]', function() {
    const mode = $('#mode').val(),
        selectedDialogVal = $(this).closest('.versionModal').find('select').val(),
        selectedDialogText = $(this).closest('.versionModal').find('select option:selected').text();

    if (!selectedDialogVal) {
        alert('버전을 선택해주세요.');
        return;
    }

    if ('copyDialog' === mode && !newVersionName) {
        alert('버전명을 입력하세요');
        return;
    }

    if ('updateActiveDialog' === mode && selectedDialogText.includes('(운영버전)')) {
        alert('이미 운영중인 버전입니다.');
        return;
    }

    // 운영버전 지정시 실행되는 조건문
    if ('updateActiveDialog' === mode) {
        if (confirm('운영버전을 변경하시겠습니까?')) {
            handleModalAction(mode, selectedDialogVal);
        }
        return;
    }

    // 버전복사시 실행되는 함수
    handleModalAction(mode, selectedDialogVal);
});

</script>
