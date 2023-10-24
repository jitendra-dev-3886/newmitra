<?php 
if(!defined('BASEPATH')) exit('No Direct Scripts Allowed');
ini_set('date.timezone', 'Asia/Calcutta');
//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

// generate json web token
require_once APPPATH . '/libraries/JWT/BeforeValidException.php';
require_once APPPATH . '/libraries/JWT/ExpiredException.php';
require_once APPPATH . '/libraries/JWT/SignatureInvalidException.php';
require_once APPPATH . '/libraries/JWT/JWT.php';
use \Firebase\JWT\JWT;

//Cus_id is Customer Mobile Number

class Rechargeapiappnmtr extends REST_Controller{
    
    protected $payload;
    
    public function __construct(){
        parent:: __construct();
        $token = $this->input->post('token');
        //Validate JWT Token;
        if(!$token) {
            $this->response(array('status' => FALSE,"message" => "Access Denied"));
        }
        try {
            $this->payload = JWT::decode($token, $this->config->item('key'), array('HS256'));
        } catch (Exception $ex) {
            $this->response(array('status' => FALSE,"message" => $ex->getMessage()));
        }
        $this->load->model(array('Rec_model','Encryption_model','Db_model','Appapi_model'));
    }
    

    public function rechargeFromApp_post(){
        
        $mobile = $this->post("mobile");
        $cus_id = $this->post("cus_id");
        $amount = $this->post("amount");
        $operator = $this->post("operator");
        $cus_type = $this->post("cus_type");
        $deviceId = $this->post("deviceId");
        $deviceName = $this->post("deviceName");
        
        $userMobile = $this->post("userMobile");
        $userPin = $this->post("userPin");
        $userPassword = $this->post("userPassword");
        $appVersion = $this->post("appVersion");
        
        
        //echo $userMobile,$userPin,$userPassword;exit;
        if(empty($mobile)||empty($cus_id)||empty($amount)||empty($operator)||empty($cus_type)||empty($deviceId)||empty($deviceName)||empty($userMobile)||empty($userPin)||empty($userPassword)||empty($appVersion)){

            $this->response([
                'status' => FALSE,
                'message' => 'Missing parameters.'
            ]);

        }else{
            if (preg_match('/^[0-9]+$/', $amount))
			    {
			        $admin = $this->Db_model->getAlldata("select recharge_limit from user where user_id = '6' ");
                    $recharge_limit = $admin['0']->recharge_limit;
                    if($amount >= $recharge_limit){
                        $this->response([
                            'status' => FALSE,
                            'result' => array('mess'=>"Recharge amount should be less than $recharge_limit")
                            //'message' => 'Something went worng with recharge amount.'
                        ]);
                    }else{
                        $ifLogedInDevice = $this->Appapi_model->checkDeviceId($deviceId,$cus_id);
                        if(!$ifLogedInDevice){
                            $this->response([
                                'status' => FALSE,
                                'result' => array('mess'=>"Please use same logged in device to recharge.")
                                //'message' => 'Please use same logged in device to recharge.'
                            ]);
                        }else{
                            $ifValidUser = $this->Appapi_model->checkIfValidUser($userMobile,$userPin,$userPassword);
                            if(!$ifValidUser){
                                $this->response([
                                    'status' => FALSE,
                                    'result' => array('mess'=>"Invalid User Details.")
                                    //'message' => 'Invalid User Details.'
                                ]);
                            }else{
                                if($cus_type == 'retailer'){
                                    $cus_data = $this->Db_model->getAlldata("Select * from customers where cus_id = '$cus_id' ");
                                    $cus_status = $cus_data['0']->avability_status;
                                    if($cus_status!= '0'){
                                        $this->response([
                                            'status' => FALSE,
                                            'result' => array('mess'=>"User Is Inactive ,Please contact admin.")
                                            //'message' => 'User Is Inactive ,Please contact admin.'
                                        ]);
                                    }else{
                                        $uid = round(microtime(true) * 1000);
                                        $r = $this->Rec_model->recharge_ot(array('cus_id'=>$cus_id,'amount'=>$amount,'number'=>$mobile,'operator'=>$operator,'mode'=>'App','type'=>$cus_type,'uid'=>$uid,'bill_fetch'=>$bill_fetch_info,'deviceId'=>$deviceId,'deviceName'=>$deviceName,'appVersion'=>$appVersion));
                                        if($r['type'])
                                        {
                                            $this->response([
                                                'status' => TRUE,
                                                'result' => $r
                                            ]);
                                        }
                                        else {
                                            if ($r['mess'] == 'add_fund') {
                                                $this->response([
                                                    'status' => FALSE,
                                                    'message' => 'Insufficient Fund!'
                                                ]);
                                            }
                                            elseif($r['mess'] == 'same_rec')
                                            {
                                                $this->response([
                                                    'status' => FALSE,
                                                    'message' => 'Same Recharge.'
                                                ]);
                                            }
                                        }
                                    }
                                }else{
                                    $this->response([
                                        'status' => FALSE,
                                        'result' => array('mess'=>'Please use retailer Id for recharge.')
                                    ]);
                                }
                            }
                        }
                    }
    			}
    		else{
                $this->response([
                    'status' => FALSE,
                    'result' => array('mess'=>"Please Enter Valid Amount")
                ]);
    		}    
        }
    }
    
