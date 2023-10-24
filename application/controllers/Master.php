<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Master extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model(array('db_model','encryption_model','rec_model'));
		$this->load->library(array('upload'));
    }

	public function index()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		   	$data['data'] = $this->db_model->adminDashboard();
		    $this->load->view('master/dashboard',$data);
		}
	}
	
	public function dashboard()

	{
	    $data['data'] = [
	        'success' =>$this->db_model->getAlldata("SELECT sum(e.amount) as total FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where  e.apiclid IN (SELECT cus_id From customers where cus_type='distributor' and cus_reffer='".$this->session->userdata('mas_id')."') ORDER BY e.recid DESC"),
	   	    //'success' => $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' and apiclid='".$this->session->userdata('mas_id')."' and DATE(reqdate)='".date('Y-m-d')."'"),
	   	    'failed' => $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' and apiclid='".$this->session->userdata('mas_id')."' and DATE(reqdate)='".date('Y-m-d')."'"),
	   	    'todayearning' => $this->db_model->getAlldata('select sum(exr.txn_crdt) as amt from exr_trnx as exr join exr_rechrgreqexr_rechrgreq_fch as err on exr.txn_recrefid = err.recid where exr.txn_type IN ("Retailer Commission","discount") and err.status="SUCCESS" and DATE(exr.txn_date)="'.date('Y-m-d').'" and exr.txn_agentid="'.$this->session->userdata('mas_id').'" group by err.recid'),
	   	    'balance' => $this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('mas_id')."' ORDER BY txn_id desc limit 1"),
	   	    'mobile' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('mobile','postpaid') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'datacard' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('datacard') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'dth' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('dth') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'landline' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('landline') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'electricity' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('ELECTRICITY') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'insurance' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('INSURANCE') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'gas' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('GAS') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'dmt' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('dmt') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'fastag' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('fastag') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'aeps' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('aeps') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'microatm' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('microatm') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	    'adhar' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('adhar') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('mas_id')."'"),
	   	   ];
        $this->load->view('master/dashboard',$data);
        
        //SELECT sum(e.amount) as total FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where  e.apiclid IN (SELECT cus_id From customers where cus_type='distributor' and cus_reffer='63') ORDER BY e.recid DESC

	}
	
	public function otp()
    {
        if(isset($_POST['checkotp']))
        {
            $enteredotp = $this->input->post('otp');
            $otp = $this->session->userdata('otp');
            if($enteredotp == $otp){
                $this->session->set_userdata('isLoginMaster', TRUE);
                redirect('master/dashboard');
            }
            else{
                
                
                $this->session->set_flashdata('msg', 'Entered OTP is Invalid');
                $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
                $this->load->view('master/otp',$data);
            }
        }
        else{
           $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
                $this->load->view('master/otp',$data);
        }
    }
    
    
    
    public function resendotp(){
        
        
        $otp=rand(100000,999999); 
        $this->session->set_userdata('otp', $otp);
        
	    $mobile = $this->session->userdata('mas_mobile');
	    $email = $this->session->userdata('mas_email');
	    $name = strtoupper($this->session->userdata('mas_name'));
	
	    $msg= $name.", Your One Time OTP Is ".$otp;
	    $sub="Login OTP From Recharge";
	    $to=$email;
	    
	    $r=$this->email_model->send_email($to,$msg,$sub);
	    $this->msg_model->sendsms($mobile,$msg);
		
		redirect('master/otp');
        
    }
    
    
    
    public function assign_user_creadit()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $r['dist'] = $this->db_model->getAlldata("SELECT * FROM `customers` WHERE cus_type='distributor' and cus_reffer='".$this->session->userdata('mas_id')."' ORDER BY cus_id DESC");
		    $r['credits'] = $this->db_model->getAlldata("select * from users_credits where cust_id='".$this->session->userdata('mas_id')."' order by users_credits_id desc limit 1");
		    $this->load->view('master/assign-user-creadit',$r);
		}
	}
		public function getuser_creadit(){
	    
	    $cus_id = $this->input->post('cus_id');
	    
		$r = $this->db_model->getAlldata("select * from users_credits where cust_id='$cus_id' order by users_credits_id desc limit 1");
		if($r)
		{
			$closing = $r['0']->available_count;
		}else{
			$closing = 0;
		}
		
		echo $closing;
		
		exit;
	}
	public function assign_credits()
	{
		$mastercreditbalance = $this->input->post('mastercreditbalance');
		$cus_id = $this->input->post('cus_id');
		$user_count = $this->input->post('user_count');
		$creditbalance = $this->input->post('creditbalance');
    		$data = array(
    						'cust_id'	=>	$cus_id,
    						'user_count'	=>	$user_count,
    						'available_count'	=>	$user_count+$creditbalance,
    					);
    					$id=$this->db_model->insert_update('users_credits',$data);
    					if($id){
    					    $mascre=$mastercreditbalance-$user_count;
        					    $data = array(
        						'cust_id'	=>	$this->session->userdata('mas_id'),
        						'user_count'	=>	$mastercreditbalance,
        						'available_count'	=>	$mascre,
        					);
        					$id=$this->db_model->insert_update('users_credits',$data);
    					    
    					    
    					    
    					    $this->session->set_flashdata('success', 'Assign User Credit Successfully');
    					}
    					else{
    					    $this->session->set_flashdata('error', "Can't Assign Credits");
    					}				
    		redirect('master/assign_user_creadit');	
	
					
	}
	public function successAmt(){
	    return $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' AND  apiclid='".$this->session->userdata('mas_id')."' AND date(reqdate)=CURDATE() ");
	}
	
	public function failedAmt(){
	    return $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' AND apiclid='".$this->session->userdata('mas_id')."' AND  date(reqdate)=CURDATE() ");
	}
	
	public function getApiSources(){
	    return $this->db_model->getAlldata("SELECT * FROM `apisource` ");
	}
	
	public function getOperators(){
	    return $this->db_model->getAlldata("SELECT * FROM `operator` ");
	}
	
	public function getMembers(){
	    return $this->db_model->getAlldata("SELECT * FROM `customers` WHERE cus_type='retailer' ORDER BY cus_id DESC ");
	}
	
	public function getMembersByType(){
	    $cus_type = $this->input->post('cus_type');
	    if($cus_type=="all"){
	        $r = $this->db_model->getAlldata("SELECT * FROM `customers` WHERE cus_type='retailer' ORDER BY cus_id DESC ");
	        echo '<option value="">Select Retailer</option>';	
    	  	 $v=null; foreach($r as $i=>$v)
    	  	{
    	  		echo '<option value='.$v->cus_id.'>'.$v->cus_name.' ('.$v->cus_id.')</option>';
    		}
	    }else{
	        $mas_id = $this->session->userdata('mas_id');
	        $r =$this->db_model->getAlldata("SELECT * FROM customers WHERE  customers.cus_type='$cus_type' and cus_reffer = '".$mas_id."' ");
    	  	echo '<option value="">Select '.ucfirst($cus_type).'</option>';	
    	  	 $v=null; foreach($r as $i=>$v)
    	  	{
    	  		echo '<option value='.$v->cus_id.'>'.$v->cus_name.' ('.$v->cus_id.')</option>';
    		}
	    }
	}
	
	public function getOperatorsByType(){
	    $operator_type = $this->input->post('operator_type');
	    $r =$this->db_model->getAlldata("SELECT * FROM operator WHERE  opsertype='$operator_type'");
	  	echo '<option value="">Select '.ucfirst($operator_type).'</option>';	
	  	$v=null; foreach($r as $i=>$v)
	  	{
	  		echo '<option value='.$v->opid.'>'.$v->operatorname.'</option>';
		}
	}
	
	public function getMemberPackage(){
	    
        $mas_id = $this->session->userdata('mas_id');
	    $cus_type = $_POST['cus_type'];
	    $dataw = $this->db_model->getAlldata("SELECT * FROM `exr_package` WHERE package_addedby =$mas_id and package_membertype=$cus_type ");
        echo '<option value="">Select Package</option>';	
	  	$v=null; foreach($data as $i=>$v)
	  	{
	  		echo '<option value='.$v->package_id.'>'.ucfirst($v->package_name).'</option>';
		}
	}
	
	public function getUplineMembers(){
	    $cus_type = $_POST['cus_type'];
	    
	    if($cus_type == "distributor"){
	        $cus_id = $this->session->userdata('mas_id');
	        $data = $this->db_model->getwhere('customers',array("cus_type"=>"master","avability_status"=>"0","cus_id" => $cus_id));
	        if($data){
    	        echo '<div class="form-group"><label class="form-label">Master:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" required>';
    	        $v=null; 
    	        foreach($data as $i=>$v){
    	            echo '<option value='.$v->cus_id.'>'.ucfirst($v->cus_name).'</option>';
    	        }
    	        echo '</select></div></div>';
	        }
	    }else if($cus_type == "retailer"){
	        $data = $this->db_model->getwhere('customers',array("cus_type"=>"distributor","avability_status"=>"0"));
	        if($data){
    	        echo '<div class="form-group"><label class="form-label">Select Distributor:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" required>';
    	        $v=null; 
    	        foreach($data as $i=>$v){
    	            echo '<option value='.$v->cus_id.'>'.ucfirst($v->cus_name).'</option>';
    	        }
    	        echo '</select></div></div>';
	        }
	    }else if($cus_type == "api"){
	        $data = $this->db_model->getwhere('customers',array("cus_type"=>"distributor","avability_status"=>"0"));
	        if($data){
    	        echo '<div class="form-group"><label class="form-label">Select Distributor:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" required>';
    	        $v=null; 
    	        foreach($data as $i=>$v){
    	            echo '<option value='.$v->cus_id.'>'.ucfirst($v->cus_name).'</option>';
    	        }
    	        echo '</select></div></div>';
	        }
	    }
	}
	
	public function search_recharge_report(){
	    $operator_type = $_POST['operator_type'];
	    $operator = $_POST['operator'];
	    $apisource = $_POST['apisource'];
	    $member_type = $_POST['member_type'];
	    $member_id = $_POST['member_id'];
	    $status = $_POST['status'];
	    $mobile_number = $_POST['mobile_number'];
	    $rec_id = $_POST['rec_id'];
	    $date = $_POST['date'];
	    
	    $sql = "SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew join apisource as ap on e.apisourceid = ap.apisourceid where ";
		if($operator_type)
		{
			$sql .= "o.opsertype ='".$operator_type."' and ";
		}		
		
		if($operator)
		{
			$sql .= "e.operator ='".$operator."' and ";
		}

		if($apisource)
		{
			$sql .= "e.apisourceid ='".$apisource."' and ";
		}

		if($mobile_number)
		{
			$sql .= "e.mobileno ='".$mobile_number."' and ";
		}

		if($rec_id)
		{
			$sql .= "e.recid ='".$rec_id."' and ";
		}

		/*if($from !='' && $to !='')
		{
			$sql .="DATE(e.reqdate) >= '".$from.' 00:00:00'."' and DATE(e.reqdate) <= '".$to.' 00:00:00'."' and ";
		}*/
		
		if($date !='')
		{
			$sql .="DATE(e.reqdate) ='".$date."' and ";
		}

		if($member_id)
		{
			$sql .= "e.apiclid ='".$member_id."' and ";
		}

		if($status)
		{
			$sql .= "e.status ='".$status."' and ";
		}

		if($member_type && $member_type!='all')
		{
			$sql .= "c.cus_type ='".$member_type."' and";
		}
		
		$sql .= " e.recid != '' ORDER BY e.recid DESC";
		//echo $sql;exit;
		$data['success'] = $this->successAmt();
        $data['failed'] = $this->failedAmt();
        $data['apisources'] = $this->getApiSources();
        $data['operator'] = $this->getOperators();
        $data['members'] = $this->getMembers();
        $data['recharges'] = $this->db_model->getAlldata("$sql");
        $this->load->view('master/view-recharge-report',$data);
	    
	}
	
	public function recharge_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where DATE(e.reqdate) >= '".date('Y-m-d')."' and DATE(e.reqdate) <= '".date('Y-m-d')."' and c.cus_reffer IN (SELECT cus_id From customers where cus_type='distributor' and cus_reffer='".$this->session->userdata('mas_id')."') ORDER BY e.recid DESC ");
		    $this->load->view('master/view-recharge-report',$data);
		}
	}
	
	public function pending_recharge_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE status='PENDING' ORDER BY e.recid DESC");
		    $this->load->view('master/view-recharge-report',$data);
		}
	}
	
	public function failed_recharge_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE status='FAILED' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('master/view-recharge-report',$data);
		}
	}
	
	public function success_recharge_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE status='SUCCESS' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('master/view-recharge-report',$data);
		}
	}
	
	public function transaction_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where DATE(e.txn_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.txn_date) <= '".date('Y-m-d').' 00:00:00'."' and c.cus_reffer IN (SELECT cus_id From customers where cus_type='distributor' and cus_reffer='".$this->session->userdata('mas_id')."') or c.cus_id='".$this->session->userdata('mas_id')."' ORDER BY e.txn_id DESC");
		    $this->load->view('master/transaction-report',$data);
		}
	}
	
	public function dmt_transaction_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `dmt_trnx` as e join customers as c on e.cus_id = c.cus_id JOIN exr_trnx ON e.dmt_trnx_id = exr_trnx.dmt_txn_recrefid and e.cus_id = '".$this->session->userdata('mas_id')."' ORDER BY e.dmt_trnx_id DESC");
		    $this->load->view('master/dmt-transaction-report',$data);
		}
	}
	
	public function redeem_request_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `payout_request` as e join customers as c on e.cus_id = c.cus_id WHERE status='PENDING' and e.cus_id = '".$this->session->userdata('mas_id')."' ");
		    $this->load->view('master/redeem-request-report',$data);
		}
	}
	
	public function redeem_wallet_report()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join customers as c on e.txn_agentid = c.cus_id WHERE e.txn_agentid = '".$this->session->userdata('mas_id')."' and txn_type LIKE '%PAYOUT%'");
		    $this->load->view('master/redeem-wallet-report',$data);
		}
	}
	
    public function redeem_request_history()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `payout_request` as e join customers as c on e.cus_id = c.cus_id WHERE status!='PENDING' and e.cus_id = '".$this->session->userdata('mas_id')."' ");
		    $this->load->view('master/redeem-request-history',$data);
		}
	}
	
	public function redeem_transaction()
	{
	    if(isset($_POST['redeem_transaction']))
		{
		    $id=$_POST['pay_req_id'];
		    
		    print_r($id);
		    exit;
		}
	}
	
	
	public function add_member()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $mas_id = $this->session->userdata('mas_id');
		    $r = $this->db_model->getAlldata("select * from users_credits where cust_id='$mas_id' order by users_credits_id desc limit 1");
		    $data['credits'] = $r['0']->available_count;
		    $data['package'] = $this->db_model->getwhere('exr_package');
		    $this->load->view('master/add-member',$data);
		}
	}
	
	public function add_member_succ()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $mobile = $this->encryption_model->encode($this->input->post('mobile'));
		    $pass = rand(10000,99999);
		    $pin = rand(10000,99999);
		    $cus_type = $this->input->post('cus_type');
		    $refer = $this->input->post('reffer_id');
		    $package = $this->input->post('package_id');
		    
		    $r = $this->db_model->getwhere('customers',array('cus_mobile'=>$mobile));
		    
		    if($r){
		        
		         $this->session->set_flashdata('error','Member With Entered Mobile Number Already Exists');
		         redirect('master/add-member');
		         
		    }else{
		            $refer=$this->session->userdata('mas_id');
    		        $data = array(
                    		        "cus_mobile" => $mobile,
                    		        "cus_name" => $this->input->post('name'),
                    		        "cus_email" => $this->input->post('email'),
                    		        "cus_outlate" => $this->input->post('outlet'),
                    		        "cus_address" => $this->input->post('address'),
                    		        "cus_type" => $cus_type,
                    		        "cus_reffer" => $refer,
                    		        "package_id"=>$package,
                    		        "cus_pass" => $this->encryption_model->encode($pass),
                    		        "cus_pin" => $this->encryption_model->encode($pin)
                    		        );
                    		    $inserted_id=$this->db_model->insert_update('customers',$data);
                    		    
                    		    $this->session->set_flashdata('success','Member Created Successfully');
                    		    
                    		    $message=array('mobile'=>$this->input->post('mobile'),'pass'=>$pass,'pin'=>$pin);
                    		    $this->msg_model->registration($message);
                    		    
                    		    $email = $this->input->post('email');
                        		$msg= $message = "Welcome in ".$this->config->item('title')." family, your member id:  ".$this->input->post('mobile').", login pass: ".$pass.", login pin: ".$pin.", APP Link: ".$this->config->item('ataapp');
                        		$sub="Successfully Registration";
                        		$to=$email;
                        		$this->email_model->send_email($to,$msg,$sub);
        		                
        		                if($cus_type == "retailer"){
                    		        redirect('master/view-all-retailer');
                    		    }else if($cus_type == "distributor"){
                    		        redirect('master/view-all-distributor');
                    		    }else if($cus_type == "master"){
                    		        redirect('master/view-all-master');
                    		    }else{
                    		        redirect('master/view-all-members');
                    		    }
    		  //  $resps = $this->db_model->getAlldata("select * from users_credits where cust_id='$refer' order by users_credits_id desc limit 1");
        // 		        if($resps){
        // 		            $available_count=$resps[0]->available_count;
        // 		            $users_credits_id=$resps[0]->users_credits_id;
        // 		            if($available_count>0){
        //             		    if($package == NULL || $package == 'SELECT API'){$package = 0;}
                    		    
        //             		    $data = array(
        //             		        "cus_mobile" => $mobile,
        //             		        "cus_name" => $this->input->post('name'),
        //             		        "cus_email" => $this->input->post('email'),
        //             		        "cus_outlate" => $this->input->post('outlet'),
        //             		        "cus_address" => $this->input->post('address'),
        //             		        "cus_type" => $cus_type,
        //             		        "cus_reffer" => $refer,
        //             		        "package_id"=>$package,
        //             		        "cus_pass" => $this->encryption_model->encode($pass),
        //             		        "cus_pin" => $this->encryption_model->encode($pin)
        //             		        );
        //             		    $inserted_id=$this->db_model->insert_update('customers',$data);
        //             		    if($inserted_id){
        //                 		        $available_count=$available_count-1;
        //                 		        $this->db_model->insert_update('users_credits',array('available_count'=>$available_count),array('users_credits_id'=>$users_credits_id));
        //                 		    }
        //             		    $this->session->set_flashdata('success','Member Created Successfully');
                    		    
        //             		    if($cus_type == "retailer"){
        //             		        redirect('master/view-all-retailer');
        //             		    }else if($cus_type == "distributor"){
        //             		        redirect('master/view-all-distributor');
        //             		    }else if($cus_type == "master"){
        //             		        redirect('master/view-all-master');
        //             		    }else{
        //             		        redirect('master/view-all-members');
        //             		    }
        // 		            }
        // 		            else{
        // 		                $this->session->set_flashdata('error',"You Are Done With You Credits, Please Contact To Admin For Upgrade Credits");
        // 		                redirect('master/add-member');
        // 		            }
        // 		        }
        // 		        else{
        // 		            $this->session->set_flashdata('error',"You Don't Have Credits For Creating Members, Please Contact To Admin");
        // 		            redirect('master/add-member');
        // 		        }
		    }
		}
	}
	
	public function edit_member($id){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $a = array();
    		$resps = $this->db_model->getwhere('customers', array('cus_id' => $id));
    		$data['package'] = $this->db_model->getwhere('exr_package');
    		$data['dmt_package'] = $this->db_model->getwhere('dmt_commission_slab');
    		$res = $resps[0];
    		{
    			$a['country'] = $a['state'] = $a['city'] = 'Null';
    			if ($res->cus_country)
    				$a['country'] = $this->find_country($res->cus_country);
    			if ($res->cus_state)
    				$a['state'] = $this->find_state($res->cus_state);
    			if ($res->cus_city)
    				$a['city'] = $this->find_city($res->cus_city);
    			    $a['image'] = $this->db_model->getwhere('cus_proof', array('cus_id' => $id));
    			    $a['info'] = $res;
    			    $data['r'] = $a;
    			    $data['mobile'] = $resps[0]->cus_mobile;
    		}
		    $this->load->view('master/edit-member',$data);
		}
	}
	
	public function edit_member_succ(){
	     $cus_id = $this->input->post('cus_id');
	     $data = array(
		        "cus_mobile" => $this->encryption_model->encode($this->input->post('mobile')),
		        "cus_name" => $this->input->post('name'),
		        "cus_email" => $this->input->post('email'),
		        "cus_outlate" => $this->input->post('outlet'),
		        "cus_address" => $this->input->post('address'),
		        "package_id"=> $this->input->post('package_id'),
		        "dmt_comm_id"=> $this->input->post('dmt_package_id')
		        );
    	  $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
    	  $this->session->set_flashdata('success','Member Updated Successfully');
    	  redirect($this->agent->referrer());
	}
	
	public function credit_wallet()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('master/credit-wallet');
		}
	}
	
	public function debit_wallet()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('master/debit-wallet');
		}
	}
	
	public function daybook()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['fundCredit'] = $this->db_model->getAlldata("SELECT sum(txn_dbdt) as amt FROM `exr_trnx` where txn_type='Pullout' AND txn_fromto='0' AND date(txn_date)=CURDATE()");
		    $data['fundDebit'] = $this->db_model->getAlldata("SELECT sum(txn_crdt) as amt FROM `exr_trnx` where txn_type='Direct Credit' AND txn_fromto='0' AND date(txn_date)=CURDATE()");
		    $this->load->view('master/daybook',$data);
		}
	}
	
	public function commission_package()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `exr_package` WHERE package_addedby = '".$this->session->userdata('mas_id')."'");
		    $this->load->view('master/commission-package',$data);
		}
	}
	
	public function dmt_package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('slab');
		//$type = $this->input->post('package_type');
		$type = 'flat';
		$member_type = $this->input->post('member_type');
		$commission = $this->input->post('commission');
		//$max_commission = $this->input->post('max_commission');
		$r = $this->db_model->insert_update('dmt_commission_slab', array('slab'=>$name, 'member_type'=>$member_type, 'dmt_update_time'=>$date, 'amount'=>$commission,'max_commi_amount'=>$commission,'type'=>$type));
		if($r)
		{
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('master/dmt-commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('master/dmt-commission-package');
		}
		
	}
	
	public function dmt_delete_package($id)
	{
		
		$this->db->delete('dmt_commission_slab', array('dmt_comm_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('master/dmt-commission-package');
	
	}
	
	public function aeps_delete_package($id)
	{
		
		$this->db->delete('aeps_commission_slab', array('aeps_comm_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('master/aeps-commission-package');
	
	}
	
	public function package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$addedby = $this->session->userdata('mas_id');
		$name = $this->input->post('package_name');
		$type = $this->input->post('package_type');
		$r = $this->db_model->insert_update('exr_package', array('package_name'=>$name, 'package_membertype'=>$type, 'package_date'=>$date, 'package_ip'=>$ip , 'package_addedby'=>$addedby ));
		$id = $this->db->insert_id();
		if($r)
		{
			$ope = $this->db_model->getWhere('operator');
			foreach($ope as $operator)
			{
				$this->db_model->insert_update('exr_packagecomm', array('packcomm_name'=>$id, 'packcomm_opcode'=>$operator->opcodenew,'packagecom_tem'=>1,'packagecom_type'=>2,'packagecom_amttype'=>2, 'packcomm_comm'=>0, 'packcomm_date'=>$date, 'packcomm_ip'=>$ip));
			}
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('master/commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('master/commission-package');
		}
		
	}
	
	public function delete_package($id)
	{
		$ck = $this->db_model->getWhere('customers', array('package_id'=>$id));
		if(!$ck)
		{
			$this->db->delete('exr_package', array('package_id'=>$id));
			$msg = "<strong>Delete!</strong> Successfully";
			$this->session->set_flashdata('success','Package Deleted Successfully');
			redirect('master/commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Deletion Failed! Please try again later');
			redirect('master/commission-package');
		}
	}
	
	public function view_package_commission($id)
	{
		$data['package'] = $this->db_model->getAlldata("select * from `exr_packagecomm` as exrpc join `exr_package` as exrp on exrpc.packcomm_name = exrp.package_id join `operator` as op on exrpc.packcomm_opcode = op.opcodenew where exrpc.packcomm_name=$id");
		$this->load->view('master/view-all-package-commission',$data);
	}
	
	public function package_commission_update()
	{
		$new_comm = $this->input->post('new_comm');
		$old_comm = $this->input->post('old_comm');
		$packcomm_id = $this->input->post('packcomm_id');
		$this->db_model->insert_update('exr_packagecomm', array('packagecom_tem' => 1, 'packagecom_type' => 2, 'packagecom_amttype' => 2, 'packcomm_comm' => $new_comm,'packcomm_date'=>date('Y-m-d h:s:i')), array('packcomm_id' => $packcomm_id));
		echo "Commission Updated Successfully";
	}

	
	public function api_commission_package()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('master/api-package-commission');
		}
	}
	
	public function aeps_commission_package()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `customers` as c , aeps_commission_slab as ac where c.aeps_comm_id = ac.aeps_comm_id and c.cus_id = '".$this->session->userdata('mas_id')."'");
		    $this->load->view('master/aeps-commission-package',$data);
		}
	}
	
	public function dmt_commission_package()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `customers` as c , dmt_commission_slab as dc where c.dmt_comm_id = dc.dmt_comm_id and c.cus_id = '".$this->session->userdata('mas_id')."'");
		    $this->load->view('master/dmt-commission-package',$data);
		}
	}
	
	public function micro_atm_commission_package(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `micro_atm_commission_slab`");
		    $this->load->view('master/view-micro-atm-commission-package',$data);
		}
	}
	
	
	public function profile()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $mas_id = $this->session->userdata('mas_id');
	        $data['data'] = $this->db_model->getwhere('customers',array('cus_id'=>$mas_id));
		    $this->load->view('master/profile',$data);
		}
	}
	
	public function update_profile()
  	{  	
	  	$user_id = $this->session->userdata('user_id');
		$name = $this->input->post('name');
		$username = $this->input->post('username');
		$email = $this->input->post('email');
		$mobile = $this->encryption_model->encode($this->input->post('mobile'));
		$address = $this->input->post('address');
		$twitter = $this->input->post('twitter');
		$facebook = $this->input->post('facebook');
		$instagram = $this->input->post('instagram');
		
		if(!empty($_FILES['image']['name'])){
            $config['upload_path'] = 'assets/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['file_name'] = 'logo.png';
            $config['overwrite'] = TRUE;
            //Load upload library and initialize configuration
            $this->load->library('upload',$config);
            $this->upload->initialize($config);
            
            $this->upload->do_upload('image');
        }
		
	    $r = $this->db_model->insert_update('user',array('username'=>$username,'name'=>$name,'email'=>$email,'mobile'=>$mobile,'address'=>$address,'twitter'=>$twitter,'facebook'=>$facebook,'instagram'=>$instagram),array('user_id'=>$user_id));
		if($r)
		{
			$this->session->set_flashdata('success','Profile Updated Successfully..');
			redirect('master/profile');		
		}
		else
		{
			$this->session->set_flashdata('error','Profile not Updated..');
			redirect('master/profile');
		}
	}
	
	public function account()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('master/account-setting');
		}
	}
	
	function check_password() 
	{
	    
	    $old_password =$this->input->post('old_password');
	    $data=$this->db_model->getwhere('customers',array('cus_pass'=>$this->encryption_model->encode($old_password),'cus_id'=>$this->session->userdata('mas_id')));
	    if($data){
	        echo TRUE;
	    }
        else{
            echo FALSE;
        }
        
    }
    
    function check_pin() 
	{
	    
	    $old_pin =$this->input->post('old_pin');
	    $data=$this->db_model->getwhere('customers',array('cus_pin'=>$this->encryption_model->encode($old_pin),'cus_id'=>$this->session->userdata('mas_id')));
	    if($data){
	        echo TRUE;
	    }
        else{
            echo FALSE;
        }
        
    }
    
	public function password_and_pin()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('master/login');
		}
		else{
		    if(isset($_POST['update_password']))
		    {
		        $record=array(
				            'cus_pass'=>$this->encryption_model->encode($_POST['new_password'])
				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('mas_id')));
    			$this->session->set_flashdata('success','Password Updated Successfully...!!!');
		    }
		    
		    if(isset($_POST['update_pin']))
		    {
                $record=array(
			            'cus_pin'=>$this->encryption_model->encode($_POST['new_pin'])
			        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('mas_id')));
    			$this->session->set_flashdata('success','Pin Updated Successfully...!!!');
		    }
		    
		    redirect('master/account');
		}
	}
	
	public function view_all_members(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers where cus_reffer = '".$this->session->userdata('mas_id')."' ORDER BY cus_id DESC");
		    $this->load->view('master/view-members',$data);
		}
	}
	
	public function view_all_distributor(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_reffer = '".$this->session->userdata('mas_id')."' and cus_type='distributor' ORDER BY cus_id DESC");
		    $this->load->view('master/view-members',$data);
		}
	}
	
	public function view_all_retailer(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_reffer = '".$this->session->userdata('mas_id')."' and cus_type='retailer' ORDER BY cus_id DESC");
		    $this->load->view('master/view-members',$data);
		}
	}
	
	
	public function view_all_api_clients(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_reffer = '".$this->session->userdata('mas_id')."' and cus_type='api' ORDER BY cus_id DESC");
		    $this->load->view('master/view-members',$data);
		}
	}
	
	public function find_country($id="")
    {
        $resps = $this->db_model->getwhere('country',array('country_id'=>$id));
        return  $resps[0]->country_name;
    }
       
    public function find_state($id="")
    {
        $resps = $this->db_model->getwhere('state',array('state_id'=>$id));
        return  $resps[0]->state_name;
    }
       
    public function find_city($id="")
    {
        $resps = $this->db_model->getwhere('city',array('city_id'=>$id));
        return  $resps[0]->city_name;
    }
	
	public function member_details($id){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $a = array();
    		$resps = $this->db_model->getwhere('customers', array('cus_id' => $id));
    		$res = $resps[0];
    		{
    			$a['country'] = $a['state'] = $a['city'] = 'Null';
    			if ($res->cus_country)
    				$a['country'] = $this->find_country($res->cus_country);
    			if ($res->cus_state)
    				$a['state'] = $this->find_state($res->cus_state);
    			if ($res->cus_city)
    				$a['city'] = $this->find_city($res->cus_city);
    			    $a['image'] = $this->db_model->getwhere('cus_proof', array('cus_id' => $id));
    			    $a['info'] = $res;
    			    $data['r'] = $a;
    			    $data['mobile'] = $resps[0]->cus_mobile;
    		}
		    $this->load->view('master/member-details',$data);
		}
	}
	
	public function moveretailer()
	{
		$dist = $this->input->post('dist');
		$mid = $this->input->post('mid');

		$this->db_model->insert_update('customers',array('cus_reffer'=>$dist),array('cus_id'=>$mid));
		redirect($this->agent->referrer());
	}

	public function movemaster()
	{
		$mast = $this->input->post('mast');
		$mid = $this->input->post('mid');

		$this->db_model->insert_update('customers',array('cus_reffer'=>$mast),array('cus_reffer'=>$mid));
		redirect($this->agent->referrer());
	}

	public function movedistributor()
	{
		$distb = $this->input->post('distb');
		$did = $this->input->post('did');

		$this->db_model->insert_update('customers',array('cus_reffer'=>$distb),array('cus_reffer'=>$did));
		redirect($this->agent->referrer());
	}
	
	public function active_customer($id)
	{
		$this->db_model->insert_update('customers',array('avability_status'=>0),array('cus_id'=>$id));
		$r = $this->db_model->getwhere('customers',array('cus_id'=>$id,'avability_status !='=>'1'));
		redirect('master/member-details/'.$id);
	}

	public function in_active_customer($id)
	{
		$this->db_model->insert_update('customers',array('avability_status'=>1),array('cus_id'=>$id));
		redirect('master/member-details/'.$id);
	}
	
	public function logout_member($id)
	{   
	    $tablename="customers";
	    $data=array('login_status'=>'loggedout','deviceId'=>'');
		$r=$this->db_model->insert_update($tablename,$data,array('cus_id'=>$id));
		$this->session->set_flashdata('success','Member logged Out Successfuly');
		redirect('master/view-all-members');
	}
	
	public function delete_account($id)
	{
		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$id));
		$this->db_model->delete('customers',array('cus_id'=>$id));
		$this->session->set_flashdata('success','Member Deleted Successfuly');
		redirect('master/view-all-members');
	}
	
	public function findparent()
	{
		$id = $this->input->post('id');
		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$id));
		$custype = $this->input->post('custype');
		if($cus[0]->cus_type == 'master' && $custype == 'retailer')
		{
			echo $this->choose_parent('distributor',$id);
		}
		elseif($cus[0]->cus_type == 'master' && $custype == 'distributor')
		{
			echo $this->choose_parent('master',$id);
		}
		elseif($cus[0]->cus_type == 'retailer' && $custype == 'distributor')
		{
			echo $this->choose_parent('master',$id);
		}
		elseif($cus[0]->cus_type == 'distributor' && $custype == 'master')
		{
			echo $this->choose_parent('distributor',$id);
		}
		elseif($cus[0]->cus_type == 'distributor' && $custype == 'retailer')
		{
			echo $this->choose_parent('distributor',$id);
		}
		elseif($cus[0]->cus_type == 'distributor' && $custype == 'customer')
		{
			echo $this->choose_parent('distributor',$id);
		}
		elseif($cus[0]->cus_type == 'master' && $custype == 'customer')
		{
			echo $this->choose_parent('master',$id);
		}
		elseif($cus[0]->cus_type == 'customer' && $custype == 'retailer')
		{
			echo $this->choose_parent('distributor',$id);
		}
		elseif($cus[0]->cus_type == 'customer' && $custype == 'distributor')
		{
			echo $this->choose_parent('master',$id);
		}
		else
		{
			echo "";
		}
	}
	
	private function choose_parent($type,$id)
	{
		$sql = $this->db->query("select * from customers where avability_status !='1' and cus_id !='".$id."' and cus_type='".$type."'")->result();
		$msg = '<select class="form-control" name="cus_reffer" required>
		<option value="">Select '.ucfirst($type).'</option>';
	  	foreach($sql as $i=>$v)
	  	{
	  		$msg .= '<option value='.$v->cus_id.'>'.$v->cus_id.'-'.$v->cus_name.'-'.$v->cus_mobile.'</option>';
		}
		$msg .= '</select>';
		return $msg;
	}
	
	public function switch_success()
	{
		$id = $this->input->post('id');
		$type = $this->input->post('type');
		$cus_reff = $this->input->post('cus_reffer');
		$cusd = $this->db_model->getwhere('customers',array('cus_id'=>$id));

		if($cusd[0]->cus_type == 'distributor' && $type == 'master')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>'0','package_id'=>'1'),array('cus_id'=>$id));
			$cusf = $this->db_model->getwhere('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('success','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('error','Failed! please try again later..');
			}			
		}
		elseif($cusd[0]->cus_type == 'distributor' && $type == 'retailer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>'3'),array('cus_id'=>$id));
			$cusf = $this->db_model->getwhere('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('success','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('error','Failed! please try again later..');
			}
		}
		elseif($cusd[0]->cus_type == 'distributor' && $type == 'customer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>'0','package_id'=>'4'),array('cus_id'=>$id));
			$cusf = $this->db_model->getwhere('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('success','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('error','Failed! please try again later..');
			}
		}
		elseif($cusd[0]->cus_type == 'master' && $type == 'customer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>'0','package_id'=>'4'),array('cus_id'=>$id));
			$cusf = $this->db_model->getwhere('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('success','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('error','Failed! please try again later..');
			}
		}
		elseif($cusd[0]->cus_type == 'customer' && $type == 'distributor')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>'2'),array('cus_id'=>$id));
			$this->session->set_flashdata('success','Account switched successfully..');
		}
		elseif($cusd[0]->cus_type == 'customer' && $type == 'retailer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>'3'),array('cus_id'=>$id));
			$this->session->set_flashdata('success','Account switched successfully..');
		}
		else
		{
			$pkg = $this->db_model->getwhere('exr_package',array('package_membertype'=>$type));
			$r = $this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>$pkg[0]->package_id),array('cus_id'=>$id));
			$this->session->set_flashdata('success','Account switched successfully..');
		}
		redirect('master/view-all-members');
	}
	
	public function switch_account($id){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
    	    $data['r'] = $this->db_model->getwhere('customers',array('cus_id'=>$id));
    		$this->load->view('master/switch-account',$data);
		}
	}
	
	public function view_all_open_tickets()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['tickets'] = $this->db_model->getAlldata("SELECT * FROM `exr_package` WHERE package_addedby = '0'");
		    $this->load->view('master/view-all-open-tickets',$data);
		}
	}
	
	public function view_all_closed_tickets()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['tickets'] = $this->db_model->getAlldata("SELECT * FROM `exr_package` WHERE package_addedby = '0'");
		    $this->load->view('master/view-all-closed-tickets',$data);
		}
	}
	
	public function getMemberBalance(){
	    
	    $cus_id = $this->input->post('cus_id');
	    
		$r = $this->db_model->getAlldata("select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1");
		if($r)
		{
			$closing = $r['0']->txn_clbal;
		}else{
			$closing = 0;
		}
		
		echo $closing;
		
		exit;
	}
	
	public function wallet_topup_success()
	{
	    $mas_id = $this->session->userdata('mas_id');
		$cus_id = $this->input->post('cus_id');
		$topup_bal = $this->input->post('topup_bal');
		$transfer_type = $this->input->post('transfer_type');
		$bank_name = "";
		$transaction_ref = $this->input->post('transaction_ref');
		$paid_due = '1';
		$transaction_date = date("Y-m-d H:s:i");
		$vb_comment = $this->input->post('vb_comment');
		$cusdt = $this->db_model->getAlldata("select cus_mobile,cus_type from customers where cus_id='".$cus_id."'");
		$txn_crdt = $topup_bal;
		$txntype = 'Direct Credit';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
		$txnid = '0';
		
		$cus_type = $cusdt[0]->cus_type;
		$mobile = $cusdt[0]->cus_mobile;
	    
		$sql = "select * from exr_trnx where txn_agentid='$mas_id' order by txn_id desc limit 1";
		$r = $this->db_model->getAlldata($sql);
		if($r)
		{
			if($r[0]->txn_clbal > $txn_crdt)
			{
			   
			    $opening = $r[0]->txn_clbal;
			    $total1 = $opening - $topup_bal;
				$data12 = array(
					'txn_agentid'=>$mas_id,
					'txn_opbal'=> $r[0]->txn_clbal,
					'txn_crdt'=> 0,
					'txn_dbdt'=> $topup_bal,
					'txn_fromto'=>$cus_id,
					'txn_clbal'=>$total1,
					'txn_type'=>'Fund Transfer',
					'txn_time'=>time(),
					'txn_checktime'=>$id.time(),
					'txn_date'=>DATE('Y-m-d h:i:s'),
					'txn_ip'=>$ip
				);
			    
			    $res11 =$this->db_model->insert_update('exr_trnx',$data12);
			    
				$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
				$r = $this->db_model->getAlldata($sql);
				if($r)
				{
					$closing = $r['0']->txn_clbal;
					$total = $r['0']->txn_clbal + $txn_crdt;
					$txn_dbdt = 0;
					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_fromto'=>$mas_id,'txn_clbal'=>$total,'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_date'=>$date,'txn_time'=>$time,'txn_ip'=>$ip));
					$txn = $this->db->insert_id();
					
					if($lasttxnrec)
					{
						$msg2 = "Credit Successfully";				
						$this->session->set_flashdata('success', $msg2);
						$message = "Dear member, your account has successfully credited rs.".$txn_crdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');
					}
					else
					{
						$msg2 = "Failed! Please try once again";
						$this->session->set_flashdata('error', $msg2);
					}
				}
				else
				{
					$closing = 0;
					$total = $txn_crdt;
					$txn_dbdt = 0;
					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_fromto'=>$mas_id,'txn_clbal'=>$total,'txn_date'=>$date,'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
					$txn = $this->db->insert_id();
					if($lasttxnrec)
					{
						$msg2 = "Credit Successfully";		
						$this->session->set_flashdata('success', $msg2);
						$message = "Dear member, your account has successfully credited rs.".$txn_crdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');
					}
					else
					{
						$msg2 = "Failed! Please try once again";
						$this->session->set_flashdata('error', $msg2);
					}
				}
				//$this->msg_model->sendsms($cusdt[0]->cus_mobile,$message);
			}
			else
			{
				$msg2 = "Insufficient fund...";				
				$this->session->set_flashdata('error', $msg2);
			}
		}
		else
		{
			$msg2 = "Insufficient fund...";				
			$this->session->set_flashdata('error', $msg2);
		}
		redirect('master/credit-wallet');
		
	    //	echo $this->db->last_query();
	}
	
	public function wallet_pullout_success()
	{
	    $mas_id = $this->session->userdata('mas_id');
		$cus_id = $this->input->post('cus_id');
		$pullout_bal = $this->input->post('pullout_bal');
		$txn_comment = $this->input->post('remarks');
		$cusdt = $this->db_model->getAlldata("select cus_mobile,cus_type from customers where cus_id='".$mas_id."'");
		$txn_dbdt = $pullout_bal;
		$txntype = 'Pullout';
		$txntype = 'Direct Debit';
		$txntype1 = 'Fund Back';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
		
		$old = $this->db_model->getAlldata("select * from exr_trnx where txn_agentid='$mas_id' order by txn_id desc limit 1");
		$clbal = $old[0]->txn_clbal;

		$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
		$r = $this->db_model->getAlldata($sql);
		if($r)
		{
		  	if($pullout_bal > 0)
		  	{
			    $closing = $r['0']->txn_clbal;
				$total = $r['0']->txn_clbal - $txn_dbdt;
				$total1 = $clbal + $txn_dbdt;

				if($closing >= $txn_dbdt)
				{
					$txn_crdt = 0;
					$lasttxnrec = $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_fromto'=>$mas_id,'txn_clbal'=>$total,'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));

					$lasttxnrec1 = $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$mas_id,'txn_fromto'=>$cus_id,'txn_opbal'=>$clbal,'txn_crdt'=>$txn_dbdt,'txn_dbdt'=>$txn_crdt,'txn_clbal'=>$total1,'txn_type'=>$txntype1,'txn_time'=>$time,'txn_ip'=>$ip));

					if($lasttxnrec)
					{
						$msg2 = "Balance successfully debit from user account";			
						$this->session->set_flashdata('success', $msg2);
						$message = "Dear member, your account has successfully credited rs.".$txn_crdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');
					}
					else
					{
						$msg2 = "Failed! Please try once again";
						$this->session->set_flashdata('error', $msg2);
					}	
				}
				else
				{
        			$msg2 = "Insufficient fund...";				
        			$this->session->set_flashdata('error', $msg2);
				}
			}
			else
			{
    			$msg2 = "Amount is not < 1";				
    			$this->session->set_flashdata('error', $msg2);
			}
		}
		else
		{
			$msg2 = "Amount not available..";				
			$this->session->set_flashdata('error', $msg2);
		}

		
		redirect('master/debit-wallet');
	//echo $this->db->last_query();
	}
	
	
	
	
	public function virtual_balance(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['vb'] = $this->db_model->getAlldata("SELECT * FROM `virtual_balance` ORDER BY `vb_id` DESC");
		    $this->load->view('master/virtual-balance',$data);
		}
	}
	
	public function search_vb_report(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    
    	    $member_type = $_POST['member_type'];
    	    $member_id = $_POST['member_id'];
    	    $date = $_POST['date'];
    	    
    	    $sql = "SELECT * FROM `virtual_balance` WHERE ";
    
    		/*if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.reqdate) >= '".$from.' 00:00:00'."' and DATE(e.reqdate) <= '".$to.' 00:00:00'."' and ";
    		}*/
    		
    		if($date !='')
    		{
    			$sql .="DATE(transaction_date) ='".$date."' and ";
    		}
    
    		if($member_id)
    		{
    			$sql .= "vb_agentid ='".$member_id."' and ";
    		}
    		
    		$sql .= " vb_id != '' ORDER BY `vb_id` DESC";
    		    
		    $data['vb'] = $this->db_model->getAlldata("$sql");
		    
		    //echo $this->db->last_query();exit;
		    $this->load->view('master/virtual-balance',$data);
    	}
	}
	
	public function virtual_balance_success()
	{
		$txn_crdt = $this->input->post('amount');
		$txntype = 'Virtual Fund Added';
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = "select * from virtual_balance order by vb_id desc limit 1";
		$r = $this->db_model->getAlldata($sql);
		if($r)
		{
			$closing = $r['0']->vb_clbal;
			$total = $r['0']->vb_clbal + $txn_crdt;
			$txn_dbdt = 0;
		}else{
			$closing = 0;
			$total = $txn_crdt;
			$txn_dbdt = 0;
		}
		$data = array(
				'vb_agentid'	=>	0,
				'vb_recrefid'	=>	0,
				'vb_opbal'	=>	$closing,
				'vb_crdt'	=>	$txn_crdt,
				'vb_dbdt'	=>	$txn_dbdt,
				'vb_clbal'	=>	$total,
				'vb_checktime'	=>	time(),
				'vb_type'	=>	$txntype,
				'vb_fromto'	=>	'',
				'vb_time'	=>	time(),
				'vb_date'	=>	date('Y-m-d h:i:s'),
				'vb_ip'	=>	$ip,
				'vb_comment'	=>	$txntype,
				'transaction_type'	=>	'',
				'bank_name'	=>	'',
				'transaction_ref'	=>	'',
				'paid_due'	=>	'1',
				'transaction_date'	=>	'',
				'status'	=>	1
        	);
        $this->db_model->insert_update('virtual_balance',$data);
        $this->session->set_flashdata('success','Virtual Fund Credited Successfuly');
		redirect('master/virtual-balance');
	}
	
	public function api_switching(){
		if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator'] =$this->db_model->getwhere('operator');	
			$data['api'] = $this->db_model->getwhere('apisource');
		    $this->load->view('master/api-switching',$data);
		}
	}
	
	public function update_api_source()
	{
	    $id = $this->input->post('id');
	    $api = $this->input->post('api');		 
	    $time = date("Y-m-d h:i:s");		  
	    $this->db_model->insert_update('operator',array('apisource'=>$api,'api_update_time'=>$time),array('opid'=>$id));
	    echo "API Source Updated Successfully";
	}
	
	public function member_ledger(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where txn_fromto !='0' and txn_type In ('Direct Credit','Fund Transfer') and DATE(e.txn_date) >= '".DATE('Y-m-d')."' and DATE(e.txn_date) <= '".DATE('Y-m-d')."' ORDER BY e.txn_id DESC");
		    $this->load->view('master/member-ledger',$data);
		}
	}
	
	public function admin_ledger(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_fromto='0' and e.txn_type In ('Direct Credit','Pullout') and DATE(e.txn_date) >= '".DATE('Y-m-d')."' and DATE(e.txn_date) <= '".DATE('Y-m-d')."' ORDER BY e.txn_id DESC");
		    $this->load->view('master/admin-ledger',$data);
		}
	}
	
	public function aeps_ledger(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` as e join `customers` as c on e.aeps_txn_agentid = c.cus_id where DATE(e.aeps_txn_date) >= '".DATE('Y-m-d')."' and DATE(e.aeps_txn_date) <= '".DATE('Y-m-d')."' ORDER BY e.aeps_txn_id DESC");
		    $this->load->view('master/aeps-ledger',$data);
		}
	}
	
	public function banner(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['banner'] = $this->db_model->getWhere('banner');
		    $this->load->view('master/banner',$data);
		}
	}
	
	public function updatebanner($bid){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['banner'] = $this->db_model->getWhere('banner',array('bid'=>$bid));
		    $this->load->view('master/update-banner',$data);
		}
	}
	
	public function banner_update()
  	{
		$id = $this->input->post('id');		  		 		
		if($_FILES['userfile']['name']!='')
		{
			$path = "uploads";
			if(!is_dir($path))
			{
			    mkdir($path,0755,TRUE);
			}
		    $config['encrypt_name'] =TRUE;
			$config['upload_path'] =$path;
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size']	= '200000';
	        $config['overwrite']     = FALSE;
	        $this->upload->initialize($config);
			if($this->upload->do_upload())
			{
				$file_data = $this->upload->data();
				$image_path='https://profitpay.co.in/uploads/'.$file_data['file_name'];
				$value=array("image"=>$id);	
				$data = array('image'=>$image_path);
	            $r=$this->db_model->insert_update("banner",$data,array('bid'=>$id));
	            $this->session->set_flashdata('success','Banner Updated Successfully');
				redirect('master/banner');
			}
		}
   	}
   	
   	public function aeps_package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('package_name');
		$member_type = $this->input->post('member_type');
		$commission = $this->input->post('commission');
		$max_commission = $this->input->post('max_commission');
		
		$r = $this->db_model->insert_update('aeps_commission_slab', array('slab'=>$name, 'updated_time'=>$date, 'member_type'=>$member_type, 'percent'=>$commission, 'maximum_comm'=>$max_commission));
		$id = $this->db->insert_id();
		if($r)
		{
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('master/aeps-commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('master/aeps-commission-package');
		}
		
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
	
	public function update_recharge_status($id){
	    $total = array();
		$r = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$id));
		$op = $r[0]->operator;
		$opname = $this->db_model->getwhere('operator',array('opcodenew'=>$op),'operatorname');
		array_push($total,$r);
		array_push($total,$opname);
		$data['v'] = $total;
		$this->load->view('master/update-recharge-status',$data);
	}
	
	public function update_recharge_status_success()
	{
		$rid = $this->input->post('id');
		$status = $this->input->post('status');
		$transaction = $this->input->post('transaction');
		$api = $this->input->post('api');
		$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$rid));
		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$r[0]->apiclid));
		$opd = $this->db_model->getwhere('operator',array('opcodenew'=>$r[0]->operator));
		$dis_cha = $this->rec_model->commi(array('cus_id' => $r[0]->apiclid, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
		$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $r[0]->apiclid, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
		$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>($status == '1' ? 'SUCCESS' : 'FAILED'),'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$transaction));
		$com = $dis_cha['com']; 
		if($status == '1')
		{			
			if($r[0]->status == 'SUCCESS')
			{
				$recdata23 = array(
					'statusdesc'	=> $transaction,
					'serverresponse'	=>	$api
				);
				$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
				$this->session->set_flashdata('error','Already success');
				redirect('master/update-recharge-status/'.$rid);
			}
			else
			{
				$type12 = $cus[0]->cus_type;
				if($type12 == 'retailer')
				{
					$recdata23 = array(
						'status'	=> "SUCCESS",
						'responsecode'	=> "SUCCESS",
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					
					$row14 = $this->db_model->get_trnx($r[0]->apiclid);
					$recdata63 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=> $rid,
						'txn_opbal'	=>	$row14[0]->txn_clbal,
						'txn_crdt'	=>	'0',
						'txn_dbdt'	=>	$r[0]->amount,
						'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->amount, 3),
						'txn_type'	=>	'recharge',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'recharge'
					);
					$this->db_model->insert_update('exr_trnx',$recdata63);
						
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $rid));
							
							$recdata3 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Retailer Commission',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Retailer Commission'
							);
							$this->db_model->insert_update('exr_trnx',$recdata3);
						}
					}
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$recdata = $this->db_model->getAlldata("SELECT * FROM exr_rechrgreqexr_rechrgreq_fch WHERE apiclid = '".$r[0]->apiclid."' ORDER BY recid DESC LIMIT 1 ");
					$pl = round($recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master),3);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
							
				else if($type12 == 'distributor')
				{
					$recdata23 = array(
						'status'	=> "SUCCESS",
						'responsecode'	=> "SUCCESS",
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
						
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor' => $com), array('recid' => $rid));
							
							$recdata3 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Distributor Commission',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Distributor Commission'
							);
							$this->db_model->insert_update('exr_trnx',$recdata3);
						}
					}
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->member_commission_distributor($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
				    $recdata = $this->db_model->getAlldata("SELECT * FROM exr_rechrgreqexr_rechrgreq_fch WHERE apiclid = '".$r[0]->apiclid."' ORDER BY recid DESC LIMIT 1 ");
					$pl = round($recdata[0]->api - ($recdata[0]->distributor + $recdata[0]->master),3);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
				
							
				else if($type12 == 'master')
				{
					$recdata23 = array(
						'status'	=> "SUCCESS",
						'responsecode'	=> "SUCCESS",
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
						
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master' => $com), array('recid' => $rid));
							
							$recdata3 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Master Commission',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Master Commission'
							);
							$this->db_model->insert_update('exr_trnx',$recdata3);
						}
					}
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->member_commission_master($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$recdata = $this->db_model->getAlldata("SELECT * FROM exr_rechrgreqexr_rechrgreq_fch WHERE apiclid = '".$r[0]->apiclid."' ORDER BY recid DESC LIMIT 1 ");
					$pl = round($recdata[0]->api - ($recdata[0]->master),3);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
				
				if($type12 == 'api')
				{
					$recdata23 = array(
						'status'	=> "SUCCESS",
						'responsecode'	=> "SUCCESS",
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					
					$row14 = $this->db_model->get_trnx($r[0]->apiclid);
					$recdata63 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=> $rid,
						'txn_opbal'	=>	$row14[0]->txn_clbal,
						'txn_crdt'	=>	'0',
						'txn_dbdt'	=>	$r[0]->amount,
						'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->amount, 3),
						'txn_type'	=>	'recharge',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'recharge'
					);
					$this->db_model->insert_update('exr_trnx',$recdata63);
						
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
				

				
				else
				{
					$data = array(
						'status'=>'SUCCESS',
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$data,array('recid'=>$rid));
						$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
			}
		}
		
		else
		{
			if($r[0]->status == 'FAILED')
			{
				$this->session->set_flashdata('error','Already failed');
				redirect('master/update-recharge-status/'.$rid);
			}
			
			else if($r[0]->status == 'SUCCESS')
			{
				$type12 = $cus[0]->cus_type;
				
				if($type12 == 'retailer')
				{					
					$recdata23 = array(
						'status'	=> "REFUNDED",
						'profitloss' => '0'
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
					$recdata23 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=> $rid,
						'txn_opbal'	=>	$row18[0]->txn_clbal,
						'txn_crdt'	=>	$r[0]->amount,
						'txn_dbdt'	=>	'0',
						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
						'txn_type'	=>	'Balance Refunded',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'Balance Refunded'
					);
					$this->db_model->insert_update('exr_trnx',$recdata23);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $rid));
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$com,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
								'txn_type'	=>	'Retailer Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Retailer Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					else
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Surcharge Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Surcharge Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
				    $apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
				
				else if($type12 == 'distributor')
				{					
					$recdata23 = array(
						'status'	=> "REFUNDED",
						'profitloss' => '0'
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
					$recdata23 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=> $rid,
						'txn_opbal'	=>	$row18[0]->txn_clbal,
						'txn_crdt'	=>	$r[0]->amount,
						'txn_dbdt'	=>	'0',
						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
						'txn_type'	=>	'Balance Refunded',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'Balance Refunded'
					);
					$this->db_model->insert_update('exr_trnx',$recdata23);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor' => '0'), array('master' => '0'), array('recid' => $rid));
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$com,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
								'txn_type'	=>	'Distributor Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Distributor Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					else
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Surcharge Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Surcharge Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->distributor_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
				
				else if($type12 == 'master')
				{					
					$recdata23 = array(
						'status'	=> "REFUNDED",
						'profitloss' => '0'
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
					$recdata23 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=> $rid,
						'txn_opbal'	=>	$row18[0]->txn_clbal,
						'txn_crdt'	=>	$r[0]->amount,
						'txn_dbdt'	=>	'0',
						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
						'txn_type'	=>	'Balance Refunded',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'Balance Refunded'
					);
					$this->db_model->insert_update('exr_trnx',$recdata23);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master' => '0'), array('recid' => $rid));
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$com,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
								'txn_type'	=>	'Master Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Master Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					else
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Surcharge Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Surcharge Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
		//			$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('msg','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}
				
				else
				{
					$recdata23 = array(
						'status'	=> "REFUNDED",
						'profitloss' => '0'
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					$row18 = $this->db_model->get_trnx($r[0]->apiclid);

					$recdata23 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=> $rid,
						'txn_opbal'	=>	$row18[0]->txn_clbal,
						'txn_crdt'	=>	$r[0]->amount,
						'txn_dbdt'	=>	'0',
						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
						'txn_type'	=>	'Balance Refunded',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'Balance Refunded'
					);
					$this->db_model->insert_update('exr_trnx',$recdata23);

					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('distributor' => '0'), array('master' => '0'), array('recid' => $rid));
										
					if ($dis_cha['comtype'] == '2') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$com,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
								'txn_type'	=>	'Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					else
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	$com,
								'txn_dbdt'	=>	'0',
								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
								'txn_type'	=>	'Surcharge Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Surcharge Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
						}
					}
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);					
				}
			}
			else
			{
				$type12 = $cus[0]->cus_type;
				if($type12 == 'retailer' || $type12 == 'distributor' || $type12 == 'master')
				{
					$recdata23 = array(
						'status'	=> "REFUNDED",
						'profitloss' => '0'
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));					

					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
					$recdata23 = array(
							'txn_agentid'	=> $r[0]->apiclid,
							'txn_recrefid'	=> $rid,
							'txn_opbal'	=>	$row18[0]->txn_clbal,
							'txn_crdt'	=>	$r[0]->amount,
							'txn_dbdt'	=>	'0',
							'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
							'txn_type'	=>	'Balance Refunded',
							'txn_time'	=>	time(),
							'txn_date'	=>	date('Y-m-d h:i:s'),
							'txn_checktime'	=>	$r[0]->apiclid.time(),
							'txn_ip'	=>	$ip,
							'txn_comment'	=>	'Balance Refunded'
					);
					$this->db_model->insert_update('exr_trnx',$recdata23);
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);
				}	
				else
				{
					$data = array(
						'status'=>'REFUNDED',
						'profitloss' => '0'
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$data,array('recid'=>$rid));
					$trnx = $this->db->query("select * from exr_trnx where txn_agentid='".$r[0]->apiclid."' order by txn_id desc limit 1")->result();
					$clbal = $trnx[0]->txn_clbal;

					$recdata20 = array(
						'txn_agentid'	=> $r[0]->apiclid,
						'txn_recrefid'	=>	$rid,
						'txn_opbal'	=>	$trnx[0]->txn_clbal,
						'txn_crdt'	=>	$r[0]->amount,
						'txn_dbdt'	=>	'0',
						'txn_clbal'	=>	round($trnx[0]->txn_clbal + $r[0]->amount, 3),
						'txn_type'	=>	'Balance Refunded',
						'txn_time'	=>	time(),
						'txn_date'	=>	date('Y-m-d h:i:s'),
						'txn_checktime'	=>	$r[0]->apiclid.time(),
						'txn_ip'	=>	$ip,
						'txn_comment'	=>	'Balance Refunded'
					);
					$this->db_model->insert_update('exr_trnx',$recdata20);
					$this->session->set_flashdata('success','Update Successfully');
					redirect('master/update-recharge-status/'.$rid);					
				}
			}
		}
	}
	
	public function news(){
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $data['news'] = $this->db_model->getAlldata("SELECT * FROM news ORDER BY news_id DESC");
		    $this->load->view('master/news',$data);
		}
	}
	
	public function delete_news($id)
	{
		
		$this->db->delete('news', array('news_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','News Deleted Successfully');
		redirect('master/news');
	
	}
	
	public function add_news()
	{
		$desc = $this->input->post('desc');	
		$status = $this->input->post('status');	
		$type = $this->input->post('newstype');
		$date = DATE('Y-m-d h:i:s');

		$newstype = implode(',', $type);

		$r = $this->db_model->insert_update('news',array('news_desc'=>$desc,'news_type'=>$newstype,'news_date'=>$date,'news_status'=>$status));
		$this->session->set_flashdata('success','News Added Successfully');
		redirect('master/news');
	}


    public function aeps_users(){
        
        if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
        }else{
		    $data['aepsUser'] = $this->db_model->getAlldata("SELECT * FROM customers where aeps_AccountNumber!='' And cus_reffer = '".$this->session->userdata('mas_id')."' ORDER BY cus_id DESC");
		    //$data['package'] = $this->db_model->getAlldata("SELECT * FROM aeps_commission_slab");
            $this->load->view('master/aeps_users',$data);
        }
    }

    public function aeps_transaction(){
        
  	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
        }else{
		    $data['aepsUser'] = $this->db_model->getAlldata("SELECT e.aeps_id , e.apiclid , e.transaction_ref_id , e.amount , e.transactionType ,e.status , e.dmt_dispute_status ,e.master_commission ,e.distributor_commission ,e.aeps_date_time ,e.aadhar_number ,e.utr ,e.device ,ab.bankName ,c.cus_name FROM aeps_transaction_fch as e join customers as c on e.apiclid = c.cus_id join aeps_bank as ab on ab.iinno = e.aeps_bank_id  where e.apiclid IN (SELECT cus_id From customers where cus_type='distributor' and cus_reffer='".$this->session->userdata('mas_id')."') ORDER BY e.aeps_id DESC");
            $this->load->view('master/aeps_reports',$data);
        }
  	}
  	
  	public function aepsPackage_assign(){
  	    
  	    $pk_id = $this->input->post('pk_id');
  	    $cus_id = $this->input->post('cus_id');
  	    $data = array('aeps_comm_id'=>$pk_id);
    	$res = $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
    	if($res){
    	    $this->session->set_flashdata('success','Updated Successfully');
    	    redirect('master/aeps_users');
    	}else{
    	    $this->session->set_flashdata('error','Failed To  Update');
    	    redirect('master/aeps_users');
    	}
  	    
  	}
  	
  	public function pancoupon_history(){
  	    
  	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
        }else{
		    $data['coupons'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,customers as c,coupon_price as cp where cb.vle_id = c.cus_id and cb.type = cp.coupon_price_id and c.cus_id = '".$this->session->userdata('mas_id')."'   ORDER BY coupon_buy_id DESC");
            $this->load->view('master/pancoupon_history',$data);
        }
  	}
  	
  	public function micro_atm(){
  	    
  	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
        }else{
		    $data['micro'] = $this->db_model->getAlldata("SELECT * FROM micro_atm as ma , customers as c where ma.cus_id = c.cus_id and c.cus_id = '".$this->session->userdata('mas_id')."' ORDER BY ma_id DESC");
            $this->load->view('master/microAtm_report',$data);
        }
  	}


    public function coupon_price(){
        
        if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
        }else{
            $data['coupon'] = $this->db_model->getAlldata("SELECT * FROM coupon_price");
            $this->load->view('master/coupon_price',$data);
        }
    }

    public function changeCoupon_price(){
        
        $id = $this->input->post('id');
        $price = $this->input->post('price');
        $dist_commision = $this->input->post('dist_commision');
        $master_commision = $this->input->post('master_commision');
        $data = array('coupon_price'=>$price,'dist_commision'=>$dist_commision,'master_commision'=>$master_commision);
    	$res = $this->db_model->insert_update('coupon_price',$data,array("coupon_price_id"=>$id));
    	if($res){
    	    $this->session->set_flashdata('success','Updated Successfully');
    	    redirect('master/coupon_price');
    	}else{
    	    $this->session->set_flashdata('error','Failed To  Update');
    	    redirect('master/coupon_price');
    	}
        
    }
    
    public function bank_details()
	{
	    if($this->session->userdata('isLoginMaster') == FALSE){
            redirect('users_login');
		}else{
		    $mas_id = $this->session->userdata('mas_id');
		    $data['r'] = $this->db_model->getwhere('bank_details',array('cus_id'=>$mas_id));
		    //echo $this->db->last_query();exit;
		    $this->load->view('master/bank-details',$data);
		}
	}
	
	public function add_bank_details()
	{
		$this->form_validation->set_rules('holder','Holder','required|min_length[4]|max_length[30]');
		$this->form_validation->set_rules('bank','Bank','required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('account','Account','required|min_length[6]|max_length[18]');
		$this->form_validation->set_rules('branch','Branch','required');
		$this->form_validation->set_rules('ifsc','ifsc','required');
		if($this->form_validation->run() == TRUE)
		{
			$r = $this->db_model->getwhere('bank_details',array('account_id'=>$this->input->post('account')));
			if(!$r)
			{
				$data = array(
					'bank_account'	=>	$this->input->post('holder'), 
					'bank_name'  =>	$this->input->post('bank'), 
					'account_id'  =>	$this->input->post('account'), 
					'branch_name'  =>	$this->input->post('branch'), 
					'ifsc_code'  =>	$this->input->post('ifsc'), 
					'account_type'  =>	'1', 
					'cus_id'  =>  $this->session->userdata('mas_id'),
					'added_date'	=>	DATE('Y-m-d h:i:s')
				);
				$this->db_model->insert_update('bank_details', $data);
				$this->session->set_flashdata('success','Successfully Inserted');
			}		
			else
			{
				$this->session->set_flashdata('error','Enter Bank Details Already Exist');
			}
		}
		else
		{
			$this->session->set_flashdata('error','Fill all mandatory filled');
		}
		redirect('master/bank-details');	
	}
	
	public function delete_bank_details($id)
	{
		$this->db_model->delete('bank_details',array('bank_id'=>$id));
		$this->session->set_flashdata('success','Successfully deleted..');
		redirect('master/bank-details');
	}
  	
  	public function update_bank_details($id)
	{
		$data['bank'] = $this->db_model->get_newdata('bank_details',array('account_type'=>'1','bank_id'=>$id,'cus_id'=> $this->session->userdata('mas_id')));
		if(!empty($data['bank']))
		{
			$this->load->view('master/update-bank-details',$data);
		}
		else
		{
			redirect('master/bank-details');
		}		
	}
	
	public function bank_details_update()
	{
		$this->form_validation->set_rules('holder','Holder','required|min_length[4]|max_length[30]');
		$this->form_validation->set_rules('bank','Bank','required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('account','Account','required|min_length[6]|max_length[18]');
		$this->form_validation->set_rules('branch','Branch','required');
		$this->form_validation->set_rules('ifsc','ifsc','required');
		$this->form_validation->set_rules('id','Id','required');
		if($this->form_validation->run() == TRUE)
		{
			$r = $this->db_model->get_newdata('bank_details',array('account_type'=>'1','cus_id'=> $this->session->userdata('mas_id'),'bank_id'=>$this->input->post('id')));
			if($r)
			{
				$data = array(
					'bank_account'	=>	$this->input->post('holder'), 
					'bank_name'  =>	$this->input->post('bank'), 
					'account_id'  =>	$this->input->post('account'), 
					'branch_name'  =>	$this->input->post('branch'), 
					'ifsc_code'  =>	$this->input->post('ifsc'),
					'account_status'	=>	'1'
				);
				$this->db_model->insert_update('bank_details', $data,array('bank_id'=>$this->input->post('id')));
				$this->session->set_flashdata('success','Successfully Updated');
			}		
			else
			{
				$this->session->set_flashdata('error','Already Exist');
			}
		}
		else
		{
			$this->session->set_flashdata('error','Fill all mandatory filled');
		}
		redirect('master/bank-details');	
	}


	public function logout()
	{
        $this->session->unset_userdata(array('mas_id','mas_name','mas_type','mas_email','mas_mobile','isLoginMaster','senderMobile'));
		redirect('users_login');
  	}
  	
  	
    public function verify_bank(){
        
        
        $bankcode = 'SBIN'; //$this->input->post('bank_code');
        $mobile = '7030512346'; //$this->input->post('mob_no');
        $adhar = '558183301991'; //$this->input->post('ifsc_code');
        $pan = 'COQPP7375K';
        $bank_acct = '33220731560'; //$this->input->post('bank_acct');


        $token = '9e37f87e69694e2886';
        $id = rand(1000000000,9999999999);
        
        
        //$data = "E06031~E1hgYt898557843789~Abc589654135~7699999821~4361256325156~SBIN~526489 754125~ CDER546859~NA~NA";
        //$data ="E06031~$token~$id~7030512346~777705123467~$bankcode~631106684669~EOPPP2051K~NA~NA";
         $data = "msg=E06031~$token~$id~$mobile~$bank_acct~$bankcode~558183301991~COQPP7375K~NA~NA";
        //$data = "msg=E06031~$token~$id~7030512346~777705123467~$bankcode~631106684669~EOPPP2051K~NA~NA";
        
        $url= 'https://ezymoney.myezypay.in/RemitMoney/mtransfer';
        
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
        //print_r($info);
        //print_r($server_output);
        $http_code = $info['http_code'];
        echo $server_output;
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            //print_r($dataArr);
        }else{
            $this->session->set_flashdata('error','Unable To get Response');
            redirect('Retailer/addbeneficary');
        }
    }
  	
  	/*public function service(){
  	    
  	    $data['service'] = $this->db_model->getAllData("SELECT * FROM service");
  	    $this->load->view('master/service_down',$data);
  	}
  	
  	public function downService(){
  	    
  	      $ser_id = $this->input->post('id');
  	      $status = $this->input->post('status');
  	      if($status == 'active'){
  	          $data =array(
  	              'status' => 'inactive'
  	              );
  	      }else{
  	          $data =array(
  	              'status' => 'active'
  	              );
  	      }
    	  $res = $this->db_model->insert_update('service',$data,array("service_id"=>$ser_id));
    	  echo $res;
  	    
  	}*/
  	
  	
    /*
    public function money_transfer_succ(){
        
        $adhar_no = $this->input->post('adhar_no');
        $pan_no = $this->input->post('pan_no');
        $amt = $this->input->post('amt');
        $benificary_code = $this->input->post('b_code');
        $trans_type = "58";
        $token = 'e1626d22b42148758d';
        $id = rand(1000000000,9999999999);
        
        $data = "msg=E06015~$token~$id~$mobile~$benificary_code~$amt~$trans_type~$adhar_no~$pan_no~NA~NA~NA";
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
        //print_r($server_output);
        $http_code = $info['http_code'];
        echo $server_output;exit;
        
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            echo "success";
        }else{
            $this->session->set_flashdata('error','Unable To get Response');
            redirect('master/addbeneficery');
        }
    }*/
    
    
    public function commission_scheme()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `commission_scheme` ORDER BY scheme_id DESC");
		    $this->load->view('admin/commission-scheme',$data);
		}
	}
	
	
	public function getOperator(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>OPERATOR NAME</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_recharge` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_recharge_id.'</td>';
	  		$tab.='<td>'.$v->slab.'</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=change(this.value,"type","'.$v->commission_scheme_recharge_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=change(this.value,"type","'.$v->commission_scheme_recharge_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->api_comm.' onchange=change(this.value,"api","'.$v->commission_scheme_recharge_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->master_comm.' onchange=change(this.value,"mas","'.$v->commission_scheme_recharge_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->distributor_comm.' onchange=change(this.value,"dis","'.$v->commission_scheme_recharge_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=change(this.value,"ret","'.$v->commission_scheme_recharge_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
		
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>OPERATOR NAME</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	public function getAepsComm(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>SLAB</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_aeps` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_aeps_id.'</td>';
	  		$tab.='<td>'.$v->slab.'('.$v->amount_min_range.'-'.$v->amount_max_range.')</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=changeAepsComm(this.value,"type","'.$v->commission_scheme_aeps_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=changeAepsComm(this.value,"type","'.$v->commission_scheme_aeps_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->api_comm.' onchange=changeAepsComm(this.value,"api","'.$v->commission_scheme_aeps_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->master_comm.' onchange=changeAepsComm(this.value,"mas","'.$v->commission_scheme_aeps_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->distributor_comm.' onchange=changeAepsComm(this.value,"dis","'.$v->commission_scheme_aeps_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=changeAepsComm(this.value,"ret","'.$v->commission_scheme_aeps_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
		
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	
	public function getDmtComm(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_dmt` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_dmt_id.'</td>';
	  		$tab.='<td>'.$v->slab.'</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=changeDmtComm(this.value,"type","'.$v->commission_scheme_dmt_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=changeDmtComm(this.value,"type","'.$v->commission_scheme_dmt_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->api_comm.' onchange=changeDmtComm(this.value,"api","'.$v->commission_scheme_dmt_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->master_comm.' onchange=changeDmtComm(this.value,"mas","'.$v->commission_scheme_dmt_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->distributor_comm.' onchange=changeDmtComm(this.value,"dis","'.$v->commission_scheme_dmt_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=changeDmtComm(this.value,"ret","'.$v->commission_scheme_dmt_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
		
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	
	
	public function getPancardComm(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_pancard` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_pancard_id.'</td>';
	  		$tab.='<td>'.$v->commission_scheme_pancard_id.'</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=changePancardComm(this.value,"type","'.$v->commission_scheme_pancard_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=changePancardComm(this.value,"type","'.$v->commission_scheme_pancard_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->api_comm.' onchange=changePancardComm(this.value,"api","'.$v->commission_scheme_pancard_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->master_comm.' onchange=changePancardComm(this.value,"mas","'.$v->commission_scheme_pancard_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->distributor_comm.' onchange=changePancardComm(this.value,"dis","'.$v->commission_scheme_pancard_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=changePancardComm(this.value,"ret","'.$v->commission_scheme_pancard_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
		
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	
	public function getMicroATMComm(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_microatm` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_microatm_id.'</td>';
	  		$tab.='<td>'.$v->slab.'('.$v->amount_min_range.'-'.$v->amount_max_range.')</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=changeMicroAtmComm(this.value,"type","'.$v->commission_scheme_microatm_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=changeMicroAtmComm(this.value,"type","'.$v->commission_scheme_microatm_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->api_comm.' onchange=changeMicroAtmComm(this.value,"api","'.$v->commission_scheme_microatm_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->master_comm.' onchange=changeMicroAtmComm(this.value,"mas","'.$v->commission_scheme_microatm_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->distributor_comm.' onchange=changeMicroAtmComm(this.value,"dis","'.$v->commission_scheme_microatm_id.'")></td>';
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=changeMicroAtmComm(this.value,"ret","'.$v->commission_scheme_microatm_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
		
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>API CLIENT</th><th>MASTER</th><th>DISTRIBUTOR</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	
	public function getAdharPayComm(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>TYPE</th><th>RETAILER</th></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_adharpay` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_adharpay_id.'</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=changeAdharPayComm(this.value,"type","'.$v->commission_scheme_adharpay_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=changeAdharPayComm(this.value,"type","'.$v->commission_scheme_adharpay_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=changeAdharPayComm(this.value,"ret","'.$v->commission_scheme_adharpay_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
		
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>TYPE</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	public function getPayoutComm(){
	    
        $op = $this->input->post('sid');
        $tab ='';
        $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
        $tab.='<thead><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>RETAILER</th></thead><tbody>';
               
        $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_payout` where scheme_id ='".$op."' ");	
	  	foreach($r as $v)
	  	{
	  		$tab.='<tr>';
	  		$tab.='<td>'.$v->commission_scheme_payout_id.'</td>';
	  		$tab.='<td>'.$v->slab.'('.$v->amount_min_range.'-'.$v->amount_max_range.')</td>';
	  		if($v->type == 'flat'){
	  		    $tab.='<td><select onchange=changePayoutComm(this.value,"type","'.$v->commission_scheme_payout_id.'")><option value="flat" selected>Flat</option><option value="percent">Percent</option></td>';
	  		}else{
	  		    $tab.='<td><select onchange=changePayoutComm(this.value,"type","'.$v->commission_scheme_payout_id.'")><option value="flat">Flat</option><option value="percent" selected>Percent</option></td>';
	  		}
	  		$tab.='<td><input type="text" style="width:100%" name="op" value='.$v->retailer_comm.' onchange=changePayoutComm(this.value,"ret","'.$v->commission_scheme_payout_id.'")></td>';
	  		
	  		$tab.='</tr>';
	  		
		}
	
		$tab.='</tbody><tfoot><tr><th>#ID</th><th>Slab</th><th>TYPE</th><th>RETAILER</th></tr></tfoot></table>';
		echo $tab;
    		
	}
	
	public function recharge_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_recharge',$data,array("commission_scheme_recharge_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_recharge',$data,array("commission_scheme_recharge_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_recharge',$data,array("commission_scheme_recharge_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_recharge',$data,array("commission_scheme_recharge_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_recharge',$data,array("commission_scheme_recharge_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	
	public function dmt_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_dmt',$data,array("commission_scheme_dmt_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_dmt',$data,array("commission_scheme_dmt_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_dmt',$data,array("commission_scheme_dmt_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_dmt',$data,array("commission_scheme_dmt_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_dmt',$data,array("commission_scheme_dmt_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	
	public function aeps_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_aeps',$data,array("commission_scheme_aeps_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_aeps',$data,array("commission_scheme_aeps_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_aeps',$data,array("commission_scheme_aeps_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_aeps',$data,array("commission_scheme_aeps_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_aeps',$data,array("commission_scheme_aeps_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	
	public function pancard_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_pancard',$data,array("commission_scheme_pancard_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_pancard',$data,array("commission_scheme_pancard_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_pancard',$data,array("commission_scheme_pancard_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_pancard',$data,array("commission_scheme_pancard_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_pancard',$data,array("commission_scheme_pancard_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	public function microatm_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_microatm',$data,array("commission_scheme_microatm_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_microatm',$data,array("commission_scheme_microatm_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_microatm',$data,array("commission_scheme_microatm_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_microatm',$data,array("commission_scheme_microatm_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_microatm',$data,array("commission_scheme_microatm_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	
	
	public function adharpay_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_adharpay',$data,array("commission_scheme_adharpay_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_adharpay',$data,array("commission_scheme_adharpay_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_adharpay',$data,array("commission_scheme_adharpay_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_adharpay',$data,array("commission_scheme_adharpay_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_adharpay',$data,array("commission_scheme_adharpay_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	
	public function payout_commission_update(){
	    
        $new_comm = $this->input->post('new_comm');
        $type = $this->input->post('type');
        $comm_id = $this->input->post('comm_id');
        
        if($type == 'type'){
            $data =array('type'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_payout',$data,array("commission_scheme_payout_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'api'){
            $data =array('api_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_payout',$data,array("commission_scheme_payout_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'dis'){
            $data =array('distributor_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_payout',$data,array("commission_scheme_payout_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'mas'){
            $data =array('master_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_payout',$data,array("commission_scheme_payout_id"=>$comm_id));
    	    echo $res;
        }elseif($type == 'ret'){
            $data =array('retailer_comm'=>$new_comm);    
          	$res = $this->db_model->insert_update('commission_scheme_payout',$data,array("commission_scheme_payout_id"=>$comm_id));
    	    echo $res;
        }
        
	    
	}
	
	
	public function updateStatus(){
	    
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        if($status == 'active'){
        
            $data =array('scheme_status'=>'inactive');    
          	$res = $this->db_model->insert_update('commission_scheme',$data,array("scheme_id"=>$id));
          	echo $res;
        }else{
            
            $data =array('scheme_status'=>'active');    
          	$res = $this->db_model->insert_update('commission_scheme',$data,array("scheme_id"=>$id));
          	echo $this->db->last_query();
            
        }  	
	}
	
	
	
	
}
