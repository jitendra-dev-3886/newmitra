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
								<li class="breadcrumb-item active" aria-current="page">Fund Request</li>
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
									Fund Request<br>
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
                        			    <form action="<?php echo base_url();?>/Retailer/send_fund_request" method="post">
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Request To</label>
                                						<select class="form-control select2-no-search" name="request" required="">
                                								<option label="Choose one"></option>
                                								<option value="admin">Admin</option>
		                                                        <option value="distributor">Distributor</option>
                                						</select>
                                					 </div>
                            					 </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Reference No</label>
                                						<input class="form-control" required="" placeholder="Reference No" type="text" name="ref">	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Payment Amount*</label>
                                						<input class="form-control" required="" placeholder="Payment Amount*" type="number" name="amount">	
                                					</div>
                            					</div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Remarks</label>
                                						<input class="form-control" required="" placeholder="Remarks" type="text" name="remark">	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Payment Mode</label>
                                						<select class="form-control select2-no-search" name="method" required="">
                                							<option label="Choose one"></option>
                                							<option value="">Select Mode</option>
                                    						<option value="1">Cash Deposit</option>
                                    						<option value="2">Cheque Deposit</option>
                                    						<option value="3">Online Transfer (IMPS/NEFT/RTGS)</option>
                                    						<option value="4">ATM Transfer</option>
                                						</select>
                                					</div>
                            					</div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Bank</label>
                                						<select class="form-control select2-no-search" name="bank" required="">
                                							<option label="Choose one"></option>
                                							<?php if($cusbank){foreach($cusbank as $bank){?>
                                							<option value="<?php echo $bank->bank_id; ?>"><?php echo $bank->bank_name.'  -  '.$bank->account_id.'  -  '.$bank->ifsc_code; ?></option>
                                							<?php }}?>
                                						</select>	
                                					</div>
                            					</div>
                        					</div>
                        					<button type="submit" class="btn btn-main-primary btn-block">Proceed</button>
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