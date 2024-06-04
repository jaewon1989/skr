<?php
require_once 'dialog/controller/DialogController.php';

$params = $_POST;
$dialogController = new DialogController();
$dialogController->interfaceProcess($params);
exit;