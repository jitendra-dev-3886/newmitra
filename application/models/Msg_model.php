<?php if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!'); 
class Msg_model extends CI_Model 
{ 
    public function __construct() 
    {
	    parent::__construct(); 
		$this->load->database();
		$this->load->helper('date');
		$this->load->model('db_model');
    }
    public function registration($data)
    {
    	$message = "Dear user your Login is ".$data['mobile'].", & Password ".$data['pass']." From, Newmitra"; 
    	$this->sendsms($data['mobile'],$message,"1707162563410941031");
    }
    public function cus_registration($data)
    {
    	$message = "Welcome in Our New Mitra, your member id: ".$data['mobile'].", login pass: ".$data['pass'].", login pin: ".$data['pin'].", APP Link: https://play.google.com/store/apps/details?id=com.satmatgroup.newmitra"; 
    	$this->sendsms($data['mobile'],$message,"1707162505918424354");
    }
    
    
    public function forget_pin($data)
    {
        $message = "Dear user , your pin is ".$data['pin']." From,Newmitra";
        // $message = "Dear user , your pin is ".$data['pin']." From, Newmitra"; 
        $this->sendsms($data['mobile'],$message,"1707162563671355241");
    }
    public function forget_pass($data)
    {
        $message = "Dear user , your password is ".$data['pass']." From, Newmitra"; 
        $this->sendsms($data['mobile'],$message,"1707162563550195589");
    }
	public function forget_data($data)
    {
        $message = "Dear user your Login is ".$data['username']." , & Password ".$data['pass']." From, Newmitra"; 
        $this->sendsms($data['mobile'],$message,"1707162563562816769");
    }
	public function approve_request($data)
    {
        $message = "Your Fund Request Has been Approved";
        $this->sendsms($data[0]->cus_mobile,$message,"1707162563574315188");
    }
	public function cancel_request($data)
    {
        $message = "Your Fund Request Has been Cancelled";
        $this->sendsms($data[0]->cus_mobile,$message,"1707162563580835540");
    }
	public function send_request($data)
    {
		$message = "You Have Received Fund Request from ".$data['name']." From, Newmitra"; 
		$this->sendsms($data['data']->cus_mobile,$message,"1707162563593717911");
    }
	public function approve_request1($data)
    {
		$this->sendsms($data['data']->cus_mobile,$data['message']);
	}
    public function reset_pin($data)
    {
        $message = "Dear ".$this->config->item('title')." , your pin is ".$data['pin']." From,Newmitra"; 
    	$this->sendsms($data['mobile'],$message,"1707162563671355241");
    }
    public function reset_pass($data)
    {
    	$message = "Dear ".$this->config->item('title')." , your password is ".$data['pass']." From, Newmitra"; 
    	$this->sendsms($data['mobile'],$message,"1707162563695747376");
    }
    public function money_send_otp($data)
    {
    	$message = "Dear user your Otp is ".$data['otp']." From, Newmitra"; 
    	return $this->sendsms($data['mobile'],$message,"1707162505936236739");
    }
    public function otp_reg_app($data)
    {
        $message = "Dear user your Otp is ".$data['otp']." From, Newmitra"; 
    	return $this->sendsms($data['mobile'],$message,"1707162505936236739");
    }
    public function sendsms($agentmobile,$message,$temp_id="")
    {
	    $message=urlencode($message);
	    
	    $get_url="http://www.smsalert.co.in/api/push.json?apikey=60bfbd175334e&route=transactional&sender=NEWMIT&mobileno=$agentmobile&text=$message&templateid=$temp_id";
	   // echo $get_url;exit;
// 		$get_url="http://www.smsalert.co.in/api/push.json?apikey=60bfbd175334e&route=transactional&sender=NEWMIT&mobile
// no=$agentmobile&text=$message";
		//60bfbd175334e
	/*	http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$agentmobile&sendername=MUZOTP&message=$message&templateid=$temp_id
	*/
		
		
// 		file_get_contents($get_url);
		
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,0);
		curl_setopt($ch, CURLOPT_URL, $get_url);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
    	curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS 
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
    	$return_val = curl_exec($ch);
    	return $return_val;
    // 	echo $get_url;echo '<br>';
    // 	print_r('result= '.$return_val);exit;
	   
    }
    
    
    public function getToken($ORDERID,$amount,$cus_id){
            $paytmParams = array();
            $paytmParams["body"] = array(
                "requestType"   => "Payment",
                "mid"           => "qpgJVx18908427499899",
                "websiteName"   => "DEFAULT",//"WEBSTAGING",
                "orderId"       => "$ORDERID",
                "callbackUrl"   => "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=$ORDERID",
                "txnAmount"     => array(
                    "value"     => "$amount",
                    "currency"  => "INR",
                ),
                "userInfo"      => array(
                    "custId"    => "CUST_$cus_id",
                ),
            );
            $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "rkc9GEf6h2Ve9yfx");
            
            $paytmParams["head"] = array(
                "signature"    => $checksum
            );
            
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
            $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=qpgJVx18908427499899&orderId=$ORDERID";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = curl_exec($ch);
            $result = json_decode($response);
            
            if($result->body->resultInfo->resultMsg == 'Success'){
                return $token=$result->body->txnToken;
                //echo $data = array('status'=>true,'message'=>'Token Fetched','Token'=>$token);
            }else{
                return false;
                //echo $data = array('status'=>false,'message'=>'Something went worng');
            }
    }

}
    
    class PaytmChecksum{

    	private static $iv = "@@@@&&&&####$$$$";
    
    	static public function encrypt($input, $key) {
    		$key = html_entity_decode($key);
    
    		if(function_exists('openssl_encrypt')){
    			$data = openssl_encrypt ( $input , "AES-128-CBC" , $key, 0, self::$iv );
    		} else {
    			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
    			$input = self::pkcs5Pad($input, $size);
    			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
    			mcrypt_generic_init($td, $key, self::$iv);
    			$data = mcrypt_generic($td, $input);
    			mcrypt_generic_deinit($td);
    			mcrypt_module_close($td);
    			$data = base64_encode($data);
    		}
    		return $data;
    	}
    
    	static public function decrypt($encrypted, $key) {
    		$key = html_entity_decode($key);
    		
    		if(function_exists('openssl_decrypt')){
    			$data = openssl_decrypt ( $encrypted , "AES-128-CBC" , $key, 0, self::$iv );
    		} else {
    			$encrypted = base64_decode($encrypted);
    			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
    			mcrypt_generic_init($td, $key, self::$iv);
    			$data = mdecrypt_generic($td, $encrypted);
    			mcrypt_generic_deinit($td);
    			mcrypt_module_close($td);
    			$data = self::pkcs5Unpad($data);
    			$data = rtrim($data);
    		}
    		return $data;
    	}
    
    	static public function generateSignature($params, $key) {
    		if(!is_array($params) && !is_string($params)){
    			throw new Exception("string or array expected, ".gettype($params)." given");			
    		}
    		if(is_array($params)){
    			$params = self::getStringByParams($params);			
    		}
    		return self::generateSignatureByString($params, $key);
    	}
    
    	static public function verifySignature($params, $key, $checksum){
    		if(!is_array($params) && !is_string($params)){
    			throw new Exception("string or array expected, ".gettype($params)." given");
    		}
    		if(isset($params['CHECKSUMHASH'])){
    			unset($params['CHECKSUMHASH']);
    		}
    		if(is_array($params)){
    			$params = self::getStringByParams($params);
    		}		
    		return self::verifySignatureByString($params, $key, $checksum);
    	}
    
    	static private function generateSignatureByString($params, $key){
    		$salt = self::generateRandomString(4);
    		return self::calculateChecksum($params, $key, $salt);
    	}
    
    	static private function verifySignatureByString($params, $key, $checksum){
    		$paytm_hash = self::decrypt($checksum, $key);
    		$salt = substr($paytm_hash, -4);
    		return $paytm_hash == self::calculateHash($params, $salt) ? true : false;
    	}
    
    	static private function generateRandomString($length) {
    		$random = "";
    		srand((double) microtime() * 1000000);
    
    		$data = "9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_";	
    
    		for ($i = 0; $i < $length; $i++) {
    			$random .= substr($data, (rand() % (strlen($data))), 1);
    		}
    
    		return $random;
    	}
    
    	static private function getStringByParams($params) {
    		ksort($params);		
    		$params = array_map(function ($value){
    			return ($value !== null && strtolower($value) !== "null") ? $value : "";
    	  	}, $params);
    		return implode("|", $params);
    	}
    
    	static private function calculateHash($params, $salt){
    		$finalString = $params . "|" . $salt;
    		$hash = hash("sha256", $finalString);
    		return $hash . $salt;
    	}
    
    	static private function calculateChecksum($params, $key, $salt){
    		$hashString = self::calculateHash($params, $salt);
    		return self::encrypt($hashString, $key);
    	}
    
    	static private function pkcs5Pad($text, $blocksize) {
    		$pad = $blocksize - (strlen($text) % $blocksize);
    		return $text . str_repeat(chr($pad), $pad);
    	}
    
    	static private function pkcs5Unpad($text) {
    		$pad = ord($text[strlen($text) - 1]);
    		if ($pad > strlen($text))
    			return false;
    		return substr($text, 0, -1 * $pad);
    	}
    }