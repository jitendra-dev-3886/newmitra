<?php if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!'); 
class Rec_model extends CI_Model 
{ 
    public function __construct() 
    { 
		parent::__construct(); 
		
		$this->load->database();
		$this->tableName = 'exr_trnx';
		$this->primaryKey = 'cus_id';
		$this->load->helper('date');
		$this->load->model(array('db_model'));

	  }
	  
	  public function getadminapicommission($apisourceid,$opcode,$amount){
	    $this->db->select('*');
	    $this->db->from('apicommissions');
	    $this->db->join('apipackage','apipackage.apipackage_id = apicommissions.apipackage_id');
	    $this->db->where('apipackage.apisourceid',$apisrourceid);
	    $this->db->where('apicommissions.packcommission_opcode',$opcode);
	    
	    $query = $this->db->get();
	    
	    if($query->num_rows() > 0){
	        
	        $data = $query ->result_array();
	        $apicommission = $data[0]['packcomm_commission'];
	        
	        $amt = round((($apicommission / 100) * $amount),3);
	        
	        return $amt;
	    }else{
	        return 0;
	    }
	    
	}

    public function check_recharge($amount,$mob,$ip="",$cus_id,$time)
	{
	    $timeck = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where apiclid='".$cus_id."' and reqtime='".$time."' order by recid desc limit 1")->result();
	    if(!$timeck)
	    {
	        $recdt = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where mobileno='".$mob."' and amount='".$amount."' and status !='FAILED' order by recid desc limit 1")->result();
    		if($recdt)
    		{	
    			$date = DateTime::createFromFormat('Y-m-d H:i:s', $recdt[0]->reqdate);
    			$date->modify('+5 minutes');
    			$recdate = $date->format('Y-m-d H:i:s');
    			$now = date('Y-m-d h:i:s');
    			if($now > $recdate)
    			{
    				return 1;
    			}
    			else
    			{
    				return 0;
    			}
    		}
    		else
    		{
    			return 1;
    		}
	    }
	    else
		{
			return 1;
		}
	}
	
	public function creditRechargeCommission($cus_id,$amount,$rec_id,$opcode){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_recharge WHERE scheme_id = '$scheme_id' AND slab_opcode = '$opcode'");
                if($commission_scheme_package){
                    if($cus_type == "retailer"){
                        
                        // Retailer Commission
                        $retailer_comm = $commission_scheme_package[0]->retailer_comm;
                        $comm_type = $commission_scheme_package[0]->type;
                        if($retailer_comm){
                            if($comm_type == "percent"){
                                $commission_amount = round((($retailer_comm / 100) * $amount),3);
                            }else{
                                $commission_amount = $retailer_comm;
                            }
                            $last_balance = $this->db_model->get_trnx($cus_id);
                            $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>$commission_amount),array('recid'=>$rec_id));
    						if($last_balance)
    						{
        						$txntype = "Retailer Commission";
        						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}else{
    						    $txntype = "Retailer Commission";
        						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}
    							
                        }
                        
                        // Distributor Commission
                        $distributor_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_id'");
                        $cus_reffer = $distributor_reffer[0]->cus_reffer;
                        if($cus_reffer && $cus_reffer != 0 ){
                            $distributor_comm = $commission_scheme_package[0]->distributor_comm;
                            $comm_type = $commission_scheme_package[0]->type;
                            if($distributor_comm){
                                if($comm_type == "percent"){
                                    $dist_commission = round((($distributor_comm / 100) * $amount),3);
                                    $dist_commission_amount = $dist_commission - $commission_amount;
                                }else{
                                    $dist_commission_amount = $distributor_comm - $commission_amount;
                                }
                                $last_balance = $this->db_model->get_trnx($cus_reffer);
                                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>$dist_commission_amount),array('recid'=>$rec_id));
        						if($last_balance)
        						{
            						$txntype = "Distributor Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}else{
        						    $txntype = "Distributor Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}
        							
                            }
                        }
                        
