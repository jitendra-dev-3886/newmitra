<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('admin/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('admin/loader'); ?>

		<?php $this->load->view('admin/header'); ?>
		
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
									Update Offer Details
								</div>
								<form action="<?php echo site_url('admin/offer_details_update'); ?>" method="post" enctype="multipart/form-data">
									<div class="row row-sm">
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Offer Title: <span class="tx-danger"></span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-spiral tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php echo $offer[0]->title; ?>" placeholder="Enter Offer Title" type="text"  name="title">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Offer Weblink: <span class="tx-danger"></span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-spiral tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php echo $offer[0]->weblink; ?>" placeholder="Enter Weblink" type="text"  name="weblink">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Offer Description: <span class="tx-danger"></span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-spiral tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php echo $offer[0]->description; ?>" placeholder="Enter Description" type="text"  name="description">
    											</div>
											</div>
									    </div>
									     <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Offer Amount: <span class="tx-danger"></span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-spiral tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php echo $offer[0]->amount; ?>" placeholder="Enter Amount" type="text"  name="amount">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
    									        <div class="form-group">
    												<label class="form-label">Image: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" value="<?php //echo $offer[0]->banner_image; ?>" placeholder="Enter Account Number" type="file"  name="banner_image">
        											</div>
    											</div>
    									    </div>
    									
    									   <input class="form-control" value="<?php echo $offer[0]->offer_id; ?>" type="hidden"  name="offer_id">
										
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
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>


</html>