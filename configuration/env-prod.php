<?php
session_start();

// host 도메인 관련
$_cloud_ = false;

$develop = 0;
$g['root_host'] = 'sktaicc.com';
//$g['front_host'] = $develop == 1 ? 'http://dev.bottalks.co.kr' : 'https://www.bottalks.co.kr';
$g['front_host'] = '/adm';

$g['chatbot_host'] = 'ccaas.sktaicc.com';

$g['cti_api_host'] = 'http://172.27.60.24:5000/doota_call_bot'; //TODO 여기 어떤 장비인지와 IP / DNS 를 확인해야 한다.

// 응답 카운트 집계용 별도 DB 여부
$g['sys_rcount'] = $_cloud_ ? true : false;

// 콜봇 응답 카운트 기준
$g['sys_callsec'] = 10; // 기준 초
$g['sys_callcount'] = 2; // 응답 카운트 수

// websocket
$g['web_socket_host'] = "http://61.250.38.141";                  // BPP-AICCB-PSWEB01 + BPP-AICCB-PSWEB02 의 VIP
//$g['web_socket_host'] = "https://chat-ccaas.sktaicc.com";     //  BPP-AICCB-PSWEB01 + BPP-AICCB-PSWEB02 의 DNS
$g['web_socket_port'] = 3000;
$g['call_socket_host'] = "127.0.0.1";
$g['call_socket_port'] = 3001;

// nexus sso 연동
$g['sid_enc_key'] = '$oCH@_q^.^p@r50nAA|__@_@@25eo6##'; //md5("peso@aicc");
$g['sid_sso_login'] = "https://vm41.nexuscommunity.kr:8083";
$g['sid_send_url'] = "https://ccaas.sktaicc.com/nexus_auth";

// DB 정보
$_db_bot['host'] = '172.27.58.26'; // BPP-AICCB-PSDB01 + BPP-AICCB-PSDB02 의 VIP
$_db_bot['name'] = 'aicc_prd_DB';
$_db_bot['user'] = 'aicc_svc';
$_db_bot['pass'] = 'Aiccb.8651!';
$_db_bot['port'] = 3306;

// Cafe24 문자 발송
$aSMSInfo = array();
$aSMSInfo['id'] = "persona03";
$aSMSInfo['key'] = "ea7523a8fa061214b4848d5344478167";
$aSMSInfo['from_phone'] = "02-762-8763";

$aSMSInfo['develop'] = 0;
$aSMSInfo['fromno_url'] = "https://sslsms.cafe24.com/smsSenderPhone.php";
$aSMSInfo['remain_url'] = "https://sslsms.cafe24.com/sms_remain.php";
$aSMSInfo['send_url'] = "https://sslsms.cafe24.com/sms_sender.php";
$aSMSInfo['result_url'] = "https://sslsms.cafe24.com/sms_list.php";

// Nexwave API
$Nexwave = '{
        "bizMessage" : {
				"alimTalk" : {
				    "tmplMo": "01",
				    "key": "abcdefg",
				    "url": "http://www.naver.com"
			    }
		}
    }';

?>