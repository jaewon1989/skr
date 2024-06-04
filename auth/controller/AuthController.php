<?php

require_once 'auth/service/AuthService.php';

class AuthController
{
    private $_params;
    private $_authService;


    public function __construct()
    {
        $this->_authService = new AuthService();
    }

    /**
     * @throws Exception
     */
    public function getJwtToken()
    {
        if (isset($_COOKIE)) {
            // Loop through each cookie and store it in an associative array
            $cookieMap = array();
            foreach ($_COOKIE as $name => $value) {
                $cookieMap[$name] = $value;
            }

            if ($cookieMap['SID'] === null) {
                echo 'non-existent SID';
            } else {
                echo $this->_authService->getJwtForNexus(str_replace(' ', '+', $cookieMap['SID']));
            }
        }
    }
}