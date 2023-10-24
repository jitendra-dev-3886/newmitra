<?php if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!');
class Email_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	
	public function send_email($to,$msg,$sub){
	    
	    $from_email="info@easypayall.com";
	    $message = $msg.'<br>'."If not you,Contact admin ";
        $subject = $sub;
        $to_email = $to;
        $to_fullname = "Recharge";
        $from_email =$from_email;
        $from_fullname = "Admin";
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
          // Additional headers
          // This might look redundant but some services REALLY favor it being there.
        $headers .= "To: $to_fullname\r\n";
        $headers .= "From: $from_fullname <$from_email> \r\n";
          
        $url="https://satmatgroup.com/Send_Email_Api.php?name=$to_fullname&email=$to_email&fromemail=$from_email&fromname=$from_fullname&message=$message&subject=$subject";
        $url = str_replace(" ", "+", $url);
        @file_get_contents($url);
        
        /*$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        $response = curl_exec($ch);
        $result = json_decode($response);
        $info = curl_getinfo($ch);
        curl_close($ch);*/
        
        return true;
        
        /*if(!mail($to_email, $subject, $message , $headers)){
            return false;
        }else {
            return true;
        }*/
    }
    
}	