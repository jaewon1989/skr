<?php
require_once 'dootawiz/controller/DootawizController.php';

$params = $_POST;
$dootawizController = new DootawizController();
$dootawizController->interfaceProcess($params);
exit;