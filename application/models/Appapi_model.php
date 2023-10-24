<?php 
if(!defined('BASEPATH')) exit('No Direct Scripts Allowed');

class Appapi_model extends CI_Model{
    
    public $key = '216ae79f054a7034d9950c654efc6da0';//216ae79f054a7034d9950c654efc6da0
    
    public function __construct(){
        parent:: __construct();
        $this->key = $this->config->item('key');
        $this->load->model(array('Db_model','Encryption_model','Email_model','msg_model'));
    }
   
    public function getCustomerId($mobile){
        $mob=$this->Encryption_model->encode($mobile);
        $query = $this->Db_model->getAlldata("SELECT cus_id,cus_type,cus_email,cus_name,profile_img,aeps_kyc_status,newaepskyc_status FROM customers WHERE cus_mobile='$mob' AND avability_status !='1'");
        //$this->db->last_query();
        if ($query)
        {   
            return $query;
        }
        else
        {
            return "Entered Mobile Invalid ";
        }
    }
    
    
   public function updatefcmtoken($cust_id,$fcm_token){
            
        $data = array(
            "fcm_token" => $fcm_token
            );
        $this->db->where('cus_id',$cust_id);
        $this->db->update('customers',$data);
        
        if($this->db->affected_rows()==1){
            
            return true;
        }else{
            return false;
        }
        
    }
    