                        // Master Commission
                        $master_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_reffer'");
                        $master_cus_reffer = $master_reffer[0]->cus_reffer;
                        if($master_cus_reffer && $master_cus_reffer != 0 ){
                            $master_comm = $commission_scheme_package[0]->master_comm;
                            $comm_type = $commission_scheme_package[0]->type;
                            if($master_comm){
                                if($comm_type == "percent"){
                                    $mast_commission = round((($master_comm / 100) * $amount),3);
                                    $mast_commission_amount = $mast_commission - ($dist_commission_amount + $commission_amount);
                                }else{
                                    $mast_commission_amount = $master_comm - ($dist_commission_amount + $commission_amount);
                                }
                                $last_balance = $this->db_model->get_trnx($master_cus_reffer);
                                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$mast_commission_amount),array('recid'=>$rec_id));
        						if($last_balance)
        						{
            						$txntype = "Master Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$master_cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}else{
        						    $txntype = "Master Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$master_cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}
        							
                            }
                        }
                        
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

	
    public function recharge_ot($data)
	{
		$billing = isset($data['billing'])?$data['billing']:'';
		$process = isset($data['process'])?$data['process']:'';
		$mess = array();
		$medium = $data['mode'];
		$time = time();
		$ip = $this->input->ip_address();
		$cus_id = $data['cus_id'];
		$amount = $amount5 = $data['amount'];
		$mob = $data['mob'];
		$optional1 = $data['optional1'];
		$optional2 = $data['optional2'];
		$deviceId = $data['deviceId'];
		$deviceName = $data['deviceName'];
		$appVersion = $data['appVersion'];

		if (array_key_exists("reqtxnid",$data))
		{
		  $reqtxnid = $data['reqtxnid'];
		}
		else
		{
		  $reqtxnid = "";
		}
		$cspk = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
		$c_type= $cspk[0]->cus_type;
		$limit_amt = $cspk[0]->limit_amount;
		$csamnt = $cspk[0]->cus_cutofamt + $amount5;
		$dis_cha = $this->commi(array('cus_id' => $cus_id, 'operator' => $data['operator'], 'amount' => $amount5));
		$apidis_cha = $this->apicommi(array('cus_id' => $cus_id, 'operator' => $data['operator'], 'amount' => $amount5));
		$com = $dis_cha['com'];  // if You Want then use as message
		$number = $data['number'];
		$operator = $data['operator'];
		$rrr = '5';
		if ($dis_cha['comtype'] == '1')
			$amount = $amount5 + $com;
		$st = $this->db_model->get_trnx($cus_id);
		$lt = 0;
		if($st){
			$lt = $st[0]->txn_clbal;
			$r=$st;
		}
		if ($lt >= $csamnt+$limit_amt)
	   
		
		{
			$closing = $r[0]->txn_clbal;
			$total = $closing - $amount5;
			
			$cusapi = $cspk[0]->cus_api_source_id;
			$opd = $this->db_model->getwhere('operator', array('opcodenew' => $operator));
			$opd1 = $this->db_model->get_newdata('amount_apisources', array('api_opcodenew' => $operator));
// 			$opd2 = $this->db_model->get_newdata('circle_api_switch', array('api_opcodenew' => $operator));
			if($opd1 && $opd1[0]->amount_apisource_id != '' && !empty($opd1[0]->amount_apisource_id) && $opd1[0]->amount_apisource_id!='Select API' && $opd1[0]->amount_apisource_id != NULL){
    			foreach($opd1 as $range){
                     $minRange = $range->api_amount_min;
                     $maxRange = $range->api_amount_max;
                    if($amount5 >= $minRange && $amount5 <= $maxRange){
            			//echo $this->db->last_query();
            			$apisource = $range->amount_apisource_id;
            			break;
                    }
    			}
    			if(!isset($apisource)){
        			//echo $this->db->last_query();
        			$apisource = $opd[0]->apisource;
    			}
			}elseif($cusapi != '' && !empty($cusapi) && $cusapi!='Select API' && $cusapi != NULL ){
			    $apisource = $cusapi;
			}else{
			    if(!isset($apisource)){
        			//echo $this->db->last_query();
        			$apisource = $opd[0]->apisource;
    			}
			}
			
			
			
			if ($opd[0]->apisource == '1')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '2')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '3')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '4')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '5')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '6')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '7')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '8')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '9')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '13')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '11')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '10')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '12')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '14')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '15')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '16')
				$qr_opcode = $opd[0]->opcodenew;
			
		
		// Here You can Add more api using else if()
			$ch = $this->check_recharge($amount5,$number, $ip,$cus_id,$time);
			if ($ch) {
			    if($cspk[0]->recharge_rs_1=='1'){
			    $val = $amount5 % 2;
			    if($val=='0') $new_amount5 = $amount5;else $new_amount5 = $amount5 + 1;
			    }else{
			        $new_amount5 = $amount5;
			    }
				$recdata = array(
					'apiclid' => $cus_id,
					'apisourceid' => $apisource,
					'requestertxnid' => $reqtxnid,
					'mobileno' => $number,
					'operator' => $operator,
					'amount' => $amount5,
					'recmedium' => $medium,
					'reqtime' => time(),
					'restime' => time(),
					'reqdate' => date('Y-m-d h:i:s'),
					'status' => '',
					'statusdesc' => '',
					'serverresponse' => '',
					'deviceName' => $deviceName,
    				'deviceId' => $deviceId,
    				'appVersion' => $appVersion,
					//'promo_id'	=>	$promo_id,
					'ip' => $ip
				);
				$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata);
				$rectxnid = $this->db->insert_id();
				if ($rectxnid) 
				{
					$txntype = "recharge";
					$txn_crdt = 0;
					
					$this->db->query("LOCK TABLE exr_trnx WRITE");
					$row14114141 =  $this->db_model->get_trnx($cus_id);

					$recdata16 = array(
						'txn_agentid' => $cus_id,
						'txn_recrefid' => $rectxnid,
						'txn_opbal' => $row14114141[0]->txn_clbal,
						'txn_crdt' => $txn_crdt,
						'txn_dbdt' => $new_amount5,
						'txn_clbal' => $row14114141[0]->txn_clbal - $new_amount5,
						'txn_type' => $txntype,
						'txn_time' => time(),
						'txn_date' => date('Y-m-d H:i:s'),
						'txn_checktime' => $cus_id . time(),
						'txn_ip' => $ip
					);
					$this->db_model->insert_update('exr_trnx', $recdata16);
					$this->db->query("UNLOCK TABLE");
					$lasttxnrec = $this->db->insert_id();
					if ($dis_cha['comtype'] == '1') 
					{
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',array('retailer' => $com,'ret_com_type' => 'surcharge'), array('recid' => $rectxnid));

						$this->db->query("LOCK TABLE exr_trnx WRITE");
						$row141 =  $this->db_model->get_trnx($cus_id);
						$recdata1 = array(
							'txn_agentid' => $cus_id,
							'txn_recrefid' => $rectxnid,
							'txn_opbal' => $row141[0]->txn_clbal,
							'txn_crdt' => '0',
							'txn_dbdt' => $com,
							'txn_clbal' => $row141[0]->txn_clbal - $com,
							'txn_type' => 'surcharge',
							'txn_time' => time(),
							'txn_date' => date('Y-m-d h:i:s'),
							'txn_checktime' => $cus_id . time(),
							'txn_ip' => $ip,
							'txn_comment' => 'Surcharge Debit'
						);
						$this->db_model->insert_update('exr_trnx', $recdata1);
						$this->db->query("UNLOCK TABLE");
					}
				// 	if($qr_opcode=='141' && $amount > '199'){
				// 	    $response2 = $this->mpay_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
				// 	}elseif($qr_opcode=='141' && $amount < '201'){
				// 	    $response2 = $this->tekdigi_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
				// 	}elseif($qr_opcode=='33' && $amount > '199'){
				// 	    $response2 = $this->mrobotic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
				// 	}elseif($qr_opcode=='33' && $amount < '201'){
				// 	    $response2 = $this->tekdigi_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
				// 	}else
					if ($apisource == '1') {
						$response2 = 'SUCCESS';
					} elseif ($apisource == '2') {
						$response2 = 'FAILED';
					} elseif ($apisource == '3') {
						$response2 = 'PENDING';
					}
                   elseif($apisource == '4')
                    {
                        $response2 = $this->recharge_wale_bbps_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process,$optional1,$optional2,$mob);
                    }
                    elseif($apisource == '5')
                    {
                        $response2 = $this->edigital_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }
                    elseif($apisource == '6')
                    {
                        $response2 = $this->mrobotic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }elseif($apisource == '7')
                    {
                        $response2 = $this->jetwings_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }
                    elseif($apisource == '8')
                    {
                        $response2 = $this->mahima_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }elseif($apisource == '13')
			        {
			            $response2 = $this->ambika_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }elseif($apisource == '11')
			        {
			            $response2 = $this->tekdigi_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }elseif($apisource == '9')
			        {
			            $response2 = $this->bharatmoney_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }elseif($apisource == '15')
			        {
			            $response2 = $this->tiktik_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }
			        elseif($apisource == '16')
			        {
			            $response2 = $this->jetwings_rec_old($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }
			        elseif($apisource == '10')
			        {
			            $response2 = $this->skgrouptechnoogy_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process,$optional1,$optional2);
			        }
			        elseif($apisource == '12')
			        {
			            $response2 = $this->airtelthanks_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }
			        elseif($apisource == '14')
			        {
			            $response2 = $this->AirtelPayment_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }
			        elseif($apisource >= '20')
			        {   
			            $response2 = $this->dynamic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing='',$process='',$apisource,$api_sequences);
			        }
			        
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('responsecode' => $response2), array('recid' => $rectxnid));
					$mess = "Your Recharge is ";
					if (in_array($response2, array("SUCCESS", "0", "Success"))) {
						if (1) 
						{
							/*$row14 = $this->db_model->get_trnx($cus_id);
							if ($dis_cha['comtype'] == '2') 
							{
								if ($row14) 
								{
									$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',array('retailer' => $com,'ret_com_type' => 'commission'), array('recid' => $rectxnid));
									$recdata1 = array(
										'txn_agentid' => $cus_id,
										'txn_recrefid' => $rectxnid,
										'txn_opbal' => $row14[0]->txn_clbal,
										'txn_crdt' => $com,
										'txn_dbdt' => '0',
										'txn_clbal' => $row14[0]->txn_clbal + $com,
										'txn_type' => 'discount',
										'txn_time' => time(),
										'txn_date' => date('Y-m-d h:i:s'),
										'txn_checktime' => $cus_id . time(),
										'txn_ip' => $ip,
										'txn_comment' => 'Retailer Commission'
									);
									$this->db->query("LOCK TABLE exr_trnx WRITE");
									$this->db_model->insert_update('exr_trnx', $recdata1);
									$this->db->query("UNLOCK TABLE");
                                	if($medium != 'web')
                                	{
                                		$t = '<br/> And Your Commission is RS.' . $com;	
                                	}									
								}
							}*/
							
							if($c_type == 'retailer'){
							    $this->creditRechargeCommission($cus_id,$amount5,$rectxnid,$qr_opcode);
							}
							
							$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
							
							/*if($c_type == 'retailer'){
								$this->member_commission($amount5,$operator,$cus_id,$rectxnid);
							}else if($c_type == 'distributor'){
								$this->member_commission_distributor($amount5,$operator,$cus_id,$rectxnid);
							}else if($c_type == 'master'){
								$this->member_commission_master($amount5,$operator,$cus_id,$rectxnid);
							}else{
								$this->member_commission($amount5,$operator,$cus_id,$rectxnid);
							}*/
							
							$recdata = $this->db_model->get_rec($cus_id);
							$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rectxnid));
						}
						$row2 = $this->db_model->get_trnx($cus_id)[0]->txn_clbal;
						$mess = array('type'=>1,'mess'=>'Your Recharge is Success','balance'=>round($row2,2),'recid' => $rectxnid);
					} else if (in_array($response2, array("PENDING", "2", "Pending"))) {
					    
					    if($c_type == 'retailer'){
							    $this->creditRechargeCommission($cus_id,$amount5,$rectxnid,$qr_opcode);
							}
				// 			$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
				// 			$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
				// 			$recdata = $this->db_model->get_rec($cus_id);
				// 			$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
				// 			$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rectxnid));
					    
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('status' => "PENDING"), array('recid' => $rectxnid));
						$row2 = $this->db_model->get_trnx($cus_id)[0]->txn_clbal;
						$mess = array('type'=>1,'mess'=>'Your Recharge is Submit','balance'=>round($row2,2),'recid' => $rectxnid);
					} else if (in_array($response2, array("SUBMIT_SUCCESS", "2", "Submit_Success"))) {
					    
					    if($c_type == 'retailer'){
							    $this->creditRechargeCommission($cus_id,$amount5,$rectxnid,$qr_opcode);
							}
				// 			$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
				// 			$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
				// 			$recdata = $this->db_model->get_rec($cus_id);
				// 			$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
				// 			$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rectxnid));
					    
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('status' => "SUBMIT_SUCCESS"), array('recid' => $rectxnid));
						$row2 = $this->db_model->get_trnx($cus_id)[0]->txn_clbal;
						$mess = array('type'=>1,'mess'=>'Your Recharge is Submit Success','balance'=>round($row2,2),'recid' => $rectxnid);
					}
					else 
					{
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('status' => "FAILED"), array('recid' => $rectxnid));
						$row2 = $this->db_model->get_trnx($cus_id);
						if($cspk[0]->recharge_rs_1=='1'){
						$amt6 = $amount5 % 2;
						if($amt6=='0') $new_amount6 = $amount5;else $new_amount6 = $amount5 + 1;
						} else{
						    $new_amount6= $amount5;
						}
						$recdata19 = array(
								'txn_agentid' => $cus_id,
								'txn_recrefid' => $rectxnid,
								'txn_opbal' => $row2[0]->txn_clbal,
								'txn_crdt' => $new_amount6,
								'txn_dbdt' => '0',
								'txn_clbal' => $row2[0]->txn_clbal + $new_amount6,
								'txn_type' => 'Recharge Failed',
								'txn_time' => time(),
								'txn_date' => date('Y-m-d h:i:s'),
								'txn_checktime' => $cus_id . time(),
								'txn_ip' => $ip,
								'txn_comment' => 'Recharge Failed'
						);
						$this->db->query("LOCK TABLE exr_trnx WRITE");
						$this->db_model->insert_update('exr_trnx', $recdata19);
						$this->db->query("UNLOCK TABLE");
						$diskchk = $this->db->query("select * from exr_trnx where txn_type='surcharge' and txn_recrefid='".$rectxnid."' order by txn_id DESC limit 1")->result();
						if($diskchk)
						{
							$row11122 = $this->db_model->get_trnx($cus_id);
							$recdata1119 = array(
								'txn_agentid' => $cus_id,
								'txn_recrefid' => $rectxnid,
								'txn_opbal' => $row11122[0]->txn_clbal,
								'txn_crdt' => $com,
								'txn_dbdt' => '0',
								'txn_clbal' => $row11122[0]->txn_clbal + $com,
								'txn_type' => 'surcharge refund',
								'txn_time' => time(),
								'txn_date' => date('Y-m-d h:i:s'),
								'txn_checktime' => $cus_id . time(),
								'txn_ip' => $ip,
								'txn_comment' => 'Surcharge Refund'
							);
							$this->db->query("LOCK TABLE exr_trnx WRITE");
							$this->db_model->insert_update('exr_trnx', $recdata1119);
							$this->db->query("UNLOCK TABLE");
						}
						
						$mess = array('type'=>1,'mess'=>'Your Recharge is Failed','balance'=>round($row2[0]->txn_clbal + $amount,2));
					}
				} else {
					$mess = array('type'=>1,'mess'=>'Fake Attempt');
				}
			} else {
				$mess = array('type'=>1,'mess'=>'Same Rehcarge');
			}
		}
		else
		{
			$mess = array('type'=>1,'mess'=>'Low Balance or Capping limit exceed','balance'=>$lt);
		}
		return  $mess;
	}

    public function recharge_oto($data)
	{
		$billing = isset($data['billing'])?$data['billing']:'';
		$process = isset($data['process'])?$data['process']:'';

		//It Contain all the data for recharge cus_id,amount,number,operator,mode
		$mess = array();
		$time = time();
		$amount = $amount5 = $data['amount'];

		if (array_key_exists("reqtxnid",$data))
		{
		  $reqtxnid = $data['reqtxnid'];
		}
		else
		{
		  $reqtxnid = "";
		}
		$number = $data['number'];
		$operator = $data['operator'];

		$lt = '0';

		if ($lt == '0')
		{
			$closing = $r[0]->txn_clbal;
			$total = $closing - $amount5;
			$opd = $this->db_model->getwhere('operator', array('opcodenew' => $operator));
			$apisource = $opd[0]->apisource;
			if ($opd[0]->apisource == '1')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '2')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '3')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '4')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '5')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '6')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '7')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '8')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '9')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '10')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '11')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '12')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '13')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '14')
				$qr_opcode = $opd[0]->opcodenew;
			else if($opd[0]->apisource == '15')
				$qr_opcode = $opd[0]->opcodenew;
			// Here You can Add more api using else if()


			$ch = $this->check_recharge($amount5,$number, $ip,$cus_id,$time);
			if ($ch) {
				$recdata = array(
					'requestertxnid' => $reqtxnid,
					'mobileno' => $number,
					'operator' => $operator,
					'amount' => $amount5
				);
				if ($recdata) 
				{
					$txntype = "recharge";
					if ($apisource == '1') {
						$response2 = 'SUCCESS';
					} elseif ($apisource == '2') {
						$response2 = 'FAILED';
					} elseif ($apisource == '3') {
						$response2 = 'PENDING';
					} elseif($apisource == '4')
			        {
			            $response2 = $this->insert_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }elseif($apisource == '13')
			        {
			            $response2 = $this->mrobotic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }
					} 
			} 
		}
			
	}
	
	
	    
    public function checkout($info)
	{
		$cus_id  = $info['cus_id'];
		$cspk = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
		$op = $info['operator'];
		$rf = $this->db_model->getwhere('operator',array('opcodenew'=>$op));
		$data['operatorname'] = $rf[0]->operatorname;
		$pkdt = $this->db_model->getwhere('exr_packagecomm',array('packcomm_opcode'=>$op,'packcomm_name'=>$cspk[0]->package_id));
		$term = $pkdt[0]->packagecom_tem;
		$type = $pkdt[0]->packagecom_type;
		$amttype = $pkdt[0]->packagecom_amttype;
		$commission = $pkdt[0]->packcomm_comm;
		$com = 0;
		if($commission != '0')
		{
			switch ($type) {
				case 0:
					$com = 0;
					$data['ti'] = '';
					break;
				case 1:
					switch ($amttype)
					{
						case 1:
							$com =  $commission;
							$data['ti'] = 'Commission charged included.';
							break;
						default:
							$amount1 = $info['amount'];
							$amount2 = round((($commission / 100) * $amount1),3);
							$com = $amount2;
							$data['ti'] = 'Commission charged included.';
							break;
					}
					break;
				default:
					switch ($amttype)
					{
						case 1:
							$com =   $commission;
							$data['ti'] = 'Commission charged included.';
							break;
						default:
							$amount1 = $info['amount'];
							$amount2 = round((($commission / 100) * $amount1),3);
							$com = $amount2;
							$data['ti'] = 'Commission charged included.';
							break;
					}
			}
		}
		return ( array('com'=>(float)$com,'comtype'=>$type));
	}

    public function member_commission_money($amount,$opcode,$cus_id,$opid)
	{
		$retcom = $this->db_model->getwhere('exr_trnx', array('txn_recrefid'=>$opid,'txn_type'=>'money charge'), array('txn_dbdt'))[0]->txn_dbdt;
	 	$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('customers', array('cus_id'=>$cus_id));
		$cus_d = $r[0]->cus_reffer;
		$com = $this->commi(array('cus_id'=>$cus_d,'operator'=>$opcode,'amount'=>$amount));
		if($com['comtype']==2)
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				if($com['com'] == '0')
				{
					$newdiscom = $retcom;
				}
				else
				{
					$newdiscom = $com['com'] - $retcom;
				}
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>$newdiscom,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=>$row3[0]->txn_clbal + $newdiscom, 'txn_type'=>'Distributer Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Distributer Commission'));
				if(1)
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$row4 = $this->db_model->get_trnx($cus_m);
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					if($row4)
					{
						if($com1['comtype']==2)
						{
							$txntype2 = "Master Commission";
							if($com1['com'] == '0')
							{
								$newmascom = $com['com'];
							}
							else
							{
								$newmascom = $com1['com'] - $com['com'];
							}
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							if($com1['com'] == '0')
							{
								$newmascom = $com['com'];
							}
							else
							{
								$newmascom = $com1['com'] - $com['com'];
							}
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
							return 1;
						}
					}
					else
					{
						if($com1['com'] == '0')
						{
							$newmascom = $com['com'];
						}
						else
						{
							$newmascom = $com1['com'] - $com['com'];
						}
						$txntype2 = "Master Commission";
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
						return 1;
					}
				}
				else
				{
					return 0;
				}
			}
			else
			{
				$txntype1 = "Distributer Commission";
				if($com['com'] == '0')
				{
					$newdiscom = $retcom;
				}
				else
				{
					$newdiscom = $com['com'] - $retcom;
				}

				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>'0', 'txn_crdt'=>$newdiscom,'txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=> $newdiscom, 'txn_type'=>$txntype1, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype1));
				if(1)
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$row4 = $this->db_model->get_trnx($cus_m);
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					if($row4)
					{
						if($com1['comtype']==2)
						{
							$txntype2 = "Master Commission";
							if($com1['com'] == '0')
							{
								$newmascom = $com['com'];
							}
							else
							{
								$newmascom = $com1['com'] - $com['com'];
							}
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						if($com1['com'] == '0')
						{
							$newmascom = $com['com'];
						}
						else
						{
							$newmascom = $com1['com'] - $com['com'];
						}
						$txntype2 = "Master Commission";
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
						return 1;
					}
				}
				else
				{
					return 0;
				}
			}
		}
		else
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				$newdiscom = $retcom - $com['com'];
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>$newdiscom,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=>$row3[0]->txn_clbal + $newdiscom, 'txn_type'=>'Distributer Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Distributer Commission'));
				if(1)
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$row4 = $this->db_model->get_trnx($cus_m);
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					if($row4)
					{
						if($com1['comtype']==1)
						{
							$txntype2 = "Master Commission";
							$newmascom = $com['com'] - $com1['com'];	
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						$newmascom = $com['com'] - $com1['com'];
						$txntype2 = "Master Commission";
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
						return 1;
					}
				}
				else
				{
					return 0;
				}
			}
			else
			{				
				$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
				$cus_m = $d[0]->cus_reffer;
				$row4 = $this->db_model->get_trnx($cus_m);
				$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
				if($row4)
				{
					if($com1['comtype']==2)
					{
						$txntype2 = "Master Commission";
						$newmascom = $com['com'] - $com1['com'];

						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
						if($mt)
						{
							return 1;
						}
						else
						{
							return 0;
						}
					}
					else
					{
						return 0;
					}
				}
			}
		}
	}

    public function member_commission($amount,$opcode,$cus_id,$opid)
	{
		$retcom = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch', array('recid'=>$opid), array('retailer'))[0]->retailer;

	 	$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('customers', array('cus_id'=>$cus_id));
		$cus_d = $r[0]->cus_reffer;
		$com = $this->commi(array('cus_id'=>$cus_d,'operator'=>$opcode,'amount'=>$amount));
		if($com['comtype']==2)
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				if($com['com'] == '0')
				{
					$newdiscom = $retcom;
				}
				else
				{
					$newdiscom = $com['com'] - $retcom;
				}
				$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>$newdiscom),array('recid'=>$opid));
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>$newdiscom,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=>$row3[0]->txn_clbal + $newdiscom, 'txn_type'=>'Distributer Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Distributer Commission'));
				if(1)
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$row4 = $this->db_model->get_trnx($cus_m);
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					if($row4)
					{
						if($com1['comtype']==2)
						{
							$txntype2 = "Master Commission";
							if($com1['com'] == '0')
							{
								$newmascom = $com['com'];
							}
							else
							{
								$newmascom = $com1['com'] - $com['com'];
							}
							$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));

							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							if($com1['com'] == '0')
							{
								$newmascom = $com['com'];
							}
							else
							{
								$newmascom = $com1['com'] - $com['com'];
							}
							$txntype2 = "Master Commission";
							$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
							return 1;
						}
					}
					else
					{
						if($com1['com'] == '0')
						{
							$newmascom = $com['com'];
						}
						else
						{
							$newmascom = $com1['com'] - $com['com'];
						}
						$txntype2 = "Master Commission";
						$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
						return 1;
					}
				}
				else
				{
					return 0;
				}
			}
			else
			{
				$txntype1 = "Distributer Commission";
				if($com['com'] == '0')
				{
					$newdiscom = $retcom;
				}
				else
				{
					$newdiscom = $com['com'] - $retcom;
				}
				$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>$newdiscom),array('recid'=>$opid));
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>'0', 'txn_crdt'=>$newdiscom,'txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=> $newdiscom, 'txn_type'=>$txntype1, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype1));
				if(1)
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$row4 = $this->db_model->get_trnx($cus_m);
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					if($row4)
					{
						if($com1['comtype']==2)
						{
							$txntype2 = "Master Commission";
							if($com1['com'] == '0')
							{
								$newmascom = $com['com'];
							}
							else
							{
								$newmascom = $com1['com'] - $com['com'];
							}
							$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						if($com1['com'] == '0')
						{
							$newmascom = $com['com'];
						}
						else
						{
							$newmascom = $com1['com'] - $com['com'];
						}
						$txntype2 = "Master Commission";
						$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
						return 1;
					}
				}
				else
				{
					return 0;
				}
			}
		}
		else
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				$newdiscom = $retcom - $com['com'];

				$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>$newdiscom),array('recid'=>$opid));

				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>$newdiscom,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=>$row3[0]->txn_clbal + $newdiscom, 'txn_type'=>'Distributer Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Distributer Commission'));
				if(1)
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$row4 = $this->db_model->get_trnx($cus_m);
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					if($row4)
					{
						if($com1['comtype']==1)
						{
							$txntype2 = "Master Commission";
							$newmascom = $com['com'] - $com1['com'];							
							$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						$newmascom = $com['com'] - $com1['com'];
						$txntype2 = "Master Commission";
						$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'-'.$newmascom),array('recid'=>$opid));
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date' =>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$newmascom, 'txn_type'=>$txntype2, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype2));
						return 1;
					}
				}
				else
				{
					return 0;
				}
			}
			else
			{				
				$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
				$cus_m = $d[0]->cus_reffer;
				$row4 = $this->db_model->get_trnx($cus_m);
				$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
				if($row4)
				{
					if($com1['comtype']==2)
					{
						$txntype2 = "Master Commission";
						$newmascom = $com['com'] - $com1['com'];						
						$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newmascom),array('recid'=>$opid));
						$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>$newmascom, 'txn_dbdt'=>'0', 'txn_clbal'=>$row4[0]->txn_clbal + $newmascom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
						if($mt)
						{
							return 1;
						}
						else
						{
							return 0;
						}
					}
					else
					{
						return 0;
					}
				}
			}
		}
	}
	
    public function member_commission_back($amount,$opcode,$cus_id,$opid)
	{
	 	$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('customers', array('cus_id'=>$cus_id));
		$cus_d = $r[0]->cus_reffer;
		$com = $this->commi(array('cus_id'=>$cus_d,'operator'=>$opcode,'amount'=>$amount));
		if($com['comtype']==2)
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				$discomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Distributer Commission'")->result();
				if($discomchk)
				{
					$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>'0'),array('recid'=>$opid));
					$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>'0','txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>$discomchk[0]->txn_crdt, 'txn_clbal'=>$row3[0]->txn_clbal - $discomchk[0]->txn_crdt, 'txn_type'=>'Distributer Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Distributer Commission Back'));
					if(1)
					{
						$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
						$cus_m = $d[0]->cus_reffer;
						$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
						$row4 = $this->db_model->get_trnx($cus_m);
						if($row4)
						{
							$mascomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Master Commission'")->result();
							if($mascomchk)
							{
								$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'0'),array('recid'=>$opid));
								$txntype2 = "Master Commission";
								$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$mascomchk[0]->txn_crdt, 'txn_clbal'=>$row4[0]->txn_clbal - $mascomchk[0]->txn_crdt, 'txn_type'=>'Master Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission Back'));
								if($mt)
								{
									return 1;
								}
								else
								{
									return 0;
								}
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						return 0;
					}
				}
				else
				{
					return 0;
				}				
			}
			else
			{
				return 0;
			}
		}
		else
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				$discomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Distributer Commission'")->result();
				if($discomchk)
				{
					if($opcode != '66')
					{
						$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>'0'),array('recid'=>$opid));
					}
					$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>'0','txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>$discomchk[0]->txn_crdt, 'txn_clbal'=>$row3[0]->txn_clbal - $discomchk[0]->txn_crdt, 'txn_type'=>'Distributer Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Distributer Commission Back'));
					if(1)
					{
						$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
						$cus_m = $d[0]->cus_reffer;
						$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
						$row4 = $this->db_model->get_trnx($cus_m);
						if($row4)
						{
							$mascomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Master Commission'")->result();
							if($mascomchk)
							{
								if($opcode != '66')
								{
									$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'0'),array('recid'=>$opid));
								}
								$txntype2 = "Master Commission";
								$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$mascomchk[0]->txn_crdt, 'txn_clbal'=>$row4[0]->txn_clbal - $mascomchk[0]->txn_crdt, 'txn_type'=>'Master Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission Back'));
								if($mt)
								{
									return 1;
								}
								else
								{
									return 0;
								}
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						return 0;
					}
				}
				else
				{
					$d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
					$cus_m = $d[0]->cus_reffer;
					$com1 = $this->commi(array('cus_id'=>$cus_m,'operator'=>$opcode,'amount'=>$amount));
					$row4 = $this->db_model->get_trnx($cus_m);
					if($row4)
					{
						$mascomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Master Commission'")->result();
						if($mascomchk)
						{
							if($opcode != '66')
							{
								$mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'0'),array('recid'=>$opid));
							}
							$txntype2 = "Master Commission Back";
							$mt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_m,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$row4[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$mascomchk[0]->txn_crdt, 'txn_clbal'=>$row4[0]->txn_clbal - $mascomchk[0]->txn_crdt, 'txn_type'=>'Master Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission Back'));
							if($mt)
							{
								return 1;
							}
							else
							{
								return 0;
							}
						}
						else
						{
							return 0;
						}
					}
					else
					{
						return 0;
					}
				}				
			}
			else
			{
				return 0;
			}
		}
	}
	
    public function member_commission_distributor($amount,$opcode,$cus_id,$opid)
	{
		$retcom = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch', array('recid'=>$opid), array('distributor'))[0]->distributor;
		$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('customers', array('cus_id'=>$cus_id));
		$cus_d = $r[0]->cus_reffer;
		$com = $this->commi(array('cus_id'=>$cus_d,'operator'=>$opcode,'amount'=>$amount));
		if($com['comtype']==2)
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				if($com['com'] == '0')
				{
					$newdiscom = $retcom;
				}
				else
				{
					$newdiscom = $com['com'] - $retcom;
				}
				$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newdiscom),array('recid'=>$opid));
				$dts1 = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>0),array('recid'=>$opid));
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>$newdiscom,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=>$row3[0]->txn_clbal + $newdiscom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));	
			}
			else
			{
				$txntype1 = "Master Commission";
				if($com['com'] == '0')
				{
					$newdiscom = $retcom;
				}
				else
				{
					$newdiscom = $com['com'] - $retcom;
				}
				$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newdiscom),array('recid'=>$opid));
				$dts1 = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>0),array('recid'=>$opid));
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>'0', 'txn_crdt'=>$newdiscom,'txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=> $newdiscom, 'txn_type'=>$txntype1, 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>$txntype1));
			}
		}
		else
		{
			$row3 = $this->db_model->get_trnx($cus_d);
				$txntype1 = "";
				$newdiscom = $retcom - $com['com'];
				$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$newdiscom),array('recid'=>$opid));
				$dts1 = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>0),array('recid'=>$opid));
				$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>$newdiscom,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_dbdt'=>'0', 'txn_clbal'=>$row3[0]->txn_clbal + $newdiscom, 'txn_type'=>'Master Commission', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission'));
		}
	}
	
    public function member_commission_master($amount,$opcode,$cus_id,$opid)
	{
		$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>0),array('recid'=>$opid));
		$dts1 = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>0),array('recid'=>$opid));		
	}

    public function distributor_commission_back($amount,$opcode,$cus_id,$opid)
	{
	 	$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('customers', array('cus_id'=>$cus_id));
		$cus_d = $r[0]->cus_reffer;
		$com = $this->commi(array('cus_id'=>$cus_d,'operator'=>$opcode,'amount'=>$amount));
		if($com['comtype']==2)
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				$discomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Master Commission'")->result();
				if($discomchk)
				{
					$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'0'),array('recid'=>$opid));
					$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>'0','txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>$discomchk[0]->txn_crdt, 'txn_clbal'=>$row3[0]->txn_clbal - $discomchk[0]->txn_crdt, 'txn_type'=>'Master Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission Back'));
				}
				else
				{
					return 0;
				}				
			}
			else
			{
				return 0;
			}
		}
		else
		{
			$row3 = $this->db_model->get_trnx($cus_d);
			if($row3)
			{
				$txntype1 = "";
				$discomchk = $this->db->query("select txn_crdt from exr_trnx where txn_recrefid='".$opid."' and txn_type='Master Commission'")->result();
				if($discomchk)
				{
					if($opcode != '66')
					{
						$dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'0'),array('recid'=>$opid));
					}
					$dt = $this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$opid,'txn_agentid'=>$cus_d, 'txn_opbal'=>$row3[0]->txn_clbal, 'txn_crdt'=>'0','txn_date' =>	date('Y-m-d h:i:s'),'txn_dbdt'=>$discomchk[0]->txn_crdt, 'txn_clbal'=>$row3[0]->txn_clbal - $discomchk[0]->txn_crdt, 'txn_type'=>'Master Commission Back', 'txn_time'=>time(), 'txn_ip'=>$ip, 'txn_comment'=>'Master Commission Back'));
				}
			}
			else
			{
				return 0;
			}
		}
	}

    public function commi($info)
	{
		$amount = $info['amount'];
		$cus_id  = $info['cus_id'];
		$cspk = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id),array('package_id'));
		//print_r($cspk);
		$op = $info['operator'];
		$rf = $this->db_model->getwhere('operator',array('opcodenew'=>$op));
		$data['operatorname'] = $rf[0]->operatorname;
		if($cspk){
		$pkdt = $this->db_model->getwhere('exr_packagecomm',array('packcomm_opcode'=>$op,'packcomm_name'=>$cspk[0]->package_id));
		if($pkdt){
		$term = $pkdt[0]->packagecom_tem;
		$type = $pkdt[0]->packagecom_type;
		$amttype = $pkdt[0]->packagecom_amttype;
		$commission = $pkdt[0]->packcomm_comm;
		$amt = 10;
		switch ($term) {
			case 1:
				if($amttype==1)//flat
					$amt = $commission;
				else if($amttype==2)//%
					$amt = round((($commission / 100) * $amount),3);
				break;
			case 2:
				$pkdts = $this->db->query("select * from commission_step where packcomm_id='".$pkdt[0]->packcomm_id."' and mintramnt<= '".$amount."' and maxtramnt >= '".$amount."'")->result();
				if($pkdts)
				{
					$type = $pkdts[0]->cmnt_take;
					if($pkdts[0]->cmnt_type==1)//flat
						$amt =  $pkdts[0]->commission;
					else if($pkdts[0]->cmnt_type==2) //%
						$amt = round((($pkdts[0]->commission / 100) * $amount),3);
				}
				break;
		}
		return  ( array('com'=>(float)$amt,'comtype'=>$type));
	}
	}
	}

    public function apicommi($info)
	{
		$amount = $info['amount'];
		$cus_id  = $info['cus_id'];
		$cspk = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id),array('package_id'));
		$op = $info['operator'];
		$rf = $this->db_model->getwhere('operator',array('opcodenew'=>$op));
		$data['operatorname'] = $rf[0]->operatorname;
		$pkdt = $this->db_model->getwhere('apicomm',array('packcomm_opcode'=>$op,'packcomm_name'=>$rf[0]->apisource));
		if($pkdt){
		$term = $pkdt[0]->packagecom_tem;
		$type = $pkdt[0]->packagecom_type;
		$amttype = $pkdt[0]->packagecom_amttype;
		$commission = $pkdt[0]->packcomm_comm;
		$amt = 10;
		switch ($term) {
			case 1:
				if($amttype==1)//flat
					$amt = $commission;
				else if($amttype==2)//%
					$amt = round((($commission / 100) * $amount),3);
				break;
			case 2:
				$pkdts = $this->db->query("select * from apicommission_step where packcomm_id='".$pkdt[0]->packcomm_id."' and mintramnt<= '".$amount."' and maxtramnt >= '".$amount."'")->result();
				if($pkdts)
				{
					$type = $pkdts[0]->cmnt_take;
					if($pkdts[0]->cmnt_type==1)//flat
						$amt =  $pkdts[0]->commission;
					else if($pkdts[0]->cmnt_type==2) //%
						$amt = round((($pkdts[0]->commission / 100) * $amount),3);
				}

				break;
		}
		return  ( array('com'=>(float)$amt,'comtype'=>$type));
	}
	}

    public function api_atabalance()
	{
		$request ="";
		$param['member_id'] = $this->config->item('ata_id');
		$param['api_password'] = $this->config->item('ata_password');
		$param['api_pin'] = $this->config->item('ata_pin');
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $ulx = $this->config->item('ata_url');
        $url = "$ulx/recharge_api/getbalance?".$request;                           
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $content = curl_exec($ch);
        curl_close($ch); 
        $data2=json_decode($content);
        return $data2->BALANCE;
	}
	

    /*public function ata_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = $this->config->item('ata_id');
		$param['api_password'] = $this->config->item('ata_password');
		$param['api_pin'] = $this->config->item('ata_pin');
        $param['number'] = $mobile;
		$param['opcode'] = $oper[0]->opcodenew;
		$param['amount'] = $txnamount;
		$param['request_id'] = $rectxnid;
        foreach($param as $key=>$val)
        {
            $request.= $key."=".urlencode($val);
            $request.= "&";
        }
		$request = substr($request, 0, -1);
        $ulx = $this->config->item('ata_url');
		$url = "$ulx/recharge_api/recharge?".$request;
		//echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch);
		$v =  json_decode($content);
		$trans_no = 'null';
		$SucOpID = 'null';
		$status = 'SUCCESS';
		if($v){
		if(($v->STATUS) == 'SUCCESS' && $SucOpID!='NULL')
		{
			$status = 'SUCCESS';
			$trans_no = $v->MEMBERREQID;
			$SucOpID = $v->OPTXNID;
		}
		else if(($v->STATUS) == 'PENDING' || ((($v->STATUS) == 'SUCCESS' && $SucOpID=='')))
		{ 
	$status = 'PENDING';
	}
		else if(($v->STATUS) == 'FAILED')
		{ $status = 'FAILED'; }
		else if(($v->STATUS) != 'SUCCESS' && ($v->STATUS) != 'PENDING' && ($v->STATUS) != 'FAILED')
		{ $status = 'FAILED'; }
		$apisms = $v->MESSAGE;
        $restime = time();
        $atabal = $this->api_atabalance();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `api_msg` = ?, `wellborn_trans_no` = ?, `optional1` = ?, `optional2` = ?, `optional3` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$apisms,$trans_no,$content,$atabal,$url,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
	}
}*/

    public function insert_atabalance()
	{
		$request ="";
		$param['member_id'] = '6280123081';
		$param['api_password'] = '351294';
		$param['api_pin'] = '166796';
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $ulx = 'http://insertservices.co.in/';
        $url = "$ulx/recharge_api/getbalance?".$request;                           
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $content = curl_exec($ch);
        curl_close($ch); 
        $data2=json_decode($content);
        return $data2->BALANCE;
	}
	
	
	 public function edigital_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = '7015871002';
		$param['api_password'] = '12135';
		$param['api_pin'] = '20592';
        $param['number'] = $mobile;
		$param['opcode'] = $oper[0]->opcodenew;
		$param['amount'] = $txnamount;
		$param['request_id'] = $rectxnid;
        foreach($param as $key=>$val)
        {
            $request.= $key."=".urlencode($val);
            $request.= "&";
        }
		$request = substr($request, 0, -1);
	
        $ulx = 'http://edigitalvillage.net';
		$url = "$ulx/recharge_api/recharge?".$request;
		$ch = curl_init();
		//echo $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch);
		$v =  json_decode($content);
		$trans_no = 'null';
		$SucOpID = 'null';
		$status = 'SUCCESS';
		if(($v->STATUS) == 'SUCCESS' && $SucOpID!='NULL')
		{
			$status = 'SUCCESS';
			$trans_no = $v->MEMBERREQID;
			$SucOpID = $v->OPTXNID;
		}
		else if(($v->STATUS) == 'PENDING' || ((($v->STATUS) == 'SUCCESS' && $SucOpID=='')))
		{ 
    	$status = 'PENDING';
    	}
		else if(($v->STATUS) == 'FAILED')
		{ $status = 'FAILED'; }
		else if(($v->STATUS) != 'SUCCESS' && ($v->STATUS) != 'PENDING' && ($v->STATUS) != 'FAILED')
		{ $status = 'FAILED'; }
		$apisms = $v->MESSAGE;
        $restime = time();
        $atabal = $this->insert_atabalance();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `api_msg` = ?, `wellborn_trans_no` = ?, `optional1` = ?, `optional2` = ?, `optional3` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$apisms,$trans_no,$content,$atabal,$url,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
	
    }

	public function ata_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = '8459815486';
		$param['api_password'] = '217073';
		$param['api_pin'] = '43000';
        $param['number'] = $mobile;
		$param['opcode'] = $oper[0]->opcodenew;
		$param['amount'] = $txnamount;
		$param['request_id'] = $rectxnid;
        foreach($param as $key=>$val)
        {
            $request.= $key."=".urlencode($val);
            $request.= "&";
        }
		$request = substr($request, 0, -1);
        $ulx = 'https://khushirecharge.in/recharge_api/recharge?';
		$url = "$ulx".$request;
	    $ch = curl_init();
		//echo $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch);
		//$var=curl_getinfo($ch);
		//print_r($var);exit;
		$v =  json_decode($content);
		$trans_no = 'null';
		$SucOpID = 'null';
		$status = 'SUCCESS';
		if(($v->STATUS) == 'SUCCESS' && $SucOpID!='NULL')
		{
			$status = 'SUCCESS';
			$trans_no = $v->MEMBERREQID;
			$SucOpID = $v->OPTXNID;
		}
		else if(($v->STATUS) == 'PENDING' || ((($v->STATUS) == 'SUCCESS' && $SucOpID=='')))
		{ 
    	$status = 'PENDING';
    	}
		else if(($v->STATUS) == 'FAILED')
		{ $status = 'FAILED'; }
		else if(($v->STATUS) != 'SUCCESS' && ($v->STATUS) != 'PENDING' && ($v->STATUS) != 'FAILED')
		{ $status = 'FAILED'; }
		$apisms = $v->MESSAGE;
        $restime = time();
        $atabal = $this->api_atabalance();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `api_msg` = ?, `wellborn_trans_no` = ?, `optional1` = ?, `optional2` = ?, `optional3` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$apisms,$trans_no,$content,$atabal,$url,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
	
    }

    public function insert_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = '6280123081';
		$param['api_password'] = '351294';
		$param['api_pin'] = '166796';
        $param['number'] = $mobile;
		$param['opcode'] = $oper[0]->opcodenew;
		$param['amount'] = $txnamount;
		$param['request_id'] = $rectxnid;
        foreach($param as $key=>$val)
        {
            $request.= $key."=".urlencode($val);
            $request.= "&";
        }
		$request = substr($request, 0, -1);
	
        $ulx = 'http://insertservices.co.in/';
		$url = "$ulx/recharge_api/recharge?".$request;
		$ch = curl_init();
		//echo $url;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch);
		$v =  json_decode($content);
		$trans_no = 'null';
		$SucOpID = 'null';
		$status = 'SUCCESS';
		if(($v->STATUS) == 'SUCCESS' && $SucOpID!='NULL')
		{
			$status = 'SUCCESS';
			$trans_no = $v->MEMBERREQID;
			$SucOpID = $v->OPTXNID;
		}
		else if(($v->STATUS) == 'PENDING' || ((($v->STATUS) == 'SUCCESS' && $SucOpID=='')))
		{ 
    	$status = 'PENDING';
    	}
		else if(($v->STATUS) == 'FAILED')
		{ $status = 'FAILED'; }
		else if(($v->STATUS) != 'SUCCESS' && ($v->STATUS) != 'PENDING' && ($v->STATUS) != 'FAILED')
		{ $status = 'FAILED'; }
		$apisms = $v->MESSAGE;
        $restime = time();
        $atabal = $this->insert_atabalance();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `api_msg` = ?, `wellborn_trans_no` = ?, `optional1` = ?, `optional2` = ?, `optional3` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$apisms,$trans_no,$content,$atabal,$url,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
	
}


    
 public function mpay_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,mpay');
        if($oper[0]->opsertype=='mobile'){
            $sid = '1';
        }elseif($oper[0]->opsertype=='dth'){
            $sid = '2';
        }elseif($oper[0]->opsertype=='datacard'){
            $sid = '3';
        }elseif($oper[0]->opsertype=='postpaid'){
            $sid = '4';
        }else{
            $sid = '0';
        }
		$request ="";
		$param['ConsumerNumber'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['OperatorID'] = $oper[0]->mpay;
		$param['ServiceID'] = $sid;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "http://mpayrecharge.in/API/rechargeapi.asmx/Recharge?";
     
      $uname='9690764506';
      $pass='Bhumi@05683';
      $pin='1234';
      $url= $uh1."Username=$uname&Password=$pass&RechargePin=$pin&$request&APIRequestID=$rectxnid"; 
      
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url;
		
		//Decode JSON
		$v =  json_decode($content);
		
		//print_r($v);
		$status = 'SUCCESS';
        if(($v->Status) == 'SUCCESS')
		{
			$status = 'SUCCESS';
		}
		else if(($v->Status) == 'PENDING')
		{
			$status = 'PENDING';
		}
		else if(($v->Status) == 'FAILURE')
		{
			$status = 'FAILED';
		}
		else if(($v->Status) != 'FAILURE' && ($v->Status) != 'PENDING' && ($v->Status) != 'SUCCESS')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->TransactionID;
			$SucOpID = $v->OPID;
			$lapu_balance = $v->closingBal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }    

 public function ambika_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,AMBIKA');
		$request ="";
		$param['Account'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['SPKey'] = $oper[0]->AMBIKA;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "https://ambikaecom.net/API/TransactionAPI?";
     
      $tok='6fd53b437ee482973d1a4c3da5b6c301';
      $ud='10609';
      $url= $uh1."Token=$tok&UserID=$ud&$request&APIRequestID=$rectxnid&Format=1"; 
      
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//echo $url;
		
		//Decode JSON
		$v =  json_decode($content);
		
		//print_r($v);
		$status = 'SUCCESS';
        if(($v->STATUS) == 'SUCCESS')
		{
			$status = 'SUCCESS';
		}
		else if(($v->STATUS) == 'PENDING')
		{
			$status = 'PENDING';
		}
		else if(($v->STATUS) == 'FAILED')
		{
			$status = 'FAILED';
		}
		else if(($v->STATUS) != 'FAILED' && ($v->STATUS) != 'PENDING' && ($v->STATUS) != 'SUCCESS')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->RPID;
			$SucOpID = $v->OPID;
			$lapu_balance = $v->BAL;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function skgrouptechnoogy_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing = '',$process = '',$optional1,$optional2)
    {
       if(!$billing){
            $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,skgrouptech');
            //print_r($oper);
            $request ="";
    		$param['Customernumber'] = $mobile;
    		$param['Amount'] = $txnamount;
    		$param['Optcode'] = $oper[0]->skgrouptech;
    		$param['Yourrchid'] = $rectxnid;
    		$param['UserID'] = 'infoeasypayall@gmail.com';
    		$param['optional1'] = $optional1;
    		$param['optional2'] = '';
    
            foreach($param as $key=>$val)
            {
               $request.= $key."=".urlencode($val);
               $request.= "&";
            }
    	    $request = substr($request, 0, -1);
    	    //print_r($request); 
    	    $uh1 = "https://www.skgrouptechnology.in/Recharge/Recharge_Get?";
    	    /*$url = "https://www.skgrouptechnology.in/Recharge/Recharge_Get?UserID=RegisterEMAIL&Customernumber=RechargeNumber&Optcode=OperatorCode&Amount=Amount&Yourrchid=RechargeRCHID&Tokenid=RegisterTOKEN&optional1=&optional2=";*/
            $Tokenid='aYU4IptUMsGcS0pK0a+kmQ==';
          
            $url= $uh1."Tokenid=$Tokenid&$request"; 
            //echo $url;
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
    		$content = curl_exec($ch); 
    		//Decode JSON
    		$v =  json_decode($content);
    		
    // 		print_r($v);
    		$status = 'SUCCESS';
            if(($v->Status) == 'Success')
    		{
    			$status = 'SUCCESS';
    		}
    		else if(($v->Status) == 'Pending')
    		{
    			$status = 'PENDING';
    		}
    		else if(($v->Status) == 'Failed')
    		{
    			$status = 'FAILED';
    		}
    		else if(($v->Status) != 'Success' && ($v->Status) != 'Pending' && ($v->Status) != 'Failed')
    		{
    			$status = 'FAILED';
    		}
    			$trans_no = $v->Transid;
    			$SucOpID = $v->RechargeID;
    			$lapu_balance = $v->Remain;
            $restime = time();
            $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
            $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
            $this->db_model->iud_data($sql8,$val8);
            $this->db_model->iud_data($sql8,$val8);
         	return $status;
        }else{
           $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,skgrouptech');
            //print_r($oper);
            $request ="";
    		$param['Customernumber'] = $mobile;
    		$param['Amount'] = $txnamount;
    		$param['Optcode'] = $oper[0]->skgrouptech;
    		$param['Yourrchid'] = $rectxnid;
    		$param['UserID'] = 'infoeasypayall@gmail.com';
    		$param['optional1'] = $optional1;
    		$param['optional2'] = '';
    
            foreach($param as $key=>$val)
            {
               $request.= $key."=".urlencode($val);
               $request.= "&";
            }
    	    $request = substr($request, 0, -1);
    	    //print_r($request); 
    	    $uh1 = "https://www.skgrouptechnology.in/Recharge/Recharge_Get?";
    	    /*$url = "https://www.skgrouptechnology.in/Recharge/Recharge_Get?UserID=RegisterEMAIL&Customernumber=RechargeNumber&Optcode=OperatorCode&Amount=Amount&Yourrchid=RechargeRCHID&Tokenid=RegisterTOKEN&optional1=&optional2=";*/
            $Tokenid='aYU4IptUMsGcS0pK0a+kmQ==';
          
            $url= $uh1."Tokenid=$Tokenid&$request"; 
            //echo $url;
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
    		$content = curl_exec($ch); 
    		//Decode JSON
    		$v =  json_decode($content);
    		
    // 		print_r($v);
    		$status = 'SUCCESS';
            if(($v->Status) == 'Success')
    		{
    			$status = 'SUCCESS';
    		}
    		else if(($v->Status) == 'Pending')
    		{
    			$status = 'PENDING';
    		}
    		else if(($v->Status) == 'Failed')
    		{
    			$status = 'FAILED';
    		}
    		else if(($v->Status) != 'Success' && ($v->Status) != 'Pending' && ($v->Status) != 'Failed')
    		{
    			$status = 'FAILED';
    		}
    			$trans_no = $v->Transid;
    			$SucOpID = $v->RechargeID;
    			$lapu_balance = $v->Remain;
            $restime = time();
            $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
            $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
            $this->db_model->iud_data($sql8,$val8);
            $this->db_model->iud_data($sql8,$val8);
         	return $status;
        }
     	
    }
    
    public function mahima_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = $this->config->item('ata_id');
		$param['api_password'] = $this->config->item('ata_password');
		$param['api_pin'] = $this->config->item('ata_pin');
        $param['number'] = $mobile;
		$param['opcode'] = $oper[0]->opcodenew;
		$param['amount'] = $txnamount;
		$param['request_id'] = $rectxnid;
        foreach($param as $key=>$val)
        {
            $request.= $key."=".urlencode($val);
            $request.= "&";
        }
		$request = substr($request, 0, -1);
        $ulx = $this->config->item('ata_url');
		$url = "$ulx/recharge_api/recharge?".$request;
		echo $url;
		echo $ulx;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch);
		$v =  json_decode($content);
		$trans_no = 'null';
		$SucOpID = 'null';
		$status = 'SUCCESS';
		if($v){
		if(($v->STATUS) == 'SUCCESS' && $SucOpID!='NULL')
		{
			$status = 'SUCCESS';
			$trans_no = $v->MEMBERREQID;
			$SucOpID = $v->OPTXNID;
		}
		else if(($v->STATUS) == 'PENDING' || ((($v->STATUS) == 'SUCCESS' && $SucOpID=='')))
		{ 
	$status = 'PENDING';
	}
		else if(($v->STATUS) == 'FAILED')
		{ $status = 'FAILED'; }
		else if(($v->STATUS) != 'SUCCESS' && ($v->STATUS) != 'PENDING' && ($v->STATUS) != 'FAILED')
		{ $status = 'FAILED'; }
		$apisms = $v->MESSAGE;
        $restime = time();
        $atabal = $this->api_atabalance();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `api_msg` = ?, `wellborn_trans_no` = ?, `optional1` = ?, `optional2` = ?, `optional3` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$apisms,$trans_no,$content,$atabal,$url,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
	}
}
    
    
     //vast API below
    public function vast_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,vast');
		$request ="";
		$param['Mobile'] = $mobile;
		$param['OptCode'] = $oper[0]->vast;
		$param['Amount'] = $txnamount;
		
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  
      $uh1 = "https://www.vastwebindia.com/API/API/Recharge?";
      
      $opc=$oper[0]->vast;
      $tok=$this->config->item('vast_token');
      $ud=$this->config->item('vast_user');
      $url= $uh1."Token=$tok&Userid=$ud&$request&rch_id=$rectxnid"; 
      
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//Decode JSON
		$v =  json_decode($content);
		$status = 'SUCCESS';
        if(($v->STATUS) == 'Success')
		{
			$status = 'SUCCESS';
		}
		else if(($v->STATUS) == 'Pending')
		{
			$status = 'PENDING';
		}
		else if(($v->STATUS) == 'Failed')
		{
			$status = 'FAILED';
		}
		else if(($v->Status) != 'Failed' && ($v->Status) != 'Pending' && ($v->Status) != 'Success')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->RID;
			$SucOpID = $v->Operatorid;
			$lapu_balance = $v->remainamount;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    

     //MAHADEV API below
    public function mahadev_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,MDRECHARGE');
		$request ="";
		$param['rech_num'] = $mobile;
		$param['opr_code'] = $qr_opcode->MDRECHARGE;
		$param['amount'] = $txnamount;
		
		//print_r($param); 
	

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  
      $uh1 = "http://mahadevmultirecharge.com/web-services/httpapi/recharge-request?";
      
      $opc=$oper[0]->MDRECHARGE;
      $tok='rmxa5g';
      $ud='ACC12503';
      $account_no ='9680765486';
      $apikey='6757f955-9077-4cfb-b047-50d2223d2bda';
      $url= $uh1."acc_no=$account_no&api_key=$apikey&$request&client_key=$rectxnid"; 
      echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//Decode JSON
		$v =  json_decode($content);
		 
		print_r($v);
		$status = 'received';
        if(($v->status) == 'received')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == 'success')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == 'failure')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != 'failure' && ($v->status) != 'suspense' && ($v->status) != 'success')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->AGENTID;
			$SucOpID = $v->OPID;
			$lapu_balance = $v->BAL;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
     public function rechargeking_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,rechargeking');
        //print_r($oper);
        $request ="";
		$param['rech_num'] = $mobile;
		$param['amount'] = $txnamount;
		$param['opr_code'] = $oper[0]->rechargeking;
		$param['client_key'] = $rectxnid;
		$param['acc_no'] = 'Acc13005';

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	    $request = substr($request, 0, -1);
	    //print_r($request); 
	    
	    //http://login.rechargeking.co.in/webservices/httpapi/recharge-request?acc_no=12345&api_key=<d86075c5- 5338-4410-973a-5e5722ce11fc>&opr_code=VF&rech_num=9839098390&amount=100&client_key=CK12345
	    
	    //http://login.rechargeking.co.in/web-services/httpapi/recharge-request-new?acc_no=ACC3&api_key=546f9-0cd2c7033b&opr_code=[OOO]&rech_num=[MMM]&amount=[aaa]&client_key=[TTT]
	    
	    $uh1 = "http://login.rechargeking.co.in/web-services/httpapi/recharge-request-new?";
     
        $api_key='729c57ae-495a-4629-9aef-54853de116aa';
      
        $url= $uh1."api_key=$api_key&$request"; 
        //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url;
		
		//Decode JSON
		$v =  explode(',',$content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v[0]) == 'success')
		{
			$status = 'SUCCESS';
		}
		else if(($v[0]) == 'received')
		{
			$status = 'SUCCESS';
		}
		else if(($v[0]) == 'failure')
		{
			$status = 'FAILED';
		}
		else if(($v[0]) != 'success' && ($v[0]) != 'received' && ($v[0]) != 'failure')
		{
			$status = 'FAILED';
		}
			$trans_no = $v[1];
			$SucOpID = $v[6];
			$lapu_balance = $v->bal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    
    public function bharatmoney_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,bharatmoney');
        //print_r($oper);
        $request ="";
		$param['rech_num'] = $mobile;
		$param['amount'] = $txnamount;
		$param['opr_code'] = $oper[0]->bharatmoney;
		$param['client_key'] = $rectxnid;
		$param['acc_no'] = 'Acc12892';

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	    $request = substr($request, 0, -1);
	    //print_r($request); 
	  
        //http://www.bharatmoneytransfer.in/web-services/httpapi/recharge-request?acc_no=ACC12345&api_key=d86075c5-%205338-4410-973a-5e5722ce11fc&opr_code=1&rech_num=9839098390&amount=100&client_key=CK12345
        $uh1 = "http://www.bharatmoneytransfer.in/web-services/httpapi/recharge-request?";
     
        $api_key='06776ccc-a758-42c0-831b-ced7a7d03918';
      
        $url= $uh1."api_key=$api_key&$request"; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url;
		
		//Decode JSON
		$v =  explode(',',$content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v[0]) == 'success')
		{
			$status = 'SUCCESS';
		}
		else if(($v[0]) == 'received')
		{
			$status = 'SUCCESS';
		}
		else if(($v[0]) == 'failure')
		{
			$status = 'FAILED';
		}
		else if(($v[0]) != 'success' && ($v[0]) != 'received' && ($v[0]) != 'failure')
		{
			$status = 'FAILED';
		}
			$trans_no = $v[1];
			$SucOpID = $v[6];
			$lapu_balance = $v->bal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    
    public function dynamic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing='',$process='',$apisource,$api_sequences){
        
        $api = $this->db_model->get_newdata('apisource',array('apisourceid'=>$apisource));
        $apisourcename = $api[0]->apisourcename;
        $mobilparam = $api[0]->api_mobile;
        $operatorparam = $api[0]->api_operator;
        $amountparam = $api[0]->api_amount;
        $txnidparam = $api[0]->api_txn_id;
        $api_authenticate = $api[0]->api_authenticate;
        $optional = $api[0]->api_optional;
        $api_hit_type = $api[0]->api_hit_type;
        $api_url = $api[0]->api_url;
        $api_resp_type = $api[0]->api_resp_type;
        
        $success_resp = $api[0]->succ_resp;
        $failed_resp = $api[0]->fail_resp;
        $pending_resp = $api[0]->pending_resp;
        
        $status_param = $api[0]->status_resp;
        $optxnid = $api[0]->op_txid_resp;

        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select="opsertype,$apisourcename");
        
		$request ="";
		$param[$mobilparam] = $number;
		$param[$operatorparam] = $oper[0]->$apisourcename;
		$param[$amountparam] = $amount5;
		$param[$txnidparam] = $rectxnid;
        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	    
	    $request = substr($request, 0, -1);
	  
	   if($api_hit_type == "get"){
    	    if(isset($optional)){
    		    $url= $api_url."?$request&$optional&$api_authenticate"; 
    		}else{
                $url= $api_url."?$request&$api_authenticate"; 
    		}
    		//echo $url;
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
    		$content = curl_exec($ch); 
    		//echo $content;
    		if($api_resp_type == "json"){
    		    $v =  json_decode($content);
	        }elseif($api_resp_type == "xml"){
	            $v = simplexml_load_string($content);
	        }
	        
	   }
	   
	   if($api_hit_type == "post"){
    	    if(isset($optional)){
    		    $url= $api_url; 
    		    $data = $request."&$optional&$api_authenticate";
    		}else{
                $url= $api_url; 
                $data = $request."&$api_authenticate";
    		}
    		//echo $url;
    		//echo $data;
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                "$data");
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
    		$content = curl_exec($ch);
    		//echo $content;
    		//Decode JSON
    		
    		if($api_resp_type == "json"){
    		    $v =  json_decode($content);
	        }elseif($api_resp_type == "xml"){
	            $v = simplexml_load_string($content);
	        }
	   }
	  
		//print_r($v);
		$status = 'SUCCESS';
        if(($v->$status_param) == $success_resp)
		{
			$status = 'SUCCESS';
		}
		else if(($v->$status_param) == $pending_resp)
		{
			$status = 'PENDING';
		}
		else if(($v->$status_param) == $failed_resp)
		{
		    //echo "welcome";
			$status = 'FAILED';
			if($opd3){
			    $sz = sizeof($api_sequences);
			    if($sz!=0){
			        
			        //print_r($api_sequences);
                    $new_apisource = $api_sequences[0];
            	    unset($api_sequences[0]);
            	    //echo "<br>";
            	    $api_sequences = array_values($api_sequences);
            	    if($new_apisource == '1'){
						$status = 'SUCCESS';
            	    }elseif($new_apisource == '2'){
						$status = 'FAILED';
            	    }elseif($new_apisource == '3'){
						$status = 'PENDING';
            	    }else{
    			        $this->dynamic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing='',$process='',$new_apisource,$api_sequences);
            	    }
			    }
			}
		}
		else if(($v->$status_param) != $failed_resp && ($v->$status_param) != $pending_resp && ($v->$status_param) != $success_resp)
		{
		    //echo "missing";
		    //if($opd3){
		        //echo "opd";
			    $sz = sizeof($api_sequences);
			    if($sz!=0){
			        //print_r($api_sequences);
                    $new_apisource = $api_sequences[0];
            	    unset($api_sequences[0]);
            	    //echo "<br>";
            	    $api_sequences = array_values($api_sequences);
            	    
            	    if($new_apisource == '1'){
						$status = 'SUCCESS';
					    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('apisourceid' => $new_apisource), array('recid' => $rectxnid));
					    //$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('responsecode' => $response2), array('recid' => $rectxnid));
            	    }elseif($new_apisource == '2'){
						$status = 'FAILED';
					    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('apisourceid' => $new_apisource), array('recid' => $rectxnid));
					    //$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('responsecode' => $response2), array('recid' => $rectxnid));
            	    }elseif($new_apisource == '3'){
						$status = 'PENDING';
					    //$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('responsecode' => $response2), array('recid' => $rectxnid));
            	    }else{
					    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('apisourceid' => $new_apisource), array('recid' => $rectxnid));
    			        $this->dynamic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing='',$process='',$new_apisource,$api_sequences);
            	    }
            	    
			    }
			//}
			$status = 'FAILED';
		}
		$trans_no = "";
		$SucOpID = $v->$optxnid;
		$lapu_balance = '';
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    
    public function tekdigi_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,tekdigi');
        //print_r($oper);
        $request ="";
		$param['Account'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['SPKey'] = $oper[0]->tekdigi;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "http://tekdigi.in/API/TransactionAPI?";
     
      $tok='7bb9abfb21e65a17276d58521c122f3d';
      $ud='1557';
      
      $url= $uh1."Token=$tok&UserID=$ud&$request&APIRequestID=$rectxnid&Format=1"; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url;
		
		//Decode JSON
		$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v->status) == '2')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == '1')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == '3')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != '3' && ($v->status) != '1' && ($v->status) != '2')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->rpid;
			$SucOpID = $v->opid;
			$lapu_balance = $v->bal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function rec_wale_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,rec_wale');
        //print_r($oper);
        $request ="";
        $param['MobileNo'] = "9690764506";
        $param['REQTYPE'] = "RECH";
		$param['CUSTNO'] = $mobile;
		$param['AMT'] = $txnamount;
		$param['SERCODE'] = $oper[0]->rec_wale;
		$param['STV'] = "1";
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "https://www.rechargewaleapi.com/RWARechargeAPI/RechargeAPI.aspx";
     
      $api_key='Zi8u5PPLo8KpeES3imogejr5JQZjNalM2vx';

      $url= $uh1; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "APIKey=$api_key&$request&REFNO=$rectxnid&RESPTYPE=JSON");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
