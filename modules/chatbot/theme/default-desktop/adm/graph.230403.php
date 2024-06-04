<?php
if($bot){
	$Bot = getDbData($table[$m.'bot'],'uid='.$bot,'*');
	$botId = $Bot['id'];
	$roleType = $Bot['role'];
	$shopTemplete = $Bot['induCat'] == 14 ? true : false;
}
if($dialog){
    $R = getDbData($table[$m.'dialog'],'uid='.$dialog,'*');
    //$graphSource = getContents($R['graph'],'TEXT');
    $_data = array('vendor'=>$vendor, 'bot'=>$bot, 'dialog'=>$dialog);
    $dialogNodeJson = $chatbot->getNodeTreeJson($_data);
}

// 스펨방지 코드
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];

$callNode = '대화자상자';
$callIntent = '인텐트'; // html/inputFilter_inputBox  placeholder  부분 별도 수정
$callEntity = '엔터티'; // html/inputFilter_inputBox  placeholder  부분 별도 수정
$callContext = '컨텍스트';
$dialogType = $_GET['type']?$_GET['type']:'default';

// dialog 타입 > 토픽인 경우
if($dialogType=='topic' || $roleType =='topic'){
    $unknownBtnStyle ='style="display:none;"';
}else{
	$unknownBtnStyle = '';
	$startNodeName = 'Welcome';
}

?>
<link href="<?php echo $g['url_root']?>/plugins/jquery-ui/1.9.2/jquery-ui.css" rel="stylesheet">
<script src="<?php echo $g['url_root']?>/plugins/jquery-ui/1.9.2/jquery-ui.min.js"></script>
<link href="<?php echo $g['url_layout']?>/_css/jquery.tagit.css" rel="stylesheet">
<script src="<?php echo $g['url_layout']?>/_js/tag-it.min.js"></script>

<link href="<?php echo $g['url_module_skin']?>/css/perfect-scrollbar.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/dialog.css?<?=date("YmdHi")?>" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/jquery.letterfx.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/atwho.css" rel="stylesheet">
<!-- jsonEditor 리소스 -->
<link href="<?php echo $g['url_module_skin']?>/css/jsoneditor.min.css" rel="stylesheet">
<script src="<?php echo $g['url_module']?>/lib/js/jsoneditor.min.js"></script>
<script src="/_core/js/jquery.mask.min.js"></script>

<div id="dialog-workspace" class="graph-workspace">

	<div id="header">

		<div id="headerTitle">
			<h4 id="title" style=""><?php echo $pageTitle?></h4>
		</div>
		<ul id="headerBtnBox" class="graph-headerBtn" data-role="graph-headerBtn">
		    <?php if($roleType=='bot' && ($type=='default'||!$type)):?>
		    <!--
			<li data-role="change-panelMod" data-type="graph">
				<button type="button" id="btn-addTopic" class="btn btn-default waves-effect" data-tooltip="tooltip" title="토픽추가" >토픽 추가 <i class="fa fa-plus-circle"></i></button>
			</li>
			-->
		    <?php endif?>

			<li data-role="change-panelMod" data-type="intent">
				<button type="button" id="btn-intent" class="btn btn-default waves-effect" data-tooltip="tooltip" title="문장에 포함된 사용자 인텐트" ><?php echo $callIntent?></button>
			</li>
			<li data-role="change-panelMod" data-type="entity">
				<button type="button" id="btn-entity" class="btn btn-default waves-effect" data-tooltip="tooltip" title="인텐트와 관련된 핵심 엔터티" ><?php echo $callEntity?></button>
			</li>
			<li data-role="change-panelMod" data-type="learning">
			    <button type="button" id="btn-learning-intent" class="btn btn-default waves-effect" data-tooltip="tooltip" title="인텐트 학습" >인텐트 학습 <i class="fa fa-share-alt"></i></button>
			</li>
			<li data-role="change-panelMod" data-type="chat">
				<span class="header-btn icon-comment" data-tooltip="tooltip" title="테스트" />
					<!-- background image-->
				</span>
			</li>
		</ul>
		<div id="topicTabs-wrapper" data-role="topicTabs-wrapper">
			<div id="subTopic-wrapper">
                <div id="defaultTopic-wrapper">
					 <ul id="topicTabDeafult-ul" data-role="topicTabsDefault-ul" class="nav nav-tabs topic-tab">
			              <!-- 메인 그래프 tabs 동적 할당 -->
					</ul>
				</div>
				<ul id="topicTab-ul" data-role="topicTabs-ul" class="nav nav-tabs topic-tab">
				</ul>
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
		        <span class="input-group-addon" data-role="btn-cell-search" style="cursor:pointer;"><i class="fa fa-search"></i></span>
            </div>
            <div id="cell_slist" class="cell_slist">
                <ul id="ul_cell_slist"></ul>
            </div>
            <a id="nodeSClose" href="javascript:;" class="fclose fa fa-times-circle" aria-hidden="true"></a>
		</div>

    </div>
    <div id="setNodePanel" data-role="setNodePanel">
		<?php include($g['dir_module_skin'].'adm/_settingPanel.php');?>
	</div>
    <div id="setApiPanel" data-role="setApiPanel">
	    <?php include($g['dir_module_skin'].'adm/_apiPanel.php');?>
	</div>
    <div id="chatbotPanel" data-role="chatbotPanel">
    	<?php include($g['dir_module_skin'].'adm/_chatbotPanel.php');?>
    </div>
    <div id="intentPanel-left" data-role="intentPanel-left" class="intentPanel panel-left">
    	<?php include($g['dir_module_skin'].'adm/_intentLeft.php');?>
    </div>
     <div id="intentPanel-right" data-role="intentPanel-right" class="intentPanel">
    	<?php include($g['dir_module_skin'].'adm/_intentRight.php');?>
    </div>
    <div id="entityPanel-left" data-role="entityPanel-left" class="entityPanel panel-left">
    	<?php include($g['dir_module_skin'].'adm/_entityLeft.php');?>
    </div>
    <div id="entityPanel-right" data-role="entityPanel-right" class="entityPanel">
    	<?php include($g['dir_module_skin'].'adm/_entityRight.php');?>
    </div>
    <div id="recommend-Panel" data-role="recommend-Panel" class="recommendPanel">
    	<?php include($g['dir_module_skin'].'adm/_recommendPanel.php');?>
    </div>
    <div id="testLogPanel" data-role="testLogPanel" class="testLogPanel">
        <?php include($g['dir_module_skin'].'adm/_testLogPanel.php');?>
    </div>
        <div id="addTopicPanel" data-role="addTopicPanel" class="addTopicPanel">
        <?php include($g['dir_module_skin'].'adm/_addTopicPanel.php');?>
    </div>
    <div id="dataSetPanel" data-role="dataSetPanel" class="dataSetPanel">
        <?php include($g['dir_module_skin'].'adm/_dataSetPanel.php');?>
    </div>


</div>

