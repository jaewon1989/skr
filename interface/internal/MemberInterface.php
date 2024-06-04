<?php
require_once 'member/controller/MemberController.php';

$params = $_POST;
$memberController = new MemberController();
$memberController->interfaceProcess($params);
exit;