    public  function getAlldata($sql)
	{
		$qr = $this->db->query($sql);
		return $qr->result();
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
    public function getWalletBalance($cus_id)
    {
        $query = $this->Db_model->getAlldata("SELECT e.txn_clbal FROM exr_trnx as e WHERE e.txn_agentid='$cus_id' ORDER BY e.txn_id DESC LIMIT 1");
        $this->db->last_query();
        if ($query)
        {   
            return $query[0]->txn_clbal;
        }
        else
        {
            return "0";
        }
    }
    
    public function getAepsBalance($cus_id)
    {
        $query = $this->Db_model->getAlldata("SELECT aeps_txn_clbal FROM `aeps_exr_trnx` JOIN `customers` ON aeps_exr_trnx.aeps_txn_agentid = customers.cus_id WHERE `cus_id` = '".$cus_id."' ORDER BY aeps_txn_id desc limit 1");
        // echo $this->db->last_query();
        if ($query)
        {   
            return $query[0]->aeps_txn_clbal;
        }
        else
        {
            return "0";
        }
    }
    
    public function getNews($cus_type)
    {
        $query = $this->Db_model->getAlldata("SELECT news_desc FROM news WHERE news_type like '%$cus_type%'");
        //$this->db->last_query();
        if ($query)
        {   
            return $query[0]->news_desc;
        }
        else
        {
            return "";
        }
    }
    
    public function getBanners()
    {
        $query = $this->Db_model->getAlldata("SELECT * FROM banner");
        //$this->db->last_query();
        if ($query)
        {   
            return $query;
        }
        else
        {
            return "No Banner Found";
        }
    }
    
    public function getOperators($operator_type)
    {
        $query = $this->Db_model->getAlldata("SELECT opid,operatorname,opcodenew,qr_opcode,opsertype,operator_image from operator WHERE opsertype = '$operator_type'");
        if($query){
            return $query;
        }else{
            return "No Operators for Operator Type : $operator_type Found";
        }
    }
    
    public function changePassword($mobile,$current_password,$new_password)
    {
        $query = $this->Db_model->getAlldata("SELECT cus_pass FROM customers WHERE cus_mobile='$mobile' AND avability_status !='1'");

        if ($query)
        {
            if($this->Encryption_model->decode($query[0]->cus_pass) == $pass)
            {
                $data = [
                    'cus_pass' => ($this->Encryption_model->encode($newpass))
                ];
                $insert = $this->Db_model->insert_update('customers',$data,array('cus_mobile'=>$mobile));
                if($insert){
                    return true;
                }else{
                    return "Error Occoured Try Again Later!";
                }
            }
            else
            {
                return "Invalid Entered Current Password";
            }
        }
        else
        {
            return "Entered Mobile is Invalid";
        }
    }
    public function forgetPassword($new_password,$mobile)
    {
        $data = [
                    'cus_pass' => ($this->Encryption_model->encode($new_password))
                ];
                $insert = $this->Db_model->insert_update('customers',$data,array('cus_mobile'=>$this->Encryption_model->encode($mobile)));
                if($insert){
                        $data = array('mobile'=>$mobile,'pass'=>$new_password);
                       $this->msg_model->forget_pass($data);
                       /* $msg="Pin Changed  successfully";
                	    $sub=":Pin changed";
                	    $this->Email_model->send_email($cus_id,$msg,$sub);*/
                	    
                	    /*$message = "Dear User , your password is ".$new_password." From,EVILLD";
                	    $message=urlencode($message);
                		$get_url="http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$mobile&sendername=MUZOTP&message=$message&templateid=1707161018226094127";
                		file_get_contents($get_url);*/
                      
                    return true;
                }else{
                    return "Error Occoured Try Again Later!";
                }
    }
    
    public function check_recharge($amount,$mob,$cus_id,$operator)
	{
	    $time = time();
	    $timeck = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where apiclid='".$cus_id."' and reqtime='".$time."' order by recid desc limit 1")->result();
	    if(!$timeck)
	    {
	        $recdt = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where mobileno='$mob' and amount='$amount' and operator='$operator' and status='SUCCESS' order by recid desc limit 1")->result();
	       // echo $this->db->last_query();
    		if($recdt)
    		{	
    			$date = DateTime::createFromFormat('Y-m-d H:i:s', $recdt[0]->reqdate);
    			$date->modify('+10 minutes');
    			$recdate = $date->format('Y-m-d H:i:s');
    			$now = date('Y-m-d H:i:s');
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
    			return true;
    		}
	    }
	    else
		{
			return true;
		}
	}
	
	public function verify_pin($mobile,$pin)
    {
        $mob=$this->Encryption_model->encode($mobile);
        $query = $this->Db_model->getAlldata("SELECT cus_mobile,cus_pin FROM customers WHERE cus_mobile='$mob'");
        if ($query)
        {
            if($this->Encryption_model->decode($query[0]->cus_pin) == $pin)
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
            return false;
        }
    }
    
    public function rechargeHistory($cus_id,$date){
        $query = $this->Db_model->getAlldata("select ef.recid,ef.reqdate,ef.recmedium,ef.amount,ef.status,ef.mobileno,c.cus_name,ef.dispute_status,ef.statusdesc,o.operatorname,ef.operator,o.operator_image,ef.retailer,ef.master,ef.distributor,(SELECT txn.txn_id FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and (txn.txn_type='discount' || txn.txn_type='Recharge Failed'||txn.txn_type='recharge') ORDER BY txn.txn_id DESC LIMIT 1) as txn_id,(SELECT txn.txn_clbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cus_id' ORDER BY txn.txn_id DESC LIMIT 1) as txn_clbal,(SELECT txn.txn_opbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cus_id' ORDER BY txn.txn_id ASC LIMIT 1) as txn_opbal from exr_rechrgreqexr_rechrgreq_fch as ef , customers as c,operator as o where o.opcodenew =ef.operator and ef.apiclid=c.cus_id and ef.apiclid='$cus_id' and DATE(ef.reqdate) LIKE '$date' order by recid DESC");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
    public function rechargeHistoryFromTo($cus_id,$fromDate,$toDate){
        $query = $this->Db_model->getAlldata("select ef.recid,ef.reqdate,ef.recmedium,ef.amount,ef.status,ef.mobileno,c.cus_name,ef.dispute_status,ef.statusdesc,o.operatorname,ef.operator,o.operator_image,ef.retailer,ef.master,ef.distributor,(SELECT txn.txn_id FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and (txn.txn_type='discount' || txn.txn_type='Recharge Failed'||txn.txn_type='recharge') ORDER BY txn.txn_id DESC LIMIT 1) as txn_id,(SELECT txn.txn_clbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cus_id' ORDER BY txn.txn_id DESC LIMIT 1) as txn_clbal,(SELECT txn.txn_opbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cus_id' ORDER BY txn.txn_id ASC LIMIT 1) as txn_opbal from exr_rechrgreqexr_rechrgreq_fch as ef , customers as c,operator as o where o.opcodenew =ef.operator and ef.apiclid=c.cus_id and ef.apiclid='$cus_id' and DATE(ef.reqdate) >= '$fromDate' and DATE(ef.reqdate) <= '$toDate' order by recid DESC");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
    public function viewCreditWallet($cus_id,$fromdate,$todate){
        $array = array();
        $check_cusid['cusid'] = $cusid;
        
        $this->db->select(array('trnx.txn_id','trnx.txn_agentid','trnx.txn_opbal','trnx.txn_crdt','trnx.txn_dbdt','trnx.txn_clbal','trnx.txn_type','trnx.txn_date as txn_time','cust.cus_name','cust.cus_type','cust.cus_mobile','txn_fromto'));
        $this->db->from('exr_trnx as trnx ');
        $this->db->join('customers as cust','trnx.txn_agentid = cust.cus_id');
        if($check_cusid)
        {
            $this->db->where('cus_id', $cusid);
            $this->db->where_in('trnx.txn_type', array('Direct Credit','Credited','AEPS Direct Credit'));
            $this->db->where('date(trnx.txn_date)>=',$fromdate);
            $this->db->where('date(trnx.txn_date)<=',$todate);
            $this->db->order_by('trnx.txn_id DESC');
        }
        
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() > 0)
        {
            
            foreach($query->result() as $rec){
                
                if($rec->txn_fromto != '0'){
                    $this->db->select(array('cus_name','cus_mobile','cus_id'));
                    $this->db->from('customers');
                    $this->db->where('cus_id',$rec->txn_fromto);
                    $data = $this->db->get();
                    
                    if ($data->num_rows() > 0)
                    {
                        $datares = $data->result();
                        $cus_name = $datares[0]->cus_name;
                        $cus_mobile = $datares[0]->cus_mobile;
                        $cus_id =$datares[0]->cus_id;
                    }else{
                        $cus_name = 'Admin';
                        $cus_mobile = '0';
                        $cus_id ='0';
                    }
                    
                }else{
                    $cus_name = 'Admin';
                    $cus_mobile = '0';
                    $cus_id ='0';
                }
                
                $arr = array(
                'txn_id' => $rec->txn_id,
                'from_cus_name' => $cus_name,
                'form_cus_mobile' => $this->Encryption_model->decode($cus_mobile),
                'from_cus_id' => $cus_id,
                'txn_agentid' => $rec->txn_agentid,
                'txn_opbal' => $rec->txn_opbal,
                'txn_crdt' => $rec->txn_crdt,
                'txn_dbdt' => $rec->txn_dbdt,
                'txn_clbal' => $rec->txn_clbal,
                'txn_type' => $rec->txn_type,
                'txn_time' => $rec->txn_time,
                'cus_name' => $rec->cus_name
                );
                array_push($array,$arr);
            }
            
            return $array;
        }
        else
        {
            return false;
        }
    }
    
    public function viewDebitWallet($cus_id,$fromdate,$todate){
        $array = array();
        $check_cusid['cusid'] = $cusid;
        
        $this->db->select(array('trnx.txn_id','trnx.txn_agentid','trnx.txn_opbal','trnx.txn_crdt','trnx.txn_dbdt','trnx.txn_clbal','trnx.txn_type','trnx.txn_date as txn_time','cust.cus_name','cust.cus_type','cust.cus_id','cust.cus_mobile','txn_fromto'));
        $this->db->from('exr_trnx as trnx ');
        $this->db->join('customers as cust','trnx.txn_agentid = cust.cus_id');
        if($check_cusid)
        {
            $this->db->where('trnx.txn_agentid', $cusid);
            $this->db->where_in('trnx.txn_type', array('Fund Transfer'));
            $this->db->where('date(trnx.txn_date)>=',$fromdate);
            $this->db->where('date(trnx.txn_date)<=',$todate);
            $this->db->order_by('trnx.txn_id DESC');
        }
       
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            
            foreach($query->result() as $rec){
                
                if($rec->txn_fromto != '0'){
                    $this->db->select(array('cus_name','cus_mobile','cus_id'));
                    $this->db->from('customers');
                    $this->db->where('cus_id',$rec->txn_fromto);
                    $data = $this->db->get();
                    
                    if ($data->num_rows() > 0)
                    {
                        $datares = $data->result();
                        $cus_name = $datares[0]->cus_name;
                        $cus_mobile = $datares[0]->cus_mobile;
                        $cus_id =$datares[0]->cus_id;
                    }else{
                        $cus_name = 'Admin';
                        $cus_mobile = '0';
                        $cus_id ='0';
                    }
                    
                }else{
                    $cus_name = 'Admin';
                    $cus_mobile = '0';
                    $cus_id ='0';
                }
                
                $arr = array(
                'txn_id' => $rec->txn_id,
                'to_cus_name' => $cus_name,
                'to_cus_mobile' => $cus_mobile,
                'to_cus_id' => $cus_id,
                'txn_agentid' => $rec->txn_agentid,
                'txn_opbal' => $rec->txn_opbal,
                'txn_crdt' => $rec->txn_crdt,
                'txn_dbdt' => $rec->txn_dbdt,
                'txn_clbal' => $rec->txn_clbal,
                'txn_type' => $rec->txn_type,
                'txn_time' => $rec->txn_time,
                'cus_name' => $rec->cus_name
                );
                array_push($array,$arr);
            }
            
            return $array;
        }else
        {
            return false;
        }
    }
    
    public function getCommissionSlab($cus_id){
        
        $res = $this->Db_model->getAlldata("select * from customers where cus_id = '".$cus_id."' ");
        $pack_id = $res['0']->scheme_id;
        if($pack_id){
            
            $data = $this->Db_model->getAlldata("select * from commission_scheme_recharge where scheme_id = '".$pack_id."' ");
            return $data;
        }else{
            return false;
        } 
    }
    
    public function viewLedgerReport($cus_id,$fromdate,$todate){
        $query = $this->Db_model->getAlldata("SELECT * FROM `exr_trnx` as e where  DATE(e.txn_date) <= '$todate' and DATE(e.txn_date) >= '$fromdate' and txn_agentid='$cus_id' ORDER BY e.txn_id DESC");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
    /*----------------------------------OLD CODE------------------------------*/
    public function checkdth2_operator($dthnumber)
	{
		$request ="";
		$apikey = $this->key; 
		$param['mobile'] = $dthnumber;
		$param['apikey'] = $apikey;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $url = "http://operatorcheck.mplan.in/api/dthoperatorinfo.php?".$request;  
        // echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $content = curl_exec($ch);
        curl_close($ch); 
        // $data2=json_decode($content);
        $data2=json_decode($content, true);
	    $a = $data2['records'];
	    $opname = $a['Operator'];
	    if($opname=='Vodafone'){
	        $opname = "VI";
	    }
        $d = $this->Db_model->getAlldata("select * from operator where operatorname='$opname'");
        $dd = array_merge($data2,$d);
        // print_r($dd);exit;
        return $dd;
        
	}
    
    
    public function roffer($mobile,$operator)
	{
	   
	   if($operator == 'BSNL' || $operator =='BSNL STV')
	    $operator = 'Bsnl';
	   elseif($operator == 'VI')
	    $operator = 'Idea';
	    
		$request ="";
		$param['apikey'] = 'baa7cfc852f403a738f9b2d0614e43fc';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $url = "https://www.mplan.in/api/plans.php?".$request;  
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
        $data2=json_decode($content);
        
        //print_r($data2);
        return $data2;
        
	}
	
	
	public function rofferStateWise($circle,$operator)
	{
	    if($operator == 'BSNL' || $operator =='BSNL STV')
	    $operator = 'Bsnl';
	   elseif($operator == 'VI')
	    $operator = 'Idea';
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['operator'] = $operator;
		$param['cricle'] = $circle;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/plans.php?".$request;                           
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
        return $data2;
	}
	
	
	public function check_operator($mobile)
	{
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['tel'] = $mobile;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?".$request;  
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
        // $data2=json_decode($content);
        $data2=json_decode($content, true);
	    $a = $data2['records'];
	    $opname = $a['Operator'];
	    if($opname=='Vodafone'){
	        $opname = "VI";
	    }
        $d = $this->Db_model->getAlldata("select * from operator where operatorname='$opname'");
        $dd = array_merge($data2,$d);
        // print_r($dd);exit;
        return $dd;
        
	}
	
	
	public function checkdth_operator($mobile)
	{
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['tel'] = $mobile;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $url = "http://operatorcheck.mplan.in/api/dthoperatorinfo.php?".$request;  
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
        // $data2=json_decode($content);
        $data2=json_decode($content, true);
	    $a = $data2['records'];
	    $opname = $a['Operator'];
	    if($opname=='Vodafone'){
	        $opname = "VI";
	    }
        $d = $this->Db_model->getAlldata("select * from operator where operatorname='$opname'");
        $dd = array_merge($data2,$d);
        // print_r($dd);exit;
        return $dd;
        
	}
	
	public function Dthinfo($mobile,$operator)
	{
	    
		$request ="";
		$param['apikey'] = 'baa7cfc852f403a738f9b2d0614e43fc';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/Dthinfo.php?".$request;                           
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
        return $data2;
        
	}
	
	public function ElectricityInfo($mobile,$operator)
	{
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/electricinfo.php?".$request;                           
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
        return $data2;
        
	}
	
	public function GasInfo($mobile,$operator)
	{
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		$param['circle'] = 'MH';
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/Bsnl.php?".$request;                           
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
        return $data2;
        
	}
	
	public function PipedGasInfo($mobile,$operator)
	{
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/Gas.php?".$request;                           
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
        return $data2;
        
	}
	
// 	public function FastagInfo($mobile,$operator)
// 	{
// 	    $request ="";
// 		$param['apikey'] = '57814711b8193ea873059df35549ec93';
// 		$param['offer'] = 'roffer';
// 		$param['tel'] = $mobile;
// 		$param['operator'] = $operator;
// 		foreach($param as $key=>$val) 
// 		{ 
// 			$request.= $key."=".urlencode($val); 
// 			$request.= "&"; 
// 		}

//         $url = "https://www.mplan.in/api/Fastag.php?".$request;                           
// 		$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_URL, $url);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
// 		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
//         $content = curl_exec($ch);
//         curl_close($ch); 
//         $data2=json_decode($content);
//         return $data2;
        
// 	}
	
	public function FastagInfo($mobile,$operator)
	{
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
		//https://www.mplan.in/api/Fastag.php?apikey=[yourapikey]&offer=roffer&tel=[Vehicle Number like AS02Q1234 only in capital letter ]&operator=(Given below Like,HDFC,)
		$url = "https://www.mplan.in/api/Fastag.php?".$request;  
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
        $data2=json_decode($content);
        //print_r($data2);
        return $data2;
	}
	
	public function InsuranceInfo($mobile,$operator,$mobno)
	{
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		$param['mob'] = $mobno;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/operatorinfo.php??".$request;                           
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
        return $data2;
        
	}
	
	public function WaterInfo($mobile,$operator)
	{
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['offer'] = 'roffer';
		$param['tel'] = $mobile;
		$param['operator'] = $operator;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}

        $url = "https://www.mplan.in/api/Water.php?".$request;                           
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
        return $data2;
        
	}
	
     public function insert_sub_recharge($cus_id,$cus_type,$amount,$deviceName,$deviceId){
	    
	    $ip = $this->input->ip_address();
        $data = array(
            		'message'	=>	"Recharge submitted of amount : $amount,From Ip address : $ip ,Device name : $deviceName ,Device Id :$deviceId",
            		'log_ip'	=>	$this->input->ip_address(),
            		'medium'	=>	'APP',
            		'log_user'  =>  $cus_id,
            		'log_type'  => $cus_type,
            		'log_intime' => date('Y-m-d H:i:s'),
            		'device_name'=>$deviceName,
            		'device_id'=>$deviceId
            	);
        $this->db->insert('exr_log',$data);
        return true;
	}
	
	
	
	public function insert_insufficent_fund_recharge($cus_id,$cus_type,$amount,$deviceName,$deviceId){
	    
	    $ip = $this->input->ip_address();
        $data = array(
            		'message'	=>	"Insufficent Fund for Recharge of amount : $amount,From Ip address : $ip ,Device name : $deviceName ,Device Id :$deviceId",
            		'log_ip'	=>	$this->input->ip_address(),
            		'medium'	=>	'APP',
            		'log_user'  =>  $cus_id,
            		'log_type'  => $cus_type,
            		'log_intime' => date('Y-m-d H:i:s'),
            		'device_name'=>$deviceName,
            		'device_id'=>$deviceId
            	);
        $this->db->insert('exr_log',$data);
        return true;
	}
	
	
	public function insert_same_recharge($cus_id,$cus_type,$amount,$deviceName,$deviceId){
	    
	    $ip = $this->input->ip_address();
        $data = array(
            		'message'	=>	"Same Recharge submitted of amount : $amount,From Ip address : $ip ,Device name : $deviceName ,Device Id :$deviceId",
            		'log_ip'	=>	$this->input->ip_address(),
            		'medium'	=>	'APP',
            		'log_user'  =>  $cus_id,
            		'log_type'  => $cus_type,
            		'log_intime' => date('Y-m-d H:i:s'),
            		'device_name'=>$deviceName,
            		'device_id'=>$deviceId
            	);
        $this->db->insert('exr_log',$data);
        return true;
	}
	
	public function check_if_valid_user($cus_id,$cus_type,$cus_mobile,$pin,$pass,$deviceName,$deviceId,$amount){
        $enc_mobile=$this->Encryption_model->encode($cus_mobile);
        $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $this->db->where('cus_pass', $pass);
        $this->db->where('cus_pin', $pin);
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() == 1){   
            return true;
        }else{
            $ip = $this->input->ip_address();
            $data = array(
        			'message'	=>	"Failed Recharge Attempt of Amount $amount Of Customer Id $cus_id with Mobile Number $cus_mobile ,Pin $pin And Password $pass from Ip : $ip , Device name : $deviceName and Device Id : $deviceId",
        			'log_ip'	=>	$ip,
        			'medium'	=>	'APP',
        			'log_user'  =>  $cus_id,
        			'log_type'  => $cus_type,
        			'log_intime' => date('Y-m-d H:i:s'),
        			'device_name'=>$deviceName,
        			'device_id'=>$deviceId
        	);
        	$update = $this->db->insert('exr_log',$data);
            return false;
        }
   }
   
   
   public function sendsms(){
       
       $txn_crdt="10";
       $total="100";
       $message = "Dear member, your account has successfully credited rs.".$txn_crdt.". Your closing balance is rs.".$total;
       $this->msg_model->sendsms('9762366696',$message);
   }

    
    public function check_if_user_exists($mobile,$email){
	    
	    $enc_mobile=$this->Encryption_model->encode($mobile);
	    
	    $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $this->db->or_where('cus_email', $email);
       
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
	    
	}
	
	
    public function check_if_user_exists_signup($mobile){
	    
	    $enc_mobile=$this->Encryption_model->encode($mobile);
	    $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
	    
	}
	
