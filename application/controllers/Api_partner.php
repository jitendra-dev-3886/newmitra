<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ini_set('display_errors', 0);
date_default_timezone_set('Asia/Kolkata');
class Api_partner extends CI_Controller

{ 
    
	public function __construct()

	{

		parent::__construct();
        $this->load->model(array('db_model','encryption_model','email_model','rec_model','msg_model','Aeps_model'));
        
	}

    
    public function index()

	{
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    
		    redirect('api_partner/dashboard');
		}

	}
	
	public function login()
    {
        
        if(isset($_POST['login']))
        {   
            $ip = $this->input->ip_address();
            $mobile = trim($this->input->post('mobile'));
  			$password = $this->input->post('password');
  			$location = $this->input->post('location');
  			$user_type = $this->input->post('user_type');
            $this->db_model->insert_update('inputdata',array('function_name'=>'api_partner login','inputs'=>$mobile.','.$password,'ip'=>$ip,'location'=>$location));
            $mob = $this->encryption_model->encode($mobile);
            $pass = $this->encryption_model->encode($password);
            $r = $this->db_model->login('customers',array('cus_mobile'=>$mob,'cus_pass'=>$pass,'cus_type'=>$user_type));
            if($r)
            {
                $cus_id=$r[0]->cus_id;
                $newdata1 = array(
                    'isLoginApi'=>'TRUE',
                    'api_id'  => $r[0]->cus_id,
                    'cus_name'  => $r[0]->cus_name,
                    'cus_type'  => $r[0]->cus_type,
                    'cus_email'  => $r[0]->cus_email,
                    'cus_mobile' => $mobile,
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                  );  
                $this->session->set_userdata($newdata1);
                $message='Login Details- '.$mobile.' and '.$password;
                $data=array('log_user'=>$cus_id,'log_type'=>'api_partner','log_ip'=>$ip,'medium'=>'Web','message'=>$message);
                $this->db_model->insert_update('exr_log',$data);
                $updata=array('cus_status'=>'1');
                $this->db_model->insert_update('customers',$updata,$cus_id);
                
                $otp = rand(100000,999999); 
                $email = $r[0]->cus_email;
        		$name = strtoupper($r[0]->cus_name);
        		$msg= $name.", Your One Time OTP Is ".$otp;
        		$sub="Login OTP From Recharge";
        		$to=$email;
        		$this->email_model->send_email($to,$msg,$sub);
         	    $this->session->set_userdata('otp', $otp);
         	    $this->session->set_flashdata('success', 'Sent OTP');
                redirect('api_partner/otp');
            }
            else{
                $this->session->set_flashdata('error', 'Invalid Login Details');
                redirect('api_partner/login');
            }
        }
        else{
            $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
            $this->load->view('api_partner/index',$data);
        }
        

	}
	
	public function otp()
    {
        if(isset($_POST['checkotp']))
        {
             $enteredotp = $this->input->post('otp');
            $otp = $this->session->userdata('otp');
            if($enteredotp == $otp){
                $cus_id = $this->session->userdata('api_id');
                $updata=array('web_login_status'=>'loggedin');
                $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));
                $this->session->set_userdata('isLoginApi', TRUE);
                redirect('api_partner/dashboard');
            }
            else{
                $this->session->set_flashdata('error', 'Entered OTP is Invalid');
                $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
                $this->load->view('api_partner/otp',$data);
            }
        }
        else{
            $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
            $this->load->view('api_partner/otp',$data);
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
		
		redirect('api_partner/otp');
        
    }
	
	public function dashboard()

	{
	    
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		      //  $this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('api_id')."' ORDER BY txn_id desc limit 1");
    	   //	    echo $this->db->last_query();exit;
    	    $data['data'] = [
    	   	    'success' => $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='SUCCESS' and apiclid='".$this->session->userdata('api_id')."' and DATE(reqdate)='".date('Y-m-d')."'"),
    	   	    'failed' => $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where status='FAILED' and apiclid='".$this->session->userdata('api_id')."' and DATE(reqdate)='".date('Y-m-d')."'"),
    	   	    'todayearning' => $this->db_model->getAlldata('select sum(exr.txn_crdt) as amt from exr_trnx as exr join exr_rechrgreqexr_rechrgreq_fch as err on exr.txn_recrefid = err.recid where exr.txn_type IN ("Retailer Commission","discount") and err.status="SUCCESS" and DATE(exr.txn_date)="'.date('Y-m-d').'" and exr.txn_agentid="'.$this->session->userdata('api_id').'" group by err.recid'),
    	   	    'aepsbalance' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_clbal),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Cash%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('api_id')."'"),
    	   	    'balance' => $this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('api_id')."' ORDER BY txn_id desc limit 1"),
    	   	    'mobile' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('mobile','postpaid') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'datacard' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('datacard') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'dth' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('dth') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'landline' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('landline') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'electricity' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('ELECTRICITY') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'insurance' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('INSURANCE') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'gas' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('GAS') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'pan' => $this->db_model->getAlldata("select sum(exr.qty) as amt from coupon_buy as exr where exr.status='SUCCESS' and exr.pan_cus_id='".$this->session->userdata('api_id')."'"),
    	   	    'dmt' => $this->db_model->getAlldata("select sum(exr.amount) as amt from dmt_trnx as exr where exr.status='SUCCESS' and DATE(exr.dmt_trnx_date)='".date('Y-m-d')."' and exr.cus_id='".$this->session->userdata('api_id')."'"),
    	   	    'fastag' => $this->db_model->getAlldata("select sum(exr.amount) as amt from exr_rechrgreqexr_rechrgreq_fch as exr join operator as op on exr.operator = op.opcodenew where op.opsertype IN ('fastag') and exr.status='SUCCESS' and DATE(exr.reqdate)='".date('Y-m-d')."' and exr.apiclid='".$this->session->userdata('api_id')."'"),
    	   	    'aeps' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Cash%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('api_id')."'"),
    	   	    'microatm' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Micro Atm%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('api_id')."'"),
    	   	    'adhar' => $this->db_model->getAlldata("SELECT IFNULL(sum(aeps_txn_crdt),0) as amt FROM aeps_exr_trnx WHERE aeps_txn_type LIKE '%Aadhar Pay%' AND date(aeps_txn_date) = CURDATE() and aeps_txn_agentid='".$this->session->userdata('api_id')."'"),
    	   	   ];
            $this->load->view('api_partner/dashboard',$data);
		}    

	}
	
	public function profile()

	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
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
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('api_id')));
    			$this->session->set_flashdata('message','Profile Updated Successfully...!!!');
		    }
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
		    $data['referal']=$this->db_model->getwhere('customers',array('cus_id'=>$data['profile'][0]->cus_reffer));
		    $data['state']=$this->db_model->getwhere('state');
		    $data['city']=$this->db_model->getwhere('city',array('state_id'=>$data['profile'][0]->cus_state));
            $this->load->view('api_partner/profile',$data);
		}
	}
	
	function check_password() 
	{
	    
	    $old_password =$this->input->post('old_password');
	    $data=$this->db_model->getwhere('customers',array('cus_pass'=>$this->encryption_model->encode($old_password),'cus_id'=>$this->session->userdata('api_id')));
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
	    $data=$this->db_model->getwhere('customers',array('cus_pin'=>$this->encryption_model->encode($old_pin),'cus_id'=>$this->session->userdata('api_id')));
	    if($data){
	        echo TRUE;
	    }
        else{
            echo FALSE;
        }
        
    }
	public function password_and_pin()

	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}
		else{
		    if(isset($_POST['update_password']))
		    {
		                $record=array(
    				            'cus_pass'=>$this->encryption_model->encode($_POST['new_password'])
    				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('api_id')));
    			$this->session->set_flashdata('message','Password Updated Successfully...!!!');
		    }
		    
		    if(isset($_POST['update_pin']))
		    {
		                $record=array(
    				            'cus_pin'=>$this->encryption_model->encode($_POST['new_pin'])
    				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('api_id')));
    			$this->session->set_flashdata('message','Pin Updated Successfully...!!!');
		    }
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
		    $this->load->view('api_partner/password_and_pin');
		}
	}
	
	public function mobile_recharge()

	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/mobile_recharge',$data);
		}
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
		$param['apikey'] = '216ae79f054a7034d9950c654efc6da0';
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
		$param['apikey'] = '216ae79f054a7034d9950c654efc6da0';
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
		$redirect = $this->input->post('redirect');
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
		
		$this->form_validation->set_rules('operator','Mobile','required');
		$this->form_validation->set_rules('amount','Amount','required');
		$this->form_validation->set_rules('type','type','required');
		if($this->form_validation->run() == TRUE)
		{
            $billing = '';$process = '';$medium = 'web';
            
			$ip = $this->input->ip_address();
			$cus_id = $this->session->userdata('api_id');
			$cus_type = $this->session->userdata('cus_type');
			$qr_opcode =$this->input->post('operator');
			
			$complete = $this->rec_model->recharge_ot(array('cus_id'=>$cus_id,'amount'=>$this->input->post('amount'),'number'=>$this->input->post('number'), 'billing' => $billing, 'process' => $process,'operator'=>$qr_opcode,'mode'=>$medium,'type'=>$cus_type));
				$msg = $complete['mess'];
				
				$this->session->set_flashdata('message',$msg);
				redirect("retailer/$redirect");		
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
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/data_card',$data);
		}
    }
	
	public function dth()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/dth',$data);
		}
    }
	
	public function landline()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/landline',$data);
		}
    }
	
	public function electricity()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/electricity',$data);
		}
    }
	
	public function insurance()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/insurance',$data);
            
		}
    }
	
	public function gas()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['operator']=$this->db_model->getwhere('operator');
            $this->load->view('api_partner/gas',$data);
		}
    }
	
	public function dmtuserlogin()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
            
        }else{    
            $status = $this->service('DMT');
            if($status == 'active'){
                
                if($this->session->userdata('senderMobile') !== NULL){
                    redirect('api_partner/getbeneficary');
                }else{
        	        $this->load->view('api_partner/dmt');
                }
            }else{
                
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
            }
        }    
    }
    
    
    
    public function dmtSender(){
        
        
        $api_mobile = '7517549899';//$this->input->post('reg_mobile');
        $api_pass = '36199';//$this->input->post('reg_pass');
        $api_user_id = '88';//$this->input->post('api_user_id');
        $api_ip = '123';//$this->input->post('reg_ip_address');
        $mobile = '7517549829';//$this->input->post('sender_mobile');
        $name = 'satmat Group';//$this->input->post('sender_name');
        
        $api_query = "select * from customers where cus_mobile = '".$this->encryption_model->encode($api_mobile)."' and cus_id = '".$api_user_id."' and cus_pass = '".$this->encryption_model->encode($api_pass)."'";
        $rec = $this->db_model->getAlldata($api_query);
        //print_r($rec);
        if($rec){
            
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
                $status = $this->service('DMT');
                if($status == 'active'){
                 
                    if(isset($_POST['register'])){
                        $mobile = $_POST['mobile'];
                        $name = $_POST['name'];
                        
                        $token = $this->token;
                        $id = rand(1000000000,9999999999);
                        $data = "msg=E06003~$token~$id~$mobile~$name~NA~NA";
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
                        
                        $http_code = $info['http_code'];
                        if($http_code == "200"){
                            $dataArr = explode('~',$server_output);
                            if($dataArr[5] == 'SUCCESS'){
                                $ref_no = $dataArr[4];
                                redirect('api_partner/verifyDmtOtp/'.$ref_no);
                            }else{
                                $this->session->set_flashdata('message',"Unable to process Request! Please Try Again Later...!");
                            }
                        }else{
                            $this->session->set_flashdata('message',"Unable To get Response");
                        }
                        redirect('api_partner/dmt');
                    }else if(isset($_POST['getbeneficiary'])){
                        
                        $mobile = $_POST['mobile'];
                        $this->session->set_userdata('senderMobile',$mobile);
                        
                        redirect('api_partner/getbeneficary');
                    }else{
                        redirect('api_partner/dmt');
                    }
                }else{
                    $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                    $this->load->view('api_partner/service',$msg);
                }  
            }else{
                echo 'Invalid IP Address';
            }    
        }else{
            return 'Invalid Api User Credentials';
        }    
    }
    
    
    public function verifyDmtOtp($ref_np){
        $data['VerifyReferenceNo'] = $ref_np;
        $this->load->view('api_partner/verifyDmtOtp',$data);
    }
    
    
	public function fastag()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $this->load->view('api_partner/fastag');
		}
    }
	
	public function aeps()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    
		    $status = $this->service('AEPS');
            if($status == 'active'){
            
    		    $data['aeps_state'] = $this->db_model->getwhere('aeps_state');
    		    $this->load->view('api_partner/aeps',$data);
            }else{
            
                $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
            } 
		}
    }
    public function aeps_transaction()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $status = $this->service('AEPS');
            if($status == 'active'){
            
    		    $data['aeps_bank'] = $this->db_model->getwhere('aeps_bank');
    		    $this->load->view('api_partner/aeps_transaction',$data);
            }else{
            
                $msg['info'] = "AEPS SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
            }   
		}
    }
	
	public function insuranceapi()
    {
        
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $status = $this->service('INSURANCES');
            if($status == 'active'){
		        $this->load->view('api_partner/micro_atm');
            }else{
                $msg['info'] = "INSURANCES SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
            }   
		}

        
    }
    public function payout()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $cus_id=$this->session->userdata('api_id');
		    $data['bankDetails'] = $this->Db_model->getAlldata("SELECT * FROM customers WHERE cus_id='$cus_id'");
            $this->load->view('api_partner/payout',$data);
		}
    }
    
    public function getPayoutCharge(){
        $amount = $_POST['amount'];
        $cus_id=$this->session->userdata('api_id');
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
        
        $cus_id=$this->session->userdata('api_id');
	    $bank_name = $_POST['bank_name'];
	    $account_number = $_POST['account_number'];
	    $ifsc_code = $_POST['ifsc_code'];
	    $account_holder_name = $_POST['account_holder_name'];
	    $amount = $_POST['amount'];
	    $charge = $_POST['charge'];
	    $moveto = $_POST['sel'];
	    
	    $this->form_validation->set_rules('bank_name','Bank name','required');
	    $this->form_validation->set_rules('account_number','Account number','required');
	    $this->form_validation->set_rules('ifsc_code','Ifsc code','required');
	    $this->form_validation->set_rules('account_holder_name','Account holder name','required');
	    $this->form_validation->set_rules('amount','Amount','required');
	    $this->form_validation->set_rules('charge','Charge','required');
	    
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
    					    
                            $this->session->set_flashdata('success','Move to  wallet Successfully');
                            redirect('api_partner/view_all_redeem_wallet_report');
    					}else{
    					    
                            $this->session->set_flashdata('error','Failed to Submit  Request');
                            redirect('api_partner/payout');
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
                		$date = DATE('Y-m-d h:i:s');
                		$total = $cus_aeps_bal - $txn_dbdt;
                		
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>'0','aeps_txn_dbdt'=>$txn_dbdt,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                		
                        
                        $this->session->set_flashdata('success','Redeem Request Submitted Successfully');
                        redirect('api_partner/view_all_redeem_request');
                    }else{
                        $this->session->set_flashdata('error','Failed to Submit Redeem Request');
                        redirect('api_partner/payout');
                    }
                }    
                        
	        }else{
	            $this->session->set_flashdata('error','Insufficient Balance');
                redirect('api_partner/payout');
            }
	    }else{
            $this->session->set_flashdata('error','Please Fill All Data In Requested Format ');
            redirect('api_partner/payout');
        }
        
    }
    
    public function view_all_redeem_request(){
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $cus_id = $this->session->userdata('api_id');
		    $data['requests'] = $this->db_model->getAlldata("SELECT * FROM payout_request WHERE cus_id='$cus_id' and DATE(request_date) = CURDATE() ORDER BY pay_req_id DESC");
		    $this->load->view('api_partner/view-all-payout-request',$data);
		}
    }
    
    
    public function search_redeem_request()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $cus_id = $this->session->userdata('api_id');
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    $cus_id = $this->session->userdata('api_id');
		    $sql = "SELECT * FROM payout_request WHERE cus_id='$cus_id' and " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(request_date) >= '".$from.' 00:00:00'."' and DATE(request_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(request_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(request_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " pay_req_id != '' ORDER BY pay_req_id DESC";
		    $data['requests'] = $this->db_model->getAlldata($sql);
		    $this->load->view('api_partner/view-all-payout-request',$data);
		}
	}
    
    public function view_all_redeem_wallet_report(){
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $cus_id = $this->session->userdata('api_id');
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join customers as c on e.txn_agentid = c.cus_id WHERE txn_type LIKE '%PAYOUT%' and DATE(e.txn_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.txn_date) <= '".date('Y-m-d').' 00:00:00'."' and txn_agentid = '".$cus_id."' order by e.txn_id DESC");
		    $this->load->view('api_partner/view_all_redeem_wallet_report',$data);
		}
    }
    
    public function search_redeem_wallet_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $cus_id = $this->session->userdata('api_id');
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
		    $this->load->view('api_partner/view_all_redeem_wallet_report',$data);
		}
	}
    
	
	public function pan_card()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
            
        }else{
            
            $status = $this->service('PANCARD');
            if($status == 'active'){
                
        	    $data['coupon'] = $this->db_model->getwhere('coupon_price');
        	    $data['couponhistory'] = $this->db_model->getwhere('coupon_buy',array('pan_cus_id'=>$this->session->userdata('api_id')));
        	    $data['cusdata'] = $this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
        	    $data['aeps_state'] = $this->db_model->getwhere('aeps_state');
        	    $this->load->view('api_partner/pan_card',$data);
            
        	}else{
        	    
                $msg['info'] = "PAN CARD SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
        	}
        }    	
    }
    
	
	public function fund_request()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $cus_id = $this->session->userdata('api_id');
		    $data['cusbank'] = $this->db_model->getwhere('bank_details',array('account_status'=>'1'));
		    $this->load->view('api_partner/fund_request',$data);
		}
    }
	
	public function send_fund_request()
	{
	    $this->form_validation->set_rules('amount','Amount','required');
		$this->form_validation->set_rules('method','Payment Method','required');
		if($this->form_validation->run() == FALSE)
		{
		    $err = validation_errors();
			$this->session->set_flashdata('message',"Please fill the mandatory filled $err");
			redirect('api_partner/fund_request');
		}
		else
		{
		    $cus_id = $this->session->userdata('api_id');
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
				'req_date'	=>	date('Y-m-d h:i:s')
			);
			
			$this->msg_model->approve_request1(array('data'=>$cusdt1[0],'message'=>"Dear Member, You have received an fund request of Rs.".$this->input->post('amount')." from ".$cusdt[0]->cus_name));
			$this->db_model->insert_update('fund_request',$data);
			$this->session->set_flashdata('message','Fund request submitted successfully');
			redirect('api_partner/fund_request');
		}
	}
	
	public function fund_transfer()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $this->load->view('api_partner/fund_transfer');
		}
    }
	
	public function recharge_report()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
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
	    
        	    $sql = "SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where apiclid='".$this->session->userdata('api_id')."' and ";
        		
        		if($operator_type){
        		    $sql .= "o.opsertype ='".$operator_type."' and ";
        		    
        		}		
        		if($operator){
        		    $sql .= "e.operator ='".$operator."' and ";
        		    
        		}
                if($mobile_number){
                    $sql .= "e.mobileno ='".$mobile_number."' and ";
                    
                }
                if($rec_id){
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
                if($status){
                    $sql .= "e.status ='".$status."' and ";
                    
                }
                $sql .= " e.recid != '' ORDER BY e.recid DESC";
                $data['recharge']=$this->db_model->getAlldata("$sql");
            }
            else
            {
		        $data['recharge']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where apiclid='".$this->session->userdata('api_id')."' and DATE(e.reqdate) = CURDATE() ORDER BY e.recid DESC");
            }
            
            $data['operator'] = $this->db_model->getwhere('operator');
		    $this->load->view('api_partner/recharge_report',$data);
		}
    }
	
	public function fund_report()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['fund']=$this->db_model->getAlldata("select * from exr_trnx as et join virtual_balance as vb on et.txn_id = vb.txn_id where et.txn_agentid='".$this->session->userdata('api_id')."' and et.txn_type='Direct Credit' Group by et.txn_id order by et.txn_id desc");
		    $this->load->view('api_partner/fund_report',$data);
		}
    }
    
    public function search_fund_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
		    $sql = "select * from exr_trnx as et join virtual_balance as vb on et.txn_id = vb.txn_id where et.txn_agentid='".$this->session->userdata('api_id')."' and et.txn_type='Direct Credit'   and " ;
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
		    $this->load->view('api_partner/fund_report',$data);
		}
	}
	
	
	
	public function ledger_report()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    if(isset($_POST['search_transaction_report']))
            {
        	    $rec_id = $_POST['rec_id'];
        	    $frm = $_POST['from_dt'];
    	        $to = $_POST['to_dt'];
    	        
        	    $from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : '');
    		    $to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		    
        	    $sql = "SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_agentid='".$this->session->userdata('api_id')."' and ";
        		
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
		        $data['fund']=$this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_agentid='".$this->session->userdata('api_id')."' and DATE(e.txn_date) = CURDATE() order by e.txn_id desc");
		    }
		     $this->load->view('api_partner/ledger_report',$data);
		}
    }
	
	public function commission_report()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    if(isset($_POST['search_commission_report']))
            {
                $operator_type = $_POST['operator_type'];
        	    $operator = $_POST['operator'];
        	    $date = $_POST['date'];
	    
        	    $sql = "SELECT sum(exr.amount) as amount, sum(exr.retailer) as com, op.operatorname FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('api_id')."' and ";
        		
        		if($operator_type){ $sql .= "op.opsertype ='".$operator_type."' and ";}		
        		if($operator){$sql .= "exr.operator ='".$operator."' and ";}
                /*if($from !='' && $to !=''){$sql .="DATE(exr.reqdate) >= '".$from.' 00:00:00'."' and DATE(exr.reqdate) <= '".$to.' 00:00:00'."' and ";}*/
        		if($date !=''){$sql .="DATE(exr.reqdate) ='".$date."' and ";}
                $sql .= " exr.recid != '' ORDER BY exr.recid DESC";
                
                $data['fund']=$this->db_model->getAlldata("$sql");
            }
            else
            {
		        $data['fund']=$this->db_model->getAlldata("SELECT sum(exr.amount) as amount, sum(exr.retailer) as com, op.operatorname FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('api_id')."'");
		    }
		     $data['operator'] = $this->db_model->getwhere('operator');
		     $this->load->view('api_partner/commission_report',$data);
		}
    }
	
	public function find_report()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    if(isset($_POST['search_find_report']))
            {
                $no = $_POST['mobile'];
	            $data['fund']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('api_id')."' and exr.mobileno='".$no."' order by exr.recid DESC ");
        		
            }
            else
            {
		        $data['fund']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as exr join operator as op on exr.operator = op.opcodenew where exr.apiclid='".$this->session->userdata('api_id')."' order by exr.recid DESC ");
		    }
		    
		    $this->load->view('api_partner/find_report',$data);
		}
    }
	
	public function package_report()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		     $r = $this->db_model->getAlldata("select * from customers where cus_id='".$this->session->userdata('api_id')."'");
		     $data['fund']=$this->db_model->getAlldata("SELECT op.operatorname,op.opsertype,expc.packcomm_id,expc.packagecom_tem,expc.packagecom_amttype,expc.packcomm_comm,expc.packagecom_type FROM `exr_packagecomm` as expc join `exr_package` as exp on expc.packcomm_name = exp.package_id join operator as op on expc.packcomm_opcode = op.opcodenew where exp.package_membertype='".$this->session->userdata('cus_type')."' and exp.package_id='".$r[0]->package_id."' order by op.opsertype");
		     $this->load->view('api_partner/package_report',$data);
		}
    }
    
    
    /*public function getRechargePackage(){
        
        $cus_id = $this->session->userdata('api_id');
        $res = $this->db_model->getAlldata("select * from customers where cus_id = '".$cus_id."' ");
        $pack_id = $res['0']->scheme_id;
        if($pack_id){
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
        }else{
            echo "NF";
        }	
    }*/
    
    
    public function getRechargePackage(){
        
        $cus_id = $this->session->userdata('api_id');
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
        	  	    $tab.='<td>'.$v->api_comm.'</td>';
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
    
    
    
    public function dmt_report(){
        
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{
            
			$id = $this->session->userdata('api_id');      
		    $data['dmt_txn'] = $this->db_model->getAlldata("select * from dmt_trnx as d,customers as c where d.cus_id = c.cus_id and d.cus_id ='".$id."' and date(dmt_trnx_date)=CURDATE() order by dmt_trnx_id desc ");
            $this->load->view('api_partner/dmt_report',$data);
        }
    }
    
    
    public function search_dmt_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    $id = $this->session->userdata('api_id');    
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
		    $this->load->view('api_partner/dmt_report',$data);
		}
	}
	
	public function ticket($id)
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['ticket']=$this->db_model->getwhere('ticket',array('ticket_id'=>$id,'cus_id'=>$this->session->userdata('api_id')));
		    $this->load->view('api_partner/ticket',$data);
		}
    }
    public function support()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $data['ticket']=$this->db_model->getAlldata('select * from ticket where cus_id="'.$this->session->userdata('api_id').'" group by ticket_id');
		    $data['support']=$this->db_model->getAlldata('SELECT * FROM `support`');
		    $this->load->view('api_partner/support',$data);
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
			redirect('api_partner/support');
		}
		else
		{  
			$txn = $this->input->post('txnid');
			$id = $this->session->userdata('api_id');      
			$from = 'user'; 
			$type = $this->input->post('type');
			$desc = $this->input->post('desc');
			$subject = $this->input->post('subject');
			$priority = $this->input->post('priority');
			$ndate = date('Y-m-d h:i:s');
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
				    redirect('api_partner/support');
				}				        
			}
			if($txn != '')
				{
					$r = $this->db_model->getwhere('exr_rechrgreqexr_rechrgreq_fch',array('apiclid'=>$this->session->userdata('api_id'),'recid'=>$txn));
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
							redirect('api_partner/support');
						}
						else
						{
							$this->session->set_flashdata('message', 'Ticket already arised..');
						    redirect('api_partner/support');
						}
					}
					else
					{
						$this->session->set_flashdata('message', 'Transaction id is invalid');
						redirect('api_partner/support');
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
					redirect('api_partner/support');
				}		
		} 
    }
	
	public function send_ticket()
	{

		$id = $this->session->userdata('api_id');
		$from = 'user';
		$desc = $this->input->post('desc');
		$ticket = $this->input->post('ticket');
		$ndate = DATE('Y-m-d h:i:s');

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
        
        $cus_id = $this->session->userdata('api_id');
        $updata=array('web_login_status'=>'loggedout');
        $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));
        $this->session->unset_userdata(array('api_id','cus_name','cus_type','cus_email','cus_mobile','isLoginApi','senderMobile'));
        redirect('api_partner/login');

	}
    

    /*
	public function addbeneficary()
    {
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $status = $this->service('DMT');
            if($status == 'active'){
    		    $data['bank'] = $this->db->query("select * from bank")->result();
    		    $this->load->view('api_partner/addbeneficary',$data);
            }else{
                
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
            }    
		}
    }*/
    
    
    
    /*
    public function moneytransfer($details){
        
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $status = $this->service('DMT');
            if($status == 'active'){
    		    $det = $this->encryption_model->decode($details);
    		    $arrdet = explode(';' ,$det);
    		    $data['bene_code'] = $arrdet[0];
    		    $data['acct_no'] = $arrdet[1];
    		    $data['ifsc'] = $arrdet[2];
    		    $this->load->view('api_partner/moneytransfer',$data);
            }else{
                $msg['info'] = "DMT SERVICE IS TEMPORARY OFF";
                $this->load->view('api_partner/service',$msg);
            }      
		}
    }*/
    
    

