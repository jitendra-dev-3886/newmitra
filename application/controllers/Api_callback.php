<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set("Asia/Calcutta");
class Api_callback extends CI_Controller 
{
	private $cus_info,$data,$reptype=1;
    public function __construct() 
	{
        parent::__construct();
        $this->load->helper(array('url','date','file','form','captcha'));
	    $this->load->library(array('session','form_validation','pagination','upload','email'));
	    $this->load->model(array('db_model','rec_model','msg_model','Encryption_model','Pushnotification_model'));
    }
    
    
    public function validateSubscriberId(){
        
        $response = file_get_contents('php://input');
        $decoded_response = json_decode($response);
        $subscriberId = $decoded_response->subscriberId;
        $data = $this->db_model->getAlldata("select cus_outlate,cus_name from customers where subscriberId_dynamicvpa = '$subscriberId' ");
        //echo $this->db->last_query();
        if($data){
            $cus_outlate = $data['0']->cus_name;
            $data = json_encode(array('status'=>'success','message'=>'Subscriber Found','cus_outlate'=>$cus_outlate));
            echo $data;
        }else{
            $data = json_encode(array('status'=>'failed','message'=>'Subscriber Not Found'));
            echo $data;
        }
    }

    public function dynamicVpaCallback()
    {
        
        $response = file_get_contents('php://input');
	    $logfile = 'upi_responselog.txt';
        $log = "\n\n".'GUID - '.date('Y-m-d H:i:s')."================================================================ \n";
        $log .= 'RESPONSE - '.$response."\n\n";
        file_put_contents($logfile, $log, FILE_APPEND | LOCK_EX);
        $insert_id = $this->db->insert_id();
        $decoded_response = json_decode($response);
        $status = $decoded_response->TxnStatus;
        $merchantTranId = $decoded_response->BankRRN;
        $cus_mobile = $decoded_response->userMobile;
        $PayerAmount = $decoded_response->PayerAmount;
        $PayerVA = $decoded_response->PayerVA;
        $PayerName = $decoded_response->PayerName;
        $subscriberId = $decoded_response->subscriberId;
        $enc_cus_mobile = $this->Encryption_model->encode($cus_mobile);
        $this->db_model->insert_update('icici_call_back_upi',array('data'=>'','decrypted_data'=>$response,'api_type'=>'Dynamic_vpa','dynamic_vpa'=>$subscriberId));
        $insert_id = $this->db->insert_id();
        $data = $this->db_model->getAlldata("SELECT * FROM customers WHERE subscriberId_dynamicvpa = '$subscriberId' ");
        if($data){
            $cus_id = $data[0]->cus_id;
            $checkIftransactionDone = $this->db_model->getAlldata("SELECT * FROM exr_trnx WHERE transaction_id='$merchantTranId'");
            if(0){
                
            }else{
                if($status == 'SUCCESS'){
                    $sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
            		$r = $this->db_model->getAlldata($sql);
            		if($r)
            		{
            			$closing = $r['0']->txn_clbal;
            			$total = $r['0']->txn_clbal + $PayerAmount;
            			$txn_dbdt = 0;
            		}else{
            		    $closing = 0;
            			$total = $r['0']->txn_clbal + $PayerAmount;
            			$txn_dbdt = 0;
            		}
        			$this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'upi_refid'=>$insert_id,'txn_opbal'=>$closing,'txn_crdt'=>$PayerAmount,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_checktime'=>$cus_id.time(),'txn_type'=>'ICICI DYNAMIC UPI','txn_time'=>time(),'txn_date'=>date('Y-m-d H:i:s'),'txn_ip'=>$_SERVER['REMOTE_ADDR'],'txn_comment'=>'ICICI DYNAMIC UPI','	transaction_id'=> $merchantTranId,'transaction_ref'=> '','upi_id'=>$PayerVA,'payer_name'=>$PayerName,'receiver_mobile'=>$receiver_mobile));
                    $title = 'UPI PAYMENT SUCCESS';
                    $message = "Amount of :- $PayerAmount RS ,Transfer by $PayerName through UPI ID $PayerVA.";
	                //$this->Pushnotification_model->push_notification_upi_collection($message,$title,$cus_id,$PayerAmount);
                }elseif($status == 'FAILURE'){
                    $title = 'UPI PAYMENT FAILED';
                    $message = "UPI transaction of Amount of :- $PayerAmount RS ,Failed.";
	                //$this->Pushnotification_model->push_notification_upi_collection($message,$title,$cus_id,$PayerAmount);
                }
            }
        }
        $output = json_decode($response);
    }
    
    
    public function status()
	{
		$display = $this->input->post('data');
		$rsp = json_decode($display,true);
		$status = 'SUCCESS';
        if($rsp['STATUS'] == '1')
		    $status =  'SUCCESS';
		else if($rsp['STATUS']== '2')
			$status =  'FAILED';
		else if($rsp['STATUS']== 'NOT SUCCESS')
			$status =  'FAILED';
		else if($rsp['STATUS']== 'ROLLBACK')
			$status =  'FAILED';
		$oid = $rsp['ORDERID'];
		$tid = $rsp['OPTRANSID'];
		$id = $rsp['PRID'];
		$ip = $this->input->ip_address();
		$datass = array(
			'return_data'	=> $this->current_full_url()
		);
		$this->db_model->insert('return_data',$datass);
		if($id != NULL)

		{
			$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
			$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
			if($r[0]->status != 'FAILED')
			{
				$cus_id = $r[0]->apiclid;
				$dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
				$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
				$com = $dis_cha['com'];
				if($r[0]->status != $status)
				{
					if($status == 'SUCCESS')
					{
						$recdata = array(
							'status' => $status,
							'responsecode' => $status,
							'statusdesc' => $tid
						);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
						$row14 = $this->db_model->get_trnx($cus_id);
						if ($dis_cha['comtype'] == '2')
						{
							if ($row14)
							{
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
								$recdata1 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
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
								$this->db_model->insert('exr_trnx', $recdata1);
	                    	}
						}
						$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
						$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
						$recdata = $this->db_model->get_rec($cus_id);
						$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
					}
					else if($status == 'FAILED')
					{
						if($r[0]->status == 'SUCCESS')
						{
							$recdata = array(
								'status' => $status,
								'responsecode' => $status,
								'statusdesc' => $tid
							);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
							$st = $this->db_model->get_trnx($cus_id);
							$recdata16 = array(
								'txn_agentid' => $cus_id,
								'txn_recrefid' => $id,
								'txn_opbal' => $st[0]->txn_clbal,
								'txn_crdt' => $r[0]->amount,
								'txn_dbdt' => '0',
								'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
								'txn_type' => 'recharge failed',
								'txn_time' => time(),
								'txn_date' => date('Y-m-d h:i:s'),
								'txn_checktime' => $cus_id . time(),
								'txn_ip' => $ip
							);
							$this->db_model->insert('exr_trnx', $recdata16);
							$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
							if($surchk)
							{
							$row14 = $this->db_model->get_trnx($cus_id);
								if ($dis_cha['comtype'] == '1') 
								{
									$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
									$recdata17 = array(
										'txn_agentid' => $cus_id,
										'txn_recrefid' => $id,
										'txn_opbal' => $row14[0]->txn_clbal,
										'txn_crdt' => $com,
										'txn_dbdt' => '0',
										'txn_clbal' => $row14[0]->txn_clbal + $com,
										'txn_type' => 'surcharge refund',
										'txn_time' => time(),
										'txn_date' => date('Y-m-d h:i:s'),
										'txn_checktime' => $cus_id . time(),
										'txn_ip' => $ip,
										'txn_comment' => 'Surcharge refund'
									);
									$this->db_model->insert('exr_trnx', $recdata17);
								}
							}
							$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
							if($dischk)
							{								
								$row15 = $this->db_model->get_trnx($cus_id);
								if ($dis_cha['comtype'] == '2')
								{
									if ($row15)
									{
										$recdata18 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row15[0]->txn_clbal,
											'txn_crdt' => '0',
											'txn_dbdt' => $com,
											'txn_clbal' => $row15[0]->txn_clbal - $com,
											'txn_type' => 'discount back',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Retailer Commission Back'
										);
										$this->db_model->insert('exr_trnx', $recdata18);
			                    	}
								}
							}
							$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
							$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
							$recdata = $this->db_model->get_rec($cus_id);
							$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
						}
						else
						{
							$recdata = array(
								'status' => $status,
								'responsecode' => $status,
								'statusdesc' => $tid
							);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
							$st = $this->db_model->get_trnx($cus_id);
							$recdata16 = array(
								'txn_agentid' => $cus_id,
								'txn_recrefid' => $id,
								'txn_opbal' => $st[0]->txn_clbal,
								'txn_crdt' => $r[0]->amount,
								'txn_dbdt' => '0',
								'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
								'txn_type' => 'recharge failed',
								'txn_time' => time(),
								'txn_date' => date('Y-m-d h:i:s'),
								'txn_checktime' => $cus_id . time(),
								'txn_ip' => $ip
							);
							$this->db_model->insert('exr_trnx', $recdata16);
							$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
							if($surchk)
							{
								$row14 = $this->db_model->get_trnx($cus_id);
								if ($dis_cha['comtype'] == '1') {
									$recdata17 = array(
										'txn_agentid' => $cus_id,
										'txn_recrefid' => $id,
										'txn_opbal' => $row14[0]->txn_clbal,
										'txn_crdt' => $com,
										'txn_dbdt' => '0',
										'txn_clbal' => $row14[0]->txn_clbal + $com,
										'txn_type' => 'surcharge refund',
										'txn_time' => time(),
										'txn_date' => date('Y-m-d h:i:s'),
										'txn_checktime' => $cus_id . time(),
										'txn_ip' => $ip,
										'txn_comment' => 'Surcharge refund'
									);
									$this->db_model->insert('exr_trnx', $recdata17);
								}
							}
							$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
							if($dischk)
							{								
								$row15 = $this->db_model->get_trnx($cus_id);
								if ($dis_cha['comtype'] == '2')
								{
									if ($row15)
									{
										$recdata18 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row15[0]->txn_clbal,
											'txn_crdt' => '0',
											'txn_dbdt' => $com,
											'txn_clbal' => $row15[0]->txn_clbal - $com,
											'txn_type' => 'discount back',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Retailer Commission Back'
										);
										$this->db_model->insert('exr_trnx', $recdata18);
			                    	}
								}
							}
						}
					}
				}
				else
				{
					$recdata = array(
						'status' => $status,
						'responsecode' => $status,
						'statusdesc' => $tid
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
				}
			}
			else
			{
			}
		}		
	}
	
	public function esymultiback()
    {
        
    $id = $this->input->get('CLIENTID');
    $ss = $this->input->get('STATUS');
    $tid = $this->input->get('OPERATORID');
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == 'SUCCESS' || $ss=='Success'){
       $status =  'SUCCESS';
            }
    else 
    $status =  'FAILED';
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}


    
    public function bharatmoneytransferback()
    {
        
    $id = $this->input->get('client_key');
    $ss = $this->input->get('status');
    $tid = $this->input->get('success_id');
    
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == 'success'){
       $status =  'SUCCESS';
    }elseif($ss == 'failure'){
        $status = 'FAILED';
    }
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->rec_model->getadminapicommission($apisource,$operator,$amount5);
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->creditRechargeCommission($cus_id,$r[0]->amount,$id,$r[0]->operator);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->rec_model->getadminapicommission($apisource,$operator,$amount5);
							
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}

    
    public function mpayback()
    {
        
    $id = $this->input->get('RequestId');
    $ss = $this->input->get('status');
    $tid = $this->input->get('TransID');
    
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('MobileNumber');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == 'SUCCESS' || $ss=='Success'){
       $status =  'SUCCESS';
            }
    else 
    $status =  'FAILED';
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $apisource = $r['0']->apisourceid;
    $operator = $r['0']->operator;
    $amount5 = $r['0']->amount;
    
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r['0']->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $id));
    $this->rec_model->member_commission($r['0']->amount,$r['0']->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    //$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}
    
    public function tekdigiback()
    {
        
    $id = $this->input->get('AGENTID');
    $ss = $this->input->get('STATUS');
    $tid = $this->input->get('LIVEID');
    
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == '2' || $ss=='2'){
       $status =  'SUCCESS';
            }
    else 
    $status =  'FAILED';
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($r['0']->apisourceid,$r['0']->operator,$r['0']->amount);
							
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($r['0']->apisourceid,$r['0']->operator,$r['0']->amount);
							
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}
    
    public function recwaleback()
    {
        
    $id = $this->input->get('ClientRefNo');
    $ss = $this->input->get('STATUS');
    $tid = $this->input->get('TrnID');
    
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == '1' || $ss=='1'){
       $status =  'SUCCESS';
            }
    else 
    $status =  'FAILED';
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($r['0']->apisourceid,$r['0']->operator,$r['0']->amount);
							
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($r['0']->apisourceid,$r['0']->operator,$r['0']->amount);
							
	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}

     public function sanjaymultirechargesback()
    {
        
    $id = $this->input->get('CLIENTID');
    $ss = $this->input->get('STATUS');
    $tid = $this->input->get('OPERATORID');
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == 'SUCCESS' || $ss=='Success'){
       $status =  'SUCCESS';
            }
    else 
    $status =  'FAILED';
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}


