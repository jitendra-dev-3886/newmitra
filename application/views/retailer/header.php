<!-- main-header opened -->
<div class="main-header nav nav-item hor-header">
	<div class="container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="<?php echo base_url();?>Retailer/dashboard">
				<img src="../../assets/logo.jpg" class="logo-white ">
			</a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-user"></i> <b> Retailer (<?php echo $this->session->userdata('cus_mobile');?>)</b></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-phone"></i> Support : <?php 
		    $support=$this->db_model->getAlldata('select * from support');echo $support['0']->mobile; ?></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="mailto:<?php echo $support['0']->email;?>"><i class="fa fa-envelope"></i> Email : <?php echo $support['0']->email;?></a>
			
			<a class="new nav-link full-screen-link" style="color:white;" href="#"> IP :- <?php echo $this->session->userdata('ip_address')?></a>
		</div><!-- search -->
		<div class="main-header-right">
		    <?php 
		        if($mobile=$this->session->userdata('senderMobile')){ 
		            $api_mobile = '7517549899';
                    $api_pass = '36199';
                    $api_user_id = '88';
                    $api_reg_ip = '1234567890';
                    
		            $mobile = $mobile;
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06005~$api_mobile~$api_pass~$api_user_id~$api_reg_ip~$id~$mobile~NA~NA";
                    $url= 'https://edigitalvillage.net/index.php/api_partner/getsenderbalance';
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
                    //echo $server_output;
                    if($http_code == "200"){
                        $dataArr = explode('~',$server_output);
                        if($dataArr[5] == 'SUCCESS'){
                            $res = $dataArr[6];
                            $wallet_bal_arr=explode(':',$res);
                            $wallet_balance=$wallet_bal_arr[1];
                        }    
                    }
		        ?>
		   
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class=""></i> <b>Dmt Limit :- <?php echo $wallet_balance;?></b></a>      
			<a class="new nav-link full-screen-link" style="color:white;" href="<?php echo base_url()?>retailer/dmtLogOut"><i class="fa fa-power-off"></i> <b> Dmt Logout</b></a>
			<?php } ?>
			
		    <a class="new nav-link full-screen-link" href="#" style="color:white;text-align: center;" >
		         <?php $bal=$this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('cus_id')."' ORDER BY txn_id desc limit 1");?>
		        <i class="fas fa-wallet"></i>&nbsp&nbsp&nbspBALANCE<br> <b style="margin-left: 25px;">₹<?php echo round($bal[0]->amt, 2);?>/-</b></a>
		        
		         <a class="new nav-link full-screen-link" href="<?php echo site_url('retailer/payout') ?>" style="color:white;text-align: center;margin-top:10px" >
		         <?php $aepsbal=$this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='".$this->session->userdata('cus_id')."' ORDER BY aeps_txn_id DESC LIMIT 1");?>
	            <img src="<?php echo base_url()?>assets/img/icic.png" height="25px" width="25px"><b style="margin-left: 5px;">₹<?php if($aepsbal[0]->aeps_txn_clbal!=0){ echo $aepsbal[0]->aeps_txn_clbal; } else{ echo "0"; }?>/-</b><br><b style="margin-left: 25px;"><button style="background-color: #A94098;color:white">REDEEM</button></b></a>
		          
			    <!--<div class="nav nav-item nav-link" id="bs-example-navbar-collapse-1">
					<form class="navbar-form" role="search">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Search">
							<span class="input-group-btn">
								<button type="reset" class="btn btn-default">
									<i class="fas fa-times"></i>
								</button>
								<button type="submit" class="btn btn-default nav-link">
									<i class="fe fe-search"></i>
								</button>
							</span>
						</div>
					</form>
				</div>-->
				<!--<div class="nav nav-item  navbar-nav-right ml-auto">
					<div class="dropdown main-profile-menu nav nav-item nav-link">
					    <?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));?>
						<a class="profile-user d-flex" href="#"><img src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';}?>" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>
						<div class="dropdown-menu">
							<div class="main-header-profile header-img">
								<div class="main-img-user"><img alt="" src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';} ?>"></div>
								<h6><?php echo $this->session->userdata('cus_name');?></h6><span><?php echo $this->session->userdata('cus_email');?></span>
							</div>
							<a class="dropdown-item" href="<?php echo base_url();?>Retailer/profile"><i class="far fa-user"></i> My Profile</a>
							<a class="dropdown-item" href="<?php echo base_url();?>Retailer/password_and_pin"><i class="far fa-edit"></i> Update Password & Pin</a>
							<a class="dropdown-item" href="<?php echo base_url();?>Retailer/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
						</div>
					</div>
				</div>-->
				
                <div class="nav nav-item  navbar-nav-right ml-auto">
					<!--<div class="nav-item full-screen fullscreen-button">
						<a class="new nav-link full-screen-link" href="#"><i class="fe fe-maximize"></i></span></a>
					</div>-->
					<div class="dropdown main-profile-menu nav nav-item nav-link">
					    <?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));?>
						<a class="profile-user d-flex dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><img src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';}?>" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<div class="main-header-profile header-img">
								<div class="main-img-user"><img alt="" src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';} ?>"></div>
								<h6><?php echo $this->session->userdata('cus_name');?></h6><span><?php echo $this->session->userdata('cus_email');?></span>
							</div>
							<a class="dropdown-item" href="<?php echo base_url();?>Retailer/profile"><i class="far fa-user"></i> My Profile</a>
							<a class="dropdown-item" href="<?php echo base_url();?>Retailer/password_and_pin"><i class="far fa-edit"></i> Update Password & Pin</a>
							<a class="dropdown-item" href="<?php echo base_url();?>Retailer/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- main-header closed -->
