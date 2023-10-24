<style>
    @media only screen and (min-width: 600px) {
      .horizontalMenu {
        margin-left:5%
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
			<a class="header-brand" href="<?php echo base_url();?>distributor">
				<img src="../../assets/logo.jpg" class="logo-white ">
			</a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-user"></i> <b> Distributor (<?php echo $this->session->userdata('dis_id');?>)</b></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="#"><i class="fa fa-phone"></i> Support : <?php echo $this->config->item('mobile'); ?></a>
			<a class="new nav-link full-screen-link" style="color:white;" href="mailto:<?php echo $this->config->item('support'); ?>"><i class="fa fa-envelope"></i> Email : <?php echo $this->config->item('support'); ?></a>
		</div><!-- search -->
		<div class="main-header-right">
		    <a class="new nav-link full-screen-link" href="#" style="color:white;" >
		         <?php $bal=$this->db_model->getAlldata("SELECT txn_clbal as amt FROM `exr_trnx` where txn_agentid='".$this->session->userdata('dis_id')."' ORDER BY txn_id desc limit 1");?>
		        <i class="fas fa-wallet"></i>&nbsp&nbsp&nbsp&nbsp&nbsp <b>₹ <?php if(!empty($bal[0]->amt)){echo $bal[0]->amt; }else{ echo '0'; }?> /-</b></a>
			<div class="nav nav-item nav-link" id="bs-example-navbar-collapse-1">
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
				</div>
				<!--<div class="nav nav-item  navbar-nav-right ml-auto">
					<div class="nav-item full-screen fullscreen-button">
						<a class="new nav-link full-screen-link" href="#"><i class="fe fe-maximize"></i></span></a>
					</div>
					<div class="dropdown main-profile-menu nav nav-item nav-link">
					    <?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('dis_id')));?>
						<a class="profile-user d-flex" href="#"><img src="<?php echo base_url(); echo $profile[0]->profile_img;?>" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>
						<div class="dropdown-menu">
							<div class="main-header-profile header-img">
								<div class="main-img-user"><img alt="" src="<?php echo base_url(); echo $profile[0]->profile_img;?>"></div>
								<h6><?php echo $this->session->userdata('dis_name');?></h6><span><?php echo $this->session->userdata('dis_email');?></span>
							</div>
							<a class="dropdown-item" href="<?php echo site_url('distributor/profile'); ?>"><i class="far fa-user"></i> My Profile</a>
							<a class="dropdown-item" href="<?php echo site_url('distributor/account'); ?>"><i class="fas fa-sliders-h"></i> Account Settings</a>
							<a class="dropdown-item" href="<?php echo site_url('distributor/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
						</div>
					</div>
				</div>-->
				<div class="nav nav-item  navbar-nav-right ml-auto">
					<!--<div class="nav-item full-screen fullscreen-button">
						<a class="new nav-link full-screen-link" href="#"><i class="fe fe-maximize"></i></span></a>
					</div>-->
					<div class="dropdown main-profile-menu nav nav-item nav-link">
					    <?php $profile=$this->db_model->getwhere('customers',array('cus_id'=>$this->session->userdata('dis_id')));?>
						<a class="profile-user d-flex dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"><img src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';}?>" alt="user-img" class="rounded-circle mCS_img_loaded"><span></span></a>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<div class="main-header-profile header-img">
								<div class="main-img-user"><img alt="" src="<?php echo base_url(); if($profile[0]->profile_img){echo $profile[0]->profile_img; }else{echo 'assets/user.png';} ?>"></div>
								<h6><?php echo $this->session->userdata('dis_name');?></h6><span><?php echo $this->session->userdata('dis_email');?></span>
							</div>
							<a class="dropdown-item" href="<?php echo base_url();?>distributor/profile"><i class="far fa-user"></i> My Profile</a>
							<a class="dropdown-item" href="<?php echo base_url();?>distributor/password_and_pin"><i class="far fa-edit"></i> Update Password & Pin</a>
							<a class="dropdown-item" href="<?php echo base_url();?>distributor/logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
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
					<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor" class=""><i class="fe fe-airplay  menu-icon"></i> Dashboard</a></li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-bar-chart-2"></i> Report <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/recharge-report" class="slide-item">All Recharge Report</a></li>
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/pending-recharge-report" class="slide-item" >Pending Recharge Report</a></li>-->
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/failed-recharge-report" class="slide-item" >Failed Recharge Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/success-recharge-report" class="slide-item" >Success Recharge Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/credit-wallet-report" class="slide-item">Credit Wallet Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/debit-wallet-report" class="slide-item">Debit Wallet Report</a></li>
							
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/transaction-report" class="slide-item">Transaction Report</a></li>
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/dmt-transaction-report" class="slide-item">DMT Transaction Report</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/redeem-request-history" class="slide-item">Redeem Request Report</a></li>
						    <li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/redeem-wallet-report" class="slide-item">Redeem Wallet Report</a></li>-->
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/member-ledger" class="slide-item">Member Ledger</a></li>
<!--							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/admin-ledger" class="slide-item">Admin Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/aeps-ledger" class="slide-item">AEPS Ledger</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/pancoupon-history" class="slide-item">Pan Coupon</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/micro-atm" class="slide-item">Micro Atm</a></li>-->
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-users"></i> Members <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/view-all-retailer" class="slide-item" >Retailers</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/add-member" class="slide-item">Add Members</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-package"></i> Commission <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/commission-package" class="slide-item"> Commission Package</a></li>
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/aeps-commission-package" class="slide-item" >AEPS Package Commission</a></li>-->
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/dmt-commission-package" class="slide-item" >DMT Package Commission</a></li>-->
							<!--<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/coupon-price" class="slide-item">PAN Coupon</a></li>-->
						</ul>
					</li>
					<!--<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-layers"></i> DMT <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/electricity" class="slide-item"> Commission Package</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/insurance" class="slide-item" >API Package Commission</a></li>
						</ul>
					</li>-->
<!--					<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/" class=""><i class="fe fe-layers"></i> DMT</a></li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/" class=""><i class="fe fe-aperture"></i> AEPS</a></li>-->
					<li aria-haspopup="true"><a href="#" class="sub-icon"><i class="fe fe-codepen"></i> Wallet <i class="fe fe-chevron-down horizontal-icon"></i></a>
						<ul class="sub-menu">
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/credit-wallet" class="slide-item">Credit Wallet</a></li>
							<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/debit-wallet" class="slide-item">Debit Wallet</a></li>
						</ul>
					</li>
					<li aria-haspopup="true"><a href="<?php echo base_url();?>distributor/daybook" class=""><i class="fe fe-book"></i> Day Book</a></li>
				</ul>
			</nav>
			<!--Nav-->
		</div>
	</div>
</div>
<!--Horizontal-main -->
<!-- Modal effects -->
<div class="modal" id="modaldemo8">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content modal-content-demo">
			<div class="modal-header">
				<h6 class="modal-title">Modal Header</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<h6>Modal Body</h6>
				<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
			</div>
			<div class="modal-footer">
				<button class="btn ripple btn-primary" type="button">Save changes</button>
				<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal effects-->

<script type="text/javascript">
    /*window.onload = function() {
        $('#modaldemo8').modal('show');
    };*/
</script>

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