public function ataback()

	{
	    $mno = $this->input->get('mobile');
	    $mopc = $this->input->get('operator');
	    $mmnt = $this->input->get('amount');
		$ss = $this->input->get('status');
		$id = $this->input->get('request_id');
		$tid = $this->input->get('optxnid');
		$ip = $this->input->ip_address();
		$atabal = $this->rec_model->api_atabalance();
		$status =  'SUCCESS';
        if(strtoupper($ss) == 'SUCCESS'){ $status =  'SUCCESS'; }
		else if(strtoupper($ss)== 'FAILED'){ $status =  'FAILED'; }
		$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
		$medium = $r[0]->recmedium;
		$uid = $r[0]->uid;
		$checkid = $r[0]->recid;
		$promo='';
		$billing ='';
		$process='';
		$customer_id = $r[0]->apiclid;
		$ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
		$wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
				$this->db->where('recid',$id);
				$query = $this->db->get('return_data');
		$cus_type = $wx[0]->cus_type;
		$cus_city = $wx[0]->cus_city;
				if($query->num_rows() == 0)
				{
				    	$datass = array(
			            'return_data'	=> $this->current_full_url(),
	    	            'recid' => $id,
	                    'rd_status' => $ss,
	                    'rec_txn_id'    =>  $id,
	                    'succ_opt_id' => $tid,
						'number' => $r[0]->mobileno,
						'amount' => $r[0]->amount,
						'user_name' => $wx[0]->cus_name,
						'user_id' => $r[0]->apiclid,
						'media' => $r[0]->recmedium,
						'api_sourse' => $ww[0]->apisourcecode
	                	);
		                $this->db_model->insert('return_data',$datass);
				}
		if($id == $checkid)
		{
			$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
			if($r[0]->status == 'PENDING' || $status == 'FAILED')
			{
				$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
				if($r[0]->status != 'FAILED')
				{
					$cus_id = $r[0]->apiclid;
					$dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
					$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
					$com = $dis_cha['com'];
						if($status == 'SUCCESS')
						{
							$recdata = array(
								'status' => $status,
								'responsecode' => $status,
								'optional2'  => $atabal,
								'statusdesc' => $tid
							);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
							$row14 = $this->db_model->get_trnx($cus_id);
							if ($dis_cha['comtype'] == '2')
							{
								if ($row14)
								{
									$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
									$recdata1 = array(
										'txn_agentid' => $cus_id,
										'txn_recrefid' => $id,
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
									$this->db_model->insert('exr_trnx', $recdata1);
		                    	}
							}
							$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
							$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
							$recdata = $this->db_model->get_rec($cus_id);
							$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
						}
						else if($status == 'FAILED' || $r[0]->status != 'FAILED')
						{
							if($r[0]->status == 'SUCCESS')
							{
								$recdata = array(
									'status' => $status,
									'responsecode' => $status,
									'optional2'  => $atabal,
									'statusdesc' => $tid
								);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
								$st = $this->db_model->get_trnx($cus_id);
								$recdata16 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
									'txn_opbal' => $st[0]->txn_clbal,
									'txn_crdt' => $r[0]->amount,
									'txn_dbdt' => '0',
									'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
									'txn_type' => 'recharge failed',
									'txn_time' => time(),
									'txn_date' => date('Y-m-d h:i:s'),
									'txn_checktime' => $cus_id . time(),
									'txn_ip' => $ip
								);
								$this->db_model->insert('exr_trnx', $recdata16);
								$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
								if($surchk)
								{
									$row14 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '1') 
									{
										$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
										$recdata17 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row14[0]->txn_clbal,
											'txn_crdt' => $com,
											'txn_dbdt' => '0',
											'txn_clbal' => $row14[0]->txn_clbal + $com,
											'txn_type' => 'surcharge refund',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Surcharge refund'
										);
										$this->db_model->insert('exr_trnx', $recdata17);
									}
								}
								$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
								if($dischk)
								{								
									$row15 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '2')
									{
										if ($row15)
										{
											$recdata18 = array(
												'txn_agentid' => $cus_id,
												'txn_recrefid' => $id,
												'txn_opbal' => $row15[0]->txn_clbal,
												'txn_crdt' => '0',
												'txn_dbdt' => $com,
												'txn_clbal' => $row15[0]->txn_clbal - $com,
												'txn_type' => 'discount back',
												'txn_time' => time(),
												'txn_date' => date('Y-m-d h:i:s'),
												'txn_checktime' => $cus_id . time(),
												'txn_ip' => $ip,
												'txn_comment' => 'Retailer Commission Back'
											);
											$this->db_model->insert('exr_trnx', $recdata18);
				                    	}
									}
								}
								$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
								$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
								$recdata = $this->db_model->get_rec($cus_id);
								$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
//								$hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
								print_r($hello);
								exit();
							}
							else
							{
								$recdata = array(
									'status' => $status,
									'responsecode' => $status,
									'optional2'  => $atabal,
									'statusdesc' => $tid
								);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
								$st = $this->db_model->get_trnx($cus_id);
								$recdata16 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
									'txn_opbal' => $st[0]->txn_clbal,
									'txn_crdt' => $r[0]->amount,
									'txn_dbdt' => '0',
									'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
									'txn_type' => 'recharge failed',
									'txn_time' => time(),
									'txn_date' => date('Y-m-d h:i:s'),
									'txn_checktime' => $cus_id . time(),
									'txn_ip' => $ip
								);
								$this->db_model->insert('exr_trnx', $recdata16);
								$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
								if($surchk)
								{
									$row14 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '1') {
										$recdata17 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row14[0]->txn_clbal,
											'txn_crdt' => $com,
											'txn_dbdt' => '0',
											'txn_clbal' => $row14[0]->txn_clbal + $com,
											'txn_type' => 'surcharge refund',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Surcharge refund'
										);
										$this->db_model->insert('exr_trnx', $recdata17);
									}
								}
								$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
								if($dischk)
								{								
									$row15 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '2')
									{
										if ($row15)
										{
											$recdata18 = array(
												'txn_agentid' => $cus_id,
												'txn_recrefid' => $id,
												'txn_opbal' => $row15[0]->txn_clbal,
												'txn_crdt' => '0',
												'txn_dbdt' => $com,
												'txn_clbal' => $row15[0]->txn_clbal - $com,
												'txn_type' => 'discount back',
												'txn_time' => time(),
												'txn_date' => date('Y-m-d h:i:s'),
												'txn_checktime' => $cus_id . time(),
												'txn_ip' => $ip,
												'txn_comment' => 'Retailer Commission Back'
											);
											$this->db_model->insert('exr_trnx', $recdata18);
				                    	}
									}
								}
							}
						}
				}
				else
				{
					$result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
					if($result < 2){
					    }else{
					
					}							
				}
			}
		}
	}
	
	public function aarihantback()

	{
	    $mno = $this->input->get('mobile');
	    $mopc = $this->input->get('Operator_id');
	    $mmnt = $this->input->get('amount');
		$ss = $this->input->get('Status');
		$id = $this->input->get('request_id');
		$tid = $this->input->get('Transaction_id');
		$ip = $this->input->ip_address();
		$atabal = $this->rec_model->api_atabalance();
		$status =  'SUCCESS';
        if(strtoupper($ss) == 'SUCCESS'){ $status =  'SUCCESS'; }
		else if(strtoupper($ss)== 'FAILED'){ $status =  'FAILED'; }
		$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
		$medium = $r[0]->recmedium;
		$uid = $r[0]->uid;
		$checkid = $r[0]->recid;
		$promo='';
		$billing ='';
		$process='';
		$customer_id = $r[0]->apiclid;
		$ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
		$wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
				$this->db->where('recid',$id);
				$query = $this->db->get('return_data');
		$cus_type = $wx[0]->cus_type;
		$cus_city = $wx[0]->cus_city;
				if($query->num_rows() == 0)
				{
				    	$datass = array(
			            'return_data'	=> $this->current_full_url(),
	    	            'recid' => $id,
	                    'rd_status' => $ss,
	                    'rec_txn_id'    =>  $id,
	                    'succ_opt_id' => $tid,
						'number' => $r[0]->mobileno,
						'amount' => $r[0]->amount,
						'user_name' => $wx[0]->cus_name,
						'user_id' => $r[0]->apiclid,
						'media' => $r[0]->recmedium,
						'api_sourse' => $ww[0]->apisourcecode
	                	);
		                $this->db_model->insert('return_data',$datass);
				}
		if($id == $checkid)
		{
			$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
			if($r[0]->status == 'PENDING' || $status == 'FAILED')
			{
				$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
				if($r[0]->status != 'FAILED')
				{
					$cus_id = $r[0]->apiclid;
					$dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
					$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
					$com = $dis_cha['com'];
						if($status == 'SUCCESS')
						{
							$recdata = array(
								'status' => $status,
								'responsecode' => $status,
								'optional2'  => $atabal,
								'statusdesc' => $tid
							);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
							$row14 = $this->db_model->get_trnx($cus_id);
							if ($dis_cha['comtype'] == '2')
							{
								if ($row14)
								{
									$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
									$recdata1 = array(
										'txn_agentid' => $cus_id,
										'txn_recrefid' => $id,
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
									$this->db_model->insert('exr_trnx', $recdata1);
		                    	}
							}
							$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
							$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
							$recdata = $this->db_model->get_rec($cus_id);
							$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
						}
						else if($status == 'FAILED' || $r[0]->status != 'FAILED')
						{
							if($r[0]->status == 'SUCCESS')
							{
								$recdata = array(
									'status' => $status,
									'responsecode' => $status,
									'optional2'  => $atabal,
									'statusdesc' => $tid
								);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
								$st = $this->db_model->get_trnx($cus_id);
								$recdata16 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
									'txn_opbal' => $st[0]->txn_clbal,
									'txn_crdt' => $r[0]->amount,
									'txn_dbdt' => '0',
									'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
									'txn_type' => 'recharge failed',
									'txn_time' => time(),
									'txn_date' => date('Y-m-d h:i:s'),
									'txn_checktime' => $cus_id . time(),
									'txn_ip' => $ip
								);
								$this->db_model->insert('exr_trnx', $recdata16);
								$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
								if($surchk)
								{
									$row14 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '1') 
									{
										$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
										$recdata17 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row14[0]->txn_clbal,
											'txn_crdt' => $com,
											'txn_dbdt' => '0',
											'txn_clbal' => $row14[0]->txn_clbal + $com,
											'txn_type' => 'surcharge refund',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Surcharge refund'
										);
										$this->db_model->insert('exr_trnx', $recdata17);
									}
								}
								$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
								if($dischk)
								{								
									$row15 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '2')
									{
										if ($row15)
										{
											$recdata18 = array(
												'txn_agentid' => $cus_id,
												'txn_recrefid' => $id,
												'txn_opbal' => $row15[0]->txn_clbal,
												'txn_crdt' => '0',
												'txn_dbdt' => $com,
												'txn_clbal' => $row15[0]->txn_clbal - $com,
												'txn_type' => 'discount back',
												'txn_time' => time(),
												'txn_date' => date('Y-m-d h:i:s'),
												'txn_checktime' => $cus_id . time(),
												'txn_ip' => $ip,
												'txn_comment' => 'Retailer Commission Back'
											);
											$this->db_model->insert('exr_trnx', $recdata18);
				                    	}
									}
								}
								$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
								$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
								$recdata = $this->db_model->get_rec($cus_id);
								$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
//								$hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
								print_r($hello);
								exit();
							}
							else
							{
								$recdata = array(
									'status' => $status,
									'responsecode' => $status,
									'optional2'  => $atabal,
									'statusdesc' => $tid
								);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
								$st = $this->db_model->get_trnx($cus_id);
								$recdata16 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
									'txn_opbal' => $st[0]->txn_clbal,
									'txn_crdt' => $r[0]->amount,
									'txn_dbdt' => '0',
									'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
									'txn_type' => 'recharge failed',
									'txn_time' => time(),
									'txn_date' => date('Y-m-d h:i:s'),
									'txn_checktime' => $cus_id . time(),
									'txn_ip' => $ip
								);
								$this->db_model->insert('exr_trnx', $recdata16);
								$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
								if($surchk)
								{
									$row14 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '1') {
										$recdata17 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row14[0]->txn_clbal,
											'txn_crdt' => $com,
											'txn_dbdt' => '0',
											'txn_clbal' => $row14[0]->txn_clbal + $com,
											'txn_type' => 'surcharge refund',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Surcharge refund'
										);
										$this->db_model->insert('exr_trnx', $recdata17);
									}
								}
								$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
								if($dischk)
								{								
									$row15 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '2')
									{
										if ($row15)
										{
											$recdata18 = array(
												'txn_agentid' => $cus_id,
												'txn_recrefid' => $id,
												'txn_opbal' => $row15[0]->txn_clbal,
												'txn_crdt' => '0',
												'txn_dbdt' => $com,
												'txn_clbal' => $row15[0]->txn_clbal - $com,
												'txn_type' => 'discount back',
												'txn_time' => time(),
												'txn_date' => date('Y-m-d h:i:s'),
												'txn_checktime' => $cus_id . time(),
												'txn_ip' => $ip,
												'txn_comment' => 'Retailer Commission Back'
											);
											$this->db_model->insert('exr_trnx', $recdata18);
				                    	}
									}
								}
							}
						}
				}
				else
				{
					$result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
					if($result < 2){
					    }else{
					
					}							
				}
			}
		}
	}
	
    public function zeetopupindiaback()

	{
	    $message = explode('|',$this->get('Message'));
	    $mno = $this->input->get('mobile');
	    $mopc = $this->input->get('Operator_id');
	    $mmnt = $this->input->get('amount');
		$ss = $message[0];
		$id = $message[2];
		$tid = $message[3];
		$ip = $this->input->ip_address();
		$atabal = $this->rec_model->api_atabalance();
		$status =  'SUCCESS';
        if(strtoupper($ss) == '0'){ $status =  'SUCCESS'; }
		else if(strtoupper($ss)== '1'){ $status =  'FAILED'; }
		$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
		$medium = $r[0]->recmedium;
		$uid = $r[0]->uid;
		$checkid = $r[0]->recid;
		$promo='';
		$billing ='';
		$process='';
		$customer_id = $r[0]->apiclid;
		$ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
		$wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
				$this->db->where('recid',$id);
				$query = $this->db->get('return_data');
		$cus_type = $wx[0]->cus_type;
		$cus_city = $wx[0]->cus_city;
				if($query->num_rows() == 0)
				{
				    	$datass = array(
			            'return_data'	=> $this->current_full_url(),
	    	            'recid' => $id,
	                    'rd_status' => $ss,
	                    'rec_txn_id'    =>  $id,
	                    'succ_opt_id' => $tid,
						'number' => $r[0]->mobileno,
						'amount' => $r[0]->amount,
						'user_name' => $wx[0]->cus_name,
						'user_id' => $r[0]->apiclid,
						'media' => $r[0]->recmedium,
						'api_sourse' => $ww[0]->apisourcecode
	                	);
		                $this->db_model->insert('return_data',$datass);
				}
		if($id == $checkid)
		{
			$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
			if($r[0]->status == 'PENDING' || $status == 'FAILED')
			{
				$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
				if($r[0]->status != 'FAILED')
				{
					$cus_id = $r[0]->apiclid;
					$dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
					$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
					$com = $dis_cha['com'];
						if($status == 'SUCCESS')
						{
							$recdata = array(
								'status' => $status,
								'responsecode' => $status,
								'optional2'  => $atabal,
								'statusdesc' => $tid
							);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
							$row14 = $this->db_model->get_trnx($cus_id);
							if ($dis_cha['comtype'] == '2')
							{
								if ($row14)
								{
									$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
									$recdata1 = array(
										'txn_agentid' => $cus_id,
										'txn_recrefid' => $id,
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
									$this->db_model->insert('exr_trnx', $recdata1);
		                    	}
							}
							$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
							$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
							$recdata = $this->db_model->get_rec($cus_id);
							$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
						}
						else if($status == 'FAILED' || $r[0]->status != 'FAILED')
						{
							if($r[0]->status == 'SUCCESS')
							{
								$recdata = array(
									'status' => $status,
									'responsecode' => $status,
									'optional2'  => $atabal,
									'statusdesc' => $tid
								);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
								$st = $this->db_model->get_trnx($cus_id);
								$recdata16 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
									'txn_opbal' => $st[0]->txn_clbal,
									'txn_crdt' => $r[0]->amount,
									'txn_dbdt' => '0',
									'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
									'txn_type' => 'recharge failed',
									'txn_time' => time(),
									'txn_date' => date('Y-m-d h:i:s'),
									'txn_checktime' => $cus_id . time(),
									'txn_ip' => $ip
								);
								$this->db_model->insert('exr_trnx', $recdata16);
								$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
								if($surchk)
								{
									$row14 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '1') 
									{
										$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
										$recdata17 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row14[0]->txn_clbal,
											'txn_crdt' => $com,
											'txn_dbdt' => '0',
											'txn_clbal' => $row14[0]->txn_clbal + $com,
											'txn_type' => 'surcharge refund',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Surcharge refund'
										);
										$this->db_model->insert('exr_trnx', $recdata17);
									}
								}
								$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
								if($dischk)
								{								
									$row15 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '2')
									{
										if ($row15)
										{
											$recdata18 = array(
												'txn_agentid' => $cus_id,
												'txn_recrefid' => $id,
												'txn_opbal' => $row15[0]->txn_clbal,
												'txn_crdt' => '0',
												'txn_dbdt' => $com,
												'txn_clbal' => $row15[0]->txn_clbal - $com,
												'txn_type' => 'discount back',
												'txn_time' => time(),
												'txn_date' => date('Y-m-d h:i:s'),
												'txn_checktime' => $cus_id . time(),
												'txn_ip' => $ip,
												'txn_comment' => 'Retailer Commission Back'
											);
											$this->db_model->insert('exr_trnx', $recdata18);
				                    	}
									}
								}
								$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
								$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
								$recdata = $this->db_model->get_rec($cus_id);
								$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
//								$hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
								print_r($hello);
								exit();
							}
							else
							{
								$recdata = array(
									'status' => $status,
									'responsecode' => $status,
									'optional2'  => $atabal,
									'statusdesc' => $tid
								);
								$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
								$st = $this->db_model->get_trnx($cus_id);
								$recdata16 = array(
									'txn_agentid' => $cus_id,
									'txn_recrefid' => $id,
									'txn_opbal' => $st[0]->txn_clbal,
									'txn_crdt' => $r[0]->amount,
									'txn_dbdt' => '0',
									'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
									'txn_type' => 'recharge failed',
									'txn_time' => time(),
									'txn_date' => date('Y-m-d h:i:s'),
									'txn_checktime' => $cus_id . time(),
									'txn_ip' => $ip
								);
								$this->db_model->insert('exr_trnx', $recdata16);
								$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
								if($surchk)
								{
									$row14 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '1') {
										$recdata17 = array(
											'txn_agentid' => $cus_id,
											'txn_recrefid' => $id,
											'txn_opbal' => $row14[0]->txn_clbal,
											'txn_crdt' => $com,
											'txn_dbdt' => '0',
											'txn_clbal' => $row14[0]->txn_clbal + $com,
											'txn_type' => 'surcharge refund',
											'txn_time' => time(),
											'txn_date' => date('Y-m-d h:i:s'),
											'txn_checktime' => $cus_id . time(),
											'txn_ip' => $ip,
											'txn_comment' => 'Surcharge refund'
										);
										$this->db_model->insert('exr_trnx', $recdata17);
									}
								}
								$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
								if($dischk)
								{								
									$row15 = $this->db_model->get_trnx($cus_id);
									if ($dis_cha['comtype'] == '2')
									{
										if ($row15)
										{
											$recdata18 = array(
												'txn_agentid' => $cus_id,
												'txn_recrefid' => $id,
												'txn_opbal' => $row15[0]->txn_clbal,
												'txn_crdt' => '0',
												'txn_dbdt' => $com,
												'txn_clbal' => $row15[0]->txn_clbal - $com,
												'txn_type' => 'discount back',
												'txn_time' => time(),
												'txn_date' => date('Y-m-d h:i:s'),
												'txn_checktime' => $cus_id . time(),
												'txn_ip' => $ip,
												'txn_comment' => 'Retailer Commission Back'
											);
											$this->db_model->insert('exr_trnx', $recdata18);
				                    	}
									}
								}
							}
						}
				}
				else
				{
					$result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
					if($result < 2){
					    }else{
					
					}							
				}
			}
		}
	}

	public function mroboticback()

    {
        if(isset($_GET['order_id'])){
       $id = $this->input->get('order_id');
    $ss = $this->input->get('status');
    $tid = $this->input->get('company_id');
    //$lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('mobile_no');
    $recamount = $this->input->get('amount');
    //$txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    if(($tid!='Failed' || $tid!='Unable to login') &&  $ss!='Failed') {
            if($ss == 'SUCCESS' || $ss=='Success'){
       $status =  'SUCCESS';
            }
    else if($ss== 'FAILED' || $ss== 'Failed'){
    $status =  'FAILED';
    }
    }
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
        }
}

	public function rechw()
    {
   $mno = $this->input->get('mobile');
   $mopc = $this->input->get('operator');
   $mmnt = $this->input->get('amount');
    $ss = $this->input->get('status');
    $id = $this->input->get('agentid');
    $tid = $this->input->get('opid'); //txn ref id
    $ip = $this->input->ip_address();
    
    $status =  'SUCCESS';
            if(strtoupper($ss) == 'SUCCESS'){ $status =  'SUCCESS'; }
    else if(strtoupper($ss)== 'FAILED'){ $status =  'FAILED'; }
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $checkid = $r[0]->recid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($query->num_rows() == 0)
    {
        $datass = array(
               'return_data' => $this->current_full_url(),
                   'recid' => $id,
                       'rd_status' => $ss,
                       'rec_txn_id'    =>  $id,
                       'succ_opt_id' => $tid,
    'number' => $r[0]->mobileno,
    'amount' => $r[0]->amount,
    'user_name' => $wx[0]->cus_name,
    'user_id' => $r[0]->apiclid,
    'media' => $r[0]->recmedium,
    'api_sourse' => $ww[0]->apisourcecode
                    );
                   $this->db_model->insert('return_data',$datass);
    }
    if($id == $checkid)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    if($r[0]->status == 'PENDING' || $status == 'FAILED')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $status,
    'optional2'  => $atabal,
    'statusdesc' => $tid
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $r[0]->status != 'FAILED')
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $status,
    'optional2'  => $atabal,
    'statusdesc' => $tid
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    // $hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
    print_r($hello);
    exit();
    }
    else
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $status,
    'optional2'  => $atabal,
    'statusdesc' => $tid
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
       }else{
    
    }
    }
    }
    }
}