// 		echo $url;
// 		print_r($content);exit;
		//Decode JSON
		$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v->TRNSTATUS) == '1' || $v->STATUSMSG=="Success")
		{
			$status = 'SUCCESS';
		}
		else if(($v->TRNSTATUS) == '0' || ($v->TRNSTATUS) == '6')
		{
			$status = 'PENDING';
		}
		else if(($v->TRNSTATUS) == '2' || ($v->TRNSTATUS) == '3' || ($v->TRNSTATUS) == '4' || ($v->TRNSTATUS) == '5')
		{
			$status = 'FAILED';
		}
		else if(($v->TRNSTATUS) != '3' && ($v->TRNSTATUS) != '1' && ($v->TRNSTATUS) != '2' && ($v->TRNSTATUS) != '0' && ($v->TRNSTATUS) != '4' && ($v->TRNSTATUS) != '5' && ($v->TRNSTATUS) != '6')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->OPRID;
			$SucOpID = $v->TRNID;
			$lapu_balance = $v->BAL;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function recharge_wale_bbps_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='',$optional1,$optional2,$mob)
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,recharge_wale_bbps');
        //print_r($oper);
        $request ="";
        $param['MobileNo'] = "9690764506";
        $param['REQTYPE'] = "BILLPAY";
		$param['CUSTNO'] = $mobile;
		$param['AMT'] = $txnamount;
		$param['SERCODE'] = $oper[0]->recharge_wale_bbps;
		$param['REFMOBILENO'] = $mob;
		$param['FIELD1'] = $optional1;
		$param['FIELD2'] = $optional2;
		$param['PCODE'] = '380054';
		$param['LAT'] = '23.1003804';
		$param['LONG'] = '72.5500481 ';
		$param['STV'] = "0";
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "https://www.rechargewaleapi.com/RWARechargeAPI/RechargeAPI.aspx";
     
      $api_key='Zi8u5PPLo8KpeES3imogejr5JQZjNalM2vx';

      $url= $uh1; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "APIKey=$api_key&$request&REFNO=$rectxnid&RESPTYPE=JSON");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
