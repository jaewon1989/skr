<?php
require_once 'auth/controller/AuthController.php';

$params = $_POST;
$authController = new AuthController();
$authController->getJwtToken($params);
exit;