public function mback()

    {
       $id = $this->input->get('client_key');
    $ss = $this->input->get('status');
    $tid = $this->input->get('success_id');
   
    $rechargeno = $this->input->get('rech_no');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('trans_no');
    $ip = $this->input->ip_address();
    
    
     echo '<pre>';
    print_r($ss);
    echo '</pre>';
    // echo 'xx';die;
    
            if($ss =='SUCCESS' || $ss=='success'){
       $status =  'SUCCESS';
            }
    else if($ss== 'FAILURE' || $ss== 'failure'){
    $status =  'FAILED';
    }
    
    
    $api_msg=$status;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
    'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
   
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
         
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
   
    if($r[0]->status != 'FAILED')
    {
         
   
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    /*echo '<pre>';
    print_r($r);
    echo '</pre>';
     echo 'xx';die;*/
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $txn_now,
    'statusdesc' => $tid,
    'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $txn_now,
    'statusdesc' => $tid,
    'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $txn_now,
    'statusdesc' => $tid,
    'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}

public function mahadevback()
    {
    $mno = $this->input->get('rech_no');
    $mopc = $this->input->get('opr_code');
    $tid = $this->input->get('success_id');
    $stat = $this->input->get('status');
    $id = $this->input->get('client_key');
    $ip = $this->input->ip_address();
    
    $ss = strtoupper($stat);
    
   if($ss == 'SUCCESS' || $ss=='Success'){
       $status =  'SUCCESS';
            }
    else if($ss== 'FAILURE' || $ss== 'Failure'){
    $status =  'FAILED';
    }
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $checkid = $r[0]->recid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($query->num_rows() == 0)
    {
        $datass = array(
               'return_data' => $this->current_full_url(),
                   'recid' => $id,
                       'rd_status' => $ss,
                       'rec_txn_id'    =>  $id,
                       //'succ_opt_id' => $tid,
    'number' => $r[0]->mobileno,
    'amount' => $r[0]->amount,
    'user_name' => $wx[0]->cus_name,
    'user_id' => $r[0]->apiclid,
    'media' => $r[0]->recmedium,
    'api_sourse' => $ww[0]->apisourcecode
                    );
                   $this->db_model->insert('return_data',$datass);
    }
    if($id == $checkid)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    if($r[0]->status == 'PENDING' || $status == 'FAILED')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $status,
    'optional2'  => $atabal,
    'statusdesc' => $tid
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $r[0]->status != 'FAILED')
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $status,
    'optional2'  => $atabal,
    'statusdesc' => $tid
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    // $hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
    print_r($hello);
    exit();
    }
    else
    {
    $recdata = array(
    'status' => $status,
    'responsecode' => $status,
    'optional2'  => $atabal,
    'statusdesc' => $tid
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
       }else{
    
    }
    }
    }
    }
}

