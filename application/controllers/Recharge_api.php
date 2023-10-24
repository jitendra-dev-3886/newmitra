<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Recharge_api extends CI_Controller 
{
	private $cus_info,$data,$reptype=1,$ip;
    public function __construct() 
	{
        parent::__construct();
        $this->load->helper(array('url','date','file','form','captcha'));
	    $this->load->library(array('session','form_validation','pagination','upload','email'));
	    $this->load->model(array('db_model','rec_model'));

	    $this->data =  (object)$this->input->get();
	    $this->ip = $this->input->ip_address();

		if(isset($this->data->member_id) && $this->data->member_id!='' && isset($this->data->api_password) && $this->data->api_password!='' && isset($this->data->api_pin) && $this->data->api_pin!='' )
		{
			$dt = $this->db_model->checkapiuser(array('id'=>$this->data->member_id,'pass'=>$this->data->api_password,'pin'=>$this->data->api_pin,'ip'=>$this->ip));
		    if($dt->ERROR==0)
		    {
			   $this->cus_info = $dt->info;
		    } 
			else
			{
				echo json_encode(array('ERROR'=>2,'MESSAGE'=>$dt->MESSAGE,'ip'=>$this->ip ,'request'=>$this->data));
				die();
			}
		}
		else
		{
			echo json_encode(array('ERROR'=>2,'MESSAGE'=>'Parameter Error','ip'=>$this->ip,'request'=>$this->data));
			die();
		}
    }

	public function recharge()
	{
		$cus_mob = $this->data->member_id;
		$pass = $this->data->api_password;
		$pin = $this->data->api_pin;
		$number = $this->data->number;
		$qr_opcode = $this->data->opcode;
		$amount = $this->data->amount;
		$req = $this->data->request_id;
		$field1 = $this->data->field1;
		$field2 = $this->data->field2;
		$ip = $this->input->ip_address();

		$cus_id = $this->cus_info->cus_id;
		$rt = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('requestertxnid'=>$req,'apiclid'=>$cus_id));
		if(!$rt)
		{
			$trnx = $this->db_model->get_trnx($cus_id);
			if($trnx)
			{
				if(($trnx[0]->txn_clbal + $this->cus_info->cus_cutofamt) >= $amount)
				{
					$rec = $this->db_model->get_rec($cus_id);
					if($rec)
					{
						$chktime = $this->checkrectime(array('mobile'=>$number,'amount'=>$amount,'ip'=>$ip,'cus_id'=>$cus_id));
						if($chktime)
						{
							$complete = $this->rec_model->recharge_ot(array('cus_id'=>$cus_id,'amount'=>$amount,'number'=>$number,'operator'=>$qr_opcode,'mode'=>'api','type'=>$this->cus_info->cus_type,'reqtxnid'=>$req));
							$rts = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('requestertxnid'=>$req, 'apiclid'=>$cus_id));
								if($rts[0]->status == 'SUCCESS')
								{
									$fullst = array('ERROR'=>'0','STATUS'=>'SUCCESS','OPTXNID'=>$rts[0]->statusdesc,'MEMBERREQID'=>$req,'AMOUNT'=>$amount,'NUMBER'=>$number,'MESSAGE'=>'Successfully Recharged','FIELD1'=>'','FIELD2'=>'');
								}
								elseif($rts[0]->status == 'FAILED')
								{
									$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Recharge Failed');
								}
								elseif($rts[0]->status == 'PENDING')
								{
									$fullst = array('ERROR'=>'1','STATUS'=>'PENDING','MESSAGE'=>'Recharge Pending');
								}
								elseif($rts[0]->status == 'SUBMIT_SUCCESS')
								{
									$fullst = array('ERROR'=>'3','STATUS'=>'SUBMIT_SUCCESS','MESSAGE'=>'Submit Success');
								}
								else
								{
									$fullst = array('ERROR'=>'2','STATUS'=>'SUCCESS','MESSAGE'=>'Invalid Requested id');
								}
							
						}
						else
						{
							$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Do next recharge after 5 minutes on same number.');
						}
					}
					else
					{
						$complete = $this->rec_model->recharge_ot(array('cus_id'=>$cus_id,'amount'=>$amount,'number'=>$number,'operator'=>$qr_opcode,'mode'=>'api','type'=>$this->cus_info->cus_type,'reqtxnid'=>$req,'FIELD1'=>'','FIELD2'=>''));
						$rts = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('requestertxnid'=>$req,'apiclid'=>$cus_id));
						if($rts[0]->status == 'SUCCESS')
						{
							$fullst = array('ERROR'=>'0','STATUS'=>'SUCCESS','OPTXNID'=>$rts[0]->statusdesc,'MEMBERREQID'=>$req,'AMOUNT'=>$amount,'NUMBER'=>$number,'MESSAGE'=>'Successfully recharged','FIELD1'=>'','FIELD2'=>'');
						}
						elseif($rts[0]->status == 'FAILED')
						{
							$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Recharge Failed');
						}
						elseif($rts[0]->status == 'SUBMIT_SUCCESS')
						{
							$fullst = array('ERROR'=>'3','STATUS'=>'SUBMIT_SUCCESS','MESSAGE'=>'Submit Success');
						}
						else
						{
							$fullst = array('ERROR'=>'1','STATUS'=>'PENDING','MESSAGE'=>'Recharge Pending');
						}	
					}
				}
				else
				{
					$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Insufficient Balance.');
				}				
			}
			else
			{
				$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Insufficient Balance.');
			}
		}
		else
		{
			$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Duplicate request id');
		}
		$this->reply($fullst);
	}

	public function getbalance()
	{
	
		$custx = $this->db_model->get_trnx($this->cus_info->cus_id);
		$fullst = array('ERROR'=>'0','BALANCE'=>(!empty($custx) ? $custx[0]->txn_clbal : '0') ,'MESSAGE'=>'Your wallet balance is '.(!empty($custx) ? $custx[0]->txn_clbal : '0'),'FIELD1'=>'','FIELD2'=>'');
		$this->reply($fullst);
	}

	public function checkstatus()
	{
		$reqtxn = $this->data->request_id;
		$ip = $this->input->ip_address();		
		
		$rts = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('requestertxnid'=>$reqtxn,'apiclid'=>$this->cus_info->cus_id));
		if($rts[0]->status == 'SUCCESS')
		{
			$fullst = array('ERROR'=>'0','STATUS'=>'SUCCESS','OPTXNID'=>$rts[0]->statusdesc,'AMOUNT'=>$rts[0]->amount,'NUMBER'=>$rts[0]->mobileno,'MESSAGE'=>$rts[0]->api_msg,'FIELD1'=>'','FIELD2'=>'');
		}
		elseif($rts[0]->status == 'FAILED')
		{
			$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>$rts[0]->api_msg);
		}
		elseif($rts[0]->status == 'PENDING')
		{
			$fullst = array('ERROR'=>'1','STATUS'=>'PENDING','MESSAGE'=>'Please Wait For Response');
		}
		elseif($rts[0]->status == 'SUBMIT_SUCCESS')
		{
			$fullst = array('ERROR'=>'3','STATUS'=>'SUBMIT_SUCCESS','MESSAGE'=>'Please Wait For Final Response');
		}
		else
		{
			$fullst = array('ERROR'=>'2','STATUS'=>'FAILED','MESSAGE'=>'Invalid Requested id');
		}
		$this->reply($fullst);
	}

	public function checkrectime($data)
	{
		$recdt = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where mobileno='".$data['mobile']."' and amount='".$data['amount']."' and ip='".$data['ip']."' and apiclid='".$data['cus_id']."' order by recid desc limit 1")->result();
		if($recdt)
		{	
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $recdt[0]->reqdate);
			$date->modify('+5 minutes');
			$recdate = $date->format('Y-m-d H:i:s');
			$now = date('Y-m-d h:i:s');
			if($now > $recdate)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return 1;
		}
	}

	private function reply($value)
	{
		echo json_encode($value+array('ip'=>$this->ip,'request'=>$this->data));
		exit();
	}

	public function current_full_url()
	{
	    $CI =& get_instance();

	    $url = $CI->config->site_url($CI->uri->uri_string());
	    return $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;
	}
	

}
?>