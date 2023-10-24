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

class Appapi extends REST_Controller{
    
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
        $this->load->model(array('Appapi_model','Encryption_model','Db_model'));
    }
    
    
    public function updatefcmtoken_post(){
        
        $cus_id= $this->post('cus_id');
        $fcm_token = $this->post('fcm_token');
        
        if(empty($cus_id)||empty($fcm_token)){
            
            $this->response([
                
                'status'=>false,
                'message'=>'Missing Arguments'
                
                ]);
            
        }else{
            
            $data=$this->Appapi_model->updatefcmtoken($cus_id,$fcm_token);
            
            if($data){
                
                $this->response([
                    'status'=>true,
                    'message'=>'Details Updated Successfully'
                    ]);
            }else{
                
                $this->response([
                    'status'=>false,
                    'message'=>'Please Try Again Later'
                    ]);
            }
        }
    }
    
    public function dashboard_post(){
        $mobile = $this->input->post('cus_mobile');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $cus_type = $cusData[0]->cus_type;
                //$walletBalance = $this->Appapi_model->getWalletBalance($cus_id);
                //$aepsBalance = $this->Appapi_model->getAepsBalance($cus_id);
                $news = $this->Appapi_model->getNews($cus_type);
                $banners = $this->Appapi_model->getBanners();
                $this->response([
                    'status' => TRUE,
                    'message' => 'Data Found',
                    //'walletBalance' => round($walletBalance,2),
                    //'aepsBalance' => round($aepsBalance,2),
                    'news' => $news,
                    'banner' => $banners,
                    'cusData' => $cusData,
                    'pan_service_activate_amt' => '449',
                    'railway_service_activate_amt' => '1100'
                    ]);
                
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    } 
    
    public function aepsBalance_post(){
        $cus_id = $this->input->post('cus_id');
        $parameters = array(
                array('field' => 'cus_id','label' => 'Customer ID','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            
                $cus_id = $cus_id;
                $walletBalance = $this->Appapi_model->getAepsBalance($cus_id);
                $this->response([
                    'status' => TRUE,
                    'message' => 'AEPS Balance',
                    'AEPSBalance' => number_format($walletBalance,2)
                    ]);
            
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function walletBalance_post(){
        $mobile = $this->input->post('cus_mobile');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $walletBalance = $this->Appapi_model->getWalletBalance($cus_id);
                $this->response([
                    'status' => TRUE,
                    'message' => 'Wallet Balance',
                    'walletBalance' => round($walletBalance,2)
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function getOperators_post(){
        $operator_type = $this->input->post('operator_type');
        $this->form_validation->set_rules('operator_type','Recharge Operator Type','required');
        $this->form_validation->set_error_delimiters(' ',' ');
        
        if($this->form_validation->run()){
            $operators = $this->Appapi_model->getOperators($operator_type);
            if(is_array($operators)){
                $this->response([
                    'status' => TRUE,
                    'message' => 'Operators Found',
                    'result' => $operators
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => $operators
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function profile_post(){
        $mobile = $this->input->post('cus_mobile');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $this->response([
                    'status' => TRUE,
                    'message' => 'Customer Data',
                    'mobile' => $mobile,
                    'result' => $cusData
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    /*public function changePassword_post(){
        $mobile = $this->input->post('cus_mobile');
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'current_password','label' => 'Customer Password','rules' => 'required'),
                array('field' => 'new_password','label' => 'Customer New Password','rules' => 'required'),
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $response = $this->Appapi_model->changePassword($mobile,$current_password,$new_password);
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
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function changePin_post(){
        $mobile = $this->input->post('cus_mobile');
        $current_pin = $this->input->post('current_pin');
        $new_pin = $this->input->post('new_pin');
    }*/
    
    
    
    public function checkIfSameRecharge_post(){
        $mobile = $this->input->post('cus_mobile');
        $amount = $this->input->post('amount');
        $recmobile = $this->input->post('recmobile');
        $operator = $this->input->post('operator');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'amount','label' => 'Amount','rules' => 'required|greater_than[0]'),
                array('field' => 'recmobile','label' => 'Recharge Mobile Number','rules' => 'required'),
                array('field' => 'operator','label' => 'Operator','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->check_recharge($amount,$recmobile,$cus_id,$operator);
                $this->response([
                    'status' => TRUE,
                    'message' => 'New Recharge'
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Same Recharge'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function verifyPin_post(){
        $mobile = $this->input->post('cus_mobile');
        $pin = $this->input->post('pin');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'pin','label' => 'Customer Pin Verification','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $response = $this->Appapi_model->verify_pin($mobile,$pin);
            if($response){
                $this->response([
                    'status' => TRUE,
                    'message' => 'Pin Verified Successfully'
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Enter Pin is Invalid'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function rechargeHistory_post(){
        $mobile = $this->input->post('cus_mobile');
        $date = $this->input->post('date');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'date','label' => 'Date','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->rechargeHistory($cus_id,$date);
                if($response){
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Recharge History',
                        'result' => $response
                        ]);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No Recharge History Found'
                        ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function rechargeHistoryFromTo_post(){
        $mobile = $this->input->post('cus_mobile');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'fromDate','label' => 'Date','rules' => 'required'),
                array('field' => 'toDate','label' => 'Date','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->rechargeHistoryFromTo($cus_id,$fromDate,$toDate);
                if($response){
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Recharge History',
                        'result' => $response
                        ]);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No Recharge History Found'
                        ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    /*public function viewCreditWallet_post(){
        $mobile = $this->input->post('cus_mobile');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'fromdate','label' => 'From Date','rules' => 'required'),
                array('field' => 'todate','label' => 'To Date','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->viewCreditWallet($cus_id,$fromdate,$todate);
                if($response){
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Credit Wallet History',
                        'result' => $response
                        ]);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No Credit Wallet History Found'
                        ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }*/
    
   /* public function viewDebitWallet_post(){
        $mobile = $this->input->post('cus_mobile');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'fromdate','label' => 'From Date','rules' => 'required'),
                array('field' => 'todate','label' => 'To Date','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->viewDebitWallet($cus_id,$fromdate,$todate);
                if($response){
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Debit Wallet History',
                        'result' => $response
                        ]);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No Debit Wallet History Found'
                        ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }*/
    
    public function getCommissionSlab_post(){
        $mobile = $this->input->post('cus_mobile');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->getCommissionSlab($cus_id);
                if($response){
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Commission Slab',
                        'result' => $response
                        ]);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No Commission Slab Found'
                        ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    /*public function viewLedgerReport_post(){
        $mobile = $this->input->post('cus_mobile');
        $fromdate = $this->input->post('fromdate');
        $todate = $this->input->post('todate');
        $parameters = array(
                array('field' => 'cus_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'fromdate','label' => 'From Date','rules' => 'required'),
                array('field' => 'todate','label' => 'To Date','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->getCustomerId($mobile);
            if($cusData){
                $cus_id = $cusData[0]->cus_id;
                $response = $this->Appapi_model->viewLedgerReport($cus_id,$fromdate,$todate);
                if($response){
                    $this->response([
                        'status' => TRUE,
                        'message' => 'Ledger Report History',
                        'result' => $response
                        ]);
                }else{
                    $this->response([
                        'status' => FALSE,
                        'message' => 'No Ledger Report History Found'
                        ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }*/
    
    
    /*--------------------------NOT TO COPY-----------------------------*/
    
    public function roffer_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->roffer($mobile,$operator);
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
    
    public function Dthinfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");

        if(empty($mobile)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->Dthinfo($mobile,$operator);
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
    
    public function FastagInfo_post(){

        $mobile = $this->post("mobile");
        $operator = $this->post("operator");
        echo $mobile;
        echo $operator;exit;
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
    
    public function loginuser_post(){

        $mobile = $this->post("mobile");
        $password = $this->post("password");
        $deviceId = $this->post("deviceId");
        $deviceName = $this->post("deviceName");

        if(empty($mobile)||empty($password)||empty($deviceId)||empty($deviceName)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
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
                    $this->response([
                        'status' => TRUE,
                        'message' => 'User has been Logged in successfully.',
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
        }
    }
    
    
    public function changepassword_post(){

        $cus_id = $this->post("cus_id");
        $password = $this->post("password");
        $newpassword = $this->post("newpassword");
        
        
        if(empty($cus_id) || empty($password) || empty($newpassword)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->change_password($cus_id,$password,$newpassword);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Successfully changed password.',
                    'result' => $data
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Cust id or Password.'
                ]);
            }
            
        }
    }
    
    public function changepin_post(){
	    
	    $cus_id = $this->post('id');
	    $curr_pin = $this->post('curr_pin');
	    $new_pin = $this->post('new_pin');
    	 
    	 if(empty($cus_id) || empty($curr_pin) || empty($new_pin))
    	 {
    	     $this->response([
    	        
    	         'status'=>false,
    	         'message'=>'Missing parameter'
    	         
    	         ]);
    	 }else{
    	     
    	     $data=$this->Appapi_model->changepin($cus_id,$curr_pin,$new_pin);
    	     
    	     if($data){
    	         $this->response([
    	             'status'=>true,
    	             'message'=>'Pin Changed Successfully',
    	             'result' => $data
	             ]);
    	     }else{
    	         $this->response([
    	             'status'=>false,
    	             'message'=>'Unable to change Pin Please Eneter Valid Credentials'
	             ]);
    	     }
        	 
    	 }
	}
	
	public function viewcreditwallet_post(){

        $cus_id = $this->post("cus_id");
        $from = $this->post("fromdate");
        $to = $this->post("todate");
        
        if(empty($cus_id)||empty($from)||empty($to)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->view_credit_wallet($cus_id,$from,$to);
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
    
    public function viewdebitwallet_post(){

        $cus_id = $this->post("cus_id");
        $from = $this->post("fromdate");
        $to = $this->post("todate");
       
        if(empty($cus_id)||empty($from)||empty($to)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->view_debit_wallet($cus_id,$from,$to);
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
    
    /*public function walletbalance_post(){

        $cus_id = $this->post("cus_id");
        

        if(empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->get_wallet_balance($cus_id);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
            
        }
    }*/

    public function forgetpassword_post(){

        $mobile = $this->post("mobile");
        $pin=$this->post('pin');
        $deviceName=$this->post('deviceName');
        $deviceId=$this->post('deviceId');
        
        if(empty($mobile) || empty($pin) || empty($deviceName) || empty($deviceId)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->forgot_pass($mobile,$pin,$deviceName,$deviceId);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'message' => 'successfull.',
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Mobile or pin number.'
            ]);
        }
     }
    }
    
    public function ledgerfromto_post(){

        $from = $this->post("fromdate");
        $to = $this->post("todate");
        $cus_id = $this->post("cus_id");
        
        
        if(empty($from)||empty($to)||empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->ledger_from_to($from,$to,$cus_id);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
            
        }
    }
    
    public function getcommslab_post(){

        $cusid = $this->post("cus_id");

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->getcommslab($cusid);
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
                'result' => 'Unable to fetch data.'
            ]);
        }
     }
    }
    
    public function disputehistory_post(){

        $cusid = $this->post("cus_id");
        

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->disputehistory($cusid);
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
    
    public function create_retailer_api_post(){
        
        $mobile = $this->post("mobile");
        $name = $this->post("name");
        $email = $this->post("email");
        $date = date("Y-m-d H:i:s");
        $password = $this->post("password");
        $dis_cus_id = $this->post("dis_cus_id");
        
        
        if(empty($mobile)||empty($name)||empty($email)||empty($date)||empty($password)||empty($dis_cus_id)){
            
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
                $resps = $this->Appapi_model->getAlldata("select * from users_credits where cust_id='$dis_cus_id' order by users_credits_id desc limit 1");
                    if($resps){
        		        $available_count=$resps[0]->available_count;
        		        $users_credits_id=$resps[0]->users_credits_id;
        		        if($available_count>0){
                            $data = $this->Appapi_model->create_retailer_api($mobile,$name,$email,$password,$date,$dis_cus_id);
                            if($data)
                            {
                                $available_count=$available_count-1;
                        		$this->Appapi_model->insert_update('users_credits',array('available_count'=>$available_count),array('users_credits_id'=>$users_credits_id));
                                
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
        		        else
                            {
                                $this->response([
                                    'status' => FALSE,
                                    'result' => 'You Are Done With You Credits, Please Contact To Admin For Upgrade Credits'
                                ]);
                            }
                    }
                    else{
                        $this->response([
                                    'status' => FALSE,
                                    'result' => "You Don't Have Credits For Creating Members, Please Contact To Admin"
                                ]);
                    }
                
            }
            
        }
    }
    
    public function create_distributor_api_post(){
        
        $mobile = $this->post("mobile");
        $name = $this->post("name");
        $email = $this->post("email");
        $date = date("Y-m-d H:i:s");
        $password = $this->post("password");
        $mst_cus_id = $this->post("mst_cus_id");
        
        if(empty($mobile)||empty($name)||empty($email)||empty($date)||empty($password)||empty($mst_cus_id)){
            
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
                $resps = $this->Appapi_model->getAlldata("select * from users_credits where cust_id='$mst_cus_id' order by users_credits_id desc limit 1");
                    if($resps){
        		        $available_count=$resps[0]->available_count;
        		        $users_credits_id=$resps[0]->users_credits_id;
        		        if($available_count>0){
                            $data = $this->Appapi_model->create_distributor_api($mobile,$name,$email,$password,$date,$mst_cus_id);
                            if($data)
                            {
                                $available_count=$available_count-1;
                        		$this->Appapi_model->insert_update('users_credits',array('available_count'=>$available_count),array('users_credits_id'=>$users_credits_id));
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
        		        else
                            {
                                $this->response([
                                    'status' => FALSE,
                                    'result' => 'You Are Done With You Credits, Please Contact To Admin For Upgrade Credits'
                                ]);
                            }
                    }
                    else{
                        $this->response([
                                    'status' => FALSE,
                                    'result' => "You Don't Have Credits For Creating Members, Please Contact To Admin"
                                ]);
                    }
            }
        }
    }
    
    public function user_list_post(){
        $cus_id = $this->post("dis_cus_id");
        
        if(empty($cus_id)){
           
           $this->response([
               'status' => FALSE,
               'result' => 'Missing parameters.'
           ]);

       }else{
          
           $data = $this->Appapi_model->user_list($cus_id);
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
                   'result' => 'Unable to fetch data.'
               ]);
           }   
       }
   }
   
    public function getcusid_post(){

        $mobile= $this->post("mobile");

        if(empty($mobile)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            $data = $this->Appapi_model->getcusid($mobile);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
        }
    }
  
    public function direct_credit_post(){

        $id = $this->post("dis_id");
		$cus_id = $this->post("c_id");
		$trnxamount = $this->post("amount");
		
        if(empty($id)||empty($cus_id)||empty($trnxamount)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
          
            $data = $this->Appapi_model->direct_credit($id,$cus_id,$trnxamount);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' => 'Succesful Fund transfer'
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
    
    public function submitdispute_post(){

    	$txn = $this->post('recid');
		$id = $this->post('cus_id');      
		$from = 'user'; 
		$type = $this->post('issue');
		$subject = $this->post('subject');
		$ndate = DATE('Y-m-d h:i:s');
		$yt = $this->Appapi_model->get_support_byid('','ticket_id');
		if(!empty($yt))
		{
		  	$tick = $yt[0]->ticket_id + 1;
		}
		else
		{
		  	$tick = 1;
		}
		

        if(empty($id)||empty($txn)||empty($from)||empty($type)||empty($subject)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
                
            $data = $this->Appapi_model->submitdispute($id,$txn,$from,$type,$subject,$ndate,$tick);
            $this->Appapi_model->updatedisputestatus($txn);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' => 'Submitted SuccessFully'
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
    
    
    public function support_post(){
        
        $cus_id = $this->post("cus_id");
        

        if(empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $data = $this->Appapi_model->getsupportdata();
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
        }
     
    }
    
    public function userdaybook_post(){

        $cus_id = $this->post("cus_id");
        
        if(empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $data = $this->Appapi_model->userdaybook($cus_id);
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
    
    public function add_wallet_balance_post(){

        $cus_id = $this->post("cus_id");
        $transfer_type = 'UPI';
        $amount = $this->post("amount");
        $bank_name = $this->post("bank");
        $transaction_ref = $this->post("transaction_ref");
        $transaction_id = $this->post("transaction_id");
        
        

        if(empty($cus_id)||empty($transfer_type)||empty($amount)||empty($bank_name)||empty($transaction_ref)||empty($transaction_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
          
            $data = $this->Appapi_model->add_wallet_balance($cus_id,$amount,$transfer_type,$bank_name,$transaction_ref,$transaction_id);
            if($data)
            {
                $cus_ref_id = $cus_id;
                $res = $this->Appapi_model->getAlldata("select * from customers where cus_id='$cus_id'");
                $mob = $res['0']->referral_id;
                if(isset($res['0']->referral_id) && $res['0']->referral_status=='0')
                {
                    $trnx = $this->Appapi_model->getAlldata("select SUM(txn_crdt) as total_crdt from exr_trnx where txn_type IN ('Direct Credit','UPI') and txn_agentid = '$cus_id' order by txn_id desc");
                    if($trnx['0']->total_crdt >999){
                        $this->Appapi_model->referrral_bonus_add($mob,$cus_ref_id);
                    }
                }
                $this->response([
                    'status' => TRUE,
                    'result' =>  'Wallet Balance Added Successfully'
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'result' => 'Unable to Add data Plaese Try again Later.'
                ]);
            }
            
        }
    }
    
    public function getupidetails_post(){

        $cus_id = $this->post("cus_id");
        
        if(empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->getupidetails($cus_id);
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
    
    public function userlogout_post(){

        $cus_id = $this->post("cus_id");
        
        if(empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $querydata = $this->Appapi_model->user_logout($cus_id);
            
            $this->response([
                'status' => TRUE,
                'result' => 'Logout Successfully.'
            ], REST_Controller::HTTP_OK);
           
        }
        
    }
    
    /*public function verifypin_post(){

        $mobile = $this->post("mobile");
        $pin = $this->post("pin");

        if(empty($mobile)||empty($pin)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $querydata = $this->Appapi_model->verify_pin($mobile,$pin);
        if($querydata)
        {
            $this->response([
                'status' => TRUE,
                'message' => 'Verified.'
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Pin.'
            ]);
        }
    }
    }*/
    
    public function getpinotp_post(){
	    
	    $mobile = $this->post('mobile');
	    $otp = $this->post('otp');
    	 
    	 if(empty($mobile) || empty($otp))
    	 {
    	     $this->response([
    	        
    	         'status'=>false,
    	         'message'=>'Missing parameter'
    	         
    	         ]);
    	 }else{
    	     
    	     $data=$this->Appapi_model->getpinotp($mobile,$otp);
    	     
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

	public function forgetpin_post(){
	    
	    $cus_id = $this->post('cus_id');
	    $deviceId = $this->post("deviceId");
        $deviceName = $this->post("deviceName");
        $cus_type = $this->post('cus_type');
    	 
    	if(empty($cus_id)||empty($deviceId)||empty($deviceName)||empty($cus_type))
    	 {
    	     $this->response([
    	        
    	         'status'=>false,
    	         'message'=>'Missing parameter'
    	         
    	         ]);
    	 }else{
    	     
    	     $data=$this->Appapi_model->forgetpin($cus_id,$deviceId,$deviceName,$cus_type);
    	     
    	     if($data){
    	         $this->response([
    	             'status'=>true,
    	             'message'=>'Pin Sent On Your Mobile Number Successfully'
	             ]);
    	     }else{
    	         $this->response([
    	             'status'=>false,
    	             'message'=>'Unable to Forget Pin'
	             ]);
    	     }
    	  }
	}	  
	
	public function user_list_byname_or_mobile_post(){
        $cus_id = $this->post("dis_cus_id");
        $moborname = $this->post("mobileorname");
        
        if(empty($cus_id)||empty($moborname)){
           
           $this->response([
               'status' => FALSE,
               'result' => 'Missing parameters.'
           ]);

       }else{
            
           if(is_numeric($moborname)){
               $data = $this->Appapi_model->user_list_by_mobile($cus_id,$moborname);
           }else{
               $data = $this->Appapi_model->user_list_by_name($cus_id,$moborname);
           }
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
                   'result' => 'Unable to fetch data.'
               ]);
           }   
            
       }
   }
   
   public function getbannerimages_post(){

        $data = $this->Appapi_model->getbannerimages();
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
                'result' => 'Unable to fetch data.'
            ]);
        }
    
    }
    
    public function operatorlist_post(){
        $op_type = $this->post("op_type");
        if(empty($op_type)){
           
           $this->response([
               'status' => FALSE,
               'result' => 'Missing parameters.'
           ]);

       }else{

       $data = $this->Appapi_model->operator_list($op_type);
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
               'result' => 'Unable to fetch data.'
           ]);
       }   
       }
   }
   
   public function check_if_same_recharge_post(){

        $cusid = $this->post("cus_id");
        $mobile = $this->post("mobile");
        $amount = $this->post("amount");

        if(empty($cusid)||empty($mobile)||empty($amount)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->check_recharge($amount,$mobile,$cusid);
        if($data)
        {
            
            $this->response([
                'status' => FALSE,
                'result' => 'New Recharge.'
            ]);
            
        }
        else
        {
            $this->response([
                'status' => TRUE,
                'result' => "Same Recharge"
            ], REST_Controller::HTTP_OK);
        }
     }
    }
    
    public function checkifsamefundtransfer_post(){

        $cusid = $this->post("cus_id");
        $to_id = $this->post("to_id");
        $amount = $this->post("amount");

        if(empty($cusid)||empty($amount)||empty($to_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->checkifsamefundtransfer($cusid,$amount,$to_id);
        // echo $this->db->last_query();
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => "Same Transfer"
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'New Transfer.'
            ]);
        }
     }
    }
    
     public function fundreq_post(){

        $cus_id = $this->post("cus_id");
        $req_to = $this->post("req_to");
        $amount = $this->post("amount");
        $pay_mode = '1';
        $bank = $this->post("bank");
        $ref_no = $this->post("ref_no");

        if(empty($cus_id)||empty($req_to)||empty($amount)||empty($pay_mode)||empty($bank)||empty($ref_no)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->fund_request($cus_id,$req_to,$amount,$pay_mode,$bank,$ref_no);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' =>  'Money Request Successfully Send'
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
    
     public function viewmyfundreq_post(){

        $cus_id = $this->post("cus_id");
        $from = $this->post("fromdate");
        $to = $this->post("todate");
        
        if(empty($cus_id)||empty($from)||empty($to)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $data = $this->Appapi_model->view_my_fund_request($from,$to,$cus_id);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' =>  $data
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'result' => 'No data Found.'
                ]);
            }
        }
    }
    
    
    public function rechargehistorybymobile_post(){

        $mobile = $this->post("mobile");
        $cus_id = $this->post("cus_id");
        
        if(empty($mobile)||empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->recharge_history_by_mobile($mobile,$cus_id);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
            
        }
    }
    
    public function rechargehistorybydate_post(){

        $date = $this->post("date");
        $cus_id = $this->post("cus_id");
        
        if(empty($date)||empty($cus_id)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $data = $this->Appapi_model->recharge_history_by_date($date,$cus_id);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
        
        }
    }
    
    
    //For AEPS users only
    /*public function walletbalance_post(){

        $cusid = $this->post("cus_id");

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->get_wallet_balance($cusid);
        if($data){
            $data = $data;
        }else{
            $data = array();
            $arr = [
                'txn_agentid'=> $cusid,
                'txn_clbal' => '0',
                'txn_date' => '0'
            ];
            array_push($data,$arr);
        }
        $aeps = $this->Appapi_model->aeps_balance($cusid);
        if($aeps){
            $aeps = $aeps;
        }else{
            $aeps = array();
            $arr = [
                'aeps_txn_agentid'=> $cusid,
                'aeps_txn_clbal'=> '0',
                'aeps_txn_date'=> '0'
            ];
            array_push($aeps,$arr);
        }
        
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'wallet' => $data,
                'aeps' => $aeps
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
    }*/
    
    public function check_if_permitted_aeps_user_post(){
        $cusid = $this->post("cus_id");
        

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->check_if_permitted_aeps_user($cusid);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'message' => 'Permitted User'
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Non Permitted User'
                ]);
            }
            
        }
    }
    
    public function aeps_commission_history_post(){

        $cusid = $this->post("cus_id");
        $date = $this->post("date");
        
        if(empty($cusid)||empty($date)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->aeps_commission_history($cusid,$date);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
            
        }
    }
    
    public function submitaepswithdrawalrequest_post(){

		$payment_type = $this->post('payment_type');
    	$upi_id = $this->post('upi_id');
		$cus_id = $this->post('cus_id');  
		$account_holder_name = $this->post('account_holder_name');
    	$account_number = $this->post('account_number');
		$ifsc_code = $this->post('ifsc_code');  
		$amount = $this->post('amount');
		$withdraw_charge = $this->post('withdraw_charge');
		

        if(empty($payment_type)||empty($cus_id)||empty($amount)||empty($withdraw_charge)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $aeps = $this->Appapi_model->aeps_balance($cus_id);
            //echo $this->db->last_query();
            if($aeps){
                $bal = $aeps[0]->aeps_txn_clbal;
                $aeps_cus_id = $aeps[0]->aeps_txn_agentid;
                $amt = $withdraw_charge+$amount;
                if($bal >= $amt){
                    $data = $this->Appapi_model->submitaepswithdrawalrequest($payment_type,$upi_id,$cus_id,$account_holder_name,$account_number,$ifsc_code,$amount,$withdraw_charge,$bal,$aeps_cus_id);
                    if($data)
                    {
                        $this->response([
                            'status' => TRUE,
                            'result' => 'Submitted SuccessFully.'
                        ], REST_Controller::HTTP_OK);
                    }
                    else
                    {
                        $this->response([
                            'status' => FALSE,
                            'result' => 'Unable to Send Request.'
                        ]);
                    }
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'result' => 'Insufficient Balance.'
                ]);
            }
            
        }
    }
    
    public function aeps_withdrawal_history_post(){

        $cusid = $this->post("cus_id");
        $date = $this->post("date");

        if(empty($cusid)||empty($date)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->aeps_withdrawal_history($cusid,$date);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
        }
    }
    
    public function aeps_direct_credit_post(){

		$cus_id = $this->post("cus_id");
		$amount = $this->post("amount");
        
        if(empty($cus_id)||empty($amount)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $aeps = $this->Appapi_model->aeps_balance($cus_id);
            if($aeps){
                $bal = $aeps[0]->aeps_txn_clbal;
                if($bal >= $amount){
                    $data = $this->Appapi_model->aeps_direct_credit($cus_id,$amount,$bal);
                    if($data)
                    {
                        $this->response([
                            'status' => TRUE,
                            'result' => 'Succesfull AEPS Fund transfer'
                        ], REST_Controller::HTTP_OK);
                    }else
                    {
                        $this->response([
                            'status' => FALSE,
                            'result' => 'Unable to fetch data.'
                        ]);
                    }
                }else{
                    $this->response([
                        'status' => FALSE,
                        'result' => 'Balance Less Than Amount.'
                    ]);
                }
            }else{
                $this->response([
                    'status' => FALSE,
                    'result' => 'Insufficient Balance.'
                ]);
            }

        }
    }
    
    public function aeps_user_ledger_post(){

        $cusid = $this->post("cus_id");
        $date = $this->post("date");
        
        if(empty($cusid)||empty($date)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->aeps_user_ledger($cusid,$date);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
            
        }
    }
    
    public function aeps_transaction_history_post(){

        $cusid = $this->post("cus_id");
        $date = $this->post("date");
            
        if(empty($cusid)||empty($date)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
           
            $data = $this->Appapi_model->aeps_transaction_history($cusid,$date);
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
                    'result' => 'Unable to fetch data.'
                ]);
            }
            
        }
    }
    
    	
	public function dmt_transaction_history_post(){

        $cusid = $this->post("cus_id");
        $date = $this->post("date");

        if(empty($cusid)||empty($date)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->dmt_transaction_history($cusid,$date);
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
                'result' => 'Unable to fetch data.'
            ]);
        }
     }
    }
    
    public function aeps_commission_slab_post(){

        $cusid = $this->post("cus_id");

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->aeps_commission_slab($cusid);
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
                'result' => 'Unable to fetch data.'
            ]);
        }
     }
    }
    
    public function dmt_commission_slab_post(){

        $cusid = $this->post("cus_id");

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->dmt_commission_slab($cusid);
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
                'result' => 'Unable to fetch data.'
            ]);
        }
     }
    }
    
    public function submitdmtdispute_post(){
        
    	$aeps_id = $this->post('aeps_id');
		$cusid = $this->post('cus_id');  
		$issue = $this->post('issue');

        if(empty($aeps_id)||empty($cusid)||empty($issue)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $data = $this->Appapi_model->submitdmtdispute($cusid,$aeps_id,$issue);
            $this->Appapi_model->updatedmtdisputestatus($aeps_id);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' => 'Submitted SuccessFully'
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
    
    public function dmt_dispute_history_post(){

        $cusid = $this->post("cus_id");

        if(empty($cusid)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->dmt_dispute_history($cusid);
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
                'result' => 'Unable to fetch data.'
            ]);
        }
     }
    }
    
    public function getCircle_post(){

        $data = $this->Appapi_model->getCircle();
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
    
    public function rofferStateWise_post(){

        $circle = $this->post("circle");
        $operator = $this->post("operator");

        if(empty($circle)||empty($operator)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
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
     }
    }
    
    public function getToken_post(){
        $amount = $this->input->post('amount');
        if(empty($amount)){
            $this->response([
                'status'=>FALSE,
                'message'=>"Missing Parameters"
                ]);
        }else{
            $data = $this->Appapi_model->getToken($amount);
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
    
     public function upigateway_post(){
        $amount = $this->input->post('amount');
        $product_name = $this->input->post('p_info');
        $customer_name = $this->input->post('customer_name');
        $customer_email = $this->input->post('customer_email');
        $customer_mobile = $this->input->post('customer_mobile');
        $cus_id = $this->input->post('cus_id');
        $parameters = array(
                array('field' => 'amount','label' => 'amount','rules' => 'required'),
                array('field' => 'p_info','label' => 'product name','rules' => 'required'),
                array('field' => 'customer_name','label' => 'Customer name','rules' => 'required'),
                array('field' => 'customer_email','label' => 'Customer email','rules' => 'required'),
                array('field' => 'customer_mobile','label' => 'Customer Mobile','rules' => 'required'),
                array('field' => 'cus_id','label' => 'Customer id','rules' => 'required')
            );
        $this->form_validation->set_rules($parameters);
        $this->form_validation->set_error_delimiters(' ', ' ');
        
        if($this->form_validation->run()){
            $cusData = $this->Appapi_model->upigateway($amount,$product_name,$customer_name,$customer_email,$customer_mobile,$cus_id);
            if($cusData){
                $payment_link = $cusData[0]->payment_url;
                $this->response([
                    'status' => TRUE,
                    'result' => $cusData
                    ]);
                
            }else{
                $this->response([
                    'status' => FALSE,
                    'message' => 'Entered Mobile Number Is Invalid'
                    ]); 
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function getTxnToken_post(){
        $cus_id = $this->input->post('cus_id');
        $amount = $this->input->post('amount');
        $rand = rand(0000,9999);
        $ORDERID = "ORDERID_$rand";
        $data = $token = $this->msg_model->getToken($ORDERID,$amount,$cus_id);
        if($data){
            $this->response([
                'status'=>TRUE,
                'message'=>'Token Fetched',
                'token'=>$data,
                'orderId'=>$ORDERID,
                //'call_back_url'=>"https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=$ORDERID"
                'call_back_url'=>'http://rerechage.in/api_callback/dmtcallback'
                ]);
        }else{
            $this->response([
                'status'=>FALSE,
                'message'=>'Something Went Wrong'
                ]);
        }
    }

    
    public function getDynamicQrCode_post(){
        
        
        $this->response([
                    'status' => FALSE,
                    'result' => 'Unable to fetch data.'
                ]);
        return;        
        $mobile = $this->post('mobile');
        $name = $this->post('name');
        $cus_id = $this->post('cus_id');
        
        if(empty($mobile)||empty($name)||empty($cus_id)){
           $this->response([
               'status' => FALSE,
               'result' => "Missing Parameters"
               ]); 
        }else{
            $data = $this->Appapi_model->getDynamicQrCode($mobile,$name,$cus_id);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'result' => 'https://newmitra.in/'.$data,
                    'shop_name' => $name
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
    
    
   
    public function CheckPanApplication_post(){
        $cus_id = $this->post('cus_id');
        $data = $this->Appapi_model->CheckPanApplication($cus_id);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'message' =>'successful',
                'pan_application' => $data[0]->pan_application,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' =>'Fail',
                'result' => 'Unable to fetch data.'
            ]);
        }
    }
    
    public function changePanApplicationStatus_post(){
        $cus_id = $this->post('cus_id');
        $txnId = $this->post('txnId');
        $data = $this->Appapi_model->CheckPanApplication($cus_id);
        // print_r($data[0]->account_opening); exit;
        if($data[0]->pan_application == 'unpaid')
        {
            $data = $this->Appapi_model->changePanApplicationStatus($cus_id,$txnId);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'message' =>'successful',
                    'result' => $data
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'message' =>'Fail',
                    'result' => 'Fail To Update.'
                ]);
            }
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' =>'Fail',
                'result' => 'Already Paid.'
            ]);
        }
    }

    public function CheckRailwayApplication_post(){
        $cus_id = $this->post('cus_id');
        $data = $this->Appapi_model->CheckRailwayApplication($cus_id);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'message' =>'successful',
                'railway_application' => $data[0]->railway_application,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' =>'Fail',
                'result' => 'Unable to fetch data.'
            ]);
        }
    }
    
    public function changeRailwayApplicationStatus_post(){
        $cus_id = $this->post('cus_id');
        $txnId = $this->post('txnId');
        $data = $this->Appapi_model->CheckRailwayApplication($cus_id);
        // print_r($data[0]->account_opening); exit;
        if($data[0]->railway_application == 'unpaid')
        {
            $data = $this->Appapi_model->changeRailwayApplicationStatus($cus_id,$txnId);
            if($data)
            {
                $this->response([
                    'status' => TRUE,
                    'message' =>'successful',
                    'result' => $data
                ], REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'message' =>'Fail',
                    'result' => 'Fail To Update.'
                ]);
            }
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' =>'Fail',
                'result' => 'Already Paid.'
            ]);
        }
    }
    
    public function getOfferBanners_post(){
        $data = $this->Db_model->getAlldata('select * from offers');
        if($data)
        {
            //$news_video = $this->Db_model->getAlldata("select offer_video,offer_description,offer_link from user where user_id= '6' ");
            $offer_banner = $this->Db_model->getAlldata("select offer_image from offer_banner");
            $this->response([
                'status' => TRUE,
                'message'=>'Offer  Banner',
                'result' => $data,
                'offer_banner'=>$offer_banner,
                //'news_video'=>$news_video['0']->offer_video,
                //'offer_description'=>$news_video['0']->offer_description,
                //'offer_link'=>$news_video['0']->offer_link
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
    
    public function OfferAmount_post(){
        $cus_id = $this->post('cus_id');
        $offer_id = $this->post('offer_id');
        $amount = $this->post('amount');
        
        
        if(empty($cus_id)||empty($offer_id)||empty($amount)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
            
            $data = $this->Db_model->get_trnx($cus_id);
            $wallet_amt = $data['0']->txn_clbal;
            
            if($wallet_amt >= $amount){
                $data = $this->Appapi_model->OfferAmount($cus_id,$offer_id,$amount);
                if($data)
                {
                    $this->response([
                        'status' => TRUE,
                        'message' =>'successful',
                        
                    ], REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response([
                        'status' => FALSE,
                        'message' =>'Fail',
                        'result' => 'Unable to fetch data.'
                    ]);
                }
            }else{
                $this->response([
                    'status'=>FALSE,
                    'message'=>'Low wallet balance'
                    ]);
            }
        }
    }
   
    public function CheckApplicationStatus_post(){
        $cus_id = $this->post('cus_id');
        $app_type =$this->post('app_type');
        //print_r ($app_type); exit;
        $query = $this->Db_model->getAlldata("SELECT * from application_status_service where cus_id='$cus_id' ");
        $app_amt = $this->Db_model->getAlldata("SELECT * from application_service where app_type = '$app_type' ");
        //echo $this->db->last_query();
        //print_r ($query);
        if($query[0]->$app_type){
            $this->response([
                    'status' => TRUE,
                    $app_type => $query[0]->$app_type,
                    'fees' => $app_amt['0']->Fees
                ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                    'status' => FALSE,
                   $app_type => '0'
                ]);
        }
    }
    
    public function changeApplicationStatus_post(){
        $cus_id = $this->post('cus_id');
        $app_type =$this->post('app_type');
        $txnId = $this->post('txnId');
        $name = $this->post('name');
        $mobile = $this->post('mobile');
        $email = $this->post('email');
        $adhar = $this->post('adhar');
        $pan = $this->post('pan');
        $amount = $this->post('amount');
        $query = $this->Db_model->getAlldata("SELECT * from application_status_service where cus_id='$cus_id'");
        //print_r($query); exit;
        if($query[0]->$app_type == '1')
        {
                $data = $this->Appapi_model->changeApplicationStatus($cus_id,$app_type,$txnId,$name,$mobile,$email,$adhar,$pan,$amount);
                if($data)
                {
                    $this->response([
                        'status' => TRUE,
                        'message' =>'successful',
                        'result' => $data
                    ], REST_Controller::HTTP_OK);
                }
                else
                {
                    $this->response([
                        'status' => FALSE,
                        'message' =>'Already'
                    ]);
                }
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' =>'Fail',
                'result' => 'Already Done.'
            ]);
        }
    }

    public function showApplicationStatus_post()
    {
        $cus_id = $this->post('cus_id');
       // print_r($cus_id);exit;
        $app_type =$this->post('app_type');
        // print_r($app_type);exit;

        $check = $this->Db_model->getAlldata("select * from application_service_purchase where cus_id='$cus_id' and appname='$app_type'");
        //print_r($check);exit;
        $response = array(
            'status'=>$check[0]->status,
            'application_id'=>$check[0]->application_id,
            'txnId' =>$check[0]->txnId,
            'application_purchase_date'=>$check[0]->application_purchase_date,
            'update_datetime'=>$check[0]->update_datetime,
            
            );
            
        if($check)
        {
            $this->response([
            'status' => TRUE,
            'message' =>'Already Registerd.',
            'result' =>$response
            ], REST_Controller::HTTP_OK);   
        }
        else
        {
            $this->response([
            'status' => FALSE,
            'message' =>'Not Registerd.'
            ]);  
        }

    }
}
?>