public function handaback()
{
   $mno = $this->input->get('mobile');
   $mopc = $this->input->get('operator');
   $mmnt = $this->input->get('amount');
$ss = $this->input->get('status');
$id = $this->input->get('agentid');
$tid = $this->input->get('opid');
$ip = $this->input->ip_address();

$status =  'SUCCESS';
        if(strtoupper($ss) == 'SUCCESS'){ $status =  'SUCCESS'; }
else if(strtoupper($ss)== 'FAILED'){ $status =  'FAILED'; }
$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
$medium = $r[0]->recmedium;
$uid = $r[0]->uid;
$checkid = $r[0]->recid;
$promo='';
$billing ='';
$process='';
$customer_id = $r[0]->apiclid;
$ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
$wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
$this->db->where('recid',$id);
$query = $this->db->get('return_data');
$cus_type = $wx[0]->cus_type;
$cus_city = $wx[0]->cus_city;
if($query->num_rows() == 0)
{
    $datass = array(
           'return_data' => $this->current_full_url(),
               'recid' => $id,
                   'rd_status' => $ss,
                   'rec_txn_id'    =>  $id,
                   'succ_opt_id' => $tid,
'number' => $r[0]->mobileno,
'amount' => $r[0]->amount,
'user_name' => $wx[0]->cus_name,
'user_id' => $r[0]->apiclid,
'media' => $r[0]->recmedium,
'api_sourse' => $ww[0]->apisourcecode
                );
               $this->db_model->insert('return_data',$datass);
}
if($id == $checkid)
{
$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
if($r[0]->status == 'PENDING' || $status == 'FAILED')
{
$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
if($r[0]->status != 'FAILED')
{
$cus_id = $r[0]->apiclid;
$dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
$com = $dis_cha['com'];
if($status == 'SUCCESS')
{
$recdata = array(
'status' => $status,
'responsecode' => $status,
'optional2'  => $atabal,
'statusdesc' => $tid
);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
$row14 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '2')
{
if ($row14)
{
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
$recdata1 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
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
$this->db_model->insert('exr_trnx', $recdata1);
                    }
}
$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
$recdata = $this->db_model->get_rec($cus_id);
$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
}
else if($status == 'FAILED' || $r[0]->status != 'FAILED')
{
if($r[0]->status == 'SUCCESS')
{
$recdata = array(
'status' => $status,
'responsecode' => $status,
'optional2'  => $atabal,
'statusdesc' => $tid
);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
$st = $this->db_model->get_trnx($cus_id);
$recdata16 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $st[0]->txn_clbal,
'txn_crdt' => $r[0]->amount,
'txn_dbdt' => '0',
'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
'txn_type' => 'recharge failed',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip
);
$this->db_model->insert('exr_trnx', $recdata16);
$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
if($surchk)
{
$row14 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '1')
{
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
$recdata17 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row14[0]->txn_clbal,
'txn_crdt' => $com,
'txn_dbdt' => '0',
'txn_clbal' => $row14[0]->txn_clbal + $com,
'txn_type' => 'surcharge refund',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Surcharge refund'
);
$this->db_model->insert('exr_trnx', $recdata17);
}
}
$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
if($dischk)
{
$row15 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '2')
{
if ($row15)
{
$recdata18 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row15[0]->txn_clbal,
'txn_crdt' => '0',
'txn_dbdt' => $com,
'txn_clbal' => $row15[0]->txn_clbal - $com,
'txn_type' => 'discount back',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Retailer Commission Back'
);
$this->db_model->insert('exr_trnx', $recdata18);
                    }
}
}
$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
$recdata = $this->db_model->get_rec($cus_id);
$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
// $hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
print_r($hello);
exit();
}
else
{
$recdata = array(
'status' => $status,
'responsecode' => $status,
'optional2'  => $atabal,
'statusdesc' => $tid
);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
$st = $this->db_model->get_trnx($cus_id);
$recdata16 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $st[0]->txn_clbal,
'txn_crdt' => $r[0]->amount,
'txn_dbdt' => '0',
'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
'txn_type' => 'recharge failed',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip
);
$this->db_model->insert('exr_trnx', $recdata16);
$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
if($surchk)
{
$row14 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '1') {
$recdata17 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row14[0]->txn_clbal,
'txn_crdt' => $com,
'txn_dbdt' => '0',
'txn_clbal' => $row14[0]->txn_clbal + $com,
'txn_type' => 'surcharge refund',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Surcharge refund'
);
$this->db_model->insert('exr_trnx', $recdata17);
}
}
$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
if($dischk)
{
$row15 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '2')
{
if ($row15)
{
$recdata18 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row15[0]->txn_clbal,
'txn_crdt' => '0',
'txn_dbdt' => $com,
'txn_clbal' => $row15[0]->txn_clbal - $com,
'txn_type' => 'discount back',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Retailer Commission Back'
);
$this->db_model->insert('exr_trnx', $recdata18);
                    }
}
}
}
}
}
else
{
$result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
if($result < 2){
   }else{

}
}
}
}
}

