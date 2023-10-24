<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
class Admin_login extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('rec_model');
		$this->load->model('db_model');
		$this->load->model('encryption_model');
		$this->load->model('email_model');
		$this->load->model('msg_model');
		
	}

    /*public function thisNotUse(){
      echo  $this->encryption_model->decode('zxchjkl;');//125191138
    }*/

    public function hacking(){
        echo "<body><div style='margin-top: 13%';>
                <h1 style='text-align: center;color:red;font-weight: 600;'>Hacking Attempt...!!!</h1>
	            <h3 style='text-align: center;color:red;font-weight: 600;'>You Are In Trouble Now,</h3>
	            <h3 style='text-align: center;color:red;font-weight: 600;'>We Have Noted Your IP, Photo, Location, Device ID And All...!!!</h3>
	            <h3 style='text-align: center;color:red;font-weight: 600;'>This Information IS Send To Cyber Police Pune</h3>
	            <h3 style='text-align: center;color:red;font-weight: 600;'>Cyber Police As Well As Our Team Is Running Behind You.</h3>
	            <h3 style='text-align: center;color:red;font-weight: 600;'>See You Soon.</h3>
	            <h4 style='text-align: center;'>OR</h4>
	            <h4 style='text-align: center;'>Surrender Your Self @ security@satmatgroup.com</h4>
            <div></body>";
		die();
    }


	public function index()
	{

        if($this->session->userdata('isLogin123') == TRUE)
		{
		    redirect('/admin/login', 'refresh');
		}else
        {
            $this->data =  (object)$this->input->get();
		    if(isset($this->data->seckey)){
		        //echo "data here";
    		    $key = $this->data->seckey;
    		    $dt = $this->db_model->getSecretKey();
    		    
    		    if($dt){
                    $dbkey = $dt[0]->seckey;
                }else{
                    $dbkey = '';
                }if($key != $dbkey)
                {
                    redirect('admin_login/hacking');
                }else{
                    if(isset($_POST['username'])){
                        //echo "here";exit;
                        // echo $this->encryption_model->decode('cd046b6b645188816b5621bee54aa2c1');exit;
                        // echo $this->encryption_model->decode('5f26dd1421765a44ed5156eddf52ba64');exit;
                        $this->form_validation->set_rules('username','Username','required');
                		$this->form_validation->set_rules('password','Password','required');
                
                		if($this->form_validation->run() == TRUE)
                		{
                		    $ip = $this->input->ip_address();
                			$username = $this->input->post('username');
                		    $passw = $this->input->post('password');
                			
                		    $this->db_model->insert_update('inputdata',array('function_name'=>'admin_login-form_login','inputs'=>$username.','.$passw,'ip'=>$ip,));
                			$password = $this->encryption_model->encode($passw);
                			$data['r'] = $this->db_model->getwhere('user',array('username'=>$username,'password'=>$password));
                			
                		    if(!empty($data['r']))
                
                			{
                			    
                                    $otp = rand(100000,999999); 
                                    $mobile = $this->encryption_model->decode($data['r'][0]->mobile);
                        		    $email = $data['r'][0]->email;
                        		    $name = strtoupper($data['r'][0]->name);
                        		    
                        		    $msg= "Dear admin ". $otp." is your OTP.";
                        		    $sub="Login OTP From Recharge";
                        		    $to='shubham@satmatgroup.com';//$email;//
                        		    
                        		    $r=$this->email_model->send_email($to,$msg,$sub);
                        		    $this->msg_model->sendsms($mobile,$msg,'1707161018221759352');
                         	    
                					$this->session->set_userdata('otp', $otp);
                			        $this->session->set_userdata('username',$username);
                                    $this->session->set_userdata('email',$email);
                                    $this->session->set_userdata('adminMobile',$mobile);
                			        $this->session->set_userdata('name',$data['r'][0]->name);
                			        $this->session->set_userdata('level',$data['r'][0]->level);
                			        $this->session->set_userdata('user_id',$data['r'][0]->user_id);
                
                					$this->db_model->insert_update('user',array('status'=>1,'loggin_status'=>'loggedin'),array('user_id'=>$data['r'][0]->user_id));
                                    
                					$this->db_model->insert_update('exr_ad_logs',array('log_ip'=>$ip,'message'=>'Logged In Successfully','log_intime'=>date('Y-m-d H:s:i')));
                
                				  	//redirect('admin_login/verifyotp');
                					
                	            $this->session->set_userdata('isLoginAdmin', TRUE);
                	            redirect('/admin');
                				
                			}
                
                			else
                
                			{
                			    $this->db_model->insert_update('exr_ad_logs',array('log_ip'=>$ip,'message'=>"Log In Attempt with Username:$username And Password:$password"));
                                $attempts = $this->check_login_attempts();
                                if($attempts)
                                {
                                    $data = [
                                        'password' => $this->encryption_model->encode(rand ( 1000000 , 9999999 )) 
                                    ];
                                    $this->db->where('user_id','6');
                                    $update = $this->db->update('user',$data);
                                    if($update){
                                        $datamsg ='<font color=red>Please Reset Your Password! You have Reached Your Login Attempts Limit! Please Contact Admin...</font>'; 
                                    }else{
                                        $datamsg ='<font color=red>Invalid username or password...</font>';
                                    }
                                }
                                else{
                                    $datamsg ='<font color=red>Invalid username or password...</font>';
                                }
                                
                                $this->session->set_flashdata('success',$datamsg);
                	            redirect($this->agent->referrer());
                
                			}
                
                		}
                
                		else
                
                		{
                
                			$datamsg ='<font color=red>Please enter username and password...</font>';
                			$this->session->set_flashdata('error',$datamsg);
                            redirect($this->agent->referrer());

                		}
                    }else{
    		            $this->load->view('admin/index');
                    }
    		    }
    		}else{redirect('admin_login/hacking');}
        }

	}

	

	public function form_login()
    {   
        redirect('admin/index');
  	}
  	
  	
  	public function check_login_attempts(){
        $i=0;
        $query = $this->db->select('*')->from('exr_ad_logs')->order_by('ad_log_id','DESC')->limit('2')->get();
        //echo $this->db->last_query();
        if($query->num_rows() > 0){
            $data = $query->result();
            foreach($data as $rec){
                if(strpos(($rec->message), 'Log In Attempt with')){
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

    public function verifyotp(){
	    $this->load->view('admin/verifyotp');
	    
	    $otp = $this->session->userdata('otp');
	    if(isset($_POST['verify'])){
	        $enteredotp = $this->input->post('enteredotp');
	        //echo "$enteredotp $otp";exit;
	        if($enteredotp == $otp){
	            $data = [
                        'loggin_status' => 'loggedin' 
                    ];
                    $this->db->where('user_id','6');
                    $update = $this->db->update('user',$data);
	            $this->session->set_userdata('isLoginAdmin', TRUE);
	            redirect('/admin');
	        }else{
	           
	            echo "<script type='text/javascript'>alert('Entered OTP is Invalid');</script>";
	        }
	    }
	}
	
	public function resendotp(){
        
        
        $otp=rand(100000,999999); 
        $this->session->set_userdata('otp', $otp);
        
	    $mobile = $this->session->userdata('adminMobile');
	    $email = $this->session->userdata('email');
	    $name = strtoupper($this->session->userdata('name'));
	
	    $msg= $name.", Your One Time OTP Is ".$otp;
	    $sub="Login OTP From Recharge";
	    $to=$email;
	    
	    $r=$this->email_model->send_email($to,$msg,$sub);
	    $this->msg_model->sendsms($mobile,$msg);
		
		redirect('admin_login/verifyotp');
        
    }
    
    public function forget_password(){
        $this->load->view('admin/forget-password');
        if(isset($_POST['username'])){
            $mobile = $this->encryption_model->encode($_POST['username']);
            $data['r'] = $this->db_model->getwhere('user',array('mobile'=>$mobile));
		    if(!empty($data['r']))
			{
			    $otp = rand(100000,999999); 
                $mobile = $this->encryption_model->decode($data['r'][0]->mobile);
    		    $email = $data['r'][0]->email;
    		    $name = strtoupper($data['r'][0]->name);
    		    
    		    $msg= $name.", Your One Time Password For Forget Password Is ".$otp;
    		    $sub="Forget Password";
    		    $to=$email;
    		    
    		    $r=$this->email_model->send_email($to,$msg,$sub);
    		    $this->msg_model->sendsms($mobile,$msg);
     	    
				$this->session->set_userdata('forget_otp', $otp);
		        $this->session->set_userdata('forget_username',$username);
                $this->session->set_userdata('forget_email',$email);
                $this->session->set_userdata('forget_adminMobile',$mobile);
                $this->session->set_userdata('forget_name',$data['r'][0]->name);
                
                redirect('admin_login/verify-password-otp');
                
			}else{
			    $this->session->set_flashdata('error','Entered Mobile Number is Invalid ');
			    redirect('admin_login/forget-password');
			}
        }
    }
    
    public function verify_password_otp(){
        $this->load->view('admin/verify-password-otp');
	    $otp = $this->session->userdata('forget_otp');
	    if(isset($_POST['verify'])){
	        $enteredotp = $this->input->post('enteredotp');
	        //echo "$enteredotp $otp";exit;
	        if($enteredotp == $otp){
	            $pass = rand(11111, 99999);
                $mobile = $this->encryption_model->encode($this->session->userdata('forget_adminMobile'));
                $this->db_model->insert_update('user',array('password'=>$this->encryption_model->encode($pass)),array('mobile'=>$mobile));
                
                $mobile = $this->session->userdata('forget_adminMobile');
        	    $email = $this->session->userdata('forget_email');
        	    $name = strtoupper($this->session->userdata('forget_name'));
        	
        	    $msg= $name.", Your New Password For Login Is ".$pass;
            	$sub="Password Reset Successfull";
        	    $to=$email;
        	    
        	    $r=$this->email_model->send_email($to,$msg,$sub);
        	    $this->msg_model->sendsms($mobile,$msg);
                
                $this->session->set_flashdata('success','New Password is Sent on Registered Mobile and Email Id.');
	            redirect('admin_login/form_login');
	        }else{
	           
	            $this->session->set_flashdata('error','Entered OTP is Invalid');
			    redirect('admin_login/verify-password-otp');
	        }
	    }
    }
    
    public function resend_pass_otp(){
        
        
        $otp=rand(100000,999999); 
        $this->session->set_userdata('forget_otp', $otp);
        
	    $mobile = $this->session->userdata('forget_adminMobile');
	    $email = $this->session->userdata('forget_email');
	    $name = strtoupper($this->session->userdata('forget_name'));
	
	    $msg= $name.", Your One Time Password For Forget Password Is ".$otp;
    	$sub="Forget Password";
	    $to=$email;
	    
	    $r=$this->email_model->send_email($to,$msg,$sub);
	    $this->msg_model->sendsms($mobile,$msg);
	    
	    $this->session->set_flashdata('success','OTP Successfully Sent On Registered Mobile and Email.');

		redirect('admin_login/verify-password-otp');
        
    }
    
    public function whatsbot() {
        $uri = $_SERVER["REQUEST_URI"];
        $uriArray = explode('?', $uri);
        $page_url = $uriArray[1];

        $uriArray1 = explode('&', $page_url);
        $u1 = $uriArray1[0];
        $u2 = $uriArray1[1];
        $u3 = $uriArray1[2];

        $uri1 = explode('=', $u1);
        $uri2 = explode('=', $u2);
        $uri3 = explode('=', $u3);
        echo $uri1[1];
        echo $uri2[1];
        echo $uri3[1];
        $mobile_no = $uri1[1];
        $message = $uri2[1];
        
        $text_count1 = explode('+', $message);
        $text_count = substr_count($message, " ") + 1;

        $mob = substr($mobile_no, 2);
        $mobile_no1 = $this->encryption_model->encode($mob);
        $query_db = $this->db->query("select * from customers where cus_mobile= '$mobile_no1'")->result();
        foreach ($query_db as $qd) {
                        $cus_name = strtoupper($qd->cus_name);
                        $cus_pass = $this->encryption_model->decode($qd->cus_pass);
                        $cus_pin = $this->encryption_model->decode($qd->cus_pin);
                    }
        
        if ($query_db) {
            if ($message == "1") {
                $text1 = "Please+enter+your+operator+name,+mobile+no.+and+amount%0D%0A%0A+e.g.+RC+airtel+9988776655+199%0A+e.g.+RC+jio+9988776655+199%0A+e.g.+RC+VI+9988776655+199%0A+e.g.+RC+BSNL+9988776655+199";
            } elseif ($text_count1[0] == 'RC') {
                $recharge_details = explode('+', $message);
                
                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]{10}$/", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$recharge_details[1]' and opsertype='mobile'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile= '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'whatsapp';

                    $complete = $this->rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['recid'];
                        if($msg=="Low Balance."){
                            $text1="Your+recharge+is+failed+due+to+insufficient+balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = "Your+recharge+is+succefull%0AMobile+no-+$mobileno%0AAmount-+$amt%0ATransaction+ID-$tran_id";
                        }else{
                            $text1="Your+recharge+is+failed+please+try+after+some+time(".str_replace(' ','+', $msg).")";
                        }
                    } else {
                        $text1 = "Recharge+not+succefull";
                    }
                    
                } else {
                    $text1 = "please+enter+correct+details%0D%0A+e.g.+RC+airtel+9988776655+199";
                }
            } elseif ($message == 2) {
                $text1 = "Please+enter+your+DTH+operator+code,+DTH+no+and+amount+%0A%0A+e.g.+DTH+D1+65646576+200%0A%0ACodes+for+operator%0D%0AD1=+Videocon%0D%0AD2=+Tatasky%0D%0AD3=+DishTv%0D%0AD4=+Sundirect%0D%0AD5=+Airtel+Dth%0D%0AD6=+Reliance+BigTv";
            }   elseif ($text_count1[0] == 'DTH') {
                $recharge_details = explode('+', $message);
                $rrr = $recharge_details[1];
                if ( $rrr[0]=='D' && preg_match("/^[0-9]/i", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    
                    if($recharge_details[1] == "D1"){
                        $operatorname = "Videocon";
                    }   elseif($recharge_details[1] == "D2"){
                        $operatorname = "TataSky";
                    }elseif($recharge_details[1] == "D3"){
                        $operatorname = "Dishtv";
                    }elseif($recharge_details[1] == "D4"){
                        $operatorname = "Sundirect";
                    }elseif($recharge_details[1] == "D5"){
                        $operatorname = "Airteldth";
                    }elseif($recharge_details[1] == "D6"){
                        $operatorname = "Reliance BIGTV";
                    }else{
                        $operatorname = "invalid";
                    }
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='dth'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile= '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'whatsapp';

                    $complete = $this->rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['recid'];
                        if($msg=="Low Balance."){
                            $text1="Your+recharge+is+failed+due+to+insufficient+balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = "Your+DTH+recharge+is+succefull%0ADTH+no-+$mobileno%0AAmount-+$amt%0ATransaction+ID-+$tran_id";
                        }else{
                            $text1="Your+recharge+is+failed+please+try+after+some+time(".str_replace(' ','+', $msg).")";
                        }
                    } else {
                        echo "DTH+recharge+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details+dth%0A%0A+e.g.+DTH+Tatasky+65646576+200";
                }
        }    elseif ($message == 3) {
                $text1 = "Please+enter+your+operator+code,+consumer+no+and+amount+%0A%0A+e.g.+ELC+E101+65646576+200%0A%0ACodes+for+operator%0D%0AE101=+Mahavitaran-Maharashtra+State+Electricity+Distribu%0D%0AE102=+India+Power+Corporation+Ltd.+(Asansol)%0D%0AE103=+SNDL+NAGPUR%0D%0AE104=+Adani+Electricity%0D%0AE105=+Tata+Power+-Mumbai%0D%0AE106=+Brihan+Mumbai+Electric+Supply+and+Transport+Undertaking";
            }   elseif ($text_count1[0] == 'ELC') {
                
                $recharge_details = explode('+', $message);

                if (preg_match("/^[A-Z0-9]/i", $recharge_details[1]) && is_numeric($recharge_details[2]) && is_numeric($recharge_details[3])) {
                    if($recharge_details[1] == "E101"){
                        $operatorname = "Mahavitaran-Maharashtra State Electricity Distribution ";
                    }   elseif($recharge_details[1] == "E102"){
                        $operatorname = "India Power Corporation Ltd. (Asansol)";
                    }   elseif($recharge_details[1] == "E103"){
                        $operatorname = "SNDL NAGPUR";
                    }   elseif($recharge_details[1] == "E104"){
                        $operatorname = "Adani Electricity";
                    }   elseif($recharge_details[1] == "E105"){
                        $operatorname = "Tata Power -Mumbai";
                    }   elseif($recharge_details[1] == "E106"){
                        $operatorname = "Brihan Mumbai Electric Supply and Transport Undertaking ";
                    }   else{
                        $operatorname = "invalid";
                    }
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='ELECTRICITY'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile= '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'whatsapp';

                    $complete = $this->rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        if($msg=="Low Balance."){
                            $text1="Your+electricity+Bill+is+failed+due+to+insufficient+balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = "Your+electricity+Bill+is+succefull%0AConsumer+no-+$mobileno%0AAmount-+$amt%0ATransaction+ID-$tran_id";
                        }else{
                            $text1="Your+electricity+Bill+is+failed+please+try+after+some+time";
                        }
                    } else {
                        $text1 = "Electricity+bill+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details+of+Electricity%0A%0A+e.g.+ELC+E101+65646576+200";
                }
            }   elseif ($message == 4) {
                $text1 = "Please+enter+your+Mobile+no,+Operator+and+amount+%0A%0Ae.g.+POST+P1+9988776655+199%0A%0AOperator+Codes%0A+P1+=+BSNL+Postpaid%0A+P2=+Airtel+Postpaid%0A+P3+=+Idea+Postpaid%0A+P4+=+Vodafone+Postpaid%0AP5+=+JIO+Postpaid";
            }   elseif ($text_count1[0] == 'POST') {

                $recharge_details = explode('+', $text);
                $rrr = $recharge_details[1];
                if ( $recharge_details[1]== "POST" ) {
                    if($text_count1[1] == "P1"){
                        $operatorname = "BSNL Postpaid";
                    }   elseif($recharge_details[1] == "P2"){
                        $operatorname = "Airtel Postpaid";
                    }   elseif($recharge_details[1] == "P3"){
                        $operatorname = "Idea Postpaid";
                    }   elseif($recharge_details[1] == "P4"){
                        $operatorname = "Vodafone Postpaid";
                    }   elseif($recharge_details[1] == "P5"){
                        $operatorname = "JIO Postpaid";
                    }   else{
                        $operatorname = "invalid";
                    }
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='postpaid'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile = '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'Telegram';

                    $complete = $this->Rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['recid'];
                        
                        if($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = 'Your+postpaid+bill+is+success%0ATransaction+id='.$tran_id.'%0AAmount='.$amt.'%0AMobile No='.$mobileno;
                        }elseif($msg=="Low Balance."){
                            $text1="Your+recharge+is+failed+due+to+insufficient+balance.";
                        }else{
                            $text1="Your+recharge+is+failed+please+try+after+some+time";
                        }
                        
                    } else {
                        echo "recharge+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details%0A%0Ae.g.+POST+P1+9988776655+199";
                }
             
        }   elseif ($message == 5) {
                $query = $this->db->query("select cus_id from customers where cus_mobile= '$mobile_no1'")->result();
                foreach ($query as $q) {
                    $cus_id = $q->cus_id;
                }

                $query1 = $this->db->query("select txn_clbal from exr_trnx where txn_agentid = '$cus_id' order by txn_id desc limit 1")->result();
                if ($query1) {
                    foreach ($query1 as $q1) {
                        $txn_clbal = round($q1->txn_clbal, 2);
                    }
                } else {
                    $txn_clbal = 0;
                }

                $text1 = "Your+current+account+balance+is+". $txn_clbal;
            }   elseif ($message == 6) {
                    $text1 = "Scan+and+pay+on+below+QR+code+to+add+money+in+your+wallet";
            }   elseif ($message == 7) {
                $query = $this->db->query("select cus_id from customers where cus_mobile= '$mobile_no1'")->result();
                foreach ($query as $q) {
                    $cus_id = $q->cus_id;
                }
                $query1 = $this->db->query("select * from exr_trnx where txn_agentid = '$cus_id' order by txn_id DESC limit 15")->result();
                $text1 = "TRANSACTION+DETAILS%0D%0AID+||+TXN_TYPE+||+TXN_DATE+||+OP_BAL+||+CRDT+||+DBT+||+CL_BAL%0D%0A";
                foreach ($query1 as $q1) {
                    $txn_id = $q1->txn_id;
                    $txn_type = $q1->txn_type;
                    $txn_date = $q1->txn_date;
                    $txn_clbal = round($q1->txn_clbal, 2);
                    $txn_crdt = round($q1->txn_crdt, 2);
                    $txn_dbdt = round($q1->txn_dbdt, 2);
                    $txn_opbal = round($q1->txn_opbal, 2);
                    $text1 .= str_replace(' ','+', $txn_id)."||".str_replace(' ','+', $txn_type)."||".str_replace(' ','+', $txn_date)."||".str_replace(' ','+', $txn_opbal)."||".str_replace(' ','+', $txn_crdt)."||".str_replace(' ','+', $txn_dbdt)."||".str_replace(' ','+', $txn_clbal)."%0D%0A";
                }
                
            }   elseif ($message == 8) {
                $text1 = "You+Password+is%0D%0A".$cus_pass;
            }   elseif ($message == 9) {
                $text1 = "You+pin+is%0D%0A".$cus_pin;
            }   elseif ($text == 10) {
                $text1 = 'Welcome+to+Easypayall+%0D%0AHow+can+I+help+you+today,+$cus_name+%0A%0A1+Mobile+Recharge%0D%0A2+DTH+Recharge%0D%0A3+Electricity+bill+payment%0D%0A4+Postpaid+Bill+Payment%0D%0A5+Check+Account+Balance%0D%0A6+Add+Money%0D%0A7+Account+History%0D%0A8+Forgot+password%0D%0A9+Forgot+pin%0A10+Main+Menu%0A%0APlease+enter+the+option+number+to+proceed+(Ex.3)';
            }   else {
                $text1 = "Welcome+to+Easypayall+%0D%0AHow+can+I+help+you+today,+$cus_name+%0A%0A1+Mobile+Recharge%0D%0A2+DTH+Recharge%0D%0A3+Electricity+bill+payment%0D%0A4+Postpaid+Bill+Payment%0D%0A5+Check+Account+Balance%0D%0A6+Add+Money%0D%0A7+Account+History%0D%0A8+Forgot+password%0D%0A9+Forgot+pin%0A10+Main+Menu%0A%0APlease+enter+the+option+number+to+proceed+(Ex.3)";
            }
        } else {
            $text1 = "Welcome+to+Easypayall%0A%0AYou+are+not+registered+user+of+Easypayall%0A%0ARegister+on+below+link+to+use+services%0A%0Ahttps://easypayall.in/";
        }
        $url = "http://whatsbot.tech/api/send_sms?api_token=7775853d-7bb9-403f-ae02-4e57ec8c697a&mobile=$mobile_no&message=$text1";

        $response = file_get_contents($url);
        // print_r($response);
    }

}	   
