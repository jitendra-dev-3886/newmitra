<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>

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
						<h4 class="content-title mb-2">Hi, welcome back!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">DMT</li>
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
						<div class="card">
							<div class="card-body">
								<div class="main-content-label mg-b-5">
									DMT Verify OTP<br>
								</div>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row"><br></div>
								<div class="row">
		                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
									<div class="card card-body pd-20 pd-md-40 border shadow-none">
										<!--<h5 class="card-title mg-b-20">Your Postpaid Details</h5>-->
										<form action="<?php echo site_url('Retailer/verifySenderOtp')?>" method="post">
        									<div class="row">	
        									    <div class="col-md-12 col-lg-12 col-xl-12">
        										    <div class="form-group">
        												<label class="form-label">OTP: <span class="tx-danger">*</span></label>
        												<div class="input-group">
            												<div class="input-group-prepend">
                    											<div class="input-group-text">
                    												<i class="typcn typcn-phone tx-24 lh--9 op-6"></i>
                    											</div>
                    										</div>
            												<input class="form-control" name="otp" placeholder="Enter OTP Sent On Entered Mobile Number" required="" type="number" maxlength="6">
            											</div>
        											</div>
            										<input class="form-control" name="VerifyReferenceNo" value="<?php  echo $VerifyReferenceNo ?>" required="" type="hidden">
        										</div>
        									</div>
        									<button type="submit" class="btn btn-main-primary btn-block" name="login" id="login">Verify OTP</button>
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
	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>
	

</html>