<?php
    exit;
    
    session_start();
    define('Rb_root',dirname(dirname(__FILE__)).'../../');
    define('Rb_path','../../');
    error_reporting(E_ALL ^ E_NOTICE);
    //error_reporting(E_ERROR);

    // class 인클루드 
    $m ='chatbot';
    $g = array(
        'path_root'   => Rb_path,
        'path_core'   => Rb_path.'_core/',
        'path_var'    => Rb_path.'_var/',
        'path_tmp'    => Rb_path.'_tmp/',
        'path_layout' => Rb_path.'layouts/',
        'path_module' => Rb_path.'modules/',
        'path_widget' => Rb_path.'widgets/',
        'path_switch' => Rb_path.'switches/',
        'path_plugin' => Rb_path.'plugins/',
        'path_page'   => Rb_path.'pages/',
        'path_file'   => Rb_path.'files/'
    );
    $date['totime'] = date('YmdHis');

    function getErrorJSON($bResult, $bResultMsg="", $bResultData="") {
    	$aArray = array("bResult"=>$bResult, "bResultMsg"=>$bResultMsg, "bResultData"=>$bResultData);
    	return json_encode($aArray);
    }
    
    function getDirectorySize($path){
        $nBytesTotal = 0;
        $path = realpath($path);
        if($path != '' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file){
                $nBytesTotal += $file->getSize();
            }
        }
        return $nBytesTotal;
    }
    
    // 변수체크
    $chMode = $_GET['chMode'];
    $dbname = $_GET['dbname'];
    $mbruid = $_GET['mbruid'];
    $vendor = $_GET['vendor'];
    $botid = $_GET['botid'];
    $targetBot = $_GET['targetBot'];
    if(!$chMode || !$dbname || !$mbruid || !$vendor || !$botid) {
        echo ''; exit;
    }
        
    $_SESSION['mbr_db'] = $dbname;
    
    require $g['path_var'].'db.info.php';
    require $g['path_var'].'table.info.php';
    require $g['path_core'].'function/db.mysql.func.php';
    
    $DB_CONNECT = isConnectedToDB($DB);
    
    include $g['path_module'].$m.'/includes/botTemp.class.php';
    
    $BT = new botTemp();
    
    $_data = array();
    $_data['vendor'] = $vendor;
    $_data['mbruid'] = $mbruid;
    $_data['botId'] = $botid;
    
    $lastBot = '';
    
    if ($chMode == 'create_bot') {
        if(!$targetBot) {
            $name = '처음부터 시작하기';
            $avatar = "";
            $induCat = 11;
    		$intro = "";
    		$service = "";
    		$auth = $type = $display = 1;
    		$hidden = $is_temp	= 0;
    		$language = 'KOR';
    		$d_regis = $date['totime'];
    		
    		$mingid = getDbCnt($table[$m.'bot'],'min(gid)','');
    		$gid = $mingid ? $mingid-1 : 1000000000;
    		
    		$QKEY = "is_temp,gid,type,auth,vendor,induCat,hidden,display,name,service,intro,website,boturl,mbruid,id,content,html,tag,lang,hit,likes,report,point,d_regis,d_modify,avatar,upload";
            $QVAL = "'$is_temp','$gid','$type','$auth','$vendor','$induCat','$hidden','$display','$name','$service','$intro','$website','$boturl','$mbruid','$botid','$content','$html','$tag','$language','$hit','$likes','$report','$point','$d_regis','$d_modify','$avatar','$upload'";
            getDbInsert($table[$m.'bot'],$QKEY,$QVAL);
            $lastBot = getDbCnt($table[$m.'bot'],'max(uid)','');
    		
    		// 전체 table column 체크
    		$BT->getSysColsCheck();
    		
    		// system 리소스 업데이트
    		$BT->updateSysResource($_data);
            
        } else {
            
            // 전체 table column 체크
    		$BT->getSysColsCheck();
            
        	$BT->dbTargetMod = 'sys';
            $BT->dbTargetServer = 'syschatbot';
            
        	$_data['targetBot'] = $targetBot;    	
            $lastBot = $BT->copyBot($_data);
        }
        echo $lastBot;
            
    }
?>