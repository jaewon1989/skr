<?php
if (!defined('__KIMS__')) exit;

$interfaceTypes = ['external', 'internal'];
if (!in_array($i, $interfaceTypes, true)) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

if (is_file('./interface/' . $i . '/' . ucfirst($a) . 'Interface.php')) {
    require_once './interface/' . $i . '/' . ucfirst($a) . 'Interface.php';
} else {
    header('HTTP/1.1 400 Bad Request');
    exit();
}