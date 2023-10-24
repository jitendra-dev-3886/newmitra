<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
ini_set('date.timezone', 'Asia/Calcutta');
//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
//ini_set('error_reporting', 0);

class Dmt extends REST_Controller{
    
    public function __construct() { 
        parent :: __construct();
        $this->load->model(array('Dmt_model'));
    }
    
    public function registerSender_post(){
    
        $mobile = $this->input->post('mobile');
        $name = $this->input->post('name');
        
        $this->form_validation->set_rules('mobile','Mobile Number','required|regex_match[/^[0-9]{10}$/]');
        $this->form_validation->set_rules('name','Name of User','required');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->registerSender($mobile,$name);
            if(is_array($data)){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    public function verifySenderOtp_post(){
        $VerifyReferenceNo = $this->input->post('VerifyReferenceNo');
        $otp = $this->input->post('otp');
        
        $this->form_validation->set_rules('VerifyReferenceNo','Verify Reference Number','required');
        $this->form_validation->set_rules('otp','One Time Password','required');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->verifySenderOtp($VerifyReferenceNo,$otp);
            if(is_array($data)){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    public function verifySender_post(){
        $mobile = $this->input->post('mobile');
        
        $this->form_validation->set_rules('mobile','Mobile Number','required|regex_match[/^[0-9]{10}$/]');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->verifySender($mobile);
            if(is_array($data)){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    
    
    
    
    public function getBankNames_post(){
        $data = $this->Dmt_model->getBankNames();
        if(is_array($data)){
            $this->response([
                'status' => 'true',
                'result' => 'Success',
                'message' => $data,
                ]);
        }else{
            $this->response([
                'status' => 'false',
                'message' => 'Request Failed'
                ]);
        }
    }
    
    public function addBeneficiary_post(){
        $mobile = $this->input->post('mobile');
        $name = $this->input->post('name');
        $bank_acct = $this->input->post('bank_acct');
        $ifsc = $this->input->post('ifsc');
        $bankname = $this->input->post('bankname');
        
        $this->form_validation->set_rules('mobile','Mobile','required|regex_match[/^[0-9]{10}$/]');
        $this->form_validation->set_rules('name','Beneficiary Name','required');
        $this->form_validation->set_rules('bank_acct','Bank Account Number','required');
        $this->form_validation->set_rules('ifsc','IFSC Code','required');
        $this->form_validation->set_rules('bankname','Bank Name','required');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->addBeneficiary($mobile,$name,$bank_acct,$ifsc,$bankname);
            if(is_array($data)){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function getBeneficiary_post(){
        $mobile = $this->input->post('mobile');
        
        $this->form_validation->set_rules('mobile','Mobile','required|regex_match[/^[0-9]{10}$/]');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->getBeneficiary($mobile);
            if($data){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function deleteBeneficiary_post(){
        $mobile = $this->input->post('mobile');
        $bene_code = $this->input->post('bene_code');
        
        $this->form_validation->set_rules('mobile','Mobile','required|regex_match[/^[0-9]{10}$/]');
        $this->form_validation->set_rules('bene_code','Beneficiary Code','required');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->deleteBeneficiary($mobile,$bene_code);
            if($data){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    public function moneyTransfer_post(){
        $adhar_no = $this->input->post('aadhar_no');
        $pan_no = $this->input->post('pan_number');
        $amount = $this->input->post('amount');
        $benificary_code = $this->input->post('bene_code');
        $mobile = $this->input->post('mobile');
        $cus_id = $this->input->post('cus_id');
        
        $this->form_validation->set_rules('mobile','Mobile','required|regex_match[/^[0-9]{10}$/]');
        $this->form_validation->set_rules('bene_code','Beneficiary Code','required');
        $this->form_validation->set_rules('aadhar_no','Aadhar Number','required');
        $this->form_validation->set_rules('pan_number','Pan Number','required');
        $this->form_validation->set_rules('amount','Amount','required|greater_than[0]');
        $this->form_validation->set_rules('cus_id','Customer Id','required');
        
        if($this->form_validation->run()){
            $exr_trnx = $this->Db_model->get_trnx($cus_id);
            $walletBalance = $exr_trnx[0]->txn_clbal;
            
            if($walletBalance >= $amount){
                $data = $this->Dmt_model->moneyTransfer($mobile,$benificary_code,$amount,$adhar_no,$pan_no,$cus_id);
                if($data){
                    $this->response([
                        'status' => 'true',
                        'result' => 'Success',
                        'message' => $data,
                        ]);
                }else{
                    $this->response([
                        'status' => 'false',
                        'message' => 'Request Failed'
                        ]);
                }
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Insufficient Balance'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function getDmtHistory_post(){
        $cus_id = $this->input->post('cus_id');
        
        $this->form_validation->set_rules('cus_id','Customer Id','required');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->getDmtHistory($cus_id);
            if($data){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'No Data Found'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    
    public function accountValidation_post(){
        
        $adhar = $this->input->post('adhar_number');
        $pan = $this->input->post('pan_number');
        $mobile = $this->input->post('mobile_number');
        $acctnumber = $this->input->post('account_number');
        $bankcode = $this->input->post('bank_code');
        
        $this->form_validation->set_rules('adhar_number','Aadhar Number','required');
        $this->form_validation->set_rules('pan_number','Pan Number','required');
        $this->form_validation->set_rules('mobile_number','Mobile','required|regex_match[/^[0-9]{10}$/]');
        $this->form_validation->set_rules('account_number','Bank Account Number','required');
        $this->form_validation->set_rules('bank_code','Bank  Code','required');
        
        if($this->form_validation->run()){
            $data = $this->Dmt_model->accountValidation($adhar,$pan,$mobile,$acctnumber,$bankcode);
            if(is_array($data)){
                $this->response([
                    'status' => 'true',
                    'result' => 'Success',
                    'message' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => 'false',
                    'message' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
    }
    
    public function dmt_commission_slab_post(){

        $cusid = $this->post("cus_id");
        
        $this->form_validation->set_rules('cus_id','Customer Id','required');

        if($this->form_validation->run()){
            $data = $this->Dmt_model->getCustomerCommissionPackage($cusid);
            if(is_array($data)){
                $this->response([
                    'status' => TRUE,
                    'result' => $data,
                    ]);
            }else{
                $this->response([
                    'status' => FALSE,
                    'result' => 'Request Failed'
                    ]);
            }
        }else{
            $this->response([
                'status' => FALSE,
                'result' => validation_errors()
                ]);
        }
    }
    
}
?>