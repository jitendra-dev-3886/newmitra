<style>
    /*@media only screen and (min-width: 600px) {
      .horizontalMenu {
        margin-left:2%
      }
    }*/
</style>
<!--
style="margin-left:7%"-->

<!-- main-header opened -->
<div class="main-header nav nav-item hor-header">
	<div class="container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="<?php echo base_url();?>master/dashboard">
				<img src="../../assets/logo.jpg" class="logo-white ">
			</a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-user"></i> <b> Master (<?php echo $this->session->userdata('mas_mobile');?>)</b></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-phone"></i> Support : <?php echo $this->config->item('mobile'); ?></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="mailto:<?php echo $this->config->item('support'); ?>"><i class="fa fa-envelope"></i> Email : <?php echo $this->config->item('support'); ?></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"> IP :- <?php echo $this->session->userdata('ip_address')?></a>
			
		</div>
		<div class="main-header-right">
		    <a class="new nav-link full-screen-link" href="#" style="color:white;width:140px" >
		     <?php $bal=$this->db_model->getAlldata("select * from exr_trnx where txn_agentid = '".$this->session->userdata('mas_id')."' order by txn_id desc limit 1");?>
		     <i class="fas fa-wallet" style="margin-left:0px"></i>&nbsp;&nbsp;<b>₹ <?php echo !$bal[0]->txn_clbal ? 0 : round($bal[0]->txn_clbal) ;?> /-</b></a>
		     
		     <!--<a class="new nav-link full-screen-link" href="#" style="color:white;width:200px" >
		     <?php $bal=$this->db_model->getAlldata("SELECT sum(`aeps_txn_clbal`) as totalBal FROM aeps_exr_trnx WHERE `aeps_txn_id` IN ( SELECT MAX(`aeps_txn_id`) FROM aeps_exr_trnx GROUP BY `aeps_txn_agentid` ) and aeps_txn_agentid = '".$this->session->userdata('mas_id')."'");?>
		     <img src ="../../assets/img/icic.png" style="width:30px" />&nbsp<b>₹ <?php echo !$bal[0]->totalBal ? 0 : round($bal[0]->totalBal,2) ;?> /-</b></a>
		     -->
		     
		     <!--<a class="new nav-link full-screen-link" href="#" style="color:white;width:200px" >
		     <?php $bal=$this->db_model->getAlldata("SELECT sum(`aeps_txn_clbal`) as totalBal FROM aeps_exr_trnx WHERE `aeps_txn_id` IN ( SELECT MAX(`aeps_txn_id`) FROM aeps_exr_trnx GROUP BY `aeps_txn_agentid` )");?>
		     <img src ="../../assets/img/icic.png" style="width:30px" />&nbsp<b>₹ <?php echo !$bal[0]->totalBal ? 0 : $bal[0]->totalBal ;?> /-</b></a>
		     -->
				<!--<div class="dropdown main-profile-menu nav nav-item nav-link">
					<a class="profile-user d-flex" href="#"><img src="../../assets/user.png" alt="user-img" class="rounded-circle mCS_img_loaded" style="width:50px"><span></span></a>
					<div class="dropdown-menu">
						<div class="main-header-profile header-img">
							<div class="main-img-user"><img alt="" src="../../assets/logo.jpg?<?php echo rand(00000,99999); ?>"></div>
							<h6><?php echo $this->session->userdata('name'); ?> </h6><span>MASTER</span>
						</div>
						<a class="dropdown-item" href="<?php echo site_url('master/profile'); ?>"><i class="far fa-user"></i> My Profile</a>
						<a class="dropdown-item" href="<?php echo site_url('master/account'); ?>"><i class="fas fa-sliders-h"></i> Account Settings</a>
						<a class="dropdown-item" href="<?php echo site_url('master/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
					</div>
				</div>-->
				
				<div class="nav nav-item  navbar-nav-right ml-auto">
					<!--<div class="nav-item full-screen fullscreen-button">
						<a class="new nav-link full-screen-link" href="#"><i class="fe fe-maximize"></i></span></a>
					</div>-->
					<div class="dropdown main-profile-menu nav nav-item nav-link">
					    <?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('mas_id')));?>
						<a class="profile-user d-flex dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><img src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';}?>" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<div class="main-header-profile header-img">
								<div class="main-img-user"><img alt="" src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';} ?>"></div>
								<h6><?php echo $this->session->userdata('mas_name');?></h6><span><?php echo $this->session->userdata('mas_email');?></span>
							</div>
							<a class="dropdown-item" href="<?php echo base_url();?>master/profile"><i class="far fa-user"></i> My Profile</a>
							<a class="dropdown-item" href="<?php echo base_url();?>master/password_and_pin"><i class="far fa-edit"></i> Update Password & Pin</a>
							<a class="dropdown-item" href="<?php echo base_url();?>master/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- main-header closed -->