    public function billpay_post(){
        
        $mobile = $this->post("mobile");
        $cus_id = $this->post("cus_id");
        $amount = $this->post("amount");
        $operator = $this->post("operator");
        $cus_type = $this->post("cus_type");
        $mob = $this->post("mobile_number");
        $optional1 = $this->post("optinal1");
        $optional2 = $this->post("optional2");
        
        $deviceId = $this->post("deviceId");
        $deviceName = $this->post("deviceName");
        $userMobile = $this->post("userMobile");
        $userPin = $this->post("userPin");
        $userPassword = $this->post("userPassword");
        $appVersion = $this->post("appVersion");
        
        $unique_recid = rand(1000,9999).time();
        $rtype = $this->post("rtype");
        if(empty($mobile)||empty($cus_id)||empty($amount)||empty($operator)||empty($cus_type)||empty($deviceId)||empty($deviceName)||empty($userMobile)||empty($userPin)||empty($userPassword)||empty($appVersion)){

            $this->response([
                'status' => FALSE,
                'message' => 'Missing parameters.'
            ]);

        }else{
            if (preg_match('/^[0-9]+$/', $amount)) {
                $admin = $this->Db_model->getAlldata("select recharge_limit from user where user_id = '6' ");
                $recharge_limit = $admin['0']->recharge_limit;
                if($amount >= $recharge_limit){
                    $this->response([
                        'status' => FALSE,
                        'message' => "Recharge amount should be less than $recharge_limit"
                        //'message' => 'Something went worng with recharge amount.'
                    ]);
                }else{
                    $ifLogedInDevice = $this->Appapi_model->checkDeviceId($deviceId,$cus_id);
                    if(!$ifLogedInDevice){
                        $this->response([
                            'status' => FALSE,
                            'message' => 'Please use same logged in device to recharge.'
                        ]);
                    }else{
                        $ifValidUser = $this->Appapi_model->checkIfValidUser($userMobile,$userPin,$userPassword);
                        if(!$ifValidUser){
                            $this->response([
                                'status' => FALSE,
                                'message' => 'Invalid User Details.'
                            ]);
                        }else{
                            if($cus_type == 'retailer'){
                                $cus_data = $this->Db_model->getAlldata("Select * from customers where cus_id = '$cus_id' ");
                                $cus_status = $cus_data['0']->avability_status;
                                if($cus_status != '0'){
                                    $this->response([
                                        'status' => FALSE,
                                        'message' => 'User Is Inactive ,Please contact admin.'
                                    ]);
                                }else{
                                    $uid = round(microtime(true) * 1000);
                                    $r = $this->Rec_model->recharge_ot(array('cus_id'=>$cus_id,'amount'=>$amount,'number'=>$mobile,'operator'=>$operator,'mode'=>'App','type'=>$cus_type,'uid'=>$uid,'billing'=>'1','optional1'=>$optional1,'optional2'=>$optional2,'mob'=>$mob,'unique_recid'=>$unique_recid,'deviceId'=>$deviceId,'deviceName'=>$deviceName,'appVersion'=>$appVersion));
                                    if($r['type'])
                                    {
                                        $this->response([
                                            'status' => TRUE,
                                            'result' => $r
                                        ]);
                                    }
                                    else {
                                        if ($r['mess'] == 'add_fund') {
                                            $this->response([
                                                'status' => FALSE,
                                                'message' => 'Insufficient Fund!'
                                            ]);
                                        }
                                        elseif($r['mess'] == 'same_rec')
                                        {
                                            $this->response([
                                                'status' => FALSE,
                                                'message' => 'Same Recharge.'
                                            ]);
                                        }
                                    }
                                }
                            }else{
                                $this->response([
                                    'status' => FALSE,
                                    'message' => "Please use retailer Id for recharge."
                                    //'result' => array('mess'=>'Please use retailer Id for recharge.')
                                ]);
                            }
                        }
                    }
                }
            }else {
                $this->response([
                    'status' => FALSE,
                    'message' => "Please Enter Valid Amount."
                    //'result' => array('mess'=>"Please Enter Valid Amount")
                ]);
            }    
        }
    }


}
?>