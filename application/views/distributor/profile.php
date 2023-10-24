<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('distributor/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('distributor/loader'); ?>

		<?php $this->load->view('distributor/header'); ?>

		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Profile</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page">PROFILE</li>
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
				
                <div class="row row-sm">
					<!-- Col -->
					<div class="col-lg-4">
					    <?php if($profile){ foreach($profile as $pro){?>
    						<div class="card mg-b-20">
    							<div class="card-body">
    								<div class="pl-0">
    									<div class="main-profile-overview">
    										<div class="main-img-user profile-user"><img alt="" src="<?php echo base_url(); echo $pro->profile_img;?>" style="border-radius:10%;border:1px solid #eee"><a href="JavaScript:void(0);" class="fas fa-camera profile-edit"></a></div>
    										<div class="d-flex justify-content-between mg-b-20">
    											<div>
    												<h5 class="main-profile-name">Profit Pay</h5>
    												<p class="main-profile-name-text">Distributor</p>
    											</div>
    										</div>
    										
    										<!-- main-profile-bio -->
    										<!--<div class="main-profile-work-list">
    											<div class="media">
    												<div class="media-logo bg-primary-transparent text-primary">
    													<i class="icon ion-logo-buffer"></i>
    												</div>
    												<div class="media-body">
    													<h6>Studied at <a href="#">University</a></h6><span>2004-2008</span>
    													<p>Graduation: Bachelor of Science in Computer Science</p>
    												</div>
    											</div>
    										</div>-->
    										<!-- main-profile-work-list -->
    
    										<hr class="mg-y-30">
    										<label class="main-content-label tx-13 mg-b-20">Personal Details</label>
    										<div class="main-profile-social-list">
    											<div class="media">
    												<div class="media-icon bg-success-transparent text-success">
    													<i class="icon ion-logo-twitter"></i>
    												</div>
    												<div class="media-body">
    													<span>Outlet Name</span> <a href="#"><?php echo $pro->cus_outlate;?></a>
    												</div>
    											</div>
    											<div class="media">
    												<div class="media-icon bg-info-transparent text-info">
    													<i class="icon ion-logo-instagram"></i>
    												</div>
    												<div class="media-body">
    													<span>Name</span> <a href="#"><?php echo $pro->cus_name;?></a>
    												</div>
    											</div>
    											<div class="media">
    												<div class="media-icon bg-danger-transparent text-danger">
    													<i class="icon ion-logo-facebook"></i>
    												</div>
    												<div class="media-body">
    													<span>Pancard Number</span> <a href="#"><?php echo $pro->cus_panno;?></a>
    												</div>
    											</div>
    										</div><!-- main-profile-social-list -->
    									</div><!-- main-profile-overview -->
    								</div>
    							</div>
    						</div>
    						<div class="card mg-b-20">
    							<div class="card-body">
    								<div class="main-content-label tx-13 mg-b-25">
    									Conatct
    								</div>
    								<div class="main-profile-contact-list">
    									<div class="media">
    										<div class="media-icon bg-primary-transparent text-primary">
    											<i class="icon ion-md-phone-portrait"></i>
    										</div>
    										<div class="media-body">
    											<span>Mobile</span>
    											<div>
    												<?php echo $this->encryption_model->decode($pro->cus_mobile);?>
    											</div>
    										</div>
    									</div>
    									<div class="media">
    										<div class="media-icon bg-success-transparent text-success">
    											<i class="icon ion-logo-slack"></i>
    										</div>
    										<div class="media-body">
    											<span>Email</span>
    											<div>
    												<?php echo $pro->cus_email;?>
    											</div>
    										</div>
    									</div>
    									<div class="media">
    										<div class="media-icon bg-info-transparent text-info">
    											<i class="icon ion-md-locate"></i>
    										</div>
    										<div class="media-body">
    											<span>Current Address</span>
    											<div>
    												<?php echo $pro->cus_address;?><br>
    												<?php if($state){foreach($state as $st){if($st->state_id == $pro->cus_state){echo $st->state_name.','; } }}?>  
    												<?php if($city){foreach($city as $ct){if($ct->city_id == $pro->cus_city){echo $ct->city_name.','; } }}?>  
    												<?php echo $pro->cus_pincode;?>
    											</div>
    										</div>
    									</div>
    									<div class="media">
    										<div class="media-icon bg-info-transparent text-info">
    											<i class="icon ion-md-locate"></i>
    										</div>
    										<div class="media-body">
    											<span>Shop Address</span>
    											<div>
    												<?php echo $pro->cus_shop_address;?>
    											</div>
    										</div>
    									</div>
    								</div><!-- main-profile-contact-list -->
    							</div>
    						</div>
						<?php }}?>
					</div>
					<!-- /Col -->

					<!-- Col -->
					<div class="col-lg-8">
						<div class="card">
						    <form action="<?php echo base_url();?>distributor/profile" method="post" enctype="multipart/form-data">
                        			 <?php if($profile){ foreach($profile as $pro){?>
							<div class="card-body">
								<div class="mb-4 main-content-label">Personal Information</div>
								
									
									<div class="mb-4 main-content-label">Name</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Outlet Name</label>
											</div>
											<div class="col-md-9">
												<input type="text" required="" name="cus_outlate" class="form-control"  placeholder="Outlet Name" value="<?php echo $pro->cus_outlate;?>">
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Name</label>
											</div>
											<div class="col-md-9">
												<input type="text" class="form-control"  required="" name="cus_name" placeholder="Name" value="<?php echo $pro->cus_name;?>">
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Pancard Number</label>
											</div>
											<div class="col-md-9">
												<input type="text" class="form-control"  required="" name="cus_panno" placeholder="Pancard Number" value="<?php echo $pro->cus_panno;?>">
											</div>
										</div>
									</div>
									<div class="mb-4 main-content-label">Contact Info</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Email<i>(required)</i></label>
											</div>
											<div class="col-md-9">
												<input type="text" class="form-control"  required="" name="cus_email" placeholder="Email" value="<?php echo $pro->cus_email;?>" >
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Resident Address</label>
											</div>
											<div class="col-md-9">
												<input type="text" class="form-control"  required="" name="cus_address" placeholder="Resident Address" value="<?php echo $pro->cus_address;?>">
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Phone</label>
											</div>
											<div class="col-md-9">
												<input type="text" class="form-control"  required="" readonly name="cus_mobile" placeholder="Mobile No" value="<?php echo $this->encryption_model->decode($pro->cus_mobile);?>" >
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">State / Province</label>
											</div>
											<div class="col-md-9">
											    <select class="form-control select2-no-search" name="state">
                        										<option label="Choose one"></option>
                        										<?php if($state){foreach($state as $st){?>
                        											<option value="<?php echo $st->state_id; ?>" <?php if($st->state_id == $pro->cus_state) echo 'selected'; ?>><?php echo $st->state_name; ?></option>
                        										<?php }}?>
                        									</select>
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Select City</label>
											</div>
											<div class="col-md-9">
											<select class="form-control select2-no-search" name="city" >
                        										<option label="Choose one"></option>
                        										<?php if($city){foreach($city as $ct){?>
                        											<option value="<?php echo $ct->city_id; ?>" <?php if($ct->city_id == $pro->cus_city) echo 'selected'; ?>><?php echo $ct->city_name; ?></option>
                        										<?php }}?>
                        									</select>
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Pin Code</label>
											</div>
											<div class="col-md-9">
												<input type="text" class="form-control"  required="" name="cus_pincode" placeholder="Pin Code" value="<?php echo $pro->cus_pincode;?>" >
											</div>
										</div>
									</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Shop Address</label>
											</div>
											<div class="col-md-9">
												<textarea class="form-control" required="" name="cus_shop_address" placeholder="Shop Address"><?php echo $pro->cus_shop_address;?></textarea>
											</div>
										</div>
									</div>
									<div class="mb-4 main-content-label">Profile</div>
									<div class="form-group ">
										<div class="row">
											<div class="col-md-3">
												<label class="form-label">Image</label>
											</div>
											<div class="col-md-9">
												<input type="file" name="image" class="dropify" data-height="100" />
											</div>
										</div>
									</div>
								
							</div>
							<div class="card-footer">
								<button type="submit" name="update_profile" class="btn btn-main-primary btn-block">Update Profile</button>
							</div>
							<?php }} ?>	
						</form>
						</div>
					</div>
					<!-- /Col -->
				</div>

			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('distributor/footer'); ?> 
	
	</body>

</html>