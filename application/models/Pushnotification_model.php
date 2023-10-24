<?php
if(!defined('BASEPATH')) exit('No Direct Access Allowed');

date_default_timezone_set('Asia/Calcutta'); 

class Pushnotification_model extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    
    public function push_notification_dynamic($message,$title){
        
        $this->db->select('fcm_token');
        $this->db->from('customers');
        //$this->db->where('cus_id',$cus_id);
        $query = $this->db->get();
        
        //echo $this->db->last_query();exit;
        
        foreach($query->result_array() as $rec){
            $device_id = $rec['fcm_token'];
    
            if(!empty($device_id)){
            //API URL of FCM
            $url = 'https://fcm.googleapis.com/fcm/send';
            
           // echo $device_id;
            
            /*api_key available in:
            Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/   
            $api_key = ' AAAAzFgROWY:APA91bFmsw1Sbzyx7D9B3NFgb3Gf0_5rdpnmC2n6Iqtw5IWaScDN_G8cf3IQHMqzmZRwe72e-SVxEonBaEkQ7zlzNIiUkBSDuf-ipWCj9EG8I24qeYrYwxZamu8kBEhRTqwWZyK5jUXp';
                        
            $fields = array (
                'to' => $device_id,
                'notification' => array (
                        "title" => "$title",
                        "body" => "$message"
                )
            );
            
            // print_r($fields);
            //header includes Content type and api key
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key='.$api_key
            );
                        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
          
        }
        }
        return $result;
    }
    
    public function push_notification($message,$title,$cus_id){
        
        $this->db->select('fcm_token');
        $this->db->from('customers');
        $this->db->where('cus_id',$cus_id);
        $query = $this->db->get();
        
        //echo $this->db->last_query();exit;
        
        foreach($query->result_array() as $rec){
            $device_id = $rec['fcm_token'];
    
            if(!empty($device_id)){
            //API URL of FCM
            $url = 'https://fcm.googleapis.com/fcm/send';
            
           // echo $device_id;
            
            /*api_key available in:
            Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/   
            $api_key = ' AAAAzFgROWY:APA91bFmsw1Sbzyx7D9B3NFgb3Gf0_5rdpnmC2n6Iqtw5IWaScDN_G8cf3IQHMqzmZRwe72e-SVxEonBaEkQ7zlzNIiUkBSDuf-ipWCj9EG8I24qeYrYwxZamu8kBEhRTqwWZyK5jUXp';
                        
            $fields = array (
                'to' => $device_id,
                'notification' => array (
                        "title" => "$title",
                        "body" => "$message"
                )
            );
            
            print_r($fields);
            //header includes Content type and api key
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key='.$api_key
            );
                        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            echo $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
          
        }
        }
        return $result;
    }
    
    
    public function push_notification_upi_collection($message,$title,$cus_id,$PayerAmount){
        
        $this->db->select('fcm_token');
        $this->db->from('customers');
        $this->db->where('cus_id',$cus_id);
        $query = $this->db->get();
        
        //echo $this->db->last_query();exit;
        
        foreach($query->result_array() as $rec){
            $device_id = $rec['fcm_token'];
    
            if(!empty($device_id)){
            //API URL of FCM
            $url = 'https://fcm.googleapis.com/fcm/send';
            
           // echo $device_id;
            
            /*api_key available in:
            Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/   
            $api_key = ' AAAAzFgROWY:APA91bFmsw1Sbzyx7D9B3NFgb3Gf0_5rdpnmC2n6Iqtw5IWaScDN_G8cf3IQHMqzmZRwe72e-SVxEonBaEkQ7zlzNIiUkBSDuf-ipWCj9EG8I24qeYrYwxZamu8kBEhRTqwWZyK5jUXp';
                        
            $received= 'Amount Received';
            $fields = array (
                'to' => $device_id,
                'notification' => array (
                        "title" => "$title",
                        "body" => "$message",
                        "tag" => $PayerAmount.$received
                )
            );
            
            //print_r($fields);
            //header includes Content type and api key
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key='.$api_key
            );
                        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
          
        }
        }
        return $result;
    }

}