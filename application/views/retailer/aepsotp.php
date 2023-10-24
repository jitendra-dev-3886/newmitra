<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>
    <script src="../../assets/jquery-1.12.4.js"></script>
    <style>
        .nav-tabs .nav-link {
    background: linear-gradient(45deg, #e43364, #3858f9) !important;border-color: #2e50fb;
    border-width: 0;
    border-radius: 0;
    padding: 10px 15px;
    line-height: 1.428;
    color: white!important;
}
.nav-tabs .nav-link.active {
    font-weight: 800;
    width: 35%;
    text-align: center;
    letter-spacing: 0.9px;
    border-radius: .5rem;
}
.nav-tabs .nav-link + .nav-link {
    margin-left: 20px !important;
}
    </style>

	<body class="main-body  app">
		
		<?php $this->load->view('retailer/loader'); ?>

		<?php $this->load->view('retailer/header'); ?>

		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
									<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Hi, welcome back <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">One More Step KYC</li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-auto">
						<div class=" d-flex right-page">
						    
							<div class="d-flex justify-content-center mr-5">
							    <div class="">
									<span class="value"><i class="icon ion-md-alarm"></i> <?php echo date("l jS \ F Y h:i:s A");?></span>
								</div>
								
							</div>
						</div>
					</div>
				</div>
				<!-- /breadcrumb -->
					
				<!-- main-content-body -->
				<div class="main-content-body">
				    <div class="col-lg-12 col-md-12">
						<div class="card custom-card" id="tab">
								<div class="card-body">
								<div class="main-content-label mg-b-5">
									AEPS  	
									<!--<?php echo $this->encryption_model->decode('421e44e2e831de39007fc91a99439f0c');?><br>-->
									<!--<?php echo $this->encryption_model->decode('9aaa7196e118852ecd11217921ebd1f7');?>-->
								</div>
								<?php if($this->session->flashdata('message')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('message');?></strong> 
                                </div>
                                <?php }?>
								<div class="row"><br></div>
									<div class="text-wrap">
										<div class="example">
											<div class="border">
											<form role="form" action="<?php echo site_url('retailer/aepskycvalidateotp') ?>" method="post" enctype="multipart/form-data">
											    <div class="card-body tab-content">
												
													<div class="tab-pane active show" id="tabCont1">
													    
                                                        <div class="row">	
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Enter OTP *</label> 
                                    								<input class="form-control" name="otp" placeholder="Enter OTP" type="text">
                                    							</div>
                                							</div>
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                    									            <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red"></label>
                                    							<a href="<?php echo base_url();?>retailer/aepskycresendotp" style="color:red"><i class="fa fa-spinner"></i> <b>Resend OTP </b></a>
			
                                    							</div>
                                							</div>
                                					    </div>
                                    					<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" type="submit" style="padding: 8px 27px;font-size: 15px;">Submit</button>
                        							</div>
												</div>
											</form>
											</div>
										</div>
								</div>
							</div>
						</div>
					</div>	
				</div>
				<!-- / main-content-body -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
		
	<!--<script src="aeps_script.js"></script>	-->
	   <?php $this->load->view('retailer/footer'); ?> 
	</body>
	
</html>