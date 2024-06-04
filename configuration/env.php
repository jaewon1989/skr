<?php
    session_start();

    // host 도메인 관련 (벤더 시스템에만 적용)
    $_cloud_ = false;

    $develop = 0;
    $g['root_host'] = 'bottalks.co.kr';
    //$g['front_host'] = $develop == 1 ? 'http://dev.bottalks.co.kr' : 'https://www.bottalks.co.kr';
    $g['front_host'] = '/adm';

    $g['chatbot_host'] = 'chatbot.bottalks.co.kr';

    // bottalks 내부 호스트 ip
    $g['chatbot_local_host'] = '10.10.0.101';

    $g['cti_api_host'] = 'http://211.37.148.176:5000/doota_call_bot';
    $g['cti_chatapi_host'] = 'http://dev.bottalks.co.kr/callbot_res';

    // 응답 카운트 집계용 별도 DB 여부
    $g['sys_rcount'] = $_cloud_ ? true : false;

    // 콜봇 응답 카운트 기준
    $g['sys_callsec'] = 10; // 기준 초
    $g['sys_callcount'] = 2; // 응답 카운트 수

    // callbot websocket
    $g['web_socket_host'] = "https://ccaas.chatbot.bottalks.co.kr";
    $g['web_socket_port'] = 3000;
    $g['call_socket_host'] = "10.10.0.115";
    $g['call_socket_port'] = 3001;

    // nexus sso 연동
    $g['sid_enc_key'] = '$oCH@_q^.^p@r50nAA|__@_@@25eo6##'; //md5("peso@aicc");
    $g['sid_sso_login'] = "https://vm41.nexuscommunity.kr:8083";
    $g['sid_send_url'] = "https://bottalks.nexuscommunity.kr/nexus_auth";
    //$g['sid_send_url'] = "http://dev.bottalks.co.kr/sid_check";

    // bottalks DB 계정
    /*
    $_db_bot['host'] = '10.10.0.115';
    $_db_bot['user'] = 'bottalks_cloud';
    $_db_bot['pass'] = 'bottalks@cloud%@&(';
    $_db_bot['port'] = 3306;
    */
//    $_db_bot['host'] = '192.168.50.211';
//    $_db_bot['name'] = 'dev2';
//    $_db_bot['user'] = 'chatbot';
//    $_db_bot['pass'] = '1234!';
//    $_db_bot['port'] = 4306;
    $_db_bot['host'] = '192.168.88.196';
    $_db_bot['name'] = 'ccaas';
    $_db_bot['user'] = 'ccaas';
    $_db_bot['pass'] = 'vpfmthsk1%';
    $_db_bot['port'] = 3306;

    // syscloud DB 계정
    $_db_sys['host'] = '10.10.0.115';
    $_db_sys['name'] = 'syscloud';
    $_db_sys['user'] = 'syscloud';
    $_db_sys['pass'] = 'syscloud5279!!';
    $_db_sys['port'] = 3306;

    // front 사이트 DB 계정
    $_db_front['host'] = '110.10.189.18';
    $_db_front['name'] = 'bottalks';
    $_db_front['user'] = 'bottalks';
    $_db_front['pass'] = 'bottalks@%@&(';
    $_db_front['port'] = 3306;

    // traffic DB 계정
    $_db_traffic['host'] = '10.10.0.115';
    $_db_traffic['name'] = 'traffic';
    $_db_traffic['user'] = 'traffic';
    $_db_traffic['pass'] = 'traffic@%@&(';
    $_db_traffic['port'] = 3306;

    $_cafe24_client_id = 'iUcEHnFTDhShRccWMNFKaD';
    $_cafe24_client_secret = 'nznYgO1AEdJviB6UDQ0leE';

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
?>