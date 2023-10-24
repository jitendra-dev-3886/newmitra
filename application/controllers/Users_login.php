<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Users_login extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model(array('db_model','encryption_model','email_model','msg_model','Aeps_model','Icici_model'));
    }
    public function index()
    {
        if(isset($_POST['login']))
        {   
            $ip = $this->input->ip_address();
            $mobile = trim($this->input->post('mobile'));
  		  	$password = $this->input->post('password');
  			$location = $this->input->post('location');
  	    	$user_type = $this->input->post('user_type');
            $this->db_model->insert_update('inputdata',array('function_name'=>$user_type,'inputs'=>$mobile.','.$password,'ip'=>$ip,'location'=>$location));
            $mob = $this->encryption_model->encode($mobile);
            $pass = $this->encryption_model->encode($password);
            $r = $this->db_model->login('customers',array('cus_mobile'=>$mob,'cus_pass'=>$pass,'cus_type'=>$user_type,'web_login_status'=>'loggedout'));
            //print_r($r);exit;
            if($r)
            {
                $cus_id=$r[0]->cus_id;
                $message='Login Details- '.$mobile.' and '.$password; 
                $data=array('log_user'=>$cus_id,'log_type'=>$user_type,'log_ip'=>$ip,'medium'=>'Web','message'=>$message,'log_intime'=>date('Y-m-d H:i:s'));
                //print_r($data);exit;
                $this->db_model->insert_update('exr_log',$data);
                /*
                $updata=array('cus_status'=>'1');
                $this->db_model->insert_update('customers',$updata,array('cus_id'=>$cus_id));*/
                
                
                
                
                $otp = rand(100000,999999); 
                $email = $r[0]->cus_email;
        		$name = strtoupper($r[0]->cus_name);
        	    $msg= $name.", Your Login OTP Is ".$otp;
        		$sub="Login OTP From Recharge";
        		//$to=$email;
        		//$this->email_model->send_email($to,$msg,$sub);
        		$smsdata=array('otp'=>$otp,'mobile'=>$mobile,'name'=>$name);
        	     $this->msg_model->otp_reg_app($smsdata);
         	    $this->session->set_userdata('otp', $otp);
         	    
         	    if($user_type=='distributor')
         	    {
                    $newdata1 = array(
                        'dis_id'  => $r[0]->cus_id,
                        'dis_name'  => $r[0]->cus_name,
                        'dis_type'  => $r[0]->cus_type,
                        'dis_email'  => $r[0]->cus_email,
                        'dis_mobile' => $mobile,
                      );
                    $this->session->set_userdata($newdata1);
                    redirect('distributor/otp');
                    
         	    }
         	    else if($user_type=='retailer')
         	    {
                    $newdata1 = array(
                    'cus_id'  => $r[0]->cus_id,
                    'cus_name'  => $r[0]->cus_name,
                    'cus_type'  => $r[0]->cus_type,
                    'cus_email'  => $r[0]->cus_email,
                    'cus_mobile' => $mobile,
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                      );
                    $this->session->set_userdata($newdata1);
                    
                 // $this->session->set_userdata('isLoginRetailer',TRUE);
                  redirect('Retailer/otp');
         	    }
         	    else if($user_type=='master')
         	    {
                    $newdata1 = array(
                    'mas_id'  => $r[0]->cus_id,
                    'mas_name'  => $r[0]->cus_name,
                    'mas_type'  => $r[0]->cus_type,
                    'mas_email'  => $r[0]->cus_email,
                    'mas_mobile' => $mobile,
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                      );
                    $this->session->set_userdata($newdata1);
                    redirect('master/otp');
         	    }
         	    else if($user_type=='api')
         	    {
                    $newdata1 = array(
                    'api_id'  => $r[0]->cus_id,
                    'api_name'  => $r[0]->cus_name,
                    'api_type'  => $r[0]->cus_type,
                    'api_email'  => $r[0]->cus_email,
                    'api_mobile' => $mobile,
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                      );
                    $this->session->set_userdata($newdata1);
                    redirect('api_partner/otp');
         	    }
         	    else{
         	         $this->session->set_flashdata('error','Invalid User Type');
                    redirect('users_login');
         	    }
            }
            else{
                 $this->session->set_flashdata('error','Invalid Login Details or Already Logged IN');
                redirect('users_login');
            }
        }
        else{
            $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
            $this->load->view('login/index',$data);
        }
        

	}
	public function forget_password()
     { 
        if(isset($_POST['forget_password']))
        {   
            $ip = $this->input->ip_address();
            $mobile = trim($this->input->post('mobile'));
  			$location = $this->input->post('location');
  			$user_type = $this->input->post('user_type');
            $this->db_model->insert_update('inputdata',array('function_name'=>$user_type.' forget password','inputs'=>$mobile,'ip'=>$ip,'location'=>$location));
            $mob = $this->encryption_model->encode($mobile);
            $r = $this->db_model->login('customers',array('cus_mobile'=>$mob,'cus_type'=>$user_type));
            if($r)
            {
                $cus_id=$r[0]->cus_id;
                $message='Forget Password Details- '.$mobile;
                $data=array('log_user'=>$cus_id,'log_type'=>$user_type,'log_ip'=>$ip,'medium'=>'Web','message'=>$message,'log_intime'=>date('Y-m-d H:i:s'));
                $this->db_model->insert_update('exr_log',$data);
                $updata=array('cus_status'=>'1');
                $this->db_model->insert_update('customers',$updata,$cus_id);
                
                $otp = rand(100000,999999); 
                $email = $r[0]->cus_email;
        		$name = strtoupper($r[0]->cus_name);
        		$msg= $name.", Your Forget Password OTP Is ".$otp;
        		$sub="Forget Password OTP From Recharge";
        		$to=$email;
        		$this->email_model->send_email($to,$msg,$sub);
        		$smsdata=array('otp'=>$otp,'mobile'=>$mobile);
        		$this->msg_model->otp_reg_app($smsdata);
         	    $this->session->set_userdata('otp', $otp);
         	    $this->session->set_userdata('cus_id', $cus_id);
         	    $this->session->set_userdata('mobile', $mobile);
         	    $this->session->set_userdata('mail', $email);
         	    $this->session->set_flashdata('success','OTP Send on Register Mobile And Email');
                redirect('users_login/forget_password_otp');
            }
            else{
                 $this->session->set_flashdata('error','Invalid Mobile Number');
                redirect('users_login/forget_password');
            }
        }
        else{
            $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
            $this->load->view('login/forget_password',$data);
        }
        

	}
	public function forget_password_otp()
    {
        if(isset($_POST['checkotp']))
        {  
            $enteredotp = $this->input->post('otp');
            $otp = $this->session->userdata('otp');
            if($enteredotp == $otp){
                $new_pass=rand(000000,999999);
                $record=array(
    				            'cus_pass'=>$this->encryption_model->encode($new_pass)
    				        );
    			$r=$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('cus_id')));
    			if($r){
    			    $mobile=$this->session->userdata('mobile');
         	        $to=$this->session->userdata('mail');
            		$msg= ", Your New Password Is ".$new_pass;
            		$sub="New Password From Recharge";
            		$this->email_model->send_email($to,$msg,$sub);
            		$smsdata=array('pass'=>$new_pass,'mobile'=>$mobile);
            		$this->msg_model->forget_pass($smsdata);
    			}
                $this->session->set_flashdata('success','Please Check Your Mail And Register Mobile For New Password');
                redirect('users_login');
            }
            else{
                $this->session->set_flashdata('error','Please Enter Valid OTP');
                redirect('users_login/forget_password_otp');
            }
        }
        else{
            $this->load->view('distributor/otp');
        }
    }
}