// 		echo $url;
// 		print_r($content);exit;
		//Decode JSON
		$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v->TRNSTATUS) == '1' || $v->STATUSMSG=="Success")
		{
			$status = 'SUCCESS';
		}
		else if(($v->TRNSTATUS) == '0' || ($v->TRNSTATUS) == '6')
		{
			$status = 'PENDING';
		}
		else if(($v->TRNSTATUS) == '2' || ($v->TRNSTATUS) == '3' || ($v->TRNSTATUS) == '4' || ($v->TRNSTATUS) == '5')
		{
			$status = 'FAILED';
		}
		else if(($v->TRNSTATUS) != '3' && ($v->TRNSTATUS) != '1' && ($v->TRNSTATUS) != '2' && ($v->TRNSTATUS) != '0' && ($v->TRNSTATUS) != '4' && ($v->TRNSTATUS) != '5' && ($v->TRNSTATUS) != '6')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->OPRID;
			$SucOpID = $v->TRNID;
			$lapu_balance = $v->BAL;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function jetwings_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    //   echo $txnamount; echo $mobile; echo $qr_opcode; echo $rectxnid; echo $billing; exit;
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,jetwings');
        //print_r($oper);
        $request ="";
		$param['Account'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['SPKey'] = $oper[0]->jetwings;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "http://jetwings.co.in/API/TransactionAPI?";
     
      $tok='d33d4936f17c49077dd80c8ba7ebff97';
      $ud='800';
      
      $url= $uh1."Token=$tok&UserID=$ud&$request&APIRequestID=$rectxnid&Format=1"; 
    //   echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url; exit;
		
		//Decode JSON
		$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v->status) == '2')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == '1')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == '3')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != '3' && ($v->status) != '1' && ($v->status) != '2')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->rpid;
			$SucOpID = $v->opid;
			$lapu_balance = $v->bal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function jetwings_rec_old($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,tekdigi');
        // print_r($oper); exit;
        $request ="";
		$param['Account'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['SPKey'] = $oper[0]->tekdigi;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 

      $uh1 = "http://jetwings.co.in/API/TransactionAPI?";
     
      $tok='c2089c5bff7fd0cba68ef4fde0c4db25';
      $ud='955';
      
      $url= $uh1."Token=$tok&UserID=$ud&$request&APIRequestID=$rectxnid&Format=1"; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url; exit;
		
		//Decode JSON
		$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v->status) == '2')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == '1')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == '3')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != '3' && ($v->status) != '1' && ($v->status) != '2')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->rpid;
			$SucOpID = $v->opid;
			$lapu_balance = $v->bal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    public function tiktik_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,tiktik');
        //print_r($oper);
        $request ="";
		$param['Account'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['SPKey'] = $oper[0]->tiktik;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "http://tiktikrecharge.com/API/TransactionAPI?";
     
      $tok='5c7bdb9d7c850c83ba0385caf3c8ec7a';
      $ud='3089';
      
      $url= $uh1."Token=$tok&UserID=$ud&$request&APIRequestID=$rectxnid&Format=1"; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
// 		echo $url;exit;
		
		//Decode JSON
		$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if(($v->status) == '2')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == '1')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == '3')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != '3' && ($v->status) != '1' && ($v->status) != '2')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->rpid;
			$SucOpID = $v->opid;
			$lapu_balance = $v->bal;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    
    public function mrobotic_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,MROBOTIC');
		$request ="";
		$param['mobile_no'] = $mobile;
		$param['company_id'] = $oper[0]->MROBOTIC;
		$param['amount'] = $txnamount;
		
