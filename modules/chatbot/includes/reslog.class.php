<?php
class Responselog {
    private $logDir;
    private $saveDir;
    private $logData;

    public function __construct() {
        $this->logDir = $_SERVER['DOCUMENT_ROOT']."/../logs";
    }

    public function getMicroTime() {
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        return $d->format("Y-m-d H:i:s.u");
    }

    public function setLogWrite($data) {
        // Ãªº¿ ·Î±× °Ë»ö
        $log_text = "";

        $chQuery = "Select A.* From rb_chatbot_chatLog A ";
        $chQuery .="Where A.bot = '".$data['bot']."' and A.roomToken = '".$data['roomToken']."' ";
        $chQuery .="Order by A.uid ASC ";
        $RCD = db_query($chQuery, $GLOBALS['DB_CONNECT']);
        while($R=db_fetch_array($RCD)){
            $dTime = date("Y-m-d H:i:s", strtotime($R['d_regis']));
            $log_text .="[date]: ".$dTime." [ip]: ".$R['ip']." [roomToken]: ".$R['roomToken']." [request]: ".$R['content']."\n";

            $chQuery = "Select A.* From rb_chatbot_botChatLog A ";
            $chQuery .="Where A.roomToken = '".$R['roomToken']."' and A.chat = '".$R['uid']."' ";
            $chQuery .="Order by A.uid ASC ";
            $BRCD = db_query($chQuery, $GLOBALS['DB_CONNECT']);
            while($row=db_fetch_array($BRCD)){
                $dTime = date("Y-m-d H:i:s", strtotime($R['d_regis']));
                $_response = $this->getResponseParse($row);
                $log_text .="[date]: ".$dTime." [roomToken]: ".$row['roomToken']." [response]: ".$_response."\n";
            }
        }

        $this->saveDir = $this->setMKDir($this->logDir, date("Ymd"));
        $logFile = $this->saveDir."/".$data['botid']."_".date("Ymd").".log";
        file_put_contents($logFile, $log_text, FILE_APPEND);
    }

    public function getResponseParse($data) {
        global $chatbot, $g;

        $res_text = "";
        $content = $data['content'];

        if($content == strip_tags($content)) {
            $res_text = trim(preg_replace('/\r\n|\r|\n/', ' ', preg_replace('/<br>|<br \/>/', ' ', $content)));
        } else {
            require_once $g['path_core'] . "function/simple_html_dom.php";
            $oHtml = str_get_html($content);
            if($oHtml->find('div.cb-chatting-balloon')) {
            	$res_text = trim(preg_replace('/\r\n|\r|\n/', ' ', preg_replace('/<br>|<br \/>/', ' ', $oHtml->find('div.cb-chatting-balloon', 0)->plaintext)));
            }
        }
        return trim($res_text);
    }

    public function setMKDir($chDataDir, $chTargetDir) {
        if (!is_dir($chDataDir."/".$chTargetDir)) {
            $oldmask = umask(0);
            mkdir($chDataDir."/".$chTargetDir, 0707);
            umask($oldmask);
            return $chDataDir."/".$chTargetDir;
        } else {
            return $chDataDir."/".$chTargetDir;
        }
    }

    public function getRemoteIP() {
        $ipaddress = "";
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
?>