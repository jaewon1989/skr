<?php
if(!$bot){
	getLink('/suAdm/tempList','','템플릿을 선택해주세요.','');
}
if($bot){
	$Bot = getDbData($table[$m.'bot'],'uid='.$bot,'*');
	$botId = $Bot['id'];
}
if($dialog){
    $R = getDbData($table[$m.'dialog'],'uid='.$dialog,'*');
    $graphSource = getContents($R['graph'],'TEXT');
}
// 스펨방지 코드 
if (!$_SESSION['upsescode']) $_SESSION['upsescode'] = str_replace('.','',$g['time_start']);
$sescode = $_SESSION['upsescode'];

$callNode = '대화자상자';
$callIntent = '인텐트'; // html/inputFilter_inputBox  placeholder  부분 별도 수정
$callEntity = '엔터티'; // html/inputFilter_inputBox  placeholder  부분 별도 수정 
$callContext = '컨텍스트';
$dialog.'-'.$bot;

?>

<link href="<?php echo $g['url_module_skin']?>/css/dialog.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/jquery.letterfx.css" rel="stylesheet">
<link href="<?php echo $g['url_module_skin']?>/css/atwho.css" rel="stylesheet">

<div id="dialog-workspace" class="graph-workspace">

	<div id="header">

		<div id="headerTitle">
			<h4 id="title" style="">템플릿 관리 > 대화그래프 제작</h4>
		</div>	
		<ul id="headerBtnBox" class="graph-headerBtn">
			<li data-role="change-panelMod" data-type="graph">
				<button type="button" class="btn btn-primary waves-effect" data-tooltip="tooltip" title="대화상자 편집" >대화그래프</button>
			</li>
			<li data-role="change-panelMod" data-type="intent">
				<button type="button" class="btn btn-default waves-effect" data-tooltip="tooltip" title="문장에 포함된 사용자 인텐트" ><?php echo $callIntent?></button>
			</li>
			<li data-role="change-panelMod" data-type="entity">
				<button type="button" class="btn btn-default waves-effect" data-tooltip="tooltip" title="인텐트와 관련된 핵심 엔터티" ><?php echo $callEntity?></button>
			</li>
			<li data-role="change-panelMod" data-type="data">
				<button type="button" class="btn btn-default waves-effect" data-tooltip="tooltip" title="데이타셋" >데이타셋</button>
			</li>
			<li data-role="change-panelMod" data-type="chat">
				<span class="header-btn icon-comment" data-tooltip="tooltip" title="테스트" />
					<!-- background image-->
				</span>
			</li>
		</ul>	
	</div>
	<div class="graph-box" style="position: relative;margin-top:15px;" data-role="dialogSpace">
		<div id="control-box">
			<div id="control-boxInner" style="position:relative;">
		<!-- 		<span class="control-item" id="save-graph" data-tooltip="tooltip" title="그래프 저장"/>
				    <i class="fa fa-save" aria-hidden="true"></i>
				</span> -->
				<span class="control-item" id="zoomIn" data-tooltip="tooltip" title="확대"/>
				    <i class="fa fa-search-plus" aria-hidden="true"></i>
			    </span>
				<span class="control-item" id="zoomOut" data-tooltip="tooltip" title="축소"/>
				    <i class="fa fa-search-minus" aria-hidden="true"></i>
				</span>
		<!-- 		<span class="control-item" id="Undo" data-tooltip="tooltip" title="취소"/>
				    <i class="fa fa-undo" aria-hidden="true"></i>
				</span> -->

			<!-- 	<span class="control-item" id="source" data-tooltip="tooltip" title="소스"/>
				    <i class="fa fa-code" aria-hidden="true"></i>
				</span> -->
		    </div>
		</div>
		<div id="graphContainer-wrapper">
			<div id="graphContainer">
				<!-- 그래프 출력 -->
        	</div>
		</div>
		    
    </div>
    <div id="setNodePanel" data-role="setNodePanel">
		<?php include($g['dir_module_skin'].'adm/_settingPanel.php');?> 
	</div>	
    <div id="chatbotPanel" data-role="chatbotPanel"> 
    	<?php include($g['dir_module_skin'].'adm/_chatbotPanel.php');?>
    </div>
    <div id="intentPanel-left" data-role="intentPanel-left" class="intentPanel">
    	<?php include($g['dir_module_skin'].'adm/_intentLeft.php');?>
    </div>
     <div id="intentPanel-right" data-role="intentPanel-right" class="intentPanel">
    	<?php include($g['dir_module_skin'].'adm/_intentRight.php');?>
    </div>
    <div id="entityPanel-left" data-role="entityPanel-left" class="entityPanel">
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
    <div id="dataSetPanel" data-role="dataSetPanel" class="dataSetPanel">
        <?php include($g['dir_module_skin'].'adm/_dataSetPanel.php');?> 
    </div>

</div>
