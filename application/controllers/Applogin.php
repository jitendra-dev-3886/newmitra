<?php 
if(!defined('BASEPATH')) exit('No Direct Scripts Allowed');
ini_set('date.timezone', 'Asia/Calcutta');
//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

// include JWT Files
require_once APPPATH . '/libraries/JWT/BeforeValidException.php';
require_once APPPATH . '/libraries/JWT/ExpiredException.php';
require_once APPPATH . '/libraries/JWT/SignatureInvalidException.php';
require_once APPPATH . '/libraries/JWT/JWT.php';
use \Firebase\JWT\JWT;

class Applogin extends REST_Controller{
    
    protected $key;
    
    public function __construct(){
        parent:: __construct();
        $this->key = $this->config->item('key');
        $this->load->model(array('Db_model','Encryption_model','Appapi_model','msg_model'));
    }
    
    public function register_user_jwt($data){
        
        $issued_at = time();
        $expiration_time = $issued_at + (24 * 365 * 60 * 60); // valid for 5 hour
        $issuer = "https://edigitalvillage.net/";

        $token = array(
           "iat" => $issued_at,
           "exp" => $expiration_time,
           "iss" => $issuer,
           "data" => $data
        );
     
        // generate jwt
        $jwt = JWT::encode($token, $this->key);
        return $jwt;
    }
    public function forgetPass_post(){
        
        $new_password = rand(000000,999999);
        $mobile=$_POST['mobile'];
        $response = $this->Appapi_model->forgetPassword($new_password,$mobile);
            if($response == true){
                $this->response([
                    'status' => TRUE,
                    'message' => 'Password Changed Successfully'
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => $response
                    ]); 
            }
        
    }
    public function getpassotp_post(){
	    
	    $mobile = $this->post('mobile');
	    $otp = $this->post('otp');
    	 
    	 if(empty($mobile) || empty($otp))
    	 {
    	     $this->response([
    	        
    	         'status'=>false,
    	         'message'=>'Missing parameter'
    	         
    	         ]);
    	 }else{
    	     
    	     $data=$this->Appapi_model->getpassotp($mobile,$otp);
    	     
    	     if($data){
    	         $this->response([
    	             'status'=>true,
    	             'message'=>'Otp Sent Successfully'
	             ]);
    	     }else{
    	         $this->response([
    	             'status'=>false,
    	             'message'=>'Unable to Forget Pin'
	             ]);
    	     }
    	  }
	}
    public function userLogin_post(){
        $mobile = $this->input->post('user_mobile');
        $password = $this->input->post('user_password');
        $deviceId = $this->post("deviceId");
        $deviceName = $this->post("deviceName");
        $otp = $this->post("otp");
        $parameters = array(
                array('field' => 'user_mobile','label' => 'Mobile Number','rules' => 'required|regex_match[/^(\\+\\d{1,2}\\s)?\\(?\\d{3}\\)?[\\s.-]?\\d{3}[\\s.-]?\\d{4}$/]|min_length[10]'),
                array('field' => 'user_password','label' => 'Password','rules' => 'required'),
                array('field' => 'deviceId','label' => 'Device Id','rules' => 'required'),
                array('field' => 'deviceName','label' => 'Device Name','rules' => 'required'),
                array('field' => 'otp','label' => 'OTP','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            
            $checkifloggedin = $this->Appapi_model->check_if_already_login($mobile,$deviceId,$deviceName);
            if($checkifloggedin){
                $this->response([
                        'status' => FALSE,
                        'message' => 'User is Already Logged in on Another device.'
                    ], REST_Controller::HTTP_OK);
            }else{
                $querydata = $this->Appapi_model->user_login($mobile,$password,$deviceId,$deviceName,$otp);
                if(is_array($querydata))
                {
                    $data = array("cus_id"=>$querydata[0]->cus_id,"cus_name"=>$querydata[0]->cus_name,"cus_mobile"=>$mobile,"cus_password"=>$password);
                    $this->response([
                        'status' => TRUE,
                        'message' => 'User has been Logged in successfully.',
                        'token' => $this->register_user_jwt($data),
                        'result' => $querydata
                    ], REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response([
                        'status' => FALSE,
                        'message' => $querydata
                    ]);
                }
            }    
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
            ]);
        }
        
    }
    public function registerenquiry_post(){
        $user_name = $this->input->post('user_name');
        $user_mobile = $this->input->post('user_mobile');
        $referralId = $this->input->post('referralId');
        $email = $this->input->post('email');
        $aadhaar_number = $this->input->post('aadhaar_number');
        $user_address = $this->input->post('user_address');
        $parameters = array(
                array('field' => 'user_name','label' => 'USER_NAME','rules' => 'required'),
                array('field' => 'user_mobile','label' => 'MOBILE','rules' => 'required|greater_than[0]'),
                array('field' => 'email','label' => 'EMAIL','rules' => 'required'),
                array('field' => 'user_address','label' => 'ADDRESS','rules' => 'required'),
                array('field' => 'aadhaar_number','label' => 'AADHAR_NUM','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $response = $this->Appapi_model->enquiry($user_name,$user_mobile,$email,$aadhaar_number,$user_address,$referralId);
            if($response){
                $this->response([
                    'status' => TRUE,
                    'message' => 'New Enquiry',
                    'result' =>$response
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Something get wrong'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    
    public function register_post(){
        
            $this->response([
                'status' => FALSE,
                'message' => 'Please Contact admin.'
                ]);
        exit;        
        $user_name = $this->input->post('user_name');
        $user_mobile = $this->input->post('user_mobile');
        $email = $this->input->post('email');
        $aadhaar_number = $this->input->post('aadhaar_number');
        $user_address = $this->input->post('user_address');
        $outlate_name = $this->input->post('outlet_name');
        $distributor_number = $this->input->post('distributor_number');
        
        $parameters = array(
                array('field' => 'user_name','label' => 'USER_NAME','rules' => 'required'),
                array('field' => 'user_mobile','label' => 'MOBILE','rules' => 'required|greater_than[0]'),
                array('field' => 'email','label' => 'EMAIL','rules' => 'required'),
                array('field' => 'user_address','label' => 'ADDRESS','rules' => 'required'),
                array('field' => 'aadhaar_number','label' => 'AADHAR_NUM','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $response = $this->Appapi_model->register_retailer($user_name,$user_mobile,$email,$aadhaar_number,$user_address,$distributor_number,$outlate_name);
            if($response){
                $this->response([
                    'status' => TRUE,
                    'message' => "Registered Successfull",
                    'result' =>$response
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Please Check Email Id or Mobile Already Exists!'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    
    public function rofferStateWise_post(){

        $circle = $this->post("circle");
        $operator = $this->post("operator");
        $key = $this->post("key");

        if(empty($circle)||empty($operator)||empty($key)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        if($key == 'B)9DkM3FD%;gL'){    
            $data = $this->Appapi_model->rofferStateWise($circle,$operator);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' => $data
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'result' => 'No data found.'
                ]);
            }
        }else{
            $this->response([
                    'status' => FALSE,
                    'result' => 'Invalid Key'
                ], REST_Controller::HTTP_OK);
        }
     }
    }
    
    
    
    public function getoperator_post(){

        $mobile = $this->post("mobile");

        if(empty($mobile)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->check_operator($mobile);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    
    public function getdthoperator_post(){

        $mobile = $this->post("mobile");

        if(empty($mobile)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->checkdth_operator($mobile);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    
    public function ElectricityInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->ElectricityInfo($mobile,$operator);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    public function GasInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->GasInfo($mobile,$operator);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }

    public function PipedGasInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->PipedGasInfo($mobile,$operator);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    public function FastagInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->FastagInfo($mobile,$operator);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    public function InsuranceInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");
        $mobno = $this->post("mobno");

        if(empty($mobile)||empty($operator)||empty($mobno)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->InsuranceInfo($mobile,$operator,$mobno);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    public function WaterInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->WaterInfo($mobile,$operator);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    
    public function verifyMobile_post(){
	    
	    $mobile = $this->post('mobile');
	    $otp = $this->post('otp');
    	if(empty($mobile) || empty($otp))
    	{
    	     $this->response([
    	         'status'=>FALSE,
    	         'message'=>'Missing parameter'
    	         ]);
    	 }else{
    	    $checkuser = $this->Appapi_model->check_if_user_exists_signup($mobile);
            if($checkuser){
                $this->response([
                    'status' => FALSE,
                    'message' => 'User With This Mobile number Or email Already Exists.'
                ]);
            }else{
        	     $data=$this->Appapi_model->getpinotp($mobile,$otp);
        	     if($data){
        	         $this->response([
        	             'status'=>TRUE,
        	             'dynamic_vpa'=>'ENPE.A1888630450571@icici',
        	             'message'=>'Otp Sent Successfully'
    	             ]);
        	     }else{
        	         $this->response([
        	             'status'=>FALSE,
        	             'message'=>'Something went wrong'
    	             ]);
        	     }
            }
	    }
	}
	
	
    public function getTxnToken_post(){
        $mobile = $this->input->post('mobile');
        $amount = $this->input->post('amount');
        $rand = rand(0000,9999);
        $ORDERID = "ORDERID_$rand";
        $data = $token = $this->msg_model->getToken($ORDERID,$amount,$mobile);
        if($data){
            $this->response([
                'status'=>TRUE,
                'message'=>'Token Fetched',
                'token'=>$data,
                'orderId'=>$ORDERID,
                'call_back_url'=>'http://easypayall.in/api_callback/dmtcallback'
                ]);
        }else{
            $this->response([
                'status'=>FALSE,
                'message'=>'Something Went Wrong'
                ]);
        }
    }
	
	
    public function registerUser_post(){
        
        $mobile = $this->post("mobile");
        $name = $this->post("name");
        $email = $this->post("email");
        $date = date("Y-m-d H:i:s");
        $address = $this->post("address");
        $customer_type = $this->post("customer_type");
        $amt = $this->post("amt");
        if(empty($mobile)||empty($name)||empty($email)||empty($date)||empty($address)||empty($customer_type)){
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);
        }else{
            $checkuser = $this->Appapi_model->check_if_user_exists($mobile,$email);
            if($checkuser){
                $this->response([
                    'status' => FALSE,
                    'result' => 'User With This Mobile number Or email Already Exists.'
                ]);
            }else{
                $data = $this->Appapi_model->registerUser($mobile,$name,$email,$address,$date,$customer_type,$amt);
                if($data)
                {
                    $this->response([
                        'status' => TRUE,
                        'result' => 'Register Successful'
                    ], REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response([
                        'status' => FALSE,
                        'result' => 'Unable to fetch data.'
                    ]);
                }
            }
            
        }
    }
    
}
?>