public function skgrouptechback()
    {
        
    $id = $this->input->get('rchid');
    $ss = $this->input->get('Status');
    $tid = $this->input->get('operatorid');
    $lapu_bal = $this->input->get('remainbal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    $ip = $this->input->ip_address();
    
    if($ss == 'Success' || $ss=='SUCCESS'){
       $status =  'SUCCESS';
    }
    else 
    $status =  'FAILED';
    
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1')
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
    $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}

public function ambikaback()
{
$mno = $this->input->get('MOBILE');
$mopc = $this->input->get('MSG');
$mmnt = $this->input->get('AMOUNT');
$ss = $this->input->get('STATUS');
$tid = $this->input->get('TRANID');
$id = $this->input->get('AGENTID');
$ip = $this->input->ip_address();

$status =  'SUCCESS';
if(strtoupper($ss) == 'SUCCESS'){ $status =  'SUCCESS'; }
else if(strtoupper($ss)== 'FAILED'){ $status =  'FAILED'; }
$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
$medium = $r[0]->recmedium;
$uid = $r[0]->uid;
$checkid = $r[0]->recid;
$promo='';
$billing ='';
$process='';
$customer_id = $r[0]->apiclid;
$ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
$wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
$this->db->where('recid',$id);
$query = $this->db->get('return_data');
$cus_type = $wx[0]->cus_type;
$cus_city = $wx[0]->cus_city;
if($query->num_rows() == 0)
{
    $datass = array(
           'return_data' => $this->current_full_url(),
               'recid' => $id,
                   'rd_status' => $ss,
                   'rec_txn_id'    =>  $id,
                   'succ_opt_id' => $tid,
'number' => $r[0]->mobileno,
'amount' => $r[0]->amount,
'user_name' => $wx[0]->cus_name,
'user_id' => $r[0]->apiclid,
'media' => $r[0]->recmedium,
'api_sourse' => $ww[0]->apisourcecode
                );
               $this->db_model->insert('return_data',$datass);
}
if($id == $checkid)
{
$r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
if($r[0]->status == 'PENDING' || $status == 'FAILED')
{
$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid));
if($r[0]->status != 'FAILED')
{
$cus_id = $r[0]->apiclid;
$dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
$com = $dis_cha['com'];
if($status == 'SUCCESS')
{
$recdata = array(
'status' => $status,
'responsecode' => $status,
'optional2'  => $atabal,
'statusdesc' => $tid
);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
$row14 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '2')
{
if ($row14)
{
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
$recdata1 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
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
$this->db_model->insert('exr_trnx', $recdata1);
                    }
}
$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
$recdata = $this->db_model->get_rec($cus_id);
$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
}
else if($status == 'FAILED' || $r[0]->status != 'FAILED')
{
if($r[0]->status == 'SUCCESS')
{
$recdata = array(
'status' => $status,
'responsecode' => $status,
'optional2'  => $atabal,
'statusdesc' => $tid
);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
$st = $this->db_model->get_trnx($cus_id);
$recdata16 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $st[0]->txn_clbal,
'txn_crdt' => $r[0]->amount,
'txn_dbdt' => '0',
'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
'txn_type' => 'recharge failed',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip
);
$this->db_model->insert('exr_trnx', $recdata16);
$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
if($surchk)
{
$row14 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '1')
{
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
$recdata17 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row14[0]->txn_clbal,
'txn_crdt' => $com,
'txn_dbdt' => '0',
'txn_clbal' => $row14[0]->txn_clbal + $com,
'txn_type' => 'surcharge refund',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Surcharge refund'
);
$this->db_model->insert('exr_trnx', $recdata17);
}
}
$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
if($dischk)
{
$row15 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '2')
{
if ($row15)
{
$recdata18 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row15[0]->txn_clbal,
'txn_crdt' => '0',
'txn_dbdt' => $com,
'txn_clbal' => $row15[0]->txn_clbal - $com,
'txn_type' => 'discount back',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Retailer Commission Back'
);
$this->db_model->insert('exr_trnx', $recdata18);
                    }
}
}
$apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
$recdata = $this->db_model->get_rec($cus_id);
$pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
// $hello = $this->rec_model->recharge_ot_again(array('cus_id'=>$customer_id,'amount'=>$mmnt,'number'=>$mno, 'billing' => $billing, 'process' => $process,'operator'=>$mopc,'mode'=>$medium,'city'=>$cus_city,'type'=>$cus_type,'promo'=>$promo));
print_r($hello);
exit();
}
else
{
$recdata = array(
'status' => $status,
'responsecode' => $status,
'optional2'  => $atabal,
'statusdesc' => $tid
);
$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
$st = $this->db_model->get_trnx($cus_id);
$recdata16 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $st[0]->txn_clbal,
'txn_crdt' => $r[0]->amount,
'txn_dbdt' => '0',
'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
'txn_type' => 'recharge failed',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip
);
$this->db_model->insert('exr_trnx', $recdata16);
$surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
if($surchk)
{
$row14 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '1') {
$recdata17 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row14[0]->txn_clbal,
'txn_crdt' => $com,
'txn_dbdt' => '0',
'txn_clbal' => $row14[0]->txn_clbal + $com,
'txn_type' => 'surcharge refund',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Surcharge refund'
);
$this->db_model->insert('exr_trnx', $recdata17);
}
}
$dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
if($dischk)
{
$row15 = $this->db_model->get_trnx($cus_id);
if ($dis_cha['comtype'] == '2')
{
if ($row15)
{
$recdata18 = array(
'txn_agentid' => $cus_id,
'txn_recrefid' => $id,
'txn_opbal' => $row15[0]->txn_clbal,
'txn_crdt' => '0',
'txn_dbdt' => $com,
'txn_clbal' => $row15[0]->txn_clbal - $com,
'txn_type' => 'discount back',
'txn_time' => time(),
'txn_date' => date('Y-m-d h:i:s'),
'txn_checktime' => $cus_id . time(),
'txn_ip' => $ip,
'txn_comment' => 'Retailer Commission Back'
);
$this->db_model->insert('exr_trnx', $recdata18);
                    }
}
}
}
}
}
else
{
$result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
if($result < 2){
   }else{

}
}
}
}
}

	
public function current_full_url()
	{
	    $CI =& get_instance();
	    $url = $CI->config->site_url($CI->uri->uri_string());
	    return $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;
	}
	
	public function upigateway_callback()
	{
	    $param['key'] = '92135a11-8fc6-44c7-a17e-d4984d15b610';
	    $param['txn_date'] = date("d-m-Y");
	    $param['client_txn_id'] =  $_GET['trx_id'];
	    
	    $cus = $_GET['cus_id'];
        $cus_id = explode("?","$cus");
        $cus_id = $cus_id[0];
		$req = json_encode($param);

        $url = "https://merchant.upigateway.com/api/check_order_status";                           
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        $data2=json_decode($resultStr, true);
        
        $data = file_get_contents('php://input');
	    $logfile = 'gateway_responselog.txt';
	    $log = "\n\n".'GUID - '.date('Y-m-d H:i:s')."================================================================ \n";
        $log .= 'Callback Get Param -  TRANS ID'.$_GET['trx_id']."|| CUS ID ".$_GET['cus_id']."\n\n";
        $log .= 'STATUS RESPONSE - '.json_encode($data2)."\n\n";
        file_put_contents($logfile, $log, FILE_APPEND | LOCK_EX);
        
        if($data2['data']['status'] == 'success'){
            $sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
            $r = $this->db_model->getAlldata($sql);

            if($r){
                $crdtbal = $data2['data']['amount'];
                $clbal = $r['0']->txn_clbal;
    			$total = $r['0']->txn_clbal + $crdtbal;
    			$txn_dbdt = 0;
    
    			$data12 = array(
    				'txn_agentid'=>$cus_id,
    				'transaction_id' => $_GET['trx_id'],
    				'txn_opbal'=>$clbal,
    				'txn_crdt'=> $crdtbal,
    				'txn_dbdt'=> $txn_dbdt,
    				'txn_clbal'=>$total,
    				'txn_type'=>'UPI gateway',
    				'txn_time'=>time(),
    				'txn_checktime'=>$id.time(),
    				'txn_date'=>DATE('Y-m-d H:i:s'),
    				'txn_ip'=>'',
    				'gateway_response' => json_encode($data2)
    			);
    			$lasttxnrec = $this->db_model->insert_update('exr_trnx',$data12);
    			redirect('https://easypayall.in/upigatewaydone');
            }else{ 
                $crdtbal = $data2['data']['amount'];
                $clbal = '0';
    			$total = '0' + $crdtbal;
    			$txn_dbdt = 0;
    
    			$data12 = array(
    				'txn_agentid'=>$cus_id,
    				'transaction_id' => $_GET['trx_id'],
    				'txn_opbal'=>$clbal,
    				'txn_crdt'=> $crdtbal,
    				'txn_dbdt'=> $txn_dbdt,
    				'txn_clbal'=>$total,
    				'txn_type'=>'UPI gateway',
    				'txn_time'=>time(),
    				'txn_checktime'=>$id.time(),
    				'txn_date'=>DATE('Y-m-d H:i:s'),
    				'txn_ip'=>'',
    				'gateway_response' => json_encode($data2)
    			);
    			$lasttxnrec = $this->db_model->insert_update('exr_trnx',$data12);
    			redirect('https://easypayall.in/upigatewaydone');
            }
        }else {
            redirect('https://easypayall.in/upigatewaydone');
        }
	}
	
	public function jetwings_callback()
    {
       
    $id = $this->input->get('AGENTID');
    $ss = $this->input->get('STATUS');
    $tid = $this->input->get('LIVEID');
    
    $lapu_bal = $this->input->get('lapubal');
    $rechargeno = $this->input->get('rechargeno');
    $recamount = $this->input->get('amount');
    $txn_now = $this->input->get('lapuno');
    
    // $id = '1271';
    // $ss = '1';
    // $tid = 'xyz123';
    
    $ip = $this->input->ip_address();
    
    if($ss == '2' || $ss=='2'){
       $status =  'SUCCESS';
            }
    else 
    $status =  'FAILED';
    $api_msg=$ss;
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $this->db->where('recid',$id);
    $query = $this->db->get('return_data');
    if($query->num_rows() == 0)
    {
    $datass = array(
    'return_data' => $this->current_full_url(),
    'recid' =>  $id,
    'rd_status' => $ss,
   // 'rec_txn_id'    =>  $txn_now,
    'succ_opt_id' => $tid,
           'number' => $r[0]->mobileno,
           'amount' => $r[0]->amount,
           'user_name' => $wx[0]->cus_name,
           'user_id' => $r[0]->apiclid,
           'media' => $r[0]->recmedium,
           'api_sourse' => $ww[0]->apisourcecode
    );
    $this->db_model->insert('return_data',$datass);
    }
    if($id != NULL)
    {
    
    $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
    
    $medium = $r[0]->recmedium;
    $uid = $r[0]->uid;
    $promo='';
    $billing ='';
    $process='';
    $customer_id = $r[0]->apiclid;
    $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
    $cus_type = $wx[0]->cus_type;
    $cus_city = $wx[0]->cus_city;
    if($r[0]->status != 'NULL')
    {
    $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
    if($r[0]->status != 'FAILED')
    {
        // print_r("Hello"); exit;
    $cus_id = $r[0]->apiclid;
    $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
    $com = $dis_cha['com'];
    if($r[0]->status != $status)
    {
    if($status == 'SUCCESS')
    {
    $recdata = array(
    'status' => $status,
    //'responsecode' => $txn_now,
    'statusdesc' => $tid,
                                    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    
    // $this->Commission_model->creditRechargeCommission($cus_id,$amount5,$rectxnid,$qr_opcode);
    $this->Commission_model->creditRechargeCommission($cus_id,$r[0]->amount,$r[0]->recid,$r[0]->operator);
    
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row14)
    {
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
    $recdata1 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
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
    $this->db_model->insert('exr_trnx', $recdata1);
                        }
    }
    $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
    $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
    $recdata = $this->db_model->get_rec($cus_id);
    $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
    }
    else if($status == 'FAILED' || $status == 'Failed' )
    {
    if($r[0]->status == 'SUCCESS')
    {
        $recdata = array(
        'status' => $status,
        //'responsecode' => $txn_now,
        'statusdesc' => $tid,
        //'optional2'=>$lapu_bal
        );
        
        $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
        $st = $this->db_model->get_trnx($cus_id);
        $recdata16 = array(
        'txn_agentid' => $cus_id,
        'txn_recrefid' => $id,
        'txn_opbal' => $st[0]->txn_clbal,
        'txn_crdt' => $r[0]->amount,
        'txn_dbdt' => '0',
        'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
        'txn_type' => 'recharge failed',
        'txn_time' => time(),
        'txn_date' => date('Y-m-d h:i:s'),
        'txn_checktime' => $cus_id . time(),
        'txn_ip' => $ip
        );
        
        $this->db_model->insert('exr_trnx', $recdata16);
        
        $rcharge = $this->db_model->getAlldata("select txn_crdt from exr_trnx where txn_recrefid='".$id."' and txn_type='Retailer Commission'");
        if($rcharge){
            $rowr = $this->db_model->get_trnx($cus_id);
            $dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>'0'),array('recid'=>$id));
            $recdata16r = array(
                'txn_agentid' => $cus_id,
                'txn_recrefid' => $id,
                'txn_opbal' => $rowr[0]->txn_clbal,
                'txn_crdt' => '0',
                'txn_dbdt' => $rcharge[0]->txn_crdt,
                'txn_clbal' => $rowr[0]->txn_clbal - $rcharge[0]->txn_crdt,
                'txn_type' => 'Retailer Commission Back',
                'txn_time' => time(),
                'txn_date' => date('Y-m-d h:i:s'),
                'txn_ip' => $ip
                );
            $this->db_model->insert('exr_trnx', $recdata16r); 
        }
        $cb = $this->db_model->getwhere('customers', array('cus_id'=>$cus_id));
        $cus_d = $cb[0]->cus_reffer;
        $row3 = $this->db_model->get_trnx($cus_d);
        $dcharge = $this->db_model->getAlldata("select txn_crdt from exr_trnx where txn_recrefid='".$id."' and txn_type='Distributor Commission'");
        if($dcharge){
            $dts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>'0'),array('recid'=>$id));
            $recdata16d = array(
                'txn_agentid' => $cus_d,
                'txn_recrefid' => $id,
                'txn_opbal' => $row3[0]->txn_clbal,
                'txn_crdt' => '0',
                'txn_dbdt' => $dcharge[0]->txn_crdt,
                'txn_clbal' => $row3[0]->txn_clbal - $dcharge[0]->txn_crdt,
                'txn_type' => 'Distributer Commission Back',
                'txn_time' => time(),
                'txn_date' => date('Y-m-d h:i:s'),
                'txn_ip' => $ip
                );
            $this->db_model->insert('exr_trnx', $recdata16d); 
        }
        $d = $this->db_model->getwhere('customers', array('cus_id'=>$cus_d));
    	$cus_m = $d[0]->cus_reffer;
    	$row4 = $this->db_model->get_trnx($cus_m);
    	$mascomchk = $this->db_model->getAlldata("select txn_crdt from exr_trnx where txn_recrefid='".$id."' and txn_type='Master Commission'");
    	if($mascomchk){
            $mts = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>'0'),array('recid'=>$id));
            $recdata16m = array(
                'txn_agentid' => $cus_m,
                'txn_recrefid' => $id,
                'txn_opbal' => $row4[0]->txn_clbal,
                'txn_crdt' => '0',
                'txn_dbdt' => $mascomchk[0]->txn_crdt,
                'txn_clbal' => $row4[0]->txn_clbal - $mascomchk[0]->txn_crdt,
                'txn_type' => 'Master Commission Back',
                'txn_time' => time(),
                'txn_date' => date('Y-m-d h:i:s'),
                'txn_ip' => $ip
                );
            $this->db_model->insert('exr_trnx', $recdata16m); 
        }
        
        $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
        if($surchk)
        {
            $row14 = $this->db_model->get_trnx($cus_id);
            if ($dis_cha['comtype'] == '1')
            {
                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
                $recdata17 = array(
                'txn_agentid' => $cus_id,
                'txn_recrefid' => $id,
                'txn_opbal' => $row14[0]->txn_clbal,
                'txn_crdt' => $com,
                'txn_dbdt' => '0',
                'txn_clbal' => $row14[0]->txn_clbal + $com,
                'txn_type' => 'surcharge refund',
                'txn_time' => time(),
                'txn_date' => date('Y-m-d h:i:s'),
                'txn_checktime' => $cus_id . time(),
                'txn_ip' => $ip,
                'txn_comment' => 'Surcharge refund'
                );
                $this->db_model->insert('exr_trnx', $recdata17);
            }
        }
        $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
        if($dischk)
        {
            $row15 = $this->db_model->get_trnx($cus_id);
            if ($dis_cha['comtype'] == '2')
            {
                if ($row15)
                {
                    $recdata18 = array(
                    'txn_agentid' => $cus_id,
                    'txn_recrefid' => $id,
                        'txn_opbal' => $row15[0]->txn_clbal,
                    'txn_crdt' => '0',
                    'txn_dbdt' => $com,
                    'txn_clbal' => $row15[0]->txn_clbal - $com,
                    'txn_type' => 'discount back',
                    'txn_time' => time(),
                    'txn_date' => date('Y-m-d h:i:s'),
                    'txn_checktime' => $cus_id . time(),
                    'txn_ip' => $ip,
                    'txn_comment' => 'Retailer Commission Back'
                    );
                    $this->db_model->insert('exr_trnx', $recdata18);
                }
            }
        }
        
        
        $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
    							
    							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
        $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
        $recdata = $this->db_model->get_rec($cus_id);
        $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
        $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
        $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
   // 'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    $st = $this->db_model->get_trnx($cus_id);
    $recdata16 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $st[0]->txn_clbal,
    'txn_crdt' => $r[0]->amount,
    'txn_dbdt' => '0',
    'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
    'txn_type' => 'recharge failed',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip
    );
    $this->db_model->insert('exr_trnx', $recdata16);
    $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
    if($surchk)
    {
    $row14 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '1') {
    $recdata17 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
    'txn_opbal' => $row14[0]->txn_clbal,
    'txn_crdt' => $com,
    'txn_dbdt' => '0',
    'txn_clbal' => $row14[0]->txn_clbal + $com,
    'txn_type' => 'surcharge refund',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Surcharge refund'
    );
    $this->db_model->insert('exr_trnx', $recdata17);
    }
    }
    $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
    if($dischk)
    {
    $row15 = $this->db_model->get_trnx($cus_id);
    if ($dis_cha['comtype'] == '2')
    {
    if ($row15)
    {
    $recdata18 = array(
    'txn_agentid' => $cus_id,
    'txn_recrefid' => $id,
        'txn_opbal' => $row15[0]->txn_clbal,
    'txn_crdt' => '0',
    'txn_dbdt' => $com,
    'txn_clbal' => $row15[0]->txn_clbal - $com,
    'txn_type' => 'discount back',
    'txn_time' => time(),
    'txn_date' => date('Y-m-d h:i:s'),
    'txn_checktime' => $cus_id . time(),
    'txn_ip' => $ip,
    'txn_comment' => 'Retailer Commission Back'
    );
    $this->db_model->insert('exr_trnx', $recdata18);
                        }
    }
    }
    }
    }
    }
    else
    {
    $recdata = array(
    'status' => $status,
   // 'responsecode' => $txn_now,
    'statusdesc' => $tid,
    //'optional2'=>$lapu_bal
    );
    $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
    }
    }
    else
    {
        
    $result = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
    if($result < 2){
                        }else{
    
    }
    }
    }
    }
}
// 	 public function jetwings_callback()
//     {
        
