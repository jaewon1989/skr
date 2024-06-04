<?php
    // Nexus 관리자 인증 API

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Bottalks-Key");

    if(!defined('__KIMS__')) exit;
    require_once $g['dir_module'].'var/var.php';
    require_once $g['dir_module'].'var/define.path.php';
    require_once 'auth/service/AuthService.php';

    $key = $GLOBALS['g']['sid_enc_key']; // ae1886aa6919bd9ae1ce7d6daf5000f3

    $referer = filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL);

    if('localhost' === $_SERVER['SERVER_NAME']){
        $uID = 'TEST021';
        $encName = 'GImfE4WXG1fa6sI1z12sPg==';
        $uName = getAESDecrypt($encName, $key, '', false);
        $tenantId = '1';
    }
    else{

        if (isset($_COOKIE)) {
            // Loop through each cookie and store it in an associative array
            $cookieMap = array();
            foreach ($_COOKIE as $name => $value) {
                $cookieMap[$name] = $value;
            }

            if ($cookieMap['SID'] === null) {
                header("Location: " . $g['sid_sso_login'] . "/router/?continue=" . $g['sid_send_url']);
                exit;
            } else {
                $authService = new AuthService();
                try {
                    $jwt = $authService->getJwtForNexus(str_replace(' ', '+', $cookieMap['SID']));
                    $uID = $jwt['userId'];
                    $uName = $jwt['userName'];
                    $tenantId = $jwt['tenantId'];
                } catch (Exception $e) {
                    header("Location: " . $g['sid_sso_login'] . "/router/?continue=" . $g['sid_send_url']);
                    exit;
                }
            }
        }
        else{
            header("Location: " . $g['sid_sso_login'] . "/router/?continue=" . $g['sid_send_url']);
            exit;
        }

    }

    $M = getDbData($table['s_mbrid'],"id='".$uID."'",'*');
	$M1 = getDbData($table['s_mbrdata'],'memberuid='.$M['uid'],'*');
    //$mbrGroupInfo = getDbData($table['s_mbrgroup'], 'uid='.$M1['mygroup'],'tenant');

	if(!$M['uid'] || $M1['auth'] != 1) {
	    if($referer) header('location:'.$referer);
        else echo "<script>history.back();</script>";
        exit;
	}

	$_SESSION['mbr_id'] = $M['id'];
    $_SESSION['mbr_uid'] = $M['uid'];
    $_SESSION['mbr_uname'] = $uName;
    $_SESSION['sso_login'] = true;
    $_SESSION['tenant'] = $tenantId;

    getLink('/adm','','','');
    exit;
?>