// 	AEPS APIS
    public function onboarding()
    {
        $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsweb/api/onboarding/merchant/creation/php/m1";
        
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];
        $merchantName=$_POST['merchantName'];
        $merchantPhoneNumber=$_POST['merchantPhoneNumber'];
        $companyLegalName=$_POST['companyLegalName'];
        $companyMarketingName =$_POST['companyMarketingName '];
        $emailId=$_POST['emailId'];
        $merchantPinCode=$_POST['merchantPinCode'];
        $merchantCityName=$_POST['merchantCityName'];
        $tan=$_POST['tan'];
        $merchantDistrictName=$_POST['merchantDistrictName'];
        $merchantState=$_POST['merchantState'];
        $merchantAddress=$_POST['merchantAddress'];
        $userPan=$_POST['userPan'];
        $aadhaarNumber=$_POST['aadhaarNumber'];
        $gstInNumber=$_POST['gstInNumber'];
        $companyOrShopPan=$_POST['companyOrShopPan'];
        $companyBankAccountNumber=$_POST['companyBankAccountNumber'];
        $bankIfscCode=$_POST['bankIfscCode'];
        $companyBankName=$_POST['companyBankName'];
        $bankBranchName=$_POST['bankBranchName'];
        $bankAccountName=$_POST['bankAccountName'];
        
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
                        $cancellationCheckImages = "http://edigitalvillage.net/includes/uploads/kyc/$picture";
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
                        $shopAndPanImage = "http://edigitalvillage.net/includes/uploads/kyc/$picture";
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
                        $ekycDocuments = "http://edigitalvillage.net/includes/uploads/kyc/$picture";
                    }
				}
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $merchantLoginId = '';
        for ($i = 0; $i < 10; $i++) {
            $merchantLoginId .= $characters[rand(0, $charactersLength - 1)];
            $merchantLoginPin .= $characters[rand(0, $charactersLength - 1)];
        }
        $values=array(
            "username"=>"Easydigitald",
            "password"=>md5('1234d'),
            "latitude"=>"18.512310",//$latitude,
            "longitude"=>"73.878790",//$longitude,
            "supermerchantId"=>"953",
            "merchants"=>[array(
                            "merchantLoginId"=>$merchantLoginId,
                            "merchantLoginPin"=>$merchantLoginPin,
                            "merchantName"=>$merchantName,
                            "merchantPhoneNumber"=>$merchantPhoneNumber,
                            "companyLegalName"=>$companyLegalName,
                            "companyMarketingName"=>$companyMarketingName,
                            "merchantBranch"=>"",
                            "emailId"=>$emailId,
                            "merchantPinCode"=>$merchantPinCode,
                            "tan"=>$tan,
                            "merchantCityName"=>$merchantCityName,
                            "merchantDistrictName"=>$merchantDistrictName,
                            "cancellationCheckImages"=> base64_encode($cancellationCheckImages),
                            "merchantAddress"=>array("merchantAddress"=>$merchantAddress,"merchantState"=>$merchantState),
                            "kyc"=>array("userPan"=>$userPan,"aadhaarNumber"=>$aadhaarNumber,"gstInNumber"=>$gstInNumber,"companyOrShopPan"=>$companyOrShopPan),
                            "settlement"=>array("companyBankAccountNumber"=>$companyBankAccountNumber,"bankIfscCode"=>$bankIfscCode,"companyBankName"=>$companyBankName,"bankBranchName"=>$bankBranchName,"bankAccountName"=>$bankAccountName),
                            "shopAndPanImage"=> base64_encode($shopAndPanImage),
                            "ekycDocuments"=> base64_encode($ekycDocuments),
                        )]
            );
        $data=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
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
                            "aeps_kyc_status"=>"KYC Completed");
    		$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('api_id')));
    		$this->session->set_flashdata('message','KYC Done Successfully...!!!');
        }
        else{
            $this->session->set_flashdata('message','KYC Not Completed...!!!');
        }
        redirect('api_partner/aeps_transaction');
    }	
    public function cashWithdrawal()
	{

        if(isset($_POST['cashwithdrawal'])){
        $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/cashWithdrawal/merchant/php/withdrawal";
        $captureResponse=$_POST['txtPidData'];
        $PidOptions=$_POST['PidOptions'];
        $adhaarNumber=$_POST['cwadhaarNumber'];
        $nationalBankIdenticationNumber=$_POST['cwnationalBankIdenticationNumber'];
        $requestRemarks=$_POST['cwrequestRemarks'];
        $mobileNumber=$_POST['cwmobileNumber'];
        $transactionAmount=$_POST['cwtransactionAmount'];
        
        $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
        $piData = $xmlCaptureResponse->Data;
        $Hmac = $xmlCaptureResponse->Hmac;
        $Skey = $xmlCaptureResponse->Skey;
        
        preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
	    $x=explode(">",$PidData);
	    $PidDatatype=$x[0];
	    
	    $cidata=explode(">",$cida);
	    $ci=$cidata[0];
	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
        $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
        $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        $values=array(   
            "merchantTranId"=>rand(00000000000000,99999999999999),
            "captureResponse"=>array(
                                "PidDatatype"=>$PidDatatype,
                                "Piddata"=>"$piData",
                                "ci"=>"$ci",
                                "dc"=>"$dc",
                                "dpID"=>$dpId,
                                "errCode"=>$errCode,
                                "errInfo"=>$errInfo,
                                "fCount"=>$fCount,
                                "fType"=>$fType,
                                "hmac"=>"$Hmac",
                                "iCount"=>$iCount,
                                "mc"=>"$mc",
                                "mi"=>$mi,
                                "nmPoints"=>"$nmPoints",
                                "pCount"=>$pCount,
                                "pType"=>$pType,
                                "qScore"=>"$qScore",
                                "rdsID"=>$rdsId,
                                "rdsVer"=>$rdsVer,
                                "sessionKey"=>"$Skey",
                            ),
            "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
            "languageCode"=>'en',
            "latitude"=>"18.512310",
            "longitude"=>"73.878790",
            "mobileNumber"=>$mobileNumber,
            "paymentType"=>'B',
            "requestRemarks"=>$requestRemarks,
            "transactionAmount"=>$transactionAmount,
            "transactionType"=>'CW',//CW
            "merchantUserName"=>$merchantUserName,
            "merchantPin"=>md5($merchantPin),
            "subMerchantId"=>$merchantUserName,
            "superMerchantId"=>'953'
        );
        $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
        
        if($response['data']['transactionStatus']=='successful'){
        
        $amount = $response['data']['transactionAmount'];
        $this->session->set_flashdata('print'," Rs. $amount Withdrawal Successfullly");
        $aepsdata=$this->db_model->insert_update('aeps_transaction_fch',array('message'=>$response['message'],'device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>$response['data']['transactionAmount'],'status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'Remark'=>$requestRemarks,'apiclid'=>$this->session->userdata('api_id'),'through'=>'WEB'));
        $cus_id=$this->session->userdata('api_id');
        if($aepsdata){
            $inserId = $this->db->insert_id();
            $this->session->set_flashdata('printid','https://edigitalvillage.net/Retailer/cashwithdrawalaeps_print/'.$inserId);
            $amount = $response['data']['transactionAmount'];
            
                $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
                $txntype = 'AEPS Cash Withdrawal';
                $total = $cus_aeps_bal + $amount;
        		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
        		
        	    $this->creditAepsCommission($cus_id,$amount,$inserId);
            }
        }
        else{
            $this->session->set_flashdata('error',$response['message']);
        }
        redirect('api_partner/aeps_transaction');
	   
	   }
	   if(isset($_POST['balancecheck'])){
	    $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/balanceInquiry/merchant/php/getBalance";
        $captureResponse=$_POST['txtPidData'];
        $PidOptions=$_POST['PidOptions'];
        $adhaarNumber=$_POST['bcadhaarNumber'];
        $nationalBankIdenticationNumber=$_POST['bcnationalBankIdenticationNumber'];
        $mobileNumber=$_POST['bcmobileNumber'];
        
        $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
        $piData = $xmlCaptureResponse->Data;
        $Hmac = $xmlCaptureResponse->Hmac;
        $Skey = $xmlCaptureResponse->Skey;
        
        preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
	    $x=explode(">",$PidData);
	    $PidDatatype=$x[0];
	    
	    $cidata=explode(">",$cida);
	    $ci=$cidata[0];
	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
        
        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
        $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
        $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        
        $values=array(
            "merchantTransactionId"=>rand(00000000000000,99999999999999),
            "captureResponse"=>array(
                                "PidDatatype"=>$PidDatatype,
                                "Piddata"=>"$piData",
                                "ci"=>"$ci",
                                "dc"=>"$dc",
                                "dpID"=>$dpId,
                                "errCode"=>$errCode,
                                "errInfo"=>$errInfo,
                                "fCount"=>$fCount,
                                "fType"=>$fType,
                                "hmac"=>"$Hmac",
                                "iCount"=>$iCount,
                                "mc"=>"$mc",
                                "mi"=>$mi,
                                "nmPoints"=>"$nmPoints",
                                "pCount"=>$pCount,
                                "pType"=>$pType,
                                "qScore"=>"$qScore",
                                "rdsID"=>$rdsId,
                                "rdsVer"=>$rdsVer,
                                "sessionKey"=>"$Skey",
                                ),
            "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>'0',"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
            "languageCode"=>'en',
            "latitude"=>"18.512310",
            "longitude"=>"73.878790",
            "mobileNumber"=>$mobileNumber,
            "paymentType"=>'B',
            "transactionType"=>'BE',
            "merchantUserName"=>$merchantUserName,
            "merchantPin"=>md5($merchantPin),
            "superMerchantId"=>'953'
           );
        $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        if($response['data']['transactionStatus']=='successful'){
        $this->session->set_flashdata('success',$response['data']['balanceAmount']);
        }
        else{
            $this->session->set_flashdata('error',$response['message']);
        }
        redirect('api_partner/aeps_transaction');
	   }
	   if(isset($_POST['ministetement'])){
	    $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/miniStatement/merchant/php/statement";
        $captureResponse=$_POST['txtPidData'];
        $PidOptions=$_POST['PidOptions'];
        $adhaarNumber=$_POST['msadhaarNumber'];
        $nationalBankIdenticationNumber=$_POST['msnationalBankIdenticationNumber'];
        $mobileNumber=$_POST['msmobileNumber'];
        
        $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
        $piData = $xmlCaptureResponse->Data;
        $Hmac = $xmlCaptureResponse->Hmac;
        $Skey = $xmlCaptureResponse->Skey;
        
        preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
	    $x=explode(">",$PidData);
	    $PidDatatype=$x[0];
	    
	    $cidata=explode(">",$cida);
	    $ci=$cidata[0];
	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
        
        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
        $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
        $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        
        $values=array(
            "merchantTransactionId"=>rand(00000000000000,99999999999999),
            "captureResponse"=>array(
                                "PidDatatype"=>$PidDatatype,
                                "Piddata"=>"$piData",
                                "ci"=>"$ci",
                                "dc"=>"$dc",
                                "dpID"=>$dpId,
                                "errCode"=>$errCode,
                                "errInfo"=>$errInfo,
                                "fCount"=>$fCount,
                                "fType"=>$fType,
                                "hmac"=>"$Hmac",
                                "iCount"=>$iCount,
                                "mc"=>"$mc",
                                "mi"=>$mi,
                                "nmPoints"=>"$nmPoints",
                                "pCount"=>$pCount,
                                "pType"=>$pType,
                                "qScore"=>"$qScore",
                                "rdsID"=>$rdsId,
                                "rdsVer"=>$rdsVer,
                                "sessionKey"=>"$Skey",
                                ),
            "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>'0',"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
            "languageCode"=>'en',
            "latitude"=>"18.512310",
            "longitude"=>"73.878790",
            "mobileNumber"=>$mobileNumber,
            "paymentType"=>'B',
            "transactionType"=>'MS',
            "merchantUserName"=>$merchantUserName,
            "merchantPin"=>md5($merchantPin),
            "superMerchantId"=>'953'
           );
        $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
        if($response['data']['transactionStatus']=='successful'){
            $resdata=$response['data']['miniStatementStructureModel'];
        for($i=0;$i<count($resdata);$i++){ 
            if($i==0){
                    $dbdate=$resdata[$i]['date'];
                    $dbtratype=$resdata[$i]['txnType'];
                    $dbamt=$resdata[$i]['amount'];
                    $dbnaration=$resdata[$i]['narration'];
                    
                    
                    $date=$resdata[$i]['date'];
                    $tratype=$resdata[$i]['txnType'];
                    $amt=$resdata[$i]['amount'];
                    $naration=$resdata[$i]['narration'];
                        $tabledata.="<tr><td>$date</td><td>$tratype</td>
                            <td>$amt</td><td>$naration</td>               
                          </tr>";
                }
                else{
                    $dbdate=$dbdate.','.$resdata[$i]['date'];
                    $dbtratype=$dbtratype.','.$resdata[$i]['txnType'];
                    $dbamt=$dbamt.','.$resdata[$i]['amount'];
                    $dbnaration=$dbnaration.','.$resdata[$i]['narration'];
                }
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
            $this->db_model->insert_update('aeps_transaction_fch',array('msdate'=>$dbdate,'mstratype'=>$dbtratype,'msamount'=>$dbamt,'msnaration'=>$dbnaration,'device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>'0','status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'apiclid'=>$this->session->userdata('api_id'),'through'=>'WEB'));
            $inserId = $this->db->insert_id();
            $this->session->set_flashdata('printid','https://edigitalvillage.net/Retailer/ministmtaeps_print/'.$inserId);
            
        // $this->session->set_flashdata('success',$table);
        }
        else{
            $this->session->set_flashdata('error',$response['message']);
        }
        redirect('api_partner/aeps_transaction');
	   }
	   if(isset($_POST['adharpayment'])){
	      /* $this->session->set_flashdata('print',' Rs Successfullly Pay By Aadhar');
            $this->session->set_flashdata('printid','https://edigitalvillage.net/Retailer/cashwithdrawalaeps_print/24');
            redirect('api_partner/aeps_transaction');
            exit;*/
        $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/aadhaarPay/merchant/php/pay";
        $captureResponse=$_POST['txtPidData'];
        $PidOptions=$_POST['PidOptions'];
        $adhaarNumber=$_POST['apadhaarNumber'];
        $nationalBankIdenticationNumber=$_POST['apnationalBankIdenticationNumber'];
        $requestRemarks=$_POST['aprequestRemarks'];
        $mobileNumber=$_POST['apmobileNumber'];
        $transactionAmount=$_POST['aptransactionAmount'];
        
        $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
        $piData = $xmlCaptureResponse->Data;
        $Hmac = $xmlCaptureResponse->Hmac;
        $Skey = $xmlCaptureResponse->Skey;
        
        preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
        preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
	    $x=explode(">",$PidData);
	    $PidDatatype=$x[0];
	    
	    $cidata=explode(">",$cida);
	    $ci=$cidata[0];
	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
        $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));
        $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
        $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        $values=array(   
            "merchantTranId"=>rand(00000000000000,99999999999999),
            "captureResponse"=>array(
                                "PidDatatype"=>$PidDatatype,
                                "Piddata"=>"$piData",
                                "ci"=>"$ci",
                                "dc"=>"$dc",
                                "dpID"=>$dpId,
                                "errCode"=>$errCode,
                                "errInfo"=>$errInfo,
                                "fCount"=>$fCount,
                                "fType"=>$fType,
                                "hmac"=>"$Hmac",
                                "iCount"=>$iCount,
                                "mc"=>"$mc",
                                "mi"=>$mi,
                                "nmPoints"=>"$nmPoints",
                                "pCount"=>$pCount,
                                "pType"=>$pType,
                                "qScore"=>"$qScore",
                                "rdsID"=>$rdsId,
                                "rdsVer"=>$rdsVer,
                                "sessionKey"=>"$Skey",
                            ),
            "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
            "languageCode"=>'en',
            "latitude"=>"18.512310",
            "longitude"=>"73.878790",
            "mobileNumber"=>$mobileNumber,
            "paymentType"=>'B',
            "requestRemarks"=>$requestRemarks,
            "transactionAmount"=>$transactionAmount,
            "transactionType"=>'M',//CW
            "merchantUserName"=>$merchantUserName,
            "merchantPin"=>md5($merchantPin),
            "subMerchantId"=>$merchantUserName,
            "superMerchantId"=>'953'
        );
        $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
        if($response['data']['transactionStatus']=='successful'){
            $inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>$response['data']['transactionAmount'],'status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'Remark'=>$requestRemarks,'apiclid'=>$this->session->userdata('api_id'),'through'=>'WEB'));
            $inserId = $this->db->insert_id();
            
            $amount = $response['data']['transactionAmount'];
            $this->session->set_flashdata('print',"$amount Rs Successfullly Pay By Aadhar");
            $this->session->set_flashdata('printid','https://edigitalvillage.net/Retailer/cashwithdrawalaeps_print/'.$inserId);

                $cus_id=$this->session->userdata('api_id');
                $amount = $response['data']['transactionAmount'];
                
                $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
                if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
                $txntype = 'Aadhar Pay';
                $total = $cus_aeps_bal + $amount;
        		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                
                $this->creditAdharPayCommission($cus_id,$amount,$inserId);
            
            
        //         $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
        //         if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
        //         $txntype = 'AEPS Cash Withdrawal';
        //         $total = $cus_aeps_bal + $amount;
        // 		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
        		
        // 		$cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
        //         if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                
        //         $commission = $this->Aeps_model->getCustomerCommissionPackage($cus_id);
                
        //         if($commission){
                    
        //             $commissionAmt = ($amount/100) * $commission[0]->percent;
        //             $maxCommi = $commission[0]->maximum_comm;
        //             if($commissionAmt > $maxCommi){
        //                 $commissionAmt = $maxCommi;
        //             }
        //             $txntype = 'Retailer Commission';
        //             $total = $cus_aeps_bal + $commissionAmt;
        //     		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$commissionAmt,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
            		
        //     		$this->Db_model->insert_update('aeps_transaction_fch',array('retailer_commission'=>$commissionAmt),array('ma_id'=>$inserId));
        //         }
            
        // 		$customerParent = $this->Aeps_model->getCustomerParent($cus_id);
        // 		if($customerParent){
        		    
        // 		    $cus_reffer = $customerParent[0]->cus_reffer;
        // 		    $cus_type = ucfirst($customerParent[0]->cus_type);
        		    
        // 		    $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_reffer);
        //             if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                    
        //             $commission = $this->Aeps_model->getCustomerCommissionPackage($cus_reffer);
                
        //             if($commission){
        //                 $commissionAmt = ($amount/100) * $commission[0]->percent;
        //                 $maxCommi = $commission[0]->maximum_comm;
        //                 if($commissionAmt > $maxCommi){
        //                     $commissionAmt = $maxCommi;
        //                 }
                        
        //                 $txntype = "Distributor Commission";
        //                 $total = $cus_aeps_bal + $commissionAmt;
        //         		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_reffer,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>$commissionAmt,'aeps_txn_dbdt'=>0,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
        //         		$this->Db_model->insert_update('aeps_transaction_fch',array('distributor_commission'=>$commissionAmt),array('ma_id'=>$inserId));
        //             }
            		    
        // 		    $customerParent = $this->Aeps_model->getCustomerParent($cus_reffer);
        //     		if($customerParent){
        //     		    $cus_reffer = $customerParent[0]->cus_reffer;
        //     		    $cus_type = ucfirst($customerParent[0]->cus_type);
            		    
        //     		    $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_reffer);
        //                 if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                        
        //                 $commission = $this->Aeps_model->getCustomerCommissionPackage($cus_reffer);
            
        //                 if($commission){
        //                     $commissionAmt = ($amount/100) * $commission[0]->percent;
        //                     $maxCommi = $commission[0]->maximum_comm;
        //                     if($commissionAmt > $maxCommi){
        //                         $commissionAmt = $maxCommi;
        //                     }
                            
        //                     $txntype = "Master Commission";
        //                     $total = $cus_aeps_bal + $commissionAmt;
        //             		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_reffer,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$commissionAmt,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
        //             		$this->Db_model->insert_update('aeps_transaction_fch',array('master_commission'=>$commissionAmt),array('ma_id'=>$inserId));
        //                 }
                		
        //     		}
        // 		}
        }
        else{
            $this->session->set_flashdata('error',$response['message']);
        }
        
        redirect('api_partner/aeps_transaction');
	   
	   }
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
        
        $cus_id = $this->session->userdata('api_id');
        $data = $this->db->query("SELECT * FROM `dmt_commission_slab` as d,customers as c where d.dmt_comm_id = c.dmt_comm_id and cus_id = '".$cus_id."' ")->result_array();
        $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
        $txn_clbal = $trnx_data[0]['txn_clbal'];
        $package_amount = $data[0]['amount'];
        $debit_amount = $package_amount + $amt;
        $new_txn_clbal = $txn_clbal - $debit_amount;
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = time();
		$date = DATE('Y-m-d h:i:s');
		
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
        
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{    
    		$data['dmt_txn'] = $this->db_model->getAlldata("select * from dmt_trnx where dmt_trnx_id='".$dmt_trnx_id."'");
            $this->load->view('api_partner/receipt',$data);
        }    
    }
    
    
    
    public function aeps_report(){
        
  	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{
            
			$cus_id = $this->session->userdata('api_id');
// 			echo $cus_id;exit;
		    $data['aepsReport'] = $this->db_model->getAlldata("SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' and at.transactionType = 'CW' and date(aeps_date_time) = CURDATE() ORDER BY aeps_id DESC");
            $this->load->view('api_partner/aeps_reports',$data);
        }
  	}
  	
  	
  	public function search_aeps_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('api_id');
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
		    $this->load->view('api_partner/aeps_reports',$data);
		}
	}
	
	public function aeps_ledger(){
        
  	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{
            
			$cus_id = $this->session->userdata('api_id');
		    $data['aeps_ledger'] = $this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` where aeps_txn_agentid = '".$cus_id."' and  date(aeps_txn_date) = CURDATE()  ORDER BY aeps_txn_id DESC");
            $this->load->view('api_partner/aeps_ledger',$data);
        }
  	}
  	
  	
  	public function search_aeps_ledger()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('api_id');
		    $sql = "SELECT * FROM `aeps_exr_trnx` as at where aeps_txn_agentid = '".$cus_id."' and " ;
    	    if($from !='' && $to !='')
    		{
    			$sql .="DATE(at.aeps_txn_date) >= '".$from.' 00:00:00'."' and DATE(at.aeps_txn_date) <= '".$to.' 00:00:00'."' and ";
    		}else{
    		    if($from !=''){
    		        $sql .=" DATE(at.aeps_txn_date) = '".$from.' 00:00:00'."'  and " ;
    		    }else if($to !=''){
    		        $sql .=" DATE(at.aeps_txn_date) = '".$to.' 00:00:00'."' and ";
    		    }
    		}
    		
		    $sql .= " at.aeps_txn_id != '' ORDER BY aeps_txn_id DESC";
		    $data['aeps_ledger'] = $this->db_model->getAlldata($sql);
		    //echo $this->db->last_query();exit;
		    $this->load->view('api_partner/aeps_ledger',$data);
		}
	}
  	
  	
  	public function pan_report(){
        
  	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{
			$cus_id = $this->session->userdata('api_id');
			$vl=$cus_id;
		    $data['coupons'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,coupon_price as cp where cb.type = cp.coupon_price_id  and cb.pan_cus_id = '".$vl."' and date(date) = CURDATE()  ORDER BY coupon_buy_id DESC");
            $this->load->view('api_partner/pan_reports',$data);
        }
  	}
  	
  	
  	public function search_pan_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    	    
    	    $cus_id = $this->session->userdata('api_id');
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
		    $this->load->view('api_partner/pan_reports',$data);
		}
	}
	
  	
  	public function pan_cardreceipt($coupon_buy_id){
        
        if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{    
    		$data['dmt_txn'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,coupon_price as cp where cb.type = cp.coupon_price_id  and cb.coupon_buy_id = '".$coupon_buy_id."'  ORDER BY coupon_buy_id DESC");
            $this->load->view('api_partner/pan_cardreceipt',$data);
        }    
    }
    
  	public function adhar_pay_report(){
        
  	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{
            
			$cus_id = $this->session->userdata('api_id');
		    $data['adhar_pay_report'] = $this->db_model->getAlldata("SELECT * FROM aeps_transaction_fch as at,aeps_bank as ab where at.aeps_bank_id = ab.iinno  and at.apiclid = '".$cus_id."' and at.transactionType='M' and date(aeps_date_time) = CURDATE() ORDER BY aeps_id DESC");
            $this->load->view('api_partner/adhar_pay_reports',$data);
        }
  	}
  	
  	
  	public function search_adhar_pay_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('api_id');
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
		    $this->load->view('api_partner/adhar_pay_reports',$data);
		}
	}
  	
  	
  	
  	public function micro_atm_report(){
        
  	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
        }else{
            
			$cus_id = $this->session->userdata('api_id');
		    $data['microatm'] = $this->db_model->getAlldata("SELECT * FROM micro_atm as ma , customers as c where ma.cus_id = c.cus_id and ma.cus_id = '".$cus_id."' and DATE(ma.ma_date_time) >= '".date('Y-m-d').' 00:00:00'."' and DATE(ma.ma_date_time) <= '".date('Y-m-d').' 00:00:00'."' ORDER BY ma_id DESC");
            $this->load->view('api_partner/micro_atm_reports',$data);
        }
  	}
  	
  	
  	public function search_micro_atm_report()
	{
	    if($this->session->userdata('isLoginApi') == FALSE){
            redirect('api_partner/login');
		}else{
		    $frm = $_POST['from_dt'];
    	    $to = $_POST['to_dt'];
    		$from = ($frm !="" ? date("Y-m-d", strtotime($frm)) : ''); echo "<br>";
    		$to = ($to !="" ? date("Y-m-d", strtotime($to)) : '');
    		
    	    
    		$cus_id = $this->session->userdata('api_id');
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
		    $this->load->view('api_partner/micro_atm_reports',$data);
		}
	}
  	
    
    public function dmtLogOut(){
        $this->session->unset_userdata('senderMobile');
        redirect('api_partner/dashboard');
        
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
            redirect('api_partner/addbeneficary');
        }
    }
    public function cashwithdrawalaeps_print($aeps_id)
    {
        // $data['data']=$this->db_model->getwhere('aeps_transaction_fch',array('aeps_id'=>$aeps_id));
        $data['data']=$this->db_model->getAlldata("SELECT at.transactionType,at.aadhar_number,at.amount,at.aeps_date_time,at.utr,c.cus_name,c.cus_mobile,ab.bankName FROM aeps_transaction_fch as at,aeps_bank as ab,customers as c where at.aeps_bank_id = ab.iinno and at.apiclid=c.cus_id and at.aeps_id = ".$aeps_id);
        $this->load->view('api_partner/cashwithdrawalaeps_print',$data);
    }
    public function ministmtaeps_print($aeps_id)
    {
        // echo $this->encryption_model->decode('421e44e2e831de39007fc91a99439f0c');echo"\n";
        // echo $this->encryption_model->decode('eea039dbb75b45b857cec7668353f128');exit;
        $data['data']=$this->db_model->getAlldata("SELECT at.msdate,at.mstratype,at.msamount,at.msnaration,at.transactionType,at.aadhar_number,at.amount,at.aeps_date_time,at.utr,c.cus_name,c.cus_mobile,ab.bankName FROM aeps_transaction_fch as at,aeps_bank as ab,customers as c where at.aeps_bank_id = ab.iinno and at.apiclid=c.cus_id and at.aeps_id = ".$aeps_id);
        $this->load->view('api_partner/ministmtaeps_print',$data);
    }
    public function service($service){
        
        $data = $this->db_model->getAllData("select * from service where name='".$service."' ");
        return $data[0]->status;
    }
    /* public function creditAepsCommission($cus_id,$amount,$aeps_id){
        
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
    } */
    
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
//---------------------------------------------------------------------------AEPS API----------------------------------------------------------------------------------------//
   
   
  /*  public function aepsapi()
	{    
	   // echo 'hello';exit;
	        $msg = $_POST['msg'];
            
            $data = explode('-',$msg);
            $api_ip = $data['3'];
            $id = $data['2'];
            $user_mobile = $data['0'];
            $pass = $data['1'];
            
            $api_query = "select * from customers where cus_mobile = '".$this->encryption_model->encode($user_mobile)."' and cus_id = '".$id."' and cus_pass = '".$this->encryption_model->encode($pass)."' and cus_type='api' ";
            $rec = $this->db_model->getAlldata($api_query);
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){
                    
            	    $captureResponse=$_POST['txtPidData'];
                    $adhaarNumber=$_POST['adhaarNumber'];
                    $cus_id=$_POST['cus_id']; 
                    $nationalBankIdenticationNumber=$_POST['nationalBankIdenticationNumber'];
                    $mobileNumber=$_POST['mobileNumber'];
                    $transactionAmount=$_POST['transactionAmount'];
                    
                    $outletname=$_POST['cus_name'];
                    $outletmobile=$_POST['cus_mobile'];
                    $merchantUserName=$_POST['merchantLoginId'];
                    $merchantPin=$_POST['merchantLoginPin'];
                    $parameters = array(
                            array('field' => 'txtPidData','label' => 'PID Data','rules' => 'required'),
                            array('field' => 'adhaarNumber','label' => 'Aadhar Number','rules' => 'required|greater_than[0]'),
                            array('field' => 'nationalBankIdenticationNumber','label' => 'Bank Name','rules' => 'required'),
                            array('field' => 'mobileNumber','label' => 'Mobile Number','rules' => 'required'),
                            array('field' => 'cus_name','label' => 'Bank Name','rules' => 'required'),
                            array('field' => 'cus_mobile','label' => 'Outlet Mobile Number','rules' => 'required'),
                            array('field' => 'merchantLoginId','label' => 'Merchant ID','rules' => 'required'),
                            array('field' => 'merchantLoginPin','label' => 'Merchant Pin','rules' => 'required'),
                            array('field' => 'cus_id','label' => 'Customer ID','rules' => 'required')
                        );
                    $this->form_validation->set_rules($parameters);
                    $this->form_validation->set_error_delimiters(' ', ' ');
                    
                    if($this->form_validation->run()){
                        
                        $iCount=0;$pCount=0;
                        preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]);  
                        preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
                        preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
                        preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
                        preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
                        preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdser=str_replace('"',"",$matches[1][0]); $rdsVer=str_replace('>',"",$rdser); 
                        preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]);
                	    
                	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
                	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
                	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
                	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
                	    
                	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
                	    $x=explode(">",$PidData);
                	    $PidDatatype=$x[0];
                	    
                	    $cidata=explode(">",$cida);
                	    $ci=$cidata[0];
                	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]);     
                            
                       
                        $xmlCaptureResponse = simplexml_load_string($captureResponse);
                    
                        $piData = $xmlCaptureResponse->Data;
                        $Hmac = $xmlCaptureResponse->Hmac;
                        $Skey = $xmlCaptureResponse->Skey;
                        $deviceInfo = $xmlCaptureResponse->DeviceInfo;
                        $rdsVers = $deviceInfo['rdsVer'];
                        $Resp = $xmlCaptureResponse->Resp;
                        $qScore = $Resp['qScore'];
                        $rdsVer=str_replace('"0":',"",$rdsVers); 
                        if($_POST['type']=='cashwithdrawal')
                        {
                            
                                $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/cashWithdrawal/merchant/php/withdrawal";
                                $values=array(   
                                "merchantTranId"=>rand(00000000000000,99999999999999),
                                "captureResponse"=>array(
                                                    "PidDatatype"=>'X',
                                                    "Piddata"=>"$piData",
                                                    "ci"=>"$ci",
                                                    "dc"=>"$dc",
                                                    "dpID"=>$dpId,
                                                    "errCode"=>$errCode,
                                                    "errInfo"=>"Capture Success",
                                                    "fCount"=>$fCount,
                                                    "fType"=>$fType,
                                                    "hmac"=>"$Hmac",
                                                    "iCount"=>$iCount,
                                                    "mc"=>"$mc",
                                                    "mi"=>$mi,
                                                    "nmPoints"=>"$nmPoints",
                                                    "pCount"=>$pCount,
                                                    "pType"=>$pType,
                                                    "qScore"=>"$qScore",
                                                    "rdsID"=>$rdsId,
                                                    "rdsVer"=>$rdsVer,
                                                    "sessionKey"=>"$Skey",
                                                ),
                                "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
                                "languageCode"=>'en',
                                "latitude"=>"18.512310",
                                "longitude"=>"73.878790",
                                "mobileNumber"=>$mobileNumber,
                                "paymentType"=>'B',
                                "requestRemarks"=>$requestRemarks,
                                "transactionAmount"=>$transactionAmount,
                                "transactionType"=>'CW',//CW
                                "merchantUserName"=>$merchantUserName,
                                "merchantPin"=>md5($merchantPin),
                                "subMerchantId"=>$merchantUserName,
                                "superMerchantId"=>'953'
                            );
                            $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
                            //$this->db_model->insert_update('aeps_transaction_fch',array('apiclid'=>$cus_id,'request'=>json_encode($values),'response'=>json_encode($response)));
                            $inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'message'=>$response['message'],'amount'=>$response['data']['transactionAmount'],'status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'apiclid'=>$cus_id,'through'=>'API Partner'));
                            $inserId = $this->db->insert_id();
                            $url='https://edigitalvillage.net/Retailer/cashwithdrawalaeps_print/'.$inserId;
                            if($response['data']['transactionStatus']=='successful'){
                                if($inserId){
                                    $amount = $response['data']['transactionAmount'];
                                    $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
                                    if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                                    $txntype = 'AEPS Cash Withdrawal';
                                    $total = $cus_aeps_bal + $amount;
                            		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                                    $this->creditAepsCommission($cus_id,$amount,$inserId);
                                }  
                            $jdata= json_encode([
                                            'status' => TRUE,
                                            'message' => $response['message'],
                                            'result' => [
                                                            "transactionAmount"=>$response['data']['transactionAmount'],
                                                            "terminalId"=>$response['data']['terminalId'],
                                                            "device"=>$dpId,
                                                            "requestTransactionTime"=>$response['data']['requestTransactionTime'],
                                                            "transactionStatus"=>$response['data']['transactionStatus'],
                                                            "balanceAmount"=>$response['data']['balanceAmount'],
                                                            "bankRRN"=>$response['data']['bankRRN'],
                                                            "transactionType"=>$response['data']['transactionType'],
                                                            "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                            "merchantTransactionId"=>$response['data']['merchantTransactionId'],
                                                        ],
                                            'ministatement' => '',
                                            'outletname' => $outletname,
                                            'outletmobile' => $outletmobile,
                                            'url' => $url,
                                            ]);
                                            echo $jdata;
                            }
                            else{
                                $jdata= json_encode([
                                            'status' => FALSE,
                                            'message' => $response['message'],
                                            'result' => [
                                                            "transactionAmount"=>$response['data']['transactionAmount'],
                                                            "terminalId"=>$response['data']['terminalId'],
                                                            "requestTransactionTime"=>$response['data']['requestTransactionTime'],
                                                            "transactionStatus"=>$response['data']['transactionStatus'],
                                                            "balanceAmount"=>$response['data']['balanceAmount'],
                                                            "bankRRN"=>$response['data']['bankRRN'],
                                                            "transactionType"=>$response['data']['transactionType'],
                                                            "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                            "merchantTransactionId"=>$response['data']['merchantTransactionId'],
                                                        ],
                                            'ministatement' => '',
                                            'outletname' => $outletname,
                                            'outletmobile' => $outletmobile,
                                            'url' => '',
                                            ]);
                                            echo $jdata;
                            }
                                        
                        }
                        else if($_POST['type']=='balancecheck')
                        {
                            $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/balanceInquiry/merchant/php/getBalance";
                            $values=array(
                                    "merchantTransactionId"=>rand(00000000000000,99999999999999),
                                    "captureResponse"=>array(
                                                        "PidDatatype"=>$PidDatatype,
                                                        "Piddata"=>"$piData",
                                                        "ci"=>"$ci",
                                                        "dc"=>"$dc",
                                                        "dpID"=>$dpId,
                                                        "errCode"=>$errCode,
                                                        "errInfo"=>"Capture Success",
                                                        "fCount"=>$fCount,
                                                        "fType"=>$fType,
                                                        "hmac"=>"$Hmac",
                                                        "iCount"=>$iCount,
                                                        "mc"=>"$mc",
                                                        "mi"=>$mi,
                                                        "nmPoints"=>"$nmPoints",
                                                        "pCount"=>$pCount,
                                                        "pType"=>$pType,
                                                        "qScore"=>"$qScore",
                                                        "rdsID"=>$rdsId,
                                                        "rdsVer"=>$rdsVer,
                                                        "sessionKey"=>"$Skey",
                                                        ),
                                    "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>'0',"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
                                    "languageCode"=>'en',
                                    "latitude"=>"18.512310",
                                    "longitude"=>"73.878790",
                                    "mobileNumber"=>$mobileNumber,
                                    "paymentType"=>'B',
                                    "transactionType"=>'BE',
                                    "merchantUserName"=>$merchantUserName,
                                    "merchantPin"=>md5($merchantPin),
                                    "superMerchantId"=>'953'
                                   );
                                        $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
                                        //$inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('response'=>$response,'request'=>$values,'apiclid'=>$cus_id,'through'=>'API Partner'));
                                       // $this->db_model->insert_update('aeps_transaction_fch',array('apiclid'=>$cus_id,'request'=>json_encode($values),'response'=>json_encode($response)));
                                        $jdata= json_encode([
                                            'status' => TRUE,
                                            'message' => $response['message'],
                                            'result' => [
                                                            "transactionAmount"=>$response['data']['transactionAmount'],
                                                            "terminalId"=>$response['data']['terminalId'],
                                                            "device"=>$dpId,
                                                            "requestTransactionTime"=>$response['data']['requestTransactionTime'],
                                                            "transactionStatus"=>$response['data']['transactionStatus'],
                                                            "balanceAmount"=>$response['data']['balanceAmount'],
                                                            "bankRRN"=>$response['data']['bankRRN'],
                                                            "transactionType"=>$response['data']['transactionType'],
                                                            "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                            "merchantTransactionId"=>$response['data']['merchantTransactionId'],
                                                        ],
                                            'ministatement' => '',
                                            'outletname' => $outletname,
                                            'outletmobile' => $outletmobile,
                                            'url' => '',
                                            ]);
                                            
                                            echo $jdata;
                                                    
                                    }
                        else if($_POST['type']=='ministatement')
                        {
                            $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/miniStatement/merchant/php/statement";
                            $values=array(
                                    "merchantTransactionId"=>rand(00000000000000,99999999999999),
                                    "captureResponse"=>array(
                                                        "PidDatatype"=>$PidDatatype,
                                                        "Piddata"=>"$piData",
                                                        "ci"=>"$ci",
                                                        "dc"=>"$dc",
                                                        "dpID"=>$dpId,
                                                        "errCode"=>$errCode,
                                                        "errInfo"=>"Capture Success",
                                                        "fCount"=>$fCount,
                                                        "fType"=>$fType,
                                                        "hmac"=>"$Hmac",
                                                        "iCount"=>$iCount,
                                                        "mc"=>"$mc",
                                                        "mi"=>$mi,
                                                        "nmPoints"=>"$nmPoints",
                                                        "pCount"=>$pCount,
                                                        "pType"=>$pType,
                                                        "qScore"=>"$qScore",
                                                        "rdsID"=>$rdsId,
                                                        "rdsVer"=>$rdsVer,
                                                        "sessionKey"=>"$Skey",
                                                        ),
                                    "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>'0',"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
                                    "languageCode"=>'en',
                                    "latitude"=>"18.512310",
                                    "longitude"=>"73.878790",
                                    "mobileNumber"=>$mobileNumber,
                                    "paymentType"=>'B',
                                    "transactionType"=>'MS',
                                    "merchantUserName"=>$merchantUserName,
                                    "merchantPin"=>md5($merchantPin),
                                    "superMerchantId"=>'953'
                                   );
                            $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
                            //$this->db_model->insert_update('aeps_transaction_fch',array('apiclid'=>$cus_id,'request'=>json_encode($values),'response'=>json_encode($response)));
                            if($response['data']['transactionStatus']=='successful'){
                                $newdata=array();
                                $resdata=$response['data']['miniStatementStructureModel'];
                                if(is_array($resdata)){
                                        for($i=0;$i<count($resdata);$i++){ 
                                            if($i==0){
                                                    $dbdate=$resdata[$i]['date'];
                                                    $dbtratype=$resdata[$i]['txnType'];
                                                    $dbamt=$resdata[$i]['amount'];
                                                    $dbnaration=$resdata[$i]['narration'];
                                                }
                                                else{
                                                    
                                                    $dbdate=$dbdate.','.$resdata[$i]['date'];
                                                    $dbtratype=$dbtratype.','.$resdata[$i]['txnType'];
                                                    $dbamt=$dbamt.','.$resdata[$i]['amount'];
                                                    $dbnaration=$dbnaration.','.$resdata[$i]['narration'];
                                                }
                                            }
                                        $this->db_model->insert_update('aeps_transaction_fch',array('message'=>$response['message'],'msdate'=>$dbdate,'mstratype'=>$dbtratype,'msamount'=>$dbamt,'msnaration'=>$dbnaration,'device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>'0','status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'apiclid'=>$cus_id,'through'=>'API PARTNER'));
                                        $inserId = $this->db->insert_id();
                                        $url='https://edigitalvillage.net/Retailer/ministmtaeps_print/'.$inserId;
                                       $jdata= json_encode([
                                                    'status' => TRUE,
                                                    'message' => $response['message'],
                                                    'result' => [
                                                        "device"=>$dpId,
                                                        "bankRRN"=>$response['data']['bankRRN'],
                                                        "transactionStatus"=>$response['data']['transactionStatus'],
                                                        "transactionType"=>$response['data']['transactionType'],
                                                        "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                    ],
                                                    'ministatement' => $resdata,
                                                    'outletname' => $outletname,
                                                    'outletmobile' => $outletmobile,
                                                    'url' => $url
                                                    ]);
                                                  echo  $jdata;
                                }else{
                                       $this->db_model->insert_update('aeps_transaction_fch',array('message'=>$response['message'],'msdate'=>'','mstratype'=>'','msamount'=>'','msnaration'=>'','device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>'0','status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'apiclid'=>$cus_id,'through'=>'API PARTNER'));       
                                        $jdata= json_encode([
                                                    'status' => TRUE,
                                                    'message' => $response['message'],
                                                    'result' => [
                                                        "device"=>$dpId,
                                                        "bankRRN"=>$response['data']['bankRRN'],
                                                        "transactionStatus"=>$response['data']['transactionStatus'],
                                                        "transactionType"=>$response['data']['transactionType'],
                                                        "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                    ],
                                                    'ministatement' => $resdata,
                                                    'outletname' => $outletname,
                                                    'outletmobile' => $outletmobile,
                                                    'url' => $url
                                                    ]);
                                                  echo  $jdata;
                                    
                                }
                            }
                            else{
                                    $jdata= json_encode([
                                        'status' => TRUE,
                                        'message' => $response['message'],
                                        'result' => [
                                                "device"=>$dpId,
                                                "bankRRN"=>$response['data']['bankRRN'],
                                                "transactionStatus"=>$response['data']['transactionStatus'],
                                                "transactionType"=>$response['data']['transactionType'],
                                                "fpTransactionId"=>$response['data']['fpTransactionId'],
                                            ],
                                        'ministatement' => '',
                                        'outletname' => $outletname,
                                        'outletmobile' => $outletmobile,
                                        'url' => '',
                                    ]);
                                    echo $jdata;
                            }
               	   }
                        else if($_POST['type']=='aadharpay'){
                            $url='';
                                    $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/aadhaarPay/merchant/php/pay";
                                   $values=array(   
                                    "merchantTranId"=>rand(00000000000000,99999999999999),
                                    "captureResponse"=>array(
                                            "PidDatatype"=>$PidDatatype,
                                            "Piddata"=>"$piData",
                                            "ci"=>"$ci",
                                            "dc"=>"$dc",
                                            "dpID"=>$dpId,
                                            "errCode"=>$errCode,
                                            "errInfo"=>"Capture Success",
                                            "fCount"=>$fCount,
                                            "fType"=>$fType,
                                            "hmac"=>"$Hmac",
                                            "iCount"=>$iCount,
                                            "mc"=>"$mc",
                                            "mi"=>$mi,
                                            "nmPoints"=>"$nmPoints",
                                            "pCount"=>$pCount,
                                            "pType"=>$pType,
                                            "qScore"=>"$qScore",
                                            "rdsID"=>$rdsId,
                                            "rdsVer"=>$rdsVer,
                                            "sessionKey"=>"$Skey",
                                        ),
                                    "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
                                    "languageCode"=>'en',
                                    "latitude"=>"18.512310",
                                    "longitude"=>"73.878790",
                                    "mobileNumber"=>$mobileNumber,
                                    "paymentType"=>'B',
                                    "requestRemarks"=>$requestRemarks,
                                    "transactionAmount"=>$transactionAmount,
                                    "transactionType"=>'M',//CW
                                    "merchantUserName"=>$merchantUserName,
                                    "merchantPin"=>md5($merchantPin),
                                    "subMerchantId"=>$merchantUserName,
                                    "superMerchantId"=>'953'
                                );
                                    $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
                                    //$this->db_model->insert_update('aeps_transaction_fch',array('apiclid'=>$cus_id,'request'=>json_encode($values),'response'=>json_encode($response)));
                                $inserted_id=$this->db_model->insert_update('aeps_transaction_fch',array('message'=>$response['message'],'device'=>$dpId,'aadhar_number'=>$adhaarNumber,'aeps_bank_id'=>$nationalBankIdenticationNumber,'amount'=>$response['data']['transactionAmount'],'status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'utr'=>$response['data']['bankRRN'],'apiclid'=>$cus_id,'through'=>'API Partner'));
                                $inserId = $this->db->insert_id();
                                $url='https://edigitalvillage.net/Retailer/cashwithdrawalaeps_print/'.$inserId;
                                if($response['data']['transactionStatus']=='successful'){
                                
                            
                            
                            if($inserId){
                            $amount = $response['data']['transactionAmount'];
                            
                            $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
                            if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
                            
                            $txntype = 'Aadhar Pay';
                            $total = $cus_aeps_bal + $amount;
                    		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                            
                            $this->creditAdharPayCommission($cus_id,$amount,$inserId);
                            
                   
                        
                        }  
                            $jdata= json_encode([
                                            'status' => TRUE,
                                            'message' => $response['message'],
                                            'result' => [
                                                            "transactionAmount"=>$response['data']['transactionAmount'],
                                                            "terminalId"=>$response['data']['terminalId'],
                                                            "device"=>$dpId,
                                                            "requestTransactionTime"=>$response['data']['requestTransactionTime'],
                                                            "transactionStatus"=>$response['data']['transactionStatus'],
                                                            "balanceAmount"=>$response['data']['balanceAmount'],
                                                            "bankRRN"=>$response['data']['bankRRN'],
                                                            "transactionType"=>$response['data']['transactionType'],
                                                            "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                            "merchantTransactionId"=>$response['data']['merchantTransactionId'],
                                                        ],
                                            'ministatement' => '',
                                            'outletname' => $outletname,
                                            'outletmobile' => $outletmobile,
                                            'url' => $url,
                                            ]);
                                            echo $jdata;
                            }
                            else{
                                            $jdata= json_encode([
                                                        'status' => FALSE,
                                                        'message' => $response['message'],
                                                        'result' => [
                                                                        "transactionAmount"=>$response['data']['transactionAmount'],
                                                                        "terminalId"=>$response['data']['terminalId'],
                                                                        "device"=>$dpId,
                                                                        "requestTransactionTime"=>$response['data']['requestTransactionTime'],
                                                                        "transactionStatus"=>$response['data']['transactionStatus'],
                                                                        "balanceAmount"=>$response['data']['balanceAmount'],
                                                                        "bankRRN"=>$response['data']['bankRRN'],
                                                                        "transactionType"=>$response['data']['transactionType'],
                                                                        "fpTransactionId"=>$response['data']['fpTransactionId'],
                                                                        "merchantTransactionId"=>$response['data']['merchantTransactionId'],
                                                                    ],
                                                        'ministatement' => '',
                                                        'outletname' => $outletname,
                                                        'outletmobile' => $outletmobile,
                                                        'url' => $url,
                                                        ]);
                                                        echo $jdata;
                                        }
                            	   }
                        else{
                            $jdata= json_encode([
                            'status' => FALSE,
                            'message' => 'Transaction Type Is Incorrect',
                            'result' => [
                                                "transactionAmount"=>'',
                                                "terminalId"=>'',
                                                "device"=>'',
                                                "requestTransactionTime"=>'',
                                                "transactionStatus"=>'',
                                                "balanceAmount"=>'',
                                                "bankRRN"=>'',
                                                "transactionType"=>'',
                                                "fpTransactionId"=>'',
                                                "merchantTransactionId"=>'',
                                                                    ],
                                    'ministatement' => '',
                                    'outletname' => '',
                                    'outletmobile' => '',
                                    'url' => '',
                            ]);
                            echo $jdata;	       
                        }
                    }
                    else{
                        $jdata= json_encode([
                            'status' => FALSE,
                            'message' => validation_errors(),
                            'result' => [
                                                "transactionAmount"=>'',
                                                "terminalId"=>'',
                                                "device"=>'',
                                                "requestTransactionTime"=>'',
                                                "transactionStatus"=>'',
                                                "balanceAmount"=>'',
                                                "bankRRN"=>'',
                                                "transactionType"=>'',
                                                "fpTransactionId"=>'',
                                                "merchantTransactionId"=>'',
                                                                    ],
                                    'ministatement' => '',
                                    'outletname' => '',
                                    'outletmobile' => '',
                                    'url' => '',
                            ]);
                            echo $jdata;
                    }
                }else{
                    $jdata= json_encode([
                                    'status' => FALSE,
                                    'message' => 'Invalid Register Ip',
                                    'result' => [
                                                "transactionAmount"=>'',
                                                "terminalId"=>'',
                                                "device"=>'',
                                                "requestTransactionTime"=>'',
                                                "transactionStatus"=>'',
                                                "balanceAmount"=>'',
                                                "bankRRN"=>'',
                                                "transactionType"=>'',
                                                "fpTransactionId"=>'',
                                                "merchantTransactionId"=>'',
                                                                    ],
                                    'ministatement' => '',
                                    'outletname' => '',
                                    'outletmobile' => '',
                                    'url' => '',
                                ]); 
                                 echo $jdata;
            } 
            }else{
                 $jdata= json_encode([
                                    'status' => FALSE,
                                    'message' => 'Invalid Api User Credentials',
                                    'result' => [
                                                "transactionAmount"=>'',
                                                "terminalId"=>'',
                                                "device"=>'',
                                                "requestTransactionTime"=>'',
                                                "transactionStatus"=>'',
                                                "balanceAmount"=>'',
                                                "bankRRN"=>'',
                                                "transactionType"=>'',
                                                "fpTransactionId"=>'',
                                                "merchantTransactionId"=>'',
                                                                    ],
                                    'ministatement' => '',
                                    'outletname' => '',
                                    'outletmobile' => '',
                                    'url' => '',
                                ]); 
                                 echo $jdata;
        }
	}
	
	public function bankList(){
        $response = $this->aeps_model->getbank_list();
        if($response){
            $jdata= json_encode([
                'status' => TRUE,
                'message' => "Bank List",
                'result' => $response
                ]);
                echo $jdata;
        }else{
            $jdata= json_encode([
                'status' => FALSE,
                'message' => "SOMETHING WENT WRONG"
                ]);
                echo $jdata;
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
                 
                    
                    if($cus_type == "api"){
                        foreach($commission_scheme_package as $comm){
                            $minRange = $comm->amount_min_range;
                            $maxRange = $comm->amount_max_range;
                            if($amount >= $minRange && $amount <= $maxRange){
                                $api_comm = $comm->api_comm; 
                                $comm_type = $comm->type;
                                if($api_comm){
                                    if($comm_type == "percent"){
                                        $commission_amount = round((($api_comm / 100) * $amount),3);
                                    }else{
                                        $commission_amount = $api_comm;
                                    }
                                    $last_balance = $this->db_model->get_aepstrnx($cus_id);
                                    $this->db_model->insert_update('aeps_transaction_fch', array('api_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
                                   
                						$txntype = "Api Aeps Commission";
                						if($last_balance){
                                            $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                        }else{
                                            $cus_aeps_bal = '0';
                                        }
                                        $total = $cus_aeps_bal + $commission_amount;
                						$this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>'0','aeps_txn_crdt'=>$commission_amount,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
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
    public function creditAdharPayCommission($cus_id,$amount,$aeps_id){
        $commission_scheme_id = $this->db_model->getAllData("SELECT scheme_id,cus_type,cus_reffer FROM customers WHERE cus_id = '$cus_id'");
        $scheme_id = $commission_scheme_id[0]->scheme_id;
        $cus_type = $commission_scheme_id[0]->cus_type;
        if($scheme_id != 0 ){
            $checkIfActive = $this->db_model->getAllData("SELECT * FROM commission_scheme WHERE scheme_id = '$scheme_id' AND scheme_status = 'active' ");
            if($checkIfActive){
                $commission_scheme_package = $this->db_model->getAllData("SELECT * FROM commission_scheme_adharpay WHERE scheme_id = '$scheme_id'");
                //print_r($commission_scheme_package);
                if($commission_scheme_package){

                    if($cus_type == "api"){
                        foreach($commission_scheme_package as $comm){
                            $minRange = $comm->amount_min_range;
                            $maxRange = $comm->amount_max_range;
                            if($amount >= $minRange && $amount <= $maxRange){
                                $api_comm = $comm->api_comm; 
                                $comm_type = $comm->type;
                                if($api_comm){
                                    if($comm_type == "percent"){
                                        $commission_amount = round((($api_comm / 100) * $amount),3);
                                    }else{
                                        $commission_amount = $api_comm;
                                    }
                                    $last_balance = $this->db_model->get_aepstrnx($cus_id);
                                    $this->db_model->insert_update('aeps_transaction_fch', array('api_commission'=>$commission_amount),array('aeps_id'=>$aeps_id));
            				// 		if($last_balance)
            				// 		{
                						$txntype = "Api Aadhar Pay Charge";
                						if($last_balance){
                                            $cus_aeps_bal = $last_balance[0]->aeps_txn_clbal;
                                        }else{
                                            $cus_aeps_bal = '0';
                                        }
                                        $total = $cus_aeps_bal - $commission_amount;
                						$this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_recrefid'=>$aeps_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_dbdt'=>$commission_amount,'aeps_txn_crdt'=>'0','aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
            				// 		}
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
    */
    
//---------------------------------------------------------------------------END AEPS API----------------------------------------------------------------------------------------//
//---------------------------------------------------------------------------DMT API----------------------------------------------------------------------------------------//
    
    /*public function registerDmtUser(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_ip = $data['4'];
            $id = $data['5'];
            $user_mobile = $data['6'];
            $user_name = $data['7'];
        
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){
                    
                    $token = $this->token;
                    $data = "msg=E06003~$token~$id~$user_mobile~$user_name~NA~NA";
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
                    
                    $http_code = $info['http_code'];
                    if($http_code == "200"){
                        echo $server_output;
                    }else{
                        echo 'Unable To get Response';
                    }
                    
                }else{
                  echo 'Invalid Register Ip';  
                } 
            }else{
                echo 'Invalid Api User Credentials';
            }
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }    
    }
    
    
    public function verifySenderOtp(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_ip = $data['4'];
            $id = $data['5'];
            $VerifyReferenceNo = $data['6'];
            $otp = $data['7'];
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){
            
                    $token = $this->token;
                    $data = "msg=E06021~$token~$id~$VerifyReferenceNo~$otp~NA~NA";
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
                    
                    $http_code = $info['http_code'];
                    if($http_code == "200"){
                        echo $server_output;
                    }else{
                        echo "Unable To get Response";
                    }
                }else{
                    echo 'Invalid Api User credentials';
                }
            }else{
                echo 'Invalid Ip Address';
            }
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }    
        
    }
	
	
    public function checkIfDmtUserExists(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_ip = $data['4'];
            $id = $data['5'];
            $mobile = $data['6'];
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){
                    $token = $this->token;
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06007~$token~$id~$mobile~NA~NA";
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
                    
                    $http_code = $info['http_code'];
                    if($http_code == "200"){
                        echo $server_output;
                    }else{
                        echo "Unable To get Response";
                    }
                }else{
                    echo 'Invalid Ip Address'; 
                }
            }else{
                echo 'Invalid Api User Credentials';
            }
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }    
             
        
    }
    
    
    
	public function getbeneficary(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_ip = $data['4'];
            $id = $data['5'];
            $mobile = $data['6'];
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){
                    
        		    $token = $this->token;
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06011~$token~$id~$mobile~NA~NA";
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
                    
                    $http_code = $info['http_code'];
                    if($http_code == "200"){
                        echo $server_output;
                    }else{
                        echo "Unable To get Response";
                    }
    		    }else{
                   echo 'Invalid Ip Address'; 
                }   
    		}else{
                echo 'Invalid Api User Credentials';
    		}
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }	
    }
    
    
    public function getsenderbalance(){
         
                    
        $msg = $this->input->post('msg');
        $rec = $this->checkapiuser($msg);
        $data = explode('~',$msg);
        $api_ip = $data['4'];
        $id = $data['5'];
        $mobile = $data['6'];
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){   
                
                $token = $this->token;
                $id = rand(1000000000,9999999999);
                $data = "msg=E06005~$token~$id~$mobile~NA~NA";
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
                //print_r($server_output);
                $http_code = $info['http_code'];
                echo $server_output;
                if($http_code == "200"){
                    $dataArr = explode('~',$server_output);
                    if($dataArr[5] == 'SUCCESS'){
                        $res = $dataArr[6];
                        $wallet_bal_arr=explode(':',$res);
                        $wallet_balance=$wallet_bal_arr[1];
                    }    
                }
            }else{
               echo 'Invalid Ip Address';
            }
        }else{
            echo 'Invalid Api User Credentials';
        }    
    }
    
    
    public function addBeneficary(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
            
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_ip = $data['4'];
            $id = $data['5'];
            $mobile = $data['6'];
            $name = $data['7'];
            $bank_acct = $data['8'];
            $ifsc = $data['9'];
            $bankcode = $data['10'];
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){ 
                    
                    $token = $this->token;
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06009~$token~$id~$mobile~$name~$bank_acct~$ifsc~$bankcode~NA";
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
                    $http_code = $info['http_code'];
                    if($http_code == "200"){
                       echo $server_output;
                    }else{
                        echo 'Unable To get Response';
                    }
                }else{
                   echo 'Invalid Ip Address';
                }
            }else{
                echo 'Invalid Api User Credentials';
            }
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }    
    }
    
    
    public function deleteBeneficiary(){
        
        $status = $this->service('DMT');
        if($status == 'active'){
        
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_ip = $data['4'];
            $id = $data['5'];
            $mobile = $data['6'];
            $bene_code = $data['7'];
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){ 
                    
            	    $token = $this->token;
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06013~$token~$id~$mobile~$bene_code~NA~NA";
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
                    
                    $http_code = $info['http_code'];
                    if($http_code == "200"){
                        echo $server_output;
                    }else{
                        echo "Unable To get Response";
                    }
                }else{
                   echo 'Invalid Ip Address';
                }
            }else{
                echo 'Invalid Api User Credentials';
            }
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }    
    }
    
    
    
    public function accountValidation(){
        
        $status = $this->service('DMT');
        if($status == 'active'){


            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $cus_id = $data['3'];
            $api_ip = $data['4'];
            $id = $data['5'];
            $mobile = $data['6'];
            $acctnumber = $data['7'];
            $bankcode = $data['8'];
            $adhar = $data['9'];
            $pan = $data['10'];
            
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){ 
                    
                    $result = $this->db->query("SELECT * FROM `user` ")->result_array();
                    $accountVerificationCharge = $result[0]['account_verify_charge'];
            
                    $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                    $txn_clbal = $trnx_data[0]['txn_clbal'];
                    
                    if($accountVerificationCharge <= $txn_clbal){
                    
                	    $token = $this->token;
                        $url= $this->base_url;
                        $data = "msg=E06031~$token~$id~$mobile~$acctnumber~$bankcode~$adhar~$pan~NA~NA";
                        
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
                            if($dataArr[5] == "SUCCESS"){
                                
                                $ip = $_SERVER['REMOTE_ADDR'];
                        		$time = time();
                        		$date = DATE('Y-m-d h:i:s');
                                $debit_amount = $accountVerificationCharge;
                                $new_txn_clbal = $txn_clbal - $accountVerificationCharge;
                                
                                $insert_data = array(
                                    'txn_agentid' => $cus_id,
                                    'dmt_txn_recrefid' => '0',
                                    'txn_opbal' => $txn_clbal,
                                    'txn_crdt' => '0',
                                    'txn_dbdt' => $debit_amount,
                                    'txn_clbal' => $new_txn_clbal,
                                    'txn_checktime' => $cus_id.time(),
                                    'txn_type' => 'Account Verification Charge',
                                    'txn_fromto' => '0',
                                    'txn_time' => $time,
                                    'txn_date' => $date,
                                    'txn_optional' => '0',
                                    'txn_ip' => $ip,
                                    'txn_comment' => '0',
                                    'transaction_id' => '0',
                                    'transaction_ref' => ''
                                );
                                $res=$this->db_model->insert_update('exr_trnx',$insert_data);  
                            }
                            echo $server_output;
                        }else{
                            echo 'Failed to responde';
                        }
                    }else{
                        echo 'Low Api Balance';
                    }
                }else{
                   echo 'Invalid Ip Address';
                }
            }else{
                echo 'Invalid Api User Credentials';
            }    
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
        }
        
    }
    
    
    
    public function moneyTransfer(){
        
        $status = $this->service('DMT');
        $package_amount =0;
        if($status == 'active'){
            
            $msg = $this->input->post('msg');
            $rec = $this->checkapiuser($msg);
            $data = explode('~',$msg);
            $api_user_id = $data['3'];
            $api_ip = $data['4'];
            $id = $data['5'];
            $mobile = $data['6'];
            $benificary_code = $data['7'];
            $amt = $data['8'];
            $trans_type = $data['9'];
            $adhar_no = $data['10'];
            $pan_no = $data['11'];
            $ifsc = $data['12'];
            $acct_no = $data['13'];
            
            if($rec){
                $ip = $rec[0]->cus_ip;
                if($ip == $api_ip){
                    $cus_id = $api_user_id;
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
                                if($cus_type == "api"){
                                    foreach($commission_scheme_package as $comm){
                                        $range = explode('-',$comm->slab);
                                         $minRange = $range[0];
                                         $maxRange = $range[1];
                                        if($amt >= $minRange && $amt <= $maxRange){
                                            $api_comm = $comm->api_comm;
                                            $comm_type = $comm->type;
                                            if($api_comm){
                                                if($comm_type == "percent"){
                                                    $commission_amount = round((($api_comm / 100) * $amt),3);
                                                }else{
                                                    $commission_amount = $api_comm;
                                                }
                                                $api_comm = $commission_amount;
                                            }  
                                            break; 
                                        }
                                    }
                                }
                            }else{
                                $api_comm = '0';
                            }    
                        }else{
                            $api_comm = '0';
                        }    
                    }else{
                        $api_comm = '0';
                    }
                    $totalamount = $amt + $api_comm;
                    
                    if($totalamount > $txn_clbal){
                        echo 'Insufficient Api Wallet Balance For Transaction.Please Contact Admin';
                    }else{
                        
                        $data = "msg=E06015~$token~$id~$mobile~$benificary_code~$amt~$trans_type~$adhar_no~$pan_no~NA~NA~NA";
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
                        //print_r($err);
                        $http_code = $info['http_code'];
                        //echo $server_output;exit;
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
                                
                                $cus_id = $api_user_id;
                                
                                $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                                $txn_clbal = $trnx_data[0]['txn_clbal'];
                                
                                $debit_amount = $package_amount + $amt;
                                $new_txn_clbal = $txn_clbal - $debit_amount;
                        		$ip = $_SERVER['REMOTE_ADDR'];
                        		$time = time();
                        		$date = DATE('Y-m-d h:i:s');
                        		
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
                                $this->creditDmtCommission($cus_id,$amt,$insert_id);
                                
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
                                
                                $cus_id = $api_user_id;
                                $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                                $txn_clbal = $trnx_data[0]['txn_clbal'];
                                
                                $debit_amount = $amt;
                                $new_txn_clbal = $txn_clbal - $debit_amount;
                        		$ip = $_SERVER['REMOTE_ADDR'];
                        		$time = time();
                        		$date = DATE('Y-m-d h:i:s');
                        		
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
                                
                                $cus_id = $api_user_id;
                                $data = $this->db->query("SELECT * FROM `dmt_commission_slab` as d,customers as c where d.dmt_comm_id = c.dmt_comm_id and cus_id = '".$cus_id."' ")->result_array();
                                $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$cus_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                                $txn_clbal = $trnx_data[0]['txn_clbal'];
                                if($data)$package_amount = $data[0]['amount']; else $package_amount=0 ;
                                $debit_amount = $package_amount + $amt;
                                $new_txn_clbal = $txn_clbal - $debit_amount;
                        		$ip = $_SERVER['REMOTE_ADDR'];
                        		$time = time();
                        		$date = DATE('Y-m-d h:i:s');
                        		
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
                            }
                            echo $server_output;
                        }else{
                            echo 'Unable To get Response';
                        }
                    }
                }else{
                    echo 'Invalid Ip Address';
                }
            }else{
                echo 'Invalid Api User Credentials';   
            }    
        }else{
            echo "DMT SERVICE IS TEMPORARY OFF";
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
                    if($cus_type == "api"){
                        foreach($commission_scheme_package as $comm){
                            $range = explode('-',$comm->slab);
                            $minRange = $range[0];
                            $maxRange = $range[1];
                            if($amount >= $minRange && $amount <= $maxRange){
                                $api_comm = $comm->api_comm; 
                                $comm_type = $comm->type;
                                if($api_comm){
                                    if($comm_type == "percent"){
                                        $commission_amount = round((($api_comm / 100) * $amount),3);
                                    }else{
                                        $commission_amount = $api_comm;
                                    }
                                    $last_balance = $this->db_model->get_trnx($cus_id);
                                    $this->db_model->insert_update('dmt_trnx', array('api_commission'=>$commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
            						if($last_balance)
            						{
            						    $this->db_model->insert_update('dmt_trnx',array('charge'=>$commission_amount),array('dmt_trnx_id'=>$dmt_txn_recrefid));
            						    
                						$txntype = "Api Dmt Charge";
                						$this->db_model->insert_update('exr_trnx', array('dmt_txn_recrefid'=>$dmt_txn_recrefid,'txn_agentid'=>$cus_id,'txn_date'	=>	date('Y-m-d h:i:s'),'txn_opbal'=>$last_balance[0]->txn_clbal, 'txn_crdt'=>'0', 'txn_dbdt'=>$commission_amount, 'txn_clbal'=>$last_balance[0]->txn_clbal - $commission_amount, 'txn_type'=>$txntype, 'txn_time'=>time(), 'txn_ip'=>$this->input->ip_address(), 'txn_comment'=>$txntype));
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
    */
    
    
    public function checkapiuser($msg){
        
        $data = explode('~',$msg);
        //print_r($data);
        $api_mobile = $data['1'];
        $api_pass = $data['2'];
        $api_user_id = $data['3'];
        $api_ip = $data['4'];
        $api_query = "select * from customers where cus_mobile = '".$this->encryption_model->encode($api_mobile)."' and cus_id = '".$api_user_id."' and cus_pass = '".$this->encryption_model->encode($api_pass)."'";
        $rec = $this->db_model->getAlldata($api_query);
        //echo $this->db->last_query();
        return $rec;
    }
    
//-----------------------------------------------------------------------END--DMT--API---------------------------------------------------------------------------------------//
    
    
    
//--------------------------------------------------------------------------PAN API-----------------------------------------------------------------------------------------//
  

    /*public function checkthis(){
      
      $curl = curl_init(); 
	   $url = "https://panmitra.com/api/add_vle.php?api_key=62f875-73c8ca-671fe6-8863c4-8ffd7f&vle_id=bharatpay90&vle_name=satmat shubh&vle_mob=7517549829&vle_email=test@gmail.com&vle_shop=test+satmat&vle_loc=pune&vle_state=15&vle_pin=422103&vle_uid=345645675678&vle_pan=EOPPP2056K";
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
         print_r($response);exit;
        curl_close($curl);
        
        $array = json_decode($response, true);
  }
    public function register_vle_id()
	{
        $api_mobile = $this->input->get('api_mobile');
        $api_pass = $this->input->get('api_pass');
        $api_user_id = $this->input->get('api_user_id');
        $api_ip = $this->input->get('api_reg_ip');
        $msg = "check~$api_mobile~$api_pass~$api_user_id~$api_ip";
        $rec = $this->checkapiuser($msg);
        
        
        $vle_id = $this->input->get('vle_id');
        $vle_name = $this->input->get('vle_name');
        $vle_mob = $this->input->get('vle_mob');
        $vle_email = $this->input->get('vle_email');
        $vle_shop = $this->input->get('vle_shop');
        $vle_loc = $this->input->get('vle_loc');
        $vle_state = $this->input->get('vle_state');
        $vle_pin = $this->input->get('vle_pin');
        $vle_uid = $this->input->get('vle_uid');
        $vle_pan = $this->input->get('vle_pan');
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){ 
                
        	    $curl = curl_init(); 
        	   // $vle_id=$vle_id;
        	    $api_key = $this->pan_api_key;
        	    $url = "https://panmitra.com/api/add_vle.php?api_key=$api_key&vle_id=$vle_id&vle_name=".urlencode($vle_name)."&vle_mob=$vle_mob&vle_email=$vle_email&vle_shop=".urlencode($vle_shop)."&vle_loc=$vle_loc&vle_state=$vle_state&vle_pin=$vle_pin&vle_uid=$vle_uid&vle_pan=$vle_pan";
                //echo $url;
                //$url = "https://panmitra.com/api/add_vle.php?api_key=62f875-73c8ca-671fe6-8863c4-8ffd7f&vle_id=bharatpay90&vle_name=satmat shubham&vle_mob=7517549829&vle_email=test@gmail.com&vle_shop=test satmat&vle_loc=pune&vle_state=15&vle_pin=422103&vle_uid=345644775678&vle_pan=EOPPP3456K";
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
                //print_r($info);exit;
                //print_r($response);exit;
                curl_close($curl);
                echo $response;
            }else{
                echo 'Invalid Register IP Address';
            }    
        }
        else{
            echo 'Invalid Api User Credentials';
        }   
	}
    public function vle_status()
	{
        $api_mobile = $this->input->post('api_mobile');
        $api_pass = $this->input->post('api_pass');
        $api_user_id = $this->input->post('api_user_id');
        $api_ip = $this->input->post('api_reg_ip');
        $msg = "check~$api_mobile~$api_pass~$api_user_id~$api_ip";
        $rec = $this->checkapiuser($msg);
        
        
        $cus_id = $this->input->post('vle_id');
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
                    	    $curl = curl_init(); curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://panmitra.com/api/vle_status.php", 
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "", 
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0, 
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => array('api_key' => '62f875-73c8ca-671fe6-8863c4-8ffd7f', 
                                                        'vle_id' => $cus_id), 
                                                        ));
                            $response = curl_exec($curl); 
                            curl_close($curl);
                            echo $response;
            }else{
                echo 'Invalid Register IP Address';
            }    
        }
        else{
            echo 'Invalid Api User Credentials';
        }   

	}
	public function buy_coupon()
	{
        $api_mobile = $this->input->post('api_mobile');
        $api_pass = $this->input->post('api_pass');
        $api_user_id = $this->input->post('api_user_id');
        $api_ip = $this->input->post('api_reg_ip');
        $msg = "check~$api_mobile~$api_pass~$api_user_id~$api_ip";
        $rec = $this->checkapiuser($msg);
        
        
        $cus_id = $this->input->post('vle_id');
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
                
                    $no_coupon=$_POST['noofcoupon'];
                    $coupon_type=$_POST['coupon_type'];
                    $amount=$_POST['amount'];
                    
            		    
            	    $curl = curl_init(); 
            	    curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://panmitra.com/api/coupon_req.php", 
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "", 
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0, 
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => array('api_key' => '62f875-73c8ca-671fe6-8863c4-8ffd7f',
                                                'vle_id' => $cus_id,
                                                'type' => $coupon_type,
                                                'qty' => $no_coupon),
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
                                    'pan_cus_id'=>$api_user_id,
                                    'type'=>$array['type'],
                                    'qty'=>$array['qty'],
                                    'rate'=>$array['rate'],
                                    'amount'=>$array['amount'],
                                    'old_bal'=>$array['old_bal'],
                                    'new_bal'=>$array['new_bal']
                                    );
                		$this->db_model->insert_update('coupon_buy',$record);
                		$inserId = $this->db->insert_id();
                		
                		$cus_id = $this->session->userdata('api_id');
                        $trnx_data = $this->db->query("SELECT * FROM `exr_trnx` where txn_agentid='".$api_user_id."' ORDER BY `txn_id` desc limit 1")->result_array();
                        $txn_clbal = $trnx_data[0]['txn_clbal'];
                        $newclosingbal=$txn_clbal-$amount;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        		$time = time();
                        		$date = DATE('Y-m-d h:i:s');
                        $insert_data = array(
                                        'txn_agentid' => $api_user_id,
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
                        $this->creditPanCommission($api_user_id,$amount,$inserId);
                        echo $response;
                    }
                    else{
                        echo $response;
                    }
            }else{
                echo 'Invalid Register IP Address';
            }    
        }
        else{
            echo 'Invalid Api User Credentials';
        }   
	}
	public function coupon_status()
	{
	    $api_mobile = $this->input->post('api_mobile');
        $api_pass = $this->input->post('api_pass');
        $api_user_id = $this->input->post('api_user_id');
        $api_ip = $this->input->post('api_reg_ip');
        $msg = "check~$api_mobile~$api_pass~$api_user_id~$api_ip";
        $rec = $this->checkapiuser($msg);
        
        
        $order_id = $this->input->post('order_id');
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
        	    $curl = curl_init(); curl_setopt_array($curl, array( 
                CURLOPT_URL => "https://panmitra.com/api/coupon_status.php",
                CURLOPT_RETURNTRANSFER => true, 
                CURLOPT_ENCODING => "", 
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0, 
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => array('api_key' => '62f875-73c8ca-671fe6-8863c4-8ffd7f','order_id' => $order_id),
                ));
                $response = curl_exec($curl); 
                curl_close($curl);
                echo $response;
            }else{
                echo 'Invalid Register IP Address';
            }    
        }
        else{
            echo 'Invalid Api User Credentials';
        }   

	}
	public function passwordresetVLEID()
	{
	     $api_mobile = $this->input->post('api_mobile');
        $api_pass = $this->input->post('api_pass');
        $api_user_id = $this->input->post('api_user_id');
        $api_ip = $this->input->post('api_reg_ip');
        $msg = "check~$api_mobile~$api_pass~$api_user_id~$api_ip";
        $rec = $this->checkapiuser($msg);
        
        
         $cus_id = $this->input->post('cus_id');
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
                
        	    $curl = curl_init(); curl_setopt_array($curl, array(
                CURLOPT_URL => "https://panmitra.com/api/pass_reset.php",
                CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", 
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0, 
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => array('api_key' => '62f875-73c8ca-671fe6-8863c4-8ffd7f','vle_id' => $cus_id),
                ));
                $response = curl_exec($curl); 
                curl_close($curl);
                echo $response;
            }else{
                echo 'Invalid Register IP Address';
            }    
        }
        else{
            echo 'Invalid Api User Credentials';
        }   

	}
	*/
	
    public function payoutRequest(){
        
        $msg = $this->input->post('msg');
        $rec = $this->checkapiuser($msg);
        $data = explode('~',$msg);
        $api_ip = $data['4'];
        $cus_id = $data['3'];
        $TXNTYPE = $data['5'];
        $UNIQUEID = $data['6'];
        $CREDITACC = $data['7'];
        $IFSC = $data['8'];
        $amount = $data['9'];
        $PAYEENAME = $data['10'];
        $bankName = $data['11'];
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
                
    	        $cus_aeps_bal_data = $this->Aeps_model->aepsBalance($cus_id);
    	        if($cus_aeps_bal_data) $cus_aeps_bal = $cus_aeps_bal_data[0]->aeps_txn_clbal; else $cus_aeps_bal = 0;
    	        $charge = $this->getPayoutChargeApi($amount,$cus_id);
    	        if($cus_aeps_bal >= ($amount+$charge)){
        	        $data = array(
                        'cus_id' => $cus_id,
                        'bankName' => $bankName,
                        'bankAccount' => $CREDITACC,
                        'bankIFSC' => $IFSC,
                        'accountHolderName' => $PAYEENAME,
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
                		$date = DATE('Y-m-d h:i:s');
                		$total = $cus_aeps_bal - $txn_dbdt;
                		
                		$lasttxnrec = $this->db_model->insert_update('aeps_exr_trnx',array('aeps_txn_agentid'=>$cus_id,'aeps_txn_opbal'=>$cus_aeps_bal,'aeps_txn_crdt'=>'0','aeps_txn_dbdt'=>$txn_dbdt,'aeps_txn_clbal'=>$total,'aeps_txn_type'=>$txntype,'aeps_txn_comment'=>$txntype));
                		//echo "Redeem Request Submitted Successfully";
                		$r=array('STATUS'=>'pending','MSG'=>'Redeem Request Submitted Successfully');
                        echo json_encode($r);
    
                    }else{
                        echo "Failed to Submit Redeem Request";
                    }
    	        }else{
    	            echo "Insufficient Balance";
                }
            }else{
                echo 'Invalid Register IP Address';
            }
        }else{
            echo 'Invalid Api User Credentials';
        }    
        
    }
    
    
    public function getPayoutChargeApi($amount,$cus_id){
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
                            $api_comm = $comm->api_comm; 
                            $comm_type = $comm->type;
                            if($api_comm){
                                if($comm_type == "percent"){
                                    $commission_amount = round((($api_comm / 100) * $amount),3);
                                }else{
                                    $commission_amount = $api_comm;
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
    
    
    public function payoutStatusCheck(){
        
        $msg = $this->input->post('msg');
        $rec = $this->checkapiuser($msg);
        $data = explode('~',$msg);
        $api_ip = $data['4'];
        $cus_id = $data['3'];
        $UNIQUEID = $data['5'];
        
        if($rec){
            $ip = $rec[0]->cus_ip;
            if($ip == $api_ip){
                $payoutDetails = $this->db_model->getWhere('payout_request',array('UNIQUEID'=>$UNIQUEID));
                if($payoutDetails[0]->status == "REJECTED"){
                    $r=array('STATUS'=>'REJECTED','MSG'=>'Payout Request is Rejected');
                    echo json_encode($r);
                }elseif($payoutDetails[0]->status == "SUCCESS"){
                    $UTRNUMBER = $payoutDetails[0]->UTRNUMBER;
                    $r=array('STATUS'=>'SUCCESS','UTRNUMBER'=>$UTRNUMBER,'UNIQUEID'=>$UNIQUEID,'MSG'=>'Payout Request is accepted successfully');
                    echo json_encode($r);
                }
                else{
                    $r=array('STATUS'=>$payoutDetails[0]->status,'MSG'=>"Payout Request is $payoutDetails[0]->status");
                    echo json_encode($r);
                }
            }else{
                echo 'Invalid Register IP Address';
            }
        }else{
            
            echo 'Invalid Api User Credentials';
        }
    }
    
}	   