//     $id = $this->input->get('AGENTID');
//     $ss = $this->input->get('STATUS');
//     $tid = $this->input->get('LIVEID');
    
//     $lapu_bal = $this->input->get('lapubal');
//     $rechargeno = $this->input->get('rechargeno');
//     $recamount = $this->input->get('amount');
//     $txn_now = $this->input->get('lapuno');
//     $ip = $this->input->ip_address();
    
//     if($ss == '2' || $ss=='2'){
//       $status =  'SUCCESS';
//             }
//     else 
//     $status =  'FAILED';
//     $api_msg=$ss;
//     $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
//     $ww = $this->db_model->get_newdata('apisource',array('apisourceid'=>$r[0]->apisourceid));
//     $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
//     $this->db->where('recid',$id);
//     $query = $this->db->get('return_data');
//     if($query->num_rows() == 0)
//     {
//     $datass = array(
//     'return_data' => $this->current_full_url(),
//     'recid' =>  $id,
//     'rd_status' => $ss,
//   // 'rec_txn_id'    =>  $txn_now,
//     'succ_opt_id' => $tid,
//           'number' => $r[0]->mobileno,
//           'amount' => $r[0]->amount,
//           'user_name' => $wx[0]->cus_name,
//           'user_id' => $r[0]->apiclid,
//           'media' => $r[0]->recmedium,
//           'api_sourse' => $ww[0]->apisourcecode
//     );
//     $this->db_model->insert('return_data',$datass);
//     }
//     if($id != NULL)
//     {
//     $r = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
//     $medium = $r[0]->recmedium;
//     $uid = $r[0]->uid;
//     $promo='';
//     $billing ='';
//     $process='';
//     $customer_id = $r[0]->apiclid;
//     $wx = $this->db_model->get_newdata('customers',array('cus_id'=>$r[0]->apiclid));
//     $cus_type = $wx[0]->cus_type;
//     $cus_city = $wx[0]->cus_city;
//     if($r[0]->status != 'NULL')
//     {
//     $this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>$status,'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$tid,'api_msg'=>$api_msg));
//     if($r[0]->status != 'FAILED')
//     {
//     $cus_id = $r[0]->apiclid;
//     $dis_cha = $this->rec_model->commi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
//     $apidis_cha = $this->rec_model->apicommi(array('cus_id' => $cus_id, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
//     $com = $dis_cha['com'];
//     if($r[0]->status != $status)
//     {
//     if($status == 'SUCCESS')
//     {
//     $recdata = array(
//     'status' => $status,
//     //'responsecode' => $txn_now,
//     'statusdesc' => $tid,
//                                     //'optional2'=>$lapu_bal
//     );
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
//     $row14 = $this->db_model->get_trnx($cus_id);
//     if ($dis_cha['comtype'] == '2')
//     {
//     if ($row14)
//     {
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $id));
//     $recdata1 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//     'txn_opbal' => $row14[0]->txn_clbal,
//     'txn_crdt' => $com,
//     'txn_dbdt' => '0',
//     'txn_clbal' => $row14[0]->txn_clbal + $com,
//     'txn_type' => 'discount',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip,
//     'txn_comment' => 'Retailer Commission'
//     );
//     $this->db_model->insert('exr_trnx', $recdata1);
//                         }
//     }
//     $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
// 							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
//     $this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$cus_id,$id);
//     $recdata = $this->db_model->get_rec($cus_id);
//     $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $id));
//     }
//     else if($status == 'FAILED' || $status == 'Failed' )
//     {
//     if($r[0]->status == 'SUCCESS')
//     {
//     $recdata = array(
//     'status' => $status,
//     //'responsecode' => $txn_now,
//     'statusdesc' => $tid,
//     //'optional2'=>$lapu_bal
//     );
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
//     $st = $this->db_model->get_trnx($cus_id);
//     $recdata16 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//     'txn_opbal' => $st[0]->txn_clbal,
//     'txn_crdt' => $r[0]->amount,
//     'txn_dbdt' => '0',
//     'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
//     'txn_type' => 'recharge failed',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip
//     );
//     $this->db_model->insert('exr_trnx', $recdata16);
//     $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
//     if($surchk)
//     {
//     $row14 = $this->db_model->get_trnx($cus_id);
//     if ($dis_cha['comtype'] == '1')
//     {
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recido' => $id));
//     $recdata17 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//     'txn_opbal' => $row14[0]->txn_clbal,
//     'txn_crdt' => $com,
//     'txn_dbdt' => '0',
//     'txn_clbal' => $row14[0]->txn_clbal + $com,
//     'txn_type' => 'surcharge refund',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip,
//     'txn_comment' => 'Surcharge refund'
//     );
//     $this->db_model->insert('exr_trnx', $recdata17);
//     }
//     }
//     $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
//     if($dischk)
//     {
//     $row15 = $this->db_model->get_trnx($cus_id);
//     if ($dis_cha['comtype'] == '2')
//     {
//     if ($row15)
//     {
//     $recdata18 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//         'txn_opbal' => $row15[0]->txn_clbal,
//     'txn_crdt' => '0',
//     'txn_dbdt' => $com,
//     'txn_clbal' => $row15[0]->txn_clbal - $com,
//     'txn_type' => 'discount back',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip,
//     'txn_comment' => 'Retailer Commission Back'
//     );
//     $this->db_model->insert('exr_trnx', $recdata18);
//                         }
//     }
//     }
//     $apicoms = $this->getadminapicommission($apisource,$operator,$amount5);
							
