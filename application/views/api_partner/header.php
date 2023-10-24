<!-- main-header opened -->
<div class="main-header nav nav-item hor-header">
	<div class="container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="<?php echo base_url();?>api_partner/dashboard">
				<img src="../../assets/logo.jpg" class="logo-white ">
			</a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-user"></i> <b> api_partner (<?php echo $this->session->userdata('api_id');?>)</b></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-phone"></i> Support : <?php $sup = $this->db_model->getAlldata("select * from support");echo $sup[0]->mobile; ?></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="mailto:<?php echo $sup[0]->email; ?>"><i class="fa fa-envelope"></i> Email : <?php echo $sup[0]->email; ?></a>
			
			<a class="new nav-link full-screen-link" style="color:white;" href="#"> IP :- <?php echo $this->session->userdata('ip_address')?></a>
		</div><!-- search -->
		<div class="main-header-right">
		    <?php 
		        if($mobile=$this->session->userdata('senderMobile')){ 
		            $mobile = $mobile;
                    $token = 'e1626d22b42148758d';
                    $id = rand(1000000000,9999999999);
                    $data = "msg=E06005~e1626d22b42148758d~$id~$mobile~NA~NA";
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
			<a class="new nav-link full-screen-link" style="color:white;" href="<?php echo base_url()?>api_partner/dmtLogOut"><i class="fa fa-power-off"></i> <b> Dmt Logout</b></a>
			<?php } ?>
			
		    <a class="new nav-link full-screen-link" href="#" style="color:white;text-align: center;" >
		         <?php $bal=$this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('api_id')."' ORDER BY txn_id desc limit 1");?>
		        <i class="fas fa-wallet"></i>&nbsp&nbsp&nbspBALANCE<br> <b style="margin-left: 25px;">₹<?php echo $bal[0]->amt;?>/-</b></a>
		        
		         <a class="new nav-link full-screen-link" href="<?php echo site_url('api_partner/payout') ?>" style="color:white;text-align: center;margin-top:10px" >
		         <?php $aepsbal=$this->db_model->getAlldata("SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='".$this->session->userdata('api_id')."' ORDER BY aeps_txn_id DESC LIMIT 1");?>
	            <img src="<?php echo base_url()?>assets/img/icic.png" height="25px" width="25px"><b style="margin-left: 5px;">₹<?php echo $aepsbal[0]->aeps_txn_clbal;?>/-</b><br><b style="margin-left: 25px;"><button style="background-color: #A94098;color:white">REDEEM</button></b></a>
		          
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
				<div class="nav nav-item  navbar-nav-right ml-auto">
					<!--<div class="nav-item full-screen fullscreen-button">
						<a class="new nav-link full-screen-link" href="#"><i class="fe fe-maximize"></i></span></a>
					</div>-->
					<div class="dropdown main-profile-menu nav nav-item nav-link">
					    <?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('api_id')));?>
						<a class="profile-user d-flex" href="#"><img src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';}?>" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>
						<div class="dropdown-menu">
							<div class="main-header-profile header-img">
								<div class="main-img-user"><img alt="" src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';} ?>"></div>
								<h6><?php echo $this->session->userdata('api_name');?></h6><span><?php echo $this->session->userdata('api_email');?></span>
							</div>
							<a class="dropdown-item" href="<?php echo base_url();?>api_partner/profile"><i class="far fa-user"></i> My Profile</a>
							<a class="dropdown-item" href="<?php echo base_url();?>api_partner/password_and_pin"><i class="far fa-edit"></i> Update Password & Pin</a>
							<a class="dropdown-item" href="<?php echo base_url();?>api_partner/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
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
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/dashboard" class=""><i class="fe fe-airplay  menu-icon"></i> Dashboard</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/recharge_report" class=""><i class="fe fe-database"></i> Recharge</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/dmt-report" class=""><i class="fe fe-layers"></i> DMT</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/adhar-pay-report" class=""><i class="icon ion-ios-rocket"></i> Aadhar Pay</a></li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-aperture"></i> AEPS <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/aeps_ledger" class="slide-item">Aeps Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/aeps-report" class="slide-item">Aeps Report</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-compass"></i> Redeem <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/view-all-redeem-request" class="slide-item">Redeem Request</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/view_all_redeem_wallet_report" class="slide-item">Redeem Wallet Report</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/package_report" class=""><i class="mdi mdi-account-card-details"></i> Package</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/pan-report" class=""><i class="icon ion-ios-pie"></i> Pan Card</a></li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-codepen"></i> Fund <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/fund_request" class="slide-item">Request</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/fund_report" class="slide-item">Report</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>api_partner/micro-atm-report" class="sub-icon"><i class="fe fe-book"></i>Micro ATM</a>
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
