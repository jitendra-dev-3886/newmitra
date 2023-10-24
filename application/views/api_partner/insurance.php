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
									Insurance <br>
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
                        					<form action="<?php echo base_url();?>/Retailer/recharge_ot" method="post">
                        						<div class="row">	
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Insurance Number</label> 
                        									<input class="form-control" id="prepaid_mobile" required="" name="number" placeholder="Insurance Number" type="text">
                                    						<input class="form-control" required="" type="hidden" name="type" value="insurance">
                                    						<input class="form-control" required="" type="hidden" name="redirect" value="insurance">
                        								</div>
                        							</div>
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                        								<div class="form-group">
                        									<label class="main-content-label tx-11 tx-medium tx-gray-600">Select Operator</label>
                        									<select class="form-control select2-no-search" name="operator" required="">
                        										<option label="Choose one"></option>
                        										<?php if($operator){foreach($operator as $op){ if($op->opsertype=='INSURANCE'){?>
                        											<option value="<?php echo $op->opcodenew;?>"><?php echo $op->operatorname;?></option>
                        										<?php }}}?>
                        									</select>
                        						        </div>
                        						      </div>
                        						  </div>
                        						  <div class="form-group">
                        							<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Recharge Amount</label>
                        							<div class="pos-relative">
                        								<input class="form-control" required="" type="text" name="amount" placeholder="Amount">
                        							</div>
                        						 </div>
                        						 <button type="submit" class="btn btn-main-primary btn-block">Proceed To Recharge</button>
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