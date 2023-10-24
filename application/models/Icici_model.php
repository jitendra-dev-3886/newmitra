<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Icici_model extends CI_Model
{
        public $pubkey = '-----BEGIN CERTIFICATE-----
MIIFiTCCA3GgAwIBAgIJAPhKHX+xSWb7MA0GCSqGSIb3DQEBBQUAMFsxCzAJBgNVBAYTAklOMRQw
EgYDVQQIDAtNQUhBUkFTSFRSQTEPMA0GA1UEBwwGTVVNQkFJMRcwFQYDVQQKDA5JQ0lDSSBCYW5r
IEx0ZDEMMAoGA1UECwwDQlRHMB4XDTE3MDkyNTA4NTcwM1oXDTIwMDYyMDA4NTcwM1owWzELMAkG
A1UEBhMCSU4xFDASBgNVBAgMC01BSEFSQVNIVFJBMQ8wDQYDVQQHDAZNVU1CQUkxFzAVBgNVBAoM
DklDSUNJIEJhbmsgTHRkMQwwCgYDVQQLDANCVEcwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIK
AoICAQCpyw5vtvzONTBwIB89oI6tNmONluYlac/IGsOIJgz/NHUbvONTQasTEcFNAQLgGkljV3ZN
o2ld8Yl6njjAqd1RFfNLbcNDq5AzWRqHEvIfbdcna/wRCz1KUVS+GyZjjoDBovoAZFNo/jM6WU6D
bA4iDW7KaSkTgczt6/0vNo5/BpiDluFNLUUHtlM6D4l9ZFw/A9xoE7jms5saTCoYMz/3Vgpr6lmp
g7gckfHmHEfecSwT0N639+wGEAGdfxzAr3yEc6yCE9XjBIRiTFafBJO32SeO6LQsjl8YGa7mYsQN
Yj+Xt2+kztyq4/M5/I5En3rWVKhP6s4o7bB10uZPO2DHEo49OHnCr2MVq0lwco341xGKPaVwZ9oI
fZX6Jh7ca0y3hTXABZrA5sXfmYwaxYxz/4o1JYeiYjqSvYcKnNt7c7pcpYLKiBC/6RENxVgoNqnY
QJZj/mYkcmvNPFmHvnAGtmnRA+hm06we0dMUO0ZQJhSqP6sfM5oDeZqMAIy291YWW7Hpoimti8db
GD+pMFQxjzS5cuxPl/JjHfPRLUx/MSf26Xu1hhgfh4/9lseuNAjuHfqQS/KiT6BnpuqoMpXkx9K0
FPcfrd8TdHhuGGihuyEtEfj+3G2uMSYE4xEmDx5BQCTXA6x5I6IQyNUN+IorkbDTOJfB2tjxhbQz
rgITHQIDAQABo1AwTjAdBgNVHQ4EFgQUWI7/jLcNvrchEffA3NCjgmTDHSMwHwYDVR0jBBgwFoAU
WI7/jLcNvrchEffA3NCjgmTDHSMwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOCAgEAlfzy
H4x6x7QUtFuL8liD6WO6Vn8W9r/vuEH3YlpiWpnggzRPq2tnDZuJ+3ohB//PSBtCHKDu28NKJMLE
NqAVpgFtashkFlmMAXTpy4vYnTfj3MyAHYr9fwtvEmUKEfiIIC1WXDQzWWP4dFLdJ//jint9bdyM
Iqx+H5ddPXmfWXwAsCs3GlXGVwEmtcc9v7OliCHyyO2s++L+ATz5FoyxKCmZyn1GHD3gmvFjXicI
WB+Us1uRkrDFO8clS1hWvmvF/ghfGYmlKOqTzu/TCY4d9u/CciNesens3iSHEgs58r/9gaxwpiEs
tRolx9eVjkem1ZI5IUCUbRC40r8sL+eEObcwhVV87nrKH2l0BX8nM/ux0lqAkRO+Ek9tdP5TmHT0
XE2E/PMJO7/AlzYvN3oznT9ZeKfu6WbNIZrFCcO6GsoNi8+pKZsWuSePbrhRQC+d3whHS7tAanS8
+6gbPMMoAfkSKt0yaogld6RI2Af1C6QerxZR2LcJM5ni8eCz1cIvS3XSpkG5hcRMXHJAGkc5GAoE
Dj08gZbQVtE4FeJRfTJoX6cpXM6cBODsi8xKzpBCGNNcA/p4r/6XGg2csXyKCCLrVtk0VNKyr/Ba
6T5dfbbuzGcbL/dVd5d/7A9cGJTkk2gRxIL6bBMKn0Qm68mSDUhVFg001zi0JR3nOy9M6Hs=
-----END CERTIFICATE-----';
        
        public $privkey = '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----';
        
    public function __construct()
    {
        parent::__construct();
        // $this->load->database();
    }
    
    public function encrypt($data,$pubkey)
    {
        if (openssl_public_encrypt($data, $encrypted, $pubkey))
        $data = base64_encode($encrypted);
        else
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
    
        return $data;
    }
    
    public function decrypt($data,$privkey)
    {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $privkey))
        $data = $decrypted;
        else
            $data = '';
        
        return $data;
    }
    
    public function register_api($CURLOPT_URL,$values)
    {
        
        $request = $this->encrypt(json_encode($values),$this->pubkey);
        $curl = curl_init();
        curl_setopt_array($curl, array(
                CURLOPT_URL => $CURLOPT_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $request,
                CURLOPT_HTTPHEADER => array(
                    "accept: */*",
                    "content-length: 684",
                    "content-type: text/plain",
                    "APIKEY: 25e3e486a5574dccafa8e6baa201b335"
                ),
            ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl);
        
        curl_close($curl);
        
        $decrypted_response = $this->decrypt($response,$this->privkey);
        return json_decode($decrypted_response);      
    }
    
    
    
}