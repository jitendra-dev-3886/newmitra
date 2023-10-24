<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('api_partner/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('api_partner/loader'); ?>

		<?php $this->load->view('api_partner/header'); ?>

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
								<li class="breadcrumb-item active" aria-current="page">Redeem</li>
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
									REDEEM REQUEST
								    <a class="btn btn-main-primary" style="color:white;float:right;">REDEEM HISTORY</a><br>
								</div>
								<?php // print_r($bankDetails); ?>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row" style="margin-top:20px"><br></div>
								<div class="row">
		                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
									<div class="card card-body pd-20 pd-md-40 border shadow-none">
										<!--<h5 class="card-title mg-b-20">Your Postpaid Details</h5>-->
										
										<form action="<?php echo site_url('api_partner/submit-payout')?>" method="post">
										    
										    <div class="row">	
        									    <div class="col-md-6 col-lg-6 col-xl-6">
        										<div class="form-group">
        											<label class="main-content-label tx-11 tx-medium tx-gray-600">Bank Name</label> 
        											<input class="form-control" name="bank_name" value="<?php echo $bankDetails[0]->bankName; ?>" placeholder="Enter Bank Name" type="text" readonly>
        										</div>
        										</div>
        										<div class="col-md-6 col-lg-6 col-xl-6">
        										<div class="form-group">
        											<label class="main-content-label tx-11 tx-medium tx-gray-600"> Account Holder's Name</label>
        											<input class="form-control" name="account_holder_name" value="<?php echo $bankDetails[0]->aeps_bankAccountName; ?>" required="" placeholder="Enter Account Holder's Name" type="text" readonly>
        										</div>
        										</div>
        									</div>
        									<div class="row">	
        									    <div class="col-md-6 col-lg-6 col-xl-6">
        										<div class="form-group">
        											<label class="main-content-label tx-11 tx-medium tx-gray-600">Bank Account Number</label> 
        											<input class="form-control" name="account_number" value="<?php echo $bankDetails[0]->aeps_AccountNumber; ?>" required="" placeholder="Enter Bank Account" type="text" onkeypress="return/[0-9]/i.test(event.key)" readonly>
        										</div>
        										</div>
        										<div class="col-md-6 col-lg-6 col-xl-6">
        										<div class="form-group">
        											<label class="main-content-label tx-11 tx-medium tx-gray-600">IFSC Code</label>
        											<input class="form-control" name="ifsc_code" value="<?php echo $bankDetails[0]->aeps_bankIfscCode; ?>" required="" placeholder="Enter IFSC Code" type="text" onkeypress="return/[a-z0-9]/i.test(event.key)" readonly>
        										</div>
        										</div>
        									</div>
        									
        									
        									<div class="row">
        										<div class="col-md-6 col-lg-6 col-xl-6">
        										<div class="form-group">
        											<label class="main-content-label tx-11 tx-medium tx-gray-600">Select </label>
        											<select class="form-control" name="sel" id="sel" onchange="chg()">
            											<option value="bank">Move To Bank</option>
            											<option value="wallet">Move To Wallet</option>
        											</select>
        											<script>
        											    function chg(){
        											       var vl = $('#sel').val();
        											       var chrg = $('#hid_charge').val();
        											       if(vl == 'wallet')
        											       $('#charge').val('0');
        											       if(vl == 'bank')
        											       $('#charge').val(chrg);
        											    }
        											</script>
        										</div>
        										</div>	
        									    <div class="col-md-6 col-lg-6 col-xl-6">
        										<div class="form-group">
        										    <label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Amount</label>
            										<div class="pos-relative">
            											<input class="form-control pd-r-80" name="amount" required="" onchange="getCharge(this)" type="text" onkeypress="return/[0-9]/i.test(event.key)" placeholder="Amount">
            										</div>
        											
        										</div>
        										</div>
        									</div>
        									
        									<div class="form-group">
        										<label class="main-content-label tx-11 tx-medium tx-gray-600">Payout Charge</label>
    											<input class="form-control pd-r-80" id="charge" name="charge"  onkeypress="return/[0-9]/i.test(event.key)" required="" type="text" readonly>
    											<input type="hidden" id="hid_charge" >
        									</div>
        									<button class="btn btn-main-primary btn-block">Proceed</button>
        									</div>
        									
										</form>
									
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
	
	   <?php $this->load->view('api_partner/footer'); ?> 
	   
	   <script>
	       var getCharge = amount =>{
	           
	           
	           var vl = $('#sel').val();
		       var chrg = $('#hid_charge').val();
		       if(vl == 'wallet'){
        	    $("#hid_charge").val(0);     
		        $('#charge').val('0');
		       } 
		       if(vl == 'bank'){
    	           amount = amount.value;
    	           $.ajax({
    	               url: "https://edigitalvillage.net/api_partner/getPayoutCharge",
    	               cache: "false",
    	               type: "post",
    	               data: {amount:amount},
    	               success: function(res){
    	                   if(res){
        	                   $("#charge").val(res);
        	                   $("#hid_charge").val(res);
    	                   }else{
    	                       $("#charge").val(0);
        	                   $("#hid_charge").val(0);
    	                   }
    	               }
    	           });
		       }    
	       }
	   </script>
	
	</body>

</html>