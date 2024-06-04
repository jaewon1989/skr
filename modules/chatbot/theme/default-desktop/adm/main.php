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

    $botModel = BotModel::of('', $_SESSION['bottype'], $searchField, $searchKeyword, $sortField, 'card');
    $response = $mainController->getMainList($botModel);
?>

<div class="container-fluid dash-botList">
    <header><?php echo $response['option']?></header>
    <div id="botList" class="row" data-role="botList-wrapper"><?php echo $response['list']?></div>
</div>
<!-- 템플릿 추가 모달-->
<div id="modal-addBot" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="">
    <?php echo $response['modal']?>
</div>
<!-- 공통 엘리먼트 -->
<?php echo $response['commonElement']?>

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