// 		print_r($param); 
	    
	    $stv = false;
	    if($qr_opcode == '13'){
	        $stv = true;
	    }else if($qr_opcode == '15'){
	        $stv = true;
	    }else if($qr_opcode == '14'){
	        $stv = false;
	    }else{
	        $stv = false;
	    }

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  
      $uh1 = "https://mrobotics.in/api/recharge?";
      
      $opc=$oper[0]->MROBOTIC;
      
      $apikey='8e630d05-917a-4a81-ae98-de67fd5f6cc5';
      $url= $uh1; 
      
    //   echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "api_token=$apikey&$request&order_id=$rectxnid&is_stv=$stv");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//Decode JSON
		$v =  json_decode($content);
		//print_r($v);
		$status = 'received';
        if(($v->status) == 'success')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == 'suspense')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == 'failure')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != 'failure' && ($v->status) != 'suspense' && ($v->status) != 'success')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->id;
			$SucOpID = $v->tnx_id;
			$lapu_balance = $v->balance;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function AirtelPayment_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,AirtelPayment');
		$request ="";
		$param['mobile_no'] = $mobile;
		$param['company_id'] = '14';
		$param['subcompany_id'] = $oper[0]->AirtelPayment;
		$param['amount'] = $txnamount;
		
// 		print_r($param); 
	    
	    $stv = false;
	    if($qr_opcode == '13'){
	        $stv = true;
	    }else if($qr_opcode == '15'){
	        $stv = true;
	    }else if($qr_opcode == '14'){
	        $stv = false;
	    }else{
	        $stv = false;
	    }

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  
      $uh1 = "https://mrobotics.in/api/multirecharge?";
      
      $opc=$oper[0]->AirtelPayment;
      
      $apikey='8e630d05-917a-4a81-ae98-de67fd5f6cc5';
      $url= $uh1; 
      
    //   echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "api_token=$apikey&$request&order_id=$rectxnid&is_stv=false");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//Decode JSON
		$v =  json_decode($content);
		//print_r($v);
		$status = 'received';
        if(($v->status) == 'success')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == 'suspense')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == 'failure')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != 'failure' && ($v->status) != 'suspense' && ($v->status) != 'success')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->id;
			$SucOpID = $v->tnx_id;
			$lapu_balance = $v->balance;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
    public function airtelthanks_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,airtel_thanks');
		$request ="";
		$param['mobile_no'] = $mobile;
		$param['company_id'] = $oper[0]->airtel_thanks;
		$param['amount'] = $txnamount;
		
		//print_r($param); 
	    
	    $stv = false;
	    if($qr_opcode == '13'){
	        $stv = true;
	    }else if($qr_opcode == '15'){
	        $stv = true;
	    }else if($qr_opcode == '14'){
	        $stv = false;
	    }else{
	        $stv = false;
	    }

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  
      $uh1 = "https://mrobotics.in/api/recharge?";
      
      $opc=$oper[0]->airtel_thanks;
      
      $apikey='8e630d05-917a-4a81-ae98-de67fd5f6cc5';
      $url= $uh1; 
      
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "api_token=$apikey&$request&order_id=$rectxnid&is_stv=$stv");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//Decode JSON
		$v =  json_decode($content);
		//print_r($v);
		$status = 'received';
        if(($v->status) == 'success')
		{
			$status = 'SUCCESS';
		}
		else if(($v->status) == 'suspense')
		{
			$status = 'PENDING';
		}
		else if(($v->status) == 'failure')
		{
			$status = 'FAILED';
		}
		else if(($v->status) != 'failure' && ($v->status) != 'suspense' && ($v->status) != 'success')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->id;
			$SucOpID = $v->tnx_id;
			$lapu_balance = $v->balance;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
    
     //Handa API below
    public function handa_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,handa');
		$request ="";
		$param['mob'] = $mobile;
		$param['opt'] = $oper[0]->handa;
		$param['amt'] = $txnamount;
		
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  
      $uh1 = "http://rechargehanda.co.in/API/APIService.aspx?";
      
      $opc=$oper[0]->handa;
      $tok=$this->config->item('handa_userid');
      $ud=$this->config->item('handa_pass');
      $url= $uh1."userid=$tok&pass=$ud&$request&agentid=$rectxnid&fmt=json"; 
      
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
		
		//Decode JSON
		$v =  json_decode($content);
		$status = 'SUCCESS';
        if(($v->STATUS) == 'SUCCESS')
		{
			$status = 'SUCCESS';
		}
		else if(($v->STATUS) == 'PENDING')
		{
			$status = 'PENDING';
		}
		else if(($v->STATUS) == 'FAILED')
		{
			$status = 'FAILED';
		}
		else if(($v->Status) != 'FAILED' && ($v->Status) != 'PENDING' && ($v->Status) != 'SUCCESS')
		{
			$status = 'FAILED';
		}
			$trans_no = $v->AGENTID;
			$SucOpID = $v->OPID;
			$lapu_balance = $v->BAL;
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$content,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
public function offline_rec($rectxnid,$billing='',$process='')
    {
    		$status = 'SUCCESS';
			$apisms = 'Dtrect Submit';
			$record= 'OFFLINE';
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `api_msg` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$apisms,$record,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    } 

