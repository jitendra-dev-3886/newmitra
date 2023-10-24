<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Distributor extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
		$this->load->model(array('db_model','encryption_model','email_model','msg_model','Aeps_model','Icici_model'));
    }

	public function index()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		   		$dis_id = $this->session->userdata('dis_id');
		   	$data['data'] = $this->db_model->distributorDashboard($dis_id);
		    $this->load->view('distributor/dashboard',$data);
		}
	}
	public function dashboard()
    {
        if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		   		$dis_id = $this->session->userdata('dis_id');
		   	$data['data'] = $this->db_model->distributorDashboard($dis_id);
		    $this->load->view('distributor/dashboard',$data);
		}
    }
	public function otp()
    {
        if(isset($_POST['checkotp']))
        {
            $enteredotp = $this->input->post('otp');
            $otp = $this->session->userdata('otp');
            if($enteredotp == $otp){
                $this->session->set_userdata('isLoginDistributor', TRUE);
                redirect('distributor/dashboard');
            }
            else{
                
                $this->session->set_flashdata('msg', 'Entered OTP is Invalid');
                
                $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
                $this->load->view('distributor/otp',$data);
            }
        }
        else{
            $data['banner']=$this->db_model->getwhere('banner',array('type'=>'web'));
            $this->load->view('distributor/otp',$data);
        }
    }
    
    
     public function resendotp(){
        
        
        $otp=rand(100000,999999); 
        $this->session->set_userdata('otp', $otp);
        
	    $mobile = $this->session->userdata('dis_mobile');
	    $email = $this->session->userdata('dis_email');
	    $name = strtoupper($this->session->userdata('dis_name'));
	
	    $msg= $name.", Your One Time OTP Is ".$otp;
	    $sub="Login OTP From Recharge";
	    $to=$email;
	    
	    $r=$this->email_model->send_email($to,$msg,$sub);
	    $this->msg_model->sendsms($mobile,$msg);
		
		redirect('master/otp');
        
    }
    
	
	public function commission_package()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{$id=$this->session->userdata('dis_id');
		    $data['package'] = $this->db_model->getAlldata("SELECT * FROM `exr_package` WHERE package_addedby =$id");
		    $this->load->view('distributor/commission-package',$data);
		}
	}
	public function account()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('distributor/account-setting');
		}
	}
	
	function check_password() 
	{
	    
	    $old_password =$this->input->post('old_password');
	    $data=$this->db_model->getwhere('customers',array('cus_pass'=>$this->encryption_model->encode($old_password),'cus_id'=>$this->session->userdata('dis_id')));
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
	    $data=$this->db_model->getwhere('customers',array('cus_pin'=>$this->encryption_model->encode($old_pin),'cus_id'=>$this->session->userdata('dis_id')));
	    if($data){
	        echo TRUE;
	    }
        else{
            echo FALSE;
        }
        
    }
	public function password_and_pin()

	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}
		else{
		    if(isset($_POST['update_password']))
		    {
		                $record=array(
    				            'cus_pass'=>$this->encryption_model->encode($_POST['new_password'])
    				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('dis_id')));
    			$this->session->set_flashdata('message','Password Updated Successfully...!!!');
		    }
		    
		    if(isset($_POST['update_pin']))
		    {
		                $record=array(
    				            'cus_pin'=>$this->encryption_model->encode($_POST['new_pin'])
    				        );
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('dis_id')));
    			$this->session->set_flashdata('message','Pin Updated Successfully...!!!');
		    }
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('dis_id')));
		    $this->load->view('distributor/account-setting');
		}
	}
	public function profile()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
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
    			$this->db_model->insert_update('customers',$record,array('cus_id'=>$this->session->userdata('dis_id')));
    			$this->session->set_flashdata('message','Profile Updated Successfully...!!!');
		    }
		    
		    $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('dis_id')));
		    $data['referal']=$this->db_model->getwhere('customers',array('cus_id'=>$data['profile'][0]->cus_reffer));
		    $data['state']=$this->db_model->getwhere('state');
		    $data['city']=$this->db_model->getwhere('city',array('state_id'=>$data['profile'][0]->cus_state));
            $this->load->view('distributor/profile',$data);
		}
	}
	public function package_add()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    		$ip = $_SERVER['REMOTE_ADDR'];
    		$date = date('Y-m-d h:s:i');
    		$dis_id=$this->session->userdata('dis_id');
    		$name = $this->input->post('package_name');
    		$r = $this->db_model->insert_update('exr_package', array('package_name'=>$name,'package_addedby'=>$dis_id, 'package_membertype'=>'distributor', 'package_date'=>$date, 'package_ip'=>$ip));
    		$id = $this->db->insert_id();
    		if($r)
    		{
    			$ope = $this->db_model->getWhere('operator');
    			foreach($ope as $operator)
    			{
    				$this->db_model->insert_update('exr_packagecomm', array('packcomm_name'=>$id, 'packcomm_opcode'=>$operator->opcodenew,'packagecom_tem'=>1,'packagecom_type'=>2,'packagecom_amttype'=>2, 'packcomm_comm'=>0, 'packcomm_date'=>$date, 'packcomm_ip'=>$ip));
    			}
    			$this->session->set_flashdata('message','package created');
    			redirect('distributor/commission-package');
    		}
    		else
    		{
    			$this->session->set_flashdata('message','Please try again later');
    			redirect('distributor/commission-package');
    		}
		}
	}
	public function delete_package($id)
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    		$ck = $this->db_model->getWhere('customers', array('package_id'=>$id));
    		if(!$ck)
    		{
    			$this->db->delete('exr_package', array('package_id'=>$id));
    			$this->session->set_flashdata('message','Package Delete Successfully');
    			redirect('distributor/commission-package');
    		}
    		else
    		{
    			$this->session->set_flashdata('message','Package is used in members');
    			redirect('distributor/commission-package');
    		}
		}
	}
	public function view_package_commission($id)
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $dis_id=$this->session->userdata('dis_id');
    		$data['package'] = $this->db_model->getAlldata("select * from `exr_packagecomm` as exrpc join `exr_package` as exrp on exrpc.packcomm_name = exrp.package_id join `operator` as op on exrpc.packcomm_opcode = op.opcodenew where exrpc.packcomm_name=$id");
    		$assikageid=$this->db_model->getAlldata("select package_id from `customers` where cus_id=$dis_id");
    		$assignpackageid=$assikageid[0]->package_id;
    		$data['assignpackage'] = $this->db_model->getAlldata("select * from `exr_packagecomm` as exrpc join `exr_package` as exrp on exrpc.packcomm_name = exrp.package_id join `operator` as op on exrpc.packcomm_opcode = op.opcodenew where exrpc.packcomm_name=$assignpackageid");
    		$this->load->view('distributor/view-all-package-commission',$data);
		}
	}
	
	public function package_commission_update()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    		$new_comm = $this->input->post('new_comm');
    		$old_comm = $this->input->post('old_comm');
    		$packcomm_id = $this->input->post('packcomm_id');
    		$this->db_model->insert_update('exr_packagecomm', array('packagecom_tem' => 1, 'packagecom_type' => 2, 'packagecom_amttype' => 2, 'packcomm_comm' => $new_comm,'packcomm_date'=>date('Y-m-d h:s:i')), array('packcomm_id' => $packcomm_id));
    		echo "Commission Updated Successfully";
		}
	}
	public function view_all_retailer(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $dis_id=$this->session->userdata('dis_id');
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='retailer' and cus_reffer=$dis_id ORDER BY cus_id DESC ");
		    $this->load->view('distributor/view-members',$data);
		}
	}
	public function add_member()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $dis_id=$this->session->userdata('dis_id');
		    $r = $this->db_model->getAlldata("select * from users_credits where cust_id='$dis_id' order by users_credits_id desc limit 1");
		    $data['credits'] = $r['0']->available_count;
		    $data['package'] = $this->db_model->getwhere('exr_package',array('package_addedby'=>$dis_id));
		    $this->load->view('distributor/add-member',$data);
		}
	}
	public function add_member_succ()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $mobile = $this->encryption_model->encode($this->input->post('mobile'));
		    $pass = rand(10000,99999);
		    $pin = rand(10000,99999);
		    $refer = $this->session->userdata('dis_id');
		    $package = $this->input->post('package_id');
		    
		    $r = $this->db_model->getwhere('customers',array('cus_mobile'=>$mobile));
		    
		    if($r){
		        
		         $this->session->set_flashdata('error','Member With Entered Mobile Number Already Exists');
		         redirect('distributor/add-member');
		    }else{
		         $resps = $this->db_model->getAlldata("select * from users_credits where cust_id='$refer' order by users_credits_id desc limit 1");
        		        if($resps){
        		            $available_count=$resps[0]->available_count;
        		            $users_credits_id=$resps[0]->users_credits_id;
        		            if($available_count>0){
                    		    if($package == NULL || $package == 'SELECT API'){$package = 0;}
                    		    
                    		    $data = array(
                    		        "cus_mobile" => $mobile,
                    		        "cus_name" => $this->input->post('name'),
                    		        "cus_email" => $this->input->post('email'),
                    		        "cus_outlate" => $this->input->post('outlet'),
                    		        "cus_address" => $this->input->post('address'),
                    		        "cus_type" => 'retailer',
                    		        "cus_reffer" => $refer,
                    		        "package_id"=>$package,
                    		        "cus_pass" => $this->encryption_model->encode($pass),
                    		        "cus_pin" => $this->encryption_model->encode($pin)
                    		        );
                    		    $inserted_id=$this->db_model->insert_update('customers',$data);
                    		    if($inserted_id){
                        		        $available_count=$available_count-1;
                        		        $this->db_model->insert_update('users_credits',array('available_count'=>$available_count),array('users_credits_id'=>$users_credits_id));
                        		        
                        		        $message=array('mobile'=>$this->input->post('mobile'),'pass'=>$pass,'pin'=>$pin);
                            		    $this->msg_model->registration($message);
                            		    
                            		    $email = $this->input->post('email');
                                		$msg= $message = "Welcome in ".$this->config->item('title')." family, your member id:  ".$this->input->post('mobile').", login pass: ".$pass.", login pin: ".$pin.", APP Link: ".$this->config->item('ataapp');
                                		$sub="Successfully Registration";
                                		$to=$email;
                                		$this->email_model->send_email($to,$msg,$sub);
                    		        
                    		    }
                        		$this->session->set_flashdata('success','Member Created Successfully');
                    		    redirect('distributor/view_all_retailer');
        		            }
        		            else{
        		                $this->session->set_flashdata('error',"You Are Done With You Credits, Please Contact To Admin For Upgrade Credits");
        		                redirect('distributor/view_all_retailer');
        		            }
        		        }
        		        else{
        		            $this->session->set_flashdata('error',"You Don't Have Credits For Creating Members, Please Contact To Admin");
        		            redirect('distributor/view_all_retailer');
        		        }
		    }
		}
	}
	public function member_details($id){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $a = array();
    		$resps = $this->db_model->getwhere('customers', array('cus_id' => $id));
    		$res = $resps[0];
    		{
    			$a['country'] = $a['state'] = $a['city'] = 'Null';
    			if ($res->cus_country){
    			    $country = $this->db_model->getAlldata("SELECT * FROM country WHERE country_id='$res->cus_country'");  
    			    $a['country']=$country[0]->country_name;
    			}
    			if ($res->cus_state){
    				$state = $this->db_model->getAlldata("SELECT * FROM state WHERE state_id='$res->cus_state'");  
    				$a['state']=$state[0]->state_name;
    			}
    			if ($res->cus_city){
    				$acity = $this->db_model->getAlldata("SELECT * FROM city WHERE city_id='$res->cus_city'"); 
    				$a['city']=$acity[0]->city_name;
    			}
    			    $a['image'] = $this->db_model->getwhere('cus_proof', array('cus_id' => $id));
    			    $a['info'] = $res;
    			    $data['r'] = $a;
    			    $data['mobile'] = $resps[0]->cus_mobile;
    		}
		    $this->load->view('distributor/member-details',$data);
		}
	}
	public function active_customer($id)
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    		$this->db_model->insert_update('customers',array('avability_status'=>0),array('cus_id'=>$id));
    		$r = $this->db_model->getwhere('customers',array('cus_id'=>$id,'avability_status !='=>'1'));
    		redirect('distributor/member-details/'.$id);
		}
	}

	public function in_active_customer($id)
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    		$this->db_model->insert_update('customers',array('avability_status'=>1),array('cus_id'=>$id));
    		redirect('distributor/member-details/'.$id);
		}
	}
	public function edit_member($id){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $a = array();
    		$resps = $this->db_model->getwhere('customers', array('cus_id' => $id));
    		$dis_id=$this->session->userdata('dis_id');
		    $data['package'] = $this->db_model->getwhere('exr_package',array('package_addedby'=>$dis_id));
    		$res = $resps[0];
    		{
    			$a['country'] = $a['state'] = $a['city'] = 'Null';
    			$a['country'] = $a['state'] = $a['city'] = 'Null';
    			if ($res->cus_country){
    			    $country = $this->db_model->getAlldata("SELECT * FROM country WHERE country_id='$res->cus_country'");  
    			    $a['country']=$country[0]->country_name;
    			}
    			if ($res->cus_state){
    				$state = $this->db_model->getAlldata("SELECT * FROM state WHERE state_id='$res->cus_state'");  
    				$a['state']=$state[0]->state_name;
    			}
    			if ($res->cus_city){
    				$acity = $this->db_model->getAlldata("SELECT * FROM city WHERE city_id='$res->cus_city'"); 
    				$a['city']=$acity[0]->city_name;
    			}
    			    $a['image'] = $this->db_model->getwhere('cus_proof', array('cus_id' => $id));
    			    $a['info'] = $res;
    			    $data['r'] = $a;
    			    $data['mobile'] = $resps[0]->cus_mobile;
    		}
		    $this->load->view('distributor/edit-member',$data);
		}
	}
	public function edit_member_succ(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    	     $cus_id = $this->input->post('cus_id');
    	     $data = array(
    		        "cus_mobile" => $this->encryption_model->encode($this->input->post('mobile')),
    		        "cus_name" => $this->input->post('name'),
    		        "cus_email" => $this->input->post('email'),
    		        "cus_outlate" => $this->input->post('outlet'),
    		        "cus_address" => $this->input->post('address'),
    		        "package_id"=> $this->input->post('package_id'),
    		        );
        	  $this->db_model->insert_update('customers',$data,array("cus_id"=>$cus_id));
        	  redirect($this->agent->referrer());
		}
	}
	public function delete_account($id)
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    		$cus = $this->db_model->getwhere('customers',array('cus_id'=>$id));
    		$this->db_model->delete('customers',array('cus_id'=>$id));
    		redirect('distributor/view_all_retailer');
		}
	}
	public function logout_member($id)
	{   
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    	    $data=array('login_status'=>'loggedout','deviceId'=>'');
    		$r=$this->db_model->insert_update('customers',$data,array('cus_id'=>$id));
    		redirect('distributor/view_all_retailer');
		}
	}
	public function credit_wallet()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $dis_id=$this->session->userdata('dis_id');
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='retailer' and cus_reffer=$dis_id ORDER BY cus_id DESC ");
		    $this->load->view('distributor/credit-wallet',$data);
		}
	}
	
	public function debit_wallet()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $dis_id=$this->session->userdata('dis_id');
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='retailer' and cus_reffer=$dis_id ORDER BY cus_id DESC ");
		    $this->load->view('distributor/debit-wallet',$data);
		}
	}
	
	public function successAmt(){
	    $dis_id=$this->session->userdata('dis_id');
	    return $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where apiclid='$dis_id' AND status='SUCCESS' AND date(reqdate)=CURDATE() ");
	}
	
	public function failedAmt(){
	    $dis_id=$this->session->userdata('dis_id');
	    return $this->db_model->getAlldata("SELECT sum(amount) as amt FROM `exr_rechrgreqexr_rechrgreq_fch` where apiclid='$dis_id' AND status='FAILED' AND date(reqdate)=CURDATE() ");
	}
	
	public function daybook()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $dis_id=$this->session->userdata('dis_id');
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['fundCredit'] = $this->db_model->getAlldata("SELECT sum(txn_dbdt) as amt FROM `exr_trnx` where txn_agentid='$dis_id' AND date(txn_date)=CURDATE()");
		    $data['fundDebit'] = $this->db_model->getAlldata("SELECT sum(txn_crdt) as amt FROM `exr_trnx` where  txn_agentid='$dis_id' AND date(txn_date)=CURDATE()");
		    $data['recharge']=$this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew where apiclid='$dis_id' ORDER BY e.recid DESC");
            $data['operator'] = $this->db_model->getwhere('operator');
            $this->load->view('distributor/daybook',$data);
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
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else
		{
    		$this->form_validation->set_rules('cus_id', 'Customer Id', 'trim|required|numeric');
    		$this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric');
    		$this->form_validation->set_rules('pin', 'Pin', 'trim|required');
    		if ($this->form_validation->run() == FALSE)
    		{
    			$this->session->set_flashdata('msg', 'Validation error...');
    			echo "<script>window.location.href='".base_url()."distributor/credit-wallet';</script>";
    		}
    		else
    		{
    			$id = $this->session->userdata('dis_id');
    			$cus_id = $this->input->post('cus_id');
    			$pin = $this->input->post('pin');
    			$trnxamount = $this->input->post('amount');
    			
    			$ip = $_SERVER['REMOTE_ADDR'];
    			$time = time();
    
    			$cups = $this->db_model->getAlldata("select cus_pin,cus_cutofamt,cus_mobile from customers where cus_id='$id'");
    			$cus_mobile=$this->encryption_model->decode($cups[0]->cus_mobile);
    			$cp = $cups[0]->cus_pin;
    			$txn_crdt = $cups[0]->cus_cutofamt + $trnxamount;
    			if($this->encryption_model->decode($cups[0]->cus_pin) == $pin)
    			{
    				$old = $this->db_model->getAlldata("select * from exr_trnx where txn_agentid='$id' order by txn_id desc limit 1");
    				if(!empty($old))
    				{
    					$clbal = $old[0]->txn_clbal;
    					if($clbal >= $txn_crdt && $trnxamount > 0)
    					{
    					$r = $this->db_model->getAlldata("select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1");
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
    								'txn_date'=>DATE('Y-m-d h:i:s'),
    								'txn_ip'=>$ip
    							);
    							$lasttxnrec = $this->db_model->insert_update('exr_trnx',$data12);
    
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
    							$lasttxnrec1 = $this->db_model->insert_update('exr_trnx',$data13);
    							if($lasttxnrec)
    							{
    								$this->msg_model->sendsms($cus_mobile,"Dear member, your account has successfully credited rs.".$trnxamount.". Your closing balance is rs.".$total.".".$this->config->item('title'));
    
    								$this->session->set_flashdata('msg', 'Balance successfully credit to user account');
    							    redirect('distributor/credit-wallet');
    							}
    							else
    							{
    								$this->session->set_flashdata('msg', 'Error Occur. Please try again later');
    							    redirect('distributor/credit-wallet');
    							}
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
    							$lasttxnrec = $this->db_model->insert_update('exr_trnx',$data12);
    
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
    							$lasttxnrec1 = $this->db_model->insert_update('exr_trnx',$data13);
    							if($lasttxnrec1)
    							{
    								$this->msg_model->sendsms($cus_mobile,"Dear member, your account has successfully credited rs.".$trnxamount.". Your closing balance is rs.".$total.".".$this->config->item('title'));
                                    $this->session->set_flashdata('msg', 'Balance successfully credit to user account');
    							    redirect('distributor/credit-wallet');
    							}
    							else
    							{
    								$this->session->set_flashdata('msg', 'Error Occur. Please try again later');
    							    redirect('distributor/credit-wallet');
    							}
    						}
    					}
    					else
    					{
    						$this->session->set_flashdata('msg', 'Insuffient Fund..');
    						redirect('distributor/credit-wallet');
    					}
    				}
    				else
    				{
    					$this->session->set_flashdata('msg', 'Insuffient Fund');
    					redirect('distributor/credit-wallet');
    				}				
    			}
    			else
    			{
    				$this->session->set_flashdata('msg', 'Please enter currect pin');
    				redirect('distributor/credit-wallet');
    			}			
    		}		
	    }
	}
	public function direct_debit()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		$cid = $this->session->userdata('dis_id');
		$this->form_validation->set_rules('cus_id', 'Customer Id', 'trim|required|numeric');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric');
		$this->form_validation->set_rules('pin', 'Pin', 'trim|required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('msg', 'Validation error...');
			echo "<script>window.location.href='".base_url()."distributor/debit-wallet';</script>";
		}
		else
		{
			$cus_id = $this->input->post('cus_id');
			$txn_dbdt = $this->input->post('amount');
			$pin = $this->input->post('pin');
			$txntype = 'Direct Debit';
			$txntype1 = 'Fund Back';
			$ip = $_SERVER['REMOTE_ADDR'];
			$time = time();

			$cups = $this->db_model->getAlldata("select cus_pin,cus_mobile from customers where cus_id='$cid'");
			$cp = $cups[0]->cus_pin;
			if($this->encryption_model->decode($cups[0]->cus_pin) == $this->input->post('pin'))
			{
				$old = $this->db_model->getAlldata("select * from exr_trnx where txn_agentid='$cid' order by txn_id desc limit 1");
				$clbal = $old[0]->txn_clbal;
                $r = $this->db_model->getAlldata("select * from exr_trnx where txn_agentid='$cus_id' order by txn_id desc limit 1");
				if($r)
				{
				  	if($txn_dbdt > 0)
				  	{
					    $closing = $r['0']->txn_clbal;
						$total = $r['0']->txn_clbal - $txn_dbdt;
						$total1 = $clbal + $txn_dbdt;

						if($closing >= $txn_dbdt)
						{
							$txn_crdt = 0;
							$lasttxnrec = $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cus_id,'txn_opbal'=>$closing,'txn_crdt'=>$txn_crdt,'txn_dbdt'=>$txn_dbdt,'txn_fromto'=>$cid,'txn_clbal'=>$total,'txn_type'=>$txntype,'txn_time'=>$time,'txn_date'=>date('Y-m-d h:i:s'),'txn_ip'=>$ip));

							$lasttxnrec1 = $this->db_model->insert_update('exr_trnx',array('txn_agentid'=>$cid,'txn_fromto'=>$cus_id,'txn_opbal'=>$clbal,'txn_crdt'=>$txn_dbdt,'txn_dbdt'=>$txn_crdt,'txn_clbal'=>$total1,'txn_type'=>$txntype1,'txn_time'=>$time,'txn_date'=>date('Y-m-d h:i:s'),'txn_ip'=>$ip));

							if($lasttxnrec)
							{
								$this->msg_model->sendsms($cups[0]->cus_mobile,"Dear member, your account has successfully debited rs.".$txn_dbdt.". Your closing balance is rs.".$total.".".$this->config->item('title'));
								$this->session->set_flashdata('msg', 'Balance successfully debit from user account');
								echo "<script>window.location.href='".base_url()."distributor/debit-wallet';</script>";
							}
							else
							{
								$this->session->set_flashdata('msg', 'Error Occur. Please try again later');
								echo "<script>window.location.href='".base_url()."distributor/debit-wallet';</script>";
							}	
						}
						else
						{
							$this->session->set_flashdata('msg', 'Insuffient Fund...');
							echo "<script>window.location.href='".base_url()."distributor/debit-wallet';</script>";
						}
					}
					else
					{
			        	$this->session->set_flashdata('msg', 'Amount is not < 1');
						echo "<script>window.location.href='".base_url()."distributor/debit-wallet';</script>";
					}
				}
				else
				{
					$this->session->set_flashdata('msg', 'Amount not available..');
					echo "<script>window.location.href='".base_url()."distributor/debit-wallet';</script>";
				}
			}				
			else
			{
				$this->session->set_flashdata('msg', 'Please enter currect pin');
				redirect('distributor/debit-wallet');
			}
		}
	}	
	}
	public function getOperators(){
	    return $this->db_model->getAlldata("SELECT * FROM `operator` ");
	}
	
	public function getMembers(){
	    $dis_id=$this->session->userdata('dis_id');
	    return $this->db_model->getAlldata("SELECT * FROM `customers` WHERE cus_type='retailer' AND cus_reffer='$dis_id' ORDER BY cus_id DESC ");
	}
	public function recharge_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE e.apiclid='".$this->session->userdata('dis_id')."' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('distributor/view-recharge-report',$data);
		}
	}
	
	public function pending_recharge_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['apisources'] = $this->getApiSources();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE e.apiclid='".$this->session->userdata('dis_id')."' AND status='PENDING' ORDER BY e.recid DESC");
		    $this->load->view('distributor/view-recharge-report',$data);
		}
	}
	
	public function failed_recharge_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE e.apiclid='".$this->session->userdata('dis_id')."' AND status='FAILED' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('distributor/view-recharge-report',$data);
		}
	}
	
	public function success_recharge_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['success'] = $this->successAmt();
		    $data['failed'] = $this->failedAmt();
		    $data['operator'] = $this->getOperators();
		    $data['members'] = $this->getMembers();
		    $data['recharges'] = $this->db_model->getAlldata("SELECT * FROM `exr_rechrgreqexr_rechrgreq_fch` as e join customers as c on e.apiclid = c.cus_id join operator as o on e.operator = o.opcodenew WHERE e.apiclid='".$this->session->userdata('dis_id')."' AND status='SUCCESS' AND date(reqdate)=CURDATE() ORDER BY e.recid DESC");
		    $this->load->view('distributor/view-recharge-report',$data);
		}
	}
	public function search_recharge_report(){
	    $operator_type = $_POST['operator_type'];
	    $operator = $_POST['operator'];
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

		$sql .= " e.recid != '' ORDER BY e.recid DESC";
		//echo $sql;exit;
		$data['success'] = $this->successAmt();
        $data['failed'] = $this->failedAmt();
        $data['operator'] = $this->getOperators();
        $data['members'] = $this->getMembers();
        $data['recharges'] = $this->db_model->getAlldata("$sql");
        $this->load->view('distributor/view-recharge-report',$data);
	    
	}
	public function credit_wallet_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['credit'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_fromto = c.cus_id where e.txn_type='Fund Transfer' and e.txn_agentid='".$this->session->userdata('dis_id')."' ORDER BY e.txn_id DESC");
		    $this->load->view('distributor/credit_wallet_report',$data);
		}
	}
	public function debit_wallet_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['credit'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_type='Direct Debit' and c.cus_reffer='".$this->session->userdata('dis_id')."' ORDER BY e.txn_id DESC");
		    $this->load->view('distributor/debit_wallet_report',$data);
		}
	}
	
	
	
	
	public function transaction_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where DATE(e.txn_date) >= '".date('Y-m-d').' 00:00:00'."' and DATE(e.txn_date) <= '".date('Y-m-d').' 00:00:00'."' and c.cus_reffer='".$this->session->userdata('dis_id')."' ORDER BY e.txn_id DESC");
		    $this->load->view('distributor/transaction-report',$data);
		}
	}
	
	public function dmt_transaction_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `dmt_trnx` as e join customers as c on e.cus_id = c.cus_id JOIN exr_trnx ON e.dmt_trnx_id = exr_trnx.dmt_txn_recrefid where c.cus_reffer='".$this->session->userdata('dis_id')."'  ORDER BY e.dmt_trnx_id DESC");
		    $this->load->view('distributor/dmt-transaction-report',$data);
		}
	}
	public function redeem_request_history()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `payout_request` as e join customers as c on e.cus_id = c.cus_id WHERE status!='PENDING' and c.cus_reffer='".$this->session->userdata('dis_id')."'");
		    $this->load->view('distributor/redeem-request-history',$data);
		}
	}
	public function redeem_wallet_report()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join customers as c on e.txn_agentid = c.cus_id WHERE txn_type LIKE '%PAYOUT%' and c.cus_reffer='".$this->session->userdata('dis_id')."'");
		    $this->load->view('distributor/redeem-wallet-report',$data);
		}
	}
	public function member_ledger(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where txn_fromto !='0' and txn_type In ('Direct Credit','Fund Transfer') and DATE(e.txn_date) >= '".DATE('Y-m-d')."' and DATE(e.txn_date) <= '".DATE('Y-m-d')."' and c.cus_reffer='".$this->session->userdata('dis_id')."' ORDER BY e.txn_id DESC");
		    $this->load->view('distributor/member-ledger',$data);
		}
	}
	
	public function admin_ledger(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `exr_trnx` as e join `customers` as c on e.txn_agentid = c.cus_id where e.txn_fromto='0' and e.txn_type In ('Direct Credit','Pullout') and DATE(e.txn_date) >= '".DATE('Y-m-d')."' and DATE(e.txn_date) <= '".DATE('Y-m-d')."' and c.cus_reffer='".$this->session->userdata('dis_id')."' ORDER BY e.txn_id DESC");
		    $this->load->view('distributor/admin-ledger',$data);
		}
	}
	
	public function aeps_ledger(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['ledger'] = $this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` as e join `customers` as c on e.aeps_txn_agentid = c.cus_id where DATE(e.aeps_txn_date) >= '".DATE('Y-m-d')."' and DATE(e.aeps_txn_date) <= '".DATE('Y-m-d')."' and c.cus_reffer='".$this->session->userdata('dis_id')."' ORDER BY e.aeps_txn_id DESC");
		    $this->load->view('distributor/aeps-ledger',$data);
		}
	}
	public function pancoupon_history(){
  	    
  	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['coupons'] = $this->db_model->getAlldata("SELECT * FROM coupon_buy as cb,customers as c,coupon_price as cp where cb.vle_id = c.cus_id and cb.type = cp.coupon_price_id and c.cus_reffer='".$this->session->userdata('dis_id')."'   ORDER BY coupon_buy_id DESC");
            $this->load->view('distributor/pancoupon_history',$data);
        }
  	}
  	
  	public function micro_atm(){
  	    
  	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['micro'] = $this->db_model->getAlldata("SELECT * FROM micro_atm as ma , customers as c where ma.cus_id = c.cus_id and c.cus_reffer='".$this->session->userdata('dis_id')."' ORDER BY ma_id DESC");
            $this->load->view('distributor/microAtm_report',$data);
        }
  	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getApiSources(){
	    return $this->db_model->getAlldata("SELECT * FROM `apisource` ");
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
    	        echo '<div class="form-group"><label class="form-label">Select Master:</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="typcn typcn-star tx-24 lh--9 op-6"></i></div></div><select class="form-control" name="reffer_id" required>';
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function api_commission_package()
	{
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $this->load->view('distributor/api-package-commission');
		}
	}
	
	
	
	
	
	public function view_all_members(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers ORDER BY cus_id DESC");
		    $this->load->view('distributor/view-members',$data);
		}
	}
	
	public function view_all_master(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='master' ORDER BY cus_id DESC");
		    $this->load->view('distributor/view-members',$data);
		}
	}
	
	public function view_all_distributor(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='distributor' ORDER BY cus_id DESC");
		    $this->load->view('distributor/view-members',$data);
		}
	}
	
	
	
	public function view_all_api_clients(){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
		    $data['members'] = $this->db_model->getAlldata("SELECT * FROM customers WHERE cus_type='api' ORDER BY cus_id DESC");
		    $this->load->view('distributor/view-members',$data);
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
		$cusd = $this->db_model->get_newdata('customers',array('cus_id'=>$id));

		if($cusd[0]->cus_type == 'distributor' && $type == 'master')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>'0','package_id'=>'1'),array('cus_id'=>$id));
			$cusf = $this->db_model->get_newdata('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('item','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('item','Failed! please try again later..');
			}			
		}
		elseif($cusd[0]->cus_type == 'distributor' && $type == 'retailer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>'3'),array('cus_id'=>$id));
			$cusf = $this->db_model->get_newdata('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('item','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('item','Failed! please try again later..');
			}
		}
		elseif($cusd[0]->cus_type == 'distributor' && $type == 'customer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>'0','package_id'=>'4'),array('cus_id'=>$id));
			$cusf = $this->db_model->get_newdata('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('item','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('item','Failed! please try again later..');
			}
		}
		elseif($cusd[0]->cus_type == 'master' && $type == 'customer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>'0','package_id'=>'4'),array('cus_id'=>$id));
			$cusf = $this->db_model->get_newdata('customers',array('cus_reffer'=>$id));
			if($cusf)
			{
				foreach($cusf as $cf)
				{
					$this->db_model->insert_update('customers',array('cus_reffer'=>$cus_reff),array('cus_id'=>$cf->cus_id));
				}
				$this->session->set_flashdata('item','Account switched successfully..');
			}
			else
			{
				$this->session->set_flashdata('item','Failed! please try again later..');
			}
		}
		elseif($cusd[0]->cus_type == 'customer' && $type == 'distributor')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>'2'),array('cus_id'=>$id));
			$this->session->set_flashdata('item','Account switched successfully..');
		}
		elseif($cusd[0]->cus_type == 'customer' && $type == 'retailer')
		{
			$this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>'3'),array('cus_id'=>$id));
			$this->session->set_flashdata('item','Account switched successfully..');
		}
		else
		{
			$pkg = $this->db_model->get_newdata('exr_package',array('package_membertype'=>$type));
			$r = $this->db_model->insert_update('customers',array('cus_type'=>$type,'cus_reffer'=>$cus_reff,'package_id'=>$pkg[0]->package_id),array('cus_id'=>$id));
			$this->session->set_flashdata('item','Account switched successfully..');
		}
		redirect('distributor/view-all-members');
	}
	
	public function switch_account($id){
	    if($this->session->userdata('isLoginDistributor') == FALSE){
            redirect('users_login');
		}else{
    	    $data['r'] = $this->db_model->getwhere('customers',array('cus_id'=>$id));
    		$this->load->view('distributor/switch-account',$data);
		}
	}
	
	public function logout()
	{
	    $this->session->unset_userdata(array('dis_id','dis_name','dis_type','dis_email','dis_mobile','isLoginDistributor')); 
		redirect('users_login');
  	}
//   	public function cashWithdrawal()
// 	{


// 	   if(isset($_POST['cashwithdrawal'])){
//         $CURLOPT_URL="https://fpuat.tapits.in/fpaepsservice/api/cashWithdrawal/merchant/php/withdrawal";
//         $captureResponse=$_POST['txtPidData'];
//         $PidOptions=$_POST['PidOptions'];
//         $adhaarNumber=$_POST['cwadhaarNumber'];
//         $nationalBankIdenticationNumber=$_POST['cwnationalBankIdenticationNumber'];
//         $requestRemarks=$_POST['cwrequestRemarks'];
//         $mobileNumber=$_POST['cwmobileNumber'];
//         $transactionAmount=$_POST['cwtransactionAmount'];
        
//         $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
//         $piData = $xmlCaptureResponse->Data;
//         $Hmac = $xmlCaptureResponse->Hmac;
//         $Skey = $xmlCaptureResponse->Skey;
        
//         preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
// 	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
// 	    $x=explode(">",$PidData);
// 	    $PidDatatype=$x[0];
	    
// 	    $cidata=explode(">",$cida);
// 	    $ci=$cidata[0];
// 	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
//         $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
//         $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
//         $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
//         $values=array(   
//             "merchantTranId"=>rand(00000000000000,99999999999999),
//             "captureResponse"=>array(
//                                 "PidDatatype"=>$PidDatatype,
//                                 "Piddata"=>"$piData",
//                                 "ci"=>"$ci",
//                                 "dc"=>"$dc",
//                                 "dpID"=>$dpId,
//                                 "errCode"=>$errCode,
//                                 "errInfo"=>$errInfo,
//                                 "fCount"=>$fCount,
//                                 "fType"=>$fType,
//                                 "hmac"=>"$Hmac",
//                                 "iCount"=>$iCount,
//                                 "mc"=>"$mc",
//                                 "mi"=>$mi,
//                                 "nmPoints"=>"$nmPoints",
//                                 "pCount"=>$pCount,
//                                 "pType"=>$pType,
//                                 "qScore"=>"$qScore",
//                                 "rdsID"=>$rdsId,
//                                 "rdsVer"=>$rdsVer,
//                                 "sessionKey"=>"$Skey",
//                             ),
//             "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
//             "languageCode"=>'en',
//             "latitude"=>"18.512310",
//             "longitude"=>"73.878790",
//             "mobileNumber"=>$mobileNumber,
//             "paymentType"=>'B',
//             "requestRemarks"=>$requestRemarks,
//             "transactionAmount"=>$transactionAmount,
//             "transactionType"=>'CW',//CW
//             "merchantUserName"=>$merchantUserName,
//             "merchantPin"=>md5($merchantPin),
//             "subMerchantId"=>$merchantUserName,
//             "superMerchantId"=>'953'
//         );
//         $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
//         $this->db_model->insert_update('aeps_transaction_fch',array('amount'=>$response['data']['transactionAmount'],'status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'apiclid'=>'59'));
//       if($response['data']['transactionStatus']=='successful'){
//         $this->session->set_flashdata('success',$response['data']['transactionAmount'].' Withdrawal Successfullly');
//         }
//         else{
//             $this->session->set_flashdata('error',$response['message']);
//         }
//         redirect('http://edigitalvillage.net/Retailer/aeps_transaction');
	   
// 	   }
// 	   if(isset($_POST['balancecheck'])){
// 	    $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/balanceInquiry/merchant/php/getBalance";
//         $captureResponse=$_POST['txtPidData'];
//         $PidOptions=$_POST['PidOptions'];
//         $adhaarNumber=$_POST['bcadhaarNumber'];
//         $nationalBankIdenticationNumber=$_POST['bcnationalBankIdenticationNumber'];
//         $mobileNumber=$_POST['bcmobileNumber'];
        
//         $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
//         $piData = $xmlCaptureResponse->Data;
//         $Hmac = $xmlCaptureResponse->Hmac;
//         $Skey = $xmlCaptureResponse->Skey;
        
//         preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
// 	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
// 	    $x=explode(">",$PidData);
// 	    $PidDatatype=$x[0];
	    
// 	    $cidata=explode(">",$cida);
// 	    $ci=$cidata[0];
// 	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
        
//         $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
//         $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
//         $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        
//         $values=array(
//             "merchantTransactionId"=>rand(00000000000000,99999999999999),
//             "captureResponse"=>array(
//                                 "PidDatatype"=>$PidDatatype,
//                                 "Piddata"=>"$piData",
//                                 "ci"=>"$ci",
//                                 "dc"=>"$dc",
//                                 "dpID"=>$dpId,
//                                 "errCode"=>$errCode,
//                                 "errInfo"=>$errInfo,
//                                 "fCount"=>$fCount,
//                                 "fType"=>$fType,
//                                 "hmac"=>"$Hmac",
//                                 "iCount"=>$iCount,
//                                 "mc"=>"$mc",
//                                 "mi"=>$mi,
//                                 "nmPoints"=>"$nmPoints",
//                                 "pCount"=>$pCount,
//                                 "pType"=>$pType,
//                                 "qScore"=>"$qScore",
//                                 "rdsID"=>$rdsId,
//                                 "rdsVer"=>$rdsVer,
//                                 "sessionKey"=>"$Skey",
//                                 ),
//             "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>'0',"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
//             "languageCode"=>'en',
//             "latitude"=>"18.512310",
//             "longitude"=>"73.878790",
//             "mobileNumber"=>$mobileNumber,
//             "paymentType"=>'B',
//             "transactionType"=>'BE',
//             "merchantUserName"=>$merchantUserName,
//             "merchantPin"=>md5($merchantPin),
//             "superMerchantId"=>'953'
//           );
//         $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
//         if($response['data']['transactionStatus']=='successful'){
//         $this->session->set_flashdata('success',$response['data']['balanceAmount']);
//         }
//         else{
//             $this->session->set_flashdata('error',$response['message']);
//         }
//         redirect('http://edigitalvillage.net/Retailer/aeps_transaction');
// 	   }
// 	   if(isset($_POST['ministetement'])){
// 	    $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/miniStatement/merchant/php/statement";
//         $captureResponse=$_POST['txtPidData'];
//         $PidOptions=$_POST['PidOptions'];
//         $adhaarNumber=$_POST['msadhaarNumber'];
//         $nationalBankIdenticationNumber=$_POST['msnationalBankIdenticationNumber'];
//         $mobileNumber=$_POST['msmobileNumber'];
        
//         $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
//         $piData = $xmlCaptureResponse->Data;
//         $Hmac = $xmlCaptureResponse->Hmac;
//         $Skey = $xmlCaptureResponse->Skey;
        
//         preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
// 	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
// 	    $x=explode(">",$PidData);
// 	    $PidDatatype=$x[0];
	    
// 	    $cidata=explode(">",$cida);
// 	    $ci=$cidata[0];
// 	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
        
//         $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
//         $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
//         $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
        
//         $values=array(
//             "merchantTransactionId"=>rand(00000000000000,99999999999999),
//             "captureResponse"=>array(
//                                 "PidDatatype"=>$PidDatatype,
//                                 "Piddata"=>"$piData",
//                                 "ci"=>"$ci",
//                                 "dc"=>"$dc",
//                                 "dpID"=>$dpId,
//                                 "errCode"=>$errCode,
//                                 "errInfo"=>$errInfo,
//                                 "fCount"=>$fCount,
//                                 "fType"=>$fType,
//                                 "hmac"=>"$Hmac",
//                                 "iCount"=>$iCount,
//                                 "mc"=>"$mc",
//                                 "mi"=>$mi,
//                                 "nmPoints"=>"$nmPoints",
//                                 "pCount"=>$pCount,
//                                 "pType"=>$pType,
//                                 "qScore"=>"$qScore",
//                                 "rdsID"=>$rdsId,
//                                 "rdsVer"=>$rdsVer,
//                                 "sessionKey"=>"$Skey",
//                                 ),
//             "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>'0',"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
//             "languageCode"=>'en',
//             "latitude"=>"18.512310",
//             "longitude"=>"73.878790",
//             "mobileNumber"=>$mobileNumber,
//             "paymentType"=>'B',
//             "transactionType"=>'MS',
//             "merchantUserName"=>$merchantUserName,
//             "merchantPin"=>md5($merchantPin),
//             "superMerchantId"=>'953'
//           );
//         $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
        
//         if($response['data']['transactionStatus']=='successful'){
//             $resdata=$response['data']['miniStatementStructureModel'];
//         for($i=0;$i<count($resdata);$i++){ $date=$resdata[$i]['date'];$tratype=$resdata[$i]['txnType'];$amt=$resdata[$i]['amount'];$naration=$resdata[$i]['narration'];
//                 $tabledata.="<tr><td>$date</td><td>$tratype</td>
//                     <td>$amt</td><td>$naration</td>               
//                   </tr>";
//             }
//         $table='<div class="table-responsive"><div class="boxscroll">
// 				                            <table class="table table-hover table-hover-border">
// 				                                <thead>
// 				                                <tr>
// 				                                    <th>Date</th>
// 				                                    <th>TxnType</th>
// 				                                    <th>Amount</th>
// 				                                    <th>Narration</th>
// 				                                </tr>
// 				                                </thead>
// 				                                <tbody>
// 				                                 '.$tabledata.'
// 				                                 </tbody>
// 				                            </table>
// 				                            </div>
// 				                        </div>';
//         $this->session->set_flashdata('success',$table);
//         }
//         else{
//             $this->session->set_flashdata('error',$response['message']);
//         }
//         redirect('http://edigitalvillage.net/Retailer/aeps_transaction');
// 	   }
// 	   if(isset($_POST['adharpayment'])){
//         $CURLOPT_URL="https://fingpayap.tapits.in/fpaepsservice/api/aadhaarPay/merchant/php/pay";
//         $captureResponse=$_POST['txtPidData'];
//         $PidOptions=$_POST['PidOptions'];
//         $adhaarNumber=$_POST['apadhaarNumber'];
//         $nationalBankIdenticationNumber=$_POST['apnationalBankIdenticationNumber'];
//         $requestRemarks=$_POST['aprequestRemarks'];
//         $mobileNumber=$_POST['apmobileNumber'];
//         $transactionAmount=$_POST['aptransactionAmount'];
        
//         $xmlCaptureResponse = simplexml_load_string($captureResponse);
        
//         $piData = $xmlCaptureResponse->Data;
//         $Hmac = $xmlCaptureResponse->Hmac;
//         $Skey = $xmlCaptureResponse->Skey;
        
//         preg_match_all('#iCount=([^\s]+)#', $PidOptions, $matches); $iCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $PidOptions, $matches);$pCount='0'; $pType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errCode=([^\s]+)#', $captureResponse, $matches);$pType='0'; $errCode=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#errInfo=([^\s]+)#', $captureResponse, $matches); $errInfo=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fCount=([^\s]+)#', $captureResponse, $matches); $fCount=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#fType=([^\s]+)#', $captureResponse, $matches); $fType=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#mi=([^\s]+)#', $captureResponse, $matches); $mi=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsId=([^\s]+)#', $captureResponse, $matches); $rdsId=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#rdsVer=([^\s]+)#', $captureResponse, $matches); $rdsVer=str_replace('"',"",$matches[1][0]); 
//         preg_match_all('#nmPoints=([^\s]+)#', $captureResponse, $matches); $nmPoints=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#qScore=([^\s]+)#', $captureResponse, $matches); $qScore=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#dpId=([^\s]+)#', $captureResponse, $matches); $dpId=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#mc=([^\s]+)#', $captureResponse, $matches); $mc=str_replace('"',"",$matches[1][0]); 
// 	    preg_match_all('#dc=([^\s]+)#', $captureResponse, $matches); $dcc=str_replace('"',"",$matches[1][0]);$dc=str_replace('>','',$dcc); 
// 	    preg_match_all('#ci=([^\s]+)#', $captureResponse, $matches); $cida=str_replace('"',"",$matches[1][0]); 
	    
// 	    preg_match_all('#type=([^\s]+)#', $captureResponse, $matches); $PidData=str_replace('"',"",$matches[1][0]); 
// 	    $x=explode(">",$PidData);
// 	    $PidDatatype=$x[0];
	    
// 	    $cidata=explode(">",$cida);
// 	    $ci=$cidata[0];
// 	    preg_match_all('#type="X">([^\s]+)#', $captureResponse, $matches); $Piddata=str_replace('"',"",$matches[1][0]); 
//         $data['profile']=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));
//         $merchantUserName=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginId);
//         $merchantPin=$this->encryption_model->decode($data['profile'][0]->aeps_merchantLoginPin);
//         $values=array(   
//             "merchantTranId"=>rand(00000000000000,99999999999999),
//             "captureResponse"=>array(
//                                 "PidDatatype"=>$PidDatatype,
//                                 "Piddata"=>"$piData",
//                                 "ci"=>"$ci",
//                                 "dc"=>"$dc",
//                                 "dpID"=>$dpId,
//                                 "errCode"=>$errCode,
//                                 "errInfo"=>$errInfo,
//                                 "fCount"=>$fCount,
//                                 "fType"=>$fType,
//                                 "hmac"=>"$Hmac",
//                                 "iCount"=>$iCount,
//                                 "mc"=>"$mc",
//                                 "mi"=>$mi,
//                                 "nmPoints"=>"$nmPoints",
//                                 "pCount"=>$pCount,
//                                 "pType"=>$pType,
//                                 "qScore"=>"$qScore",
//                                 "rdsID"=>$rdsId,
//                                 "rdsVer"=>$rdsVer,
//                                 "sessionKey"=>"$Skey",
//                             ),
//             "cardnumberORUID"=>array("adhaarNumber"=>$adhaarNumber,"indicatorforUID"=>0,"nationalBankIdentificationNumber"=>$nationalBankIdenticationNumber),
//             "languageCode"=>'en',
//             "latitude"=>"18.512310",
//             "longitude"=>"73.878790",
//             "mobileNumber"=>$mobileNumber,
//             "paymentType"=>'B',
//             "requestRemarks"=>$requestRemarks,
//             "transactionAmount"=>$transactionAmount,
//             "transactionType"=>'M',//CW
//             "merchantUserName"=>$merchantUserName,
//             "merchantPin"=>md5($merchantPin),
//             "subMerchantId"=>$merchantUserName,
//             "superMerchantId"=>'953'
//         );
//         $response=$this->Aeps_model->onboarding($CURLOPT_URL,$values);
//         $this->db_model->insert_update('aeps_transaction_fch',array('amount'=>$response['data']['transactionAmount'],'status'=>$response['data']['transactionStatus'],'transactionType'=>$response['data']['transactionType'],'transaction_ref_id'=>$response['data']['fpTransactionId'],'apiclid'=>'59'));
//       if($response['data']['transactionStatus']=='successful'){
//         $this->session->set_flashdata('success',$response['data']['transactionAmount'].' Withdrawal Successfullly');
//         }
//         else{
//             $this->session->set_flashdata('error',$response['message']);
//         }
//         redirect('http://edigitalvillage.net/Retailer/aeps_transaction');
	   
// 	   }
// 	}
	
   
    

}
