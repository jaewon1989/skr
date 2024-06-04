<?php

?>
<script type="text/javascript">
    const bot = '<?php echo $bot?>';
</script>

<link href="<?php echo $g['url_module_skin']?>/css/blackList.css" rel="stylesheet">

<div class="container-fluid">
    <div class="overview">
        <div class="page-title">블랙리스트 설정</div>
        <div class="sub-frame">
            <div class="sub-title">SK telecom AICC / <?php echo $pageTitle ?></div>
        </div>
    </div>
    <div class="blackListDiv">
        <div class="blackListWrapper">
            <button class="blackListSubmitBtn btn btn-pri">저장</button>
            <h4 class="blackListHeader">금지어</h4>
            <span class="blackListSpan">블랙리스트어를 콤마(,)로 구분하여 입력해 주세요.</span>
            <textarea class="blackListTextArea"></textarea>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/blackList.js'?>"></script>
