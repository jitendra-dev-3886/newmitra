<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encryption_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    
    public function encode($code){
       
        $data=bin2hex(
            openssl_encrypt(
                "$code",
                'aes-256-cbc',
                "MIIEpAISVbgTSQEArBxBS",
                OPENSSL_RAW_DATA,
                str_repeat("\0", 16) 
            )
        );
        
        return $data;
    }

    public function decode($code){
        
        $dec=openssl_decrypt(
            hex2bin("$code"),
            'aes-256-cbc',
            "MIIEpAISVbgTSQEArBxBS",
            OPENSSL_RAW_DATA,
            str_repeat("\0", 16)
        );
        
        return $dec;
    }
    
}

?>