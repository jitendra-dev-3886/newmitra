<?php if(!defined('BASEPATH')) exit('Hacking Attempt : Get Out of the system ..!');
class Db_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('encryption_model');
	}
	
	public function getSecretKey()
	{
		$this->db->select('*');
		$this->db->from('user');
		$this->db->where('type','admin');
		$query = $this->db->get();
		//print_r($query->result_array());
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	public function getwhere($tablename,$where=0)
	{
		if(is_int($where))
		{
			$query = $this->db->get($tablename);
		}
		else
		{
			$query = $this->db->get_where($tablename,$where);
		}
		return $query->result();


	}
	
	public function insert_update($tablename,$data,$id=0)
	{
		if($id)
		{
			$this->db->where($id);
			return  ($this->db->update($tablename,$data))?1:0;
			//echo $this->db->get_compiled_update();
		}
		else
		{
			$this->db->set($data);
			return $this->db->insert($tablename);
			//echo $this->db->last_query();
			//die;
		}

    }
    
    public function login($table,$data)
    {
        $query = $this->db->get_where($table,$data);
        return $query->result();
    }
    
    public  function getAlldata($sql)
	{
		$qr = $this->db->query($sql);
		return $qr->result();
	}
	
	public function delete($tablename,$where)
	{
		return $this->db->delete($tablename,$where);
	}
	
	public function distributorDashboard($id){
	    $successData = $this->getAlldata("SELECT sum(amount) as amt ,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` JOIN customers ON customers.cus_id = exr_rechrgreqexr_rechrgreq_fch.apiclid where status='SUCCESS' AND month(reqdate) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
		$failedData = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` JOIN customers ON customers.cus_id = exr_rechrgreqexr_rechrgreq_fch.apiclid where status='FAILED' AND month(reqdate) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
		$pendingData = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` JOIN customers ON customers.cus_id = exr_rechrgreqexr_rechrgreq_fch.apiclid where status='PENDING' AND month(reqdate) = MONTH(CURRENT_TIMESTAMP)AND cus_reffer='$id'");
		$penFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE req_status='PENDING' AND month(req_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
		$successFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE req_status='SUCCESS' AND month(req_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
		$rejectedFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE req_status='REJECTED' AND month(req_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
		$fundReqData = $this->db_model->getAlldata("SELECT SUM(pay_amount) AS amt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE month(req_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'"); 
		
	   	$retailers = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='retailer' AND cus_reffer='$id'");
	   	$retailersBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='retailer' AND cus_reffer='$id')");
	   
	    $totalAepsBal = $this->getAlldata("SELECT sum(`aeps_txn_clbal`) as totalBal FROM aeps_exr_trnx WHERE `aeps_txn_id` IN ( SELECT MAX(`aeps_txn_id`) FROM aeps_exr_trnx GROUP BY `aeps_txn_agentid` )");
 	    $penTickets = $this->getAlldata("SELECT * from `ticket` JOIN customers ON customers.cus_id=ticket.cus_id where status='0' AND cus_reffer='$id' group by ticket_id");
	   	$closeTickets = $this->getAlldata("SELECT * from `ticket` JOIN customers ON customers.cus_id=ticket.cus_id where status='1' AND cus_reffer='$id' group by ticket_id");
	   	$ticketAmount = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id JOIN customers ON customers.cus_id=t.cus_id WHERE cus_reffer='$id'");
	   	$pendingTicketAmount = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id JOIN customers ON customers.cus_id=t.cus_id WHERE t.status='0' AND cus_reffer='$id'");
	  	$panBuyQuantity = $this->getAlldata("SELECT IFNULL(sum(qty),0) as quantity FROM coupon_buy JOIN customers ON customers.cus_id=coupon_buy.pan_cus_id WHERE status='SUCCESS' and month(added_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	  	$panBuyAmount = $this->getAlldata("SELECT IFNULL(sum(amount),0) as amount,sum(new_bal) as balance FROM coupon_buy JOIN customers ON customers.cus_id=coupon_buy.pan_cus_id WHERE status='SUCCESS' AND month(added_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	  	$aepsCashWithdrawAmount = $this->getAlldata("SELECT sum(aeps_txn_crdt) as amount FROM aeps_exr_trnx JOIN customers ON customers.cus_id=aeps_exr_trnx.aeps_txn_agentid WHERE aeps_txn_type LIKE '%AEPS Cash Withdrawal%' AND month(aeps_txn_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	    $aepsAadharPayAmount = $this->getAlldata("SELECT sum(aeps_txn_crdt) as amount FROM aeps_exr_trnx JOIN customers ON customers.cus_id=aeps_exr_trnx.aeps_txn_agentid WHERE aeps_txn_type LIKE '%Aadhar Pay%' AND month(aeps_txn_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	    $aepsFailedCount = $this->getAlldata("SELECT count(aeps_id) as sum FROM aeps_transaction_fch JOIN customers ON customers.cus_id=aeps_transaction_fch.apiclid WHERE status ='failed' AND month(aeps_date_time) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	    $dmtSuccessAmount = $this->getAlldata("SELECT sum(amount) as amount FROM dmt_trnx JOIN customers ON customers.cus_id=dmt_trnx.cus_id WHERE status ='SUCCESS' AND month(dmt_trnx_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	    $dmtFailedAmount = $this->getAlldata("SELECT sum(amount) as amount FROM dmt_trnx JOIN customers ON customers.cus_id=dmt_trnx.cus_id WHERE status ='FAILED' AND month(dmt_trnx_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	    $dmtChargeAmount = $this->getAlldata("SELECT sum(charge) as amount FROM dmt_trnx JOIN customers ON customers.cus_id=dmt_trnx.cus_id WHERE status ='SUCCESS' AND month(dmt_trnx_date) = MONTH(CURRENT_TIMESTAMP) AND cus_reffer='$id'");
	    
	    //Todays Report
	    
	    $panBuyQuantityToday = $this->getAlldata("SELECT IFNULL(sum(qty),0) as quantity FROM coupon_buy JOIN customers ON customers.cus_id=coupon_buy.pan_cus_id WHERE status='SUCCESS' and date(added_date) = CURDATE() AND cus_reffer='$id'");
	  	$panBuyAmountToday = $this->getAlldata("SELECT IFNULL(sum(amount),0) as amount,IFNULL(sum(new_bal),0) as balance FROM coupon_buy JOIN customers ON customers.cus_id=coupon_buy.pan_cus_id WHERE status='SUCCESS' and date(added_date) = CURDATE() AND cus_reffer='$id'");
	  	$aepsCashWithdrawAmountToday = $this->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amount FROM aeps_exr_trnx JOIN customers ON customers.cus_id=aeps_exr_trnx.aeps_txn_agentid WHERE aeps_txn_type LIKE '%AEPS Cash Withdrawal%' AND date(aeps_txn_date) = CURDATE() AND cus_reffer='$id'");
	    $aepsAadharPayAmountToday = $this->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amount FROM aeps_exr_trnx JOIN customers ON customers.cus_id=aeps_exr_trnx.aeps_txn_agentid WHERE aeps_txn_type LIKE '%Aadhar Pay%' AND date(aeps_txn_date) = CURDATE() AND cus_reffer='$id'");
	    $aepsFailedCountToday = $this->getAlldata("SELECT count(aeps_id) as sum FROM aeps_transaction_fch JOIN customers ON customers.cus_id=aeps_transaction_fch.apiclid WHERE status ='failed' AND date(aeps_date_time) = CURDATE() AND cus_reffer='$id'");
	    $dmtSuccessAmountToday = $this->getAlldata("SELECT sum(amount) as amount FROM dmt_trnx JOIN customers ON customers.cus_id=dmt_trnx.cus_id WHERE status ='SUCCESS' AND date(dmt_trnx_date) = CURDATE() AND cus_reffer='$id'");
	    $dmtFailedAmountToday = $this->getAlldata("SELECT IFNULL(sum(amount),0) as amount FROM dmt_trnx JOIN customers ON customers.cus_id=dmt_trnx.cus_id WHERE status ='FAILED' AND date(dmt_trnx_date) = CURDATE() AND cus_reffer='$id'");
	    $dmtChargeAmountToday = $this->getAlldata("SELECT IFNULL(sum(charge),0) as amount FROM dmt_trnx JOIN customers ON customers.cus_id=dmt_trnx.cus_id WHERE status ='SUCCESS' AND date(dmt_trnx_date) = CURDATE() AND cus_reffer='$id'");
	    $ticketAmountToday = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id JOIN customers ON customers.cus_id=t.cus_id WHERE cus_reffer='$id' AND date(ticket_date) = CURDATE()");
	   	$pendingTicketAmountToday = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id JOIN customers ON customers.cus_id=t.cus_id WHERE cus_reffer='$id' AND t.status='0' AND date(ticket_date) = CURDATE()");
	   	$penTicketsToday = $this->getAlldata("SELECT * from `ticket` JOIN customers ON customers.cus_id=ticket.cus_id where status='0' AND cus_reffer='$id' AND date(ticket_date) = CURDATE() group by ticket_id");
	   	$closeTicketsToday = $this->getAlldata("SELECT * from `ticket` JOIN customers ON customers.cus_id=ticket.cus_id where status='1' AND cus_reffer='$id' AND date(ticket_date) = CURDATE() group by ticket_id");
	   	
	   	$successDataToday = $this->getAlldata("SELECT sum(amount) as amt ,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` JOIN customers ON customers.cus_id = exr_rechrgreqexr_rechrgreq_fch.apiclid where status='SUCCESS' AND date(reqdate) = CURDATE() AND cus_reffer='$id'");
		$failedDataToday = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` JOIN customers ON customers.cus_id = exr_rechrgreqexr_rechrgreq_fch.apiclid where status='FAILED' AND date(reqdate) = CURDATE() AND cus_reffer='$id'");
		$pendingDataToday = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` JOIN customers ON customers.cus_id = exr_rechrgreqexr_rechrgreq_fch.apiclid where status='PENDING' AND date(reqdate) = CURDATE() AND cus_reffer='$id'");
		
		$penFundReqDataToday = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE req_status='PENDING' AND date(req_date) = CURDATE() AND cus_reffer='$id'");
		$successFundReqDataToday = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE req_status='SUCCESS' AND date(req_date) = CURDATE() AND cus_reffer='$id'");
		$rejectedFundReqDataToday = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE req_status='REJECTED' AND date(req_date) = CURDATE() AND cus_reffer='$id'");
		$fundReqDataToday = $this->db_model->getAlldata("SELECT SUM(pay_amount) AS amt FROM `fund_request` JOIN customers ON customers.cus_id=fund_request.cus_id WHERE date(req_date) = CURDATE() AND cus_reffer='$id'");  
	  	
	    
	   	$data = [
	   	    'success' => $successData,
	   	    'failed' => $failedData,
	   	    'pendding' => $pendingData,
	   	    'pendingFundReq' => $penFundReqData,
	   	    'successFundReq' => $successFundReqData,
	   	    'rejectedFundReq' => $rejectedFundReqData,
	   	    'fundReq' => $fundReqData,
	   	    'retCnt' => $retailers,
	   	    'distCnt' => $distributors,
	   	    'masCnt' => $masters,
	   	    'apiCnt' => $apiclients,
	   	    'retBal' => $retailersBal,
	   	    'distBal' => $distributorsBal,
	   	    'masBal' => $mastersBal,
	   	    'apiBal' => $apiclientsBal,
	   	    'aepsBal' => $totalAepsBal,
	   	    'penTickets' => $penTickets,
	   	    'closeTickets' => $closeTickets,
	   	    'ticketAmount' => $ticketAmount,
	   	    'pendingTicketAmount' => $pendingTicketAmount,
	   	    'panBuyQuantity' => $panBuyQuantity,
	   	    'panBuyAmount' => $panBuyAmount,
	   	    'aepsCashWithdrawAmount' => $aepsCashWithdrawAmount,
	   	    'aepsAadharPayAmount' => $aepsAadharPayAmount,
	   	    'aepsFailedCount' => $aepsFailedCount,
	   	    'dmtSuccessAmount' => $dmtSuccessAmount,
	   	    'dmtFailedAmount' => $dmtFailedAmount,
	   	    'dmtChargeAmount' => $dmtChargeAmount,
	   	    'panBuyQuantityToday' => $panBuyQuantityToday,
	   	    'panBuyAmountToday' => $panBuyAmountToday,
	   	    'aepsCashWithdrawAmountToday' => $aepsCashWithdrawAmountToday,
	   	    'aepsAadharPayAmountToday' => $aepsAadharPayAmountToday,
	   	    'aepsFailedCountToday' => $aepsFailedCountToday,
	   	    'dmtSuccessAmountToday' => $dmtSuccessAmountToday,
	   	    'dmtFailedAmountToday' => $dmtFailedAmountToday,
	   	    'dmtChargeAmountToday' => $dmtChargeAmountToday,
	   	    'ticketAmountToday' => $ticketAmountToday,
	   	    'pendingTicketAmountToday' => $pendingTicketAmountToday,
	   	    'penTicketsToday' => $penTicketsToday,
	   	    'closeTicketsToday' => $closeTicketsToday,
	   	    'successToday' => $successDataToday,
	   	    'failedToday' => $failedDataToday,
	   	    'penddingToday' => $pendingDataToday,
	   	    'pendingFundReqToday' => $penFundReqDataToday,
	   	    'successFundReqToday' => $successFundReqDataToday,
	   	    'rejectedFundReqToday' => $rejectedFundReqDataToday,
	   	    'fundReqToday' => $fundReqDataToday
	   	    ];
	   	    
	   	return $data;
	}
	public function masterDashboard(){
	    $successData = $this->getAlldata("SELECT sum(amount) as amt ,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' and apiclid='".$this->session->userdata('cus_id')."' and DATE(reqdate)='".date('Y-m-d')."'");
		$failedData = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' and apiclid='".$this->session->userdata('cus_id')."' and DATE(reqdate)='".date('Y-m-d')."'");
		$pendingData = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='PENDING' and apiclid='".$this->session->userdata('cus_id')."' and DATE(reqdate)='".date('Y-m-d')."' ");
		$penFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='PENDING'");
		$successFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='SUCCESS'");
		$rejectedFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='REJECTED'");
		$fundReqData = $this->db_model->getAlldata("SELECT SUM(pay_amount) AS amt FROM `fund_request`"); 
	   	$retailers = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='retailer' and cus_reffer ='".$id."'");
	    $distributors = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='distributor'");
	   	//$masters = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='master'");
	   	$apiclients = $this->db_model->getAlldata("select count(cus_id) as cnt from customers where cus_type='api'"); 
	   	$retailersBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='retailer')");
	    $distributorsBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='distributor')");
	   	//$mastersBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='master')");
	   	$apiclientsBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='customer')");
	   	/*$penTickets = $this->getAlldata("SELECT * from `ticket`");
	   	$closeTickets = $this->getAlldata("SELECT * from `ticket`");
	   	$ticketAmount = $this->getAlldata("SELECT sum(r.amount) FROM `ticket`");
	   	$pendingTicketAmount = $this->getAlldata("SELECT sum(r.amount) FROM `ticket`");*/
	    $totalAepsBal = $this->getAlldata("SELECT sum(`aeps_txn_clbal`) as totalBal FROM aeps_exr_trnx WHERE `aeps_txn_id` IN ( SELECT MAX(`aeps_txn_id`) FROM aeps_exr_trnx GROUP BY `aeps_txn_agentid` )");

	   	$data = [
	   	    'success' => $successData,
	   	    'failed' => $failedData,
	   	    'pendding' => $pendingData,
	   	    'pendingFundReq' => $penFundReqData,
	   	    'successFundReq' => $successFundReqData,
	   	    'rejectedFundReq' => $rejectedFundReqData,
	   	    'fundReq' => $fundReqData,
	   	    'retCnt' => $retailers,
	   	    'distCnt' => $distributors,
	   	    'masCnt' => $masters,
	   	    'apiCnt' => $apiclients,
	   	    'retBal' => $retailersBal,
	   	    'distBal' => $distributorsBal,
	   	    'masBal' => $mastersBal,
	   	    'apiBal' => $apiclientsBal,
	   	    'penTickets' => $penTickets,
	   	    'closeTickets' => $closeTickets,
	   	    'ticketAmount' => $ticketAmount,
	   	    'pendingTicketAmount' => $pendingTicketAmount,
	   	    'aepsBal' => $totalAepsBal
	   	    ];
	   	    
	   	return $data;
	}
	
	
	
		public function adminDashboard(){
	    $successData = $this->getAlldata("SELECT sum(amount) as amt ,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' AND month(reqdate) = MONTH(CURRENT_TIMESTAMP)");
		$failedData = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' AND month(reqdate) = MONTH(CURRENT_TIMESTAMP)");
		$pendingData = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='PENDING' AND month(reqdate) = MONTH(CURRENT_TIMESTAMP)");
		$penFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='PENDING' AND month(req_date) = MONTH(CURRENT_TIMESTAMP)");
		$successFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='SUCCESS' AND month(req_date) = MONTH(CURRENT_TIMESTAMP)");
		$rejectedFundReqData = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='REJECTED' AND month(req_date) = MONTH(CURRENT_TIMESTAMP)");
		$fundReqData = $this->db_model->getAlldata("SELECT SUM(pay_amount) AS amt FROM `fund_request` WHERE month(req_date) = MONTH(CURRENT_TIMESTAMP)"); 
	   	$retailers = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='retailer'");
	    $distributors = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='distributor'");
	   	$masters = $this->getAlldata("select count(cus_id) as cnt from customers where cus_type='master'");
	   	$apiclients = $this->db_model->getAlldata("select count(cus_id) as cnt from customers where cus_type='api'"); 
	   	$retailersBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='retailer')");
	    $distributorsBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='distributor')");
	   	$mastersBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='master')");
	   	$apiclientsBal = $this->getAlldata("SELECT sum(`txn_clbal`) as totalBal FROM exr_trnx WHERE `txn_id` IN ( SELECT MAX(`txn_id`) FROM exr_trnx GROUP BY `txn_agentid` ) AND `txn_agentid` IN (SELECT cus_id FROM `customers` WHERE `cus_type`='api')");
	    $totalAepsBal = $this->getAlldata("SELECT sum(`aeps_txn_clbal`) as totalBal FROM aeps_exr_trnx WHERE `aeps_txn_id` IN ( SELECT MAX(`aeps_txn_id`) FROM aeps_exr_trnx GROUP BY `aeps_txn_agentid` )");
 	    $penTickets = $this->getAlldata("SELECT * from `ticket` where status='0' group by ticket_id");
	   	$closeTickets = $this->getAlldata("SELECT * from `ticket` where status='1' group by ticket_id");
	   	$ticketAmount = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id");
	   	$pendingTicketAmount = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id WHERE t.status='0'");
	  	$panBuyQuantity = $this->getAlldata("SELECT sum(qty) as quantity FROM coupon_buy WHERE status='SUCCESS' and month(added_date) = MONTH(CURRENT_TIMESTAMP)");
	  	$panBuyAmount = $this->getAlldata("SELECT sum(amount) as amount,sum(new_bal) as balance FROM coupon_buy WHERE status='SUCCESS' AND month(added_date) = MONTH(CURRENT_TIMESTAMP)");
	  	$aepsCashWithdrawAmount = $this->getAlldata("SELECT sum(aeps_txn_crdt) as amount FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%AEPS Cash Withdrawal%' AND month(aeps_txn_date) = MONTH(CURRENT_TIMESTAMP)");
	    $aepsAadharPayAmount = $this->getAlldata("SELECT sum(aeps_txn_crdt) as amount FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Aadhar Pay%' AND month(aeps_txn_date) = MONTH(CURRENT_TIMESTAMP)");
	    $aepsFailedCount = $this->getAlldata("SELECT count(aeps_id) as sum FROM aeps_transaction_fch WHERE status ='failed' AND month(aeps_date_time) = MONTH(CURRENT_TIMESTAMP)");
	    $dmtSuccessAmount = $this->getAlldata("SELECT sum(amount) as amount FROM dmt_trnx WHERE status ='SUCCESS' AND month(dmt_trnx_date) = MONTH(CURRENT_TIMESTAMP)");
	    $dmtFailedAmount = $this->getAlldata("SELECT sum(amount) as amount FROM dmt_trnx WHERE status ='FAILED' AND month(dmt_trnx_date) = MONTH(CURRENT_TIMESTAMP)");
	    $dmtChargeAmount = $this->getAlldata("SELECT sum(charge) as amount FROM dmt_trnx WHERE status ='SUCCESS' AND month(dmt_trnx_date) = MONTH(CURRENT_TIMESTAMP)");
	    
	    //Todays Report
	    
	    $panBuyQuantityToday = $this->getAlldata("SELECT IFNULL(sum(qty),0) as quantity FROM coupon_buy WHERE status='SUCCESS' AND date(added_date) = CURDATE() ");
	  	$panBuyAmountToday = $this->getAlldata("SELECT IFNULL(sum(amount),0) as amount,IFNULL(sum(new_bal),0) as balance FROM coupon_buy WHERE status='SUCCESS' and date(added_date) = CURDATE() ");
	  	$aepsCashWithdrawAmountToday = $this->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amount FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%AEPS Cash Withdrawal%' AND date(aeps_txn_date) = CURDATE()");
	    $aepsAadharPayAmountToday = $this->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amount FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Aadhar Pay%' AND date(aeps_txn_date) = CURDATE()");
	    $aepsFailedCountToday = $this->getAlldata("SELECT IFNULL(count(aeps_id),0) as sum FROM aeps_transaction_fch WHERE status ='failed' AND date(aeps_date_time) = CURDATE()");
	    $dmtSuccessAmountToday = $this->getAlldata("SELECT IFNULL(sum(amount),0) as amount FROM dmt_trnx WHERE status ='SUCCESS' AND date(dmt_trnx_date) = CURDATE()");
	    $dmtFailedAmountToday = $this->getAlldata("SELECT IFNULL(sum(amount),0) as amount FROM dmt_trnx WHERE status ='FAILED' AND date(dmt_trnx_date) = CURDATE()");
	    $dmtChargeAmountToday = $this->getAlldata("SELECT IFNULL(sum(charge),0) as amount FROM dmt_trnx WHERE status ='SUCCESS' AND date(dmt_trnx_date) = CURDATE()");
	    $ticketAmountToday = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id AND date(ticket_date) = CURDATE()");
	   	$pendingTicketAmountToday = $this->getAlldata("SELECT sum(r.amount) FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch AS r ON r.recid = t.rec_id WHERE t.status='0' AND date(ticket_date) = CURDATE()");
	   	$penTicketsToday = $this->getAlldata("SELECT * from `ticket` where status='0' AND date(ticket_date) = CURDATE() ORDER by ticket_id");
	   	$closeTicketsToday = $this->getAlldata("SELECT * from `ticket` where status='1' AND date(ticket_date) = CURDATE() ORDER by ticket_id");
	   	
	   	$successDataToday = $this->getAlldata("SELECT sum(amount) as amt ,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' AND date(reqdate) = CURDATE() ");
		$failedDataToday = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' AND date(reqdate) = CURDATE() ");
		$pendingDataToday = $this->getAlldata("SELECT sum(amount) as amt,count(*) AS cnt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='PENDING' AND date(reqdate) = CURDATE()");
		$penFundReqDataToday = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='PENDING' AND date(req_date) = CURDATE() ");
		$successFundReqDataToday = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='ACCEPTED' AND date(req_date) = CURDATE()");
		$rejectedFundReqDataToday = $this->getAlldata("SELECT SUM(pay_amount) AS amt, count(req_id) as cnt FROM `fund_request` WHERE req_status='REJECTED' AND date(req_date) = CURDATE()");
		$fundReqDataToday = $this->db_model->getAlldata("SELECT SUM(pay_amount) AS amt FROM `fund_request` WHERE date(req_date) = CURDATE()"); 
	  	
	    
	   	$data = [
	   	    'success' => $successData,
	   	    'failed' => $failedData,
	   	    'pendding' => $pendingData,
	   	    'pendingFundReq' => $penFundReqData,
	   	    'successFundReq' => $successFundReqData,
	   	    'rejectedFundReq' => $rejectedFundReqData,
	   	    'fundReq' => $fundReqData,
	   	    'retCnt' => $retailers,
	   	    'distCnt' => $distributors,
	   	    'masCnt' => $masters,
	   	    'apiCnt' => $apiclients,
	   	    'retBal' => $retailersBal,
	   	    'distBal' => $distributorsBal,
	   	    'masBal' => $mastersBal,
	   	    'apiBal' => $apiclientsBal,
	   	    'aepsBal' => $totalAepsBal,
	   	    'penTickets' => $penTickets,
	   	    'closeTickets' => $closeTickets,
	   	    'ticketAmount' => $ticketAmount,
	   	    'pendingTicketAmount' => $pendingTicketAmount,
	   	    'panBuyQuantity' => $panBuyQuantity,
	   	    'panBuyAmount' => $panBuyAmount,
	   	    'aepsCashWithdrawAmount' => $aepsCashWithdrawAmount,
	   	    'aepsAadharPayAmount' => $aepsAadharPayAmount,
	   	    'aepsFailedCount' => $aepsFailedCount,
	   	    'dmtSuccessAmount' => $dmtSuccessAmount,
	   	    'dmtFailedAmount' => $dmtFailedAmount,
	   	    'dmtChargeAmount' => $dmtChargeAmount,
	   	    'panBuyQuantityToday' => $panBuyQuantityToday,
	   	    'panBuyAmountToday' => $panBuyAmountToday,
	   	    'aepsCashWithdrawAmountToday' => $aepsCashWithdrawAmountToday,
	   	    'aepsAadharPayAmountToday' => $aepsAadharPayAmountToday,
	   	    'aepsFailedCountToday' => $aepsFailedCountToday,
	   	    'dmtSuccessAmountToday' => $dmtSuccessAmountToday,
	   	    'dmtFailedAmountToday' => $dmtFailedAmountToday,
	   	    'dmtChargeAmountToday' => $dmtChargeAmountToday,
	   	    'ticketAmountToday' => $ticketAmountToday,
	   	    'pendingTicketAmountToday' => $pendingTicketAmountToday,
	   	    'penTicketsToday' => $penTicketsToday,
	   	    'closeTicketsToday' => $closeTicketsToday,
	   	    'successToday' => $successDataToday,
	   	    'failedToday' => $failedDataToday,
	   	    'penddingToday' => $pendingDataToday,
	   	    'pendingFundReqToday' => $penFundReqDataToday,
	   	    'successFundReqToday' => $successFundReqDataToday,
	   	    'rejectedFundReqToday' => $rejectedFundReqDataToday,
	   	    'fundReqToday' => $fundReqDataToday
	   	    ];
	   	    
	   	return $data;
	}
	
    public function get_trnx($cus_id)
	{
		$this->db->select('*');
		$this->db->from('exr_trnx');
		$this->db->where('txn_agentid',$cus_id);
		$this->db->order_by('txn_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	public function get_newdata($table,$where,$select='*')
	{
		$this->db->select($select);
		$this->db->from($table);
		$this->db->where($where);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			//print_r($this->db->last_query());
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	public function insert($table,$data)
	{
		$this->db->insert($table, $data);
		//print_r($this->db->last_query());
		if($this->db->affected_rows() > 0)
		{
			return true; // to the controller
		}
		else
		{
			return false;
		}
	}
	public function iud_data($sql,$value,$id = NULL)

	{
		if($id)
		{
			$qr=$this->db->query($sql,$value);
			if($qr)
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
			$t= array();
			foreach($value as $val)
			{
				$t[] = $val;
			}
			$qr=$this->db->query($sql,$t);
			if($qr)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}

	}
	
	public function get_rec($cus_id)
	{
		$this->db->select('*');
		$this->db->from('exr_rechrgreqexr_rechrgreq_fch');
		$this->db->where('apiclid',$cus_id);
		$this->db->order_by('recid','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	
	public function checkapiuser($data)
	{   
	    $mobile = $this->encryption_model->encode($data['id']);
		$this->db->select(array('cus_id','cus_cutofamt','cus_mobile','cus_pass','cus_pin','cus_ip','cus_type'));
		$this->db->from('customers');
		$this->db->where('cus_type', 'api');
		$this->db->where('cus_mobile', $mobile);
		$this->db->where('avability_status !=', '1');
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			if(1)
			{
				if($this->encryption_model->decode($query->result()[0]->cus_pass) == $data['pass'] && $query->result()[0]->cus_ip == $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) == $data['pin'])
				{
						return (object)array('ERROR'=>0,'info'=>$query->result()[0]);
				}
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) == $data['pass'] && $query->result()[0]->cus_ip != $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) == $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid IP');
				}
				
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) != $data['pass'] && $query->result()[0]->cus_ip != $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) == $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid Password and IP');
				}
				
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) != $data['pass'] && $query->result()[0]->cus_ip != $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) != $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid Password, Pin and IP');
				}
				
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) == $data['pass'] && $query->result()[0]->cus_ip == $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) != $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid Pin');
				}
				
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) != $data['pass'] && $query->result()[0]->cus_ip == $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) == $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid Password');
				}
				
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) == $data['pass'] && $query->result()[0]->cus_ip != $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) != $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid Pin and IP');
				}
				elseif($this->encryption_model->decode($query->result()[0]->cus_pass) != $data['pass'] && $query->result()[0]->cus_ip == $data['ip'] && $this->encryption_model->decode($query->result()[0]->cus_pin) != $data['pin'])
				
				{
					return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid Pass and Pin');
				}
				
			}
			else
			{
				return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid ip token.');
			}
		}
		else 
		{
			return (object)array('ERROR'=>2,'MESSAGE'=>'Invalid user credential');
		}
	}

}