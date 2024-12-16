<?php

class Crypt
{
    private $iv; 
    private $cipher; 


    public function __construct()
    {
        $this->iv = random_bytes(16);
        $this->cipher = "AES-256-CBC"; 
    }

    public function encryptData($data)
    {   
       
        $encryptedString = openssl_encrypt($data, $this->cipher, SECRET_KEY, OPENSSL_RAW_DATA, $this->iv);
        $encryptedBase64 = base64_encode($this->iv . $encryptedString);
        
        return json_encode(array("data" => $encryptedBase64));
    }

    public function decryptData($encryptedString)
{
    $data = json_decode($encryptedString, true); 
    if (isset($data['data'])) {
        $decoded = base64_decode($data['data']);
        $iv_decoded = substr($decoded, 0, 16);
        $encrypted_data = substr($decoded, 16);
        $decrypted = openssl_decrypt($encrypted_data, $this->cipher, SECRET_KEY, OPENSSL_RAW_DATA, $iv_decoded);
        return $decrypted;
    }
    return null;
}
}
?>