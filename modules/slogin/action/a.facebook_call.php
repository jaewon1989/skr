<?php
if(!defined('__KIMS__')) exit;
include $g['dir_module'].'var/var.php';
   $state='fb_'.$d['social']['key_f'];
      $redirect_uri = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&facebook=Y');
   $loginUrl = "http://www.facebook.com/dialog/oauth?"
          . "client_id=" . $d['social']['key_f']
          . "&redirect_uri=" . $redirect_uri
          . "&state=" . $state
          . "&response_type=code"
          . "&scope=public_profile,email"; // openid%20profile
          
     header('Location:'.$loginUrl);
  exit;

?>
