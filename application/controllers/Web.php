<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Web extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('db_model','encryption_model','msg_model','email_model'));
    }

	public function index()
	{
	    $data['link1'] = $this->db_model->getwhere('user_links',array('id'=>'1'));
	    $data['link2'] = $this->db_model->getwhere('user_links',array('id'=>'2'));
	    $data['link3'] = $this->db_model->getwhere('user_links',array('id'=>'3'));
	    $this->load->view('index',$data);
	}
	
	public function signup()
	{
	    $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
	    $this->load->view('signup',$data);
	}
	
	public function signupsuccess()
	{
	    $name = $this->input->post('name');
	    $email = $this->input->post('email');
	    $address = $this->input->post('address');
	    $amount = $this->input->post('amount');
	    $mobile = $this->encryption_model->encode($this->input->post('mobile'));
	    $pass = rand(10000,99999);
	    $pin = rand(10000,99999);
	    
	    $r = $this->db_model->getwhere('customers',array('cus_mobile'=>$mobile));
	    if($r){
		        
		         $this->session->set_flashdata('error','Member With Entered Mobile Number Already Exists');
		      //   $this->msg_model->whatsapp_sms($mob,'Member With Entered Mobile Number Already Exists');
		         
		         redirect('web/signup');
		         
		    }else{
		        
		        
    		    if($refer){ $refer = $refer;}else{$refer = 0;}
        		     $data = array(
        		        'cus_mobile' => $mobile,
                        'cus_name' => $name,
                        'scheme_id'=>'0',
                        'cus_email' => $email,
                        'cus_pass' => $this->encryption_model->encode($pass),
                        'cus_added_date' => $date,
                        'cus_reffer' => '0',
            			'cus_type' => 'retailer',
            			'cus_status' => '1',
            			'avability_status' => '0',
            			'cus_pin'=>$this->encryption_model->encode($pin),
            			'customer_signup_as' => 'retailer',
            			'registration_amt' => $amount
        		        );
        		    $inserted_id=$this->db_model->insert_update('customers',$data);
        		    $last_id = $this->db->insert_id();
        		    
        		    $message=array('mobile'=>$this->input->post('mobile'),'pass'=>$pass,'pin'=>$pin,'id'=>$last_id,'name'=>$this->input->post('name'));
        		    $this->msg_model->registration($message);
        		    
        		    $email = $this->input->post('email');
            		$msg= $message = "Welcome in ".$this->config->item('title')." family, your member id:  ".$this->input->post('mobile').", login pass: ".$pass.", login pin: ".$pin.", APP Link: ".$this->config->item('ataapp');
            		$sub="Successfully Registration";
            		$to=$email;
            		$this->email_model->send_email($to,$msg,$sub);
        		
        		    $this->session->set_flashdata('success','Member Created Successfully');
        		    $newdata1 = array(
                    'cus_id'  => $r[0]->cus_id,
                    'cus_name'  => $r[0]->cus_name,
                    'cus_type'  => $r[0]->cus_type,
                    'cus_email'  => $r[0]->cus_email,
                    'cus_mobile' => $mobile,
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                      );
                      
                    $this->session->set_userdata($newdata1);
        		    $this->session->set_userdata('isLoginRetailer',TRUE);
        		    redirect('Retailer/dashboard');
    		    }
	    
	   // $this->load->view('signup',$data);
	}
	public function policy()
	{
	    $this->load->view('privacy-policy');
	}
	
}
