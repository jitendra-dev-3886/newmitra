<style>
    @media only screen and (min-width: 600px) {
      .horizontalMenu {
        margin-left:2%
      }
    }
</style>
<!--
style="margin-left:7%"-->

<!-- main-header opened -->
<div class="main-header nav nav-item hor-header">
	<div class="container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="<?php echo base_url() ?>admin">
				<img src="../../assets/logo.jpg?<?php echo rand(00000,99999); ?>" class="logo-white " style="width:100%;height: %;border-radius:10%">
				<img src="../../assets/logo.jpg?<?php echo rand(00000,99999); ?>" class="logo-default">
				<img src="../../assets/logo.jpg?<?php echo rand(00000,99999); ?>" class="icon-white">
				<img src="../../assets/img/brand/favicon.png" class="icon-default">
			</a>
			
		</div>
		<div class="main-header-right">
		    <a class="new nav-link full-screen-link" href="#" style="color:white;width:140px" >
		     <?php $bal=$this->db_model->getAlldata("select vb_clbal from virtual_balance order by vb_id desc limit 1");?>
		     <i class="fas fa-wallet"></i>&nbsp;&nbsp;<b>₹ <?php echo !$bal[0]->vb_clbal ? 0 : $bal[0]->vb_clbal ;?>/-</b></a>
		     
		     <!--<a class="new nav-link full-screen-link" href="#" style="color:white;width:200px" >
		     <?php $bal=$this->db_model->getAlldata("SELECT sum(`aeps_txn_clbal`) as totalBal FROM aeps_exr_trnx WHERE `aeps_txn_id` IN ( SELECT MAX(`aeps_txn_id`) FROM aeps_exr_trnx GROUP BY `aeps_txn_agentid` )");?>
		     <img src ="../../assets/img/icic.png" style="width:30px" />&nbsp<b>₹ <?php echo !$bal[0]->totalBal ? 0 : round($bal[0]->totalBal,2) ;?> /-</b></a>
		     -->
				<div class="dropdown main-profile-menu nav nav-item nav-link" id="show_profile" >
					<a class="profile-user d-flex" href="#"><img src="../../assets/user.png" alt="user-img" id="profile_click" class="rounded-circle mCS_img_loaded" style="width:50px"><span></span></a>
					<div class="dropdown-menu">
						<div class="main-header-profile header-img">
							<div class="main-img-user"><img alt="" src="<?php echo base_url(); ?>assets/logo.jpg?10022"></div>
							<h6 style="font-size:12px;"><?php echo $this->session->userdata('name'); ?> </h6><span>Admin</span>
						</div>
						<a class="dropdown-item" href="<?php echo site_url('admin/profile'); ?>"><i class="far fa-user"></i> My Profile</a>
						<a class="dropdown-item" href="<?php echo site_url('admin/account'); ?>"><i class="fas fa-sliders-h"></i> Account Settings</a>
						<a class="dropdown-item" href="<?php echo site_url('admin/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
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
					<li aria-haspopup="true"><a href="<?php echo base_url();?>admin" class=""><i class="fe fe-airplay  menu-icon"></i> Dashboard</a></li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-bar-chart-2"></i> Report <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/recharge-report" class="slide-item">Live Recharge Report</a></li>
							
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/all-recharge-report" class="slide-item" >Recharge Report</a></li>
							<!--
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/pending-recharge-report" class="slide-item" >Pending Recharge Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/failed-recharge-report" class="slide-item" >Failed Recharge Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/success-recharge-report" class="slide-item" >Success Recharge Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/transaction-report" class="slide-item">Transaction Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/dmt-transaction-report" class="slide-item">DMT Transaction Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/redeem-request-history" class="slide-item">Redeem Bank Report</a></li>
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/redeem-wallet-report" class="slide-item">Redeem Wallet Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/member-ledger" class="slide-item">Member Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/admin-ledger" class="slide-item">Admin Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/aeps-ledger" class="slide-item">AEPS Ledger</a></li>
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/pancoupon-history" class="slide-item">Pan Coupon</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/micro-atm" class="slide-item">Micro Atm</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view_offer_purchase_history" class="slide-item">Purchase Offer History</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-users"></i> Members <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-members" class="slide-item"> All Members </a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-retailer" class="slide-item" >Retailers</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-distributor" class="slide-item">Distributors</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-master" class="slide-item">Masters</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-api-clients" class="slide-item">API Clients</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/add-member" class="slide-item">Add Members</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-package"></i> Commission <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/commission-scheme" class="slide-item"> Commission Scheme</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/commission-package" class="slide-item"> Commission Package</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/aeps-commission-package" class="slide-item" >AEPS Package Slab</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/dmt-commission-package" class="slide-item" >DMT Package Slab</a></li>
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/coupon-price" class="slide-item">PAN Coupon</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/micro-atm-commission-package" class="slide-item">Micro ATM Package Slab</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/payout-commission-package" class="slide-item">Payout Package Slab</a></li>
						</ul>
					</li>
					<!--<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fa fa-ticket-alt"></i> Tickets <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-open-tickets" class="slide-item">Open Tickets</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-closed-tickets" class="slide-item" >Closed Tickets</a></li>
						</ul>
					</li>-->
					
					<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/" class=""><i class="fe fe-layers"></i> DMT</a></li>-->
					<li aria-haspopup="true"><a class="sub-icon"><i class="fe fe-layers"></i> AEPS <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/aeps-users" class="slide-item">AEPS Users</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/aeps_transaction" class="slide-item">Transactions</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/redeem-request-report" class="slide-item">Redeem Requests</a></li>

						</ul>
					</li>
					
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-book"></i> API Management <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/add_api" class="slide-item">New API Integration</a></li>
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view_all_api" class="slide-item">View All API</a></li>
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/api-switching" class="slide-item">API Switching</a></li>
						    <!--<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/api-switching1" class="slide-item">Priority Wise API Awitching</a></li>-->
						    <!--<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/circle-wise-api-switching" class="slide-item">Circle Wise API Awitching</a></li>-->
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/amount-wise-api-switching" class="slide-item">Amount Wise API Awitching</a></li>
						</ul>
					</li>
					
					<!--<li aria-haspopup="true"><a href="<?php echo site_url('admin/api-switching') ?>" class="sub-icon"><i class="fe fe-aperture"></i> API Switching</a>-->
					
					<!--</li>-->
					
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-codepen"></i> Wallet <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/virtual-balance" class="slide-item">Admin Virtual Balance</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/credit-wallet" class="slide-item">Credit Wallet</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/debit-wallet" class="slide-item">Debit Wallet</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/fund-requests" class="slide-item">Fund Request</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-book"></i> More <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>admin/daybook" class="slide-item">Day Book</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/banner" class="slide-item">Banner</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/offers" class="slide-item">Offers</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/news" class="slide-item">News</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/bank-details" class="slide-item">Bank Details</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/assign-user-creadit" class="slide-item">Assign User Credit</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-open-tickets" class="slide-item">Open Tickets</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/view-all-closed-tickets" class="slide-item" >Closed Tickets</a></li>					
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/enquiry" class="slide-item">Enquiry</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/application_status" class="slide-item">Application Status</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/application_request" class="slide-item">Application Request</a></li>
<!--						<li aria-haspopup="true"><a href="<?php echo base_url();?>admin/service" class="slide-item">Service Down</a></li>-->
-->						</ul>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#profile_click").click(function(){
        $("#show_profile").toggleClass("show");
    })
})
</script>

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