<!--Horizontal-main -->
<div class="sticky">
	<div class="horizontal-main hor-menu clearfix side-header">
		<div class="horizontal-mainwrapper container clearfix">
			<!--Nav-->
			<nav class="horizontalMenu clearfix" style="margin-left: -5%;">
				<ul class="horizontalMenu-list">
					<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/dashboard" class=""><i class="fe fe-airplay  menu-icon"></i> Dashboard</a></li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-database"></i> Recharge <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/mobile_recharge" class="slide-item"> Mobile</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/data_card" class="slide-item" >Data Card</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/dth" class="slide-item">DTH</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/landline" class="slide-item">Landline</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-package"></i> Bill <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/electricity" class="slide-item"> Electricity</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/insurance" class="slide-item" >Insurance</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/gas" class="slide-item">Gas</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/gas_cylinder" class="slide-item">Gas Cylinder</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/dmt" class=""><i class="fe fe-layers"></i> DMT</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/fastag" class=""><i class="icon ion-ios-rocket"></i> Fastag</a></li>
					
					
					<?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('cus_id')));?>
					
					    <?php if($profile[0]->aeps_kyc_status=='KYC Not Completed'){ ?>
					        <li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/aeps" class=""><i class="fe fe-aperture"></i> AEPS</a></li>
					    <?php }else{ ?>
					    <?php if($profile[0]->aeps_kyc_status=='KYC Completed' && $profile[0]->newaepskyc_status=='not_done'){?>
					        <li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/updateaepsekyc" class=""><i class="fe fe-aperture"></i> AEPS</a></li>
					    <?php }else{ ?>
					        <li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/aeps_transaction" class=""><i class="fe fe-aperture"></i> AEPS</a></li>
					    <?php } ?>
					<?php }?>
					
					
					<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/insuranceapi" class=""><i class="mdi mdi-account-card-details"></i> Insurance</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/pan_card" class=""><i class="icon ion-ios-pie"></i> Pan Card</a></li>
					
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-codepen"></i> Fund <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/fund_request" class="slide-item">Request</a></li><!--
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/fund_transfer" class="slide-item">Transfer</a></li>-->
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-bar-chart-2"></i> Report <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/recharge_report" class="slide-item"> Recharge Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/fund_report" class="slide-item" >Fund Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/ledger_report" class="slide-item">Ledger Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/commission_report" class="slide-item">Commission Modals</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/find_report" class="slide-item">Find Report By Number</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/package_report" class="slide-item"> My Package</a></li>
							<!--
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/dmt-report" class="slide-item" >Dmt Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/aeps-report" class="slide-item" >Aeps Report</a></li>
							
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/pan-report" class="slide-item" >Pan Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/adhar-pay-report" class="slide-item" >Adhar Pay Report</a></li>
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/micro-atm-report" class="slide-item" >Micro Atm Report</a></li>-->
							
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/view-all-redeem-request" class="slide-item" >Redeem Bank Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/view_all_redeem_wallet_report" class="slide-item" >Redeem Wallet Report</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>Retailer/support" class=""><i class="fe fe-compass"></i> Support</a></li>
				</ul>
			</nav>
			<!--Nav-->
		</div>
	</div>
