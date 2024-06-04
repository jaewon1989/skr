<?php

require_once 'auth/SIDHelper.php';

class AuthService
{
    /**
     * @var SIDHelper
     */
    private $sidHelper;

    public function __construct() {
        $this->sidHelper =  new SIDHelper();
    }

    /**
     * @throws Exception
     */
    public function getJwtForNexus($data) {

        $decryptSID = $this->sidHelper->decrypt($data);
        $sidArray = explode(',', $decryptSID);
        return $this->sidHelper->jwtDecoder($sidArray[0]);
    }

}