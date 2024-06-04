<?php
    // 2ae7b04aeb5feb38a42bf32bf46503a5
    /*
    if (count($argv) < 2 || $argv[1] != md5("shop_refresh_token")) {
        exit;
    }
    */

    $chConfDir = "/data/chatbot";
    include_once $chConfDir.'/bottalksConf.php';

    class CMysql {
        var $m_pConnection;

        public function __construct() {
            global $g, $_db_bot;

            $chDB_Host = $_db_bot['host'];
            $chDB_ID = $_db_bot['user'];
            $chDB_PW = $_db_bot['pass'];

            if (!$this->m_pConnection) {
                $this->m_pConnection = mysqli_connect($chDB_Host, $chDB_ID, $chDB_PW) or die( "MySQL server에 연결할 수 없습니다(".mysqli_connect_errno().")");
                mysqli_query($this->m_pConnection, "set names utf8");
            }
        }

        public function fnClose() {
            if (is_resource($this->m_pConnection)) mysqli_close($this->m_pConnection);
        }
    }

    $CMysql = new CMysql;
    $pConnection = $CMysql->m_pConnection;

    // cafe24 oAuth Key
    $client_id = $_cafe24_client_id;
    $client_secret = $_cafe24_client_secret;
    $nowDate = date('Y-m-d');

    // All DB Name
    $chQuery = "Select schema_name From information_schema.schemata Where schema_name like 'bot_user%' ";
    $pResult = mysqli_query($pConnection, $chQuery);

    $aDBName = array();
    while($pRow = mysqli_fetch_assoc($pResult)) {
        $aDBName[] = $pRow['SCHEMA_NAME'];
    }
    mysqli_free_result($pResult);

    foreach($aDBName as $chDBName) {
        // Get Shopping Template Bot
        $chQuery = "Select uid From ".$chDBName.".rb_chatbot_bot Where induCat = 14 ";
        $pResult = mysqli_query($pConnection, $chQuery);
        while($pRow = mysqli_fetch_assoc($pResult)) {
            $bot = $pRow['uid'];

            $chQuery = "Select name, value From ".$chDBName.".rb_chatbot_botSettings Where bot = ".$bot." and (name = 'use_shopapi' or name like 'shopapi_%') Order by uid ASC";
            $pResultAPI = mysqli_query($pConnection, $chQuery);

            $aAPI = array();
            while($pRowAPI = mysqli_fetch_assoc($pResultAPI)) {
                $aAPI[$pRowAPI['name']] = trim($pRowAPI['value']);
            }

            // shop api 사용중이고 mallid, 리프레쉬 토큰 정보 있을 경우 갱신
            if($aAPI['shopapi_vendor'] != 'cafe24') continue;
            if($aAPI['use_shopapi'] != "on" || !$aAPI['shopapi_mall_id'] || !$aAPI['shopapi_refresh_token'] || !$aAPI['shopapi_refresh_token_expire']) continue;

            $mall_id = $aAPI['shopapi_mall_id'];
            $refresh_token = $aAPI['shopapi_refresh_token'];
            $refresh_expire = date('Y-m-d', strtotime($aAPI['shopapi_refresh_token_expire']));
            $date1 = new DateTime($nowDate);
            $date2 = new DateTime($refresh_expire);
            $interval = $date1->diff($date2);
            $dRemain = (int)$interval->format('%R%a');

            // 리프레쉬 토큰 만료일 3일 이상 남은 경우, 즉 2일 이내인 경우 갱신
            if($dRemain >= 3) continue;

            //리프레쉬 토큰 갱신
            $apiURL = "https://".$mall_id.".cafe24api.com/api/v2/oauth/token";
            $headers = array(
                'Authorization: Basic '.base64_encode($client_id.':'.$client_secret),
                'Content-Type: application/x-www-form-urlencoded'
            );

            $aPostVal = array();
            $aPostVal['grant_type'] = "refresh_token";
            $aPostVal['refresh_token'] = $refresh_token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aPostVal));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);

            // 갱신 결과 정상일 경우만 업데이트
            $resultToken = json_decode($result);

            if($resultToken->access_token && $resultToken->refresh_token) {
                $aToken = array();
                $aToken['shopapi_access_token'] = $resultToken->access_token;
                $aToken['shopapi_access_token_expire'] = $resultToken->expires_at;
                $aToken['shopapi_refresh_token'] = $resultToken->refresh_token;
                $aToken['shopapi_refresh_token_expire'] = $resultToken->refresh_token_expires_at;

                foreach($aToken as $key=>$value) {
                    $chQuery = "Update ".$chDBName.".rb_chatbot_botSettings Set value = '".$value."' Where bot = ".$bot." and name = '".$key."' ";
                    mysqli_query($pConnection, $chQuery);
                }
            }

            sleep(2);
        }
        mysqli_free_result($pResult);
    }

    $CMysql->fnClose();
    exit;
?>


