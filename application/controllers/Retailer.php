<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ini_set('display_errors', 0);
date_default_timezone_set('Asia/Kolkata');
class Retailer extends CI_Controller
{
	public function __construct()

	{

		parent::__construct();
        $this->load->model(array('db_model','encryption_model','email_model','rec_model','msg_model','Aeps_model'));
        
	}
// 	public function usertets()
// 	{
// 	    $otp = $this->session->userdata('otp');
//              echo $otp;exit;
// 	}
    
    public function index() 

	{
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    redirect('retailer/dashboard');
		}

	}
// 	public function tstpass()
// 	{
// 	    echo $this->encryption_model->decode('32b95faaa51ed494b7acb522c64cb629');echo"<br>";
// 	}
	
// 	public function login()
//     {
        
//         if(isset($_POST['login']))
//         {   
//             // echo $this->encryption_model->decode('d03935011520d3293e515d3728197d50');echo"<br>";
//             // echo $this->encryption_model->decode('9bc8a5f54378a66448e5f50c5967e762');exit;
//             $ip = $this->input->ip_address();
//             $mobile = trim($this->input->post('mobile'));
//   			$password = $this->input->post('password');
//   			$location = $this->input->post('location');
//             $this->db_model->insert_update('inputdata',array('function_name'=>'retailer login','inputs'=>$mobile.','.$password,'ip'=>$ip,'location'=>$location));
//             $mob = $this->encryption_model->encode($mobile);
//             $pass = $this->encryption_model->encode($password);
//             $r = $this->db_model->login('customers',array('cus_mobile'=>$mob,'cus_pass'=>$pass,'cus_type'=>'retailer'));
//             if($r)
//             {
//                 $cus_id=$r[0]->cus_id;
//                 $newdata1 = array(
//                     'isLoginRetailer'=>'TRUE',
//                     'cus_id'  => $r[0]->cus_id,
//                     'cus_name'  => $r[0]->cus_name,
//                     'cus_type'  => $r[0]->cus_type,
//                     'cus_email'  => $r[0]->cus_email,
//                     'cus_mobile' => $mobile,
//                     'ip_address' => $_SERVER['REMOTE_ADDR']
//                   );  
//                 $this->session->set_userdata($newdata1);
//                 $message='Login Details- '.$mobile.' and '.$password;
//                 $data=array('log_user'=>$cus_id,'log_type'=>'retailer','log_ip'=>$ip,'medium'=>'Web','message'=>$message);
//                 $this->db_model->insert_update('exr_log',$data);
//                 $updata=array('cus_status'=>'1');
//                 $this->db_model->insert_update('customers',$updata,$cus_id);
                
//                 $otp = rand(100000,999999); 
//                 $email = $r[0]->cus_email;
//         		$name = strtoupper($r[0]->cus_name);
//         		$msg= $name.", Your One Time OTP Is ".$otp;
//         		$sub="Login OTP From Recharge";
//         		$to=$email;
//         		$this->email_model->send_email($to,$msg,$sub);
//          	    $this->session->set_userdata('otp', $otp);
//                 redirect('retailer/otp');
//             }
//             else{
//                 $this->session->set_flashdata('msg', 'Invalid Login Details');
//                 redirect('users_login');
//             }
//         }
//         else{
//             $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
//             $this->load->view('retailer/index',$data);
//         }
        

