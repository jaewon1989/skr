<?php
/* 
 ** guzzel 폴더의 src 는 사용하지 않음
 ** guzzel 은 composer 로 설치함 > gmail 설치메뉴얼 참조    
 */
//require $g['path_root'].'vendor/autoload.php';
require dirname(__file__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

?>