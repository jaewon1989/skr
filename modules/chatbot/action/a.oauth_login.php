<?php
if(!defined('__KIMS__')) exit;
require_once $g['dir_module'].'var/var.php';
require_once $g['dir_module'].'var/define.path.php';

if(!trim($_POST['mode']) || !trim($_POST['mall_id'])) {
    echo "<script>alert('쇼핑몰 ID를 입력해주세요.'); window.close();</script>";
    exit;
} 

$mode = trim($_POST['mode']);
$vendor = trim($_POST['vendor']);
$bot = trim($_POST['bot']);
$mall_id = trim($_POST['mall_id']);
$mall_vendor = trim($_POST['mall_vendor']);
$mall_address = trim($_POST['mall_address']);

$_SESSION['S_oauth'] = array();
$_SESSION['S_oauth']['mode'] = $mode;
$_SESSION['S_oauth']['vendor'] = $vendor;
$_SESSION['S_oauth']['bot'] = $bot;
$_SESSION['S_oauth']['mall_id'] = $mall_id;
$_SESSION['S_oauth']['mall_vendor'] = $mall_vendor;
$_SESSION['S_oauth']['mall_address'] = $mall_address;

include_once $g['path_root'].'/shopAPI/class/cbotShopApi.oauth.php';

$chatbot = new Chatbot();

$params = array();
$params['client_id'] = $chatbot->shopApiVendor['cafe24']['client_id'];
$params['client_secret'] = $chatbot->shopApiVendor['cafe24']['client_secret'];
$params['mall_id'] = $_SESSION['S_oauth']['mall_id'];

if($mode == 'get_token') {
    $cbotapi = new cbotShopOauthApi($params);
    $cbotapi->checkRefreshToken();
}
if($mode == 'refresh_token') {
    $R = getDbData($table[$m.'botSettings'], "vendor=".$vendor." and bot=".$bot." and name='shopapi_refresh_token'", 'value');
    $refresh_token = $R['value'];
    $params['refresh_token'] = $refresh_token;
    $cbotapi = new cbotShopOauthApi($params);
    $result = $cbotapi->getRefreshToken();
    if($result->access_token && $result->refresh_token) {
        $_data = array();
        $_data['nameArray'] = array();
        $_data['nameArray']['shopapi_access_token'] = $result->access_token;
        $_data['nameArray']['shopapi_access_token_expire'] = $result->expires_at;
        $_data['nameArray']['shopapi_refresh_token'] = $result->refresh_token;
        $_data['nameArray']['shopapi_refresh_token_expire'] = $result->refresh_token_expires_at;
        
        $data = array();
        $data['vendor'] = $vendor;
        $data['bot'] = $bot;
        $_data['data'] = $data;
        $chatbot->updateBotSettings($_data);
        
        echo json_encode(array('result'=>true));
    } else {
        echo json_encode(array('result'=>false));
    }
}
exit;
?>