    public function check_if_user_loggedin($cus_id,$cus_type,$amount,$deviceName,$deviceId)
    {
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('cus_id', $cus_id);
        $query = $this->db->get();
        if ($query->num_rows() == 1)
        {
            if($query->result()[0]->login_status == 'loggedin'){
                return true;
            }else{
                
                $ip = $this->input->ip_address();
                $data = array(
            		'message'	=>	"Failed Recharge attempt of amount : $amount,From Ip address : $ip ,Device name : $deviceName ,Device Id :$deviceId",
            		'log_ip'	=>	$this->input->ip_address(),
            		'medium'	=>	'APP',
            		'log_user'  =>  $cus_id,
            		'log_type'  => $cus_type,
            		'log_intime' => date('Y-m-d H:i:s'),
            		'device_name'=>$deviceName,
            		'device_id'=>$deviceId
            	);
        	    $this->db->insert('exr_log',$data);
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    	//query to allow user to login if mobile and password matches
    public function check_if_already_login($mobile,$deviceId,$deviceName)
    {
        $enc_mobile=$this->Encryption_model->encode($mobile);
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $query = $this->db->get();
        if ($query->num_rows() == 1)
        {
            $cus_id = $query->result()[0]->cus_id;
            $cus_type = $query->result()[0]->cus_type;
            $data = array(
        			'message'	=>	'Logged Out User, Failed Login Attempt',
        			'log_ip'	=>	$this->input->ip_address(),
        			'medium'	=>	'APP',
        			'log_user'  =>  $cus_id,
        			'log_type'  => $cus_type,
        			'log_intime' => date('Y-m-d H:i:s'),
        			'device_name'=>$deviceName,
        			'device_id'=>$deviceId
        	);
        	$update = $this->db->insert('exr_log',$data);
    	
            if($query->result()[0]->login_status == 'loggedin' && $query->result()[0]->deviceId != $deviceId){
                return true;
            }else{
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function check_login_attempts($cus_id){
        $i=0;
        $query = $this->db->select('*')->from('exr_log')->where('log_user',$cus_id)->order_by('log_id','DESC')->limit('2')->get();
        //echo $this->db->last_query();
        if($query->num_rows() > 0){
            $data = $query->result();
            foreach($data as $rec){
                if(strpos(($rec->message), 'Failed Login Attempt')){
                    $i = $i+1;
                }
            }
            if($i == 2){
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function user_login($mob,$pass,$deviceId,$deviceName,$otp)
    {
        
        $ipadd=$this->input->ip_address();
        $inputs="$mob,$pass,$deviceId,$deviceName";
        $this->db->insert('inputdata',array('ip'=>$ipadd,'inputs'=>$inputs,'function_name'=>'user_login'));
        $enc_mobile=$this->Encryption_model->encode($mob);
        
        $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $this->db->where('avability_status !=', '1');
        $query = $this->db->get();
        //$this->db->last_query();
        if ($query->num_rows() == 1)
        {   
            $cus_id = $query->result()[0]->cus_id;
            $cus_type = $query->result()[0]->cus_type;
            $cus_email = $query->result()[0]->cus_email;
            
            if($this->Encryption_model->decode($query->result()[0]->cus_pass) == $pass)
            {
                $message = "";
                $subject = "";
                
                $this->getpassotp($mob,$otp);
                
                $data = array(
            			'message'	=>	"User Logged In ,Logged in Device id is $deviceId and device name is $deviceName",
            			'log_ip'	=>	$this->input->ip_address(),
            			'medium'	=>	'APP',
            			'log_user'  =>  $cus_id,
            			'log_type'  => $cus_type,
            			'log_intime' => date('Y-m-d H:i:s'),
            			'device_name'=>$deviceName,
            			'device_id'=>$deviceId
            	);
            	$update = $this->db->insert('exr_log',$data);
        	   // $msg="Login OTP IS $otp, User Logged In , Logged in Device id is $deviceId and device name is $deviceName";
        	   // $sub="Logging OTP";
        	    $this->Email_model->send_email($cus_email,$msg,$sub);
                $cusid = $query->result()[0]->cus_id;
                $data = [
                    'login_status' => 'loggedin',
                    'deviceId' => $deviceId
                ];
                $this->db->where('cus_id',$cusid);
                $update = $this->db->update('customers',$data);  
                //echo $this->db->last_query();
                if($update){
                    $sendData = array();
                    $arr = [
                        "cus_id" => $query->result()[0]->cus_id,
                        "cus_name" => $query->result()[0]->cus_name,
                        "cus_type" => $query->result()[0]->cus_type,
                        "cus_mobile" => $this->Encryption_model->decode($query->result()[0]->cus_mobile),
                        "cus_state" => $query->result()[0]->cus_state,
                        "cus_city" => $query->result()[0]->cus_city,
                        "cus_pincode" => $query->result()[0]->cus_pincode,
                        "cus_pass" => $query->result()[0]->cus_pass,
                        "cus_pin" => $query->result()[0]->cus_pin,
                        "cus_email" => $query->result()[0]->cus_email,
                        "package_id" => $query->result()[0]->package_id
                        ];
                    array_push($sendData,$arr);
                    return $sendData;
                }else{
                    return "Oops Something Went Wrong! Try Again Later";
                }
            }
            else
            {
                $cus_id = $query->result()[0]->cus_id;
                $cus_type = $query->result()[0]->cus_type;
                $data = array(
            			'message'	=>	"Invalid Entered Password i.e $pass , Failed Login Attempt from  Device id  $deviceId and device name  $deviceName ",
            			'log_ip'	=>	$this->input->ip_address(),
            			'medium'	=>	'APP',
            			'log_user'  =>  $cus_id,
            			'log_type'  => $cus_type,
            			'log_intime' => date('Y-m-d H:i:s'),
            			'device_name'=>$deviceName,
            			'device_id'=>$deviceId
            	);
            	$update = $this->db->insert('exr_log',$data);
            	$msg="Invalid Entered Password i.e $pass , Failed Login Attempt from  Device id  $deviceId and device name  $deviceName";
        	    $sub="Failed Loging Attempt";
        	   // $this->Email_model->send_email($cus_id,$msg,$sub);
                $attempts = $this->check_login_attempts($cus_id);
                if($attempts){
                    $data = [
                        'avability_status' => '1'
                    ];
                    $this->db->where('cus_id',$cus_id);
                    $update = $this->db->update('customers',$data);
                    if($update){
                        return "Account Blocked! You have Reached Your Login Attempts Limit! Please Contact Admin";
                    }else{
                        return "Oops Something Went Wrong! Try Again Later";
                    }
                }else{
                    return "Entered Mobile Or Password is Invalid ";
                }
            }
        }
        else
        {
            return "Entered Mobile Invalid ";
        }
    }
    
    public function recharge_history_from_to($from,$to,$cusid){
        $sqlquery = "select ef.recid,ef.reqdate,ef.recmedium,ef.amount,ef.status,ef.mobileno,c.cus_name,ef.dispute_status,ef.statusdesc,o.operatorname,ef.operator,o.operator_image,ef.retailer,ef.master,ef.distributor,(SELECT txn.txn_id FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and (txn.txn_type='discount' || txn.txn_type='Recharge Failed'||txn.txn_type='recharge') ORDER BY txn.txn_id DESC LIMIT 1) as txn_id,(SELECT txn.txn_clbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cusid' ORDER BY txn.txn_id DESC LIMIT 1) as txn_clbal,(SELECT txn.txn_opbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cusid' ORDER BY txn.txn_id ASC LIMIT 1) as txn_opbal from exr_rechrgreqexr_rechrgreq_fch as ef , customers as c,operator as o where o.opcodenew =ef.operator and ef.apiclid=c.cus_id and ef.apiclid='$cusid' and DATE(ef.reqdate) >= '$from' and DATE(ef.reqdate) <= '$to' order by recid DESC";
       
        $query= $this->db->query($sqlquery);
        //echo $this->db->last_query();
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function check_if_valid_user_details($cus_id,$cus_type,$cus_mobile,$pin,$pass,$deviceName,$deviceId,$function){
        $enc_mobile=$this->Encryption_model->encode($cus_mobile);
        $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $this->db->where('cus_pass', $pass);
        $this->db->where('cus_pin', $pin);
        $this->db->where('login_status','loggedin');
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() == 1){   
            return true;
        }else{
            $ip = $this->input->ip_address();
            $data = array(
        			'message'	=>	"Failed $function Of Customer Id $cus_id with Mobile Number $cus_mobile ,Pin $pin And Password $pass from Ip : $ip , Device name : $deviceName and Device Id : $deviceId",
        			'log_ip'	=>	$ip,
        			'medium'	=>	'APP',
        			'log_user'  =>  $cus_id,
        			'log_type'  => $cus_type,
        			'log_intime' => date('Y-m-d H:i:s'),
        			'device_name'=>$deviceName,
        			'device_id'=>$deviceId
        	);
        	$update = $this->db->insert('exr_log',$data);
            return false;
        }
   }
   
   public function change_password($cusid,$pass,$newpass)
    {
    
        $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_id', $cusid);
         
        $query = $this->db->get();
        if ($query->num_rows() == 1)
        {
            if($this->Encryption_model->decode($query->result()[0]->cus_pass) == $pass)
            {
                $data = [
                    'cus_pass' => ($this->Encryption_model->encode($newpass)),
                ];
                $this->db->where('cus_id',$cusid);
                $update = $this->db->update('customers',$data);  
                //echo $this->db->last_query();
                if($update){
                    $msg="Change Password successfully";
            	    $sub="Change Password";
            	    $this->Email_model->send_email($cusid,$msg,$sub);
            	    
            	    $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
                    $this->db->from('customers');
                    $this->db->where('cus_id',$cusid);
                    $query = $this->db->get();
                    $sendData = array();
                    $arr = [
                        "cus_id" => $query->result()[0]->cus_id,
                        "cus_name" => $query->result()[0]->cus_name,
                        "cus_type" => $query->result()[0]->cus_type,
                        "cus_mobile" => $this->Encryption_model->decode($query->result()[0]->cus_mobile),
                        "cus_state" => $query->result()[0]->cus_state,
                        "cus_city" => $query->result()[0]->cus_city,
                        "cus_pincode" => $query->result()[0]->cus_pincode,
                        "cus_pass" => $query->result()[0]->cus_pass,
                        "cus_pin" => $query->result()[0]->cus_pin,
                        "cus_email" => $query->result()[0]->cus_email,
                        "package_id" => $query->result()[0]->package_id
                        ];
                    array_push($sendData,$arr);
                    return $sendData;
                }else{
                    $msg="Failed Change Password Attempt";
            	    $sub="Failed Change Password attempt";
            	    $this->Email_model->send_email($cusid,$msg,$sub);
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function changepin($id,$curr_pin,$new_pin){
        $curr_pin = $this->Encryption_model->encode($curr_pin);
        $new_pin = $this->Encryption_model->encode($new_pin);
        $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_id',$id);
        $this->db->where('cus_pin',$curr_pin);
        
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            $data = array('cus_pin' => $new_pin );
            $this->db->where('cus_id',$id);
            $this->db->update('customers',$data);
            if($this->db->affected_rows()=='1'){
                $msg="Pin Changed  successfully";
        	    $sub=":Pin changed";
        	    $this->Email_model->send_email($id,$msg,$sub);
        	    
        	    $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
                $this->db->from('customers');
                $this->db->where('cus_id',$id);
                $query = $this->db->get();
                $sendData = array();
                $arr = [
                    "cus_id" => $query->result()[0]->cus_id,
                    "cus_name" => $query->result()[0]->cus_name,
                    "cus_type" => $query->result()[0]->cus_type,
                    "cus_mobile" => $this->Encryption_model->decode($query->result()[0]->cus_mobile),
                    "cus_state" => $query->result()[0]->cus_state,
                    "cus_city" => $query->result()[0]->cus_city,
                    "cus_pincode" => $query->result()[0]->cus_pincode,
                    "cus_pass" => $query->result()[0]->cus_pass,
                    "cus_pin" => $query->result()[0]->cus_pin,
                    "cus_email" => $query->result()[0]->cus_email,
                    "package_id" => $query->result()[0]->package_id
                    ];
                array_push($sendData,$arr);
                return $sendData;
                
            }else{
                
                $msg="Pin Change Attempt";
        	    $sub=":Failed Pin change Attempt";
        	    $this->Email_model->send_email($id,$msg,$sub);
               return false;
            }
           
        }else{
            return false;
        }
        
    }
    
    public function view_credit_wallet($cusid,$from,$to)
    {
        $array = array();
        $check_cusid['cusid'] = $cusid;
        
        $this->db->select(array('trnx.txn_id','trnx.txn_agentid','trnx.txn_opbal','trnx.txn_crdt','trnx.txn_dbdt','trnx.txn_clbal','trnx.txn_type','trnx.txn_date as txn_time','cust.cus_name','cust.cus_type','cust.cus_mobile','txn_fromto'));
        $this->db->from('exr_trnx as trnx ');
        $this->db->join('customers as cust','trnx.txn_agentid = cust.cus_id');
        if($check_cusid)
        {
            $this->db->where('cus_id', $cusid);
            $this->db->where_in('trnx.txn_type', array('Direct Credit','Credited','AEPS Direct Credit'));
            $this->db->where('date(trnx.txn_date)>=',$from);
            $this->db->where('date(trnx.txn_date)<=',$to);
            $this->db->order_by('trnx.txn_id DESC');
        }
        
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() > 0)
        {
            
            foreach($query->result() as $rec){
                
                if($rec->txn_fromto != '0'){
                    $this->db->select(array('cus_name','cus_mobile','cus_id'));
                    $this->db->from('customers');
                    $this->db->where('cus_id',$rec->txn_fromto);
                    $data = $this->db->get();
                    
                    if ($data->num_rows() > 0)
                    {
                        $datares = $data->result();
                        $cus_name = $datares[0]->cus_name;
                        $cus_mobile = $datares[0]->cus_mobile;
                        $cus_id = $this->Encryption_model->decode($datares[0]->cus_id);
                    }else{
                        $cus_name = 'Admin';
                        $cus_mobile = '0';
                        $cus_id ='0';
                    }
                    
                }else{
                    $cus_name = 'Admin';
                    $cus_mobile = '0';
                    $cus_id ='0';
                }
                
                $arr = array(
                'txn_id' => $rec->txn_id,
                'from_cus_name' => $cus_name,
                'form_cus_mobile' => $cus_mobile,
                'from_cus_id' => $cus_id,
                'txn_agentid' => $rec->txn_agentid,
                'txn_opbal' => $rec->txn_opbal,
                'txn_crdt' => $rec->txn_crdt,
                'txn_dbdt' => $rec->txn_dbdt,
                'txn_clbal' => $rec->txn_clbal,
                'txn_type' => $rec->txn_type,
                'txn_time' => $rec->txn_time,
                'cus_name' => $rec->cus_name
                );
                array_push($array,$arr);
            }
            
            return $array;
        }
        else
        {
            return false;
        }
    }
    
    public function view_debit_wallet($cusid,$from,$to)
    {
    
        $array = array();
        $check_cusid['cusid'] = $cusid;
        
        $this->db->select(array('trnx.txn_id','trnx.txn_agentid','trnx.txn_opbal','trnx.txn_crdt','trnx.txn_dbdt','trnx.txn_clbal','trnx.txn_type','trnx.txn_date as txn_time','cust.cus_name','cust.cus_type','cust.cus_id','cust.cus_mobile','txn_fromto'));
        $this->db->from('exr_trnx as trnx ');
        $this->db->join('customers as cust','trnx.txn_agentid = cust.cus_id');
        if($check_cusid)
        {
            $this->db->where('trnx.txn_agentid', $cusid);
            $this->db->where_in('trnx.txn_type', array('Fund Transfer'));
            $this->db->where('date(trnx.txn_date)>=',$from);
            $this->db->where('date(trnx.txn_date)<=',$to);
            $this->db->order_by('trnx.txn_id DESC');
        }
       
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            
            foreach($query->result() as $rec){
                
                if($rec->txn_fromto != '0'){
                    $this->db->select(array('cus_name','cus_mobile','cus_id'));
                    $this->db->from('customers');
                    $this->db->where('cus_id',$rec->txn_fromto);
                    $data = $this->db->get();
                    
                    if ($data->num_rows() > 0)
                    {
                        $datares = $data->result();
                        $cus_name = $datares[0]->cus_name;
                        $cus_mobile = $datares[0]->cus_mobile;
                        $cus_id =$datares[0]->cus_id;
                    }else{
                        $cus_name = 'Admin';
                        $cus_mobile = '0';
                        $cus_id ='0';
                    }
                    
                }else{
                    $cus_name = 'Admin';
                    $cus_mobile = '0';
                    $cus_id ='0';
                }
                
                $arr = array(
                'txn_id' => $rec->txn_id,
                'to_cus_name' => $cus_name,
                'to_cus_mobile' => $cus_mobile,
                'to_cus_id' => $cus_id,
                'txn_agentid' => $rec->txn_agentid,
                'txn_opbal' => $rec->txn_opbal,
                'txn_crdt' => $rec->txn_crdt,
                'txn_dbdt' => $rec->txn_dbdt,
                'txn_clbal' => $rec->txn_clbal,
                'txn_type' => $rec->txn_type,
                'txn_time' => $rec->txn_time,
                'cus_name' => $rec->cus_name
                );
                array_push($array,$arr);
            }
            
            return $array;
        }else
        {
            return false;
        }
    }
    
    public function get_wallet_balance($cusid)
    {
    
        $check_cusid['cusid'] = $cusid;
        
        $this->db->select(array('e.txn_agentid','e.txn_clbal','e.txn_date'));
        $this->db->from('exr_trnx as e');
        if($check_cusid)
        {
            $this->db->where('e.txn_agentid',$cusid);
            $this->db->order_by('e.txn_id','DESC');
            $this->db->limit('1');
        }
       
        $query = $this->db->get();
        if ($query->num_rows() >0)
        {
            return $query -> result();
        }
        else
        {
            return false;
        }
    }
    
    public function ledger_from_to($from,$to,$cusid){
        //$sqlquery = " SELECT * FROM `exr_trnx` as e where txn_type In ('Direct Credit','Fund Transfer') and DATE(e.txn_date) <= '$to' and DATE(e.txn_date) >= '$from' and txn_agentid='$cusid' ORDER BY e.txn_id DESC";
        $sqlquery = " SELECT * FROM `exr_trnx` as e where  DATE(e.txn_date) <= '$to' and DATE(e.txn_date) >= '$from' and txn_agentid='$cusid' ORDER BY e.txn_id DESC";

        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function getcommslab($cusid){
        $query = "SELECT c.cus_id,c.package_id,e.package_id,ep.packcomm_comm,ep.packcomm_opcode,o.operatorname FROM customers as c,exr_package as e,exr_packagecomm as ep ,operator as o WHERE c.package_id = e.package_id AND e.package_id = ep.packcomm_name AND o.opcodenew=ep.packcomm_opcode AND c.cus_id='$cusid'";
        $data = $this->db->query($query);
        if($data->num_rows() >0){
            return $data->result();   
        }else{
            return false;
        }
    }
    
    public function disputehistory($cusid){
        $sqlquery = "SELECT *  FROM `ticket` as t JOIN exr_rechrgreqexr_rechrgreq_fch as rec ON t.rec_id=rec.recid WHERE `cus_id` = '$cusid' ORDER BY t_id DESC";
      
        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function create_retailer_api($mobile,$name,$email,$password,$date,$dis_cus_id){
        
        $pin=rand ( 10000 , 99999 );
        $enc_pin=$this->Encryption_model->encode($pin);
        $enc_mobile=$this->Encryption_model->encode($mobile);
        $pass= $this->Encryption_model->encode($password);
        $data = array(
            'cus_mobile' => $enc_mobile,
            'cus_name' => $name,
            'cus_email' => $email,
            'cus_pass' => $pass,
            'cus_added_date' => $date,
            'cus_reffer' => $dis_cus_id,
			'cus_type' => 'retailer',
			'cus_status' => '1',
			'avability_status' => '0',
			'cus_pin'=>$enc_pin
		);
		$message=array('mobile'=>$mobile,'pass'=>$password,'pin'=>$pin,'id'=>$last_id,'name'=>$name);
 		//print_r($message);exit;
        $this->msg_model->registration($message);
        /*$message = "Your Login ".$mobile." & Password ".$password." From, EVILLD";
        //"Welcome in E Digital village  family, your member id:$mobile, login pass:$password and Pin:$pin, Thank You";
        $message=urlencode($message);
		$get_url="http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$mobile&sendername=MUZOTP&message=$message&templateid=1707161018218714749";
		file_get_contents($get_url);
		*/
        /*$url="http://sms.satmatgroup.com/api/sendhttp.php?authkey=23410AMIRwu1pK5a421472&mobiles=$mobile&message=$message&sender=NEWMSG&route=6&country=0";
        //$url = str_replace(" ", "%20%", $url);
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $curl_scraped_page = curl_exec($ch); 
        curl_close($ch);*/
        
		return $this->db->insert('customers',$data); 
		
		
    }

	public function create_distributor_api($mobile,$name,$email,$password,$date,$mst_cus_id){
        $pass= $this->Encryption_model->encode($password);
        
        $pin=rand ( 10000 , 99999 );
        $enc_pin=$this->Encryption_model->encode($pin);
        $enc_mobile=$this->Encryption_model->encode($mobile);

        $data = array(
            'cus_mobile' => $enc_mobile,
            'cus_name' => $name,
            'cus_email' => $email,
            'cus_pass' => $pass,
            'cus_added_date' => $date,
            'cus_reffer' => $mst_cus_id,
			'cus_type' => 'distributor',
			'cus_status' => '1',
			'avability_status' => '0',
			'cus_pin'=>$enc_pin
		);
		$ddd = $this->db->insert('customers',$data);   
		$last_id = $this->db->insert_id();
		$this->Db_model->insert_update('application_status_service',array('cus_id'=>$last_id));
		$message=array('mobile'=>$mobile,'pass'=>$password,'pin'=>$pin,'id'=>$last_id,'name'=>$name);
        $this->msg_model->registration($message);
		// $this->msg->msg_model(array('pass'=>$password,'mobile'=>$mobile));
		/*$message = "Your Login ".$mobile.", & Password ".$password." From,EVILLD"; 
        // $message = "Welcome in E Digital Village family, your member id:$mobile, login pass:$password, Thank You";
        $message=urlencode($message);
		$get_url="http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$mobile&sendername=MUZOTP&message=$message&templateid=1707161018218714749";
		file_get_contents($get_url);
		*/
        /*$msg="http://sms.satmatgroup.com/api/sendhttp.php?authkey=23410AMIRwu1pK5a421472&mobiles=$mobile&message=$message&sender=MASTER&route=6&country=0";
        $msg = str_replace(" ", "+", $msg);
        $response = file_get_contents($msg); */
		return $ddd;
		
    }
    
    
    public function registerUser($mobile,$name,$email,$address,$date,$customer_type,$amt){
        
        // $admin_data = $this->Db_model->getAlldata("SELECT $customer_type as scheme_id FROM `user` where user_id = '6' ");
        // if($admin_data){
        //     $scheme_id = $admin_data['0']->scheme_id;
        // }else{
            $scheme_id = '0';
        // }
        
        
        $pin = rand(1000,9999);
        $password = rand(100000,999999);
        $enc_pin = $this->Encryption_model->encode($pin);
        $enc_mobile = $this->Encryption_model->encode($mobile);
        $pass = $this->Encryption_model->encode($password);
        $data = array(
            'cus_mobile' => $enc_mobile,
            'cus_name' => $name,
            'scheme_id'=>$scheme_id,
            'cus_email' => $email,
            'cus_pass' => $pass,
            'cus_added_date' => $date,
            'cus_reffer' => '0',
			'cus_type' => 'retailer',
			'cus_status' => '1',
			'avability_status' => '0',
			'cus_pin'=>$enc_pin,
			'customer_signup_as' => $customer_type,
			'registration_amt' => $amt
		);
		$res = $this->db->insert('customers',$data); 
		$last_id = $this->db->insert_id();
		if($last_id){
	    	$message=array('mobile'=>$mobile,'pass'=>$password,'pin'=>$pin,'id'=>$last_id,'name'=>$name);
	    	$this->Db_model->insert_update('application_status_service',array('cus_id'=>$last_id));
            $this->msg_model->registration($message);
            return true;
		}else{
		    return false;
		}    
            
		
    }
    
    public function user_list($cusid){
        $sqlquery = "SELECT c.cus_id,c.cus_name,c.cus_mobile,(SELECT e.txn_clbal FROM exr_trnx as e WHERE e.txn_agentid=c.cus_id ORDER BY e.txn_id DESC LIMIT 1) AS clbal from customers as c where c.cus_reffer='$cusid' ORDER BY c.cus_id DESC";
        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            
            $sendData = array();
            foreach( $query->result() as $rec){
                $arr = [
                    "cus_id" => $rec->cus_id,
                    "cus_name" => $rec->cus_name,
                    "cus_mobile" => $this->Encryption_model->decode($rec->cus_mobile),
                    "clbal" => $rec->clbal
                    ];
                array_push($sendData,$arr);
            }
            return $sendData;
        }else{
            return false;
        }   
    }
    
    public function getcusid($mobile){
        
        $enc_mobile=$this->Encryption_model->encode($mobile);
        $sqlquery = "SELECT c.cus_id,c.cus_name,c.cus_mobile,(SELECT e.txn_clbal FROM exr_trnx as e WHERE e.txn_agentid=c.cus_id ORDER BY e.txn_id DESC LIMIT 1) AS clbal from customers as c where c.cus_mobile ='$enc_mobile' ORDER BY c.cus_id DESC";
        $query= $this->db->query($sqlquery);
        
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function direct_credit($id,$cus_id,$trnxamount)
	{
		$ip = '';

		$time = time();

		$cups = $this->Db_model->getAlldata("select cus_pin,cus_cutofamt from customers where cus_id='$id'");


		$txn_crdt = $cups[0]->cus_cutofamt + $trnxamount;

		$old = $this->Db_model->getAlldata("select * from exr_trnx where txn_agentid='$id' order by txn_id desc limit 1");

		if(!empty($old))

		{

			$clbal = $old[0]->txn_clbal;

			if($clbal >= $txn_crdt && $trnxamount > 0)

			{

				$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";

				$r = $this->Db_model->getAlldata($sql);

				if($r)

				{

					$closing = $r['0']->txn_clbal;

					$total = $r['0']->txn_clbal + $trnxamount;

					$total1 = $clbal - $trnxamount;



					$txn_dbdt = 0;



					$data12 = array(

						'txn_agentid'=>$id,

						'txn_opbal'=>$clbal,

						'txn_crdt'=>$txn_dbdt,

						'txn_dbdt'=>$trnxamount,

						'txn_fromto'=>$cus_id,

						'txn_clbal'=>$total1,

						'txn_type'=>'Fund Transfer',

						'txn_time'=>time(),

						'txn_checktime'=>$id.time(),

						'txn_date'=>DATE('Y-m-d H:i:s'),

						'txn_ip'=>$ip

					);

					$lasttxnrec = $this->Db_model->insert_update('exr_trnx',$data12);

					$data13 = array(

						'txn_agentid'=>$cus_id,

						'txn_opbal'=>$closing,

						'txn_crdt'=>$trnxamount,

						'txn_dbdt'=>$txn_dbdt,

						'txn_fromto'=>$id,

						'txn_clbal'=>$total,

						'txn_type'=>'Direct Credit',

						'txn_time'=>time(),

						'txn_checktime'=>$cus_id.time(),

						'txn_date'=>DATE('Y-m-d H:i:s'),

						'txn_ip'=>$ip

					);

					$lasttxnrec1 = $this->Db_model->insert_update('exr_trnx',$data13);
                    return true;
					
				}

				else

				{

					$closing = 0;

					$total = $trnxamount;

					$total1 = $clbal - $trnxamount;

					$txn_dbdt = 0;


					$data12 = array(

						'txn_agentid'=>$id,

						'txn_opbal'=>$clbal,

						'txn_crdt'=>$txn_dbdt,

						'txn_dbdt'=>$trnxamount,

						'txn_fromto'=>$cus_id,

						'txn_clbal'=>$total1,

						'txn_type'=>'Fund Transfer',

						'txn_time'=>time(),

						'txn_checktime'=>$id.time(),

						'txn_date'=>DATE('Y-m-d h:i:s'),

						'txn_ip'=>$ip

					);

					$lasttxnrec = $this->Db_model->insert_update('exr_trnx',$data12);



					$data13 = array(

						'txn_agentid'=>$cus_id,

						'txn_opbal'=>$closing,

						'txn_crdt'=>$trnxamount,

						'txn_dbdt'=>$txn_dbdt,

						'txn_fromto'=>$id,

						'txn_clbal'=>$total,

						'txn_type'=>'Direct Credit',

						'txn_time'=>time(),

						'txn_checktime'=>$cus_id.time(),

						'txn_date'=>DATE('Y-m-d h:i:s'),

						'txn_ip'=>$ip

					);

					$lasttxnrec1 = $this->Db_model->insert_update('exr_trnx',$data13);
                    return true;
					

				}

			}

			else

			{

				return false;

			}


		}
	}
	
	public function submitdispute($id,$txn,$from,$type,$subject,$ndate,$tick){   
      
        $data = array(
    		'cus_id'	=>	$id,
    		'ticket_id'	=>	$tick,
    		'reply_from'	=>	$from,
    		'issue'	=>	$type,
    		'subject'	=>	$subject,
    		'ticket_date'	=>	$ndate,
    		'rec_id'	=>	$txn
    	);
    	
    	$this->Db_model->insert_update('ticket',$data);
    	if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    
    } 
    
    public function get_support_byid($where='',$select='*')
    {
    	$this->db->select($select);
    	$this->db->from('ticket');
    	if($where !='')
    		$this->db->where($where);
    	$this->db->order_by('ticket_id','DESC');
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
    
	public function getsupportdata()
    {
        $this->db->select('*');
        $this->db->from('support');
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }
        else
        {
            return false;
        }
    }
    
    public function userdaybook($cus_id){
	    
		$arr = array();
		
	    $sqlpenRec = "select sum(amount) as bal,count(*) as cnt from (select amount from exr_rechrgreqexr_rechrgreq_fch where status='PENDING' and apiclid = '$cus_id') as subt";
		$data = $this->Db_model->getAlldata($sqlpenRec);
		
		if($data[0]->bal == NULL){
		    $bal = 0;
		}else{
		    $bal = $data[0]->bal;
		}
		
		$data1 = array(
		        'type' => "PENDING RECHARGES",
		        'bal' => "$bal",
		        'cnt' => $data[0]->cnt
		    );
		    
		array_push($arr,$data1);
		    
		$sqlSucRec = "select sum(amount) as bal,count(*) as cnt from (select amount from exr_rechrgreqexr_rechrgreq_fch where status='SUCCESS' and apiclid = '$cus_id' and date(reqdate)=CURDATE()) as subt";
		$data = $this->Db_model->getAlldata($sqlSucRec);
		
		if($data[0]->bal == NULL){
		    $bal = 0;
		}else{
		    $bal = $data[0]->bal;
		}
		
		$data1 = array(
		        'type' => "SUCCESS RECHARGES",
		        'bal' => "$bal",
		        'cnt' => $data[0]->cnt
		    );
		
		array_push($arr,$data1);
		
		$sqlFailRec = "select sum(amount) as bal,count(*) as cnt from (select amount from exr_rechrgreqexr_rechrgreq_fch where status='FAILED' and apiclid = '$cus_id' and date(reqdate)=CURDATE()) as subt";
		$data = $this->Db_model->getAlldata($sqlFailRec);
		
		if($data[0]->bal == NULL){
		    $bal = 0;
		}else{
		    $bal = $data[0]->bal;
		}
		
		$data1 = array(
		        'type' => "FAILED RECHARGES",
		        'bal' => "$bal",
		        'cnt' => $data[0]->cnt
		    );
		
		array_push($arr,$data1);
		
		$sqlFunCre = "select sum(txn_crdt) as bal,count(*) as cnt from exr_trnx where txn_type ='Direct Credit' AND date(txn_date) = CURDATE() AND exr_trnx.txn_agentid='$cus_id'";
		$data = $this->Db_model->getAlldata($sqlFunCre);
		
		if($data[0]->bal == NULL){
		    $bal = 0;
		}else{
		    $bal = $data[0]->bal;
		}
		
		$data1 = array(
		        'type' => "FUND CREDITED",
		        'bal' => "$bal",
		        'cnt' => $data[0]->cnt
		    );
		
		array_push($arr,$data1);
		
		$sqlFunDbt = "select sum(txn_dbdt) as bal,count(*) as cnt from exr_trnx where txn_type ='Fund Transfer Credit' AND date(txn_date) = CURDATE() AND exr_trnx.txn_agentid='$cus_id'";
		$data = $this->Db_model->getAlldata($sqlFunDbt);
		
		if($data[0]->bal == NULL){
		    $bal = 0;
		}else{
		    $bal = $data[0]->bal;
		}
		
		$data1 = array(
		        'type' => "FUND DEBITED",
		        'bal' => "$bal",
		        'cnt' => $data[0]->cnt
		    );
		
		array_push($arr,$data1);
		
		$sqlFunOpen = "SELECT txn_opbal as bal FROM exr_trnx WHERE txn_agentid = '$cus_id' and date(txn_date) = CURDATE() ORDER BY txn_id ASC LIMIT 1";
		$data = $this->Db_model->getAlldata($sqlFunOpen);
		
		if($data[0]->bal != NULL){
		    $bal = $data[0]->bal;
		}else{
		    $sqlFunOpen = "SELECT txn_opbal as bal, count(*) as cnt FROM exr_trnx WHERE txn_agentid = '$cus_id' ORDER BY txn_id DESC LIMIT 1";
		    $data = $this->Db_model->getAlldata($sqlFunOpen);
		    if($data[0]->bal == NULL){
		        $bal = 0;
		    }else{
		        $bal = $data[0]->bal;
		    }
		}
		
		$data1 = array(
		        'type' => "OPENING BALANCE",
		        'bal' => "$bal",
		        'cnt' => ''
		    );
		
		array_push($arr,$data1);
		
		$sqlFunOpen = "SELECT txn_clbal as bal FROM exr_trnx WHERE txn_agentid = '$cus_id' ORDER BY txn_id DESC LIMIT 1";
		$data = $this->Db_model->getAlldata($sqlFunOpen);
		
		if($data[0]->bal == NULL){
		    $bal = 0;
		}else{
		    $bal = $data[0]->bal;
		}
		
		$data1 = array(
		        'type' => "CLOSING BALANCE",
		        'bal' => "$bal",
		        'cnt' => ''
		    );
		
		array_push($arr,$data1);
		
		return $arr;
	}
	
	public function add_wallet_balance($cus_id,$amount,$transfer_type,$bank_name,$transaction_ref,$transaction_id)
	{
		$cusdt = $this->Db_model->getAlldata("select cus_mobile from customers where cus_id='".$cus_id."'");
		$transaction_date = date("Y-m-d h:s:i");
		$txn_crdt = $amount;
		$txntype = 'UPI';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
		$lastid = 0;
		

		$sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
		$r = $this->Db_model->getAlldata($sql);
		if($r)
		{
			$closing = $r['0']->txn_clbal;
			$total = $r['0']->txn_clbal + $txn_crdt;
			$txn_dbdt = 0;
			$lasttxnrec = $this->Db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_time'=>$time,'txn_date'=>$date,'txn_ip'=>$ip,'txn_comment'=>$bank_name,'	transaction_id'=> $transaction_id,'transaction_ref'=> $transaction_ref));
			if($lasttxnrec)
			{
				$lastid = $this->db->insert_id();
				$message = "Dear member, your account has successfully credited rs.".$txn_crdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');

				$this->msg_model->sendsms($cusdt[0]->cus_mobile,$message);		
				return true;
			}
			else
			{
				return false;

			}
		}
		else
		{
			$closing = 0;
			$total = $txn_crdt;
			$txn_dbdt = 0;
			$lasttxnrec= $this->Db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_date'=>$date,'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip,'txn_comment'=>$bank_name,'transaction_id'=> $transaction_id,'transaction_ref'=> $transaction_ref));
			if($lasttxnrec)
			{
				$lastid = $this->db->insert_id();
				$message = "Dear member, your account has successfully credited rs.".$txn_crdt.". Your closing balance is rs.".$total.". ".$this->config->item('title');
                $this->msg_model->sendsms($cusdt[0]->cus_mobile,$message);
				return true;

			}
			else
			{
				return false;
			}
		}

	}
	
	public function getupidetails($cus_id){
        $data = $this->Db_model->getAlldata('select upi_id , minimun_amount from user where user_id = 6');
        if($data){
            return $data;
        }else{
            return false;
        }
    }
    
    
    /* public function verify_pin($mobile,$pin)
    {
    
        $enc_mobile=$this->Encryption_model->encode($mobile);
        $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile','cus_state','cus_city','cus_pincode','cus_pass','cus_pin','cus_email','package_id'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
          
        $query = $this->db->get();
      
        if ($query->num_rows() == 1)
        {
            if($this->Encryption_model->decode($query->result()[0]->cus_pin) == $pin)
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
            return false;
        }
    }*/
    
    public function getpinotp($mobile,$otp){
        $data = array('mobile'=>$mobile,'otp'=>$otp);
        $this->msg_model->otp_reg_app($data);
        /*$message = $otp." is your OTP From, EVILLD"; 
        // "You OTP for Changing Pin Is $otp , Please do not share with anyone. Thank You.";
        $message=urlencode($message);
		$get_url="http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$mobile&sendername=MUZOTP&message=$message&templateid=1707161018221759352";
		file_get_contents($get_url);
		*/
        /*$msg="http://sms.satmatgroup.com/api/sendhttp.php?authkey=23410AMIRwu1pK5a421472&mobiles=$mobile&message=$message&sender=NEWMSG&route=6&country=0";
		$msg = str_replace(" ", "+", $msg);
        $ch = curl_init($msg); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $curl_scraped_page = curl_exec($ch); 
        curl_close($ch);*/
        return true;
    }
    
    public function getpassotp($mobile,$otp){
        $data = array('mobile'=>$mobile,'otp'=>$otp);
        $this->msg_model->otp_reg_app($data);
        /*$message = $otp." is your OTP From, EVILLD"; 
        $message=urlencode($message);
		$get_url="http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$mobile&sendername=MUZOTP&message=$message&templateid=1707161018221759352";
		file_get_contents($get_url);*/
        /*$msg="http://sms.satmatgroup.com/api/sendhttp.php?authkey=23410AMIRwu1pK5a421472&mobiles=$mobile&message=$message&sender=NEWMSG&route=6&country=0";
		$msg = str_replace(" ", "+", $msg);
        $ch = curl_init($msg); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $curl_scraped_page = curl_exec($ch); 
        curl_close($ch);*/
        return true;
    }
    
    public function forgetpin($cus_id,$deviceId,$deviceName,$cus_type){
        $newpin = rand(10000,99999);
        $pin = $this->Encryption_model->encode($newpin);
        $data = array('cus_pin' => $pin );
        $this->db->where('cus_id',$cus_id);
        $this->db->update('customers',$data);
        if($this->db->affected_rows()=='1'){
            $this->db->select('*');
            $this->db->from('customers');
            $this->db->where('cus_id', $cus_id);
            $query = $this->db->get();
            if ($query->num_rows() == 1)
            {
                $mobile = $this->Encryption_model->decode($query->result()[0]->cus_mobile);
                
                $data = array('mobile'=>$mobile,'pin'=>$newpin);
                       $this->msg_model->forget_pin($data);
            
           /* $msg="Pin Changed  successfully";
    	    $sub=":Pin changed";
    	    $this->Email_model->send_email($cus_id,$msg,$sub);*/
    	    /*$message = "Dear ".$this->config->item('title').", your pin is ".$newpin." From,EVILLD"; 
    	    $message=urlencode($message);
    		$get_url="http://priority.muzztech.in/sms_api/sendsms.php?username=EVILLD&password=mayank8790&mobile=$mobile&sendername=MUZOTP&message=$message&templateid=1707161018226094127";
    		file_get_contents($get_url);*/
            }
            /*$msg="http://sms.satmatgroup.com/api/sendhttp.php?authkey=23410AMIRwu1pK5a421472&mobiles=$mobile&message=$message&sender=NEWMSG&route=6&country=0";
    		$msg = str_replace(" ", "+", $msg);
            $ch = curl_init($msg); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            $curl_scraped_page = curl_exec($ch); 
            curl_close($ch);*/
            
    	    $data = array(
    			'message'	=>	"Pin Changed Successfully from  Device id  $deviceId and device name  $deviceName ",
    			'log_ip'	=>	$this->input->ip_address(),
    			'medium'	=>	'APP',
    			'log_user'  =>  $cus_id,
    			'log_type'  => $cus_type,
    			'log_intime' => date('Y-m-d H:i:s'),
    			'device_name'=>$deviceName,
    			'device_id'=>$deviceId
        	);
            $update = $this->db->insert('exr_log',$data);
    	    
           return true;
        }else{
            
            $msg="Pin Change Attempt";
    	    $sub=":Failed Pin change Attempt";
    	    $this->Email_model->send_email($cus_id,$msg,$sub);
    	    
    	    $data = array(
    			'message'	=>	"Failed Pin Forget Attempt from  Device id  $deviceId and device name  $deviceName ",
    			'log_ip'	=>	$this->input->ip_address(),
    			'medium'	=>	'APP',
    			'log_user'  =>  $cus_id,
    			'log_type'  => $cus_type,
    			'log_intime' => date('Y-m-d H:i:s'),
    			'device_name'=>$deviceName,
    			'device_id'=>$deviceId
        	);
            $update = $this->db->insert('exr_log',$data);
            
            return false;
        }
    }

    public function user_list_by_name($cusid,$name){
        $sqlquery = "SELECT c.cus_id,c.cus_name,c.cus_mobile,(SELECT e.txn_clbal FROM exr_trnx as e WHERE e.txn_agentid=c.cus_id ORDER BY e.txn_id DESC LIMIT 1) AS clbal from customers as c where c.cus_reffer='$cusid' and c.cus_name LIKE '%$name%' ORDER BY c.cus_id DESC";
        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function user_list_by_mobile($cusid,$mobile){
        $enc_mobile=$this->Encryption_model->encode($mobile);
        $sqlquery = "SELECT c.cus_id,c.cus_name,c.cus_mobile,(SELECT e.txn_clbal FROM exr_trnx as e WHERE e.txn_agentid=c.cus_id ORDER BY e.txn_id DESC LIMIT 1) AS clbal from customers as c where c.cus_reffer='$cusid' and c.cus_mobile ='$enc_mobile' ORDER BY c.cus_id DESC";
        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function getbannerimages()
    {
    	$this->db->select('*');
    	$this->db->from('banner');
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
    
    public function operator_list($op_type){
        $op_type= $op_type;
        $this->db->select('*');
        $this->db->from('operator');
        $this->db->where('opsertype',$op_type);
        
        $query= $this->db->get();
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    /*public function check_recharge($amount,$mob,$cus_id)
	{
	    $time = time();
	    $timeck = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where apiclid='".$cus_id."' and reqtime='".$time."' order by recid desc limit 1")->result();
	    if(!$timeck)
	    {
	        $recdt = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch where mobileno='".$mob."' and amount='".$amount."' order by recid desc limit 1")->result();
    		if($recdt)
    		{	
    			$date = DateTime::createFromFormat('Y-m-d H:i:s', $recdt[0]->reqdate);
    			$date->modify('+10 minutes');
    			$recdate = $date->format('Y-m-d H:i:s');
    			$now = date('Y-m-d H:i:s');
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
	}*/

    public function checkifsamefundtransfer($cusid,$amount,$to_id){
        $today = new DateTime(date("Y-m-d H:i:s"));
        $check_cusid = $cusid;
        $sqlquery = "select * from exr_trnx where txn_agentid='$cusid' and txn_fromto='$to_id' and `txn_type` LIKE 'Fund Transfer' order by txn_id desc limit 1";
        $query= $this->db->query($sqlquery);
        //echo $this->db->last_query();
        if($query->num_rows() > 0){
            foreach($query->result_array() as $row){
                
               
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['txn_date']);
    			$date->modify('+5 minutes');
    			$recdate = $date->format('Y-m-d H:i:s');
    			$now = date('Y-m-d H:i:s');
    		
            }
            if(($now < $recdate)){
                return true;
            }else{
                return false;
            } 
        }else{
            return false;
        }   
    }
    
    public function fund_request($cus_id,$req_to,$amount,$pay_mode,$bank,$ref_no)
	{

		if($req_to == 'master' || $req_to == 'distributor')
		{
			$cusdt = $this->db->query("select * from customers where cus_id='$cus_id'")->result();
			$payto = $cusdt[0]->cus_reffer;
			$cusdt1 = $this->db->query("select * from customers where cus_id='".$payto."'")->result();

		}
		else
		{
				$cusdt = $this->db->query("select * from customers where cus_id='$cus_id'")->result();
				$cusdt1 = $this->db->query("select * from customers where cus_id='5505'")->result();
				$payto='5505';
		}
		$data = array(
				'cus_id'	=>	$cus_id,
				'request_from'	=>	$req_to,
				'pay_amount'	=>	$amount,
				'pay_mode'	=>	$pay_mode,
				'pay_to'	=>	$payto,
				'pay_bank'	=>	$bank,
				'ref_no'	=>	$ref_no,
				'remarks'	=>	'App',
				'req_date'	=>	DATE('Y-m-d h:i:s')
		);
		
		if($cusdt){
		$message="Dear Member, You have received an fund request of Rs.".$amount." from ".$cusdt[0]->cus_name."";}
		$this->Db_model->insert_update('fund_request',$data);
		return true;

	}

	
	public function view_my_fund_request($from,$to,$cus_id)
	{
		$qry = "select cs.cus_name,cs.cus_id,cs.cus_type,fr.pay_amount,fr.request_from,pay_bank,fr.req_date,fr.req_status,fr.ref_no,fr.res_date,fr.req_id,cs.cus_mobile from customers as cs join fund_request as fr on cs.cus_id = fr.cus_id where fr.cus_id=".$cus_id." order by fr.req_id desc";
		$r =  $this->db->query($qry)->result();
		if($r)
			return $r;

		else
		{
			return false;

		}
	}
	
	public function recharge_history_by_date($date,$cusid){
        $sqlquery = "select ef.recid,ef.reqdate,ef.recmedium,ef.amount,ef.status,ef.mobileno,c.cus_name,ef.dispute_status,ef.statusdesc,o.operatorname,ef.operator,o.operator_image,ef.retailer,ef.master,ef.distributor,(SELECT txn.txn_id FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and (txn.txn_type='discount' || txn.txn_type='Recharge Failed'||txn.txn_type='recharge') ORDER BY txn.txn_id DESC LIMIT 1) as txn_id,(SELECT txn.txn_clbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cusid' ORDER BY txn.txn_id DESC LIMIT 1) as txn_clbal,(SELECT txn.txn_opbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cusid' ORDER BY txn.txn_id ASC LIMIT 1) as txn_opbal from exr_rechrgreqexr_rechrgreq_fch as ef , customers as c,operator as o where o.opcodenew =ef.operator and ef.apiclid=c.cus_id and ef.apiclid='$cusid' and DATE(ef.reqdate) LIKE '$date' order by recid DESC";
       
        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    
    public function recharge_history_by_mobile($mobile,$cusid){
        $sqlquery = "select ef.recid,ef.reqdate,ef.recmedium,ef.amount,ef.status,ef.mobileno,c.cus_name,ef.dispute_status,ef.statusdesc,o.operatorname,ef.operator,o.operator_image,ef.retailer,ef.master,ef.distributor,(SELECT txn.txn_id FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and (txn.txn_type='discount' || txn.txn_type='Recharge Failed'||txn.txn_type='recharge') ORDER BY txn.txn_id DESC LIMIT 1) as txn_id,(SELECT txn.txn_clbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cusid' ORDER BY txn.txn_id DESC LIMIT 1) as txn_clbal,(SELECT txn.txn_opbal FROM exr_trnx as txn WHERE txn.txn_recrefid= ef.recid and txn_agentid='$cusid' ORDER BY txn.txn_id ASC LIMIT 1) as txn_opbal from exr_rechrgreqexr_rechrgreq_fch as ef , customers as c,operator as o where o.opcodenew =ef.operator and ef.apiclid=c.cus_id and ef.apiclid='$cusid' and ef.mobileno ='$mobile' order by recid DESC";
       
        $query= $this->db->query($sqlquery);
        if($query->num_rows () >0){
            return $query->result();
        }else{
            return false;
        }   
    }
    public function enquiry($user_name,$user_mobile,$email,$aadhaar_number,$user_address,$referralId)
    {
        $data = [
                    'user_name' => $user_name,
                    'user_mobile' => $user_mobile,
                    'email' => $email,
                    'user_address' => $aadhaar_number,
                    'aadhaar_number' => $user_address,
                    'referral_id' => $referralId
                    
                ];
                $insert = $this->Db_model->insert_update('register_enquiry',$data);
                if($insert){
                    return true;
                }else{
                    return true;
                }
    }
    
    
    public function user_logout($cus_id)
    {
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('cus_id', $cus_id);
        $query = $this->db->get();
        $cus_type=$query->result()[0]->cus_type;
        //$device_id=$query->result()[0]->deviceId;
        if ($query->num_rows() == 1)
        {
        
            $this->db->where('cus_id', $cus_id);
            $this->db->update('customers',array('login_status'=>'loggedout','deviceId'=>''));
            
            if($this->db->affected_rows()>0){
                $ip = $this->input->ip_address();
                /*$data = array(
            		'message'	=>	"User Logout ,From Ip address : $ip",
            		'log_ip'	=>	$this->input->ip_address(),
            		'medium'	=>	'APP',
            		'log_user'  =>  $cus_id,
            		'log_type'  => $cus_type,
            		'log_intime' => time(),
            		'device_name'=>$deviceName,
            		'device_id'=>$deviceId
            	);
        	    $this->db->insert('exr_log',$data);*/
                return true;
            }else{
                return false;
            }    
        }
        else
        {
            return false;
        }
    }
    
    public function getCircle(){
	    $res = $this->Db_model->getAlldata('select * from state');
	    if($res){
	        return $res;
	    }else{
	        return false;
	    }
    }
    
    public function referrral_bonus_add($mob,$cus_ref_id){
        $mobile=$this->Encryption_model->encode($mob);
        $data = $this->Db_model->getAlldata("select * from customers where cus_mobile='$mobile'");
        $cus_id = $data['0']->cus_id;
        
        $transaction_date = date("Y-m-d h:s:i");
		$txn_crdt = '10';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
		$lastid = 0;
		
        if($data){
        $sql = "select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1";
		$r = $this->Db_model->getAlldata($sql);
		if($r)
		{
			$closing = $r['0']->txn_clbal;
			$total = $r['0']->txn_clbal + '10';
			$txn_dbdt = 0;
			$lasttxnrec = $this->Db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>'10','txn_dbdt'=>$txn_dbdt,'txn_clbal'=>$total,'txn_checktime'=>$cus_id.time(),'txn_type'=>'Referral Bonus','txn_time'=>$time,'txn_date'=>$date,'txn_ip'=>$ip,'txn_comment'=>'','transaction_id'=> '','transaction_ref'=> ''));
		    
		if($lasttxnrec){
		            
		        $sql = "select * from virtual_balance where 1 order by vb_id desc limit 1";
				$r = $this->Db_model->getAlldata($sql);
				if($r)
				{
					$total = $r['0']->vb_clbal - 10;
					$txn_dbdt = $txn_crdt;

					$data = array(
						'txn_id'	=>	'0',
						'vb_agentid'	=>	$cus_id,
						'vb_recrefid'	=>	0,
						'vb_opbal'	=>	$r['0']->vb_clbal,
						'vb_crdt'	=>	0,
						'vb_dbdt'	=>	$txn_dbdt,
						'vb_clbal'	=>	$total,
						'vb_checktime'	=>	time(),
						'vb_type'	=>	'by admin bonus',
						'vb_fromto'	=>	'',
						'vb_time'	=>	time(),
						'vb_date'	=>	date('Y-m-d h:i:s'),
						'vb_ip'	=>	'000',
						'vb_comment'	=>	'referral bonus',
						'transaction_type'	=>	"refferal bonus",
						'bank_name'	=>	'admin',
						'transaction_ref'	=>	"vb",
						'paid_due'	=>	'paid',
						'transaction_date'	=>	date('Y-m-d h:i:s'),
						'status'	=>	1							
					);
					$this->Db_model->insert_update('virtual_balance',$data);				
				}
		    
		        $dt= array(
                    'referral_status' => '1'
                );
		        $this->Db_model->insert_update('customers',$dt,array('cus_id'=>$cus_ref_id));
		      //  echo $this->db->last_query();
		        }
		    
		    }
        }
        
    }
    
    
    public function register_retailer($user_name,$user_mobile,$email,$aadhaar_number,$user_address,$distributor_number,$outlate_name){
        
        $checkuser = $this->Appapi_model->check_if_user_exists($user_mobile,$email);
            if($checkuser){
                return false;
            }else{
        
                $enc_mobile = $this->Encryption_model->encode($user_mobile);
                        
                if($distributor_number){
                    $dis_enc_mobile = $this->Encryption_model->encode($distributor_number);
                    $disDetails = $this->Db_model->getAlldata("SELECT * FROM customers WHERE cus_mobile='$dis_enc_mobile'");
                    if($disDetails){
                        $dis_cus_id = $disDetails[0]->cus_id;
                        if($disDetails[0]->scheme_id) $scheme_id = $disDetails[0]->scheme_id; else $scheme_id = '0';
                    }else{
                        $dis_cus_id = 0;
                        $scheme_id = 0;
                    }
                }else{
                    $dis_cus_id = 0;
                    $scheme_id = 0;
                }
                $user = $this->Db_model->getAlldata("SELECT * FROM user");
                
                if($scheme_id){
                     $retailer_scheme_id = $scheme_id;
                }else{
                    $retailer_scheme_id = $user['0']->commission_scheme_id;
                }
                //exit;
                $pin = rand ( 10000 , 99999 );
                $password = rand ( 10000 , 99999 );
                $enc_pin=$this->Encryption_model->encode($pin);
                $pass= $this->Encryption_model->encode($password);
                $data = array(
                    'cus_mobile' => $enc_mobile,
                    'cus_name' => $user_name,
                    'scheme_id'=>$retailer_scheme_id,
                    'cus_outlate'=>$outlate_name,
                    'cus_email' => $email,
                    'cus_pass' => $pass,
                    'cus_added_date' => date("Y-m-d H:i:s "),
                    'cus_reffer' => $dis_cus_id,
        			'cus_type' => 'retailer',
        			'cus_status' => '1',
        			'avability_status' => '0',
        			'cus_pin'=>$enc_pin,
        		);
        		//print_r($data);
         		$this->msg_model->registration(array('pass'=>$password,'mobile'=>$user_mobile,'pin'=>$pin));
        		return $this->db->insert('customers',$data); 
        }
    }
    
    public function getToken($amount){
        $orderId = 'easypayorder'.rand(1000000000,9999999999);
        $data_array = array('orderId'=>$orderId,'orderAmount'=>$amount,'orderCurrency'=>'INR');
        $postData = json_encode($data_array);
        $header = array(
                'Content-Type: application/json',            
                'x-client-id:2275406879b67f5634a7cffd07045722',       
                'x-client-secret:46681b352448e7901c459fd0351f550639672d6e'       
            );
        $url = 'https://api.cashfree.com/api/v2/cftoken/order';                       
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1); 
		curl_setopt($ch ,CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
        $content = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch); 
        if($curl_info['http_code'] == '200'){
            $pos = strpos($content,'{');
            $json_data = substr($content,$pos);
            $result = json_decode($json_data);
            //print_r($result);
            $status = $result->status;
            if($status == 'OK'){
                $token = $result->cftoken;
                $message = $result->message;
                return array('msg'=>$message,'foramount'=>$amount,'orderid'=>$orderId,'token'=>$token);
            }else{
                return FALSE;
            }
        }else{
            echo FALSE;
        }    
    }
    
    public function upigateway($amount,$product_name,$customer_name,$customer_email,$customer_mobile,$cus_id)
	{
	    
		$request ="";
		$rand = rand(1000000000,9999999999);
		$param['key'] = '92135a11-8fc6-44c7-a17e-d4984d15b610';
		$param['client_txn_id'] = "$rand";
		$param['amount'] = $amount;
		$param['p_info'] = $product_name;
		$param['customer_name'] = $customer_name;
		$param['customer_email'] = $customer_email;
		$param['customer_mobile'] = $customer_mobile;
		$param['redirect_url'] = "https://easypayall.in/Api_callback/upigateway_callback?trx_id=$rand&cus_id=$cus_id";
		$req = json_encode($param);

        $url = "https://merchant.upigateway.com/api/create_order";                           
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resultStr = curl_exec($ch);
        $data2=json_decode($resultStr, true);
        return $data2;
        
	}
	
	public function getQrCodeCashAtPos($mobile,$name,$cus_id){
        
        $qrString = "upi://pay?pa=enterpe@icici&tr=MNO$mobile&pn=$name&am=&cu=INR&mc=6010";
        $url = "userqrcodescashpos/";

        $create_file_name = $url.$mobile.'.png';
        if(file_exists($create_file_name)){
            return $create_file_name; 
        }else{
            //return false;
            $file_handle = fopen($create_file_name,"w");
            fclose($file_handle); 
            $size = '200';
            $logo = 'https://enterpe.in/assets/qrlogo.png';
            $QR = imagecreatefrompng('https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs='.$size.'&chl='.urlencode($qrString));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width/4;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            imagecopyresampled($QR, $logo, $QR_width/2.7, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            
            if($QR) {
                if($create_file_name) {
                    //file_put_contents($create_file_name, $img);
                    imagepng($QR, $create_file_name);
                    imagedestroy($QR); 
                } else {
                   
                }
            }
        }
        return $create_file_name; 
    }
    

    
    public function getDynamicQrCode($mobile,$name,$cus_id){
        
        $vpa_initial = 'A259'; //$this->generateRandomString(4);
	    $subscriberId_dynamicvpa = $vpa_initial.$mobile;
        $dynamicvpa = 'ENPE.'.$subscriberId_dynamicvpa.'@icici';
        $qrString = "upi://pay?pa=$dynamicvpa&tr=MQR$mobile&pn=$name&am=&cu=INR&mc=5411";
        $url = "userQrCodeDynamicVPA/";

        $create_file_name = $url.$mobile.'.png';
        if(file_exists($create_file_name)){
            return $create_file_name; 
        }else{
            //return false;
            $file_handle = fopen($create_file_name,"w");
            fclose($file_handle); 
            $size = '200';
            $logo = 'https://newmitra.in/assets/logo.jpg';
            $QR = imagecreatefrompng('https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs='.$size.'&chl='.urlencode($qrString));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width/4;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            imagecopyresampled($QR, $logo, $QR_width/2.7, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            if($QR){
                if($create_file_name){
                    $d = array('subscriberId_dynamicvpa'=>$subscriberId_dynamicvpa);
        		    $this->Db_model->insert_update('customers',$d,array('cus_id'=>$cus_id));
                    imagepng($QR, $create_file_name);
                    imagedestroy($QR); 
                } else {
                   
                }
            }
        }
        return $create_file_name; 
    }
    
    
    public function updatedisputestatus($txn){
        
        $data = array(
            'dispute_status' => 'disputed'
            );
        $this->db->where('recid',$txn);
        $this->db->update('exr_rechrgreqexr_rechrgreq_fch',$data);
        //echo $this->db->last_query();
        if($this->db->affected_rows()== '1'){
            return true;
        }else{
            return false;
        }
    }
    
    public function CheckPanApplication($cus_id)
    {
        $query = $this->Db_model->getAlldata("SELECT pan_application from customers where cus_id='$cus_id' ");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
    public function changePanApplicationStatus($cus_id,$txnId){
        $data = array('pan_application' => 'paid','pan_application_txn'=>$txnId);
        // print_r($data); exit;
        $insert = $this->Db_model->insert_update('customers',$data,array('cus_id'=>$cus_id));
        return true;
    }
    
     public function CheckRailwayApplication($cus_id)
    {
        $query = $this->Db_model->getAlldata("SELECT railway_application from customers where cus_id='$cus_id' ");
        if($query){
            return $query;
        }else{
            return false;
        }
    }
    
     public function changeRailwayApplicationStatus($cus_id,$txnId){
        $data = array('railway_application' => 'paid','railway_application_txn'=>$txnId);
        // print_r($data); exit;
        $insert = $this->Db_model->insert_update('customers',$data,array('cus_id'=>$cus_id));
        return true;
    }
    
    public function OfferAmount($cus_id,$offer_id,$amount){
        $data = $this->Db_model->get_trnx($cus_id);
        $txntype = 'Offer Purchase Succesful';
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
        $wallet_amt = $data['0']->txn_clbal;
        $total = $wallet_amt - $amount;
        $lasttxnrec= $this->Db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$wallet_amt,'txn_crdt'=>0,'txn_dbdt'=>$amount,'txn_clbal'=>$total,'txn_date'=>$date,'txn_checktime'=>$cus_id.time(),'txn_type'=>$txntype,'txn_time'=>$time,'txn_ip'=>$ip));
    	$data1 = array(
            "cus_id" => $cus_id,
            // "payment_type" => $payment_type,
            "amount" => $amount,
            "offer_id" => $offer_id,
            "payment_status" => 'ORDER PLACED',
            // "transaction_ref" => $transaction_ref,
            // "transaction_id" =>$transaction_id,
            // "purchase_status" => $purchase_status,
            // "quantity" => $quantity,
            // "delivery_address"=>$address,
            // 'delivery_pin'=>$pincode,
            // 'receiver_name'=>$receiver_name
            );
            
        $result = $this->Db_model->insert_update('puchaseOffer',$data1);
        $this->db->last_query();
        $purchase_id = $this->db->insert_id();
        
        if($purchase_id){
            return true;
        }else{
            return false;
        }			
        
    }
    
    public function changeApplicationStatus($cus_id,$app_type,$txnId,$name,$mobile,$email,$adhar,$pan,$amount){
        $data = array(
            'cus_id'=>$cus_id,
            'txnId'=>$txnId,
            'name'=>$name,
            'appname'=>$app_type,
            'mobile'=>$mobile,
            'email' => $email,
            'adhar_number' => $adhar,
            'pan_number' => $pan,
            'amount' => $amount,
            'status' => '1'
            );
        //print_r($data); exit;
        $insert = $this->Db_model->insert_update('application_service_purchase',$data);
        return true;
    }
    public function checkDeviceId($deviceId,$cus_id){
	    $data = $this->Db_model->getAlldata("select * from customers where cus_id = '$cus_id'  and deviceId = '$deviceId'");
	    if($data) return true; else return false;
	}
	public function checkIfValidUser($userMobile,$userPin,$userPassword){
	    
        $enc_mobile = $this->Encryption_model->encode($userMobile);
	    $this->db->select(array('cus_id','cus_name','cus_type','cus_mobile'));
        $this->db->from('customers');
        $this->db->where('cus_mobile', $enc_mobile);
        $this->db->where('cus_pin', $userPin);
        $this->db->where('cus_pass', $userPassword);
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
	}

}
?>