<?php
if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!'); 

class Aeps_model extends CI_Model
{
    public $pubkey ='-----BEGIN CERTIFICATE-----
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
-----END CERTIFICATE-----';
        
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Db_model','Encryption_model'));
    }
    public function getbank_list()
    {
        $query = $this->Db_model->getAlldata("SELECT * from aeps_bank");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
    public function onboarding($CURLOPT_URL,$value)
    {
        
        $key = '';
        $num='';
        $mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        foreach ($mt_rand as $chr)
        {   $num.=$chr;          
            $key .= chr($chr);         
        }
        $iv = '06f2f04cc530364f';
        $ciphertext_raw = openssl_encrypt(json_encode($value), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
        $request = base64_encode($ciphertext_raw);
        openssl_public_encrypt($key,$crypttext,$this->pubkey);
        $header = [         
                'Content-Type: text/xml',             
                'trnTimestamp:'.date('d/m/Y H:i:s'),         
                'hash:'.base64_encode(hash("sha256",json_encode($value), True)),         
                'deviceIMEI:'.'J9:H5:4D:9D:0Q',         
                'eskey:'.base64_encode($crypttext)         
            ];
        
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
        $array = json_decode($response, true);
        
        $err = curl_error($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        return $array;
        
    }
    
    
    public function getstate()
    {
        $query = $this->Db_model->getAlldata("SELECT * from aeps_state");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
    public function geturlforprint($order_id){
            $url = "https://edigitalvillage.net/aepsinvoice/$order_id.pdf";
            $urltohit = "https://edigitalvillage.net/Retailer/cashwithdrawalaeps_print/$order_id";
            //  echo $urltohit;
            $filename = "aepsinvoice/$order_id.pdf";
            if(file_exists($filename)){
                return $url;
            }else{
                
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $urltohit);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($curl);
                // return $result;
                return $url;
            }
            
    }
    public function getPayoutDetails($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM customers WHERE cus_id='$cus_id'");
        if($data){
            return $data;
        }else{
            return false; 
        }
    }
    
    public function getPayoutCharge($amount,$cus_id){
        /*$data = $this->Db_model->getAlldata("SELECT * FROM payout_commission_slab");
        if($data){
            $charge = '0';
            foreach($data as $rec){
                $amountArr = explode('-',$rec->amount);
                $min = $amountArr[0];
                $max = $amountArr[1];
                if($amount >= $min && $amount <= $max){
                    $charge = $rec->charge;
                }
            }
            return $charge;
        }else{
            return '0'; 
        }*/
        
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_payout WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
                if($commission_scheme_package){
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
                            
                                return $commission_amount;
                            }
                        }
                    }
                }
            }
        }
        
        return '0'; 
        
    }
    
    public function aepsBalance($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='$cus_id' ORDER BY aeps_txn_id DESC LIMIT 1");
        if($data){
            return $data;
        }else{
            return false; 
        }
    }

    public function submitPayout($cus_id,$bank_name,$account_number,$ifsc_code,$account_holder_name,$amount,$charge,$type){
        
        if($type== "payoutToBank"){
        
            $data = array(
                'cus_id' => $cus_id,
                'bankName' => $bank_name,
                'bankAccount' => $account_number,
                'bankIFSC' => $ifsc_code,
                'accountHolderName' => $account_holder_name,
                'amount' => $amount,
                'charge' => $charge
                );
                
            $result = $this->Db_model->insert_update('payout_request',$data);
            if($result){
                
                $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
                $txntype = 'Redeem';
                $total = $cus_aeps_bal - $amount;
        		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>'0','aeps_txn_dbdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                
                $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
        		$txn_dbdt = $charge;
        		$txntype = 'Redeem Charge';
        		$ip = $_SERVER['REMOTE_ADDR'];
        		$date = DATE('Y-m-d h:i:s');
        		$total = $cus_aeps_bal - $txn_dbdt;
        		
        		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>'0','aeps_txn_dbdt'=>$txn_dbdt,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
        		
        		$CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/submit_payout";
        	
                        $param['msg'] = "8630450571-89938-143-92.204.134.236";
                        $param['bank_name'] = $bank_name;
                        $param['account_number'] = $account_number;
                        $param['ifsc_code'] = $ifsc_code;
                        $param['account_holder_name'] = $account_holder_name;
                        $param['amount'] = $amount;
                        $param['charge'] = $charge;
                        $param['moveto'] = "Payout to Bank";
                        
                        
                		  foreach($param as $key=>$val) 
                		{ 
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
                            
                        $res = curl_exec($curl);
                        $response = json_decode($res,true);
                        $err = curl_error($curl);
                        $info = curl_getinfo($curl);
                        curl_close($curl);
        		
                return true;
            }else{
                return false;
            }
        
        }
        
        if($type== "payoutToWallet"){
            
            $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
	        
	        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $total_aeps_closing = $cus_aeps_bal - $amount;
            $txntype = "Payout ( Move To Wallet )";
            $lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>$amount,'aeps_txn_crdt'=>'0','aeps_txn_clbal'=>$total_aeps_closing,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
            
            if($lasttxnrec){
                
                $cus_wallet_bal_data = $this->db_model->get_trnx($cus_id);
                
                if($cus_wallet_bal_data){
                    $cus_wallet_bal = $cus_wallet_bal_data[0]->txn_clbal;
                    $cus_closing_wallet_bal = $cus_wallet_bal + $amount;
    				$insert_wallet = array(
    					'txn_agentid'	=> $cus_id,
    					'txn_recrefid'	=> '0',
    					'txn_opbal'	=>	$cus_wallet_bal,
    					'txn_crdt'	=>	$amount,
    					'txn_dbdt'	=>	'0',
    					'txn_clbal'	=>	round($cus_closing_wallet_bal, 3),
    					'txn_type'	=>	$txntype,
    					'txn_time'	=>	time(),
    					'txn_date'	=>	date('Y-m-d h:i:s'),
    					'txn_checktime'	=>	$cus_id.time(),
    					'txn_ip'	=>	$ip,
    					'txn_comment'	=>	$txntype
    				);
    				$res = $this ->db_model->insert('exr_trnx',$insert_wallet);
    				if($res){
                        return true;
    				}else{
    				    return false;
    				}
                }else{
                    $cus_closing_wallet_bal = $amount;
    				$insert_wallet = array(
    					'txn_agentid'	=> $cus_id,
    					'txn_recrefid'	=> '0',
    					'txn_opbal'	=>	'0',
    					'txn_crdt'	=>	$amount,
    					'txn_dbdt'	=>	'0',
    					'txn_clbal'	=>	round($cus_closing_wallet_bal, 3),
    					'txn_type'	=>	$txntype,
    					'txn_time'	=>	time(),
    					'txn_date'	=>	date('Y-m-d h:i:s'),
    					'txn_checktime'	=>	$cus_id.time(),
    					'txn_ip'	=>	$ip,
    					'txn_comment'	=>	$txntype
    				);
    				$res = $this ->db_model->insert('exr_trnx',$insert_wallet);
    				if($res){
                        return true;
    				}else{
    				    return false;
    				}
                }	
            }
	                
        }
    }
    
    public function payoutHistory($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM payout_request WHERE cus_id='$cus_id' ORDER BY pay_req_id DESC");
        if($data){
            return $data;
        }else{
            return false; 
        }
    }
    
    public function microAtmDetails($cus_id){
        $data = $this->Db_model->getAlldata("SELECT aeps_merchantLoginId,aeps_merchantLoginPin FROM customers WHERE cus_id='$cus_id'");
        if($data){
            $response = array(
                "aeps_merchantLoginId" => $this->Encryption_model->decode($data[0]->aeps_merchantLoginId),
                "aeps_merchantLoginPin" => $this->Encryption_model->decode($data[0]->aeps_merchantLoginPin)
                );
            return $response;
        }else{
            return false; 
        }
    }
    
    public function getCustomerParent($cus_id){
        $data = $this->Db_model->getAlldata("SELECT cus_reffer,cus_type FROM customers WHERE cus_id='$cus_id'");
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    public function getCustomerCommissionPackage($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM aeps_commission_slab JOIN customers ON aeps_commission_slab.aeps_comm_id = customers.aeps_comm_id WHERE cus_id='$cus_id'");
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    public function aeps_commission_slab($cus_id){
        
        $res = $this->db_model->getAlldata("select * from customers where cus_id = '".$cus_id."' ");
        $pack_id = $res['0']->scheme_id;
        if($pack_id){
            
            $data = $this->Db_model->getAlldata("select * from commission_scheme_aeps as cs join aeps_commission_slab ac on cs.slab_opcode = ac.aeps_comm_id where scheme_id = '".$pack_id."' ");
            return $data;
        }else{
            return false;
        }       
    }
    public function getCustomerMicroAtmCommissionPackage($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM micro_atm_commission_slab JOIN customers ON micro_atm_commission_slab.atm_comm_id = customers.atm_comm_id WHERE cus_id='$cus_id'");
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    public function submitMicroAtmResponse($cus_id,$status,$response,$transAmount,$balAmount,$bankRrn,$transType,$type,$cardNum,$bankName,$cardType,$terminalId,$fpId,$transId){
        
        if($status == "true"){
            $transactionStatus = "SUCCESS";    
        }else{
            $transactionStatus = "FAILED";  
        }
        
        $dataArray = array(
            "cus_id" => "$cus_id",
            "status" => "$status",
            "response" => "$response",
            "transAmount" => "$transAmount",
            "balAmount" => "$balAmount",
            "bankRrn" => "$bankRrn",
            "transType" => "$transType",
            "type" => "$type",
            "cardNum" => "$cardNum",
            "bankNm" => "$bankName",
            "cardType" => "$cardType",
            "terminalId" => "$terminalId",
            "fpId" => "$fpId",
            "transId" => "$transId",
            "transactionStatus" => "$transactionStatus"
            );
            
        $data = $this->Db_model->insert_update('micro_atm',$dataArray);
        if($data){
            $inserId = $this->db->insert_id();
            if($transactionStatus == "SUCCESS"){
                
                $amount = $transAmount;
                
                $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
                $txntype = 'Micro ATM Cash Withdrawal';
                $total = $cus_aeps_bal + $amount;
        		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'microatm_recrefid'=>$inserId,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
        		
                $this->creditMicroAtmCommission($cus_id,$amount,$inserId);
            
            }
            
            return true;
        }else{
            return false;
        }
        
    }
    
    public function aeps_history($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM aeps_transaction_fch WHERE apiclid='$cus_id' AND status!='null' order by aeps_id desc");
        //echo $this->db->last_query();
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    public function aeps_ledger($cus_id){
        $data = $this->Db_model->getAlldata("SELECT * FROM aeps_exr_trnx WHERE aeps_txn_agentid='$cus_id' ");
        //echo $this->db->last_query();
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    public function creditMicroAtmCommission($cus_id,$amount,$aeps_id){
        
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_microatm WHERE scheme_id = '$scheme_id'");
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
                                    $last_balance = $this->aeps_model->aepsBalance($cus_id);
                                    $this->db_model->insert_update('aeps_transaction_fch', array('retailer_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
                                    
                					$txntype = 'Retailer Micro Atm Commission';
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
                                        $last_balance = $this->aeps_model->aepsBalance($cus_reffer);
                                        
                                        if($last_balance){
                                            $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                        }else{
                                            $cus_aeps_bal = '0';
                                        }
                                        
                                        $this->db_model->insert_update('aeps_transaction_fch', array('distributor_commission'=>$dist_commission_amount),array('aeps_id'=>$aeps_id));
                                        
                						$txntype = 'Distributor Micro Atm Commission';
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
                                       $last_balance = $this->aeps_model->aepsBalance($master_cus_reffer);
                                        
                                        $this->db_model->insert_update('aeps_transaction_fch', array('master_commission'=>$mast_commission_amount),array('aeps_id'=>$aeps_id));
                                        
                                        if($last_balance){
                                            $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                        }else{
                                            $cus_aeps_bal = '0';
                                        }
                                        
                						$txntype = 'Master Micro Atm Commission';
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
    
}