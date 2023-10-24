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
						<div class="card mg-b-20">
							<div class="card-body">
								<div class="pl-0">
									<div class="main-profile-overview">
										<div class="main-img-user profile-user"><img alt="" src="../../assets/logo.png?<?php echo rand(00000,99999); ?>" style="border-radius:10%;border:1px solid #eee"><a href="JavaScript:void(0);" class="fas fa-camera profile-edit"></a></div>
										<div class="d-flex justify-content-between mg-b-20">
											<div>
												<h5 class="main-profile-name"><?php echo ucfirst($data[0]->cus_name); ?></h5>
												<p class="main-profile-name-text"><?php echo ucfirst($data[0]->username); ?></p>
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
										<label class="main-content-label tx-13 mg-b-20">Social</label>
										<div class="main-profile-social-list">
											<div class="media">
												<div class="media-icon bg-success-transparent text-success">
													<i class="icon ion-logo-twitter"></i>
												</div>
												<div class="media-body">
													<span>Twitter</span> <a href="#"><?php echo $data[0]->twitter; ?></a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-info-transparent text-info">
													<i class="icon ion-logo-instagram"></i>
												</div>
												<div class="media-body">
													<span>Instagram</span> <a href="#"><?php echo $data[0]->instagram; ?></a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-danger-transparent text-danger">
													<i class="icon ion-logo-facebook"></i>
												</div>
												<div class="media-body">
													<span>Facebook</span> <a href="#"><?php echo $data[0]->facebook; ?></a>
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
												<?php echo $this->encryption_model->decode($data[0]->cus_mobile); ?>
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
												<?php echo $data[0]->cus_email; ?>
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
												<?php echo $data[0]->cus_address; ?>
											</div>
										</div>
									</div>
								</div><!-- main-profile-contact-list -->
							</div>
						</div>
					</div>
					<!-- /Col -->

					<!-- Col -->
					<div class="col-lg-8">
						<div class="card">
						    
						    <form class="form-horizontal" action="<?php echo site_url('master/update-profile'); ?>" method="post" enctype="multipart/form-data">
    							<div class="card-body">
    								<div class="mb-4 main-content-label">Personal Information</div>
    									
    									<div class="mb-4 main-content-label">Name</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">Outlate Name</label>
    											</div>
    											<div class="col-md-9">
    												<input type="text" class="form-control" name="name"  placeholder="Name" value="<?php echo $data[0]->cus_outlate; ?>" required>
    											</div>
    										</div>
    									</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">User Name</label>
    											</div>
    											<div class="col-md-9">
    												<input type="text" class="form-control" name="name"  placeholder="First Name" value="<?php echo $data[0]->cus_name; ?>" required>
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
    												<input type="text" class="form-control" name="email"  placeholder="Email" value="<?php echo $data[0]->cus_email; ?>" required>
    											</div>
    										</div>
    									</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">Phone</label>
    											</div>
    											<div class="col-md-9">
    												<input type="text" class="form-control" name="mobile"  placeholder="phone number" value="<?php echo $this->encryption_model->decode($data[0]->cus_mobile); ?>" required>
    											</div>
    										</div>
    									</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">Address</label>
    											</div>
    											<div class="col-md-9">
    												<textarea class="form-control" name="address" rows="2"  placeholder="Address" required><?php echo $data[0]->cus_address; ?></textarea>
    											</div>
    										</div>
    									</div>
    									<div class="mb-4 main-content-label">Social Info</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">Twitter</label>
    											</div>
    											<div class="col-md-9">
    												<input type="text" class="form-control" name="twitter" placeholder="twitter" value="<?php echo $data[0]->twitter; ?>" required>
    											</div>
    										</div>
    									</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">Facebook</label>
    											</div>
    											<div class="col-md-9">
    												<input type="text" class="form-control" name="facebook"  placeholder="facebook" value="<?php echo $data[0]->facebook; ?>" required>
    											</div>
    										</div>
    									</div>
    									<div class="form-group ">
    										<div class="row">
    											<div class="col-md-3">
    												<label class="form-label">Instagram</label>
    											</div>
    											<div class="col-md-9">
    												<input type="text" class="form-control" name="instagram"  placeholder="Instagram" value="<?php echo $data[0]->instagram; ?>" required>
    											</div>
    										</div>
    									</div>
    									
    									<div class="form-group ">
    										<div class="row">
    									        <div class="col-md-6 col-lg-6 col-xl-6">
                        						    <div class="form-group">
                        							    <label class="main-content-label tx-11 tx-medium tx-gray-600">LOGO</label> 
                        							    <input type="file" name="image" class="dropify" data-height="100" />
                        							</div>
                        						</div>
                        				    </div>
    									</div>
    								
    							</div>
    							<div class="card-footer">
    								<button type="submit" class="btn btn-primary waves-effect waves-light">Update Profile</button>
    							</div>	
    							
    						</form>
						</div>
					</div>
					<!-- /Col -->
				</div>

			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>

</html>