<?
    define('Rb_root',dirname(dirname(__FILE__)).'../');
    define('Rb_path','../');
    
    //error_reporting(E_ALL & ~E_NOTICE);
    error_reporting(E_ERROR);
    session_start();
    
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
    $g['https_on'] = $_SERVER['HTTPS']=='on' || stripos($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') !== false ? true : false;

    $chConfDir = './configuration';
    if('localhost' === $_SERVER['SERVER_NAME']){
        include_once $chConfDir.'/env-local.php';
    }
    elseif('bottalks.nexuscommunity.kr' === $_SERVER['SERVER_NAME']){
        include_once $chConfDir.'/env-dev.php';
    }
    elseif('61.250.39.72' === $_SERVER['SERVER_ADDR']){
        include_once $chConfDir.'/env-stage.php';
    }
    else{
        $chConfDir = substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/"));
        include_once $chConfDir.'/bottalksConf.php';
    }

    require $g['path_var'].'db.info.php';
    require $g['path_var'].'table.info.php';
    require $g['path_core'].'function/db.mysql.func.php';
    require $g['path_core'].'function/sys.func.php';

    $DB_CONNECT = isConnectedToDB($DB);
    $g['mobile']= isMobileConnect($_SERVER['HTTP_USER_AGENT']);
    $g['dir_module'] = $g['path_module'].$m.'/';
    $g['dir_include'] = $g['dir_module'].'includes/';
    $g['url_host'] = 'http'.($g['https_on'] ? 's':'').'://'.$_SERVER['HTTP_HOST'];

    include_once $g['dir_module'].'var/var.php'; // 모듈 설정값 
    require_once $g['dir_module'].'var/define.path.php';

    include_once $g['path_root'].'/shopAPI/class/cbotShopApi.oauth.php';
    
    $chatbot = new Chatbot();
    
    if(isset($_SESSION['S_oauth']['mode']) && $_SESSION['S_oauth']['mode'] == 'get_token') {
        $params = array();
        $params['client_id'] = $chatbot->shopApiVendor['cafe24']['client_id'];
        $params['client_secret'] = $chatbot->shopApiVendor['cafe24']['client_secret'];
        $params['mall_id'] = $_SESSION['S_oauth']['mall_id'];
        
        $cbotapi = new cbotShopOauthApi($params);
        $result = $cbotapi->checkRefreshToken();
        
        if($result->access_token && $result->refresh_token) {
            $_data = array();
            $_data['nameArray'] = array();
            $_data['nameArray']['use_shopapi'] = 'on';
            $_data['nameArray']['shopapi_vendor'] = $_SESSION['S_oauth']['mall_vendor'];
            $_data['nameArray']['shopapi_domain'] = $_SESSION['S_oauth']['mall_address'];
            $_data['nameArray']['shopapi_mall_id'] = $_SESSION['S_oauth']['mall_id'];
            $_data['nameArray']['shopapi_access_token'] = $result->access_token;
            $_data['nameArray']['shopapi_access_token_expire'] = $result->expires_at;
            $_data['nameArray']['shopapi_refresh_token'] = $result->refresh_token;
            $_data['nameArray']['shopapi_refresh_token_expire'] = $result->refresh_token_expires_at;
            
            $data = array();
            $data['vendor'] = $_SESSION['S_oauth']['vendor'];
            $data['bot'] = $_SESSION['S_oauth']['bot'];
            $_data['data'] = $data;
            $chatbot->updateBotSettings($_data);
        
            $script = "<script>";
            $script .=" opener.document.getElementById('is_token').value='true';";
            $script .=" opener.adminObj.showToast({msg:'접속토큰이 획득되었습니다.'});";
            $script .=" opener.document.getElementById('shopapi_get_token').style.display='none';";
            $script .=" window.close();";
            $script .="</script>";
        } else {
            $script = "<script>alert('접속토큰 획득 실패!');window.close();</script>";
        }
        echo $script;
        exit;
    }
?>