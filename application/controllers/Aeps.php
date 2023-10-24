<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
ini_set('date.timezone', 'Asia/Calcutta');
//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
//ini_set('error_reporting', 0);

class Aeps extends REST_Controller{
    private $api_mobile = '8630450571';
    private $api_pass = '722093';
    private $api_user_id = '143';
    private $api_reg_ip = '92.204.134.236';
    private $msg="8630450571-722093-143-92.204.134.236"; 
    public function __construct() { 
        parent :: __construct();
        $this->load->model(array('Aeps_model','db_model','encryption_model'));
    }
    public function getbank_post(){
        $bank = $this->Aeps_model->getbank_list();
        if(is_array($bank)){
            $this->response([
                'status' => TRUE,
                'message' => 'Bank Found',
                'result' => $bank
                ]);
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'Bank Not Found'
                ]);
        }
    }
    
    public function getstate_post(){
        $bank = $this->Aeps_model->getstate();
        if(is_array($bank)){
            $this->response([
                'status' => TRUE,
                'message' => 'State Found',
                'result' => $bank
                ]);
        }else{
            $this->response([
                'status' => FALSE,
                'message' => 'State Not Found'
                ]);
        }
    }
    
    
    public function whatsappno_post(){
        $mobile_no = "9472684030";//9430696478
        $this->response([
                            'status' => TRUE,
                            'message' => "Whatsapp mobile no.",
                            'result' => $mobile_no
                            ]);
    }
    
    public function getPidData_post(){
        $pidData = $_POST['PidData'];
        $PidOption = $_POST['PidOption'];
        $this->Db_model->insert_update('aeps_transaction_fch',array('pidData'=>$pidData,'PidOption'=>$PidOption));
    }
    
    
    public function onboarding_post()
    {
        
        if(empty($_POST['cus_id']) && empty($_POST['latitude']) && empty($_POST['longitude']) && empty($_POST['merchantName']) && empty($_POST['merchantPhoneNumber']) && empty($_POST['emailId']) && empty($_POST['userPan']) && empty($_POST['aadhaarNumber']) && empty($_POST['bankBranchName']) && empty($_POST['bankAccountName'])){
            $this->response([
                'status' => FALSE,
                'message' => "Missing Argument"
            ]);
        }
        else{
        $CURLOPTOTP_URL="https://tiktikpay.in/Api_client/onboarding";
        $param['cus_id']=$cus_id=$_POST['cus_id'];
        
        $param['username']="Tiktikd";
        $param['password']="md5('1234d')";
        $param['latitude']="18.512310";//$latitude,
        $param['longitude']="73.878790";//$longitude,
        $param['supermerchantId']="931";
        
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];
        $param['merchantName']=$merchantName=$_POST['merchantName'];
        $param['merchantPhoneNumber']=$merchantPhoneNumber=$_POST['merchantPhoneNumber'];
        $param['companyLegalName']=$companyLegalName='';
        $param['companyMarketingName']=$companyMarketingName ='';
        $param['emailId']=$emailId=$_POST['emailId'];
        $param['merchantPinCode']=$merchantPinCode=$_POST['merchantPinCode'];
        $param['merchantCityName']=$merchantCityName=$_POST['merchantCityName'];
        $param['tan']=$tan='';
        $param['merchantDistrictName']=$merchantDistrictName=$_POST['merchantDistrictName'];
        $param['merchantState']=$merchantState=$_POST['merchantState'];
        $param['merchantAddress']=$merchantAddress=$_POST['merchantAddress'];
        $param['userPan']=$userPan=$_POST['userPan'];
        $param['aadhaarNumber']=$aadhaarNumber=$_POST['aadhaarNumber'];
        $param['gstInNumber']=$gstInNumber='';
        $param['companyOrShopPan']=$companyOrShopPan='';
        $param['companyBankAccountNumber']=$companyBankAccountNumber=$_POST['companyBankAccountNumber'];
        $param['bankIfscCode']=$bankIfscCode=$_POST['bankIfscCode'];
        $param['companyBankName']=$companyBankName=$_POST['companyBankName'];
        $param['bankAccountName']=$bankAccountName=$_POST['bankAccountName'];
        $param['bankBranchName']=$bankBranchName=$_POST['bankBranchName'];
        
            $cancellationCheckImages="";
            $shopAndPanImage="";$ekycDocuments="";
            
            if(!empty($_POST['cancellationCheckImages'])){
                
                $image = base64_decode($_POST['cancellationCheckImages']);
                $imagename = $_SERVER['DOCUMENT_ROOT']."/includes/uploads/kyc/".$cus_id."_cancellationCheckImages.jpeg";
                file_put_contents($imagename, $image);
                $param['']=$cancellationCheckImages = "https://easypayall.in/".$imagename;
            }
            if(!empty($_POST['shopAndPanImage'])){
                $image = base64_decode($_POST['shopAndPanImage']);
                $imagename = $_SERVER['DOCUMENT_ROOT']."/includes/uploads/kyc/".$cus_id."_shopAndPanImage.jpeg";
                file_put_contents($imagename, $image);
                $param['']=$shopAndPanImage = "https://easypayall.in/".$imagename;
            }
            if(!empty($_POST['ekycDocuments'])){
                $image = base64_decode($_POST['ekycDocuments']);
                $imagename = $_SERVER['DOCUMENT_ROOT']."/includes/uploads/kyc/".$cus_id."_ekycDocuments.jpeg";
                file_put_contents($imagename, $image);
                $param['ekycDocuments'] = $ekycDocuments = "https://easypayall.in/".$imagename;
            }
           
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            // $param['merchantLoginId']=$merchantLoginId = '';$merchantLoginPin='';
           
            $param['merchantLoginId']=$merchantLoginId = 'EasyPay'.$cus_id;
            $param['merchantLoginPin']=$merchantLoginPin = 'EasyPay'.$cus_id;
            
            foreach($param as $key=>$val) { 
    			$request.= $key."=".urlencode($val); 
    			$request.= "&"; 
    		}
            
            
            $header = [         
                    'Content-Type:application/x-www-form-urlencoded',             
                    'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                    'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                ];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $CURLOPTOTP_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true, 
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => $header
                ));
                
            $response_data = curl_exec($curl);
            $response = json_decode($response_data, true);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
                            
            $data = $response;
            //saving data
                $recd=array('ekyrequest'=>json_encode($values));
                $this->db_model->insert_update('customers',$recd,array('cus_id'=>$cus_id));
                $recd=array('ekycresponse'=>json_encode($data));
                $this->db_model->insert_update('customers',$recd,array('cus_id'=>$cus_id));
            //end
            
            if($data['data']['merchants'][0]['status']='Successfully Created' || $data['data']['merchants'][0]['status']='Successfully Updated')
            {
                $record=array(  'aeps_merchantLoginId'=>$this->encryption_model->encode($merchantLoginId),
                                'aeps_merchantLoginPin'=>$this->encryption_model->encode($merchantLoginPin),
                                'aeps_userPan'=>$userPan,
                                'aeps_aadhaarNumber'=>$aadhaarNumber,
                                'aeps_AccountNumber'=>$companyBankAccountNumber,
                                'aeps_bankIfscCode'=>$bankIfscCode,
                                'aeps_bankAccountName'=>$bankAccountName,
                                'bankName'=>$companyBankName,
                                // "aeps_kyc_status"=>"KYC Completed"
                                );
        		$insis=$this->db_model->insert_update('customers',$record,array('cus_id'=>$cus_id));
    		        
		        $this->response([
                        'status' => TRUE,
                        'message' => "Onboarding Done",
                        'result' => $response
                        ]);
        }
        else{
                  $this->response([
                            'status' => FALSE,
                            'message' => "KYC Not Done",
                            'result' => ''
                            ]);
            }
        }
    }
    
    public function ekycsendotp_post()
    {
        if(empty($_POST['cus_id'])){
            $this->response([
                    'status' => FALSE,
                    'message' => "Missing Customer ID Argument"
                    ]);
        }
        else{
            $cus_id=$_POST['cus_id'];
            $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
            $param['merchantLoginId']=$merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['merchantPhoneNumber']=$merchantPhoneNumber=$this->encryption_model->decode($data['profile'][0]->cus_mobile);
            $param['aadhaarNumber']=$aadhaarNumber=$data['profile'][0]->aeps_aadhaarNumber;
            $param['userPan']=$userPan=$data['profile'][0]->aeps_userPan;
            
            foreach($param as $key=>$val) { 
    			$request.= $key."=".urlencode($val); 
    			$request.= "&"; 
    		}
    		
            $header = [         
                    'Content-Type:application/x-www-form-urlencoded',             
                    'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                    'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                ];
                
                
            
            $CURLOPTOTP_URL="https://tiktikpay.in/index.php/api_partner/updateaepsekyc";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                    CURLOPT_URL => $CURLOPTOTP_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true, 
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => $header
                ));
                
            $response_data = curl_exec($curl);
            $response = json_decode($response_data, true);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
    		
    		    if($response['status']=='true'){
    		        $record=array(  
    		                    'primaryKeyId'=>$response['data']['primaryKeyId'],
                                'encodeFPTxnId'=>$response['data']['encodeFPTxnId'],
                                );
                                
        		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$cus_id));
        		    $this->response([
                            'status' => TRUE,
                            'message' => "OTP Send Successfully",
                            'result' => $response
                            ]);
                	
    		    }
        	    else{
                   $this->response([
                            'status' => FALSE,
                            'message' => "Can Not Send OTP",
                            'result' => $response
                            ]);
                	}
        }
        
    }
    
    public function validateekycotp_post()
    {
        $otp = $_POST['otp'];
        $cus_id = $_POST['cus_id'];
        $CURLOPTOTP_URL="https://tiktikpay.in/index.php/api_partner/aepskycvalidateotp";
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['primaryKeyId']=$primaryKeyId=$data['profile'][0]->primaryKeyId;
            $param['encodeFPTxnId'] = $encodeFPTxnId=$data['profile'][0]->encodeFPTxnId;
            $param['otp'] = $otp;
		    $param['cus_id'] = $cus_id;
		    
            
            foreach($param as $key=>$val) { 
    			$request.= $key."=".urlencode($val); 
    			$request.= "&"; 
    		}
    		
    		
            $header = [         
                    'Content-Type:application/x-www-form-urlencoded',             
                    'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                    'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                ];
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                    CURLOPT_URL => $CURLOPTOTP_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true, 
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => $header
                ));
                
            echo $response_data = curl_exec($curl);
            $response = json_decode($response_data, true);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
		  //  $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
		    
      
		    if($response['status']=='true'){
		        $record=array("aeps_kyc_status"=>"KYC Completed");
    		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$cus_id));
		        $this->response([
                'status' => TRUE,
                'message' => $response['message']
                ]);
		    }
		    else{
		        $this->response([
                'status' => false,
                'message' => $response['message']
                ]);
		    }
    }
    
    public function resendekycotp_post()
    {
        $param['cus_id'] = $cus_id = $_POST['cus_id'];
        $CURLOPTOTP_URL="https://tiktikpay.in/index.php/api_partner/aepskycresendotp";
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['primaryKeyId'] = $primaryKeyId=$data['profile'][0]->primaryKeyId;
            $param['encodeFPTxnId'] = $encodeFPTxnId=$data['profile'][0]->encodeFPTxnId;
		    
            foreach($param as $key=>$val) { 
    			$request.= $key."=".urlencode($val); 
    			$request.= "&"; 
    		}
            
            // print_r($request);exit;
            
            $header = [         
                    'Content-Type:application/x-www-form-urlencoded',             
                    'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                    'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                ];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $CURLOPTOTP_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true, 
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => $header
                ));
                
            $response_data = curl_exec($curl);
            $response = json_decode($response_data, true);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
		  //  $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
		    
		    if($response['status']=='true'){
		        $this->response([
                'status' => TRUE,
                'message' => $response['message']
                ]);
		    }
		    else{
		        $this->response([
                'status' => false,
                'message' => $response['message']
                ]);
		    }
    }
    
    public function ekycsubmit_post()
    {
            $CURLOPTOTP_URL="https://tiktikpay.in/index.php/Api_client/ekycsubmit";
		    
		    $param['requestRemarks']=$requestRemarks=$_POST['requestRemarks'];
		    $param['userPan']=$userPan=$_POST['userPan'];
            $param['aadhaarNumber']=$adhaarNumber=$_POST['aadhaarNumber'];
		    $param['txtPidData'] = $_POST['txtPidData'];
            $param['PidOptions']=$PidOptions=$_POST['PidOptions'];
            $param['cus_id']=$cus_id=$_POST['cus_id'];
            // $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
            // $piData = $xmlCaptureResponse->Data;
            // $Hmac = $xmlCaptureResponse->Hmac;
            // $Skey = $xmlCaptureResponse->Skey;
            
            preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
            preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVerss=str_replace('"',"",$matches[1][0]); $rdsVer=str_replace('>','',$rdsVerss);
            preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
    	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScoress=str_replace('"',"",$matches[1][0]); $qScore=str_replace('\/><\/PidData>','',$qScoress);
    	    
    	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
    	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
    	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
    	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
    	    
    	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
    	    $x=explode(">",$PidData);
    	    $PidDatatype=$x[0];
    	    
    	    $cidata=explode(">",$cida);
    	    $ci=$cidata[0];
    	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
	        
	        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['primaryKeyId']=$primaryKeyId=$data['profile'][0]->primaryKeyId;
            $param['encodeFPTxnId']=$encodeFPTxnId=$data['profile'][0]->encodeFPTxnId;
            
            $values=array(   
		        "superMerchantId"=>'953',
		        "merchantLoginId"=> $merchantLoginId,
                "primaryKeyId"=> $primaryKeyId,
                "encodeFPTxnId"=> $encodeFPTxnId,
                "requestRemarks"=> $requestRemarks,
                "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>null),
                "captureResponse"=>array(
                                "errCode"=>$errCode,
                                "errInfo"=>$errInfo,
                                "fType"=>$fType,
                                "fCount"=>$fCount,
                                "iCount"=>$iCount,
                                "pCount"=>$pCount,
                                "pType"=>$pType,
                                "nmPoints"=>"$nmPoints",
                                "qScore"=>"$qScore",
                                "dpID"=>$dpId,
                                "rdsID"=>$rdsId,
                                "rdsVer"=>$rdsVer,
                                "dc"=>"$dc",
                                "mc"=>"$mc",
                                "mi"=>$mi,
                                "ci"=>"$ci",
                                "sessionKey"=>"$Skey", 
                                "hmac"=>"$Hmac",
                                "PidDatatype"=>$PidDatatype,
                                "Piddata"=>"$piData",
                            )
        );
        
            foreach($param as $key=>$val) { 
    			$request.= $key."=".urlencode($val); 
    			$request.= "&"; 
    		}
    		
            $header = [         
                    'Content-Type:application/x-www-form-urlencoded',             
                    'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                    'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                ];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $CURLOPTOTP_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true, 
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => $header
                ));
                
            $response_data = curl_exec($curl);
            $response = json_decode($response_data, true);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            // $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
            $recd=array('ekyrequest'=>json_encode($values));
            $this->db_model->insert_update('customers',$recd,array('cus_id'=>$cus_id));
            $recd=array('ekycresponse'=>json_encode($response));
            $this->db_model->insert_update('customers',$recd,array('cus_id'=>$cus_id));
            
            if($response['status']=='true'){
                $record=array(  
		                'aeps_kyc_status'=>'KYC Completed',
		                    'newaepskyc_status'=>'done'
                            );
                            
    		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$cus_id));
    		    
		        $this->response([
                'status' => TRUE,
                'message' => $response['message']
                ]);
		    }
		    else{
		        $this->response([
                'status' => false,
                'message' => $response['message']
                ]);
		    }
            
    }
	
	public function aepsapi_post()
	{

	    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$_POST['cus_id']));
                $aeps_merchantLoginId=$data['profile'][0]->aeps_merchantLoginId;
                $aeps_merchantLoginPin=$data['profile'][0]->aeps_merchantLoginPin;
                $cus_name=$data['profile'][0]->cus_name;
                $cus_mobile=$data['profile'][0]->cus_mobile;
                
	    if(!empty($_POST['cus_id'])){
	    
	        $request = "";
	        $msg=$this->msg;  
	        
    	    $param['msg'] = $msg;
    	    $param['txtPidData'] = $_POST['txtPidData'];
    		$param['adhaarNumber'] = $_POST['adhaarNumber'];
    		$param['nationalBankIdenticationNumber'] = $_POST['nationalBankIdenticationNumber'];
    		$param['mobileNumber'] = $_POST['mobileNumber'];
    		$param['transactionAmount'] = $_POST['transactionAmount'];
    		$param['type'] = $_POST['type'];
    		$param['cus_id'] =  $this->api_user_id;
    		$param['cus_name'] = $cus_name;
    		$param['cus_mobile'] = $this->encryption_model->decode($cus_mobile);
    		$param['merchantLoginId'] = $this->encryption_model->decode($aeps_merchantLoginId);
    		$param['merchantLoginPin'] = $this->encryption_model->decode($aeps_merchantLoginPin);
    		
    		foreach($param as $key=>$val) 
    		{ 
    			$request.= $key."=".urlencode($val); 
    			$request.= "&"; 
    		}
    // 		echo $request;
            $header = [         
                    'Content-Type:application/x-www-form-urlencoded',             
                    'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                    'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                ];
            
                
            $CURLOPT_URL='https://tiktikpay.in/index.php/api_partner/aepsapi';
            
            // print_r($request);exit;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                    CURLOPT_URL => $CURLOPT_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => true, 
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => $header
                ));
                
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            
            $this->db_model->insert_update('aeps_transaction_fch',array('request'=>$request,'response'=>$response));
            
            $array = json_decode($response, true);
            if($array['status'])
            {
                $balanceAmount='0';$traamt='0';
                if($_POST['type']=='ministatement'){
                   $balanceAmount='0';
                   $ministmt1=json_encode($array['ministatement']);
                   $transactionStatus=$array['result']['transactionStatus'];
                   $tratype='MS';
                   $terminalId='';$requestTransactionTime='';
                   $merchantTransactionId='';
                   $url='https://easypayall.in/Retailer/ministmtaeps_print/'.$inserId;
                }
                else{
                    $balanceAmount=$array['result']['balanceAmount'];
                    $traamt=$array['result']['transactionAmount'];
                    $tratype=$array['result']['transactionType'];
                    $transactionStatus=$array['result']['transactionStatus'];
                    $terminalId=$array['result']['terminalId'];
                    $requestTransactionTime=$array['result']['requestTransactionTime'];
                    $merchantTransactionId=$array['result']['merchantTransactionId'];
                }
                // if($array['result']['transactionStatus']=='successful')
                // {
                    $cus_id=$_POST['cus_id'];
                    $inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('aadhar_number'=>$_POST['adhaarNumber'],'balanceAmount'=>$balanceAmount,'ministatement'=>$ministmt,'aeps_bank_id'=>$_POST['nationalBankIdenticationNumber'],'amount'=>$traamt,'status'=>$transactionStatus,'transactionType'=>$tratype,'transaction_ref_id'=>$array['result']['fpTransactionId'],'utr'=>$array['result']['bankRRN'],'apiclid'=>$cus_id,'through'=>'App'));
                    // echo $this->db->last_query();
                    // echo "<br>";
                    $inserId = $this->db->insert_id();
                    $amount = $array['result']['transactionAmount'];
                    
                    if($array['result']['transactionType']=='CW')
                    {
                        $cus_aeps_bal_data=$this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='$cus_id' ORDER BY aeps_txn_id DESC LIMIT 1");
                        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                        $txntype = 'AEPS Cash Withdrawal';
                        $total = $cus_aeps_bal + $amount;
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    
                        $this->creditAepsCommission($cus_id,$amount,$inserId);  
                        $print_url='https://easypayall.in/Retailer/cashwithdrawalaeps_print/'.$inserId;
                    }
                    if($array['result']['transactionType']=='M')
                    {
                        $cus_aeps_bal_data=$this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='$cus_id' ORDER BY aeps_txn_id DESC LIMIT 1");
                        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                        $txntype = 'AEPS Cash Withdrawal';
                        $total = $cus_aeps_bal + $amount;
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    
                        $this->creditAdharPayCommission_get($cus_id,$amount,$inserId);
                        $print_url='https://easypayall.in/Retailer/cashwithdrawalaeps_print/'.$inserId;
                    }
                    if($array['result']['transactionType']=='MS')
                    {
                        if($transactionStatus =='successful'){
                            $ministmt = json_decode($ministmt1,true);
                                
                                        for($i=0;$i<count($ministmt);$i++){ 
                                            if($i==0){
                                                    $dbdate=$ministmt[$i]['date'];
                                                    $dbtratype=$ministmt[$i]['txnType'];
                                                    $dbamt=$ministmt[$i]['amount'];
                                                    $dbnaration=$ministmt[$i]['narration'];
                                                }
                                                else{
                                                    
                                                    $dbdate=$dbdate.','.$ministmt[$i]['date'];
                                                    $dbtratype=$dbtratype.','.$ministmt[$i]['txnType'];
                                                    $dbamt=$dbamt.','.$ministmt[$i]['amount'];
                                                    $dbnaration=$dbnaration.','.$ministmt[$i]['narration'];
                                                }
                                            }
                                        $this->db_model->insert_update('aeps_transaction_fch',array('message'=>$array['message'],'msdate'=>$dbdate,'mstratype'=>$dbtratype,'msamount'=>$dbamt,'msnaration'=>$dbnaration,'device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>'0','status'=>$array['data']['transactionStatus'],'transactionType'=>$array['data']['transactionType'],'transaction_ref_id'=>$array['data']['fpTransactionId'],'utr'=>$array['data']['bankRRN'],'apiclid'=>$cus_id,'through'=>'API PARTNER'),array('aeps_id'=>$inserted_id));
                                        // echo $this->db->last_query();
                                        $inserId = $this->db->insert_id();
                                        $url='https://easypayall.in/Retailer/ministmtaeps_print/'.$inserId;
                                
                            }
                    }
                    
                    $this->response([
                                'status' => TRUE,
                                'message' => $array['message'],
                                'result' => [
                                                "transactionAmount"=>$traamt,
                                                "terminalId"=>$terminalId,
                                                "device"=>$array['result']['device'],
                                                "requestTransactionTime"=>$requestTransactionTime,
                                                "transactionStatus"=>$array['result']['transactionStatus'],
                                                "balanceAmount"=>$balanceAmount,
                                                "bankRRN"=>$array['result']['bankRRN'],
                                                "transactionType"=>$tratype,
                                                "fpTransactionId"=>$array['result']['fpTransactionId'],
                                                "merchantTransactionId"=>$merchantTransactionId,
                                            ],
                                'cus_id'=>$_POST['cus_id'],
                                'ministatement' =>$array['ministatement'],
                                'outletname' => $array['outletname'],
                                'outletmobile' => $array['outletmobile'],
                                'url'=>$url
                                ]);
            }
            else
            {
                $this->response($array);
            }
	    }else
            {
                $this->response([
                    'status' => FALSE,
                    'result' => 'Customer ID are Required',
                    'cus_id'=>$_POST['cus_id'],
                ]);
            }
            
	}
	public function getPayoutDetails_post(){
	    $cus_id = $_POST['cus_id'];
	    
	    $this->form_validation->set_rules('cus_id','Customer Id','required');
	    if($this->form_validation->run()){
	        $response=$this->Aeps_model->getPayoutDetails($cus_id);
	        if($response){
	            $this->response([
                    'status' => TRUE,
                    'message' => 'Data Found',
                    'result' => $response
                    ]); 
	        }else{
	           $this->response([
                    'status' => FALSE,
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
	
	public function getCharge_post(){
	    $cus_id = $_POST['cus_id'];
	    $amount = $_POST['amount'];
	    
	    $this->form_validation->set_rules('amount','Amount','required');
	    $this->form_validation->set_rules('cus_id','Customer Id','required');
	    if($this->form_validation->run()){
	        $charge = $this->Aeps_model->getPayoutCharge($amount,$cus_id);
            $this->response([
                'status' => TRUE,
                'message' => 'Data Found',
                'result' => $charge
                ]); 
	    }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
	}
	
	public function aepsBalance_post(){
	
	    $response = $this->Aeps_model->aepsBalance($cus_id);
	    $aeps_bal = $response[0]->aeps_txn_clbal;
	    if($response){
    	            $this->response([
                        'status' => TRUE,
                        'message' => $aeps_bal
                        ]); 
    	        }else{
    	           $this->response([
                        'status' => FALSE,
                        'message' => 'Invalid Customer Id'
                        ]); 
    	        }
	}
	
	public function submitPayout_post(){
	    $cus_id = $_POST['cus_id'];
	    $bank_name = $_POST['bank_name'];
	    $account_number = $_POST['account_number'];
	    $ifsc_code = $_POST['ifsc_code'];
	    $account_holder_name = $_POST['account_holder_name'];
	    $amount = $_POST['amount'];
	    $charge = $_POST['charge'];
	    $type = $_POST['type'];
	    
	    $this->form_validation->set_rules('cus_id','Customer Id','required');
    	$this->form_validation->set_rules('type','Type','required');
	    $this->form_validation->set_rules('amount','Amount','required');
	    
	    if($this->form_validation->run()){
	        
	        $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
	        
	        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
	        
	        if($cus_aeps_bal >= ($amount+$charge) ){
    	        $response=$this->Aeps_model->submitPayout($cus_id,$bank_name,$account_number,$ifsc_code,$account_holder_name,$amount,$charge,$type);
    	        if($response){
    	            $this->response([
                        'status' => TRUE,
                        'message' => 'Request Submitted Successfully'
                        ]); 
    	        }else{
    	           $this->response([
                        'status' => FALSE,
                        'message' => 'Failed to Submit Request'
                        ]); 
    	        }
	        }else{
                $this->response([
                    'status' => FALSE,
                    'message' => "Insufficient Balance"
                    ]);
            }
	    }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
	}
	
	public function payoutHistory_post(){
	    $cus_id = $_POST['cus_id'];
	    
	    $this->form_validation->set_rules('cus_id','Customer Id','required');
	    if($this->form_validation->run()){
	        $response=$this->Aeps_model->payoutHistory($cus_id);
	        if($response){
	            $this->response([
                    'status' => TRUE,
                    'message' => 'Data Found',
                    'result' => $response
                    ]); 
	        }else{
	           $this->response([
                    'status' => FALSE,
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
	
	public function microAtmDetails_post(){
	    $cus_id = $_POST['cus_id'];
	    
	    $this->form_validation->set_rules('cus_id','Customer Id','required');
	    if($this->form_validation->run()){
	        $response=$this->Aeps_model->microAtmDetails($cus_id);
	        if($response){
	            $this->response([
                    'status' => TRUE,
                    'message' => 'Data Found',
                    'result' => $response
                    ]); 
	        }else{
	           $this->response([
                    'status' => FALSE,
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
	
	public function submitMicroAtmResponse_post(){
	    $cus_id = $_POST['cus_id'];
	    $status = $_POST['status'];
	    $response = $_POST['response'];
	    $transAmount = $_POST['transAmount'];
	    $balAmount = $_POST['balAmount'];
	    $bankRrn = $_POST['bankRrn'];
	    $transType  = $_POST['transType'];
	    $type = $_POST['type'];
	    $cardNum = $_POST['cardNum'];
	    $bankName = $_POST['bankName'];
	    $cardType = $_POST['cardType'];
	    $terminalId = $_POST['terminalId'];
	    $fpId  = $_POST['fpId'];
	    $transId  = $_POST['transId'];
	    
	    $this->form_validation->set_rules('cus_id','Customer Id','required');
	    $this->form_validation->set_rules('status','Status','required');
	    $this->form_validation->set_rules('response','Response','required');
	    $this->form_validation->set_rules('transAmount','Transaction Amount','required');
	    $this->form_validation->set_rules('balAmount','Balance Amount','required');
	    $this->form_validation->set_rules('bankRrn','Bank RRN','required');
	    $this->form_validation->set_rules('transType','Transaction Type','required');
	    $this->form_validation->set_rules('type','Type','required');
	    $this->form_validation->set_rules('cardNum','Card Number','required');
	    $this->form_validation->set_rules('bankName','Bank Name','required');
	    $this->form_validation->set_rules('cardType','Card Type','required');
	    $this->form_validation->set_rules('terminalId','Terminal Id','required');
	    $this->form_validation->set_rules('fpId','FP Id','required');
	    $this->form_validation->set_rules('transId','Transaction Id','required');
	    
	    if($this->form_validation->run()){
	        $response=$this->Aeps_model->submitMicroAtmResponse($cus_id,$status,$response,$transAmount,$balAmount,$bankRrn,$transType,$type,$cardNum,$bankName,$cardType,$terminalId,$fpId,$transId);
	        if($response){
	            $this->response([
                    'status' => TRUE,
                    'message' => 'Data Submitted Successfully'
                    ]); 
	        }else{
	           $this->response([
                    'status' => FALSE,
                    'message' => 'Failed to Submit Request'
                    ]); 
	        }
	    }else{
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
                ]);
        }
	}
	
	public function aeps_history_post(){

        $cusid = $this->post("cus_id");

            if(empty($cusid)){
                
                $this->response([
                    'status' => FALSE,
                    'result' => 'Missing parameters.'
                ]);
    
            }else{
            $data = $this->Aeps_model->aeps_history($cusid);
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
            $data = $this->Aeps_model->aeps_commission_slab($cusid);
            if(is_array($data))
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
                    'result' => 'Package Not Assigned.'
                ]);
            }
        }
    }
    
    public function aeps_ledger_post(){

        $cusid = $this->post("cus_id");

            if(empty($cusid)){
                
                $this->response([
                    'status' => FALSE,
                    'result' => 'Missing parameters.'
                ]);
    
            }else{
            $data = $this->Aeps_model->aeps_ledger($cusid);
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
    public function creditAepsCommission($cus_id,$amount,$aeps_id){
        
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_aeps WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
                if($commission_scheme_package){
                    if($cus_type == "retailer"){
                        foreach($commission_scheme_package as $comm){
                            $minRange = $comm->amount_min_range;
                            $maxRange = $comm->amount_max_range;
                            if($amount >= $minRange && $amount <= $maxRange){
                                
                                $retailer_comm = $comm->retailer_comm; 
                                $comm_type = $comm->type;
                                if($retailer_comm){
                                    if($comm_type == "percent"){
                                        $commission_amount = round((($retailer_comm / 100) * $amount),3);
                                    }else{
                                        $commission_amount = $retailer_comm;
                                    }
                                    $last_balance = $this->Aeps_model->aepsBalance($cus_id);
                                    $this->db_model->insert_update('aeps_transaction_fch', array('retailer_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
                                    
                					$txntype = 'Retailer Commission';
                                    if($last_balance){
                                        $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                    }else{
                                        $cus_aeps_bal = '0';
                                    }
                                    
                                    $total = $cus_aeps_bal + $commission_amount;
                                    
                            		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$commission_amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                                    
            							
                                }
                                
                                // Distributor Commission
                                $distributor_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_id'");
                                $cus_reffer = $distributor_reffer[0]->cus_reffer;
                                if($cus_reffer && $cus_reffer != 0 ){
                                    $distributor_comm = $comm->distributor_comm;
                                    $comm_type = $comm->type;
                                    if($distributor_comm){
                                        if($comm_type == "percent"){
                                            $dist_commission = round((($distributor_comm / 100) * $amount),3);
                                            $dist_commission_amount = $dist_commission - $commission_amount;
                                        }else{
                                            $dist_commission_amount = $distributor_comm - $commission_amount;
                                        }
                                        $last_balance = $this->Aeps_model->aepsBalance($cus_reffer);
                                        
                                        if($last_balance){
                                            $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                        }else{
                                            $cus_aeps_bal = '0';
                                        }
                                        
                                        $this->db_model->insert_update('aeps_transaction_fch', array('distributor_commission'=>$dist_commission_amount),array('aeps_id'=>$aeps_id));
                                        
                						$txntype = 'Distributor Commission';
                                        $total = $cus_aeps_bal + $dist_commission_amount;
                                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_reffer,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$commission_amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    							
                                        }
                                }
                                
                                // Master Commission
                                $master_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_reffer'");
                                $master_cus_reffer = $master_reffer[0]->cus_reffer;
                                if($master_cus_reffer && $master_cus_reffer != 0 ){
                                    $master_comm = $comm->master_comm;
                                    $comm_type = $comm->type;
                                    if($master_comm){
                                        if($comm_type == "percent"){
                                            $mast_commission = round((($master_comm / 100) * $amount),3);
                                            $mast_commission_amount = $mast_commission - ($dist_commission_amount + $commission_amount);
                                        }else{
                                            $mast_commission_amount = $master_comm - ($dist_commission_amount + $commission_amount);
                                        }
                                        $mast_commission_amount;
                                       $last_balance = $this->Aeps_model->aepsBalance($master_cus_reffer);
                                        
                                        $this->db_model->insert_update('aeps_transaction_fch', array('master_commission'=>$mast_commission_amount),array('aeps_id'=>$aeps_id));
                                        
                                        if($last_balance){
                                            $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                        }else{
                                            $cus_aeps_bal = '0';
                                        }
                                        
                						$txntype = 'Master Commission';
                                        $total = $cus_aeps_bal + $mast_commission_amount;
                                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$master_cus_reffer,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$mast_commission_amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                							
                                    }
                                }
                                
                                
                            }
                        }
                    }
                }
            }else{
                return false;
            }
        }
    }
    
    public function creditAdharPayCommission_get($cus_id,$amount,$aeps_id){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_adharpay WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
                if($commission_scheme_package){
                    if($cus_type == "retailer"){
                        
                        $retailer_comm = $commission_scheme_package[0]->retailer_comm; 
                        $comm_type = $commission_scheme_package[0]->type;
                        if($retailer_comm){
                            
                            if($comm_type == "percent"){
                                $commission_amount = round((($retailer_comm / 100) * $amount),3);
                            }else{
                                $commission_amount = $retailer_comm;
                            }
                            $last_balance = $this->Aeps_model->aepsBalance($cus_id);
                            $this->db_model->insert_update('aeps_transaction_fch', array('retailer_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
                            
        					$txntype = 'Retailer Aadhar Pay Charge';
                            if($last_balance){
                                $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                            }else{
                                $cus_aeps_bal = '0';
                            }
                            
                            $total = $cus_aeps_bal - $commission_amount;
                            
                    		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>$commission_amount,'aeps_txn_crdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                            
    							
                        }
                        
                    }
                }
            }else{
                return false;
            }
        }
    }
    
    
    
    
}
?>