/* recall function start */
public function Recall() {
$querymi = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where status='PENDING'");
		$resultmi = $querymi->result();
		foreach($resultmi as $rmi)
		{
			$statusID = $rmi->recid;
			$queryfgf=$this->db->query("select * from return_data where recid='$statusID'");
			$resultfgf = $queryfgf->result();
			$numcount = $queryfgf->num_rows();
			$recall=null;
			if($numcount)
			{
				foreach($resultfgf as $fgf)
				{
				$recall=$fgf->return_data;
				}
			}
//	fopen($recall,'r');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $recall);
$result = curl_exec($ch);
curl_close($ch);
		}
}
 /* recall function end */  

public function call_back_status_update($info)
	{
		$cusdt = $this->db_model->getwhere('customers',array('cus_id'=>$info['cus_id'],'cus_type'=>'api'));
		if(!empty($cusdt))
		{
			$call_back = $cusdt[0]->call_back;
			$request = '';
	        $param['mobile'] = $info['number'];
	        $param['operator'] = $info['opcode'];
	        $param['amount'] = $info['amount'];
	        $param['status'] = $info['status'];
			$param['request_id'] = $info['request_id'];
	        $param['optxnid'] = $info['optxnid'];	
			foreach($param as $key=>$val)
			{
				$request.= $key."=".urlencode($val);
				$request.= "&";
			}
			$request = substr($request, 0, -1);
			$url = $call_back."?".$request;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_TIMEOUT,9000);
			$content = curl_exec($ch);
		}			
	}
}