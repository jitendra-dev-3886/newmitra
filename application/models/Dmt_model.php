<?php
if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!'); 

class Dmt_model extends CI_Model
{
    private $api_mobile = '7015871002';
    private $api_pass = '12135';
    private $api_user_id = '563';
    private $api_reg_ip = '162.215.209.105';

	public function __construct(){
        parent:: __construct();
        $this->load->model(array('Db_model','Encryption_model'));
    }
   
    
    public function registerSender($mobile,$name){
        
        $api_mobile = $this->api_mobile;
        $api_pass = $this->api_pass;
        $api_user_id = $this->api_user_id;
        $api_reg_ip = $this->api_reg_ip;
        $id = rand(1000000000,9999999999);
        $data = "msg=E06007~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~$name~NA~NA";
        $url= 'https://edigitalvillage.net/index.php/api_partner/registerDmtUser';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"$url");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"$data");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        $info = curl_getinfo($ch);
        $err = curl_error($ch);
        curl_close ($ch);
        $http_code = $info['http_code'];
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            return array('status'=>$dataArr[5],'api_msg'=>$dataArr[6],'VerifyReferenceNo'=>$dataArr[4]);
        }else{
            return "Unable To get Response";
        }

    }
    
    public function verifySenderOtp($VerifyReferenceNo,$otp){
            
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            $id = rand(1000000000,9999999999);
            $data = "msg=E06021~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$VerifyReferenceNo~$otp~NA~NA";
            $url= 'https://edigitalvillage.net/index.php/api_partner/verifySenderOtp';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"$url");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "$data");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            $info = curl_getinfo($ch);
            $err = curl_error($ch);
            curl_close ($ch);
        
        $http_code = $info['http_code'];
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            return array('status'=>$dataArr[5],'api_msg'=>$dataArr[6]);;
        }else{
            return "Unable To get Response";
        }

    }
     
    public function verifySender($mobile){
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            
            $id = rand(1000000000,9999999999);
            $data = "msg=E06007~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~NA~NA";
            $url= 'https://edigitalvillage.net/index.php/api_partner/checkIfDmtUserExists';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"$url");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "$data");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            $info = curl_getinfo($ch);
            $err = curl_error($ch);
            curl_close ($ch);
        $http_code = $info['http_code'];
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            return array('status'=>$dataArr[5],'api_msg'=>$dataArr[6]);
        }else{
            return "Unable To get Response";
        }

    }
    
    public function getBankNames(){
        $banks = $this->Db_model->getAlldata("Select * from bank");
        if($banks){
            return $banks;
        }else{
            return false;
        }
    }
    
    public function addBeneficiary($mobile,$name,$bank_acct,$ifsc,$bankcode){
        
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
        
                $id = rand(1000000000,9999999999);
                $data = "msg=E06009~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~$name~$bank_acct~$ifsc~$bankcode~NA";
                $url= 'https://edigitalvillage.net/index.php/api_partner/addBeneficary';
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"$url");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                            "$data");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec ($ch);
                $info = curl_getinfo($ch);
                $err = curl_error($ch);
                curl_close ($ch);
                
        $http_code = $info['http_code'];
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            return array('status'=>$dataArr[5],'api_msg'=>$dataArr[6]);
        }else{
            return "Unable To get Response";
        }
        
    }
    
    public function getBeneficiary($mobile){
        
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
                $id = rand(1000000000,9999999999);
                $data = "msg=E06011~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~NA~NA";
                $url= 'https://edigitalvillage.net/index.php/api_partner/getbeneficary';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"$url");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                            "$data");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec ($ch);
                $info = curl_getinfo($ch);
                $err = curl_error($ch);
                curl_close ($ch);
                
        $http_code = $info['http_code'];
        
        if($http_code == "200"){
            $response = json_decode($server_output);
            return $response;
        }else{
            return false;
        }
        
    }
    
    public function deleteBeneficiary($mobile,$bene_code){
        
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
                $id = rand(1000000000,9999999999);
                $data = "msg=E06013~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~$bene_code~NA~NA";
                $url= 'https://edigitalvillage.net/index.php/api_partner/deleteBeneficiary';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"$url");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                            "$data");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec ($ch);
                $info = curl_getinfo($ch);
                $err = curl_error($ch);
                curl_close ($ch);
        $http_code = $info['http_code'];
        if($http_code == "200"){
            if($dataArr[6]){
                if($dataArr[5] == "SUCCESS"){
                    return $dataArr[6];
                }else{
                    return $dataArr[6];
                }
            }else{
                return $server_output;
            }
        }else{
            return false;
        }
        
    }
    
    public function moneyTransfer($mobile,$benificary_code,$amt,$adhar_no,$pan_no,$cus_id){
        $package_amount =0;
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
                $id = rand(1000000000,9999999999);
                
                $trans_type='';$ifsc='';$acct_no='';
                    $data = "msg=E06015~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~$benificary_code~$amt~$trans_type~$adhar_no~$pan_no~$ifsc~$acct_no~NA~NA~NA";
                    $url= 'https://edigitalvillage.net/index.php/api_partner/moneyTransfer';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"$url");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "$data");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec ($ch);
                    $info = curl_getinfo($ch);
                    $err = curl_error($ch);
                    curl_close ($ch);
        $http_code = $info['http_code'];
        
        if($http_code == "200"){
            $resArr = explode('~',$server_output);
                if($resArr[5] == 'SUCCESS' || $resArr[5] == 'PENDING'){
                    
                    $request_id = $resArr[1];
                    $mobile_no = $resArr[2];
                    $bene_code = $resArr[3];
                    $status = $resArr[5];
                    $imps_ref_no = $resArr[6];
                    $desc = $resArr[7];
                    $bene_name = $resArr[8];
                    $trans_id = $resArr[9];
                    $bank_name = $resArr[10];
                    
                    $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                    $txn_clbal = $trnx_data[0]['txn_clbal'];
                    $debit_amount =  $amt;
                    $new_txn_clbal = $txn_clbal - $debit_amount;
            		$ip = $_SERVER['REMOTE_ADDR'];
            		$time = time();
            		$date = DATE('Y-m-d h:i:s');
            		
        		    $dmt_tnx_data = array('request_id' => $request_id,'mobile_no' => $mobile_no,'bene_code' => $bene_code,'status' => $status,'imps_ref_no' => $imps_ref_no,'description' => $desc,'bene_name' => $bene_name,'trans_id' => $trans_id,'bank_name' => $bank_name,'amount' => $amt,'charge' =>  $package_amount,'cus_id' => $cus_id
                    );
                    $dmt=$this->Db_model->insert_update('dmt_trnx',$dmt_tnx_data);
                    $insert_id = $this->db->insert_id();
                        
                    if($insert_id){
                        $insert_data = array('txn_agentid' => $cus_id,'dmt_txn_recrefid' => $insert_id,'txn_opbal' => $txn_clbal,'txn_crdt' => '0','txn_dbdt' => $debit_amount,'txn_clbal' => $new_txn_clbal,'txn_checktime' => '','txn_type' => 'DMT','txn_fromto' => '0','txn_time' => $time,'txn_date' => $date,'txn_optional' => $imps_ref_no,'txn_ip' => $ip,'txn_comment' => $desc,'transaction_id' => $trans_id,'transaction_ref' => ''
                        );
                        $res=$this->Db_model->insert_update('exr_trnx',$insert_data);    
                        
                        $this->creditDmtCommission($cus_id,$amt,$insert_id);
                    }
                    return array('status'=>$resArr[5],'api_msg'=>$resArr[6]);
                    
                }elseif($resArr[5] == 'FAILED'){
                    
                    $request_id = $resArr[1];
                    $mobile_no = $resArr[2];
                    $bene_code = $resArr[3];
                    $status = $resArr[5];
                    $imps_ref_no = $resArr[6];
                    $desc = $resArr[7];
                    $bene_name = $resArr[8];
                    $trans_id = $resArr[9];
                    $bank_name = $resArr[10];
                    
                  
            		
        		    $dmt_tnx_data = array('request_id' => $request_id,'mobile_no' => $mobile_no,'bene_code' => $bene_code,'status' => $status,'imps_ref_no' => $imps_ref_no,'description' => $desc,'bene_name' => $bene_name,'trans_id' => $trans_id,'bank_name' => $bank_name,'ifsc' => '0','account_num' => '0','amount' => $amt,'charge' =>  $package_amount,'cus_id' => $cus_id,'response' => $server_output
                    );
                    $dmt=$this->Db_model->insert_update('dmt_trnx',$dmt_tnx_data);
                    
                    return array('status'=>$resArr[5],'api_msg'=>$resArr[6]);
                }else{
                    return $server_output;
                }
            
        }else{
            return false;
        }
        
    }
    
    public function getDmtHistory($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM dmt_trnx WHERE cus_id ='$cus_id' ");
        if($data){
            return $data;
        }else{
            return false;
        }
        
    }
    
    
    public function accountValidation($adhar,$pan,$mobile,$acctnumber,$bankcode){
        
        $token = 'e1626d22b42148758d';
        $id = rand(1000000000,9999999999);
        
        $data = "msg=E06031~$token~$id~$mobile~$acctnumber~$bankcode~$adhar~$pan~NA~NA";
        $url= 'http://180.179.20.116:8030/RemitMoney/mtransfer';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"$url");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    "$data");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        $info = curl_getinfo($ch);
        $err = curl_error($ch);
        curl_close ($ch);
        $http_code = $info['http_code'];
        if($http_code == "200"){
            if($dataArr[6]){
                if($dataArr[5] == "SUCCESS"){
                    return $dataArr[8];
                }else{
                    return $dataArr[6];
                }
            }else{
                return $server_output;
            }
        }else{
            return false;
        }
        
        
    }
    
    public function getCustomerCommissionPackage($cus_id){
        
        $res = $this->Db_model->getAlldata("select * from customers where cus_id = '".$cus_id."' ");
        $pack_id = $res['0']->scheme_id;
        if($pack_id){
            
            $data = $this->Db_model->getAlldata("SELECT * FROM `commission_scheme_dmt` where scheme_id = '".$pack_id."' ");
            return $data;
        }else{
            return false;
        }   
    }
    
     public function creditDmtCommission($cus_id,$amount,$dmt_txn_recrefid){
        $commission_scheme_id = $this->Db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        
        if($scheme_id != 0 ){
            $checkIfActive = $this->Db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->Db_model->getAllData("SELECT * FROM commission_scheme_dmt WHERE scheme_id = '$scheme_id'");
                // print_r($commission_scheme_package);exit;
                if($commission_scheme_package){
                    if($cus_type == "retailer"){
                        foreach($commission_scheme_package as $comm){
                            $range = explode('-',$comm->slab);
                            $minRange = $range[0];
                            $maxRange = $range[1];
                            if($amount >= $minRange && $amount <= $maxRange){
                                $retailer_comm = $comm->retailer_comm; 
                                $comm_type = $comm->type;
                                if($retailer_comm){
                                    if($comm_type == "percent"){
                                        $commission_amount = round((($retailer_comm / 100) * $amount),3);
                                    }else{
                                        $commission_amount = $retailer_comm;
                                    }
                                    // echo $commission_amount;exit;
                                    $last_balance = $this->Db_model->get_trnx($cus_id);
                                    $this->Db_model->insert_update('dmt_trnx', array('retailer_commission'=>$commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
            						if($last_balance)
            						{
            						    $this->Db_model->insert_update('dmt_trnx',array('charge'=>$commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
            						    
                						$txntype = "Retailer Dmt Charge";
                						$this->Db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$commission_amount, 'txn_clbal'=>$last_balance[0]->txn_clbal - $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            						}
            							
                                }
                                
                                // Distributor Commission
                                $distributor_reffer = $this->Db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_id'");
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
                                        $last_balance = $this->Db_model->get_trnx($cus_reffer);
                                        $this->Db_model->insert_update('dmt_trnx', array('distributor_commission'=>$dist_commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
                						if($last_balance)
                						{
                    						$txntype = "Distributor Dmt Commission";
                    						$this->Db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}else{
                						    $txntype = "Distributor Dmt Commission";
                    						$this->Db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}
                							
                                    }
                                }
                                
                                // Master Commission
                                $master_reffer = $this->Db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_reffer'");
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
                                        $last_balance = $this->Db_model->get_trnx($master_cus_reffer);
                                        $this->Db_model->insert_update('dmt_trnx', array('master_commission'=>$mast_commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
                						if($last_balance)
                						{
                    						$txntype = "Master Dmt Commission";
                    						$this->Db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$master_cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}else{
                						    $txntype = "Master Dmt Commission";
                    						$this->Db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$master_cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}
                							
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
    
}