// 							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rectxnid));
//     $this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$cus_id,$id);
//     $recdata = $this->db_model->get_rec($cus_id);
//     $pl = $recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master);
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $id));
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $id));
//     }
//     else
//     {
//     $recdata = array(
//     'status' => $status,
//   // 'responsecode' => $txn_now,
//     'statusdesc' => $tid,
//   // 'optional2'=>$lapu_bal
//     );
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
//     $st = $this->db_model->get_trnx($cus_id);
//     $recdata16 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//     'txn_opbal' => $st[0]->txn_clbal,
//     'txn_crdt' => $r[0]->amount,
//     'txn_dbdt' => '0',
//     'txn_clbal' => $st[0]->txn_clbal + $r[0]->amount,
//     'txn_type' => 'recharge failed',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip
//     );
//     $this->db_model->insert('exr_trnx', $recdata16);
//     $surchk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='surcharge'")->result();
//     if($surchk)
//     {
//     $row14 = $this->db_model->get_trnx($cus_id);
//     if ($dis_cha['comtype'] == '1') {
//     $recdata17 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//     'txn_opbal' => $row14[0]->txn_clbal,
//     'txn_crdt' => $com,
//     'txn_dbdt' => '0',
//     'txn_clbal' => $row14[0]->txn_clbal + $com,
//     'txn_type' => 'surcharge refund',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip,
//     'txn_comment' => 'Surcharge refund'
//     );
//     $this->db_model->insert('exr_trnx', $recdata17);
//     }
//     }
//     $dischk = $this->db->query("select * from exr_trnx where txn_recrefid='".$id."' and txn_type='discount'")->result();
//     if($dischk)
//     {
//     $row15 = $this->db_model->get_trnx($cus_id);
//     if ($dis_cha['comtype'] == '2')
//     {
//     if ($row15)
//     {
//     $recdata18 = array(
//     'txn_agentid' => $cus_id,
//     'txn_recrefid' => $id,
//     'txn_opbal' => $row15[0]->txn_clbal,
//     'txn_crdt' => '0',
//     'txn_dbdt' => $com,
//     'txn_clbal' => $row15[0]->txn_clbal - $com,
//     'txn_type' => 'discount back',
//     'txn_time' => time(),
//     'txn_date' => date('Y-m-d h:i:s'),
//     'txn_checktime' => $cus_id . time(),
//     'txn_ip' => $ip,
//     'txn_comment' => 'Retailer Commission Back'
//     );
//     $this->db_model->insert('exr_trnx', $recdata18);
//                         }
//     }
//     }
//     }
//     }
//     }
//     else
//     {
//     $recdata = array(
//     'status' => $status,
//   // 'responsecode' => $txn_now,
//     'statusdesc' => $tid,
//     //'optional2'=>$lapu_bal
//     );
//     $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', $recdata,array('recid'=>$id));
//     }
//     }
//     else
//     {
//     $result = $this->db_model->get_newdata1('exr_rechrgreqexr_rechrgreq_fch',array('uid'=>$uid));
//     if($result < 2){
//                         }else{
    
//     }
//     }
//     }
//     }
// }
}