<!--Horizontal-main -->
<div class="sticky sticky-pin">
	<div class="horizontal-main hor-menu clearfix side-header">
		<div class="horizontal-mainwrapper container clearfix">
			<!--Nav-->
			<nav class="horizontalMenu clearfix">
				<ul class="horizontalMenu-list" >
					<li aria-haspopup="true"><a href="<?php echo base_url();?>master" class=""><i class="fe fe-airplay  menu-icon"></i> Dashboard</a></li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-bar-chart-2"></i> Report <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/recharge-report" class="slide-item">Live Recharge Report</a></li>
							
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/pending-recharge-report" class="slide-item" >Recharge Report</a></li>
							<!--
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/pending-recharge-report" class="slide-item" >Pending Recharge Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/failed-recharge-report" class="slide-item" >Failed Recharge Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/success-recharge-report" class="slide-item" >Success Recharge Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/transaction-report" class="slide-item">Transaction Report</a></li><!--
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/dmt-transaction-report" class="slide-item">DMT Transaction Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/redeem-request-history" class="slide-item">Redeem Bank Report</a></li>
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>master/redeem-wallet-report" class="slide-item">Redeem Wallet Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/member-ledger" class="slide-item">Member Ledger</a></li><!--
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/admin-ledger" class="slide-item">Admin Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/aeps-ledger" class="slide-item">AEPS Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/pancoupon-history" class="slide-item">Pan Coupon</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/micro-atm" class="slide-item">Micro Atm</a></li>-->
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-users"></i> Members <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/view-all-distributor" class="slide-item">Distributors</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/add-member" class="slide-item">Add Members</a></li>
						</ul>
					</li>
					<!--<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-package"></i> Commission <i class="fe fe-chevron-down horizontal-icon"></i></a>-->
					<!--	<ul class="sub-menu">-->
					<!--		<li aria-haspopup="true"><a href="<?php echo base_url();?>master/commission-package" class="slide-item"> Commission Package</a></li>-->
					<!--		<li aria-haspopup="true"><a href="<?php echo base_url();?>master/aeps-commission-package" class="slide-item" >AEPS Package Commission</a></li>-->
					<!--		<li aria-haspopup="true"><a href="<?php echo base_url();?>master/dmt-commission-package" class="slide-item" >DMT Package Commission</a></li>-->
					<!--		<li aria-haspopup="true"><a href="<?php echo base_url();?>master/coupon-price" class="slide-item">PAN Coupon</a></li>-->
					<!--		<li aria-haspopup="true"><a href="<?php echo base_url();?>master/micro-atm-commission-package" class="slide-item">Micro ATM Package Commission</a></li>-->
					<!--	</ul>-->
					<!--</li>-->
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fa fa-ticket-alt"></i> Tickets <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/view-all-open-tickets" class="slide-item">Open Tickets</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/view-all-closed-tickets" class="slide-item" >Closed Tickets</a></li>
						</ul>
					</li>
					
					<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>master/" class=""><i class="fe fe-layers"></i> DMT</a></li>-->
<!--					<li aria-haspopup="true"><a class="sub-icon"><i class="fe fe-layers"></i> AEPS <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>master/aeps-users" class="slide-item">AEPS Users</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/aeps_transaction" class="slide-item">Transactions</a></li>
						</ul>
					</li>
					-->
					<!--<li aria-haspopup="true"><a href="<?php echo site_url('master/api-switching') ?>" class="sub-icon"><i class="fe fe-aperture"></i> API Switching</a>
					-->
					</li>
					
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-codepen"></i> Wallet <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/credit-wallet" class="slide-item">Credit Wallet</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/debit-wallet" class="slide-item">Debit Wallet</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-book"></i> More <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>master/daybook" class="slide-item">Day Book</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/bank-details" class="slide-item">Bank Details</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>master/assign-user-creadit" class="slide-item">Assign User Credit</a></li>
						</ul>
					</li>
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
<!-- End Modal effects-->

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
