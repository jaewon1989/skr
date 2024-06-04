<?php
require_once 'blackList/controller/BlackListController.php';

$params = $_POST;
$blackListController = new BlackListController();
try {
    $blackListController->interfaceProcess($params);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode((object)array('error' => 'Exception 발생', 'message' => $e->getMessage()), JSON_THROW_ON_ERROR);
}
exit;