// 	}
	public function otp()
    {
        if(isset($_POST['checkotp']))
        {
             $enteredotp = $this->input->post('otp');
            $otp = $this->session->userdata('otp');
            // echo $otp;exit;
            if($enteredotp == $otp){
                $cus_id = $this->session->userdata('cus_id');
                $updata=array('web_login_status'=>'loggedin');
                $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));
                $this->session->set_userdata('isLoginRetailer', TRUE);
                redirect('retailer/dashboard');
            }
            else{
                $this->session->set_flashdata('error', 'Entered OTP is Invalid');
                $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
                $this->load->view('retailer/otp',$data);
            }
        }
        else{
            $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
            $this->load->view('retailer/otp',$data);
        }
    }
    
    public function resendotp(){
        
        
        $otp=rand(100000,999999); 
        $this->session->set_userdata('otp', $otp);
        
	    $mobile = $this->session->userdata('cus_mobile');
	    $email = $this->session->userdata('cus_email');
	    $name = strtoupper($this->session->userdata('cus_name'));
	
	    $msg= $name.", Your One Time OTP Is ".$otp;
	    $sub="Login OTP From Recharge";
	    $to=$email;
	    
	    $r=$this->email_model->send_email($to,$msg,$sub);
	    $this->msg_model->sendsms($mobile,$msg);
		
		redirect('retailer/otp');
        
    }
	
	public function dashboard()

	{
	    
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
    	    $data['data'] = [
    	   	    'success' => $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' and apiclid='".$this->session->userdata('cus_id')."' and DATE(reqdate)='".date('Y-m-d')."'"),
    	   	    'failed' => $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' and apiclid='".$this->session->userdata('cus_id')."' and DATE(reqdate)='".date('Y-m-d')."'"),
    	   	    'todayearning' => $this->db_model->getAlldata('select sum(exr.txn_crdt) as amt from exr_trnx as exr join exr_rechrgreqexr_rechrgreq_fch as err on exr.txn_recrefid = err.recid where exr.txn_type IN ("Retailer Commission","discount") and err.status="SUCCESS" and DATE(exr.txn_date)="'.date('Y-m-d').'" and exr.txn_agentid="'.$this->session->userdata('cus_id').'" group by err.recid'),
    	   	    'balance' => $this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('cus_id')."' ORDER BY txn_id desc limit 1"),
    	   	    'mobile' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('mobile','postpaid') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'datacard' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('datacard') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'dth' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('dth') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'landline' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('landline') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'electricity' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('ELECTRICITY') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'insurance' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('INSURANCE') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'gas' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('GAS') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'pan' => $this->db_model->getAlldata("select sum(exr.qty) as amt from coupon_buy as exr where exr.status='SUCCESS' and exr.pan_cus_id='".$this->session->userdata('cus_id')."'"),
    	   	    'dmt' => $this->db_model->getAlldata("select sum(exr.amount) as amt from dmt_trnx as exr where exr.status='SUCCESS' and DATE(exr.dmt_trnx_date)='".date('Y-m-d')."' and exr.cus_id='".$this->session->userdata('cus_id')."'"),
    	   	    'fastag' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('fastag') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('cus_id')."'"),
    	   	    'aeps' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Cash%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('cus_id')."'"),
    	   	    'microatm' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Micro Atm%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('cus_id')."'"),
    	   	    'adhar' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Aadhar Pay%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('cus_id')."'"),
    	   	   ];
            $this->load->view('retailer/dashboard',$data);
		}    

	}
	
	public function profile()

	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    if(isset($_POST['update_profile']))
		    {
		        if(!empty($_FILES['image']['name'])){
                    $config['upload_path'] = 'assets/img/faces/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['file_name'] = $_FILES['image']['name'];
                    
                    //Load upload library and initialize configuration
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config);
                    
                    if($this->upload->do_upload('image')){
                        $uploadData = $this->upload->data();
                        $picture = $uploadData['file_name'];
                        $image = "assets/img/faces/$picture";
                        $record=array(
    				            'cus_outlate'=>$_POST['cus_outlate'],
    				            'cus_name'=>$_POST['cus_name'],
    				            'cus_panno'=>$_POST['cus_panno'],
    				            'cus_mobile'=>$this->encryption_model->encode($_POST['cus_mobile']),
    				            'cus_address'=>$_POST['cus_address'],
    				            'cus_shop_address'=>$_POST['cus_shop_address'],
    				            'cus_state'=>$_POST['state'],
    				            'cus_city'=>$_POST['city'],
    				            'cus_email'=>$_POST['cus_email'],
    				            'cus_pincode'=>$_POST['cus_pincode'],
    				            'profile_img'=>$image
    				        );
                    }else{
                        $record=array(
    				            'cus_outlate'=>$_POST['cus_outlate'],
    				            'cus_name'=>$_POST['cus_name'],
    				            'cus_panno'=>$_POST['cus_panno'],
    				            'cus_mobile'=>$this->encryption_model->encode($_POST['cus_mobile']),
    				            'cus_address'=>$_POST['cus_address'],
    				            'cus_shop_address'=>$_POST['cus_shop_address'],
    				            'cus_state'=>$_POST['state'],
    				            'cus_city'=>$_POST['city'],
    				            'cus_email'=>$_POST['cus_email'],
    				            'cus_pincode'=>$_POST['cus_pincode']
    				        );
                    }
				}
		        else{
    				    $record=array(
    				            'cus_outlate'=>$_POST['cus_outlate'],
    				            'cus_name'=>$_POST['cus_name'],
    				            'cus_panno'=>$_POST['cus_panno'],
    				            'cus_mobile'=>$this->encryption_model->encode($_POST['cus_mobile']),
    				            'cus_address'=>$_POST['cus_address'],
    				            'cus_shop_address'=>$_POST['cus_shop_address'],
    				            'cus_state'=>$_POST['state'],
    				            'cus_city'=>$_POST['city'],
    				            'cus_email'=>$_POST['cus_email'],
    				            'cus_pincode'=>$_POST['cus_pincode']
    				        );
    				}
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    			$this->session->set_flashdata('message','Profile Updated Successfully...!!!');
		    }
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
		    $data['referal']=$this->db_model->getwhere('customers',array('cus_id'=>$data['profile'][0]->cus_reffer));
		    $data['state']=$this->db_model->getwhere('state');
		    $data['city']=$this->db_model->getwhere('city',array('state_id'=>$data['profile'][0]->cus_state));
            $this->load->view('retailer/profile',$data);
		}
	}
	
	function check_password() 
	{
	    
	    $old_password =$this->input->post('old_password');
	    $data=$this->db_model->getwhere('customers',array('cus_pass'=>$this->encryption_model->encode($old_password),'cus_id'=>$this->session->userdata('cus_id')));
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
	    $data=$this->db_model->getwhere('customers',array('cus_pin'=>$this->encryption_model->encode($old_pin),'cus_id'=>$this->session->userdata('cus_id')));
	    if($data){
	        echo TRUE;
	    }
        else{
            echo FALSE;
        }
        
    }
	public function password_and_pin()

	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}
		else{
		    if(isset($_POST['update_password']))
		    {
		                $record=array(
    				            'cus_pass'=>$this->encryption_model->encode($_POST['new_password'])
    				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    			$this->session->set_flashdata('message','Password Updated Successfully...!!!');
		    }
		    
		    if(isset($_POST['update_pin']))
		    {
		                $record=array(
    				            'cus_pin'=>$this->encryption_model->encode($_POST['new_pin'])
    				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    			$this->session->set_flashdata('message','Pin Updated Successfully...!!!');
		    }
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
		    $this->load->view('retailer/password_and_pin');
		}
	}
	
	public function mobile_recharge()

	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/mobile_recharge',$data);
		}
	}
	
	public function rofferStateWise()
	{
	    $operator = $_POST['operator'];
	    if($operator == 'BSNL' || $operator =='BSNL STV')
	    $operator = 'Bsnl';
	   elseif($operator == 'VI')
	    $operator = 'Idea';
	    
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['operator'] = $operator;
		$param['cricle'] = $_POST['circle'];
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
        print_r($content);
	}
	
	public function roffer()
    {
        $mobile = $this->input->post('prepaid_mobile');
        $opcodeid = $this->input->post('opcodeid');
        
        /*$mobile='8459974552';
        $opcodeid='141';*/
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$opcodeid));
        $opcodenew = $oper[0]->opcodenew;
        $operator = $oper[0]->operatorname;
        $request ="";
        $table = "";
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
        // print_r($data2);exit;
        foreach($data2 as $d=>$key){if(is_array($key)){$i=1;foreach($key as $r){ 
            $table.="<tr><td><input name='myRadio' onClick='MyAlert(this.value)' value=$r->rs type='radio'></td>
                <td>$r->rs</td>
                <td>$r->desc</td>               
              </tr>";
        }}}
        $out='<div class="modal fade" id="modaldemo3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6><img style="width: 45px;height: 40px;border-radius: 50%;" src="'.base_url().'assets/'.$oper[0]->operator_image.'" alt="'.$oper[0]->operatorname.'">&nbsp;&nbsp;<b>'.$oper[0]->operatorname.'</b> </h6>
						<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
					    <div class="table-responsive"><div class="boxscroll">
				                            <table class="table table-hover table-hover-border">
				                                <thead>
				                                <tr>
				                                    <th>Select</th>
				                                    <th>Amount</th>
				                                    <th>Description</th>
				                                </tr>
				                                </thead>
				                                <tbody>
				                                 '.$table.'
				                                 </tbody>
				                            </table>
				                            </div>
				                        </div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-success" data-dismiss="modal" type="button">Done</button>
					</div>
				</div>
			</div>
            </div>';
         echo $out;
        
    }
    
    public function dthInfo()
    {
        $mobile = $this->input->post('prepaid_mobile');
        $opcodeid = $this->input->post('opcodeid');
        
        /*$mobile='10530737450';
        $opcodeid='15';*/
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$opcodeid));
        $opcodenew = $oper[0]->opcodenew;
        $operator = $oper[0]->operatorname;
        $request ="";
        $table = "";
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
        $records = $data2->records;
        print_r($records);
        
        $customerName = $records[0]->customerName;
        $status = strtoupper($records[0]->status);
        $Balance = $records[0]->Balance;
        $MonthlyRecharge = $records[0]->MonthlyRecharge;
        $NextRechargeDate = $records[0]->NextRechargeDate;
        $planname = $records[0]->planname;
        
        $table.="
        <tr><td style='width:250px'><b>Customer Name</b></td><td>$customerName</td></tr>
        <tr><td style='width:250px'><b>Status</b></td><td class='text-info'><b>$status</b></td></tr>
        <tr><td style='width:250px'><b>Balance</b></td><td class='text-danger'><b>₹ $Balance</b></td></tr>
        <tr><td style='width:250px'><b>Monthly Recharge</b></td><td>$MonthlyRecharge</td></tr>
        <tr><td style='width:250px'><b>Plan</b></td><td>$planname</td></tr>
        <tr><td style='width:250px'><b>Next Recharge Date</b></td><td>$NextRechargeDate</td></tr>
        ";
        $out='<div class="modal fade" id="modaldemo3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6><img style="width: 45px;height: 40px;border-radius: 50%;" src="'.base_url().'assets/'.$oper[0]->operator_image.'" alt="'.$oper[0]->operatorname.'">&nbsp;&nbsp;<b>'.$oper[0]->operatorname.'</b> </h6>
						<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
					    <div class="table-responsive"><div class="boxscroll">
				                            <table class="table table-hover table-hover-border">
				                                <tbody>
				                                 '.$table.'
				                                 </tbody>
				                            </table>
				                            </div>
				                        </div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-success" data-dismiss="modal" type="button">Done</button>
					</div>
				</div>
			</div>
            </div>';
         echo $out;
        
    }
    
    public function recharge_ot()
	{
		$rtype = $this->input->post('type');
		$oper = $this->input->post('operator');
	    $amt = $this->input->post('amount');
		$redirect = $this->input->post('redirect');
		$optional1 = $this->input->post('optional1');
		$optional2 = $this->input->post('optional2');
		$mob = $this->input->post('c_number');
		switch ($rtype) {
			case 'prepaid':
				$this->form_validation->set_rules('number','Mobile','required|regex_match[/^[0-9]{10}$/]');
				break;
			case 'postpaid':
				$this->form_validation->set_rules('number','Mobile','required|regex_match[/^[0-9]{10}$/]');
				break;
			case 'datacard':
				$this->form_validation->set_rules('number','Mobile','required|regex_match[/^[0-9]{10}$/]');
				break;
			case 'dth':
				$this->form_validation->set_rules('number','Mobile','required|min_length[5]|max_length[20]');
				break;
			case 'landline':
				$this->form_validation->set_rules('number','Mobile','required|min_length[5]|max_length[20]');
				break;
			case 'electricity':
				$this->form_validation->set_rules('number','Mobile','required|min_length[5]|max_length[20]');
				break;
			case 'insurance':
				$this->form_validation->set_rules('number','Mobile','required|min_length[5]|max_length[20]');
				break;
			case 'gas':
				$this->form_validation->set_rules('number','Mobile','required|min_length[5]|max_length[20]');
				break;			
			default:
				$this->form_validation->set_rules('number','Mobile','required|regex_match[/^[0-9]{10}$/]');
				break;
		}
		
// 		$this->form_validation->set_rules('operator','Mobile','required');
// 		$this->form_validation->set_rules('amount','Amount','required');
// 		$this->form_validation->set_rules('type','type','required');
		if(!empty($rtype) || !empty($oper) || !empty($amt))
		{
		    $ip = $this->input->ip_address();
			$cus_id = $this->session->userdata('cus_id');
			$cus_type = $this->session->userdata('cus_type');
			$qr_opcode =$this->input->post('operator');
		    if (preg_match('/^[0-9]+$/', $amt))
			{
			    $cus_data = $this->db_model->getAlldata("select * from customers where cus_id = '$cus_id' ");
			    $avability_status = $cus_data['0']->avability_status;
                $cus_type = $cus_data[0]->cus_type;
                if($cus_type == 'retailer')
                {
                    if($avability_status == '0'){
                        
                        $admin = $this->db_model->getAlldata("select recharge_limit from user where user_id = '6' ");
                        $recharge_limit = $admin['0']->recharge_limit;
                        if($amt <= $recharge_limit)
                        {
                            $billing = '1';$process = '';$medium = 'web';
        
			                $complete = $this->rec_model->recharge_ot(array('cus_id'=>$cus_id,'amount'=>$this->input->post('amount'),'number'=>$this->input->post('number'), 'billing' => $billing, 'process' => $process,'operator'=>$qr_opcode,'mode'=>$medium,'type'=>$cus_type,'optional1'=>$optional1,'optional2'=>$optional2,$mob=>$mob));
			                $msg = $complete['mess'];
				
			                $this->session->set_flashdata('message',$msg);
			                redirect("retailer/$redirect");	
                        }else{
                            $msg = "Recharge amount should be less than $recharge_limit";
        			        $this->session->set_flashdata('message',$msg);
        			        redirect("retailer/$redirect");
                        }
                        
                    }else{
                        $msg = "User is Inactive.";
        			    $this->session->set_flashdata('message',$msg);
        			    redirect("retailer/$redirect");
                    }
                }
                else{
			    $msg = "Recharges allowed for Retailer Members Only";
    			$this->session->set_flashdata('message',$msg);
    			redirect("retailer/$redirect");
			    }
                    
			}else{
			    $msg = "Please Enter Valid Amount.";
    			$this->session->set_flashdata('message',$msg);
    			redirect("retailer/$redirect");
			}
		}	
		else
		{
			$msg = "Please fill the mandatory filled.";
			$this->session->set_flashdata('message',$msg);
			redirect("retailer/$redirect");
		}
	}
	
	public function data_card()

	{
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/data_card',$data);
		}
    }
	
	public function dth()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/dth',$data);
		}
    }
	
	public function landline()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/landline',$data);
		}
    }
	
	public function electricity()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/electricity',$data);
		}
    }
	
	public function insurance()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/insurance',$data);
            
		}
    }
	
	public function gas()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/gas',$data);
		}
    }
    
    public function gas_cylinder()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('retailer/gas_cylinder',$data);
		}
    }
	
	public function fastag()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
		    $this->load->view('retailer/fastag',$data);
		}
    }
	
	public function aeps()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    
		    $status = $this->service('AEPS');
            if($status == 'active'){
            
    		    $data['aeps_state'] = $this->db_model->getwhere('aeps_state');
    		    $this->load->view('retailer/aeps',$data);
            }else{
            
                $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            } 
		}
    }
    
    // public function service_test(){
    //     $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
    //     $this->load->view('retailer/service',$msg);
    // }
    
    public function aeps_transaction()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $status = $this->service('AEPS');
            if($status == 'active'){
            
    		    $data['aeps_bank'] = $this->db_model->getwhere('aeps_bank');
    		    $this->load->view('retailer/aeps_transaction',$data);
            }else{
            
                $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }   
		}
    }
	
	public function insuranceapi()
    {
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $status = $this->service('INSURANCES');
            if($status == 'active'){
		        $this->load->view('retailer/micro_atm');
            }else{
                $msg['info'] = "INSURANCES SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }   
		}

        
    }
    public function payout()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $cus_id=$this->session->userdata('cus_id');
		    $data['bankDetails'] = $this->Db_model->getAlldata("SELECT * FROM customers WHERE cus_id='$cus_id'");
            $this->load->view('retailer/payout',$data);
		}
    }
    
    public function getPayoutCharge(){
        $amount = $_POST['amount'];
        $cus_id=$this->session->userdata('cus_id');
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
                            
                                echo $commission_amount;
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function submit_payout(){
        $msg="8630450571-89938-143-92.204.134.236";
        
        $cus_id=$this->session->userdata('cus_id');
	    $bank_name = $_POST['bank_name'];
	    $account_number = $_POST['account_number'];
	    $ifsc_code = $_POST['ifsc_code'];
	    $account_holder_name = $_POST['account_holder_name'];
	    $amount = $_POST['amount'];
	    $charge = "1";
	    $moveto = $_POST['sel'];
	    
	    $this->form_validation->set_rules('bank_name','Bank name','required');
	    $this->form_validation->set_rules('account_number','Account number','required');
	    $this->form_validation->set_rules('ifsc_code','Ifsc code','required');
	    $this->form_validation->set_rules('account_holder_name','Account holder name','required');
	    $this->form_validation->set_rules('amount','Amount','required');
	   // $this->form_validation->set_rules('charge','Charge','required');
	    
	    if($this->form_validation->run()){
	        $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
	        
	        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
	        
	        if($cus_aeps_bal >= ($amount+$charge)){
	            
	            if($moveto == 'wallet'){
	                
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
        						'txn_date'	=>	date('Y-m-d H:i:s'),
        						'txn_checktime'	=>	$cus_id.time(),
        						'txn_ip'	=>	$ip,
        						'txn_comment'	=>	$txntype
        					);
        					$res = $this ->db_model->insert('exr_trnx',$insert_wallet);
        					if($res){
        					    
                                $this->session->set_flashdata('success','Move to  wallet Successfully');
                                redirect('Retailer/view_all_redeem_request');
        					}else{
        					    
                                $this->session->set_flashdata('error','Failed to Submit  Request');
                                redirect('Retailer/payout');
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
        						'txn_date'	=>	date('Y-m-d H:i:s'),
        						'txn_checktime'	=>	$cus_id.time(),
        						'txn_ip'	=>	$ip,
        						'txn_comment'	=>	$txntype
        					);
        					$res = $this ->db_model->insert('exr_trnx',$insert_wallet);
        					if($res){
        					    
                                $this->session->set_flashdata('success','Move to  wallet Successfully');
                                redirect('Retailer/view_all_redeem_request');
        					}else{
        					    
                                $this->session->set_flashdata('error','Failed to Submit  Request');
                                redirect('Retailer/payout');
        					}
    	                }	
	                }
	                
	            }else{
	            
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
                        
                        $txntype = 'Redeem';
                        $total = $cus_aeps_bal - $amount;
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>'0','aeps_txn_dbdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                        
                        $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
    	                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                        
                		$txn_dbdt = $charge;
                		$txntype = 'Redeem Charge';
                		$ip = $_SERVER['REMOTE_ADDR'];
                		$date = DATE('Y-m-d H:i:s');
                		$total = $cus_aeps_bal - $txn_dbdt;
                		
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>'0','aeps_txn_dbdt'=>$txn_dbdt,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                		
                // 		$CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/submit_payout";
                //         $param['msg'] = $msg;
                //         $param['bank_name'] = $bank_name;
                //         $param['account_number'] = $account_number;
                //         $param['ifsc_code'] = $ifsc_code;
                //         $param['account_holder_name'] = $account_holder_name;
                //         $param['amount'] = $amount;
                //         $param['charge'] = $charge;
                //         $param['moveto'] = $moveto;
                        
                        
                // 		  foreach($param as $key=>$val) 
                // 		{ 
                // 			$request.= $key."=".urlencode($val); 
                // 			$request.= "&"; 
                // 		}
                		  
                // 		  $header = [         
                //                 'Content-Type:application/x-www-form-urlencoded',             
                //                 'x-api-key:dIUcY3YIHGKjzmOFlN79PFLzNUFUfiBA',                 
                //                 'Authorization:Basic YWVwczpBRVBTU2F0bWF0',            
                //             ];
                        
                		  
                // 		  $curl = curl_init();
                //         curl_setopt_array($curl, array(
                //                 CURLOPT_URL => $CURLOPT_URL,
                //                 CURLOPT_RETURNTRANSFER => true,
                //                 CURLOPT_SSL_VERIFYPEER => true, 
                //                 CURLOPT_SSL_VERIFYHOST => 2,
                //                 CURLOPT_TIMEOUT => 30,
                //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                 CURLOPT_CUSTOMREQUEST => "POST",
                //                 CURLOPT_POSTFIELDS => $request,
                //                 CURLOPT_HTTPHEADER => $header
                //             ));
                            
                //         $res = curl_exec($curl);
                //         $response = json_decode($res,true);
                //         $err = curl_error($curl);
                //         $info = curl_getinfo($curl);
                //         curl_close($curl);
                		
                        $message = "Request Submit succefully";
                        $this->session->set_flashdata('success',$message);
                        redirect('Retailer/view_all_redeem_request');
                    }else{
                        $this->session->set_flashdata('error','Failed to Submit Redeem Request');
                        redirect('Retailer/payout');
                    }
                }    
                        
	        }else{
	            $this->session->set_flashdata('error','Insufficient Balance');
                redirect('Retailer/payout');
            }
	    }else{
            $this->session->set_flashdata('error','Please Fill All Data In Requested Format ');
            redirect('Retailer/payout');
        }
        
    }
    
    public function view_all_redeem_request(){
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $cus_id = $this->session->userdata('cus_id');
		    $data['requests'] = $this->db_model->getAlldata("SELECT * FROM payout_request WHERE cus_id='$cus_id' ORDER BY pay_req_id DESC");
		    $this->load->view('retailer/view-all-payout-request',$data);
		}
    }
    
    public function view_all_redeem_wallet_report(){
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $cus_id = $this->session->userdata('cus_id');
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join customers as c on e.txn_agentid = c.cus_id WHERE txn_type LIKE '%PAYOUT%' and DATE(e.txn_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.txn_date) <= '".date('Y-m-d').' 00:00:00'."' and txn_agentid = '".$cus_id."' order by e.txn_id DESC");
		    $this->load->view('retailer/view_all_redeem_wallet_report',$data);
		}
    }
    
    public function search_redeem_wallet_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $cus_id = $this->session->userdata('cus_id');
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    
		    $sql = "SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where txn_type LIKE '%PAYOUT%' and " ;
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
    		
		    $sql .= " e.txn_id != '' and e.txn_agentid = '".$cus_id."' ORDER BY e.txn_id DESC";
		    $data['ledger'] = $this->db_model->getAlldata($sql);
		    $this->load->view('retailer/view_all_redeem_wallet_report',$data);
		}
	}
    
	
	public function pan_card()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
            
        }else{
            
            $status = $this->service('PANCARD');
            if($status == 'active'){
                
        	    $data['coupon'] = $this->db_model->getwhere('coupon_price');
        	    $data['couponhistory'] = $this->db_model->getwhere('coupon_buy',array('pan_cus_id'=>$this->session->userdata('cus_id')));
        	    $data['cusdata'] = $this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
        	    $data['aeps_state'] = $this->db_model->getwhere('aeps_state');
        	    $this->load->view('retailer/pan_card',$data);
            
        	}else{
        	    
                $msg['info'] = "PAN CARD SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
        	}
        }    	
    }
	
	
	public function fund_request()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $cus_id = $this->session->userdata('cus_id');
		    $data['cusbank'] = $this->db_model->getwhere('bank_details',array('account_status'=>'1'));
		    $this->load->view('retailer/fund_request',$data);
		}
    }
	
	public function send_fund_request()
	{
	    
		    $cus_id = $this->session->userdata('cus_id');
		    $cusdt = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
		    $payto = $cusdt[0]->cus_reffer;
		    if($this->input->post('request') == 'distributor')
			    $cusdt1 = $this->db_model->getwhere('customers',array('cus_id'=>$payto));
			else
			    $cusdt1 = $this->db_model->getwhere('customers',array('cus_id'=>'5505'));
			    
			$data = array(
				'cus_id'    =>	$cus_id,
				'pay_amount'=>	$this->input->post('amount'),
				'request_from'=>$this->input->post('request'),
				'pay_mode'	=>	$this->input->post('method'),
				'pay_to'	=>	$payto,
				'pay_bank'	=>	$this->input->post('bank'),
				'ref_no'	=>	$this->input->post('ref'),
				'remarks'	=>	$this->input->post('remark'),
				'req_date'	=>	date('Y-m-d H:i:s')
			);
			
			$this->msg_model->approve_request1(array('data'=>$cusdt1[0],'message'=>"Dear Member, You have received an fund request of Rs.".$this->input->post('amount')." from ".$cusdt[0]->cus_name));
			$this->db_model->insert_update('fund_request',$data);
			$this->session->set_flashdata('message','Fund request submitted successfully');
			redirect('retailer/fund_request');
		
	}
	
	public function fund_transfer()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('retailer/fund_transfer');
		}
    }
	
	public function recharge_report()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    if(isset($_POST['search_recharge_report']))
            {
                $operator_type = $_POST['operator_type'];
        	    $operator = $_POST['operator'];
        	    $mobile_number = $_POST['mobile_number'];
        	    $rec_id = $_POST['rec_id'];
        	    $status = $_POST['status'];
        	    $frm = $_POST['from_dt'];
    	        $to = $_POST['to_dt'];
    	        
        	    $from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : '');
    		    $to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
	    
        	    $sql = "SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where apiclid='".$this->session->userdata('cus_id')."' and ";
        		
        		if($operator_type){ $sql .= "o.opsertype ='".$operator_type."' and ";}		
        		if($operator){$sql .= "e.operator ='".$operator."' and ";}
                if($mobile_number){$sql .= "e.mobileno ='".$mobile_number."' and ";}
                if($rec_id){$sql .= "e.recid ='".$rec_id."' and ";}
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
        		if($date !=''){$sql .="DATE(e.reqdate) ='".$date."' and ";}
                if($status){$sql .= "e.status ='".$status."' and ";}
                $sql .= " e.recid != '' ORDER BY e.recid DESC";
                
                $data['recharge']=$this->db_model->getAlldata("$sql");
            }
            else
            {
		        $data['recharge']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where apiclid='".$this->session->userdata('cus_id')."' ORDER BY e.recid DESC");
            }
            
            $data['operator'] = $this->db_model->getwhere('operator');
		    $this->load->view('retailer/recharge_report',$data);
		}
    }
	
	public function search_fund_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
		    $sql = "select * from exr_trnx as et join virtual_balance as vb on et.txn_id = vb.txn_id where et.txn_agentid='".$this->session->userdata('cus_id')."' and et.txn_type='Direct Credit'   and " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(et.txn_date) >= '".$from.' 00:00:00'."' and DATE(et.txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(et.txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(et.txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " et.txn_id != '' Group by et.txn_id order by et.txn_id desc";
		    $data['fund'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('retailer/fund_report',$data);
		}
	}
	
	public function fund_report()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['fund']=$this->db_model->getAlldata("select * from exr_trnx as et join virtual_balance as vb on et.txn_id = vb.txn_id where et.txn_agentid='".$this->session->userdata('cus_id')."' and et.txn_type='Direct Credit' Group by et.txn_id order by et.txn_id desc");
		    $this->load->view('retailer/fund_report',$data);
		}
    }
	
	public function ledger_report()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    if(isset($_POST['search_transaction_report']))
            {
        	    $rec_id = $_POST['rec_id'];
        	    $frm = $_POST['from_dt'];
    	        $to = $_POST['to_dt'];
    	        
        	    $from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : '');
    		    $to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		    
        	    $sql = "SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_agentid='".$this->session->userdata('cus_id')."' and ";
        		
        		if($rec_id){
        		    $sql .= "e.txn_recrefid ='".$rec_id."' and ";
        		    
        		}
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
                $sql .= " e.txn_id !='' ORDER BY e.txn_id DESC";
                
                $data['fund']=$this->db_model->getAlldata("$sql");
            }
            else
            {
		        $data['fund']=$this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_agentid='".$this->session->userdata('cus_id')."'");
		    }
		     $this->load->view('retailer/ledger_report',$data);
		}
    }
	
	public function commission_report()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    if(isset($_POST['search_commission_report']))
            {
                $operator_type = $_POST['operator_type'];
        	    $operator = $_POST['operator'];
        	    $frm = $_POST['from_dt'];
    	        $to = $_POST['to_dt'];
    	        
        	    $from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : '');
    		    $to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		    
	    
        	    $sql = "SELECT sum(exr.amount) as amount, sum(exr.retailer) as com, op.operatorname FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('cus_id')."' and ";
        		
        		if($operator_type){ $sql .= "op.opsertype ='".$operator_type."' and ";}		
        		if($operator){$sql .= "exr.operator ='".$operator."' and ";}
                if($from !='' && $to !='')
        		{
        			$sql .="DATE(exr.reqdate) >= '".$from.' 00:00:00'."' and DATE(exr.reqdate) <= '".$to.' 00:00:00'."' and ";
        		}else{
        		    
        		    if($from !=''){
        		        $sql .=" DATE(exr.reqdate) = '".$from.' 00:00:00'."'  and " ;
        		    }else if($to !=''){
        		        $sql .=" DATE(exr.reqdate) = '".$to.' 00:00:00'."' and ";
        		    }
        		}
        // 		if($date !=''){$sql .="DATE(exr.reqdate) ='".$date."' and ";}
                $sql .= " exr.recid != '' ORDER BY exr.recid DESC";
                
                $data['fund']=$this->db_model->getAlldata("$sql");
            }
            else
            {
		        $data['fund']=$this->db_model->getAlldata("SELECT sum(exr.amount) as amount, sum(exr.retailer) as com, op.operatorname FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('cus_id')."'");
		    }
		     $data['operator'] = $this->db_model->getwhere('operator');
		     $this->load->view('retailer/commission_report',$data);
		}
    }
	
	public function find_report()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    if(isset($_POST['search_find_report']))
            {
                $no = $_POST['mobile'];
	            $data['fund']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('cus_id')."' and exr.mobileno='".$no."' order by exr.recid DESC ");
        		
            }
            else
            {
		        $data['fund']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('cus_id')."' order by exr.recid DESC ");
		    }
		    
		    $this->load->view('retailer/find_report',$data);
		}
    }
	
	 public function getRechargePackage(){
        
        $cus_id = $this->session->userdata('cus_id');
        $type = $this->input->post('type');
        $res = $this->db_model->getAlldata("select * from customers where cus_id = '".$cus_id."' ");
        $pack_id = $res['0']->scheme_id;
        if($pack_id){
            
            if($type == 'recharge'){
                $i=0;
                $r = $this->db_model->getAlldata("select * from commission_scheme_recharge where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>OPERATOR NAME</th><th>COMMISSION</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  		$tab.='<td>'.$v->slab.'</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>OPERATOR NAME</th><th>COMMISSION</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
        		
            }else if($type == 'aeps'){
                $i=0;
                $r = $this->db_model->getAlldata("select * from commission_scheme_aeps as cs join aeps_commission_slab ac on cs.slab_opcode = ac.aeps_comm_id where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>AMOUNT RANGE</th><th>COMMISSION</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  	    $tab.='<td>'.$v->slab.'('.$v->amount_min_range.'-'.$v->amount_max_range.')</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>AMOUNT RANGE</th><th>COMMISSION</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
                
            }else if($type == 'dmt'){
                
                $i=0;
                $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_dmt` where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>AMOUNT RANGE</th><th>CHARGE</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  	    $tab.='<td>'.$v->slab.'</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>AMOUNT RANGE</th><th>COMMISSION</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
                
            }else if($type == 'adharpay'){
                
                $i=0;
                $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_adharpay` where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>CHARGE</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>COMMISSION</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
        		
            }else if($type == 'microatm'){
                
                $i=0;
                $r = $this->db_model->getAlldata("SELECT * FROM commission_scheme_microatm as cs join micro_atm_commission_slab as ma on ma.atm_comm_id = cs.slab_opcode where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>AMOUNT RANGE</th><th>COMMISSION</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  	    $tab.='<td>'.$v->slab.'('.$v->amount_min_range.'-'.$v->amount_max_range.')</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>AMOUNT RANGE</th><th>COMMISSION</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
        		
            }else if($type == 'pancard'){
                
                $i=0;
                $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_pancard` where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>COMMISSION</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>COMMISSION</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
        		
            }else if($type == 'payout'){
                
                $i=0;
                $r = $this->db_model->getAlldata("SELECT * FROM `commission_scheme_payout` as cs  join payout_commission_slab as pc on pc.payout_charge_id = cs.slab_opcode where scheme_id = '".$pack_id."' ");
                $tab ='';
                $tab.='<table id="next" class="table table-striped table-bordered" style="width:100%">';
                $tab.='<thead><tr><th>#ID</th><th>AMOUNT RANGE</th><th>CHARGE</th><th>TYPE</th></tr></thead><tbody>';
                       
        	  	foreach($r as $v)
        	  	{
        	  		$tab.='<tr>';
        	  		$tab.='<td>'.++$i.'</td>';
        	  	    $tab.='<td>'.$v->slab.'('.$v->amount_min_range.'-'.$v->amount_max_range.')</td>';
        	  	    $tab.='<td>'.$v->retailer_comm.'</td>';
        	  	    $tab.='<td>'.$v->type.'</td>';
        	  		$tab.='</tr>';
        	  		
        		}
        		$tab.='</tbody><tfoot><tr><th>#ID</th><th>AMOUNT RANGE</th><th>CHARGE</th><th>TYPE</th></tr></tfoot></table>';
        		echo $tab;
            }
            
        }else{
            echo "NF";
        }	
    }
	public function package_report()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		     $r = $this->db_model->getAlldata("select * from customers where cus_id='".$this->session->userdata('cus_id')."'");
		     $data['fund']=$this->db_model->getAlldata("SELECT op.operatorname,op.opsertype,expc.packcomm_id,expc.packagecom_tem,expc.packagecom_amttype,expc.packcomm_comm,expc.packagecom_type FROM `exr_packagecomm` as expc join `exr_package` as exp on expc.packcomm_name = exp.package_id join operator as op on expc.packcomm_opcode = op.opcodenew where exp.package_membertype='".$this->session->userdata('cus_type')."' and exp.package_id='".$r[0]->package_id."' order by op.opsertype");
		     $this->load->view('retailer/package_report',$data);
		}
    }
    
    public function dmt_report(){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{
            
			$id = $this->session->userdata('cus_id');      
		    $data['dmt_txn'] = $this->db_model->getAlldata("select * from dmt_trnx as d,customers as c where d.cus_id = c.cus_id and d.cus_id ='".$id."'");
            $this->load->view('retailer/dmt_report',$data);
        }
    }
	
	public function search_dmt_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    $id = $this->session->userdata('cus_id');    
		    $sql = "select * from dmt_trnx as d,customers as c where d.cus_id = c.cus_id and d.cus_id ='".$id."' and " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(d.dmt_trnx_date) >= '".$from.' 00:00:00'."' and DATE(d.dmt_trnx_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(d.dmt_trnx_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(d.dmt_trnx_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " d.dmt_trnx_id != '' ORDER BY d.dmt_trnx_id DESC";
		    $data['dmt_txn'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('retailer/dmt_report',$data);
		}
	}
	public function ticket($id)
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['ticket']=$this->db_model->getwhere('ticket',array('ticket_id'=>$id,'cus_id'=>$this->session->userdata('cus_id')));
		    $this->load->view('retailer/ticket',$data);
		}
    }
    public function support()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $data['ticket']=$this->db_model->getAlldata('select * from ticket where cus_id="'.$this->session->userdata('cus_id').'" group by ticket_id');
		    $this->load->view('retailer/support',$data);
		}
    }
    public function send_support()
    {
        $this->form_validation->set_rules('type','Type','required|xss_clean');
		$this->form_validation->set_rules('desc','Desc','required|xss_clean');
		$this->form_validation->set_rules('subject','Bank','required|xss_clean');
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('message','Please fill the mandatory filled');
			redirect('retailer/support');
		}
		else
		{  
			$txn = $this->input->post('txnid');
			$id = $this->session->userdata('cus_id');      
			$from = 'user'; 
			$type = $this->input->post('type');
			$desc = $this->input->post('desc');
			$subject = $this->input->post('subject');
			$priority = $this->input->post('priority');
			$ndate = date('Y-m-d H:i:s');
			$yt = $this->db_model->getwhere('ticket');
			if(!empty($yt))
    			$tick = $yt[0]->ticket_id + 1;
			else
			    $tick = 1;

			if($_FILES['userfile']['name']!='')
			{
			    $path = "./includes/uploads/support/";
			    if(!is_dir($path)) //create the folder if it's not already exists
			    {
			        mkdir($path,0755,TRUE);
			    } 
			    $config['encrypt_name'] =TRUE;
			    $config['upload_path'] =$path;
			    $config['allowed_types'] = 'gif|png|jpeg|jpg';
			    $config['max_size'] = '2000000';
				$config['overwrite']     = true;
				$this->upload->initialize($config);
				if($this->upload->do_upload())
				{
				    $file_data = $this->upload->data();
				    $orginal_path='includes/uploads/support/'.$file_data['file_name'];
				}
				else
				{
				   	$this->session->set_flashdata('message', 'Error Occur ! please try again later');
				    redirect('retailer/support');
				}				        
			}
			if($txn != '')
				{
					$r = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch',array('apiclid'=>$this->session->userdata('cus_id'),'recid'=>$txn));
					if(!empty($r))
					{
						$s = $this->db_model->getwhere('ticket',array('rec_id'=>$txn));
						if(empty($s))
						{
							$data = array(
								'cus_id'	=>	$id,
								'ticket_id'	=>	$tick,
								'reply_from'	=>	$from,
								'issue'	=>	$type,
								'subject'	=>	$subject,
								'message'	=>	$desc,
								'priority'	=>	$priority,
								'ticket_date'	=>	$ndate,
								'rec_id'	=>	$txn,
								'ticket_image'	=>	$orginal_path
							);
							$this->db_model->insert_update('ticket',$data);
							$this->session->set_flashdata('message', 'Thankyou ! Our Technical Support Executives will Revert you back');
							redirect('retailer/support');
						}
						else
						{
							$this->session->set_flashdata('message', 'Ticket already arised..');
						    redirect('retailer/support');
						}
					}
					else
					{
						$this->session->set_flashdata('message', 'Transaction id is invalid');
						redirect('retailer/support');
					}
				}
				else
				{
					$data = array(
						'cus_id'	=>	$id,
						'ticket_id'	=>	$tick,
						'reply_from'	=>	$from,
						'issue'	=>	$type,
						'subject'	=>	$subject,
						'message'	=>	$desc,
						'priority'	=>	$priority,
						'ticket_date'	=>	$ndate,
						'rec_id'	=>	$txn
					);
					$this->db_model->insert_update('ticket',$data);
					$this->session->set_flashdata('message', 'Thankyou ! Our Technical Support Executives will Revert you back');
					redirect('retailer/support');
				}		
		} 
    }
	
	public function send_ticket()
	{

		$id = $this->session->userdata('cus_id');
		$from = 'user';
		$desc = $this->input->post('desc');
		$ticket = $this->input->post('ticket');
		$ndate = DATE('Y-m-d H:i:s');

        $orginal_path='';
		if ($_FILES['userfile']['name'] != '') {
			$path = "./includes/uploads/support/";
			if (!is_dir($path)) //create the folder if it's not already exists
			{
				mkdir($path, 0755, TRUE);
			}
			$config['encrypt_name'] = TRUE;
			$config['upload_path'] = $path;
			$config['allowed_types'] = '*';
			$config['max_size'] = '20000000000';
			$config['overwrite'] = FALSE;
			$this->upload->initialize($config);
			if ($this->upload->do_upload()) {
				$file_data = $this->upload->data();
				$orginal_path = 'includes/uploads/support/' . $file_data['file_name'];
			}
		} 
		                    $data = array(
								'cus_id'	=>	$id,
								'ticket_id'	=>	$ticket,
								'reply_from'	=>	$from,
								'message'	=>	$desc,
								'ticket_date'	=>	$ndate,
								'ticket_image'	=>	$orginal_path
							);
							$this->db_model->insert_update('ticket',$data);
							$this->session->set_flashdata('message', 'Thankyou ! Our Technical Support Executives will Revert you back');
							echo "<script>window.location.href='" . base_url('retailer/ticket') . '/' . $ticket . "';</script>";
							
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
	
	public function logout()
    {
        
        $cus_id = $this->session->userdata('cus_id');
        $updata=array('web_login_status'=>'loggedout');
        $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));
        $this->session->unset_userdata(array('cus_id','cus_name','cus_type','cus_email','cus_mobile','isLoginRetailer','senderMobile'));
        redirect('users_login');

	}
	
	
	//-------------------------------------------------------------D-M-T-------API------Edigital-----------------------------------------------------------------------//
	
	
	public function dmt()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
            
        }else{    
            $status = $this->service('DMT');
            if($status == 'active'){
                
                if($this->session->userdata('senderMobile') !== NULL){
                    redirect('Retailer/getbeneficary');
                }else{
        	        $this->load->view('retailer/dmt');
                }
            }else{
                
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }
        }    
    }
    
    public function dmt_user(){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
            $mobile = $_POST['mobile'];
            $name = $_POST['name'];
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            
            $status = $this->service('DMT');
            if($status == 'active'){
                
                if(isset($_POST['register'])){ 
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06007~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~$name~NA~NA";
                    $url= 'https://edigitalvillage.net/index.php/api_partner/registerDmtUser';
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
                    if($http_code == "200"){
                        $dataArr = explode('~',$server_output);
                        if($dataArr[5] == 'SUCCESS'){
                            $ref_no = $dataArr[4];
                            redirect('retailer/verifyDmtOtp/'.$ref_no);
                        }else{
                            echo "Unable to process Request! Please Try Again Later...!";
                        }
                    }else{
                        echo "Unable To get Response";
                    }
                }else if(isset($_POST['getbeneficiary'])){
                    
                    $this->session->set_userdata('senderMobile',$mobile);
                    redirect('Retailer/getbeneficary');
                }else{
                    redirect('Retailer/dmt');
                }
            }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            } 
		}    
        
    }
    
    public function checkIfDmtUserExists(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            
            $mobile = $_POST['mobile'];
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
            //print_r($server_output);
            $http_code = $info['http_code'];
            if($http_code == "200"){
                $dataArr = explode('~',$server_output);
                if($dataArr[5])
                    echo $dataArr[5];
                else
                   echo  $server_output;
            }else{
                echo "Unable To get Response";
            }
        }else{
            $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
            $this->load->view('retailer/service',$msg);
        }    
        
    }
    
    public function verifyDmtOtp($ref_np){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            $data['VerifyReferenceNo'] = $ref_np;
            $this->load->view('retailer/verifyDmtOtp',$data);
        }else{
            $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
            $this->load->view('retailer/service',$msg);
        }    
    }
    
    public function verifySenderOtp(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            $otp = $_POST['otp'];
            $VerifyReferenceNo = $_POST['VerifyReferenceNo'];
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
                if($dataArr[6]){
                    if($dataArr[5] == "SUCCESS"){
                        //echo $dataArr[5];
                        $this->session->set_flashdata('success',$dataArr[6]);
                    }else{
                        //echo $dataArr[6];
                        $this->session->set_flashdata('error',$dataArr[6]);
                    }
                }else{
                    //echo $server_output;
                    $this->session->set_flashdata('error',$server_output);
                }
            }else{
                $this->session->set_flashdata('message',"Unable To get Response");
            }
            redirect('Retailer/dmt');
            
        }else{
            $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
            $this->load->view('retailer/service',$msg);
        }    
    }
    
    
	public function getbeneficary()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $status = $this->service('DMT');
            if($status == 'active'){
                
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
    		    $mobile = $this->session->userdata('senderMobile');
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
                    if($response)
                    $res['beneficary'] = $response;
                    else
                    $this->session->set_flashdata('error',"$server_output");
                }else{
                    $this->session->set_flashdata('error',"Unable To get Response");
                }
    		    $this->load->view('retailer/view-all-bene',$res);
		    }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }   
		}
    }
    
    public function deleteBeneficiary($bene_code){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
            $status = $this->service('DMT');
            if($status == 'active'){
                
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
                
                $mobile = $this->session->userdata('senderMobile');
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
                    $dataArr = explode('~',$server_output);
                    if($dataArr[6]){
                        if($dataArr[5] == "SUCCESS"){
                            $this->session->set_flashdata('success',$dataArr[6]);
                        }else{
                            $this->session->set_flashdata('error',$dataArr[6]);
                        }
                    }else{
                        $this->session->set_flashdata('error',$server_output);
                    }
                }else{
                    $this->session->set_flashdata('error',"Unable To get Response");
                }
        	    redirect('Retailer/getbeneficary');
            }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            } 
		}    
    }

    
	public function addbeneficary()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $status = $this->service('DMT');
            if($status == 'active'){
    		    $data['bank'] = $this->db->query("select * from bank")->result();
    		    $this->load->view('retailer/addbeneficary',$data);
            }else{
                
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }    
		}
    }
    
    
    public function add_beneficary_success(){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
            $status = $this->service('DMT');
            if($status == 'active'){
		    
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
                $name = $this->input->post('name');
                $bankcode = $this->input->post('bank_code');
                $mobile = $this->input->post('mob_no');
                $ifsc = $this->input->post('ifsc_code');
                $bank_acct = $this->input->post('bank_acct');
        
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
                //print_r($server_output);
                $http_code = $info['http_code'];
                //echo $server_output;exit;
                if($http_code == "200"){
                    $dataArr = explode('~',$server_output);
                    if($dataArr[6]){
                        if($dataArr[5] == "SUCCESS"){
                            $this->session->set_flashdata('success',$dataArr[6]);
                            redirect('Retailer/getbeneficary');
                        }else{
                            $this->session->set_flashdata('error',$dataArr[6]);
                            redirect('Retailer/addbeneficary');
                        }
                    }else{
                        $this->session->set_flashdata('error',$server_output);
                        redirect('Retailer/addbeneficary');
                    }
                    
                }else{
                    $this->session->set_flashdata('error','Unable To get Response');
                    redirect('Retailer/addbeneficary');
                }
            }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }    
		}    
    }
    
    
    public function moneytransfer($details){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $status = $this->service('DMT');
            if($status == 'active'){
    		    $det = $this->encryption_model->decode($details);
    		    $arrdet = explode(';' ,$det);
    		    $data['bene_code'] = $arrdet[0];
    		    $data['acct_no'] = $arrdet[1];
    		    $data['ifsc'] = $arrdet[2];
    		    $this->load->view('retailer/moneytransfer',$data);
            }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            }      
		}
    }
    
    
    public function money_transfer_success(){
        
        $status = $this->service('DMT');
        $package_amount =0;
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
            if($status == 'active'){
                $api_mobile = $this->api_mobile;
                $api_pass = $this->api_pass;
                $api_user_id = $this->api_user_id;
                $api_reg_ip = $this->api_reg_ip;
                
                $cus_id = $this->session->userdata('cus_id');
                $acct_no = $this->input->post('acct_no');
                $ifsc = $this->input->post('ifsc');
                $adhar_no = '745862957054';//$this->input->post('aadhar_no');
                $pan_no = 'ATTPK6075F';//$this->input->post('pan_number');
                $amt = $this->input->post('amount');
                $mobile = $this->session->userdata('senderMobile');
                $benificary_code = $this->input->post('bene_code');
                $trans_type = "58";
                $token = $this->token;
                $id = rand(1000000000,9999999999);
                
                $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                $txn_clbal = $trnx_data[0]['txn_clbal'];
                
                $commission_scheme_id = $this->Db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
                $scheme_id = $commission_scheme_id[0]->scheme_id;
                $cus_type = $commission_scheme_id[0]->cus_type;
                
                if($scheme_id != 0 ){
                    $checkIfActive = $this->Db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
                    if($checkIfActive){
                        $commission_scheme_package = $this->Db_model->getAllData("SELECT * FROM commission_scheme_dmt WHERE scheme_id = '$scheme_id'");
                        //print_r($commission_scheme_package);
                        if($commission_scheme_package){
                            if($cus_type == "retailer"){
                                foreach($commission_scheme_package as $comm){
                                    $range = explode('-',$comm->slab);
                                     $minRange = $range[0];
                                     $maxRange = $range[1];
                                    if($amt >= $minRange && $amt <= $maxRange){
                                        $retailer_comm = $comm->retailer_comm;
                                        $comm_type = $comm->type;
                                        if($retailer_comm){
                                            if($comm_type == "percent"){
                                                $commission_amount = round((($retailer_comm / 100) * $amt),3);
                                            }else{
                                                $commission_amount = $retailer_comm;
                                            }
                                            $retailer_comm = $commission_amount;
                                        }    
                                        break;
                                    }
                                }
                            }
                        }else{
                            $retailer_comm = '0';
                        }    
                    }else{
                        $retailer_comm = '0';
                    }    
                }else{
                    $retailer_comm = '0';
                }
                $totalamount = $amt + $retailer_comm;
                
                if($totalamount > $txn_clbal){
                    $this->session->set_flashdata('error','Insufficient Wallet Balance For Transaction.Please Contact Admin');
                    redirect('retailer/getbeneficary');
                }else{
                    
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
                    //print_r($info);exit;
                    //print_r($err);
                    $http_code = $info['http_code'];
                    //echo $server_output;
                    if($http_code == "200"){
                        $resArr = explode('~',$server_output);
                        if($resArr[5] == 'SUCCESS'){
                            
                            $request_id = $resArr[1];
                            $mobile_no = $resArr[2];
                            $bene_code = $resArr[3];
                            $status = $resArr[5];
                            $imps_ref_no = $resArr[6];
                            $desc = $resArr[7];
                            $bene_name = $resArr[8];
                            $trans_id = $resArr[9];
                            $bank_name = $resArr[10];
                            
                            $cus_id = $this->session->userdata('cus_id');
                            
                            $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                            $txn_clbal = $trnx_data[0]['txn_clbal'];
                            
                            $debit_amount = $package_amount + $amt;
                            $new_txn_clbal = $txn_clbal - $debit_amount;
                    		$ip = $_SERVER['REMOTE_ADDR'];
                    		$time = time();
                    		$date = DATE('Y-m-d H:i:s');
                    		
                    		    $dmt_tnx_data = array(
                                    'request_id' => $request_id,
                                    'mobile_no' => $mobile_no,
                                    'bene_code' => $bene_code,
                                    'status' => $status,
                                    'imps_ref_no' => $imps_ref_no,
                                    'description' => $desc,
                                    'bene_name' => $bene_name,
                                    'ifsc' => $ifsc,
                                    'account_num' => $acct_no,
                                    'trans_id' => $trans_id,
                                    'bank_name' => $bank_name,
                                    'amount' => $amt,
                                    'charge' =>  $package_amount,
                                    'cus_id' => $cus_id,
                                    'response' => $server_output
                                );
                                $dmt=$this->db_model->insert_update('dmt_trnx',$dmt_tnx_data);
                                $insert_id = $this->db->insert_id();
                                
                            if($insert_id){
                                $insert_data = array(
                                    'txn_agentid' => $cus_id,
                                    'dmt_txn_recrefid' => $insert_id,
                                    'txn_opbal' => $txn_clbal,
                                    'txn_crdt' => '0',
                                    'txn_dbdt' => $debit_amount,
                                    'txn_clbal' => $new_txn_clbal,
                                    'txn_checktime' => '',
                                    'txn_type' => 'DMT',
                                    'txn_fromto' => '0',
                                    'txn_time' => $time,
                                    'txn_date' => $date,
                                    'txn_optional' => $imps_ref_no,
                                    'txn_ip' => $ip,
                                    'txn_comment' => $desc,
                                    'transaction_id' => $trans_id,
                                    'transaction_ref' => ''
                                );
                                $res=$this->db_model->insert_update('exr_trnx',$insert_data);    
                            }
                            $this->session->set_flashdata('success',$desc);
                            $this->creditDmtCommission($cus_id,$amt,$insert_id);
                            redirect("retailer/dmt_report");
                            
                        }elseif($resArr[5] == 'PENDING'){
                            
                            $request_id = $resArr[1];
                            $mobile_no = $resArr[2];
                            $bene_code = $resArr[3];
                            $status = $resArr[5];
                            $imps_ref_no = $resArr[6];
                            $desc = $resArr[7];
                            $bene_name = $resArr[8];
                            $trans_id = $resArr[9];
                            $bank_name = $resArr[10];
                            
                            $cus_id = $this->session->userdata('cus_id');
                            $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                            $txn_clbal = $trnx_data[0]['txn_clbal'];
                            
                            $debit_amount = $amt;
                            $new_txn_clbal = $txn_clbal - $debit_amount;
                    		$ip = $_SERVER['REMOTE_ADDR'];
                    		$time = time();
                    		$date = DATE('Y-m-d H:i:s');
                    		
                    		    $dmt_tnx_data = array(
                                    'request_id' => $request_id,
                                    'mobile_no' => $mobile_no,
                                    'bene_code' => $bene_code,
                                    'status' => $status,
                                    'imps_ref_no' => $imps_ref_no,
                                    'description' => $desc,
                                    'bene_name' => $bene_name,
                                    'trans_id' => $trans_id,
                                    'bank_name' => $bank_name,
                                    'ifsc' => $ifsc,
                                    'account_num' => $acct_no,
                                    'amount' => $amt,
                                    'charge' =>  $package_amount,
                                    'cus_id' => $cus_id,
                                    'response' => $server_output
                                );
                                $dmt=$this->db_model->insert_update('dmt_trnx',$dmt_tnx_data);
                                $insert_id = $this->db->insert_id();
                                
                            if($insert_id){
                                $insert_data = array(
                                    'txn_agentid' => $cus_id,
                                    'dmt_txn_recrefid' => $insert_id,
                                    'txn_opbal' => $txn_clbal,
                                    'txn_crdt' => '0',
                                    'txn_dbdt' => $debit_amount,
                                    'txn_clbal' => $new_txn_clbal,
                                    'txn_checktime' => '',
                                    'txn_type' => 'DMT',
                                    'txn_fromto' => '0',
                                    'txn_time' => $time,
                                    'txn_date' => $date,
                                    'txn_optional' => $imps_ref_no,
                                    'txn_ip' => $ip,
                                    'txn_comment' => $desc,
                                    'transaction_id' => $trans_id,
                                    'transaction_ref' => ''
                                );
                                $res=$this->db_model->insert_update('exr_trnx',$insert_data);   
                                $this->creditDmtCommission($cus_id,$amt,$insert_id);
                            }
                            $this->session->set_flashdata('pending',$desc);
                            redirect("retailer/dmt_report");
                            
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
                            
                            $cus_id = $this->session->userdata('cus_id');
                            /*$data = $this->db->query("SELECT * FROM `dmt_commission_slab` as d,customers as c where d.dmt_comm_id = c.dmt_comm_id and cus_id = '".$cus_id."' ")->result_array();
                            $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                            $txn_clbal = $trnx_data[0]['txn_clbal'];
                            if($data)$package_amount = $data[0]['amount']; else $package_amount=0 ;
                            $debit_amount = $package_amount + $amt;
                            $new_txn_clbal = $txn_clbal - $debit_amount;
                    		$ip = $_SERVER['REMOTE_ADDR'];
                    		$time = time();
                    		$date = DATE('Y-m-d H:i:s');*/
                    		
                    		    $dmt_tnx_data = array(
                                    'request_id' => $request_id,
                                    'mobile_no' => $mobile_no,
                                    'bene_code' => $bene_code,
                                    'status' => $status,
                                    'imps_ref_no' => $imps_ref_no,
                                    'description' => $desc,
                                    'bene_name' => $bene_name,
                                    'trans_id' => $trans_id,
                                    'bank_name' => $bank_name,
                                    'ifsc' => $ifsc,
                                    'account_num' => $acct_no,
                                    'amount' => $amt,
                                    'charge' =>  $package_amount,
                                    'cus_id' => $cus_id,
                                    'response' => $server_output
                                );
                                
                                $dmt=$this->db_model->insert_update('dmt_trnx',$dmt_tnx_data);
                                $insert_id = $this->db->insert_id();
                            $this->session->set_flashdata('error',$desc);
                            redirect("retailer/dmt_report");
                        }else{
                            $this->session->set_flashdata('error',$server_output);
                            redirect("retailer/getbeneficary");
                        }
                    }else{
                        $this->session->set_flashdata('error','This Unable To get Response');
                        redirect('retailer/getbeneficary');
                    }
                }
            }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
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
            						    $this->db_model->insert_update('dmt_trnx',array('charge'=>$commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
            						    
                						$txntype = "Retailer Dmt Charge";
                						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$commission_amount, 'txn_clbal'=>$last_balance[0]->txn_clbal - $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_reffer,'txn_date'=>	date('Y-m-d H:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}else{
                						    $txntype = "Distributor Dmt Commission";
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_reffer,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$dist_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $dist_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$master_cus_reffer,'txn_date'=>	date('Y-m-d H:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}else{
                						    $txntype = "Master Dmt Commission";
                    						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$master_cus_reffer,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$mast_commission_amount, 'txn_dbdt'=>'0', 'txn_clbal'=> $mast_commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
                						}
                							
                                    }
                                }
                    
                              break;  
                            }
                        }
                    }
                }
            }else{
                return false;
            }
        }
    }
    
//------------------------------------------------------------END------D-M-T-------API------Edigital-----------------------------------------------------------------------//


//-------------------------------------------------------------------PAN-------API------Edigital--------------------------------------------------------------------------//


	public function registerVLEID()
	{
	    if(isset($_POST['register']))
        {
            $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
    	    $cus_id = $this->session->userdata('cus_id');
    	    $updata=array( 'shop_name' =>$_POST['shop_name'],
                                    'cus_city' =>$_POST['cus_city'],
                                    'cus_state' =>$_POST['cus_state'],
                                    'cus_pincode' =>$_POST['cus_pincode'],
                                    'cus_adharno' =>$_POST['cus_adharno'],
                                    'cus_panno' =>$_POST['cus_panno']
                                    );
            $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));
    		$cusdt = $this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
            $curl = curl_init(); 
    	    $vle_id='bharatpay'.$cus_id;
    	    $url = "https://edigitalvillage.net/index.php/api_partner/register_vle_id?api_pass=$api_pass&api_mobile=$api_mobile&api_user_id=$api_user_id&api_reg_ip=$api_reg_ip&vle_id=$vle_id&vle_name=".urlencode($cusdt[0]->cus_name)."&vle_mob=".$this->session->userdata('cus_mobile')."&vle_email=".$cusdt[0]->cus_email."&vle_shop=".urlencode($cusdt[0]->shop_name)."&vle_loc=".urlencode($cusdt[0]->cus_city)."&vle_state=".$cusdt[0]->cus_state."&vle_pin=".$cusdt[0]->cus_pincode."&vle_uid=".$cusdt[0]->cus_adharno."&vle_pan=".$cusdt[0]->cus_panno;
            //echo $url;exit;
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, 
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "get",
           // CURLOPT_POSTFIELDS => $array_value, 
                                ));
            $response = curl_exec($curl); 
            $info = curl_getinfo($curl);
            $err = curl_error($curl);
            //print_r($info);exit;
            curl_close($curl);
            $array = json_decode($response, true);
            if($array['message']=='Instant Vle Added Successfully' ||$array['message']=='Vle Data Atready Exist'){
            $this->session->set_flashdata('success',$array['message']);
                $updata=array( 'pan_register' =>"Yes");
                $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));
            }
            else{
                $this->session->set_flashdata('error',$array['message']);
            }
            redirect('retailer/pan_card');
        }else{
            redirect('retailer/pan_card');
        }    

	}
	

	
	public function vlestatus()
	{
	    
        $api_mobile = $this->api_mobile;
        $api_pass = $this->api_pass;
        $api_user_id = $this->api_user_id;
        $api_reg_ip = $this->api_reg_ip;
        
	    $cus_id = "bharatpay".$this->session->userdata('cus_id');
	    $curl = curl_init(); curl_setopt_array($curl, array(
        CURLOPT_URL => "https://edigitalvillage.net/index.php/api_partner/vle_status", 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "", 
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0, 
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array('api_mobile' =>$api_mobile,
                                    'api_pass' => $api_pass,
                                    'api_user_id' => $api_user_id,
                                    'api_reg_ip' => $api_reg_ip, 
                                    'vle_id' => $cus_id), 
                                    ));
        $response = curl_exec($curl); 
        curl_close($curl);
        $array = json_decode($response, true);
        if($array['status']=='SUCCESS'){
        $this->session->set_flashdata('success',$array['message']);
        }
        else{
            $this->session->set_flashdata('error',$array['message']);
        }
        redirect('retailer/pan_card');

	}
	public function buycoupon()
	{
	    if(isset($_POST['buy_coupon']))
        {
            $no_coupon=$_POST['noofcoupon'];
            $coupon_type=$_POST['coupon_type'];
            $amount=$_POST['amount'];
            
    		    
    	    $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            
    	    $cus_id = "bharatpay".$this->session->userdata('cus_id');
    	    $curl = curl_init(); curl_setopt_array($curl, array(
            CURLOPT_URL => "https://edigitalvillage.net/index.php/api_partner/buy_coupon", 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "", 
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0, 
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('api_mobile' =>$api_mobile,
                                        'api_pass' => $api_pass,
                                        'api_user_id' => $api_user_id,
                                        'api_reg_ip' => $api_reg_ip, 
                                        'vle_id' => $cus_id,
                                        'noofcoupon' => $no_coupon,
                                        'coupon_type' => $coupon_type,
                                        'amount' => $amount,
                                        ), 
                                        ));
            $response = curl_exec($curl); 
            curl_close($curl);
            $array = json_decode($response, true);
            if($array['status']=='SUCCESS'){
                $record=array(  'order_id'=>$array['order_id'],
                            'date'=>$array['date'],
                            'status'=>$array['status'],
                            'message'=>$array['message'],
                            'vle_id'=>$array['vle_id'],
                            'type'=>$array['type'],
                            'qty'=>$array['qty'],
                            'rate'=>$array['rate'],
                            'amount'=>$array['amount'],
                            'old_bal'=>$array['old_bal'],
                            'new_bal'=>$array['new_bal']
                            );
        		$this->db_model->insert_update('coupon_buy',$record);
        		$inserId = $this->db->insert_id();
        		
        		$cus_id = $this->session->userdata('cus_id');
                $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                $txn_clbal = $trnx_data[0]['txn_clbal'];
                $newclosingbal=$txn_clbal-$amount;
                $ip = $_SERVER['REMOTE_ADDR'];
                		$time = time();
                		$date = DATE('Y-m-d H:i:s');
                $insert_data = array(
                                'txn_agentid' => $cus_id,
                                'coupon_buy_id' => $inserId,
                                'txn_opbal' => $txn_clbal,
                                'txn_crdt' => '0',
                                'txn_dbdt' => $amount,
                                'txn_clbal' => $newclosingbal,
                                'txn_checktime' => '',
                                'txn_type' => 'Pan Card',
                                'txn_fromto' => '0',
                                'txn_time' => $time,
                                'txn_date' => $date,
                                'txn_optional' => '',
                                'txn_ip' => $ip,
                                'txn_comment' => 'buy Coupon',
                                'transaction_id' => '',
                                'transaction_ref' => ''
                            );
                            $res=$this->db_model->insert_update('exr_trnx',$insert_data);  
                $this->creditPanCommission($cus_id,$amount,$inserId);
                $this->session->set_flashdata('success',$array['message']);
            }
            else{
                $this->session->set_flashdata('error',$array['message']);
            }
        }
        redirect('retailer/pan_card');

	}
	public function coupon_status($order_id)
	{
	    
	        $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            
    	   // $cus_id = "bharatpay".$this->session->userdata('cus_id');
    	    $curl = curl_init(); curl_setopt_array($curl, array(
            CURLOPT_URL => "https://edigitalvillage.net/index.php/api_partner/coupon_status", 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "", 
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0, 
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('api_mobile' =>$api_mobile,
                                        'api_pass' => $api_pass,
                                        'api_user_id' => $api_user_id,
                                        'api_reg_ip' => $api_reg_ip,
                                        'order_id' => $order_id
                                        ), 
                                        ));
            $response = curl_exec($curl); 
            curl_close($curl);
            $array = json_decode($response, true);
            if($array['status']=='SUCCESS'){
            $this->session->set_flashdata('success',$array['message']);
            }
            else{
                $this->session->set_flashdata('error',$array['message']);
            }
            redirect('retailer/pan_card');

	}
	public function passwordresetVLEID()
	{
	    
	        $api_mobile = $this->api_mobile;
            $api_pass = $this->api_pass;
            $api_user_id = $this->api_user_id;
            $api_reg_ip = $this->api_reg_ip;
            
    	    $cus_id = "bharatpay".$this->session->userdata('cus_id');
    	    $curl = curl_init(); curl_setopt_array($curl, array(
            CURLOPT_URL => "https://edigitalvillage.net/index.php/api_partner/coupon_status", 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "", 
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0, 
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('api_mobile' =>$api_mobile,
                                        'api_pass' => $api_pass,
                                        'api_user_id' => $api_user_id,
                                        'api_reg_ip' => $api_reg_ip,
                                        'cus_id' => $cus_id
                                        ), 
                                        ));
            $response = curl_exec($curl); 
            curl_close($curl);
            $array = json_decode($response, true);
            if($array['status']=='SUCCESS'){
            $this->session->set_flashdata('success',$array['message']);
            }
            else{
                $this->session->set_flashdata('error',$array['message']);
            }
            redirect('retailer/pan_card');

	}


//--------------------------------------------------------------END-------PAN-------API------Edigital------------------------------------------------------------------------//




//---------------------------------------------------------------------AEPS-----APIS-----------------------------------------------------------------------------------------//

    public function onboarding()
    {
        
        $CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/onboarding";
        
        $param['latitude'] = $latitude=$_POST['latitude'];
        $param['longitude'] = $longitude=$_POST['longitude'];
        $param['merchantName'] = $merchantName=$_POST['merchantName'];
        $param['merchantPhoneNumber'] = $merchantPhoneNumber=$_POST['merchantPhoneNumber'];
        $param['companyLegalName'] = $companyLegalName=$_POST['companyLegalName'];
        $param['companyMarketingName'] = $companyMarketingName =$_POST['companyMarketingName '];
        $param['emailId'] = $emailId=$_POST['emailId'];
        $param['merchantPinCode'] = $merchantPinCode=$_POST['merchantPinCode'];
        $param['merchantCityName'] = $merchantCityName=$_POST['merchantCityName'];
        $param['tan'] = $tan=$_POST['tan'];
        $param['merchantDistrictName'] = $merchantDistrictName=$_POST['merchantDistrictName'];
        $param['merchantState'] = $merchantState=$_POST['merchantState'];
        $param['merchantAddress'] = $merchantAddress=$_POST['merchantAddress'];
        $param['userPan'] = $userPan=$_POST['userPan'];
        $param['aadhaarNumber'] = $aadhaarNumber=$_POST['aadhaarNumber'];
        $param['gstInNumber'] = $gstInNumber=$_POST['gstInNumber'];
        $param['companyOrShopPan'] = $companyOrShopPan=$_POST['companyOrShopPan'];
        $param['companyBankAccountNumber'] = $companyBankAccountNumber=$_POST['companyBankAccountNumber'];
        $param['bankIfscCode'] = $bankIfscCode=$_POST['bankIfscCode'];
        $param['companyBankName'] = $companyBankName=$_POST['companyBankName'];
        $param['bankBranchName'] = $bankBranchName=$_POST['bankBranchName'];
        $param['bankAccountName'] = $bankAccountName=$_POST['bankAccountName'];
        
        $cancellationCheckImages="";
        $shopAndPanImage="";$ekycDocuments="";
        if(!empty($_FILES['cancellationCheckImages']['name'])){
                    $config['upload_path'] = 'includes/uploads/kyc/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['file_name'] = $_FILES['cancellationCheckImages']['name'];
                    
                    //Load upload library and initialize configuration
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config);
                    
                    if($this->upload->do_upload('cancellationCheckImages')){
                        $uploadData = $this->upload->data();
                        $picture = $uploadData['file_name'];
                        $param['cancellationCheckImages'] = $cancellationCheckImages = "http://easypayall.in/includes/uploads/kyc/$picture";
                    }
				}
				if(!empty($_FILES['shopAndPanImage']['name'])){
                    $config['upload_path'] = 'includes/uploads/kyc/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['file_name'] = $_FILES['shopAndPanImage']['name'];
                    
                    //Load upload library and initialize configuration
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config);
                    
                    if($this->upload->do_upload('shopAndPanImage')){
                        $uploadData = $this->upload->data();
                        $picture = $uploadData['file_name'];
                        $param['shopAndPanImage'] = $shopAndPanImage = "http://easypayall.in/includes/uploads/kyc/$picture";
                    }
				}
				if(!empty($_FILES['ekycDocuments']['name'])){
                    $config['upload_path'] = 'includes/uploads/kyc/';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['file_name'] = $_FILES['ekycDocuments']['name'];
                    
                    //Load upload library and initialize configuration
                    $this->load->library('upload',$config);
                    $this->upload->initialize($config);
                    
                    if($this->upload->do_upload('ekycDocuments')){
                        $uploadData = $this->upload->data();
                        $picture = $uploadData['file_name'];
                        $param['ekycDocuments'] = $ekycDocuments = "http://easypayall.in/includes/uploads/kyc/$picture";
                    }
				}
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $merchantLoginId = '';
        
        $param['cus_id'] = $cus_id = $this->session->userdata('cus_id');
        $param['merchantLoginId'] = $merchantLoginId = 'EasyPay'.$cus_id;
        $param['merchantLoginPin'] = $merchantLoginPin = 'EasyPay'.$cus_id;
            
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
            $data = json_decode($res,true);
            $err = curl_error($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
        
            
        // $data=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
        if($data['data']['merchants'][0]['status']='Successfully Created' || $data['data']['merchants'][0]['status']='Successfully Updated')
        {
            $record=array(  'aeps_merchantLoginId'=>$this->encryption_model->encode($merchantLoginId),
                            'aeps_merchantLoginPin'=>$this->encryption_model->encode($merchantLoginPin),
                            'aeps_userPan'=>$userPan,
                            'aeps_aadhaarNumber'=>$aadhaarNumber,
                            'aeps_AccountNumber'=>$companyBankAccountNumber,
                            'aeps_bankIfscCode'=>$bankIfscCode,
                            'aeps_bankAccountName'=>$bankAccountName,
                            'bankName'=>$companyBankName,
                            // "aeps_kyc_status"=>"KYC Completed"
                            );
    		$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    		
    		$CURLOPTOTP_URL="https://tiktikpay.in/index.php/api_partner/updateaepsekyc";
            
            $param['merchantLoginId'] = $merchantLoginId;
            $param['merchantPhoneNumber'] = $merchantPhoneNumber;
            $param['aadhaarNumber'] = $aadhaarNumber;
            $param['userPan'] = $userPan;
            
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
                    CURLOPT_URL => $CURLOPTOTP_URL,
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
            
		  //  $response=$this->Aeps_model->onboarding($CURLOPTOTP_URL,$valuesOTP);
		  
		    if($response['status']=='true'){
		        $record=array(  
		                    'primaryKeyId'=>$response['data']['primaryKeyId'],
                            'encodeFPTxnId'=>$response['data']['encodeFPTxnId'],
                            );
                            
    		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
		        $this->session->set_flashdata('success',$response['message']);
		        redirect('retailer/aepskycotp');
		      //redirect('retailer/aeps_transaction');
		    }
		    else{
		        $this->session->set_flashdata('error',$response['message']);
		        redirect('retailer/aeps');
		    }
        }
        else{
            $this->session->set_flashdata('message','KYC Not Completed...!!!');
        }
        redirect('retailer/aeps_transaction');
    }
    
    
    public function aepskycotp()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('retailer/aepsotp');
    	}
    }
    
    
    public function aepskycvalidateotp()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    
		    $CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/aepskycvalidateotp";
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['primaryKeyId'] = $primaryKeyId=$data['profile'][0]->primaryKeyId;
            $param['encodeFPTxnId'] = $encodeFPTxnId=$data['profile'][0]->encodeFPTxnId;
            $param['otp'] = $_POST['otp'];
            
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
		  
		    if($response['status']=='true'){
		        $record=array("aeps_kyc_status"=>"KYC Completed");
    		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
		        $this->session->set_flashdata('success',$response['message']);
		        redirect('retailer/aepsekyc');
		    }
		    else{
		        $this->session->set_flashdata('error',$response['message']);
		        redirect('retailer/aepskycotp');
		    }
		}
		
    }
    
    public function aepskycresendotp()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/aepskycresendotp";
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
            
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['primaryKeyId'] = $primaryKeyId=$data['profile'][0]->primaryKeyId;
            $param['encodeFPTxnId'] = $encodeFPTxnId=$data['profile'][0]->encodeFPTxnId;
		    
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
		    
		    if($response['status']=='true'){
		        $this->session->set_flashdata('success',$response['message']);
		    }
		    else{
		        $this->session->set_flashdata('error',$response['message']);
		    }
		}
		redirect('retailer/aepskycotp');
    }
    
    public function aepsekyc()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    
		    $status = $this->service('AEPS');
            if($status == 'active'){
            
    		    $data['aeps_state'] = $this->db_model->getwhere('aeps_state');
    		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
    		    $this->load->view('retailer/updateaeps',$data);
            }else{
            
                $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            } 
		}
    }
    
    
    public function updateaepsekyc()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    
		    $status = $this->service('AEPS');
            if($status == 'active'){
                
            $CURLOPT_URL="https://tiktikpay.in/index.php/api_partner/updateaepsekyc";
                
            $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['merchantPhoneNumber'] = $merchantPhoneNumber=$this->encryption_model->decode($data['profile'][0]->cus_mobile);
            $param['aadhaarNumber'] = $aadhaarNumber=$data['profile'][0]->aeps_aadhaarNumber;
            $param['userPan'] = $userPan=$data['profile'][0]->aeps_userPan;
            
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
    		    if($response['status']=='true'){
    		        $record=array(  
    		                    'primaryKeyId'=>$response['data']['primaryKeyId'],
                                'encodeFPTxnId'=>$response['data']['encodeFPTxnId'],
                                );
                                
        		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    		        $this->session->set_flashdata('success','OTP Send On Register Mobile');
    		        
    		        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
    		        redirect('retailer/aepskycotp');
    		    }
    		    else if($response['message']=='Ekyc already Verified'){
    		        $record=array(  
		                'aeps_kyc_status'=>'KYC Completed',
		                    'newaepskyc_status'=>'done'
                            );
                            
        		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    		        $this->session->set_flashdata('success',$response['message']);
    		        redirect('retailer/aeps_transaction');
    		    }else if($response['status']==''){
    		        $this->session->set_flashdata('error',$response['message']);
    		        redirect('retailer/aeps');
    		    }
    		    else{
    		        redirect('retailer/updateaepsekyc');
    		    }
                
            }else{
            
                $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
                $this->load->view('retailer/service',$msg);
            } 
		}
    }
    
    
    public function aepsekyc_submit()
    {
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $CURLOPT_URL = "https://tiktikpay.in/index.php/api_partner/aepsekyc_submit";
		    
		    $param['requestRemarks'] = $requestRemarks=$_POST['requestRemarks'];
		    $param['userPan'] = $userPan=$_POST['userPan'];
            $param['adhaarNumber'] = $adhaarNumber=$_POST['aadhaarNumber'];
		    $param['captureResponse'] = $captureResponse=$_POST['txtPidData'];
            $param['PidOptions'] = $PidOptions=$_POST['PidOptions'];
        
	        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
            $param['merchantLoginId'] = $merchantLoginId=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
            $param['primaryKeyId'] = $primaryKeyId=$data['profile'][0]->primaryKeyId;
            $param['encodeFPTxnId'] = $encodeFPTxnId=$data['profile'][0]->encodeFPTxnId;
            
            
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

        $recd=array('ekyrequest'=>json_encode($request));
        $this->db_model->insert_update('customers',$recd,array('cus_id'=>$this->session->userdata('cus_id')));
          
        $recd=array('ekycresponse'=>json_encode($response));
        $this->db_model->insert_update('customers',$recd,array('cus_id'=>$this->session->userdata('cus_id')));
         
         
		    if($response['status']=='true'){
		        $record=array(  
		                'aeps_kyc_status'=>'KYC Completed',
		                    'newaepskyc_status'=>'done'
                            );
                            
    		    $this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
		        $this->session->set_flashdata('success',$response['message']);
		        redirect('retailer/aeps_transaction');
		    }
		    else{
		        $this->session->set_flashdata('error',$response['message']);
		        redirect('retailer/aepsekyc');
		    }
		}
		
    }
    	
    	
    public function cashWithdrawal()
	{
	   // print_r($this->input->post());exit;
       if(isset($_POST['cashwithdrawal'])){
             $this->form_validation->set_rules('txtPidData','txtPidData','required');
    		$this->form_validation->set_rules('cwadhaarNumber','cwadhaarNumber','required');
    		$this->form_validation->set_rules('cwnationalBankIdenticationNumber','cwnationalBankIdenticationNumber','required');
    		$this->form_validation->set_rules('cwmobileNumber','cwmobileNumber','required');
    		$this->form_validation->set_rules('cwtransactionAmount','cwtransactionAmount','required');
    		if($this->form_validation->run() == TRUE)
    		{
                $request = "";
    	        $msg="8630450571-89938-143-92.204.134.236";  
        	    $param['msg'] = $msg;
        	    $param['txtPidData'] = $_POST['txtPidData'];
        		$param['adhaarNumber'] = $_POST['cwadhaarNumber'];
        		$param['nationalBankIdenticationNumber'] = $_POST['cwnationalBankIdenticationNumber'];
        		$param['mobileNumber'] = $_POST['cwmobileNumber'];
        		$param['transactionAmount'] = $_POST['cwtransactionAmount'];
        		$param['type'] = 'cashwithdrawal';
        		
        		$cus_id=$this->session->userdata('cus_id');
        		    $param['cus_id'] = $this->api_user_id;
        		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
                    $param['cus_name']=$data['profile'][0]->cus_name;
                    $param['cus_mobile']=$this->encryption_model->decode($data['profile'][0]->cus_mobile);
                    $param['merchantLoginId']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
                    $param['merchantLoginPin']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        		
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
                    
                $CURLOPT_URL='https://tiktikpay.in/index.php/api_partner/aepsapi';
                
                
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
                $this->db_model->insert_update('aeps_transaction_fch',array('request'=>$request,'response'=>$response));
                if($array['status'])
                {
                    if($array['result']['transactionStatus']=='successful')
                    {
                        $balanceAmount='0';$ministmt='0';$traamt='0';
                        if($param['type']=='ministatement'){
                           $balanceAmount='0';
                           $ministmt=json_encode($array['ministatement']);
                           $transactionStatus=$array['result']['transactionStatus'];
                           $tratype='MS';
                           $terminalId='';$requestTransactionTime='';
                           $merchantTransactionId='';
                        }
                        else{
                            $balanceAmount=$array['result']['balanceAmount'];
                            $traamt=$array['result']['transactionAmount'];
                            $tratype=$array['result']['transactionType'];
                            $transactionStatus=$array['result']['transactionStatus'];
                            $terminalId=$array['result']['terminalId'];
                            $requestTransactionTime=$array['result']['requestTransactionTime'];
                            $merchantTransactionId=$array['result']['merchantTransactionId'];
                        }
                        $inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('aadhar_number'=>$_POST['cwadhaarNumber'],'balanceAmount'=>$balanceAmount,'ministatement'=>$ministmt,'aeps_bank_id'=>$_POST['cwnationalBankIdenticationNumber'],'amount'=>$traamt,'status'=>$transactionStatus,'transactionType'=>$tratype,'transaction_ref_id'=>$array['result']['fpTransactionId'],'utr'=>$array['result']['bankRRN'],'apiclid'=>$cus_id,'through'=>'Web'));
                        $inserId = $this->db->insert_id();
                        $amount = $array['result']['transactionAmount'];
                        
                        if($array['result']['transactionType']=='CW')
                        {
                            $cus_aeps_bal_data=$this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='$cus_id' ORDER BY aeps_txn_id DESC LIMIT 1");
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            $txntype = 'AEPS Cash Withdrawal';
                            $total = $cus_aeps_bal + $amount;
                    		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                        
                            $this->creditAepsCommission($cus_id,$amount,$inserId);   
                        }
                        if($array['result']['transactionType']=='M')
                        {
                            $cus_aeps_bal_data=$this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='$cus_id' ORDER BY aeps_txn_id DESC LIMIT 1");
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            $txntype = 'AEPS Cash Withdrawal';
                            $total = $cus_aeps_bal + $amount;
                    		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                        
                            $this->creditAdharPayCommission_get($cus_id,$amount,$inserId);
                        }
                    
                    
                            $this->session->set_flashdata('print'," Rs. $amount Withdrawal Successfullly");
                            $this->session->set_flashdata('printid','https://easypayall.in/Retailer/cashwithdrawalaeps_print/'.$inserId);
                        }
                        else{
                            $this->session->set_flashdata('error',$array['message']);
                        }
                }
                else{$this->session->set_flashdata('error',$array['message']);}
            redirect('retailer/aeps_transaction');
    	   
    	   }
    	   else{
    	       $this->session->set_flashdata('error','Please fill the mandatory filled.');
           }
       }
	   else if(isset($_POST['balancecheck'])){
	       $this->form_validation->set_rules('txtPidData','txtPidData','required');
    		$this->form_validation->set_rules('bcadhaarNumber','bcadhaarNumber','required');
    		$this->form_validation->set_rules('bcnationalBankIdenticationNumber','bcnationalBankIdenticationNumber','required');
    		$this->form_validation->set_rules('bcmobileNumber','bcmobileNumber','required');
    		if($this->form_validation->run() == TRUE)
    		{
	        $request = "";
	        $msg="8630450571-89938-143-92.204.134.236"; 
    	    $param['msg'] = $msg;
    	    $param['txtPidData'] = $_POST['txtPidData'];
    		$param['adhaarNumber'] = $_POST['bcadhaarNumber'];
    		$param['nationalBankIdenticationNumber'] = $_POST['bcnationalBankIdenticationNumber'];
    		$param['mobileNumber'] = $_POST['bcmobileNumber'];
    		$param['transactionAmount'] = '0';
    		$param['type'] = 'balancecheck';
    		
    		$cus_id=$this->session->userdata('cus_id');
    		    $param['cus_id'] = $cus_id;
    		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
                $param['cus_name']=$data['profile'][0]->cus_name;
                $param['cus_mobile']=$this->encryption_model->decode($data['profile'][0]->cus_mobile);
                $param['merchantLoginId']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
                $param['merchantLoginPin']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
    		
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
                
            $CURLOPT_URL='https://tiktikpay.in/index.php/api_partner/aepsapi';
            
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
            
            $this->db_model->insert_update('aeps_transaction_fch',array('apiclid'=>$cus_id,'request'=>json_encode($request),'response'=>json_encode($response)));
            if($array['status'])
            {
                if($array['result']['transactionStatus']=='successful')
                {
                    $this->session->set_flashdata('success',$array['result']['balanceAmount']);
                }
                else{
                    $this->session->set_flashdata('error',$array['message']);
                }
            }
            else{$this->session->set_flashdata('error',$array['message']);}
            redirect('retailer/aeps_transaction');
	   }
	        else{
	       $this->session->set_flashdata('error','Please fill the mandatory filled.');
	    }
	   }
	   else if(isset($_POST['ministetement'])){
	       $this->form_validation->set_rules('txtPidData','txtPidData','required');
		$this->form_validation->set_rules('msadhaarNumber','msadhaarNumber','required');
		$this->form_validation->set_rules('msnationalBankIdenticationNumber','msnationalBankIdenticationNumber','required');
		$this->form_validation->set_rules('msmobileNumber','msmobileNumber','required');
    		if($this->form_validation->run() == TRUE)
    		{
    	       $request = "";
    	        $msg="8630450571-89938-143-92.204.134.236";  
        	    $param['msg'] = $msg;
        	    $param['txtPidData'] = $_POST['txtPidData'];
        		$param['adhaarNumber'] = $_POST['msadhaarNumber'];
        		$param['nationalBankIdenticationNumber'] = $_POST['msnationalBankIdenticationNumber'];
        		$param['mobileNumber'] = $_POST['msmobileNumber'];
        		$param['transactionAmount'] = '0';
        		$param['type'] = 'ministatement';
        		$adhaarNumber = $_POST['msadhaarNumber'];
            	$nationalBankIdenticationNumber = $_POST['msnationalBankIdenticationNumber'];
        		$cus_id=$this->session->userdata('cus_id');
        		    $param['cus_id'] = $cus_id;
        		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
                    $param['cus_name']=$data['profile'][0]->cus_name;
                    $param['cus_mobile']=$this->encryption_model->decode($data['profile'][0]->cus_mobile);
                    $param['merchantLoginId']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
                    $param['merchantLoginPin']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        		
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
                    
                $CURLOPT_URL='https://tiktikpay.in/index.php/api_partner/aepsapi';
                
                
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
                // print_r($response);
                // echo "<br>";
                // print_r($array['ministatement']);
                
                if($array['status'])
                {
                    $tabledata = '';
                    if($array['result']['transactionStatus']=='successful'){
                        
                        $resdata=$array['ministatement'];
                        for($i=0;$i<count($resdata);$i++){ 
                            
                            $date=$resdata[$i]['date'];
                            $tratype=$resdata[$i]['txnType'];
                            $amt=$resdata[$i]['amount'];
                            $naration=$resdata[$i]['narration'];
                                $tabledata.="<tr><td>$date</td><td>$tratype</td>
                                    <td>$amt</td><td>$naration</td>               
                                  </tr>";
                            
                            $dbdate=$dbdate.','.$resdata[$i]['date'];
                            $dbtratype=$dbtratype.','.$resdata[$i]['txnType'];
                            $dbamt=$dbamt.','.$resdata[$i]['amount'];
                            $dbnaration=$dbnaration.','.$resdata[$i]['narration'];
                            
                        }
                        $table='<div class="table-responsive"><div class="boxscroll">
                				                            <table class="table table-hover table-hover-border">
                				                                <thead>
                				                                <tr>
                				                                    <th>Date</th>
                				                                    <th>TxnType</th>
                				                                    <th>Amount</th>
                				                                    <th>Narration</th>
                				                                </tr>
                				                                </thead>
                				                                <tbody>
                				                                 '.$tabledata.'
                				                                 </tbody>
                				                            </table>
                				                            </div>
                				                        </div>';
        		
            		$this->session->set_flashdata('print',$table);
            		
                    $this->db_model->insert_update('aeps_transaction_fch',array('msdate'=>$dbdate,'mstratype'=>$dbtratype,'msamount'=>$dbamt,'msnaration'=>$dbnaration,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>'0','status'=>$array['result']['transactionStatus'],'transactionType'=>$array['result']['transactionType'],'transaction_ref_id'=>$array['result']['fpTransactionId'],'utr'=>$array['result']['bankRRN'],'apiclid'=>$this->session->userdata('cus_id'),'through'=>'WEB'));
                    $inserId = $this->db->insert_id();
                    $this->session->set_flashdata('printid','https://easypayall.in/Retailer/ministmtaeps_print/'.$inserId);
                    
                // $this->session->set_flashdata('success',$table);
                }
                else{
                    $this->session->set_flashdata('error',$array['message']);
                }
                
        	   }
        	    else{
                    $this->session->set_flashdata('error',$array['message']);
                }
                redirect('retailer/aeps_transaction');
    	   }
    	   else{
    	       $this->session->set_flashdata('error','Please fill the mandatory filled.');
    	   }
    	}
	   else if(isset($_POST['adharpayment'])){
	   $this->form_validation->set_rules('txtPidData','txtPidData','required');
		$this->form_validation->set_rules('apadhaarNumber','apadhaarNumber','required');
		$this->form_validation->set_rules('apnationalBankIdenticationNumber','apnationalBankIdenticationNumber','required');
		$this->form_validation->set_rules('apmobileNumber','apmobileNumber','required');
		if($this->form_validation->run() == TRUE)
		{   
	     $request = "";
	        $msg="8630450571-89938-143-92.204.134.236";  
    	    $param['msg'] = $msg;
    	    $param['txtPidData'] = $_POST['txtPidData'];
    		$param['adhaarNumber'] = $_POST['apadhaarNumber'];
    		$param['nationalBankIdenticationNumber'] = $_POST['apnationalBankIdenticationNumber'];
    		$param['mobileNumber'] = $_POST['apmobileNumber'];
    		$param['transactionAmount'] = $_POST['aptransactionAmount'];
    		$param['type'] = 'aadharpay';
    		
    		$cus_id=$this->session->userdata('cus_id');
    		    $param['cus_id'] = $cus_id;
    		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$cus_id));
                $param['cus_name']=$data['profile'][0]->cus_name;
                $param['cus_mobile']=$this->encryption_model->decode($data['profile'][0]->cus_mobile);
                $param['merchantLoginId']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
                $param['merchantLoginPin']=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
    		
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
                
            $CURLOPT_URL='https://tiktikpay.in/index.php/api_partner/aepsapi';
            
            
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
            if($array['status'])
            {
        if($array['data']['transactionStatus']=='successful'){
            $inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('aadhar_number'=>$_POST['apadhaarNumber'],'aeps_bank_id'=>$_POST['apnationalBankIdenticationNumber'],'amount'=>$array['data']['transactionAmount'],'status'=>$array['data']['transactionStatus'],'transactionType'=>$array['data']['transactionType'],'transaction_ref_id'=>$array['data']['fpTransactionId'],'utr'=>$array['data']['bankRRN'],'apiclid'=>$this->session->userdata('cus_id'),'through'=>'WEB'));
            $inserId = $this->db->insert_id();
            
            $amount = $array['data']['transactionAmount'];
            $this->session->set_flashdata('print',"$amount Rs Successfullly Pay By Aadhar");
            $this->session->set_flashdata('printid','https://easypayall.in/Retailer/cashwithdrawalaeps_print/'.$inserId);

                $cus_id=$this->session->userdata('cus_id');
                $amount = $array['data']['transactionAmount'];
                
                $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
                $txntype = 'Aadhar Pay';
                $total = $cus_aeps_bal + $amount;
        		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                
                $this->creditAdharPayCommission($cus_id,$amount,$inserId);
            }
            else{
                $this->session->set_flashdata('error',$array['message']);
            }
            
            
    	   
    	   }
    	   else{
    	     $this->session->set_flashdata('error',$array['message']);
    	   }
    	    redirect('retailer/aeps_transaction');
    	    }
    	 else{
	       $this->session->set_flashdata('error','Please fill the mandatory filled.');
    	   }
    	}
       else{
    	   $this->session->set_flashdata('error','Please Select A Service');
       }
	    redirect('retailer/aeps_transaction');
	
	}
	
	
	/*
    public function trnx(){
        
        $amt = '1';
        $response="E06016~Abc589654135~7699999821~1448~NA~SUCCESS~62154565452~Description~Sandy~206541012~ State Bank of India ~NA~NA~NA";
        //MessageCode~ Request id~Mobile No~ BeneficiaryCode~NA~ Status ~IMPS Ref No~Description ~Beneficiary Name~Transaction ID~Bank name~Na~Na~Na~Na
        //Array ( [0] => E06016 [1] => Abc589654135 [2] => 7699999821 [3] => 1448 [4] => NA [5] => SUCCESS [6] => 62154565452 [7] => Description [8] => Sandy [9] => 206541012 [10] => State Bank of India [11] => NA [12] => NA [13] => NA )
        $resArr = explode('~' , $response);
        $request_id = $resArr[1];
        $mobile_no = $resArr[2];
        $bene_code = $resArr[3];
        $status = $resArr[5];
        $imps_ref_no = $resArr[6];
        $desc = $resArr[7];
        $bene_name = $resArr[8];
        $trans_id = $resArr[9];
        $bank_name = $resArr[10];
        
        $cus_id = $this->session->userdata('cus_id');
        $data = $this->db->query("SELECT * FROM `dmt_commission_slab` as d,customers as c where d.dmt_comm_id = c.dmt_comm_id and cus_id = '".$cus_id."' ")->result_array();
        $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
        $txn_clbal = $trnx_data[0]['txn_clbal'];
        $package_amount = $data[0]['amount'];
        $debit_amount = $package_amount + $amt;
        $new_txn_clbal = $txn_clbal - $debit_amount;
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d H:i:s');
		
		    $dmt_tnx_data = array(
                'request_id' => $request_id,
                'mobile_no' => $mobile_no,
                'bene_code' => $bene_code,
                'status' => $status,
                'imps_ref_no' => $imps_ref_no,
                'description' => $desc,
                'bene_name' => $bene_name,
                'trans_id' => $trans_id,
                'bank_name' => $bank_name,
                'amount' => $amt,
                'charge' =>  $package_amount,
                'cus_id' => $cus_id
            );
            $dmt=$this->db_model->insert_update('dmt_trnx',$dmt_tnx_data);
            $insert_id = $this->db->insert_id();
            
        if($insert_id){
            $insert_data = array(
                'txn_agentid' => $cus_id,
                'dmt_txn_recrefid' => $insert_id,
                'txn_opbal' => $txn_clbal,
                'txn_crdt' => '0',
                'txn_dbdt' => $debit_amount,
                'txn_clbal' => $new_txn_clbal,
                'txn_checktime' => '',
                'txn_type' => 'DMT',
                'txn_fromto' => '0',
                'txn_time' => $time,
                'txn_date' => $date,
                'txn_optional' => $imps_ref_no,
                'txn_ip' => $ip,
                'txn_comment' => $desc,
                'transaction_id' => $trans_id,
                'transaction_ref' => ''
            );
            $res=$this->db_model->insert_update('exr_trnx',$insert_data);    
        }
    }*/
    
    
    public function receipt($dmt_trnx_id){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{    
    		$data['dmt_txn'] = $this->db_model->getAlldata("select * from dmt_trnx where dmt_trnx_id='".$dmt_trnx_id."'");
            $this->load->view('retailer/receipt',$data);
        }    
    }
    
    
    
    public function aeps_report(){
        
  	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{
            
			$cus_id = $this->session->userdata('cus_id');
		    $data['aepsReport'] = $this->db_model->getAlldata("SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' and at.transactionType!='M' ORDER BY aeps_id DESC");
            $this->load->view('retailer/aeps_reports',$data);
        }
  	}
  	
  	public function search_aeps_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('cus_id');
		    $sql = "SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' and at.transactionType = 'CW'  and " ;
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
    		
		    $sql .= " at.aeps_id != '' ORDER BY at.aeps_id DESC";
		    $data['aepsReport'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('retailer/aeps_reports',$data);
		}
	}
	
  	public function pan_report(){
        
  	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{
			$cus_id = $this->session->userdata('cus_id');
			$vl='edigitalvillage'.$cus_id;
		    $data['coupons'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,coupon_price as cp where cb.type = cp.coupon_price_id  and cb.vle_id = '".$vl."'  ORDER BY coupon_buy_id DESC");
            $this->load->view('retailer/pan_reports',$data);
        }
  	}
  	
  	public function search_pan_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    $cus_id = $this->session->userdata('cus_id');
			$vl='edigital'.$cus_id;  
		    $sql = "SELECT * FROM coupon_buy as cb,coupon_price as cp where cb.type = cp.coupon_price_id  and cb.vle_id = '".$vl."'  and " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(cb.date) >= '".$from.' 00:00:00'."' and DATE(cb.date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(cb.date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(cb.date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " cb.coupon_buy_id != '' ORDER BY coupon_buy_id DESC";
		    $data['coupons'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('retailer/pan_reports',$data);
		}
	}
	
  	public function pan_cardreceipt($coupon_buy_id){
        
        if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{    
    		$data['dmt_txn'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,coupon_price as cp where cb.type = cp.coupon_price_id  and cb.coupon_buy_id = '".$coupon_buy_id."'  ORDER BY coupon_buy_id DESC");
            $this->load->view('retailer/pan_cardreceipt',$data);
        }    
    }
    
  	public function adhar_pay_report(){
        
  	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{
            
			$cus_id = $this->session->userdata('cus_id');
		    $data['adhar_pay_report'] = $this->db_model->getAlldata("SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' and at.transactionType='M' ORDER BY aeps_id DESC");
            $this->load->view('retailer/adhar_pay_reports',$data);
        }
  	}
  	
  	public function search_adhar_pay_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('cus_id');
		    $sql = "SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' and at.transactionType = 'M'  and " ;
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
    		
		    $sql .= " at.aeps_id != '' ORDER BY at.aeps_id DESC";
		    $data['adhar_pay_report'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('retailer/adhar_pay_reports',$data);
		}
	}
	
  	public function micro_atm_report(){
        
  	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
        }else{
            
			$cus_id = $this->session->userdata('cus_id');
		    $data['aepsReport'] = $this->db_model->getAlldata("SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' ORDER BY aeps_id DESC");
            $this->load->view('retailer/micro_atm_reports',$data);
        }
  	}
  	
    public function search_micro_atm_report()
	{
	    if($this->session->userdata('isLoginRetailer') == FALSE){
            redirect('users_login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('cus_id');
		    $sql = "SELECT * FROM micro_atm as ma , customers as c where ma.cus_id = c.cus_id and ma.cus_id = '".$cus_id."' and  " ;
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
    		
		    $sql .= " ma.ma_id != '' ORDER BY ma_id DESC ";
		    $data['microatm'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('retailer/micro_atm_reports',$data);
		}
	}
  	
  	
    public function dmtLogOut(){
        $this->session->unset_userdata('senderMobile');
        redirect('retailer/dashboard');
        
    }
    
    
    public function verify_bank(){
        
        $bankcode = $this->input->post('bank_code');//'SBIN';
        $mobile = $this->input->post('mob_no'); //'7030512346';
        $adhar = $this->input->post('aadhar'); //'558183301991';
        $pan = $this->input->post('pan'); //'COQPP7375K';
        $bank_acct = $this->input->post('bank_acct'); //'33220731560';
        
        $token = '9e37f87e69694e2886';
        $id = rand(1000000000,9999999999);
        
        
        //$data = "E06031~E1hgYt898557843789~Abc589654135~7699999821~4361256325156~SBIN~526489 754125~ CDER546859~NA~NA";
        //$data ="E06031~$token~$id~7030512346~777705123467~$bankcode~631106684669~EOPPP2051K~NA~NA";
         $data = "msg=E06031~$token~$id~$mobile~$bank_acct~$bankcode~558183301991~COQPP7375K~NA~NA";
        //$data = "msg=E06031~$token~$id~7030512346~777705123467~$bankcode~631106684669~EOPPP2051K~NA~NA";
        //$url= 'https://ezymoney.myezypay.in/RemitMoney/mtransfer';
        
        $url= $this->base_url;
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
        $server_output;
        if($http_code == "200"){
            $dataArr = explode('~',$server_output);
            if($dataArr['5'] == 'SUCCESS'){
                echo $name = $dataArr['8'];
                echo $bankName = $dataArr['10'];
            }
        }else{
            $this->session->set_flashdata('error','Unable To get Response');
            redirect('Retailer/addbeneficary');
        }
    }
    public function cashwithdrawalaeps_print($aeps_id)
    {
        // $data['data']=$this->db_model->getwhere('aeps_transaction_fch',array('aeps_id'=>$aeps_id));
        $data['data']=$this->db_model->getAlldata("SELECT at.transactionType,at.aadhar_number,at.amount,at.aeps_date_time,at.utr,c.cus_name,c.cus_mobile,ab.bankName FROM aeps_transaction_fch as at,aeps_bank as ab,customers as c where at.aeps_bank_id = ab.iinno and at.apiclid=c.cus_id and at.aeps_id = ".$aeps_id);
        $this->load->view('retailer/cashwithdrawalaeps_print',$data);
    }
    public function ministmtaeps_print($aeps_id)
    {
        // echo $this->encryption_model->decode('421e44e2e831de39007fc91a99439f0c');echo"\n";
        // echo $this->encryption_model->decode('eea039dbb75b45b857cec7668353f128');exit;
        $data['data']=$this->db_model->getAlldata("SELECT at.msdate,at.mstratype,at.msamount,at.msnaration,at.transactionType,at.aadhar_number,at.amount,at.aeps_date_time,at.utr,c.cus_name,c.cus_mobile,ab.bankName FROM aeps_transaction_fch as at,aeps_bank as ab,customers as c where at.aeps_bank_id = ab.iinno and at.apiclid=c.cus_id and at.aeps_id = ".$aeps_id);
        $this->load->view('retailer/ministmtaeps_print',$data);
    }
    public function service($service){
        
        $data = $this->db_model->getAllData("select * from service where name='".$service."' ");
        return $data[0]->status;/*
        $msg['info'] = "Dmt Is Down for today";
        $this->load->view('retailer/service',$msg);*/
    }
    public function creditAdharPayCommission_get($cus_id,$amount,$aeps_id){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_adharpay WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
                if($commission_scheme_package){
                    if($cus_type == "retailer"){
                        
                        $retailer_comm = $commission_scheme_package[0]->retailer_comm; 
                        $comm_type = $commission_scheme_package[0]->type;
                        if($retailer_comm){
                            
                            if($comm_type == "percent"){
                                $commission_amount = round((($retailer_comm / 100) * $amount),3);
                            }else{
                                $commission_amount = $retailer_comm;
                            }
                            $last_balance = $this->Aeps_model->aepsBalance($cus_id);
                            $this->db_model->insert_update('aeps_transaction_fch', array('retailer_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
                            
        					$txntype = 'Retailer Aadhar Pay Charge';
                            if($last_balance){
                                $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                            }else{
                                $cus_aeps_bal = '0';
                            }
                            
                            $total = $cus_aeps_bal - $commission_amount;
                            
                    		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>$commission_amount,'aeps_txn_crdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                            
    							
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
                                    $last_balance = $this->Aeps_model->aepsBalance($cus_id);
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
                                        $last_balance = $this->Aeps_model->aepsBalance($cus_reffer);
                                        
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
                                       $last_balance = $this->Aeps_model->aepsBalance($master_cus_reffer);
                                        
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
    
    public function creditAdharPayCommission($cus_id,$amount,$aeps_id){
        
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
                                
                        $retailer_comm = $commission_scheme_package[0]->retailer_comm; 
                        $comm_type = $commission_scheme_package[0]->type;
                        if($retailer_comm){
                            if($comm_type == "percent"){
                                $commission_amount = round((($retailer_comm / 100) * $amount),3);
                            }else{
                                $commission_amount = $retailer_comm;
                            }
                            $last_balance = $this->Aeps_model->aepsBalance($cus_id);
                            $this->db_model->insert_update('aeps_transaction_fch', array('retailer_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
                            
        					$txntype = 'Retailer Aadhar Pay Charge';
                            if($last_balance){
                                $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                            }else{
                                $cus_aeps_bal = '0';
                            }
                            
                            $total = $cus_aeps_bal - $commission_amount;
                            
                    		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>$commission_amount,'aeps_txn_crdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                            
    							
                        }

                    }
                }
            }else{
                return false;
            }
        }
    }
    
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
        						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$retailer_charge, 'txn_clbal'=>$last_balance[0]->txn_clbal-$retailer_charge, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
        					}
                        }
                        
                        $getParentDistributor = $commission_scheme_id[0]->cus_reffer;
                        $distributor_comm = $commission_scheme_package[0]->distributor_comm;
                        if($distributor_comm){
                            $last_balance = $this->db_model->get_trnx($getParentDistributor);
    						if($last_balance)
    						{
        						$txntype = "Distributor Pan Card Commission";
        						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentDistributor,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$distributor_comm, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $distributor_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
    						}else{
    						    $txntype = "Distributor Pan Card Commission";
        						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentDistributor,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$distributor_comm, 'txn_dbdt'=>'0', 'txn_clbal'=> $distributor_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
                						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentMaster,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>$master_comm, 'txn_dbdt'=>'0', 'txn_clbal'=>$last_balance[0]->txn_clbal + $master_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
            						}else{
            						    $txntype = "Master Pan Card Commission";
                						$this->db_model->insert_update('exr_trnx', array('coupon_buy_id'=>$coupon_buy_id,'txn_agentid'=>$getParentMaster,'txn_date'	=>	date('Y-m-d H:i:s'),'txn_opbal'=>'0', 'txn_crdt'=>$master_comm, 'txn_dbdt'=>'0', 'txn_clbal'=> $master_comm, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
    
    
    public function testapi(){
        
        $api_user_id = '88';
        $api_device = 'mantra';//$dpId;
        $api_amount = '600';//$response['data']['transactionAmount'];
        $api_status = 'successful';//$response['data']['transactionStatus'];
        $api_transactionType = 'CW';//$response['data']['transactionType'];
        $api_transaction_ref_id = '1234';//$response['data']['fpTransactionId'];
        $api_utr = '3724747874';//$response['data']['bankRRN'];
        $api_through = 'WEB';
        $url = "http://edigitalvillage.net/B2b_Aeps/cashWithdrawal?api_user_id=$api_user_id&device=$api_device&aadhar_number=$adhaarNumber&aeps_bank_id=$nationalBankIdenticationNumber&amount=$api_amount&status=$api_status&transactionType=$api_transactionType&transaction_ref_id=$api_transaction_ref_id&utr=$api_utr&through=$api_through";
        $res = file_get_contents($url);
        echo $res;
    }
    
    public function check_operator()
	{
	    $mobile = $this->input->post('mobile');
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['tel'] = $mobile;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?".$request;  
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
        print_r($content);
        // return $content;
        
	}
	
	public function ElectricityInfo()
	{
	    $mobile = $this->input->post('prepaid_mobile');
        $opcodeid = $this->input->post('opcodeid'); 
        
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$opcodeid));
        $opcodenew = $oper[0]->opcodenew;
        $operator = $oper[0]->qr_opcode;
	    $table = "";
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
        echo $url;
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
        // print_r($data2);
        // exit;
        
        $records = $data2->records;
        // print_r($records); exit;
        $customerName = $records[0]->CustomerName;
        $Billamount = $records[0]->Billamount;
        $Billdate = $records[0]->Billdate;
        $Duedate = $records[0]->Duedate;
        $records1= $customerName."#".$Billamount."#".$Billdate."#".$Duedate;
        
        $table.="
        <tr><td style='width:250px'><b>Customer Name</b></td><td>$customerName</td></tr>
        <tr><td style='width:250px'><b>Bill Amount </b></td><td id='amt'>$Billamount</td></tr>
        <tr><td style='width:250px'><b>Bill Date</b></td><td class='text-info'><b>$Billdate</b></td></tr>
        <tr><td style='width:250px'><b>Due Date</b></td><td class='text-danger'><b>$Duedate</b></td></tr>
      ";
     $out='<div class="modal fade" id="modaldemo3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6><img style="width: 45px;height: 40px;border-radius: 50%;" src="'.base_url().'assets/'.$oper[0]->operator_image.'" alt="'.$oper[0]->operatorname.'">&nbsp;&nbsp;<b>'.$oper[0]->operatorname.'</b> </h6>
						<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
					    <div class="table-responsive"><div class="boxscroll">
				                            <table class="table table-hover table-hover-border">
				                                <tbody>
				                                 '.$table.'
				                                 </tbody>
				                            </table>
				                            </div>
				                        </div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-success" data-dismiss="modal" type="button">Done</button>
					</div>
				</div>
			</div>
            </div>';
         echo $customerName.",".$Billamount.",".$out.",".$records1;
	}
	
	public function GasInfo()
	{
	    $mobile = $this->input->post('prepaid_mobile');
        $opcodeid = $this->input->post('opcodeid'); 
        
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$opcodeid));
        $opcodenew = $oper[0]->opcodenew;
        $operator = $oper[0]->qr_opcode;
	    $table = "";
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
        // print_r($data2);
        
        $records = $data2->records;
        // print_r($records); exit;
        $customerName = $records[0]->customerName;
        $Billamount = $records[0]->Billamount;
        $Billdate = $records[0]->DistributorName;
        $records1= $customerName."#".$Billamount."#".$Billdate;
        
        $table.="
        <tr><td style='width:250px'><b>Customer Name</b></td><td>$customerName</td></tr>
        <tr><td style='width:250px'><b>Bill Amount </b></td><td id='amt'>$Billamount</td></tr>
        <tr><td style='width:250px'><b>Distributor Name</b></td><td class='text-info'><b>$Billdate</b></td></tr>
        
      ";
     $out='<div class="modal fade" id="modaldemo3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6><img style="width: 45px;height: 40px;border-radius: 50%;" src="'.base_url().'assets/'.$oper[0]->operator_image.'" alt="'.$oper[0]->operatorname.'">&nbsp;&nbsp;<b>'.$oper[0]->operatorname.'</b> </h6>
						<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
					    <div class="table-responsive"><div class="boxscroll">
				                            <table class="table table-hover table-hover-border">
				                                <tbody>
				                                 '.$table.'
				                                 </tbody>
				                            </table>
				                            </div>
				                        </div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-success" data-dismiss="modal" type="button">Done</button>
					</div>
				</div>
			</div>
            </div>';
         echo $customerName.",".$Billamount.",".$out.",".$records1;
	}
	
	public function FastagInfo()
	{
	    $mobile = $this->input->post('prepaid_mobile');
        $opcodeid = $this->input->post('opcodeid'); 
        
        $oper = $this->db_model->getwhere('operator',array('opcodenew'=>$opcodeid));
        $opcodenew = $oper[0]->opcodenew;
        $operator = $oper[0]->qr_opcode;
	    $table = "";
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

        $url = "https://www.mplan.in/api/Fastag.php?".$request;                           
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
        // print_r($data2);
        
        $records = $data2->records;
        // print_r($records); exit;
        $customerName = $records[0]->CustomerName;
        $Billamount = $records[0]->Balance;
        $records1= $customerName."#".$Billamount;
        
        $table.="
        <tr><td style='width:250px'><b>Customer Name</b></td><td>$customerName</td></tr>
        <tr><td style='width:250px'><b>Bill Amount </b></td><td id='amt'>$Billamount</td></tr>
        
      ";
     $out='<div class="modal fade" id="modaldemo3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6><img style="width: 45px;height: 40px;border-radius: 50%;" src="'.base_url().'assets/'.$oper[0]->operator_image.'" alt="'.$oper[0]->operatorname.'">&nbsp;&nbsp;<b>'.$oper[0]->operatorname.'</b> </h6>
						<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
					    <div class="table-responsive"><div class="boxscroll">
				                            <table class="table table-hover table-hover-border">
				                                <tbody>
				                                 '.$table.'
				                                 </tbody>
				                            </table>
				                            </div>
				                        </div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-success" data-dismiss="modal" type="button">Done</button>
					</div>
				</div>
			</div>
            </div>';
         echo $customerName.",".$Billamount.",".$out.",".$records1;
	}
    
}	   
