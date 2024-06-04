<?php

class SIDHelper {
    private $iv;
    private $key;

    public function __construct() {
        $this->key = $this->AIM_KEY();
        $this->iv = substr($this->key, 0, 16);
    }

    /**
     * @throws Exception
     */
    public function decrypt($data): string
    {

        if (is_null($data)) {
            throw new Exception('ANY_VALUE_IS_NULL');
        }

        $decodedData = base64_decode($data);
        if ($decodedData === false) {
            throw new Exception('Base64 decoding failed');
        }

        $decrypted = openssl_decrypt($decodedData, 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
        if ($decrypted === false) {
            throw new Exception('Decryption failed: ' . openssl_error_string());
        }

        return $decrypted;
    }

    /**
     * @throws Exception
     */
    public function jwtDecoder($accessToken) {
        if (is_null($accessToken)) {
            throw new Exception('ANY_VALUE_IS_NULL');
        } else {
            $chunks = explode('.', $accessToken);
            if (count($chunks) < 2) {
                throw new Exception('Invalid JWT token');
            }
            $payload = base64_decode($chunks[1]);
            if ($payload === false) {
                throw new Exception('Base64 decoding failed');
            }
            return json_decode($payload, true);
        }
    }


    public static function AIM_KEY(): string
    {
        return "aiocutaetaidatoka";
    }
}

