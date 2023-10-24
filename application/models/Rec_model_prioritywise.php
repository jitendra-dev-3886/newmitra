<?php if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!'); 
class Rec_model extends CI_Model 
{ 
    //public $api_sequences = array();
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
	    $this->db->where('apipackage.apisourceid',$apisourceid);
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
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id' and is_flat = 0");
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
                            $last_balance = $this->db_model->comm_exr_trnx($cus_id);
                            $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>$commission_amount),array('recid'=>$rec_id));
    						if($last_balance)
    						{
        						$txntype = "Retailer Commission";
        						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$cus_id,'comm_txn_date'	=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>$last_balance[0]->txn_clbal, 'comm_txn_crdt'=>$commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=>$last_balance[0]->txn_clbal + $commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
    						}else{
    						    $txntype = "Retailer Commission";
        						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$cus_id,'comm_txn_date'	=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>'0', 'comm_txn_crdt'=>$commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=> $commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
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
                                $last_balance = $this->db_model->comm_exr_trnx($cus_reffer);
                                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>$dist_commission_amount),array('recid'=>$rec_id));
        						if($last_balance)
        						{
            						$txntype = "Distributor Commission";
            						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$cus_reffer,'comm_txn_date'=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>$last_balance[0]->txn_clbal, 'comm_txn_crdt'=>$dist_commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=>$last_balance[0]->txn_clbal + $dist_commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
        						}else{
        						    $txntype = "Distributor Commission";
            						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$cus_reffer,'comm_txn_date'	=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>'0', 'comm_txn_crdt'=>$dist_commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=> $dist_commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
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
                                $last_balance = $this->db_model->comm_exr_trnx($master_cus_reffer);
                                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$mast_commission_amount),array('recid'=>$rec_id));
        						if($last_balance)
        						{
            						$txntype = "Master Commission";
            						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$master_cus_reffer,'comm_txn_date'=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>$last_balance[0]->txn_clbal, 'comm_txn_crdt'=>$mast_commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=>$last_balance[0]->txn_clbal + $mast_commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
        						}else{
        						    $txntype = "Master Commission";
            						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$master_cus_reffer,'comm_txn_date'	=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>'0', 'comm_txn_crdt'=>$mast_commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=> $mast_commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
        						}
        							
                            }
                        }
                        
                    }else if($cus_type == "api"){
                        
                        $api_comm = $commission_scheme_package[0]->api_comm;
                        $comm_type = $commission_scheme_package[0]->type;
                        if($api_comm){
                            if($comm_type == "percent"){
                                $commission_amount = round((($api_comm / 100) * $amount),3);
                            }else{
                                $commission_amount = $api_comm;
                            }
                            $last_balance = $this->db_model->comm_exr_trnx($cus_id);
                            $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api'=>$commission_amount),array('recid'=>$rec_id));
                            //echo $this->db->last_query();exit;
    						if($last_balance)
    						{
        						$txntype = "API Commission";
        						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$cus_id,'comm_txn_date'	=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>$last_balance[0]->txn_clbal, 'comm_txn_crdt'=>$commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=>$last_balance[0]->txn_clbal + $commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
    						}else{
    						    $txntype = "API Commission";
        						$this->db_model->insert_update('comm_exr_trnx', array('comm_txn_recrefid'=>$rec_id,'comm_txn_agentid'=>$cus_id,'comm_txn_date'	=>	date('Y-m-d h:i:s'),'comm_txn_opbal'=>'0', 'comm_txn_crdt'=>$commission_amount, 'comm_txn_dbdt'=>'0', 'comm_txn_clbal'=> $commission_amount, 'comm_txn_type'=>$txntype, 'comm_txn_time'=>time(), 'comm_txn_ip'=>$this->input->ip_address(), 'comm_txn_comment'=>$txntype));
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
		if (array_key_exists("reqtxnid",$data))
		{
		  $reqtxnid = $data['reqtxnid'];
		}
		else
		{
		  $reqtxnid = "";
		}
		$cspk = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
        $limit_amount = $query[0]->limit_amount;
		$c_type= $cspk[0]->cus_type;
		$csamnt = $cspk[0]->cus_cutofamt + $amount5 + $limit_amount;
		$dis_cha = $this->commi(array('cus_id' => $cus_id, 'operator' => $data['operator'], 'amount' => $amount5));
		$apidis_cha = $this->apicommi(array('cus_id' => $cus_id, 'operator' => $data['operator'], 'amount' => $amount5));
		$com = $dis_cha['com'];  // if You Want then use as message
		$number = $data['number'];
		$operator = $data['operator'];
		$circle_name = $data['circle_name'];
		$rrr = '5';
		if ($dis_cha['comtype'] == '1')
			$amount = $amount5 + $com;
		$st = $this->db_model->get_trnx($cus_id);
		$lt = 0;
		if($st){
			$lt = $st[0]->txn_clbal;
			$r=$st;
		}
		if ($lt >= $csamnt)
		{
			$closing = $r[0]->txn_clbal;
			$total = $closing - $amount5;
			
			$cusapi = $cspk[0]->cus_api_source_id;
			$isblockcircle = $cspk[0]->isblockcircle;
			$isblockdenomination = $cspk[0]->isblockdenomination;
			$isblockapiswitching = $cspk[0]->isblockapiswitching;
			
			$opd = $this->db_model->getwhere('operator', array('opcodenew' => $operator));
			if(!$isblockcircle)
			$opd2 = $this->db_model->get_newdata('circle_api_switch', array('circle_name' => $circle_name));
			if(!$isblockdenomination)
			$opd1 = $this->db_model->get_newdata('amount_apisources', array('api_opcodenew' => $operator));
			if(!$isblockapiswitching)
			$opd3 = $this->db_model->get_newdata('priority_api', array('api_opcodenew' => $operator));
			$api_sequences = array();
			
			/*if(!$opd2){
    			if($cusapi != '' && !empty($cusapi) && $cusapi!='Select API' && $cusapi != NULL ){
    			    $apisource = $cusapi;
    			}else if($opd1 && $opd1[0]->amount_apisource_id != '' && !empty($opd1[0]->amount_apisource_id) && $opd1[0]->amount_apisource_id!='Select API' && $opd1[0]->amount_apisource_id != NULL){
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
    			}else{
    			    if(!isset($apisource)){
            			//echo $this->db->last_query();
            			$apisource = $opd[0]->apisource;
        			}
    			}
			}else{
			    if(!isset($apisource)){
        			//echo $this->db->last_query();
        			$apisource = $opd2[0]->circle_api_source_id;
    			}
			}*/
			
			if(!$opd2){
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
    			}elseif($opd3){
    			    //echo "api switch";
    			    $api_src = $this->db_model->getAlldata('select * from apisource');
	                $i = 0;
            	    foreach($api_src as $as){
            	        $col_name = $as->apisourcename;
            	        if($opd3[0]->$col_name!= '0' && $opd3[0]->$col_name!= NULL){
            	            $value1[$col_name]=$opd3[0]->$col_name;
            	        }
            	    }
                    asort($value1);  
                    //print_r($value1);
        	        foreach($value1 as $x=>$x_value)
                    {
                        $api_src = $this->db_model->getAlldata('select * from apisource where apisourcename="'.$x.'"');
                        $api_sequences[$i++]=$api_src[0]->apisourceid;
                    }
                    //print_r($api_sequences);
                    $apisource = $api_sequences[0];
            	    unset($api_sequences[0]);
            	    $api_sequences = array_values($api_sequences);
                    
    			}elseif($cusapi != '' && !empty($cusapi) && $cusapi!='Select API' && $cusapi != NULL ){
    			    $apisource = $cusapi;
    			}else{
    			    if(!isset($apisource)){
            			$apisource = $opd[0]->apisource;
    			    }
    			}    
			}else{
			    if(!isset($apisource)){
        			//echo $this->db->last_query();
        			$apisource = $opd2[0]->circle_api_source_id;
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
			else if($opd[0]->apisource >= '19')
				$qr_opcode = $opd[0]->opcodenew;
			
		
		// Here You can Add more api using else if()
			$ch = $this->check_recharge($amount5,$number, $ip,$cus_id,$time);
			if ($ch) {
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
						'txn_dbdt' => $amount5,
						'txn_clbal' => $row14114141[0]->txn_clbal - $amount5,
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
					if ($apisource == '1') {
						$response2 = 'SUCCESS';
					} elseif ($apisource == '2') {
						$response2 = 'FAILED';
					} elseif ($apisource == '3') {
						$response2 = 'PENDING';
					}
                   elseif($apisource == '4')
                    {
                        $response2 = $this->ata_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }
                    elseif($apisource == '5')
                    {
                        $response2 = $this->mahadev_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }
                    elseif($apisource == '6')
                    {
                        $response2 = $this->mrobotic_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }
                    elseif($apisource == '7')
                    {
                        $response2 = $this->vast_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }elseif($apisource == '8')
                    {
                        $response2 = $this->emoneygroup_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }elseif($apisource == '9')
                    {
                        $response2 = $this->mahima_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
                    }elseif($apisource == '10')
			        {
			            $response2 = $this->ambika_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }elseif($apisource == '11')
			        {
			            $response2 = $this->tekdigi_rec($amount5,$number,$qr_opcode,$rectxnid,$billing,$process);
			        }elseif($apisource >= '12')
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
							
							if($c_type == 'retailer' || $c_type == 'api'){
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
						$mess = array('type'=>1,'mess'=>'Your Recharge is Success ','balance'=>round($row2,2));
					} else if (in_array($response2, array("PENDING", "2", "Pending"))) {
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('status' => "PENDING"), array('recid' => $rectxnid));
						$row2 = $this->db_model->get_trnx($cus_id)[0]->txn_clbal;
						$mess = array('type'=>1,'mess'=>'Your Recharge is Submit','balance'=>round($row2,2));
					} else if (in_array($response2, array("SUBMIT_SUCCESS", "2", "Submit_Success"))) {
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('status' => "SUBMIT_SUCCESS"), array('recid' => $rectxnid));
						$row2 = $this->db_model->get_trnx($cus_id)[0]->txn_clbal;
						$mess = array('type'=>1,'mess'=>'Your Recharge is Submit Success','balance'=>round($row2,2));
					}
					else 
					{
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('status' => "FAILED"), array('recid' => $rectxnid));
						$row2 = $this->db_model->get_trnx($cus_id);
						$recdata19 = array(
								'txn_agentid' => $cus_id,
								'txn_recrefid' => $rectxnid,
								'txn_opbal' => $row2[0]->txn_clbal,
								'txn_crdt' => $amount5,
								'txn_dbdt' => '0',
								'txn_clbal' => $row2[0]->txn_clbal + $amount5,
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
			$mess = array('type'=>1,'mess'=>'Low Balance or capping exceed.','balance'=>$lt);
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
	
	
	
	public function ata_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = '';
		$param['api_password'] = '';
		$param['api_pin'] = '';
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
     
      $tok='';
      $ud='';
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
    
    public function mahima_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
    	$oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,opcodenew');
		$request ="";
		$param['member_id'] = "sdsdC";//$this->config->item('ata_id');
		$param['api_password'] = "csacdscd";//$this->config->item('ata_password');
		$param['api_pin'] = "dccscas";//$this->config->item('ata_pin');
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
// 		echo $url;
// 		echo $ulx;
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
      $tok= "jhv";//$this->config->item('vast_token');
      $ud="dsvsd";//$this->config->item('vast_user');
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
      $tok='';
      $ud='';
      $account_no ='';
      $apikey='';
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
    
    public function mrobotic_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
    {
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,MROBOTIC');
		$request ="";
		$param['mobile_no'] = $mobile;
		$param['company_id'] = $oper[0]->MROBOTIC;
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
      
      $opc=$oper[0]->MROBOTIC;
      
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
	public function emoneygroup_rec($txnamount,$mobile,$qr_opcode,$rectxnid,$billing='',$process='')
   {
       
        $oper = $this->db_model->get_newdata('operator',array('opcodenew'=>$qr_opcode),$select='opsertype,emoneygroup');
        $OPCode=$oper[0]->emoneygroup;
        //print_r($oper);
        $request ="";
		$param['Account'] = $mobile;
		$param['Amount'] = $txnamount;
		$param['SPKey'] = $oper[0]->emoneygroup;
		//print_r($param); 

        foreach($param as $key=>$val)
        {
           $request.= $key."=".urlencode($val);
           $request.= "&";
        }
	  $request = substr($request, 0, -1);
	  //print_r($request); 
	  

      $uh1 = "http://service.emoneygroup.co/request/emoney_group_xml.asmx/do_Recharge?";
      $ServiceType='MR';
     if($oper[0]->opsertype=='mobile' || $oper[0]->opsertype=='PREPAID' || $oper[0]->opsertype=='mobi'){
         $ServiceType='MR';
     }else if($oper[0]->opsertype=='post paid' || $oper[0]->opsertype=='postpaid'){
         $ServiceType='PP';
     }
     else if($oper[0]->opsertype=='dth'){
         $ServiceType='DH';
     }
      $UserName='';
      $APIKey='';
      
      $Merchantrefno=rand(00000000,99999999);
      $url= $uh1."UserName=$UserName&APIKey=$APIKey&Number=$mobile&Amount=$txnamount&OPCode=$OPCode&ServiceType=$ServiceType&Merchantrefno=$Merchantrefno"; 
      //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT,9000);
		$content = curl_exec($ch); 
// 		echo $url;
		$xml = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $v = json_decode($json,TRUE);
		//Decode JSON
	//	$v =  json_decode($content);
		
// 		print_r($v);
		$status = 'SUCCESS';
        if($v['Status'] == 'SUCCESS')
		{
			$status = 'SUCCESS';
		}
		else if($v['Status'] == 'PROCESSED')
		{
			$status = 'PENDING';
		}
		else if($v['Status'] == 'FAILURE')
		{
			$status = 'FAILED';
		}
		else
		{
			$status = 'FAILED';
		}
			$trans_no = $v['eMoney_OrderID'];
			$SucOpID = $v['OP_Transaction_ID'];
			$lapu_balance = $v['Balance'];
        $restime = time();
        $sql8 = "UPDATE `exr_rechrgreqexr_rechrgreq_fch` SET `restime` = ?, `status` = ?, `statusdesc` = ?, `optional2` = ?, `wellborn_trans_no` = ?, `optional1` = ? WHERE `recid` = ?";
        $val8 = array($restime,$status,$SucOpID,$lapu_balance,$trans_no,$json,$rectxnid);
        $this->db_model->iud_data($sql8,$val8);
        $this->db_model->iud_data($sql8,$val8);
     	return $status;
    }
}