</div>
<!--Horizontal-main -->



<!-- Modal effects -->
<div class="modal" id="successModal">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content tx-size-sm">
			<div class="modal-body tx-center pd-y-20 pd-x-20">
				<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button> <i class="icon ion-ios-checkmark-circle-outline tx-100 tx-success lh-1 mg-t-20 d-inline-block"></i>
				<h4 class="tx-success tx-semibold mg-b-20">Success!</h4>
				<p class="mg-b-20 mg-x-20"><?php echo $this->session->flashdata('success'); ?>.</p><button aria-label="Close" class="btn ripple btn-success pd-x-25" data-dismiss="modal" type="button">OK</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="printModal">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content tx-size-sm">
			<div class="modal-body tx-center pd-y-20 pd-x-20">
				<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button> <i class="icon ion-ios-checkmark-circle-outline tx-100 tx-success lh-1 mg-t-20 d-inline-block"></i>
				<h4 class="tx-success tx-semibold mg-b-20">Success!</h4>
				<p class="mg-b-20 mg-x-20"><?php echo $this->session->flashdata('print'); ?>.</p>
				<a class="btn btn-main-primary btn-block nextBtn" href="<?php echo $this->session->flashdata('printid'); ?>">Print</a><br>
				<button aria-label="Close" class="btn ripple btn-success pd-x-25" data-dismiss="modal" type="button">OK</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="errorModal">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content tx-size-sm">
			<div class="modal-body tx-center pd-y-20 pd-x-20">
				<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button> <i class="icon icon ion-ios-close-circle-outline tx-100 tx-danger lh-1 mg-t-20 d-inline-block"></i>
				<h4 class="tx-danger mg-b-20">Error!</h4>
				<p class="mg-b-20 mg-x-20"><?php echo $this->session->flashdata('error'); ?></p><button aria-label="Close" class="btn ripple btn-danger pd-x-25" data-dismiss="modal" type="button">OK</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="pendingModal">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content tx-size-sm">
			<div class="modal-body tx-center pd-y-20 pd-x-20">
				<h4 class="sa-icon sa-warning pulseWarning">Pending!</h4>
				<p class="mg-b-20 mg-x-20"><?php echo $this->session->flashdata('pending'); ?></p><button aria-label="Close" class="btn ripple btn-warning pd-x-25" data-dismiss="modal" type="button">OK</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal effects-->
<div class="sa-icon sa-warning pulseWarning" style="display: block;">
<span class="sa-body pulseWarningIns"></span>
<span class="sa-dot pulseWarningIns"></span>
</div>

<?php 
$successStatus = false;
if(!empty($this->session->flashdata('success'))){
    $successStatus = true;
}

if($successStatus == true){ ?>

<script type="text/javascript">
    window.onload = function() {
        $('#successModal').modal('show');
    };
</script>

<?php } ?>

<?php 
$printStatus = false;
if(!empty($this->session->flashdata('printid'))){
    $printStatus = true;
}

if($printStatus == true){ ?>

<script type="text/javascript">
    window.onload = function() {
        $('#printModal').modal('show');
    };
</script>

<?php } ?>

<?php 
$errorStatus = false;
if(!empty($this->session->flashdata('error'))){
    $errorStatus=true;
}
if($errorStatus == true){ ?>

    <script type="text/javascript">
        window.onload = function() {
            $('#errorModal').modal('show');
        };
    </script>

<?php } ?>


<?php 
$pendingStatus = false;
if(!empty($this->session->flashdata('pending'))){
    $pendingStatus=true;
}
if($pendingStatus == true){ ?>

    <script type="text/javascript">
        window.onload = function() {
            $('#pendingModal').modal('show');
        };
    </script>

<?php } ?>
