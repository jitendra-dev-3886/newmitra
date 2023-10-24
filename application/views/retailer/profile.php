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
						<h4 class="content-title mb-2">Hi, welcome back <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Profile</li>
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
									Edit Profile <br>
								</div>
								<?php if($this->session->flashdata('message')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('message');?></strong> 
                                </div>
                                <?php }?>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row"><br></div>
								<div class="row">
									<div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        			    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<form action="<?php echo base_url();?>Retailer/profile" method="post" enctype="multipart/form-data">
                        					    <?php if($profile){ foreach($profile as $pro){?>
                        						<div class="row">	
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">Outlet Name</label> 
                            							    <input class="form-control" required="" name="cus_outlate" placeholder="Outlet Name" value="<?php echo $pro->cus_outlate;?>" type="text" readonly>
                                    						<input class="form-control" required="" type="hidden" name="cus_id" value="<?php echo $pro->cus_id;?>">
                            						    </div>
                        							</div>
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Name</label>
                        									<input class="form-control" required="" name="cus_name" placeholder="Name" value="<?php echo $pro->cus_name;?>" type="text" readonly>
                        								</div>
                        							</div>
                        						</div>
                        						<div class="row">	
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">Pancard Number</label> 
                            							    <input class="form-control" required="" name="cus_panno" placeholder="Pancard Number" value="<?php echo $pro->cus_panno;?>" type="text" readonly>
                                    					</div>
                        							</div>
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Mobile No</label>
                        									<input class="form-control" required="" readonly name="cus_mobile" placeholder="Mobile No" value="<?php echo $this->encryption_model->decode($pro->cus_mobile);?>" type="text">
                        								</div>
                        							</div>
                        						</div>
                        						<div class="row">	
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">Resident Address</label> 
                            							    <input class="form-control" required="" name="cus_address" placeholder="Resident Address" value="<?php echo $pro->cus_address;?>" type="text" readonly>
                                    					</div>
                        							</div>
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Shop Address</label>
                        									<input class="form-control" required="" name="cus_shop_address" placeholder="Shop Address" value="<?php echo $pro->cus_shop_address;?>" type="text" readonly>
                        								</div>
                        							</div>
                        						</div>
                        						<div class="row">	<!--
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">State / Province</label> 
                            							   <select class="form-control select2-no-search" name="state" readonly>
                        										<option label="Choose one"></option>
                        										<?php if($state){foreach($state as $st){?>
                        											<option value="<?php echo $st->state_id; ?>" <?php if($st->state_id == $pro->cus_state) echo 'selected'; ?>><?php echo $st->state_name; ?></option>
                        										<?php }}?>
                        									</select>
                                    					</div>
                        							</div>-->
<!--                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Select City</label>
                        									<select class="form-control select2-no-search" name="city" readonly >
                        										<option label="Choose one"></option>
                        										<?php if($city){foreach($city as $ct){?>
                        											<option value="<?php echo $ct->city_id; ?>" <?php if($ct->city_id == $pro->cus_city) echo 'selected'; ?>><?php echo $ct->city_name; ?></option>
                        										<?php }}?>
                        									</select>
                        								</div>
                        							</div>-->
                        						</div>
                        						<div class="row">	
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">Email</label> 
                            							    <input class="form-control" required="" name="cus_email" placeholder="Email" value="<?php echo $pro->cus_email;?>" type="text" readonly>
                                    					</div>
                        							</div>
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Pin Code</label>
                        									<input class="form-control" required="" name="cus_pincode" placeholder="Pin Code" value="<?php echo $pro->cus_pincode;?>" type="text" readonly>
                        								</div>
                        							</div>
                        						</div>
                        					
<!--                        							<div class="row">	
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">Image</label> 
                            							    <input type="file" name="image" class="dropify" data-height="100" />
                            							</div>
                        							</div>
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        							    <?php if($referal){ foreach($referal as $ref){?>
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Distributor</label> <br>
                            							    <b>ID: <?php echo $ref->cus_id;?>  Name: <?php echo $ref->cus_name;?> </b>
                        								</div>
                        								<?php }}?>
                        							</div>
                        						</div>-->
                        						
                        						<button name="update_profile" class="btn btn-main-primary btn-block">Contact Admin To Update Details</button>
                        						<?php }}?>
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