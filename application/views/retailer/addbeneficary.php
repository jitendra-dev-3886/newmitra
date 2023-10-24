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
								<li class="breadcrumb-item active" aria-current="page">Add Beneficery</li>
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
									Add Beneficary<br>
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
                        			    <form action="<?php echo base_url();?>retailer/add_beneficary_success" method="post">
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Name&nbsp;<span style="color:red">*</span></label>
                                						<input class="form-control" required="" placeholder="Enter Beneficary Name*" onkeypress="return/[a-z ]/i.test(event.key)" type="text" name="name">	
                                					</div>
                            					</div>
                        					    <div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group">
                                    					<label class="main-content-label tx-11 tx-medium tx-gray-600">Bank&nbsp;<span style="color:red">*</span></label>
                                    					<select class="form-control" name="bank_code">
                                    					    <option>Select Bank</option>
                                    					    <?php if(is_array($bank)){ foreach($bank as $b){ ?>
                                    					    <option value="<?php echo $b->bank_name;?>"><?php echo $b->bank_name;?></option>
                                    					    <?php }}else {}?>
                                    					</select>
                            					    </div>
                        					    </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group">
                                    					<label class="main-content-label tx-11 tx-medium tx-gray-600">IFSC Code&nbsp;<span style="color:red">*</span></label>
                                    					<input class="form-control"  placeholder="IFSC Code" type="text" onkeypress="return/[a-z0-9]/i.test(event.key)" name="ifsc_code" required>
                            					    </div>
                        					    </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Bank Account&nbsp;<span style="color:red">*</span></label>
                                						<input class="form-control" required="" placeholder="Bank Account" type="text" onkeypress="return/[0-9]/i.test(event.key)" name="bank_acct">	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group">
                                    					<label class="main-content-label tx-11 tx-medium tx-gray-600">Mobile Number&nbsp;<span style="color:red">*</span></label>
                                    					<input class="form-control"  placeholder="Mobile Number" type="text" onkeypress="return/[0-9]/i.test(event.key)" value="<?php echo $this->session->userdata('senderMobile'); ?>" maxlength="10" name="mob_no" readonly required>
                            					    </div>
                            					</div>  
                        					</div>
                        					<!--
                        					<hr></hr>
                        					<b>Fill Below Details if you want to verify Account Number.</b>
                        					<div class="row" class="test">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group" id="nameDiv">
                                    					<label class="main-content-label tx-11 tx-medium tx-gray-600">Adhar Number&nbsp;<span style="color:red">*</span></label>
                                    					<input class="form-control"  placeholder="IFSC Code" type="text" onkeypress="return/[a-z0-9]/i.test(event.key)" name="ifsc_code" required>
                            					    </div>
                        					    </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Pan Number&nbsp;<span style="color:red">*</span></label>
                                						<input class="form-control" required="" placeholder="Bank Account" type="text" onkeypress="return/[0-9]/i.test(event.key)" name="bank_acct">	
                                					</div>
                            					</div>
                        					</div>-->
                        					
                        					<!--
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group"><button type="submit" class="btn btn-main-primary btn-block">Proceed</button></div>
                            					</div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group"><button  class="btn btn-main-primary btn-block" onclick="verify()">Verify Account</button></div>
                            					</div>
                        					</div>-->
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
	    <script>
	    
	        $(document).ready(function(){
	            
	        $("#nameDiv").hide();
             }); 
             
	    </script>
	    
	    
	   <script type="text/javascript">
	   
	   function verify(){
	       
      		var adhar = 'test';//$('#selplanid').val();
      		var pan = 'welcome';//$('#seluserid').val();
      		var acct_no =
      		//alert(keyword);alert(userid);
              $.ajax({
      			url: "<?php echo base_url(); ?>Retailer/verify_bank",
      			async: false,
      			type: "POST", 
      			data: {planid:keyword,userid:userid},
      			dataType: "html",
      			success: function(data) {
                    alert(data);
      		}
      		})
    	}		
      	</script>
      	
      	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>

</html>