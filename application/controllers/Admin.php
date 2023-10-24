<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Admin extends CI_Controller {
    
    private $api_mobile = '7015871002';
    private $api_pass = '12135';
    private $api_user_id = '563';
    private $api_reg_ip = '162.215.209.105';
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model(array('db_model','encryption_model','rec_model','email_model','msg_model','Icici_model','aeps_model','Appapi_model','Pushnotification_model'));
		$this->load->library(array('upload'));
    }
    
    
    public function tert(){
        $amount5 = '200';
        
	    $val = $amount5 % 2;
	    if($val=='0') $amount5 = $amount5;else $amount5 = $amount5 + 1;
	    echo $amount5;
    }
   
	public function index()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		   	$data['data'] = $this->db_model->adminDashboard();
		    $this->load->view('admin/dashboard',$data);
		}
	}
	
	public function fund_requests()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `fund_request` as e join customers as c on e.cus_id = c.cus_id   WHERE date(req_date)=CURDATE() ORDER BY e.req_id DESC");
		    //echo $this->db->last_query();exit;
		    $this->load->view('admin/fund-requests',$data);
		}
	}
	
	public function fund_request_accept($reqid)
	{
		$cdts = $this->db_model->get_newdata('fund_request',array('req_id'=>$reqid));
		$cus_id = $cdts[0]->pay_to;
		$txnid = '0';
		
		$cups1 = $this->db_model->getAlldata("select cus_mobile from customers where cus_id='$cus_id'");
		$msg_number = $cups1[0]->cus_mobile;
		

		$txn_crdt = $cdts[0]->pay_amount;
		$txntype = 'Direct Credit';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
        
		$vir_sql = "SELECT * FROM `virtual_balance` order by vb_id desc limit 1";
		$admin_virtual = $this->db_model->getAlldata($vir_sql);
		$vir_bal = $admin_virtual[0]->vb_clbal;
		
		if($vir_bal >= $txn_crdt){
    		$sql = "select * from exr_trnx where txn_agentid='".$cdts[0]->cus_id."' order by txn_id desc limit 1";
    		$r = $this->db_model->getAlldata($sql);
    
    		if($r)
    		{
    			$closing = $r['0']->txn_clbal;
    			$total = $r['0']->txn_clbal + $txn_crdt;
    			$txn_dbdt = 0;
    			$message = "Dear member, your account has successfully credited Rs.".$txn_crdt.". Your closing balance is Rs.".$total.". ".$this->config->item('title');
    			$lasttxnrec= $this->db_model->insert('exr_trnx',array('txn_agentid'=>$cdts[0]->cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>round($total, 3),'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_time'=>$time,'txn_date'=>DATE('Y-m-d h:i:s'),'txn_ip'=>$ip));
    			$txn = $this->db->insert_id();
    			if($lasttxnrec)
    			{
    				$data = array(
    					'req_status'	=>	'ACCEPTED'
    				);				
    				$this->db_model->insert_update('fund_request', $data,array('req_id'=>$reqid));
    				$msg2 = "<span style='color:green;font-size:18px;font-weight:bold;margin-left:20px;'>Credited Successfully</span>";
    				$this->session->set_flashdata('success',$msg2);
    			}
    			else
    			{
    				$msg2 = "<span style='color:red;font-size:18px;font-weight:bold;margin-left:20px;'>Failed try again later</span>";
    				$this->session->set_flashdata('error',$msg2);
    			}
    		}
    		else
    		{
    			$closing = 0;
    			$total = $txn_crdt;
    			$txn_dbdt = 0;
    			$lasttxnrec= $this->db_model->insert('exr_trnx',array('txn_agentid'=>$cdts[0]->cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_date'=>DATE('Y-m-d h:i:s'),'txn_clbal'=>round($total, 3),'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
    			$txn = $this->db->insert_id();
    			if($lasttxnrec)
    			{
    				$data = array(
    					'req_status'	=>	'ACCEPTED'
    				);				
    				$this->db_model->insert_update('fund_request', $data,array('req_id'=>$reqid));
    				$msg2 = "<span style='color:green;font-size:18px;font-weight:bold;margin-left:20px;'>Credited Successfully</span>";
    				$this->session->set_flashdata('success',$msg2);
    			}
    			else
    			{
    				$msg2 = "<span style='color:red;font-size:18px;font-weight:bold;margin-left:20px;'>Failed try again later</span>";
    				$this->session->set_flashdata('error',$msg2);
    			}
    		}
    
    		$sql156 = "select * from virtual_balance where 1 order by vb_id desc limit 1";
    		$r = $this->db_model->getAlldata($sql156);
    		if($r)
    		{
    			$total = $r['0']->vb_clbal - $txn_crdt;
    			$txn_dbdt = $txn_crdt;
    			$data = array(
    				'txn_id'	=>	$txn,
    				'vb_agentid'	=>	$cus_id,
    				'vb_recrefid'	=>	0,
    				'vb_opbal'	=>	$r['0']->vb_clbal,
    				'vb_crdt'	=>	0,
    				'vb_dbdt'	=>	$txn_dbdt,
    				'vb_clbal'	=>	$total,
    				'vb_checktime'	=>	time(),
    				'vb_type'	=>	$txntype,
    				'vb_fromto'	=>	'',
    				'vb_time'	=>	time(),
    				'vb_date'	=>	date('Y-m-d h:i:s'),
    				'vb_ip'	=>	$ip,
    				'vb_comment'	=>	'',
    				'transaction_type'	=>	'',
    				'bank_name'	=>	'',
    				'transaction_ref'	=>	'',
    				'paid_due'	=>	'',
    				'transaction_date'	=>	DATE('Y-m-d'),
    				'status'	=>	1
    			);
    			$this->db_model->insert_update('virtual_balance',$data);
    		}else{
    		    $msg2 = "<span style='color:red;font-size:18px;font-weight:bold;margin-left:20px;'>Failed try again later</span>";
    			$this->session->set_flashdata('error',$msg2);
    		}
    			
		}else{
		    
    		    $msg2 = "<span style='color:red;font-size:18px;font-weight:bold;margin-left:20px;'>Low Virtual Balance</span>";
    			$this->session->set_flashdata('error',$msg2);
		}
	
		redirect('admin/fund-requests');	
	}
	
	public function updateaepsbank(){
  	    
  	    $BankName = $this->input->post('BankName');
  	    $bankIfscCode = $this->input->post('bankIfscCode');
  	    $cus_id = $this->input->post('cus_id');
  	    $BankAccountNumber = $this->input->post('BankAccountNumber');
  	    $data = array('bankName'=>$BankName,'aeps_bankIfscCode'=>$bankIfscCode,'aeps_AccountNumber'=>$BankAccountNumber,);
  	    $res = $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
    	if($res){
    	    $this->session->set_flashdata('success','Updated Successfully');
    	    redirect('admin/aeps_users');
    	}else{
    	    $this->session->set_flashdata('error','Failed To  Update');
    	    redirect('admin/aeps_users');
    	}
  	    
  	}
  	
	public function deletekyc(){
  	    $cus_id=$this->uri->segment(3);
  	    $data = array('aeps_kyc_status'=>'KYC Not Completed');
  	    $res = $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
    	if($res){
    	    $this->session->set_flashdata('success','KYC Deleted Successfully');
    	    redirect('admin/aeps_users');
    	}else{
    	    $this->session->set_flashdata('error','Failed To  Delete');
    	    redirect('admin/aeps_users');
    	}
  	    
  	}
  	
	public function fund_request_reject()
	{
	    $pay_req_id=$this->input->post('id');
	    $cus_id = $this->input->post('cusid');
	    $reason=$this->input->post('reason');
	    $resdata=array('req_status'=>'REJECTED','reason'=>$reason);
    	$updata=array('req_id'=>$pay_req_id);
    	$res=$this->db_model->insert_update('fund_request',$resdata,$updata);
    	$msg2 = "<span style='color:green;font-size:18px;font-weight:bold;margin-left:20px;'>Request Rejected Successfully</span>";
		$this->session->set_flashdata('success',$msg2);
		redirect('admin/fund-requests');
	}
	public function search_fund_request()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
            
		}else{
		    
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
		    $sql = "SELECT * FROM `fund_request` as e join customers as c on e.cus_id = c.cus_id join bank_details as b on b.bank_id = e.pay_bank  WHERE " ;
		    
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.req_date) >= '".$from.' 00:00:00'."' and DATE(e.req_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    
    		    if($from !=''){
    		        $sql .=" DATE(e.req_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.req_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " e.req_id != '' ORDER BY e.req_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/fund-requests',$data);
		}
	}
	
	public function successAmt(){
	    return $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' AND date(reqdate)=CURDATE() ");
	}
	
	public function failedAmt(){
	    return $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' AND date(reqdate)=CURDATE() ");
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
	        $r =$this->db_model->getAlldata("SELECT * FROM customers WHERE  customers.cus_type='$cus_type'");
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
	    $cus_type = $_POST['cus_type'];
	    $data = $this->db_model->getwhere('exr_package',array('package_membertype'=>$cus_type));
        echo '<option value="">Select Package</option>';	
	  	$v=null; foreach($data as $i=>$v)
	  	{
	  		echo '<option value='.$v->package_id.'>'.ucfirst($v->package_name).'</option>';
		}
	}
	
	public function getUplineMembers(){
	    $cus_type = $_POST['cus_type'];
	    
	    if($cus_type == "distributor"){
	        $data = $this->db_model->getwhere('customers',array("cus_type"=>"master","avability_status"=>"0"));
	        if($data){
    	        echo '<div class="form-group"><label class="form-label">Select Master:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" onchange="getMemberBalance(this)" required><option value="0">Choose Master</option>';
    	        $v=null; 
    	        foreach($data as $i=>$v){
    	            echo '<option value='.$v->cus_id.'>'.ucfirst($v->cus_name).'</option>';
    	        }
    	        echo '</select></div></div>';
	        }
	    }else if($cus_type == "retailer"){
	        $data = $this->db_model->getwhere('customers',array("cus_type"=>"distributor","avability_status"=>"0"));
	        if($data){
    	        echo '<div class="form-group"><label class="form-label">Select Distributor:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" onchange="getMemberBalance(this)" required><option value="0">Choose Distributor</option>';
    	        $v=null; 
    	        foreach($data as $i=>$v){
    	            echo '<option value='.$v->cus_id.'>'.ucfirst($v->cus_name).'</option>';
    	        }
    	        echo '</select></div></div>';
	        }
	    }else if($cus_type == "api"){
	        $data = $this->db_model->getwhere('customers',array("cus_type"=>"distributor","avability_status"=>"0"));
	        if($data){
    	        echo '<div class="form-group"><label class="form-label">Select Distributor:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" onchange="getMemberBalance(this)" required><option value="0">Choose Distributor</option>';
    	        $v=null; 
    	        foreach($data as $i=>$v){
    	            echo '<option value='.$v->cus_id.'>'.ucfirst($v->cus_name).'</option>';
    	        }
    	        echo '</select></div></div>';
	        }
	    }
	}
	
	public function search_recharge_report(){
	    
	    
	    
	    $frm = $_POST['from_dt'];
	    $to = $_POST['to_dt'];
		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
	    
	    $operator_type = $_POST['operator_type'];
	    $operator = $_POST['operator'];
	    $apisource = $_POST['apisource'];
	    $member_type = $_POST['member_type'];
	    $member_id = $_POST['member_id'];
	    $status = $_POST['status'];
	    $mobile_number = $_POST['mobile_number'];
	    $rec_id = $_POST['rec_id'];
	    
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

		if($from !='' && $to !='')
		{
			$sql .="DATE(e.reqdate) >= '".$from.' 00:00:00'."' and DATE(e.reqdate) <= '".$to.' 00:00:00'."' and ";
			
		}else{
		    
		    if($from !=''){
		        $sql .=" DATE(e.reqdate) = '".$from.' 00:00:00'."'  and " ;
		    }else if($to !=''){
		        $sql .=" DATE(e.reqdate) = '".$to.' 00:00:00'."' and ";
		    }
		}
		
		/*if($date !='')
		{
			$sql .="DATE(e.reqdate) ='".$date."' and ";
		}*/

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
        $this->load->view('admin/view-recharge-report',$data);
	    
	}
	
	public function recharge_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('admin/view-recharge-report',$data);
		}
	}
	
	public function all_recharge_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew  ORDER BY e.recid DESC");
		    $this->load->view('admin/view-recharge-report',$data);
		}
	}
	
	public function failed_recharge_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE status='FAILED' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('admin/view-recharge-report',$data);
		}
	}
	
	public function success_recharge_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE status='SUCCESS' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('admin/view-recharge-report',$data);
		}
	}
	
	public function transaction_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where DATE(e.txn_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.txn_date) <= '".date('Y-m-d').' 00:00:00'."' ORDER BY e.txn_id DESC");
		    $this->load->view('admin/transaction-report',$data);
		}
	}
	
	public function search_transaction_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.txn_date) >= '".$from.' 00:00:00'."' and DATE(e.txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(e.txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " e.txn_id != '' ORDER BY e.txn_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/transaction-report',$data);
		}
	}
	
	public function dmt_transaction_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `dmt_trnx` as e join customers as c on e.cus_id = c.cus_id JOIN exr_trnx ON e.dmt_trnx_id = exr_trnx.dmt_txn_recrefid WHERE DATE(e.dmt_trnx_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.dmt_trnx_date) <= '".date('Y-m-d').' 00:00:00'."' ORDER BY e.dmt_trnx_id DESC");
		    $this->load->view('admin/dmt-transaction-report',$data);
		}
	}
	
	
	public function search_dmt_transaction_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
            
		}else{
		    
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
		    $sql = "SELECT * FROM `dmt_trnx` as e join customers as c on e.cus_id = c.cus_id JOIN exr_trnx ON e.dmt_trnx_id = exr_trnx.dmt_txn_recrefid where " ;
		    
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.dmt_trnx_date) >= '".$from.' 00:00:00'."' and DATE(e.dmt_trnx_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(e.dmt_trnx_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.dmt_trnx_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " e.dmt_trnx_id != '' ORDER BY e.dmt_trnx_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/dmt-transaction-report',$data);
		}
	}
	
	
	
	public function redeem_request_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    //$data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `payout_request` as e join customers as c on e.cus_id = c.cus_id WHERE status='PENDING'");
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `payout_request`  WHERE status='PENDING' order by pay_req_id desc");
		    $this->load->view('admin/redeem-request-report',$data);
		}
	}
	
	public function redeem_reject()
	{
	    $pay_req_id=$this->input->post('id');
	    $reason=$this->input->post('reason');
	    $resdata=array('status'=>'REJECTED');
    	$updata=array('pay_req_id'=>$pay_req_id);
    	$res=$this->db_model->insert_update('payout_request',$resdata,$updata);
    	if($res)
		{
		    $ope = $this->db_model->getWhere('payout_request',array('pay_req_id'=>$pay_req_id));
		    foreach($ope as $operator){
    			    $cus_id=$operator->cus_id;
    			    $amount=$operator->amount;
    			    $charge=$operator->charge;
    			    $cusdetails=$this->db_model->getAlldata("SELECT * FROM aeps_exr_trnx WHERE aeps_txn_agentid= $cus_id ORDER BY aeps_txn_id DESC LIMIT 1");
    			    $aeps_txn_clbal=$cusdetails[0]->aeps_txn_clbal;
    			    $newclosingbal=$aeps_txn_clbal+$amount+$charge;
    			    
    			    $rdata=array('aeps_txn_opbal'=>$aeps_txn_clbal,'aeps_txn_crdt'=>($amount+$charge),'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$newclosingbal,'aeps_txn_type'=>'Redeem Rejected','aeps_txn_comment'=>$reason);
                	$tt=$this->db_model->insert_update('aeps_exr_trnx',$rdata);
    			}
		    $this->session->set_flashdata('success','Reject Successfully');
		    redirect('admin/redeem-request-report');
		}
		else{
		    $this->session->set_flashdata('error','Something Went Wrong');
		    redirect('admin/redeem-request-report');
		}
	}
	
	
	public function redeem_success()
	{
	    $pay_req_id=$this->input->post('id');
	    $utr_ref=$this->input->post('utr_ref');
	    $resdata=array('status'=>'SUCCESS','UTRNUMBER'=>$utr_ref);
    	$updata=array('pay_req_id'=>$pay_req_id);
    	$res=$this->db_model->insert_update('payout_request',$resdata,$updata);
    	if($res)
		{/*
		    $ope = $this->db_model->getWhere('payout_request',array('pay_req_id'=>$pay_req_id));
		    foreach($ope as $operator){
    			    $cus_id=$operator->cus_id;
    			    $amount=$operator->amount;
    			    $charge=$operator->charge;
    			    $cusdetails=$this->db_model->getAlldata("SELECT * FROM aeps_exr_trnx WHERE aeps_txn_agentid= $cus_id ORDER BY aeps_txn_id DESC LIMIT 1");
    			    $aeps_txn_clbal=$cusdetails[0]->aeps_txn_clbal;
    			    $newclosingbal=$aeps_txn_clbal+$amount+$charge;
    			    
    			    $rdata=array('aeps_txn_opbal'=>$aeps_txn_clbal,'aeps_txn_crdt'=>($amount+$charge),'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$newclosingbal,'aeps_txn_type'=>'Redeem Rejected','aeps_txn_comment'=>$reason);
                	$tt=$this->db_model->insert_update('aeps_exr_trnx',$rdata);
    			}*/
    			        $msg="8630450571-89938-143-92.204.134.236";
    			     
    			        $pay_data = $this->db_model->getAlldata("SELECT * FROM payout_request WHERE pay_req_id= '$pay_req_id'");

    			        $CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/submit_payout";
                        $param['msg'] = $msg;
                        $param['bank_name'] = $pay_data['0']->bankName;
                        $param['account_number'] = $pay_data['0']->bankAccount;
                        $param['ifsc_code'] = $pay_data['0']->bankIFSC;
                        $param['account_holder_name'] = $pay_data['0']->accountHolderName;
                        $param['amount'] = $pay_data['0']->amount;
                        $param['charge'] = $pay_data['0']->charge;
                        // $param['moveto'] = $pay_data['0']->bankAccount;
                        
                        
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
    			        
    			       $message = $response['message'];
		    $this->session->set_flashdata('success',$message);
		    redirect('admin/redeem-request-report');
		}
		else{
		    $this->session->set_flashdata('error','Something Went Wrong');
		    redirect('admin/redeem-request-report');
		}
	}
	
	
	public function aepsBalance($cus_id)
    {
        $query = $this->Db_model->getAlldata("SELECT * FROM aeps_exr_trnx as e WHERE e.aeps_txn_agentid='$cus_id' ORDER BY e.aeps_txn_id DESC LIMIT 1");
        $this->db->last_query();
        if ($query)
        {   
            return $query;
        }
        else
        {
            return "0";
        }
    }
	
	public function transaction_enquiry($UNIQUEID)
	{
	    
        $CURLOPT_URL="https://api.icicibank.com:8443/api/Corporate/CIB/v1/TransactionInquiry";
		$values=array("AGGRID"=>"BAAS0021","CORPID"=>"575908502","USERID"=>"MAYANKTI","UNIQUEID"=>"$UNIQUEID","URN"=>"SR189932540");
        // { "RESPONSE": "SUCCESS", "STATUS": "SUCCESS", "URN": "SR189932540", "UNIQUEID": "098765434567890", "UTRNUMBER": "034617084034" }
		$response = $this->Icici_model->register_api($CURLOPT_URL,$values);
		if($response){
		    $status = $response->STATUS;
		    if($status == "FAILURE"){
		        $status = "REJECTED";
                $payoutDetails = $this->db_model->getWhere('payout_request',array('UNIQUEID'=>$UNIQUEID));
                if($payoutDetails[0]->status == "PENDING"){
                    $amount = $payoutDetails[0]->amount;
                    $charge = $payoutDetails[0]->charge;
                    $cus_id = $payoutDetails[0]->cus_id;
                    
                     $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                    if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                    
                    $txntype = 'Redeem Back';
                    $total = $cus_aeps_bal + $amount;
            		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$amount,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    
                    $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                    if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                    
            		$txntype = 'Redeem Charge Back';
            		$ip = $_SERVER['REMOTE_ADDR'];
            		$date = DATE('Y-m-d h:i:s');
            		$total = $cus_aeps_bal + $charge;
            		
            		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$charge,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                }
                
		        $this->session->set_flashdata('error',"Payout Request is Rejected");
		    }else{
		        $status = $response->STATUS;
		        $this->session->set_flashdata('success',"Payout Request is $status");
		    }
		    $resdata=array('status'=>"$status",'date'=>date('Y-m-d h:s:i'),'UNIQUEID'=>$UNIQUEID,'UTRNUMBER'=>$response->UTRNUMBER,'response'=>json_encode($response));
            $updata=array('UNIQUEID'=>$UNIQUEID);
        	$this->db_model->insert_update('payout_request',$resdata,$updata);
		}else{
	        $this->session->set_flashdata('error',"Please Try again Later");
		}
       	redirect('admin/redeem-request-report');
                
	}
	
	
	public function redeem_transaction_Api($mobile,$name){
        
        
	    if(isset($_POST['redeem_transaction']))
		{
		    $id=$_POST['pay_req_id'];
		    for($i=0;$i<count($id);$i++){
		        $ope = $this->db_model->getWhere('payout_request',array('pay_req_id'=>$id[$i]));
    			foreach($ope as $operator)
    			{
    			    $TXNTYPE = 'IFS';
    				$UNIQUEID=rand(000000000000000,999999999999999);
    				$DEBITACC=$operator->bankAccount;
    				$IFSC=$operator->bankIFSC;
    				$AMOUNT=$operator->amount+$operator->charge;
    				$PAYEENAME=$operator->accountHolderName;
    				$bankName=$operator->bankName;
    				if($bankName == 'ICICI' || $bankName == 'ICICI BANK' ){
            	        $TXNTYPE = 'TPA';
            	        $IFSC = 'ICIC0000011';
            	    }
            	    
            	    
                    $data = "msg=payout~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$TXNTYPE~$UNIQUEID~$DEBITACC~$IFSC~$AMOUNT~$PAYEENAME~$bankName";
            	    
    	    
    				$values=array("AGGRID"=>"BAAS0021","AGGRNAME"=>"SATMAT","CORPID"=>"575908502","USERID"=>"","URN"=>"","UNIQUEID"=>"$UNIQUEID","DEBITACC"=>"","CREDITACC"=>"$DEBITACC","IFSC"=>"$IFSC","AMOUNT"=>"$AMOUNT","CURRENCY"=>"INR","TXNTYPE"=>"$TXNTYPE","PAYEENAME"=>"$PAYEENAME","REMARKS"=>"");
    				$CURLOPT_URL="https://api.icicibank.com:8443/api/Corporate/CIB/v1/Transaction";
    				$response=$this->Icici_model->register_api($CURLOPT_URL,$values);
    				if($response){
    				    if($response->STATUS == "FAILURE"){
    				        $stat = "REJECTED";
    				        
                            $amount = $operator->amount;
                            $charge = $operator->charge;
                            $cus_id = $operator->cus_id;
                            
                            $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            
                            $txntype = 'Redeem Back';
                            $total = $cus_aeps_bal + $amount;
                    		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$amount,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                            
                            $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            
                    		$txntype = 'Redeem Charge Back';
                    		$ip = $_SERVER['REMOTE_ADDR'];
                    		$date = DATE('Y-m-d h:i:s');
                    		$total = $cus_aeps_bal + $charge;
                    		
                    		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$charge,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    		
    				        $this->session->set_flashdata('error',"Your Payout Status is $stat");
    				    }else{
    				        $stat = $response->STATUS;
    				        $this->session->set_flashdata('success',"Your Payout Status is $stat");
    				    }
    				    
        				$resdata=array('status'=>$stat, 'REQID'=>$response->REQID,'UNIQUEID'=>$UNIQUEID,'UTRNUMBER'=>$response->UTRNUMBER,'date'=>date('Y-m-d h:s:i'),'response'=>json_encode($response));
        				$updata=array('pay_req_id'=>$id[$i]);
        				$this->db_model->insert_update('payout_request',$resdata,$updata);
    				}
    				else{
    				    $resdata=array('status'=>'PENDING','date'=>date('Y-m-d h:s:i'),'UNIQUEID'=>$UNIQUEID,'response'=>json_encode($response));
        				$updata=array('pay_req_id'=>$id[$i]);
        				$this->db_model->insert_update('payout_request',$resdata,$updata);
    				    $this->session->set_flashdata('error',"Your Payout Status is PENDING");
    				}
    			}
		    }
		    
		    redirect('https://edigitalvillage.net/admin/redeem-request-report');
		}
        
        
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
	
	public function redeem_transaction()
	{
	    if(isset($_POST['redeem_transaction']))
		{
		    $id=$_POST['pay_req_id'];
		    for($i=0;$i<count($id);$i++){
		        $ope = $this->db_model->getWhere('payout_request',array('pay_req_id'=>$id[$i]));
    			foreach($ope as $operator)
    			{
    			    $TXNTYPE = 'IFS';
    				$UNIQUEID=rand(000000000000000,999999999999999);
    				$DEBITACC=$operator->bankAccount;
    				$IFSC=$operator->bankIFSC;
    				$AMOUNT=$operator->amount+$operator->charge;
    				$PAYEENAME=$operator->accountHolderName;
    				$bankName=$operator->bankName;
    				if($bankName == 'ICICI' || $bankName == 'ICICI BANK' ){
            	        $TXNTYPE = 'TPA';
            	        $IFSC = 'ICIC0000011';
            	    }
    	    
    				$values=array("AGGRID"=>"BAAS0021","AGGRNAME"=>"SATMAT","CORPID"=>"575908502","USERID"=>"","URN"=>"","UNIQUEID"=>"$UNIQUEID","DEBITACC"=>"","CREDITACC"=>"$DEBITACC","IFSC"=>"$IFSC","AMOUNT"=>"$AMOUNT","CURRENCY"=>"INR","TXNTYPE"=>"$TXNTYPE","PAYEENAME"=>"$PAYEENAME","REMARKS"=>"");
    				$CURLOPT_URL="https://api.icicibank.com:8443/api/Corporate/CIB/v1/Transaction";
    				$response=$this->Icici_model->register_api($CURLOPT_URL,$values);
    				if($response){
    				    if($response->STATUS == "FAILURE"){
    				        $stat = "REJECTED";
    				        
                            $amount = $operator->amount;
                            $charge = $operator->charge;
                            $cus_id = $operator->cus_id;
                            
                            $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            
                            $txntype = 'Redeem Back';
                            $total = $cus_aeps_bal + $amount;
                    		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$amount,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                            
                            $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            
                    		$txntype = 'Redeem Charge Back';
                    		$ip = $_SERVER['REMOTE_ADDR'];
                    		$date = DATE('Y-m-d h:i:s');
                    		$total = $cus_aeps_bal + $charge;
                    		
                    		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$charge,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    		
    				        $this->session->set_flashdata('error',"Your Payout Status is $stat");
    				    }else{
    				        $stat = $response->STATUS;
    				        $this->session->set_flashdata('success',"Your Payout Status is $stat");
    				    }
    				    
        				$resdata=array('status'=>$stat, 'REQID'=>$response->REQID,'UNIQUEID'=>$UNIQUEID,'UTRNUMBER'=>$response->UTRNUMBER,'date'=>date('Y-m-d h:s:i'),'response'=>json_encode($response));
        				$updata=array('pay_req_id'=>$id[$i]);
        				$this->db_model->insert_update('payout_request',$resdata,$updata);
    				}
    				else{
    				    $resdata=array('status'=>'PENDING','date'=>date('Y-m-d h:s:i'),'UNIQUEID'=>$UNIQUEID,'response'=>json_encode($response));
        				$updata=array('pay_req_id'=>$id[$i]);
        				$this->db_model->insert_update('payout_request',$resdata,$updata);
    				    $this->session->set_flashdata('error',"Your Payout Status is PENDING");
    				}
    			}
		    }
		    
		    redirect('https://edigitalvillage.net/admin/redeem-request-report');
		}
		
		    redirect('https://edigitalvillage.net/admin/redeem-request-report');
	}
	
	
	public function redeem_wallet_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join customers as c on e.txn_agentid = c.cus_id WHERE txn_type LIKE '%PAYOUT%' and DATE(e.txn_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.txn_date) <= '".date('Y-m-d').' 00:00:00'."' order by e.txn_id DESC");
		    $this->load->view('admin/redeem-wallet-report',$data);
		}
	}
	
	public function search_redeem_wallet_report()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
            
		}else{
		    
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
		    $sql = "SELECT * FROM `exr_trnx` as e join customers as c on e.txn_agentid = c.cus_id WHERE txn_type LIKE '%PAYOUT%' and " ;
		    
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.txn_date) >= '".$from.' 00:00:00'."' and DATE(e.txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    
    		    if($from !=''){
    		        $sql .=" DATE(e.txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " e.txn_id != '' ORDER BY e.txn_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/redeem-wallet-report',$data);
		}
	}
	
	
    public function redeem_request_history()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `payout_request`");
		    $this->load->view('admin/redeem-request-history',$data);
		}
	}
	
	
	public function search_redeem_request_history()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
            
		}else{
		    
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
		    $sql = "SELECT * FROM `payout_request` as e join customers as c on e.cus_id = c.cus_id WHERE status!='PENDING' and " ;
		    
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.request_date) >= '".$from.' 00:00:00'."' and DATE(e.request_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(e.request_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.request_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " e.pay_req_id != '' ORDER BY e.pay_req_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/redeem-request-history',$data);
		}
	}
	
	public function resend_pass($id)
	{
	    $resps = $this->db_model->getwhere('customers', array('cus_id' => $id));
	    $res = $resps[0];
	    $mobile=$this->encryption_model->decode($res->cus_mobile);
	    $pass=$this->encryption_model->decode($res->cus_pass);
	    $message=array('mobile'=>$mobile,'pass'=>$pass);
        $this->msg_model->registration($message);
        $this->session->set_flashdata('success','Successfully sent password to registered mobile number and email');
        redirect('/admin/view-all-members');
	}
	
	
	public function add_member()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getwhere('commission_scheme',array('scheme_status'=>'active'));
		    $this->load->view('admin/add-member',$data);
		}
	}
	
	public function add_member_succ()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $mobile = $this->encryption_model->encode($this->input->post('mobile'));
		    $pass = rand(10000,99999);
		    $pin = rand(10000,99999);
		    $cus_type = $this->input->post('cus_type');
		    $refer = $this->input->post('reffer_id');
		    $package = $this->input->post('package_id');
		    $mob = $this->input->post('mobile');
		    $referral_id = $this->input->post('referral_id');
		    
		    $r = $this->db_model->getwhere('customers',array('cus_mobile'=>$mobile));
		    
		    if($r){
		        
		         $this->session->set_flashdata('error','Member With Entered Mobile Number Already Exists');
		        // $this->msg_model->whatsapp_sms($mob,'Member With Entered Mobile Number Already Exists');
		         
		         redirect('admin/add-member');
		         
		    }else{
		        
    		    if($refer){ $refer = $refer;}else{$refer = 0;}
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
        		        "cus_pin" => $this->encryption_model->encode($pin),
        		        "referral_id" => $referral_id
        		        );
        		    $inserted_id=$this->db_model->insert_update('customers',$data);
        		    $last_id = $this->db->insert_id();
        		    
	                $this->db_model->insert_update('application_status_service',array('cus_id'=>$last_id));
        		    
        		    $message=array('mobile'=>$this->input->post('mobile'),'pass'=>$pass,'pin'=>$pin,'id'=>$last_id,'name'=>$this->input->post('name'));
        		    $this->msg_model->registration($message);
        		    
        		    $email = $this->input->post('email');
            		$msg= $message = "Welcome in ".$this->config->item('title')." family, your member id:  ".$this->input->post('mobile').", login pass: ".$pass.", login pin: ".$pin.", APP Link: ".$this->config->item('ataapp');
            		$sub="Successfully Registration";
            		$to=$email;
            		$this->email_model->send_email($to,$msg,$sub);
        		
        		    $this->session->set_flashdata('success','Member Created Successfully');
        		    
        		    if($cus_type == "retailer"){
        		        redirect('admin/view-all-retailer');
        		    }else if($cus_type == "distributor"){
        		        redirect('admin/view-all-distributor');
        		    }else if($cus_type == "master"){
        		        redirect('admin/view-all-master');
        		    }else{
        		        redirect('admin/view-all-members');
        		    }
    		    }
		    }
	}
	
	public function edit_member($id){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $a = array();
    		$resps = $this->db_model->getwhere('customers', array('cus_id' => $id));
    		$data['package'] = $this->db_model->getwhere('commission_scheme');
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
		    $this->load->view('admin/edit-member',$data);
		}
	}
	
	public function edit_member_succ(){
	     $cus_id = $this->input->post('cus_id');
	     $user_type = $this->db_model->getAlldata("select cus_type from customers where cus_id='$cus_id'")[0]->cus_type;
	     $data = array(
		        "cus_mobile" => $this->encryption_model->encode($this->input->post('mobile')),
		        "cus_name" => $this->input->post('name'),
		        "cus_email" => $this->input->post('email'),
		        "cus_outlate" => $this->input->post('outlet'),
		        "cus_address" => $this->input->post('address'),
		        "scheme_id"=> $this->input->post('package_id'),
		        "cus_ip"=> $this->input->post('cus_ip'),
		        "limit_amount"=> $this->input->post('limit_amount')
		        );
    	  $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
    	  
    	  $data1=array('log_user'=>$cus_id,'log_type'=>$user_type."(edit_member)",'log_ip'=>$this->input->ip_address(),'medium'=>'Web','log_intime'=>date('Y-m-d H:i:s'),'message'=>json_encode($data));
          $this->db_model->insert_update('exr_log',$data1);
          
          $logfile = 'LOG_Edit_Member.txt';
    	    $log = "\n\n********************************************Edit Member Details**********************************************************";
            $log .= "\n\n DATE-TIME :- ".date('Y-m-d H:i:s');
            $log .= "\n\n Edited Data :- ".json_encode($data)."\n\n";
            file_put_contents($logfile, $log, FILE_APPEND | LOCK_EX);
    	  
    	  $this->session->set_flashdata('success','Member Updated Successfully');
    	  redirect($this->agent->referrer());
	}
	
	public function credit_wallet()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $this->load->view('admin/credit-wallet');
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
	
	public function assign_user_creadit()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $this->load->view('admin/assign-user-creadit');
		}
	}
	
	public function assign_credits()
	{
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
    					    $this->session->set_flashdata('success', 'Assign User Credit Successfully');
    					}
    					else{
    					    $this->session->set_flashdata('error', "Can't Assign Credits");
    					}				
    		redirect('admin/assign_user_creadit');	
	
					
	}
	
	public function debit_wallet()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $this->load->view('admin/debit-wallet');
		}
	}
	
	public function daybook()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['fundCredit'] = $this->db_model->getAlldata("SELECT sum(txn_dbdt) as amt FROM `exr_trnx` where txn_type='Pullout' AND txn_fromto='0' AND date(txn_date)=CURDATE()");
		    $data['fundDebit'] = $this->db_model->getAlldata("SELECT sum(txn_crdt) as amt FROM `exr_trnx` where txn_type='Direct Credit' AND txn_fromto='0' AND date(txn_date)=CURDATE()");
		    $this->load->view('admin/daybook',$data);
		}
	}
	
	public function commission_scheme()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `commission_scheme` ORDER BY scheme_id DESC");
		    $this->load->view('admin/commission-scheme',$data);
		}
	}
	
	public function commission_package()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `exr_package` WHERE package_addedby = '0'");
		    $this->load->view('admin/commission-package',$data);
		}
	}
	
	public function dmt_package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('slab');
		//$type = $this->input->post('package_type');
		$type = 'flat';
		$member_type = 'retailer';
		$commission = '0';
		//$max_commission = $this->input->post('max_commission');
		$r = $this->db_model->insert_update('dmt_commission_slab', array('slab'=>$name, 'member_type'=>$member_type, 'dmt_update_time'=>$date, 'amount'=>$commission,'max_commi_amount'=>$commission,'type'=>$type));
		if($r)
		{
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('admin/dmt-commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('admin/dmt-commission-package');
		}
		
	}
	
	public function dmt_delete_package($id)
	{
		
		$this->db->delete('dmt_commission_slab', array('dmt_comm_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('admin/dmt-commission-package');
	
	}
	
	public function aeps_delete_package($id)
	{
		
		$this->db->delete('aeps_commission_slab', array('aeps_comm_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('admin/aeps-commission-package');
	
	}
	
	public function micro_atm_delete_package($id)
	{
		
		$this->db->delete('micro_atm_commission_slab', array('atm_comm_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('admin/micro-atm-commission-package');
	
	}
	
	
	public function payout_delete_package($id)
	{
		
		$this->db->delete('payout_commission_slab', array('payout_charge_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('admin/payout-commission-package');
	
	}
	
	public function package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('package_name');
		$type = $this->input->post('package_type');
		$r = $this->db_model->insert_update('exr_package', array('package_name'=>$name, 'package_membertype'=>$type, 'package_date'=>$date, 'package_ip'=>$ip));
		$id = $this->db->insert_id();
		if($r)
		{
			$ope = $this->db_model->getWhere('operator');
			foreach($ope as $operator)
			{
				$this->db_model->insert_update('exr_packagecomm', array('packcomm_name'=>$id, 'packcomm_opcode'=>$operator->opcodenew,'packagecom_tem'=>1,'packagecom_type'=>2,'packagecom_amttype'=>2, 'packcomm_comm'=>0, 'packcomm_date'=>$date, 'packcomm_ip'=>$ip));
			}
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('admin/commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('admin/commission-package');
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
			redirect('admin/commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Deletion Failed! Please try again later');
			redirect('admin/commission-package');
		}
	}
	
	public function view_package_commission($id)
	{
		$data['package'] = $this->db_model->getAlldata("select * from `exr_packagecomm` as exrpc join `exr_package` as exrp on exrpc.packcomm_name = exrp.package_id join `operator` as op on exrpc.packcomm_opcode = op.opcodenew where exrpc.packcomm_name=$id");
		$this->load->view('admin/view-all-package-commission',$data);
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
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $this->load->view('admin/api-package-commission');
		}
	}
	
	public function micro_atm_commission_package(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `micro_atm_commission_slab`");
		    $this->load->view('admin/view-micro-atm-commission-package',$data);
		}
	}
	
	public function aeps_commission_package()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `aeps_commission_slab`");
		    $this->load->view('admin/aeps-commission-package',$data);
		}
	}
	
	public function dmt_commission_package()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `dmt_commission_slab`");
		    $this->load->view('admin/dmt-commission-package',$data);
		}
	}
	
	public function payout_commission_package()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `payout_commission_slab`");
		    $this->load->view('admin/payout-commission-package',$data);
		}
	}
	
	
	public function profile()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $user_id = $this->session->userdata('user_id');
	        $data['data'] = $this->db_model->getwhere('user',array('user_id'=>$user_id));
	        $data['support'] = $this->db_model->getwhere('support');
	        $data['link1'] = $this->db_model->getwhere('user_links',array('id'=>'1'));
	        $data['link2'] = $this->db_model->getwhere('user_links',array('id'=>'2'));
	        $data['link3'] = $this->db_model->getwhere('user_links',array('id'=>'3'));
		    $data['package'] = $this->db_model->getwhere('commission_scheme',array('scheme_status'=>'active'));
		    $this->load->view('admin/profile',$data);
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
		$upi_id = $this->input->post('upi_id');
		$twitter = $this->input->post('twitter');
		$facebook = $this->input->post('facebook');
		$instagram = $this->input->post('instagram');
		
		$support_email = $this->input->post('support_email');
		$support_mobile = $this->input->post('support_mobile');
		$support_watsapp_mobile = $this->input->post('support_watsapp_mobile');
		
		$title1 = $this->input->post('title1');
		$link1 = $this->input->post('link1');
		
		$title2 = $this->input->post('title2');
		$link2 = $this->input->post('link2');
		
		$title3 = $this->input->post('title3');
		$link3 = $this->input->post('link3');
		
		
		$package_id_b2c = $this->input->post('package_id_b2c');
		$package_id_b2b = $this->input->post('package_id_b2b');
		$package_id_b2bp = $this->input->post('package_id_b2bp');
		$recharge_limit = $this->input->post('recharge_limit');
		
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
		
	    $r = $this->db_model->insert_update('user',array('username'=>$username,'name'=>$name,'email'=>$email,'mobile'=>$mobile,'address'=>$address,'upi_id'=>$upi_id,'twitter'=>$twitter,'facebook'=>$facebook,'instagram'=>$instagram,'recharge_limit'=>$recharge_limit/*,'b_2_b'=>$package_id_b2b,'b_2_c'=>$package_id_b2c,'b_2_b_Prime'=>$package_id_b2bp*/),array('user_id'=>$user_id));
	    
	    $f = $this->db_model->insert_update('support',array('mobile'=>$support_mobile,'email'=>$support_email,'watsappnum'=>$support_watsapp_mobile),array('supportid'=>'1'));

        $l1 = $this->db_model->insert_update('user_links',array('title'=>$title1,'link'=>$link1),array('id'=>'1'));
        
        $l2 = $this->db_model->insert_update('user_links',array('title'=>$title2,'link'=>$link2),array('id'=>'2'));
        
        $l3 = $this->db_model->insert_update('user_links',array('title'=>$title3,'link'=>$link3),array('id'=>'3'));
        
		if($r || $f || $l1 || $l2  || $l3)
		{
			$this->session->set_flashdata('success','Profile Updated Successfully..');
			redirect('admin/profile');		
		}
		else
		{
			$this->session->set_flashdata('error','Profile not Updated..');
			redirect('admin/profile');
		}
	}
	
	public function account()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $this->load->view('admin/account-setting');
		}
	}
	
	function check_password() 
	{
	    
	    $old_password =$this->input->post('old_password');
	    $data=$this->db_model->getwhere('user',array('password'=>$this->encryption_model->encode($old_password),'user_id'=>$this->session->userdata('user_id')));
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
	    $data=$this->db_model->getwhere('user',array('pin'=>$this->encryption_model->encode($old_pin),'user_id'=>$this->session->userdata('user_id')));
	    if($data){
	        echo TRUE;
	    }
        else{
            echo FALSE;
        }
        
    }
    
	public function password_and_pin()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('admin/login');
		}
		else{
		    if(isset($_POST['update_password']))
		    {
		        $record=array(
				            'password'=>$this->encryption_model->encode($_POST['new_password'])
				        );
    			$this->db_model->insert_update('user',$record,array('user_id'=>$this->session->userdata('user_id')));
    			$this->session->set_flashdata('success','Password Updated Successfully...!!!');
		    }
		    
		    if(isset($_POST['update_pin']))
		    {
                $record=array(
			            'pin'=>$this->encryption_model->encode($_POST['new_pin'])
			        );
    			$this->db_model->insert_update('user',$record,array('user_id'=>$this->session->userdata('user_id')));
    			$this->session->set_flashdata('error','Pin Updated Successfully...!!!');
		    }
		    
		    redirect('admin/account');
		}
	}
	
	public function view_all_members(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers ORDER BY cus_id DESC");
		    $this->load->view('admin/view-members',$data);
		}
	}
	
	public function view_all_master(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='master' ORDER BY cus_id DESC");
		    $this->load->view('admin/view-members',$data);
		}
	}
	
	public function view_all_distributor(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='distributor' ORDER BY cus_id DESC");
		    $this->load->view('admin/view-members',$data);
		}
	}
	
	public function view_all_retailer(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='retailer' ORDER BY cus_id DESC");
		    $this->load->view('admin/view-members',$data);
		}
	}
	
	public function view_all_api_clients(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='api' ORDER BY cus_id DESC");
		    $this->load->view('admin/view-members',$data);
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
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
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
		    $this->load->view('admin/member-details',$data);
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
		redirect('admin/member-details/'.$id);
	}

	public function in_active_customer($id)
	{
		$this->db_model->insert_update('customers',array('avability_status'=>1),array('cus_id'=>$id));
		redirect('admin/member-details/'.$id);
	}
	
	public function logout_member_app($id)
	{   
	    $tablename="customers";
	    $data=array('login_status'=>'loggedout','deviceId'=>'');
		$r=$this->db_model->insert_update($tablename,$data,array('cus_id'=>$id));
		$this->session->set_flashdata('success','Member logged Out Successfuly');
		redirect('admin/view-all-members');
	}
	
	public function logout_member_web($id)
	{   
	    $tablename="customers";
	    $data=array('web_login_status'=>'loggedout');
		$r=$this->db_model->insert_update($tablename,$data,array('cus_id'=>$id));
		$this->session->set_flashdata('success','Member logged Out Successfuly');
		redirect('admin/view-all-members');
	}
	
	public function delete_account($id)
	{
		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$id));
		$this->db_model->delete('customers',array('cus_id'=>$id));
		$this->db_model->delete('application_status_service',array('cus_id'=>$id));
		$this->session->set_flashdata('success','Member Deleted Successfuly');
		redirect('admin/view-all-members');
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
		redirect('admin/view-all-members');
	}
	
	public function switch_account($id){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
    	    $data['r'] = $this->db_model->getwhere('customers',array('cus_id'=>$id));
    		$this->load->view('admin/switch-account',$data);
		}
	}
	
	public function view_all_open_tickets()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['r'] = $this->db_model->getAlldata("SELECT * FROM `ticket` JOIN customers ON customers.cus_id = ticket.cus_id WHERE status='0' AND reply_from!='admin' ORDER BY t_id DESC ");
		    // $this->db->last_query();exit;
		    $this->load->view('admin/view-all-open-tickets',$data);
		}
	}
	
	public function view_all_closed_tickets()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['r'] = $this->db_model->getAlldata("SELECT * FROM `ticket` JOIN customers ON customers.cus_id = ticket.cus_id WHERE status='1' AND reply_from!='admin' ORDER BY t_id DESC ");
		    $this->load->view('admin/view-all-closed-tickets',$data);
		}
	}
	
	
	public function ticket($id)
	{
		$data['r'] = $this->db_model->getAlldata("SELECT * FROM ticket WHERE ticket_id='$id'");
		$na = $this->db_model->getwhere('customers',array('cus_id'=>$data['r'][0]->cus_id),'cus_name,cus_email,cus_mobile');
		$data['name'] = $na[0]->cus_name;
		$data['email'] = $na[0]->cus_email;
		$data['mobile'] = $na[0]->cus_mobile;
		$this->load->view('admin/view-all-ticket',$data);
	}
	
	public function reply()
  	{    	
    	$from = 'admin'; 
	    $desc = $this->input->post('desc');
	    $ticket = $this->input->post('ticket'); 
	    $cus = $this->input->post('cus'); 
	    $ndate = DATE('Y-m-d h:i:s');

        if($_FILES['userfile']['name']!='')
	    {
	        $path = "./includes/uploads/support/";
	        if(!is_dir($path)) //create the folder if it's not already exists
	        {
	            mkdir($path,0755,TRUE);
	        } 
	          $config['encrypt_name'] =TRUE;
	          $config['upload_path'] =$path;
	          $config['allowed_types'] = 'gif|jpg|png|jpeg';
	          $config['max_size'] = '20000000000';
	          $config['overwrite']     = FALSE;
	          $this->upload->initialize($config);
	          if($this->upload->do_upload())
	          {
	            $file_data = $this->upload->data();
	            $orginal_path='includes/uploads/support/'.$file_data['file_name'];
	            $data = array(
		            'cus_id'	=>	$cus,
		            'ticket_id'	=>	$ticket,
		            'reply_from'	=>	$from,
		            'message'	=>	$desc,
		            'ticket_date'	=>	$ndate,
		            'ticket_image'	=>	$orginal_path
		        );
		        $r = $this->db_model->insert_update('ticket',$data);
	            if($r)
	            {
	              echo "<script>window.location.href='".base_url('admin/ticket').'/'.$ticket."';</script>"; 
	            }
	            else
	            {
	              echo "<script>window.location.href='".base_url('admin/ticket').'/'.$ticket."';</script>";
	            }
	          }
	          else
	          {
	            echo "<script>window.location.href='".base_url('admin/ticket').'/'.$ticket."';</script>";
	          }
	        }
	        else
	        {
	        	$data = array(
		            'cus_id'	=>	$cus,
		            'ticket_id'	=>	$ticket,
		            'reply_from'	=>	$from,
		            'message'	=>	$desc,
		            'ticket_date'	=>	$ndate
		        );
		        $r = $this->db_model->insert_update('ticket',$data);
	            if($r)
	            {
	              echo "<script>window.location.href='".base_url('admin/ticket').'/'.$ticket."';</script>";
	            }
	            else
	            {
	              echo "<script>window.location.href='".base_url('admin/ticket').'/'.$ticket."';</script>";
	            }
	      }  
	}
	
	 public function closed()
      {
        $tk = $this->input->post('tk');
        $recid = $this->input->post('recid');
        
        // echo $tk; echo"<br>"; echo $recid; exit;
        
        $data = array(
    		'status'	=>	'1'
    	);
    	
    	$update=array('dispute_status'=>'close');
    	
    	$r = $this->db_model->insert_update('ticket',$data,array('ticket_id'=>$tk));
    	$rec = $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$update,array('recid'=>$recid));
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
	    
		$sql = "select * from virtual_balance order by vb_id desc limit 1";
		$r = $this->db_model->getAlldata($sql);
		if($r)
		{
			if($r[0]->vb_clbal >= $txn_crdt)
			{
				$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
				$r = $this->db_model->getAlldata($sql);
				if($r)
				{
					$closing = $r['0']->txn_clbal;
					$total = $r['0']->txn_clbal + $txn_crdt;
					$txn_dbdt = 0;
					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_date'=>$date,'txn_time'=>$time,'txn_ip'=>$ip));
					$txn = $this->db->insert_id();
					if($lasttxnrec)
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
					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_date'=>$date,'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
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
				$sql = "select * from virtual_balance where 1 order by vb_id desc limit 1";
				$r = $this->db_model->getAlldata($sql);
				if($r)
				{
					$total = $r['0']->vb_clbal - $txn_crdt;
					$txn_dbdt = $txn_crdt;

					$data = array(
						'txn_id'	=>	$txn,
						'vb_agentid'	=>	$cus_id,
						'vb_recrefid'	=>	0,
						'vb_opbal'	=>	$r['0']->vb_clbal,
						'vb_crdt'	=>	0,
						'vb_dbdt'	=>	$txn_dbdt,
						'vb_clbal'	=>	$total,
						'vb_checktime'	=>	time(),
						'vb_type'	=>	$txntype,
						'vb_fromto'	=>	'',
						'vb_time'	=>	time(),
						'vb_date'	=>	date('Y-m-d h:i:s'),
						'vb_ip'	=>	$ip,
						'vb_comment'	=>	$vb_comment,
						'transaction_type'	=>	$transfer_type,
						'bank_name'	=>	$bank_name,
						'transaction_ref'	=>	$transaction_ref,
						'paid_due'	=>	$paid_due,
						'transaction_date'	=>	$transaction_date,
						'status'	=>	1							
					);
					$this->db_model->insert_update('virtual_balance',$data);				
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
			$msg2 = "Insufficient fund...";				
			$this->session->set_flashdata('error', $msg2);
		}
		redirect('admin/virtual-balance');
		
	    //	echo $this->db->last_query();
	}
	
	public function wallet_pullout_success()
	{
		$cus_id = $this->input->post('cus_id');
		$pullout_bal = $this->input->post('pullout_bal');
		$vb_comment = $this->input->post('vb_comment');
		$cusdt = $this->db_model->getAlldata("select cus_mobile,cus_type from customers where cus_id='".$cus_id."'");
		$txn_dbdt = $pullout_bal;
		$txntype = 'Pullout';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
		
		$cus_type = $cusdt[0]->cus_type;
		$mobile = $cusdt[0]->cus_mobile;
	    
		if($cus_type == "api"){
		    
		    $sql = "select * from virtual_balance order by vb_id desc limit 1";
    		$r = $this->db_model->getAlldata($sql);
    		if($r)
    		{
    			if($r[0]->vb_clbal > $txn_crdt)
    			{
    				$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
    				$r = $this->db_model->getAlldata($sql);
    				
    				
				    if($r){
    					$closing = $r['0']->txn_clbal;
    					$total = $r['0']->txn_clbal - $txn_dbdt;
    					$txn_crdt = 0;
    					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_date'=>$date,'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
    					if($lasttxnrec)
    					{
    						$msg2 = "Debited Successfully";				
    						$this->session->set_flashdata('success', $msg2);
    						
    						$message = "Dear member, your account has successfully debited rs.".$txn_dbdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');						
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
    					$total = $txn_dbdt;
    					$txn_crdt = 0;
    					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_date'=>$date,'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
    					if($lasttxnrec)
    					{
    						$msg2 = "Credit Successfully";		
    						$this->session->set_flashdata('success', $msg2);
    						$message = "Dear member, your account has successfully debited rs.".$txn_dbdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');
    					}
    					else
    					{
    						$msg2 = "Failed! Please try once again";
    						$this->session->set_flashdata('error', $msg2);
    					}
    				}
    				
    				/*======== Adding fund to agent ===*/
				
    				$sql = "select * from virtual_balance where 1 order by vb_id desc limit 1";
    				$r = $this->db_model->getAlldata($sql);
    				if($r)
    				{
    					$total = $r['0']->vb_clbal + $txn_dbdt;
    					$data = array(
    						'vb_agentid'	=>	$cus_id,
    						'vb_recrefid'	=>	0,
    						'vb_opbal'	=>	$r['0']->vb_clbal,
    						'vb_crdt'	=>	$txn_dbdt,
    						'vb_dbdt'	=>	0,
    						'vb_clbal'	=>	$total,
    						'vb_checktime'	=>	time(),
    						'vb_type'	=>	$txntype,
    						'vb_fromto'	=>	'',
    						'vb_time'	=>	time(),
    						'vb_date'	=>	date('Y-m-d h:i:s'),
    						'vb_ip'	=>	$ip,
    						'vb_comment'	=>	$vb_comment,
    						'transaction_type'	=>	'',
    						'bank_name'	=>	'',
    						'transaction_ref'	=>	'',
    						'paid_due'	=>	'1',
    						'transaction_date'	=>	'',
    						'status'	=>	1					
    					);
    					$this->db_model->insert_update('virtual_balance',$data);			
    				}
    				
    				}else
        			{
        				$msg2 = "Insufficient fund...";				
        				$this->session->set_flashdata('error', $msg2);
        			}
    
    				//$this->msg_model->sendsms($cusdt[0]->cus_mobile,$message);
    		
    		}
    		else
    		{
    			$msg2 = "Insufficient fund...";				
    			$this->session->set_flashdata('error', $msg2);
    		}
		    
		}else{
		
    		$sql = "select * from virtual_balance order by vb_id desc limit 1";
    		$r = $this->db_model->getAlldata($sql);
    		if($r)
    		{
    			if($r[0]->vb_clbal > $txn_dbdt)
    			{
    				$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
    				$r = $this->db_model->getAlldata($sql);
    				if($r)
    				{
    					$closing = $r['0']->txn_clbal;
    					$total = $r['0']->txn_clbal - $txn_dbdt;
    					$txn_crdt = 0;
    					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_date'=>$date,'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
    					if($lasttxnrec)
    					{
    						$msg2 = "Debit Successfully";				
    						$this->session->set_flashdata('success', $msg2);
    						
    						$message = "Dear member, your account has successfully debited rs.".$txn_dbdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');						
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
    					$total = $txn_dbdt;
    					$txn_crdt = 0;
    					$lasttxnrec= $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_date'=>$date,'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
    					if($lasttxnrec)
    					{
    						$msg2 = "Credit Successfully";		
    						$this->session->set_flashdata('success', $msg2);
    						$message = "Dear member, your account has successfully debited rs.".$txn_dbdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');
    					}
    					else
    					{
    						$msg2 = "Failed! Please try once again";
    						$this->session->set_flashdata('error', $msg2);
    					}
    				}
    
    				//$this->msg_model->sendsms($cusdt[0]->cus_mobile,$message);
    
    				/*======== Adding fund to agent ===*/
    				
    				$sql = "select * from virtual_balance where 1 order by vb_id desc limit 1";
    				$r = $this->db_model->getAlldata($sql);
    				if($r)
    				{
    					$total = $r['0']->vb_clbal + $txn_dbdt;
    					$data = array(
    						'vb_agentid'	=>	$cus_id,
    						'vb_recrefid'	=>	0,
    						'vb_opbal'	=>	$r['0']->vb_clbal,
    						'vb_crdt'	=>	$txn_dbdt,
    						'vb_dbdt'	=>	0,
    						'vb_clbal'	=>	$total,
    						'vb_checktime'	=>	time(),
    						'vb_type'	=>	$txntype,
    						'vb_fromto'	=>	'',
    						'vb_time'	=>	time(),
    						'vb_date'	=>	date('Y-m-d h:i:s'),
    						'vb_ip'	=>	$ip,
    						'vb_comment'	=>	$vb_comment,
    						'transaction_type'	=>	'',
    						'bank_name'	=>	'',
    						'transaction_ref'	=>	'',
    						'paid_due'	=>	'1',
    						'transaction_date'	=>	'',
    						'status'	=>	1					
    					);
    					$this->db_model->insert_update('virtual_balance',$data);			
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
    			$msg2 = "Insufficient fund...";				
    			$this->session->set_flashdata('error', $msg2);
    		}
		}
		redirect('admin/virtual-balance');
	//echo $this->db->last_query();
	}
	
	public function virtual_balance(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['vb'] = $this->db_model->getAlldata("SELECT * FROM `virtual_balance` ORDER BY `vb_id` DESC");
		    $this->load->view('admin/virtual-balance',$data);
		}
	}
	
	public function search_vb_report(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
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
		    $this->load->view('admin/virtual-balance',$data);
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
		redirect('admin/virtual-balance');
	}
	
	public function api_switching(){
		if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['priority_api'] =$this->db_model->getwhere('priority_api');
		    $data['operator'] =$this->db_model->getwhere('operator');	
			$data['api'] = $this->db_model->getwhere('apisource');
		    $this->load->view('admin/api-switching',$data);
		}
	}
	
	public function update_api_priority()
	{
	    $id = $this->input->post('id');
	    $value = $this->input->post('value');
	    $col_name = $this->input->post('col_name');		  
	    $this->db_model->insert_update('priority_api',array($col_name=>$value),array('priority_api_id'=>$id));
	    echo "API Source Priority Updated Successfully";
	}
	
	public function amount_wise_api_switching(){
		if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['amount_apisource'] =$this->db_model->getAlldata('SELECT * FROM amount_apisources JOIN operator ON amount_apisources.api_opcodenew = operator.opcodenew ORDER BY amount_apisources.amount_id DESC');	
			$data['api'] = $this->db_model->getwhere('apisource');
			$data['operator'] =$this->db_model->getwhere('operator');
		    $this->load->view('admin/amount-wise-api-switching',$data);
		}
	}
	
	public function update_amount_api_source()
	{
	    $id = $this->input->post('id');
	    $api = $this->input->post('api');		 
	    $time = date("Y-m-d h:i:s");		  
	    $this->db_model->insert_update('amount_apisources',array('amount_apisource_id'=>$api,'amount_apisource_update_datetime'=>$time),array('amount_id'=>$id));
	    echo "Amount API Source Updated Successfully";
	}
	
	public function add_new_amount_apisource(){
	    $api = $_POST['apisourceid'];
	    $op = $_POST['opcodenew'];
	    $min_amt = $_POST['min_amt'];
	    $max_amt = $_POST['max_amt'];
	    $time = date("Y-m-d h:i:s");	
	    $data = $this->db_model->getAlldata("SELECT * FROM amount_apisources WHERE api_opcodenew='$op' AND api_amount_min='$min_amt' and api_amount_max='$max_amt'");
	    if($data){
	        $this->session->set_flashdata('error','Denomination With Same Amount And Operator Already Exists!');
    		redirect('admin/amount-wise-api-switching');
	    }else{
    	    $res = $this->db_model->insert_update('amount_apisources',array('amount_apisource_id'=>$api,'api_opcodenew'=>$op,'api_amount_min'=>$min_amt,'api_amount_max'=>$max_amt,'amount_apisource_update_datetime'=>$time));
    	    if($res){
    	        $this->session->set_flashdata('success','New Denomination Added Successfully');
    			redirect('admin/amount-wise-api-switching');
    	    }else{
    	        $this->session->set_flashdata('error','Unable to Add Denomination Try Again Later');
    			redirect('admin/amount-wise-api-switching');
    	    }
	    }
	    
	}
	
	public function circle_wise_api_switching(){
		if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['circle_apisource'] =$this->db_model->getAlldata('SELECT * FROM circle_api_switch');	
			$data['api'] = $this->db_model->getwhere('apisource');
			$data['operator'] =$this->db_model->getwhere('operator');
		    $this->load->view('admin/circular-api-switching',$data);
		}
	}
	
	public function update_circle_api_source()
	{
	    $id = $this->input->post('id');
	    $api = $this->input->post('api');		 
	    $time = date("Y-m-d h:i:s");		  
	    $this->db_model->insert_update('circle_api_switch',array('circle_api_source_id'=>$api,'circle_apisource_update_datetime'=>$time),array('circle_id'=>$id));
	    echo "Circle API Source Updated Successfully";
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
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where txn_fromto !='0' and txn_type In ('Direct Credit','Fund Transfer') and DATE(e.txn_date) >= '".DATE('Y-m-d')."' and DATE(e.txn_date) <= '".DATE('Y-m-d')."' ORDER BY e.txn_id DESC");
		    $this->load->view('admin/member-ledger',$data);
		}
	}
	
	public function search_member_ledger()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    	    $mobile_number = $_POST['mobile'];
    	    $mob = $this->encryption_model->encode($mobile_number);
    	    
    	    $from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where txn_fromto !='0' and txn_type In ('Direct Credit','Fund Transfer') and  " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.txn_date) >= '".$from.' 00:00:00'."' and DATE(e.txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(e.txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		if($mobile_number)
    		{
    			$sql .= "c.cus_mobile ='".$mob."' OR c.cus_name='".$mobile_number."' and ";
    		}
    		
		    $sql .= " e.txn_id != '' ORDER BY e.txn_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/member-ledger',$data);
		}
	}
	
	public function admin_ledger(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_fromto='0' and e.txn_type In ('Direct Credit','Pullout') and DATE(e.txn_date) >= '".DATE('Y-m-d')."' and DATE(e.txn_date) <= '".DATE('Y-m-d')."' ORDER BY e.txn_id DESC");
		    $this->load->view('admin/admin-ledger',$data);
		}
	}
	
	
	public function search_admin_ledger()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    	    $mobile_number = $_POST['mobile'];
    	    $mob = $this->encryption_model->encode($mobile_number);
    	    
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_fromto='0' and e.txn_type In ('Direct Credit','Pullout') and  " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.txn_date) >= '".$from.' 00:00:00'."' and DATE(e.txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(e.txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		if($mobile_number)
    		{
    			$sql .= "c.cus_mobile ='".$mob."' OR c.cus_name='".$mobile_number."' and ";
    		}
    		
		    $sql .= " e.txn_id != '' ORDER BY e.txn_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/search-admin-ledger',$data);
		}
	}
	
	
	
	public function aeps_ledger(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` as e join `customers` as c on e.aeps_txn_agentid = c.cus_id where DATE(e.aeps_txn_date) >= '".DATE('Y-m-d')."' and DATE(e.aeps_txn_date) <= '".DATE('Y-m-d')."' ORDER BY e.aeps_txn_id DESC");
		    $this->load->view('admin/aeps-ledger',$data);
		}
	}
	
	public function search_aeps_ledger()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM `aeps_exr_trnx` as e join `customers` as c on e.aeps_txn_agentid = c.cus_id  and  " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(e.aeps_txn_date) >= '".$from.' 00:00:00'."' and DATE(e.aeps_txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(e.aeps_txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(e.aeps_txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " e.aeps_txn_id != '' ORDER BY e.aeps_txn_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/aeps-ledger',$data);
		}
	}
	
	
	public function banner(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['banner'] = $this->db_model->getWhere('banner');
		    $this->load->view('admin/banner',$data);
		}
	}
	
	public function updatebanner($bid){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['banner'] = $this->db_model->getWhere('banner',array('bid'=>$bid));
		    $this->load->view('admin/update-banner',$data);
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
				$image_path='https://newmitra.in/uploads/'.$file_data['file_name'];
				$value=array("image"=>$id);	
				$data = array('image'=>$image_path);
	            $r=$this->db_model->insert_update("banner",$data,array('bid'=>$id));
	            $this->session->set_flashdata('success','Banner Updated Successfully');
				redirect('admin/banner');
			}else{
			    echo "failed";
			}
		}
   	}
   	
   	public function aeps_package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('package_name');
		$amount_max_range = $this->input->post('amount_max_range');
		$amount_min_range = $this->input->post('amount_min_range');
		/*$member_type = $this->input->post('member_type');
		$commission = $this->input->post('commission');
		$max_commission = $this->input->post('max_commission');*/
		
		$r = $this->db_model->insert_update('aeps_commission_slab', array('slab'=>$name, 'updated_time'=>$date,'amount_max_range'=>$amount_max_range,'amount_min_range'=>$amount_min_range ,'member_type'=>'retailer', 'percent'=>0, 'maximum_comm'=>0));
		$id = $this->db->insert_id();
		if($r)
		{
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('admin/aeps-commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('admin/aeps-commission-package');
		}
		
	}
	
	
	
   	public function payout_package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('package_name');
		$amount_max_range = $this->input->post('amount_max_range');
		$amount_min_range = $this->input->post('amount_min_range');
		/*$member_type = $this->input->post('member_type');
		$commission = $this->input->post('commission');
		$max_commission = $this->input->post('max_commission');*/
		
		$r = $this->db_model->insert_update('payout_commission_slab', array('slab'=>$name, 'datetime'=>$date,'amount_max_range'=>$amount_max_range,'amount_min_range'=>$amount_min_range));
		$id = $this->db->insert_id();
		if($r)
		{
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('admin/payout-commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('admin/payout-commission-package');
		}
		
	}
	
	
	public function add_commission_scheme(){
	    
	    $ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('package_name');
		
		$r = $this->db_model->insert_update('commission_scheme', array('scheme_name'=>$name, 'updated_datetime'=>$date));
		$id = $this->db->insert_id();
		if($id)
		{
		    $ope = $this->db_model->getWhere('operator');
			foreach($ope as $operator)
			{
				$this->db_model->insert_update('commission_scheme_recharge', array('scheme_id'=>$id,'slab_opcode'=>$operator->opcodenew, 'slab'=>$operator->operatorname,'type'=>'percent','retailer_comm'=>0,'distributor_comm'=>0, 'master_comm'=>0, 'api_comm'=>0));
			}
			
			$aeps = $this->db_model->getWhere('aeps_commission_slab');
			foreach($aeps as $aeps_operator)
			{
				$this->db_model->insert_update('commission_scheme_aeps', array('scheme_id'=>$id,'slab_opcode'=>$aeps_operator->aeps_comm_id, 'slab'=>$aeps_operator->slab,'amount_max_range'=>$aeps_operator->amount_max_range,'amount_min_range'=>$aeps_operator->amount_min_range,'type'=>'percent','retailer_comm'=>0,'distributor_comm'=>0, 'master_comm'=>0, 'api_comm'=>0));
			}
			
			$dmt = $this->db_model->getWhere('dmt_commission_slab');
			foreach($dmt as $dmt_operator)
			{
				$this->db_model->insert_update('commission_scheme_dmt', array('scheme_id'=>$id,'slab_opcode'=>$dmt_operator->dmt_comm_id, 'slab'=>$dmt_operator->slab,'type'=>'flat','retailer_comm'=>0,'distributor_comm'=>0, 'master_comm'=>0, 'api_comm'=>0));
			}
			
			$microatm = $this->db_model->getWhere('micro_atm_commission_slab');
			foreach($microatm as $microatm_operator)
			{
				$this->db_model->insert_update('commission_scheme_microatm', array('scheme_id'=>$id,'slab_opcode'=>$microatm_operator->atm_comm_id,'amount_max_range'=>$microatm_operator->amount_max_range, 'amount_min_range'=>$microatm_operator->amount_min_range,'slab'=>$microatm_operator->slab,'type'=>'percent','retailer_comm'=>0,'distributor_comm'=>0, 'master_comm'=>0, 'api_comm'=>0));
			}
			
			
			$payout = $this->db_model->getWhere('payout_commission_slab');
			foreach($payout as $payout_operator)
			{
				$this->db_model->insert_update('commission_scheme_payout', array('scheme_id'=>$id,'slab_opcode'=>$payout_operator->payout_charge_id,'amount_max_range'=>$payout_operator->amount_max_range, 'amount_min_range'=>$payout_operator->amount_min_range,'slab'=>$payout_operator->slab,'type'=>'percent','retailer_comm'=>0));
			}
			
			$this->db_model->insert_update('commission_scheme_pancard', array('scheme_id'=>$id,'type'=>'flat','retailer_comm'=>0,'distributor_comm'=>0, 'master_comm'=>0, 'api_comm'=>0));
			
			$this->db_model->insert_update('commission_scheme_adharpay', array('scheme_id'=>$id,'type'=>'flat','retailer_comm'=>0));
			
			
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('admin/commission_scheme');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('admin/commission_scheme');
		}
	}
	
	public function micro_atm_package_add()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date('Y-m-d h:s:i');
		
		$name = $this->input->post('package_name');
		$amount_max_range = $this->input->post('amount_max_range');
		$amount_min_range = $this->input->post('amount_min_range');
		$member_type = 'retailer';
		$commission = '0';
		$max_commission = '0';
		
		$r = $this->db_model->insert_update('micro_atm_commission_slab', array('slab'=>$name, 'updated_time'=>$date,'amount_max_range'=>$amount_max_range,'amount_min_range'=>$amount_min_range , 'member_type'=>$member_type, 'percent'=>$commission, 'maximum_comm'=>$max_commission));
		$id = $this->db->insert_id();
		if($r)
		{
			$this->session->set_flashdata('success','Package Created Successfully');
			redirect('admin/micro-atm-commission-package');
		}
		else
		{
			$this->session->set_flashdata('error','Package Creation Failed! Please try again later');
			redirect('admin/micro-atm-commission-package');
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
		$this->load->view('admin/update-recharge-status',$data);
	}
	
// 	public function update_recharge_status_success()
// 	{
// 		$rid = $this->input->post('id');
// 		$status = $this->input->post('status');
// 		$transaction = $this->input->post('transaction');
// 		$api = $this->input->post('api');
// 		$ip = $this->input->ip_address();
// 		$r = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$rid));
// 		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$r[0]->apiclid));
// 		$opd = $this->db_model->getwhere('operator',array('opcodenew'=>$r[0]->operator));
// 		$dis_cha = $this->rec_model->commi(array('cus_id' => $r[0]->apiclid, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
// 		$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $r[0]->apiclid, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
// 		$this->rec_model->call_back_status_update(array('cus_id'=>$r[0]->apiclid,'request_id'=>$r[0]->requestertxnid,'status'=>($status == '1' ? 'SUCCESS' : 'FAILED'),'number'=>$r[0]->mobileno,'opcode'=>$r[0]->operator,'amount'=>$r[0]->amount,'optxnid'=>$transaction));
// 		$com = $dis_cha['com']; 
// 		if($status == '1')
// 		{			
// 			if($r[0]->status == 'SUCCESS')
// 			{
// 				$recdata23 = array(
// 					'statusdesc'	=> $transaction,
// 					'serverresponse'	=>	$api
// 				);
// 				$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
// 				$this->session->set_flashdata('error','Already success');
// 				redirect('admin/update-recharge-status/'.$rid);
// 			}
// 			else
// 			{
// 				$type12 = $cus[0]->cus_type;
// 				if($type12 == 'retailer')
// 				{
// 					$recdata23 = array(
// 						'status'	=> "SUCCESS",
// 						'responsecode'	=> "SUCCESS",
// 						'statusdesc'	=> $transaction,
// 						'serverresponse'	=>	$api
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					
// 					$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 					$recdata63 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=> $rid,
// 						'txn_opbal'	=>	$row14[0]->txn_clbal,
// 						'txn_crdt'	=>	'0',
// 						'txn_dbdt'	=>	$r[0]->amount,
// 						'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->amount, 3),
// 						'txn_type'	=>	'recharge',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'recharge'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata63);
						
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => $com), array('recid' => $rid));
							
// 							$recdata3 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Retailer Commission',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Retailer Commission'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata3);
// 						}
// 					}
// 					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 					$this->rec_model->member_commission($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 					$recdata = $this->db_model->getAlldata("SELECT * FROM exr_rechrgreqexr_rechrgreq_fch WHERE apiclid = '".$r[0]->apiclid."' ORDER BY recid DESC LIMIT 1 ");
// 					$pl = round($recdata[0]->api - ($recdata[0]->retailer + $recdata[0]->distributor + $recdata[0]->master),3);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rid));
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
							
// 				else if($type12 == 'distributor')
// 				{
// 					$recdata23 = array(
// 						'status'	=> "SUCCESS",
// 						'responsecode'	=> "SUCCESS",
// 						'statusdesc'	=> $transaction,
// 						'serverresponse'	=>	$api
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
						
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor' => $com), array('recid' => $rid));
							
// 							$recdata3 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Distributor Commission',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Distributor Commission'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata3);
// 						}
// 					}
// 					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 					$this->rec_model->member_commission_distributor($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 				    $recdata = $this->db_model->getAlldata("SELECT * FROM exr_rechrgreqexr_rechrgreq_fch WHERE apiclid = '".$r[0]->apiclid."' ORDER BY recid DESC LIMIT 1 ");
// 					$pl = round($recdata[0]->api - ($recdata[0]->distributor + $recdata[0]->master),3);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rid));
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
				
							
// 				else if($type12 == 'master')
// 				{
// 					$recdata23 = array(
// 						'status'	=> "SUCCESS",
// 						'responsecode'	=> "SUCCESS",
// 						'statusdesc'	=> $transaction,
// 						'serverresponse'	=>	$api
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
						
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master' => $com), array('recid' => $rid));
							
// 							$recdata3 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Master Commission',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Master Commission'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata3);
// 						}
// 					}
// 					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 					$this->rec_model->member_commission_master($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 					$recdata = $this->db_model->getAlldata("SELECT * FROM exr_rechrgreqexr_rechrgreq_fch WHERE apiclid = '".$r[0]->apiclid."' ORDER BY recid DESC LIMIT 1 ");
// 					$pl = round($recdata[0]->api - ($recdata[0]->master),3);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => $pl), array('recid' => $rid));
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
				
// 				if($type12 == 'api')
// 				{
// 					$recdata23 = array(
// 						'status'	=> "SUCCESS",
// 						'responsecode'	=> "SUCCESS",
// 						'statusdesc'	=> $transaction,
// 						'serverresponse'	=>	$api
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					
// 					$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 					$recdata63 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=> $rid,
// 						'txn_opbal'	=>	$row14[0]->txn_clbal,
// 						'txn_crdt'	=>	'0',
// 						'txn_dbdt'	=>	$r[0]->amount,
// 						'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->amount, 3),
// 						'txn_type'	=>	'recharge',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'recharge'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata63);
						
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
				

				
// 				else
// 				{
// 					$data = array(
// 						'status'=>'SUCCESS',
// 						'statusdesc'	=> $transaction,
// 						'serverresponse'	=>	$api
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$data,array('recid'=>$rid));
// 						$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
// 			}
// 		}
		
// 		else
// 		{
// 			if($r[0]->status == 'FAILED')
// 			{
// 				$this->session->set_flashdata('error','Already failed');
// 				redirect('admin/update-recharge-status/'.$rid);
// 			}
			
// 			else if($r[0]->status == 'SUCCESS')
// 			{
// 				$type12 = $cus[0]->cus_type;
				
// 				if($type12 == 'retailer')
// 				{					
// 					$recdata23 = array(
// 						'status'	=> "REFUNDED",
// 						'profitloss' => '0'
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
// 					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
// 					$recdata23 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=> $rid,
// 						'txn_opbal'	=>	$row18[0]->txn_clbal,
// 						'txn_crdt'	=>	$r[0]->amount,
// 						'txn_dbdt'	=>	'0',
// 						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
// 						'txn_type'	=>	'Balance Refunded',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'Balance Refunded'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata23);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $rid));
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	'0',
// 								'txn_dbdt'	=>	$com,
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
// 								'txn_type'	=>	'Retailer Commission Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Retailer Commission Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					else
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Surcharge Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Surcharge Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 				    $apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 					$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
				
// 				else if($type12 == 'distributor')
// 				{					
// 					$recdata23 = array(
// 						'status'	=> "REFUNDED",
// 						'profitloss' => '0'
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
// 					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
// 					$recdata23 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=> $rid,
// 						'txn_opbal'	=>	$row18[0]->txn_clbal,
// 						'txn_crdt'	=>	$r[0]->amount,
// 						'txn_dbdt'	=>	'0',
// 						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
// 						'txn_type'	=>	'Balance Refunded',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'Balance Refunded'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata23);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor' => '0'), array('master' => '0'), array('recid' => $rid));
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	'0',
// 								'txn_dbdt'	=>	$com,
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
// 								'txn_type'	=>	'Distributor Commission Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Distributor Commission Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					else
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Surcharge Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Surcharge Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 					$this->rec_model->distributor_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
				
// 				else if($type12 == 'master')
// 				{					
// 					$recdata23 = array(
// 						'status'	=> "REFUNDED",
// 						'profitloss' => '0'
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
// 					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
// 					$recdata23 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=> $rid,
// 						'txn_opbal'	=>	$row18[0]->txn_clbal,
// 						'txn_crdt'	=>	$r[0]->amount,
// 						'txn_dbdt'	=>	'0',
// 						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
// 						'txn_type'	=>	'Balance Refunded',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'Balance Refunded'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata23);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master' => '0'), array('recid' => $rid));
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	'0',
// 								'txn_dbdt'	=>	$com,
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
// 								'txn_type'	=>	'Master Commission Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Master Commission Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					else
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Surcharge Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Surcharge Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 		//			$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
// 					$this->session->set_flashdata('msg','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}
				
// 				else
// 				{
// 					$recdata23 = array(
// 						'status'	=> "REFUNDED",
// 						'profitloss' => '0'
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
// 					$row18 = $this->db_model->get_trnx($r[0]->apiclid);

// 					$recdata23 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=> $rid,
// 						'txn_opbal'	=>	$row18[0]->txn_clbal,
// 						'txn_crdt'	=>	$r[0]->amount,
// 						'txn_dbdt'	=>	'0',
// 						'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
// 						'txn_type'	=>	'Balance Refunded',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'Balance Refunded'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata23);

// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('distributor' => '0'), array('master' => '0'), array('recid' => $rid));
										
// 					if ($dis_cha['comtype'] == '2') 
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	'0',
// 								'txn_dbdt'	=>	$com,
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal - $com, 3),
// 								'txn_type'	=>	'Commission Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Commission Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					else
// 					{
// 						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
// 						if($row14)
// 						{
// 							$recdata63 = array(
// 								'txn_agentid'	=> $r[0]->apiclid,
// 								'txn_recrefid'	=> $rid,
// 								'txn_opbal'	=>	$row14[0]->txn_clbal,
// 								'txn_crdt'	=>	$com,
// 								'txn_dbdt'	=>	'0',
// 								'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
// 								'txn_type'	=>	'Surcharge Back',
// 								'txn_time'	=>	time(),
// 								'txn_date'	=>	date('Y-m-d h:i:s'),
// 								'txn_checktime'	=>	$r[0]->apiclid.time(),
// 								'txn_ip'	=>	$ip,
// 								'txn_comment'	=>	'Surcharge Back'
// 							);
// 							$this->db_model->insert_update('exr_trnx',$recdata63);
// 						}
// 					}
// 					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
// 						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
// 					$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);					
// 				}
// 			}
// 			else
// 			{
// 				$type12 = $cus[0]->cus_type;
// 				if($type12 == 'retailer' || $type12 == 'distributor' || $type12 == 'master')
// 				{
// 					$recdata23 = array(
// 						'status'	=> "REFUNDED",
// 						'profitloss' => '0'
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));					

// 					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
// 					$recdata23 = array(
// 							'txn_agentid'	=> $r[0]->apiclid,
// 							'txn_recrefid'	=> $rid,
// 							'txn_opbal'	=>	$row18[0]->txn_clbal,
// 							'txn_crdt'	=>	$r[0]->amount,
// 							'txn_dbdt'	=>	'0',
// 							'txn_clbal'	=>	round($row18[0]->txn_clbal + $r[0]->amount, 3),
// 							'txn_type'	=>	'Balance Refunded',
// 							'txn_time'	=>	time(),
// 							'txn_date'	=>	date('Y-m-d h:i:s'),
// 							'txn_checktime'	=>	$r[0]->apiclid.time(),
// 							'txn_ip'	=>	$ip,
// 							'txn_comment'	=>	'Balance Refunded'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata23);
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);
// 				}	
// 				else
// 				{
// 					$data = array(
// 						'status'=>'REFUNDED',
// 						'profitloss' => '0'
// 					);
// 					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$data,array('recid'=>$rid));
// 					$trnx = $this->db->query("select * from exr_trnx where txn_agentid='".$r[0]->apiclid."' order by txn_id desc limit 1")->result();
// 					$clbal = $trnx[0]->txn_clbal;

// 					$recdata20 = array(
// 						'txn_agentid'	=> $r[0]->apiclid,
// 						'txn_recrefid'	=>	$rid,
// 						'txn_opbal'	=>	$trnx[0]->txn_clbal,
// 						'txn_crdt'	=>	$r[0]->amount,
// 						'txn_dbdt'	=>	'0',
// 						'txn_clbal'	=>	round($trnx[0]->txn_clbal + $r[0]->amount, 3),
// 						'txn_type'	=>	'Balance Refunded',
// 						'txn_time'	=>	time(),
// 						'txn_date'	=>	date('Y-m-d h:i:s'),
// 						'txn_checktime'	=>	$r[0]->apiclid.time(),
// 						'txn_ip'	=>	$ip,
// 						'txn_comment'	=>	'Balance Refunded'
// 					);
// 					$this->db_model->insert_update('exr_trnx',$recdata20);
// 					$this->session->set_flashdata('success','Update Successfully');
// 					redirect('admin/update-recharge-status/'.$rid);					
// 				}
// 			}
// 		}
// 	}
public function update_recharge_status_success()
	{
	   // echo "Hello";exit;
		$rid = $this->input->post('id');
		$status = $this->input->post('status');
// 		echo $status;exit;
		$transaction = $this->input->post('transaction');
		$api = $this->input->post('api');
		$ip = $this->input->ip_address();
		$r = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$rid));
		$cus_id=$r[0]->apiclid;
		$rectxnid= $rid;
		$amount5= $r[0]->amount;
		$qr_opcode = $r[0]->operator;
// 		echo $cus_id."<br>".$rectxnid."<br>".$amount5."<br>".$qr_opcode;exit;
		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$r[0]->apiclid));
		$opd = $this->db_model->getwhere('operator',array('opcodenew'=>$r[0]->operator));
// 		$dis_cha = $this->rec_model->commi(array('cus_id' => $r[0]->apiclid, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
// 		$apidis_cha = $this->rec_model->apicommi(array('cus_id' => $r[0]->apiclid, 'operator' => $r[0]->operator, 'amount' => $r[0]->amount));
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
				redirect('admin/update-recharge-status/'.$rid);
			}
			elseif($r[0]->status == 'FAILED')
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
					redirect('admin/update-recharge-status/'.$rid);
				}
							
				/*else if($type12 == 'distributor')
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
					redirect('admin/update-recharge-status/'.$rid);
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
					redirect('admin/update-recharge-status/'.$rid);
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
					redirect('admin/update-recharge-status/'.$rid);
				}*/
				

				
				else
				{
					$data = array(
						'status'=>'SUCCESS',
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$data,array('recid'=>$rid));
						$this->session->set_flashdata('success','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
				}
			}
			elseif($r[0]->status == 'PENDING'){
			    $type12 = $cus[0]->cus_type;
				if($type12 == 'retailer'){
				    $recdata23 = array(
						'status'	=> "SUCCESS",
						'responsecode'	=> "SUCCESS",
						'statusdesc'	=> $transaction,
						'serverresponse'	=>	$api
					);
					
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
				    $this->rec_model->creditRechargeCommission($cus_id,$amount5,$rectxnid,$qr_opcode);
					$this->session->set_flashdata('success','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
				}
			}
			
		}
		
		else
		{
		    if($r[0]->status == 'FAILED')
			{
			 //   echo"Hello"; exit;
				$this->session->set_flashdata('error','Already failed');
				redirect('admin/update-recharge-status/'.$rid);
			}
			
			else if($r[0]->status == 'SUCCESS')
			{
			 //   echo"Hello2"; exit;
				$type12 = $cus[0]->cus_type;
				
				if($type12 == 'retailer')
				{	
				    $recdata23 = array(
						'status'	=> "REFUNDED",
						'profitloss' => '0'
					);
					
				// 	print_r($recdata23); exit;
					
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch',$recdata23,array('recid'=>$rid));
					$row18 = $this->db_model->get_trnx($r[0]->apiclid);
				// 	echo"<pre>"; print_r($row18);exit;
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
				// 	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $rid));
					
					if ($r[0]->retailer!='0') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$r[0]->retailer,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->retailer, 3),
								'txn_type'	=>	'Retailer Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Retailer Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $rid));
						}
					}
					
					if ($r[0]->distributor!='0') 
					{
					    
						$row14 = $this->db_model->get_trnx($cus[0]->cus_reffer);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $cus[0]->cus_reffer,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$r[0]->distributor,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->distributor, 3),
								'txn_type'	=>	'Distributor Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Distributor Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor' => '0'), array('recid' => $rid));
						}
					}
					
					if ($r[0]->master!='0') 
					{
					    $cus_id=$cus[0]->cus_reffer;
		                $master_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_id'");
					    $master_cus_reffer = $master_reffer[0]->cus_reffer;
					   // $master_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_reffer'");
						$row14 = $this->db_model->get_trnx($master_cus_reffer);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $master_cus_reffer,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$r[0]->master,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->master, 3),
								'txn_type'	=>	'Master Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Master Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master' => '0'), array('recid' => $rid));
						}
					}
					
				// 	else
				// 	{
				// 		$row14 = $this->db_model->get_trnx($r[0]->apiclid);
				// 		if($row14)
				// 		{
				// 			$recdata63 = array(
				// 				'txn_agentid'	=> $r[0]->apiclid,
				// 				'txn_recrefid'	=> $rid,
				// 				'txn_opbal'	=>	$row14[0]->txn_clbal,
				// 				'txn_crdt'	=>	$com,
				// 				'txn_dbdt'	=>	'0',
				// 				'txn_clbal'	=>	round($row14[0]->txn_clbal + $com, 3),
				// 				'txn_type'	=>	'Surcharge Back',
				// 				'txn_time'	=>	time(),
				// 				'txn_date'	=>	date('Y-m-d h:i:s'),
				// 				'txn_checktime'	=>	$r[0]->apiclid.time(),
				// 				'txn_ip'	=>	$ip,
				// 				'txn_comment'	=>	'Surcharge Back'
				// 			);
				// 			$this->db_model->insert_update('exr_trnx',$recdata63);
				// 		}
				// 	}
				    // $apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
				// 		$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
				// 	$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
				// 	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
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
					/*else
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
					}*/
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->distributor_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
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
					$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('msg','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
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
					redirect('admin/update-recharge-status/'.$rid);					
				}
			}
			else if($r[0]->status == 'PENDING'){
			 //   echo "Hello";echo"<br>";
			    $type12 = $cus[0]->cus_type;
			 //   echo $type12;exit;
			    
			    if($type12 == 'retailer'){
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
				// 	$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $rid));
					
					if ($r[0]->retailer!='0') 
					{
						$row14 = $this->db_model->get_trnx($r[0]->apiclid);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $r[0]->apiclid,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$r[0]->retailer,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->retailer, 3),
								'txn_type'	=>	'Retailer Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Retailer Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer' => '0'), array('recid' => $rid));
						}
					}
					
					if ($r[0]->distributor!='0') 
					{
					    
						$row14 = $this->db_model->get_trnx($cus[0]->cus_reffer);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $cus[0]->cus_reffer,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$r[0]->distributor,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->distributor, 3),
								'txn_type'	=>	'Distributor Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Distributor Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor' => '0'), array('recid' => $rid));
						}
					}
					
					if ($r[0]->master!='0') 
					{
					    $cus_id=$cus[0]->cus_reffer;
		                $master_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_id'");
					    $master_cus_reffer = $master_reffer[0]->cus_reffer;
					   // $master_reffer = $this->db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_reffer'");
						$row14 = $this->db_model->get_trnx($master_cus_reffer);
						if($row14)
						{
							$recdata63 = array(
								'txn_agentid'	=> $master_cus_reffer,
								'txn_recrefid'	=> $rid,
								'txn_opbal'	=>	$row14[0]->txn_clbal,
								'txn_crdt'	=>	'0',
								'txn_dbdt'	=>	$r[0]->master,
								'txn_clbal'	=>	round($row14[0]->txn_clbal - $r[0]->master, 3),
								'txn_type'	=>	'Master Commission Back',
								'txn_time'	=>	time(),
								'txn_date'	=>	date('Y-m-d h:i:s'),
								'txn_checktime'	=>	$r[0]->apiclid.time(),
								'txn_ip'	=>	$ip,
								'txn_comment'	=>	'Master Commission Back'
							);
							$this->db_model->insert_update('exr_trnx',$recdata63);
							$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master' => '0'), array('recid' => $rid));
						}
					}
					
					$this->session->set_flashdata('success','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
			    }
			    
			    else if($type12 == 'distributor'){
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
					$apicoms = $this->getadminapicommission($r[0]->apisourceid,$r[0]->operator,$r[0]->amount);
						$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('api' => $apicoms), array('recid' => $rid));
					$this->rec_model->distributor_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('success','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
			    }
			    
			    else if($type12 == 'master'){
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
					$this->rec_model->member_commission_back($r[0]->amount,$r[0]->operator,$r[0]->apiclid,$rid);
					$this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('profitloss' => '0'), array('recid' => $rectxnid));
					$this->session->set_flashdata('msg','Update Successfully');
					redirect('admin/update-recharge-status/'.$rid);
			    }
			    
			    else{
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
					redirect('admin/update-recharge-status/'.$rid);	
			    }
			}
			else
			{
				// echo"Hello3"; exit;
				$this->session->set_flashdata('error','Already Refunded');
				redirect('admin/update-recharge-status/'.$rid);
			}
		}
	}
	
	public function news(){
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['news'] = $this->db_model->getAlldata("SELECT * FROM news ORDER BY news_id DESC");
		    $this->load->view('admin/news',$data);
		}
	}
	
	public function delete_news($id)
	{
		
		$this->db->delete('news', array('news_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','News Deleted Successfully');
		redirect('admin/news');
	
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
		redirect('admin/news');
	}


    public function aeps_users(){
        
        if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
        }else{
		    $data['aepsUser'] = $this->db_model->getAlldata("SELECT * FROM customers where aeps_AccountNumber!='' ORDER BY cus_id DESC");
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM aeps_commission_slab");
		    $data['atmpackage'] = $this->db_model->getAlldata("SELECT * FROM micro_atm_commission_slab");
            $this->load->view('admin/aeps_users',$data);
        }
    }

    public function aeps_transaction(){
        
  	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
        }else{
		    $data['aepsUser'] = $this->db_model->getAlldata("SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  ORDER BY aeps_id DESC");
            $this->load->view('admin/aeps_reports',$data);
        }
  	}
  	
  	public function search_aeps_transaction()
	{
	    
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno and  " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(at.aeps_date_time) >= '".$from.' 00:00:00'."' and DATE(at.aeps_date_time) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(at.aeps_date_time) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(at.aeps_date_time) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " at.aeps_id != '' ORDER BY aeps_id DESC";
		    
		    $data['aepsUser'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/aeps_reports',$data);
		}
	}
  	
  	public function aepsPackage_assign(){
  	    
  	    $pk_id = $this->input->post('pk_id');
  	    $cus_id = $this->input->post('cus_id');
  	    $atm_id = $this->input->post('atm_id');
  	    if(isset($pk_id) && isset($atm_id)){
  	    $data = array('aeps_comm_id'=>$pk_id,'atm_comm_id'=>$atm_id);
  	    }
  	    if(isset($pk_id) && !isset($atm_id)){
  	    $data = array('aeps_comm_id'=>$pk_id);
  	    }
  	    if(!isset($pk_id) && isset($atm_id)){
  	    $data = array('atm_comm_id'=>$atm_id);
  	    }
    	$res = $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
    	if($res){
    	    $this->session->set_flashdata('success','Updated Successfully');
    	    redirect('admin/aeps_users');
    	}else{
    	    $this->session->set_flashdata('error','Failed To  Update');
    	    redirect('admin/aeps_users');
    	}
  	    
  	}
  	
  	public function pancoupon_history(){
  	    
  	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
        }else{
		    $data['coupons'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,customers as c,coupon_price as cp where cb.vle_id = c.cus_id and cb.type = cp.coupon_price_id   ORDER BY coupon_buy_id DESC");
            $this->load->view('admin/pancoupon_history',$data);
        }
  	}
  	
  	
  	public function search_pancoupon_history()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM coupon_buy as cb,customers as c,coupon_price as cp where cb.vle_id = c.cus_id and cb.type = cp.coupon_price_id and  " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(cb.added_date) >= '".$from.' 00:00:00'."' and DATE(cb.added_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(cb.added_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(cb.added_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " cb.coupon_buy_id != '' ORDER BY coupon_buy_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/pancoupon_history',$data);
		}
	}
  	
  	public function micro_atm(){
  	    
  	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
        }else{
		    $data['micro'] = $this->db_model->getAlldata("SELECT * FROM micro_atm as ma , customers as c where ma.cus_id = c.cus_id and DATE(ma.ma_date_time) >= '".date('Y-m-d').' 00:00:00'."' and DATE(ma.ma_date_time) <= '".date('Y-m-d').' 00:00:00'."' ORDER BY ma_id DESC");
            $this->load->view('admin/microAtm_report',$data);
        }
  	}
    
    public function search_micro_atm()
	{
	    
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM micro_atm as ma , customers as c where ma.cus_id = c.cus_id and  " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(ma.ma_date_time) >= '".$from.' 00:00:00'."' and DATE(ma.ma_date_time) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(ma.ma_date_time) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(ma.ma_date_time) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " ma.ma_id != '' ORDER BY ma_id DESC";
		    $data['micro'] = $this->db_model->getAlldata($sql);
		    $this->load->view('admin/microAtm_report',$data);
		}
	}

    public function coupon_price(){
        
        if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
        }else{
            $data['coupon'] = $this->db_model->getAlldata("SELECT * FROM coupon_price");
            $this->load->view('admin/coupon_price',$data);
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
    	    redirect('admin/coupon_price');
    	}else{
    	    $this->session->set_flashdata('error','Failed To  Update');
    	    redirect('admin/coupon_price');
    	}
        
    }
    
    public function bank_details()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['r'] = $this->db_model->getwhere('bank_details',array('cus_id'=>'0'));
		    //echo $this->db->last_query();exit;
		    $this->load->view('admin/bank-details',$data);
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
					'cus_id'  => '0',
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
		redirect('admin/bank-details');	
	}
	
	public function delete_bank_details($id)
	{
		$this->db_model->delete('bank_details',array('bank_id'=>$id));
		$this->session->set_flashdata('success','Successfully deleted..');
		redirect('admin/bank-details');
	}
  	
  	public function update_bank_details($id)
	{
		$data['bank'] = $this->db_model->get_newdata('bank_details',array('account_type'=>'1','bank_id'=>$id,'cus_id'=>'0'));
		if(!empty($data['bank']))
		{
			$this->load->view('admin/update-bank-details',$data);
		}
		else
		{
			redirect('admin/bank-details');
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
			$r = $this->db_model->get_newdata('bank_details',array('account_type'=>'1','cus_id'=>'0','bank_id'=>$this->input->post('id')));
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
		redirect('admin/bank-details');	
	}
	
	
/*	public function service(){
  	    
  	    $data['service'] = $this->db_model->getAllData("SELECT * FROM service");
  	    $this->load->view('admin/service',$data);
  	}*/
  	
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
  	    
  	}




	public function logout()
	{
	    $this->session->unset_userdata('isLoginAdmin');
		//$this->load->view('admin/index');
		redirect('admin');
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
            redirect('admin/addbeneficery');
        }
    }*/
    
    /*public function creditRechargeCommission($cus_id,$amount,$rec_id,$opcode){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
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
    }*/
    
    public function creditPanCommission($cus_id,$amount,$coupon_buy_id){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_pancard WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
                if($commission_scheme_package){
                    if($cus_type == "retailer"){
                        $retailer_charge = $commission_scheme_package[0]->retailer_comm;
                        
                        if($retailer_charge){
                            $last_balance = $this->db_model->get_trnx($cus_id);
        					if($last_balance)
        					{
        						$txntype = "Retailer Pan Charge";
        						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$retailer_charge, 'txn_clbal'=>$last_balance[0]->txn_clbal-$retailer_charge, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        					}
                        }
                        
                        $getParentDistributor = $commission_scheme_id[0]->cus_reffer;
                        $distributor_comm = $commission_scheme_package[0]->distributor_comm;
                        if($distributor_comm){
                            $last_balance = $this->db_model->get_trnx($getParentDistributor);
    						if($last_balance)
    						{
        						$txntype = "Distributor Pan Card Commission";
        						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentDistributor,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$distributor_comm, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $distributor_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}else{
    						    $txntype = "Distributor Pan Card Commission";
        						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentDistributor,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$distributor_comm, 'txn_dbdt'=>'0', 'txn_clbal'=> $distributor_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}
                        }
                        
                        if($getParentDistributor){
                            
                            $parentMaster = $this->db_model->getAllData("SELECT cus_reffer from customers WHERE cus_id='$getParentDistributor'");
                            $getParentMaster = $parentMaster[0]->cus_reffer;
                            if($getParentMaster){
    
                                $master_comm = $commission_scheme_package[0]->master_comm;
                                if($master_comm){
                                    $last_balance = $this->db_model->get_trnx($getParentMaster);
            						if($last_balance)
            						{
                						$txntype = "Master Pan Card Commission";
                						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentMaster,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$master_comm, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $master_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            						}else{
            						    $txntype = "Master Pan Card Commission";
                						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentMaster,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$master_comm, 'txn_dbdt'=>'0', 'txn_clbal'=> $master_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
                                    $last_balance = $this->aeps_model->aepsBalance($cus_id);
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
                                        $last_balance = $this->aeps_model->aepsBalance($cus_reffer);
                                        
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
                                       $last_balance = $this->aeps_model->aepsBalance($master_cus_reffer);
                                        
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
    
     public function creditDmtCommission($cus_id,$amount,$dmt_txn_recrefid){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_dmt WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
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
                                    $last_balance = $this->db_model->get_trnx($cus_id);
                                    $this->db_model->insert_update('dmt_trnx', array('retailer_commission'=>$commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
            						if($last_balance)
            						{
                						$txntype = "Retailer Dmt Commission";
                						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            						}else{
            						    $txntype = "Retailer Dmt Commission";
                						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            						}
            							
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
                                        $last_balance = $this->db_model->get_trnx($cus_reffer);
                                        $this->db_model->insert_update('dmt_trnx', array('distributor_commission'=>$dist_commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
                						if($last_balance)
                						{
                    						$txntype = "Distributor Dmt Commission";
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}else{
                						    $txntype = "Distributor Dmt Commission";
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}
                							
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
                                        $last_balance = $this->db_model->get_trnx($master_cus_reffer);
                                        $this->db_model->insert_update('dmt_trnx', array('master_commission'=>$mast_commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
                						if($last_balance)
                						{
                    						$txntype = "Master Dmt Commission";
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$master_cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}else{
                						    $txntype = "Master Dmt Commission";
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$master_cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
    
    public function creditMicroAtmCommission($cus_id){
        $amount=100;$aeps_id=1;
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
	
	
	public function redeemRequest(){
	    
	    if(isset($_POST['redeem_transaction']))
		{
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
		    $id=$_POST['pay_req_id'];
		    if($id){
    		    for($i=0;$i<count($id);$i++){
    		        $ope = $this->db_model->getWhere('payout_request',array('pay_req_id'=>$id[$i]));
        			foreach($ope as $operator)
        			{
        			    $TXNTYPE = 'IFS';
        				$UNIQUEID=rand(000000000000000,999999999999999);
        				$DEBITACC=$operator->bankAccount;
        				$IFSC=$operator->bankIFSC;
        				$AMOUNT='10';$operator->amount+$operator->charge;
        				$PAYEENAME=$operator->accountHolderName;
        				$bankName=$operator->bankName;
        				if($bankName == 'ICICI' || $bankName == 'ICICI BANK' ){
                	        $TXNTYPE = 'TPA';
                	        $IFSC = 'ICIC0000011';
                	    }
                        $data = "msg=payout~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$TXNTYPE~$UNIQUEID~$DEBITACC~$IFSC~$AMOUNT~$PAYEENAME~$bankName";
                	    $url= 'https://edigitalvillage.net/index.php/api_partner/payoutRequest';
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
                            $result=json_decode($server_output);
                            if($result->STATUS == 'pending'){
                                $resdata=array('status'=>'PENDING','date'=>date('Y-m-d h:s:i'),'UNIQUEID'=>$UNIQUEID,'response'=>$server_output);
                				$updata=array('pay_req_id'=>$id[$i]);
                				$this->db_model->insert_update('payout_request',$resdata,$updata);
            				    $this->session->set_flashdata('error',$result->MSG);
                            }else{
                                $this->session->set_flashdata('error',$server_output);
                                redirect('admin/redeem_request_report');
                            }
                        }else{
                            $this->session->set_flashdata('error','Unable To get Response');
                            redirect('admin/redeem_request_report');
                        }
    				}
    			}
		    }else{
		        $this->session->set_flashdata('error','Please select Transaction');
		        redirect('admin/redeem_request_report');
		    }
	    }
	    redirect('admin/redeem-request-report');
    }
    
	
	public function transactionEnquiryApi($UNIQUEID)
	{
	    $api_mobile = $this->api_mobile;
        $api_pass = $this->api_pass;
        $api_user_id = $this->api_user_id;
        $api_reg_ip = $this->api_reg_ip;
        $data = "msg=transactionStatus~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$UNIQUEID";
	    $url= 'https://edigitalvillage.net/index.php/api_partner/payoutRequest';
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
            $result=json_decode($server_output);
    		if($result){
    		    $response = $result;
    		    $status = $response->STATUS;
    		    if($status == "REJECTED"){
    		        $status = "REJECTED";
                    $payoutDetails = $this->db_model->getWhere('payout_request',array('UNIQUEID'=>$UNIQUEID));
                    if($payoutDetails[0]->status == "PENDING"){
                        $amount = $payoutDetails[0]->amount;
                        $charge = $payoutDetails[0]->charge;
                        $cus_id = $payoutDetails[0]->cus_id;
                        
                         $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                        
                        $txntype = 'Redeem Back';
                        $total = $cus_aeps_bal + $amount;
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$amount,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                        
                        $cus_aeps_bal_data = $this->aepsBalance($cus_id);
                        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                        
                		$txntype = 'Redeem Charge Back';
                		$ip = $_SERVER['REMOTE_ADDR'];
                		$date = DATE('Y-m-d h:i:s');
                		$total = $cus_aeps_bal + $charge;
                		
                		$lasttxnrec = $this->db_model->insert('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$charge,'aeps_txn_dbdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                    }
    		        $this->session->set_flashdata('error',"Payout Request is Rejected");
    		    }else{
    		        $status = $response->STATUS;
    		        $this->session->set_flashdata('success',"Payout Request is $status");
    		    }
    		    $resdata=array('status'=>"$status",'date'=>date('Y-m-d h:s:i'),'UNIQUEID'=>$UNIQUEID,'UTRNUMBER'=>$response->UTRNUMBER,'response'=>$server_output);
                $updata=array('UNIQUEID'=>$UNIQUEID);
            	$this->db_model->insert_update('payout_request',$resdata,$updata);
            	
    		}else{
    	        $this->session->set_flashdata('error',"Please Try again Later");
    		}
        }else{
            $this->session->set_flashdata('error',$server_output);
        }
       	redirect('admin/redeem-request-report');
                
	}
	
	
	
	public function creditRechargeCommission($cus_id='23',$amount='21',$rec_id='11',$opcode='141'){
	    //Testing function 
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
                            echo "Retailer :- ".$commission_amount.'<br><br>';
                            /*$last_balance = $this->db_model->get_trnx($cus_id);
                            $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('retailer'=>$commission_amount),array('recid'=>$rec_id));
    						if($last_balance)
    						{
        						$txntype = "Retailer Commission";
        						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}else{
    						    $txntype = "Retailer Commission";
        						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}*/
    							
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
                                
                                echo "Distributor :- ".$dist_commission_amount.'<br><br>';
                                /*$last_balance = $this->db_model->get_trnx($cus_reffer);
                                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('distributor'=>$dist_commission_amount),array('recid'=>$rec_id));
        						if($last_balance)
        						{
            						$txntype = "Distributor Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}else{
        						    $txntype = "Distributor Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}*/
        							
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
                                    echo $mast_commission = round((($master_comm / 100) * $amount),3);
                                    $mast_commission_amount = $mast_commission - ($dist_commission_amount + $commission_amount);
                                }else{
                                    $mast_commission_amount = $master_comm - ($dist_commission_amount + $commission_amount);
                                }
                                /*$last_balance = $this->db_model->get_trnx($master_cus_reffer);
                                $this->db_model->insert_update('exr_rechrgreqexr_rechrgreq_fch', array('master'=>$mast_commission_amount),array('recid'=>$rec_id));
                                */
                                 echo "Master :- ".$mast_commission_amount.'<br><br>';
        						/*if($last_balance)
        						{
            						$txntype = "Master Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$master_cus_reffer,'txn_date'=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}else{
        						    $txntype = "Master Commission";
            						$this->db_model->insert_update('exr_trnx', array('txn_recrefid'=>$rec_id,'txn_agentid'=>$master_cus_reffer,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        						}*/
        							
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
	
	public function view_all_api()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['api'] = $this->db_model->getwhere('apisource');
		    $this->load->view('admin/view-all-api',$data);
		}
	}
	
	public function add_api()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $this->load->view('admin/add-api');
		}
	}
	
	public function add_api_succ()
	{
	    $apiname = $this->input->post('apisourcename');
	  	$totalarray = array(
  			'apisourcecode'	=>	$this->input->post('apisourcename'),
			'apisourcename'	=>	$this->input->post('apisourcename'),
			'bal_api_url'	=>	$this->input->post('bal_api_url'),
			'api_url'	=>	$this->input->post('api_url'),
			'api_operator'	=>	$this->input->post('operator'),
			'api_amount'	=>	$this->input->post('amount'),
			'api_mobile'	=>	$this->input->post('mobile'),
			'api_txn_id'	=>	$this->input->post('txn_id'),
			'api_optional'	=>	$this->input->post('optional'),
			'succ_resp'	=>	$this->input->post('succ_resp'),
			'fail_resp'	=>	$this->input->post('fail_resp'),
			'pending_resp'	=>	$this->input->post('pending_resp'),
			'api_resp_type'	=>	$this->input->post('api_resp_type'),
			'api_hit_type'	=>	$this->input->post('api_hit_type'),
			'op_txid_resp'	=>	$this->input->post('op_txid_resp'),
			'status_resp'	=>	$this->input->post('status_resp'),
			'api_authenticate' => $this->input->post('authenticate')
  		);
	
  		$r = $this->db_model->insert_update('apisource',$totalarray);
  		
  		$sql = $this->db->query("ALTER TABLE operator ADD $apiname VARCHAR(50);");
  		$sql = $this->db->query("ALTER TABLE priority_api ADD $apiname VARCHAR(50) DEFAULT '0';");
  		
	  	$this->session->set_flashdata('item','Added Successfully');
	  	redirect('admin/view-all-api');
	}
	
	
	public function update_api($id)
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['api'] = $this->db_model->getAlldata("select * from `apisource` where apisourceid=$id");
		    $this->load->view('admin/update-api',$data);
		}
	}
	
		public function update_api_succ()
	{
	    
	    $apisourceid = $this->input->post('apisourceid');
	  	$totalarray = array(
  			'apisourcecode'	=>	$this->input->post('apisourcename'),
			'apisourcename'	=>	$this->input->post('apisourcename'),
			'bal_api_url'	=>	$this->input->post('bal_api_url'),
			'api_url'	=>	$this->input->post('api_url'),
			'api_operator'	=>	$this->input->post('operator'),
			'api_amount'	=>	$this->input->post('amount'),
			'api_mobile'	=>	$this->input->post('mobile'),
			'api_txn_id'	=>	$this->input->post('txn_id'),
			'api_optional'	=>	$this->input->post('optional'),
			'succ_resp'	=>	$this->input->post('succ_resp'),
			'fail_resp'	=>	$this->input->post('fail_resp'),
			'pending_resp'	=>	$this->input->post('pending_resp'),
			'api_resp_type'	=>	$this->input->post('api_resp_type'),
			'api_hit_type'	=>	$this->input->post('api_hit_type'),
			'op_txid_resp'	=>	$this->input->post('op_txid_resp'),
			'status_resp'	=>	$this->input->post('status_resp'),
			'api_authenticate' => $this->input->post('authenticate')
  		);
	
  		$r = $this->db_model->insert_update('apisource',$totalarray,array('apisourceid' => $apisourceid));
  		// echo $this->db->last_query();exit;
	  	$this->session->set_flashdata('item','Updated Successfully');
	  	redirect('admin/view-all-api');
		
				 
	}
	
	public function view_all_api_operator($id)
	{
		$r = $this->db_model->getAlldata("select * from `apisource` where apisourceid=$id");
		$name = $r[0]->apisourcecode;
		$data['v'] = $this->db_model->getAlldata("select * from `apisource` where apisourceid=$id");
		$data['r'] = $this->db_model->getAlldata("select operatorname,$name,opsertype,opid from `operator`");
	    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers ORDER BY cus_id DESC");
	    $this->load->view('admin/view-all-api-operator',$data);
	}
	
	public function whatsapp(){
	    $this->msg_model->whatsapp_sms('8669474802','Testing');
	}
	
	public function ulrhit(){
	    $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?apikey=57814711b8193ea873059df35549ec93&tel=7030512346";  
        //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $content = curl_exec($ch);
        curl_close($ch); 
	    $data2=json_decode($content, true);
	    echo "<pre>";
	    $a = $data2['records'];
	    $opname = $a['Operator'];
        $d = $this->Db_model->getAlldata("select * from operator where operatorname='$opname'");
        
        $dd = array_merge($data2,$d);
        // print_r($dd);exit;
        return $dd;
	}
	
	
	public function apiopcode_update()
	{
		$operatorid = $this->input->post('operatorid');
		$name = $this->input->post('apisourceid');
		$opcode = $this->input->post('opcode');
    		
		$query = $this->db_model->insert_update('operator', array($name => $opcode), array('opid' => $operatorid));
		
		$data = $this->db->last_query();
		
		if ($this->db->affected_rows() > 0) {
			$p = " <div class=\"alert alert-success\" style=\"margin-left:0px;\">
                                <h4>Success!</h4>
                                <p>Oprator Updated Successfuly</p>
                            </div>";
			echo json_encode(array('mess' => $p,'date'=>DATE('d M, Y h:i:s',now())));
		} else {
			
			$p = " <div class=\"alert alert-danger\" style=\"margin-left:0px;\">
                                <h4>Error!</h4>
                                <p>Please Try Again Later. </p>
                            </div>";
			echo json_encode(array('mess' => $p,'date'=>DATE('d M, Y h:i:s',now())));
		}
	}
	
	public function update_recharge1()
	{
	    $id = $this->input->post('id');
	    $per = $this->input->post('per');	
	    $columnname = $this->input->post('column');	
	    $time = date("Y-m-d h:i:s");		  
	    $this->db_model->insert_update('customers',array($columnname=>$per),array('cus_id'=>$id));
	    echo "Updated Successfully";
	}
	
	public function enquiry(){
	        
        $data['r'] = $this->db_model->getAlldata('select * from register_enquiry order by register_enquiry_id desc');
	    $this->load->view('admin/enquiry',$data);
	}
	

	public function delete_range_wise_api($id)
	{
		$this->db->delete('amount_apisources', array('amount_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Package Deleted Successfully');
		redirect('admin/amount-wise-api-switching');
	}
	
	public function put(){
	    
	    
        $data = file_get_contents('php://input');
	    $logfile = 'gateway_responselog.txt';
	    $log = "\n\n".'GUID - '.time()."================================================================ \n";
        $log .= 'Callback Get Param -  TRANS ID'.$_GET['trx_id']."|| CUS ID ".$_GET['cus_id']."\n\n";
        $log .= 'STATUS RESPONSE - '.json_encode($data)."\n\n";
        file_put_contents($logfile, $log, FILE_APPEND | LOCK_EX);
	}
	public function test()
	{
	    $data =$this->db_model->getAlldata("select cus_id from customers");
	    //print_r($data);exit;
	    foreach($data as $rec)
	    {
	       // print_r($rec->cus_id);exit;
	        $this->db_model->insert_update('application_status_service',array('cus_id'=>$rec->cus_id));
	    }
	}
	public function testnot(){
	    $message = 'test';
	    $title = 'vampire on testing';
	    $cus_id = '366';
	    $PayerAmount = 'rutuja';
	    //$this->Pushnotification_model->push_notification($message,$title,$cus_id);
	    $this->Pushnotification_model->push_notification_upi_collection($message,$title,$cus_id,$PayerAmount);
	}
	
	public function offers(){
    if($this->session->userdata('isLoginAdmin') == FALSE){
        redirect('/admin_login');
	}else{
	    $data['offer'] = $this->db_model->getAlldata("SELECT * FROM offers ORDER BY offer_id DESC");
	    $this->load->view('admin/offers',$data);
		}
	}
	public function delete_offer($id)
	{
		
		$this->db->delete('offers', array('offer_id'=>$id));
		$msg = "<strong>Delete!</strong> Successfully";
		$this->session->set_flashdata('success','Offer Deleted Successfully');
		redirect('admin/offers');
	
	}
	public function add_offer()
	{
// 		$description = $this->input->post('description');	
		$date = DATE('Y-m-d h:i:s');

// 		$newstype = implode(',', $type);
        
        
        if($_FILES['banner_image']['name'])
		{
			$config['upload_path']		= 'uploads/';
			$config['allowed_types']	= 'jpg|jpeg|png|gif';
			
		
			$update_data="";
			$this->upload->initialize($config);
				
			if(!$this->upload->do_upload('banner_image')){
				$error=array('error' => $this->upload->display_errors());
				// print_r($error);
			    $this->session->set_flashdata('error','Invalid File');
			}else{
			    $path = 'https://newmitra.in/uploads/'.$_FILES['banner_image']['name'];
				$get_image_data=array('upload_image'=> $this->upload->data());
				$update_data=$get_image_data['upload_image']['file_name'];
    			
			}	
		}
        
        
		$r = $this->db_model->insert_update('offers',array('banner_image'=> 'https://newmitra.in/uploads/'.$update_data,'amount'=>$amount=$this->input->post('amount'),'datetime'=>$date,'title'=>$this->input->post('offer_title'),'weblink'=>$this->input->post('offer_weblink'),'description'=>$this->input->post('description')));
		$this->session->set_flashdata('success','Offer Added Successfully');
		redirect('admin/offers');
	}
	public function update_offer($offer_id){
	   // echo $offer_id; exit;\
    if($this->session->userdata('isLoginAdmin') == FALSE){
        redirect('/admin_login');
	}else{
	    $data['offer'] = $this->db_model->getAlldata("SELECT * FROM offers where offer_id=$offer_id");
	    $this->load->view('admin/update_offer',$data);
		}
	}
	public function offer_details_update(){
		$offer_id= $this->input->post('offer_id');	
		$amount=$this->input->post('amount');	
		$description = $this->input->post('description'); 
	    $title=$this->input->post('title');
		$weblink = $this->input->post('weblink');
		
	   // echo $_FILES['banner_image']['name']; exit;
        if($_FILES['banner_image']['name'])
		{
			$config['upload_path']		= 'uploads/';
			$config['allowed_types']	= 'jpg|jpeg|png|gif';
			
		
			$update_data="";
			$this->upload->initialize($config);
				
			if(!$this->upload->do_upload('banner_image')){
				$error=array('error' => $this->upload->display_errors());
				// print_r($error);
			    $this->session->set_flashdata('error','Invalid File');
			}else{
			    $path = 'https://newmitra.in/uploads/'.$_FILES['banner_image']['name'];
				$get_image_data=array('upload_image'	=> $this->upload->data());
				$update_data=$get_image_data['upload_image']['file_name'];
    			
			}	
		}
        
        $data = array(
                    'amount' => $amount,
					'description'  =>$description,
					'title' =>$title,
					'weblink' =>$weblink,
					'banner_image'=> 'https://newmitra.in/uploads/'.$update_data,
				);
				// print_r($data); exit;
				$this->db_model->insert_update('offers',$data,array('offer_id'=>$this->input->post('offer_id')));
				$this->session->set_flashdata('success','Offer Updated Successfully');
		redirect('admin/offers');
             }
	
	public function view_offer_purchase_history()
	{
	    if($this->session->userdata('isLoginAdmin') == FALSE){
            redirect('/admin_login');
		}else{
		    $data['orders'] = $this->db_model->getAlldata("SELECT * FROM `puchaseOffer` JOIN customers ON customers.cus_id=puchaseOffer.cus_id JOIN app_offers ON app_offers.offer_id=puchaseOffer.offer_id ORDER BY puchaseOffer.purchase_id DESC");
		  //  print_r($data['orders']);exit;
		    $this->load->view('admin/view-purchase-history',$data);
		}
	}
	
	public function approveorder(){
        $id = $this->input->post('id');
	    $date = $this->input->post('date');
        $this->db_model->insert_update('puchaseOffer',array('purchase_status'=>'APPROVED','delivery_date'=>$date),array('purchase_id'=>$id)); 
        $this->session->set_flashdata('success','Status Updated Successfully');
        redirect('admin/view-offer-purchase-history');
    }
    
    public function deliver_order()
	{
	    $id = $this->input->post('id');
	    $date = $this->input->post('date');
        $this->db_model->insert_update('puchaseOffer',array('purchase_status'=>'DELIVERED','delivered_to'=>$date),array('purchase_id'=>$id)); 
        // echo $this->db->last_query();exit;
        $this->session->set_flashdata('success','Status Updated Successfully');
        redirect('admin/view-offer-purchase-history');
	}
	
	public function RefundOfferAmount()
    {
        $cus_id = $this->input->post('cusid');
        $amt = $this->input->post('amt');
        $mas_comm = $this->input->post('mas_comm');
        $dist_comm = $this->input->post('dis_comm');
        $purchase_id = $this->input->post('id');
        $reason = $this->input->post('reason');
        
        $retailer_data = $this->Db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        if($retailer_data[0]->cus_reffer) $dis_id = $retailer_data[0]->cus_reffer; else $dis_id = '0';
        if($dis_id != '0'){
            if($dist_comm > 0){
                $last_balance1 = $this->Db_model->get_trnx($dis_id);
                $txntype = 'Refund Purchase Offer Distributor Commission';
                $this->Db_model->insert_update('exr_trnx', array('product_purchase_id'=>$purchase_id,'txn_agentid'=>$dis_id,'txn_date'=>date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance1[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$dist_comm, 'txn_clbal'=>$last_balance1[0]->txn_clbal - $dist_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            }    
        }
        $dis_data = $this->Db_model->getAllData("SELECT cus_reffer FROM customers WHERE cus_id = '$dis_id'");
        if($dis_data[0]->cus_reffer) $mas_id = $dis_data[0]->cus_reffer; else $mas_id = '0';
        if($mas_id != '0')
        {
            if($mas_comm > 0){
                $last_balance2 = $this->Db_model->get_trnx($mas_id);
                $txntype = 'Refund Purchase Offer Master Commission';
                $this->Db_model->insert_update('exr_trnx', array('product_purchase_id'=>$purchase_id,'txn_agentid'=>$mas_id,'txn_date'=>date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance2[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$mas_comm, 'txn_clbal'=>$last_balance2[0]->txn_clbal - $mas_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            }    
        }
        $last_balance3 = $this->Db_model->get_trnx($cus_id);
        $txntype1 = 'Refund Purchase Offer';
        $this->Db_model->insert_update('exr_trnx', array('product_purchase_id'=>$purchase_id,'txn_agentid'=>$cus_id,'txn_date'=>date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance3[0]->txn_clbal, 'txn_crdt'=>$amt, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance3[0]->txn_clbal + $amt, 'txn_type'=>$txntype1, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype1));
        $this->Db_model->insert_update('puchaseOffer', array('reason'=>$reason,'purchase_status'=>'REJECTED'),array('purchase_id'=>$purchase_id));
        $this->session->set_flashdata('success','Purchase Offer Deleted Successfully');
        redirect('admin/view-offer-purchase-history');
    }
	
	public function application_status(){
	        
        $data['members'] = $this->db_model->getAlldata('select * from application_status_service as a join customers as c on a.cus_id = c.cus_id order by a.id desc');
        //SELECT * FROM customers as c join emp_rights as er on c.cus_id = er.cus_id WHERE c.cus_type='employee' ORDER BY c.cus_id DESC
	    $this->load->view('admin/application_status',$data);
	}
	
	public function update_emp_permission()
	{
	    $id = $this->input->post('id');
	    $per = $this->input->post('per');	
	    $columnname = $this->input->post('column');	
	    $time = date("Y-m-d h:i:s");		  
	    $this->db_model->insert_update('application_status_service',array($columnname=>$per),array('id'=>$id));
	    echo "Application Status Updated Successfully";
	}
	public function application_request(){
	        
        $data['r'] = $this->db_model->getAlldata('select * from application_service_purchase as a join customers as c on a.cus_id = c.cus_id order by id desc');
	    $this->load->view('admin/application_request',$data);
	}
	public function application_request_approved()
	{
	    $id =$this->input->post('id');
	    $application_name = $this->input->post('appname');
	    $cus_id = $this->input->post('cusid');
	    $application_id=$this->input->post('application_id');
	    $status = 'Approved';
	    $time = date("Y-m-d h:i:s");	

		$update_rows = array('application_id' => $application_id, 'status' => $status, 'update_datetime' => $time );
		$multipleWhere = array('cus_id' => $cus_id, 'appname' => $application_name );

		$this->db->where($multipleWhere);		
		$res = $this->db->update('application_service_purchase', $update_rows);	
		//echo $this->db->last_query();
		//print_r($res);exit;
		
    	//$res=$this->db_model->insert_update('application_service_purchase',array('application_id'=>$application_id,'status'=>'Approved','update_datetime'=>$time),array('cus_id'=>$cus_id));
        if($res)
        {
    	    $msg2 = "<span style='color:green;font-size:18px;font-weight:bold;margin-left:20px;'>Application Approved Successfully</span>";
		    $this->session->set_flashdata('success',$msg2);
        }
        else
        {
            $this->session->set_flashdata('error',$msg);
        }
		redirect('admin/application_request');
	}
}
