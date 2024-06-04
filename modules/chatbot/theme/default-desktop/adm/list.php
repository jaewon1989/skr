<?php
    require_once 'main/controller/MainController.php';
    // DI
    $mainController = new MainController();

    $copyRemoteServers = array();
    $copyRemoteServers['sys-chatbot'] = array('need_db'=>false);
    $copyRemoteServers['cloud-chatbot'] = array('need_db'=>true);
    $copyRemoteServers['cv1-chatbot'] = array('need_db'=>false);

    $searchField = isset($_GET['searchField']) ? $_GET['searchField'] : 'name';
    $searchKeyword = $_GET['searchKeyword'];
    $sortField = isset($_GET['sortField']) ? $_GET['sortField'] : 'd_regis';

    $botModel = BotModel::of('', $_SESSION['bottype'], $searchField, $searchKeyword, $sortField, 'list');
    $response = $mainController->getMainList($botModel);
?>
<div class="container-fluid dash-botList">
    <header><?php echo $response['option']?></header>
    <div id="chatbot-list" class="row">
        <div class="header-title back-th thumbnail chatbot" style="text-align:center;padding-top: 5px;height: 50px;">
            <div class="chatbot-avatar d-absolute ml-15 th-left" style="line-height: 39px;">아이콘</div>
            <div class="col-md-12 col-sm-12 col-xs-12">봇명</div>
            <div class="col-md-12 col-sm-12 col-xs-12">서비스명</div>
            <div class="col-md-12 col-sm-12 col-xs-12">누적 접속 수</div>
            <div class="col-md-12 col-sm-12 col-xs-12">월간 접속 수</div>
            <div class="mt-30 th-right">등록일</div>
        </div>
        <?php echo $response['list']?>
    </div>
</div>
<!-- 템플릿 추가 모달-->
<div id="modal-addBot" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <?php echo $response['modal']?>
</div>
<!-- 공통 엘리먼트 -->
<?php echo $response['commonElement']?>
<style>

    #chatbot-list .header-title {height: 40px; line-height: 40px;}

    .back-th {
        background-color: #f4f4f4;
    }
    .th-left {border-top-left-radius: 10px; border-bottom-left-radius: 10px;}
    .th-right {border-top-right-radius: 10px; border-bottom-right-radius: 10px;}

    p {
        line-height: 2.0;
    }
    .col-md-12 {width: 20%;}
    /* 썸네일 */
    .thumbnail {border-color: #e4e4e4;}
    .thumbnail.chatbot {padding: 28px;}
    .thumbnail.chatbot .chatbot-avatar {display: block; width: 45px; height:45px; margin-right: 5px; float: left; line-height: 50px; overflow: hidden;background-repeat: no-repeat;background-position: center center;background-size: cover; vertical-align: middle; border-radius: 30px;}
    .thumbnail.chatbot .chatbot-info .chatbot-name span {color: #ff0000;}
</style>

<!--스크립트 내에서 사용할 php 변수 선언-->
<script type="text/javascript">
    let vendor = $('input[name="vendor_value"]').val(),
        module = $('input[name="module_value"]').val(),
        botType = $('input[name="botType_value"]').val(),
        mySuper = $.parseJSON($('input[name="isSuper"]').val()),
        myManager = $.parseJSON($('input[name="isManager"]').val()),
        copyRemoteServers = <?php echo json_encode($copyRemoteServers);?>,
        searchFieldValue = "<?php echo $searchField;?>",
        sortFieldValue = "<?php echo $sortField;?>";
</script>
<!--공통 스크립트 호출-->
<script src="/view/main/js/main.js" type="text/javascript"></script>

