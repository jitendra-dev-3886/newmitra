<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('master/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('master/loader'); ?>

		<?php $this->load->view('master/header'); ?>
		
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Member Management</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($this->uri->segment('2')); ?></li>
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
									<!-- row -->
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
							    <div class="main-content-label mg-b-5">
									Update Bank Details
								</div>
								<form action="<?php echo site_url('master/bank-details-update'); ?>" method="post">
									<div class="row row-sm">
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Holder Name: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-user tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php echo $bank[0]->bank_account; ?>" placeholder="Enter Holder Name" type="text" required name="holder">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Bank Name: <span class="tx-danger"></span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
            										<input class="form-control" value="<?php echo $bank[0]->bank_name; ?>" placeholder="Enter Bank Name" type="text" required name="bank">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Branch: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-spiral tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php echo $bank[0]->branch_name; ?>" placeholder="Enter Branch Name" type="text" required name="branch">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
    									        <div class="form-group">
    												<label class="form-label">Account Number: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" value="<?php echo $bank[0]->account_id; ?>" placeholder="Enter Account Number" type="text" required name="account">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-6">
    									        <div class="form-group">
    												<label class="form-label">IFSC Code: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-link tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" value="<?php echo $bank[0]->ifsc_code; ?>" placeholder="Enter IFSC Code" type="text" required name="ifsc">
        											</div>
    											</div>
    									    </div>
    									   
    									   <input class="form-control" value="<?php echo $bank[0]->bank_id; ?>" type="hidden" required name="id">
										
										<div class="col-12" style="margin-top:2%"><button class="btn btn-main-primary pd-x-20 mg-t-10" type="submit">Update Details</button></div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>


</html>