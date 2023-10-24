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
								<li class="breadcrumb-item active" aria-current="page">Insurance</li>
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
									Insurance<br>
								</div>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row"><br></div>
								<div class="row">
									                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										    <form action="https://www.easypolicy.com/epnew/landing/landing.aspx" method="post" target="_blank">
                        											<!--<h5 class="card-title mg-b-20">Your Postpaid Details</h5>-->
                        										<div class="row" style="display:none;">	
                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="utm_source" readonly value="edigitalvillage" type="text">
                        											</div>
                        											</div>
                        											<div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="utm_medium" readonly value="edigitalvillage"  type="text">
                        											</div>
                        											</div>
                        										</div>
                        										<div class="row" style="display:none;">	
                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="utm_campaign" readonly value="edigitalvillage" type="text">
                        											</div>
                        											</div>
                        											<div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="utm_term" readonly value="edigitalvillage"  type="text">
                        											</div>
                        											</div>
                        										</div>
                        										<div class="row" style="display:none;">	
                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="partnerleadid" readonly value="<?php echo rand(000000,999999)?>" type="text">
                        											</div>
                        											</div>
                        											<div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="partneragentid" readonly value="<?php echo $this->session->userdata('cus_mobile');?>"  type="text">
                        											</div>
                        											</div>
                        										</div>
                        										<div class="row" style="display:none;">	
                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="udf1" readonly value="" type="text">
                        											</div>
                        											</div>
                        											<div class="col-md-6 col-lg-6 col-xl-6">
                        											<div class="form-group">
                        												<input class="form-control" name="udf2" readonly value=""  type="text">
                        											</div>
                        											</div>
                        										</div>
                        										<button class="btn btn-main-primary btn-block" type="submit">Proceed</button>
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