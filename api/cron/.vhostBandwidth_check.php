<?
    //error_reporting(0);
    // 35de3e6b6f15b608c2e558c25bc4b662
    /*
    if (count($argv) < 2 || $argv[1] != md5("botbandwidth")) {
        exit;
    }
    */
    $chConfDir = "/data/chatbot";
    include_once $chConfDir.'/bottalksConf.php';
    
    class CMysql {
        var $m_pConnection;
        
        public function __construct() {
            global $g, $_db_traffic;
            
            $chDB_Host = $_db_traffic['host'];
            $chDB_ID = $_db_traffic['user'];
            $chDB_PW = $_db_traffic['pass'];
            $chDB_Port = $_db_traffic['port'];
            $chDB_Name = $_db_traffic['name'];
            
            $this->m_chDBName = $chDB_Name;
            
            if (!$this->m_pConnection) {
                $this->m_pConnection = mysqli_connect($chDB_Host, $chDB_ID, $chDB_PW, $chDB_Name, $chDB_Port) or die( "MySQL server에 연결할 수 없습니다(".mysqli_connect_errno().")");
                mysqli_query($this->m_pConnection, "set names utf8");
            }
        }
        
        public function fnClose() {
            mysqli_close($this->m_pConnection);
        }
    }
    
    function setRecord($chQuery, $oConnection="") {
        global $pConnection;
        $pConnectionDB = $oConnection ? $oConnection : $pConnection;
        $aVar = array();
        if (!is_object($pConnectionDB)) return $aVar;
        
        $pResult = mysqli_query($pConnectionDB, $chQuery);
        if ($pResult) {
            while($pRow = mysqli_fetch_assoc($pResult)){
                $aVar[] = $pRow;
            }
            mysqli_free_result($pResult);
        }
        return $aVar;
    }
    
    function setRecordOne($chQuery, $oConnection="") {
        global $pConnection;
        $pConnectionDB = $oConnection ? $oConnection : $pConnection;
        $aVar = array();
        if (!is_object($pConnectionDB)) return $aVar;
        
        $pResult = mysqli_query($pConnectionDB, $chQuery);
        if ($pResult) {
            $pRow = mysqli_fetch_assoc($pResult);
        }
        return $pRow === null ? $aVar : $pRow;
    }
    
    function setSQLQuery($chQuery, $oConnection="") {
        global $pConnection;
        $pConnectionDB = $oConnection ? $oConnection : $pConnection;
        if (!is_object($pConnectionDB)) return 0;
        
        $bResult = mysqli_query($pConnectionDB, $chQuery);
        if ($bResult) {
            return preg_match("/^insert/i", $chQuery) ? mysqli_insert_id($pConnectionDB) : true;
        } else {
            return mysqli_error($pConnectionDB);
        }
    }
    
    $chShellFile = "/data/.vhostBandwidth.sh";
    $webServerID = 1;
    $chTableTraffic = "rb_service_traffic";
    $chTableSummary = $chTableTraffic."_summary";
    
    if (file_exists($chShellFile)) {
        exec($chShellFile, $aOutput, $pResult);
        
        if (count($aOutput) > 0 && $pResult == 0) {
            //----- DB 접속 -----//
            $CMysql = new CMysql;
            $pConnection = $CMysql->m_pConnection;
            
            $aTraffic = array();
            foreach($aOutput as $aTemp) {
                //echo $aTemp."\n";
                if (!trim($aTemp)) continue;
                
                $aTemp = explode(",", $aTemp);
                if (!$aTemp[0] && !$aTemp[1]) continue;
                
                $webid = $webServerID;
                $d_regis = trim($aTemp[0]);
                $botid = trim($aTemp[1]);
                $tin = trim($aTemp[2]) ? (int)trim($aTemp[2]) : 0;
                $tout = trim($aTemp[3]) ? (int)trim($aTemp[3]) : 0;
                
                // 첫번째 트래픽일 경우
                $aResult = setRecordOne("Select count(*) as nCnt From $chTableTraffic Where webid='".$webid."' and botid='".$botid."'");
                $nCnt = $aResult['nCnt'];
                if ($nCnt == 0) {
                    $tin_diff = $tin;
                    $tout_diff = $tout;
                    
                    // 트래픽 입력
                    $chQuery = "Insert into $chTableTraffic ( ";
                    $chQuery .="	category, webid, botid, tin, tout, tin_diff, tout_diff, d_regis ";
                    $chQuery .=") values ( ";
                    $chQuery .="	'chatbot', '".$webid."', '".$botid."', '".$tin."', '".$tout."', '".$tin_diff."', '".$tout_diff."', '".$d_regis."' ";
                    $chQuery .=")";
                    setSQLQuery($chQuery);
                } else {
                
                    // 자정일 경우 일 트래픽 입력
                    if (strpos($d_regis, "00:00:00") !== false) {
                    
                        // 7일전 데이터 삭제
                        if ($i == 0) {
                            setSQLQuery("Delete From $chTableTraffic Where category='chatbot' and date(d_regis) <= (curdate()-interval 7 day) ");
                        }
                        
                        // 전날 구하기
                        $dDate = substr($d_regis,0,10);
                        $aDate = explode("-", $dDate);
                        $dPrevDate = date("Y-m-d", mktime(0,0,0,$aDate[1], ($aDate[2]-1), $aDate[0]));
                        
                        // 전날의 마지막 데이터
                        $aPrevDayTraffic = setRecordOne("Select A.tin, A.tout From $chTableTraffic A Where A.uid = (Select max(uid) From $chTableTraffic Where webid='".$webid."' and botid='".$botid."' and d_regis like '".$dPrevDate."%')");
                        
                        $tin_diff = $tin && $aPrevDayTraffic['tin'] ? ($tin-$aPrevDayTraffic['tin']) : 0;
                        $tout_diff = $tout && $aPrevDayTraffic['tout'] ? ($tout-$aPrevDayTraffic['tout']) : 0;
                        
                        // 트래픽 입력
                        $chQuery = "Insert into $chTableTraffic ( ";
                        $chQuery .="	category, webid, botid, tin, tout, tin_diff, tout_diff, d_regis ";
                        $chQuery .=") values ( ";
                        $chQuery .="	'chatbot', '".$webid."', '".$botid."', '".$tin."', '".$tout."', '".$tin_diff."', '".$tout_diff."', '".$dPrevDate." 23:59:59' ";
                        $chQuery .=")";
                        setSQLQuery($chQuery);
                        
                        //---- 당일 트래픽 결산(WEB 별도) -------//
                        $aLastData = setRecordOne("Select sum(tin_diff) as tin, sum(tout_diff) as tout From $chTableTraffic Where webid='".$webid."' and botid='".$botid."' and d_regis like '".$dPrevDate."%' Group by botid ");
                        $tin_sum = $aLastData['tin'] ? $aLastData['tin'] : 0;
                        $tout_sum = $aLastData['tout'] ? $aLastData['tout'] : 0;
                        
                        $chQuery = "Insert into $chTableSummary ( ";
                        $chQuery .="	category, webid, botid, tin, tout, d_regis ";
                        $chQuery .=") values ( ";
                        $chQuery .="	'chatbot', '".$webid."', '".$botid."', '".$tin_sum."', '".$tout_sum."', '".$dPrevDate." 23:59:59' ";
                        $chQuery .=")";
                        setSQLQuery($chQuery);
                        
                    } else {
                    
                        // 당일의 첫번째 트래픽(0보다 큰)일 경우
                        $aMax = setRecordOne("Select max(tin) as tin, max(tout) as tout From $chTableTraffic Where webid='".$webid."' and botid='".$botid."' and d_regis like '".substr($d_regis,0,10)."%' ");
                        if (!$aMax['tin'] && !$aMax['tout']) {
                            $tin_diff = $tin;
                            $tout_diff = $tout;
                        } else {
                            $aPrev = setRecordOne("Select tin, tout From $chTableTraffic Where uid = (Select max(uid) From $chTableTraffic Where webid='".$webid."' and botid='".$botid."' and d_regis like '".substr($d_regis,0,10)."%') ");
                            $tin_diff = $aPrev['tin'] ? ($tin-$aPrev['tin']) : 0;
                            $tout_diff = $aPrev['tout'] ? ($tout-$aPrev['tout']) : 0;
                        }
                        
                        // 트래픽 입력
                        $chQuery = "Insert into $chTableTraffic ( ";
                        $chQuery .="	category, webid, botid, tin, tout, tin_diff, tout_diff, d_regis ";
                        $chQuery .=") values ( ";
                        $chQuery .="	'chatbot', '".$webid."', '".$botid."', '".$tin."', '".$tout."', '".$tin_diff."', '".$tout_diff."', '".$d_regis."' ";
                        $chQuery .=")";
                        setSQLQuery($chQuery);
                    }
                }
            }
            
            //----- DB 종료 -----//
            $CMysql->fnClose();
        }
    }
    exit;
?>
