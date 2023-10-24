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
								<li class="breadcrumb-item active" aria-current="page">Pan Card</li>
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
								Pan Card<br>
								</div>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row"><br></div>
								<div class="row">
									                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										    <div class="row">
                        										        <div class="col-md-6 col-lg-6 col-xl-6">
                        										            <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										                <div class="row">	
                                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                                        											<div class="form-group">
                                        											    <?php foreach($cusdata as $cdt){ if($cdt->pan_register=='No'){ $username='';}else{$username='edigital'.$this->session->userdata('cus_id');}}?>
                                        												<label class="main-content-label tx-11 tx-medium tx-gray-600">Username</label> 
                                        												<input class="form-control" required="" placeholder="Username" value="<?php echo $username;?>" readonly type="text">
                                        											</div>
                                        											</div>
                                        											<div class="col-md-6 col-lg-6 col-xl-6">
                                        											<div class="form-group">
                                        												<label class="main-content-label tx-11 tx-medium tx-gray-600">Password</label>
                                        												<input class="form-control" required="" placeholder="Password" value="<?php echo $username;?>" readonly type="text">
                                        											</div>
                                        											</div>
                                        										</div>
                                        										<a href="https://www.psaonline.utiitsl.com/psaonline/" target="_blank" class="btn btn-main-primary btn-block">Login</a>
                        										             </div>	
                        										             <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										                <div class="main-content-label mg-b-5">Buy Coupon<br></div>
                        										                <div class="row">	
                                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                                        											<div class="form-group">
                                        												<label class="main-content-label tx-11 tx-medium tx-gray-600">P-Coupon : 107</label> 
                                        											</div>
                                        											</div>
                                        											<div class="col-md-6 col-lg-6 col-xl-6">
                                        											    <div class="form-group">
                                            												<label class="main-content-label tx-11 tx-medium tx-gray-600">E-Coupon : 75</label> 
                                            											</div>
                                            										</div>
                                            									</div> 
                        										                <form action="<?php echo site_url('Retailer/buycoupon'); ?>" method="post">
                        										                <div class="row">	
                                        										    <div class="col-md-6 col-lg-6 col-xl-6">
                                        											<div class="form-group">
                                        												<label class="main-content-label tx-11 tx-medium tx-gray-600">No Of Coupon</label> 
                                        												<input class="form-control" required="" placeholder="Coupons" type="text" name="noofcoupon" id="couponid" value="0">
                                        												<input class="form-control" required="" value="<?php foreach($coupon as $co){ if($co->coupon_type=='P-Coupon'){echo $co->coupon_price;}}?>" placeholder="Coupons" type="hidden" id="P-Coupon">
                                        												<input class="form-control" required="" value="<?php foreach($coupon as $co){ if($co->coupon_type=='E-Coupon'){echo $co->coupon_price;}}?>" placeholder="Coupons" type="hidden" id="E-Coupon">
                                        											</div>
                                        											</div>
                                        											<div class="col-md-6 col-lg-6 col-xl-6">
                                        											    <div class="form-group">
                                            												<label class="main-content-label tx-11 tx-medium tx-gray-600">Select Service</label> 
                                            												<select class="form-control select2-no-search" name="coupon_type" id="coupon_type" onchange="getcoupon()">
                                        														<option label="Select Service"></option>
                                        														<option value="1" selected>P-Coupon</option>
                                        														<option value="2">E-Coupon</option>
                                        													</select>
                                            											</div>
                                            										</div>
                                            									</div>
                                            									<div class="row">	
                                            									<div class="col-md-12 col-lg-12 col-xl-12">
                                        											<div class="form-group">
                                        												<label class="main-content-label tx-11 tx-medium tx-gray-600">Total</label>
                                        												<input class="form-control" required="" readonly placeholder="Amount" name="amount" type="text" id="totamount">
                                        											</div>
                                        										</div>
                                        										</div>
                                        										<button class="btn btn-main-primary btn-block" type="submit" name="buy_coupon">Buy Coupon</button>
                                        										</form>
                        										             </div>	
                        										        </div>	
                        										    	 <div class="col-md-6 col-lg-6 col-xl-6">
                        										    	     <?php foreach($cusdata as $cdt){ if($cdt->pan_register=='No'){ ?>
                        										    	     <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										                <form action="<?php echo site_url('Retailer/registerVLEID'); ?>" method="post">
                        										                <div class="row">	
                        										                <?php foreach($cusdata as $cdt){ ?>
                                        										    <div class="col-md-12 col-lg-12 col-xl-12">
                                        											<div class="form-group">
                                        												<input class="form-control" required="" placeholder="Shop Name" type="text" name="shop_name" value="<?php echo $cdt->shop_name;?>">
                                        											</div>
                                        											</div>
                                        											<div class="col-md-12 col-lg-12 col-xl-12">
                                        											<div class="form-group">
                                        												<input class="form-control" required="" placeholder="City" type="text" name="cus_city" value="<?php echo $cdt->cus_city;?>">
                                        											</div>
                                        											</div>
                                        											
                                        											<div class="col-md-12 col-lg-12 col-xl-12">
                                        											    <div class="form-group">
                                            												<select class="form-control select2-no-search" name="cus_state">
                                            												    <?php 
                                            												        foreach($aeps_state as $state){ 
                                            												            if($cdt->cus_state==$state->aeps_state_id){
                                            												    ?>
                                            												       <option value="<?php echo $state->aeps_state_id;?>" selected><?php echo $state->state;?></option>
                                            												    <?php }?>
                                            												    <option value="<?php echo $state->aeps_state_id;?>"><?php echo $state->state;?></option>
                                        														<?php }?>
                                        													</select>
                                            											</div>
                                            										</div>
                                        											<div class="col-md-12 col-lg-12 col-xl-12">
                                        											<div class="form-group">
                                        												<input class="form-control" required="" placeholder="Pin Code" type="text" name="cus_pincode" value="<?php echo $cdt->cus_pincode;?>">
                                        											</div>
                                        											</div>
                                        											<div class="col-md-12 col-lg-12 col-xl-12">
                                        											<div class="form-group">
                                        												<input class="form-control" required="" placeholder="Aadhar Number" type="text" name="cus_adharno" value="<?php echo $cdt->cus_adharno;?>">
                                        											</div>
                                        											</div>
                                        											<div class="col-md-12 col-lg-12 col-xl-12">
                                        											<div class="form-group">
                                        												<input class="form-control" required="" placeholder="Pan Number" type="text" name="cus_panno" value="<?php echo $cdt->cus_panno;?>">
                                        											</div>
                                        											</div>
                                        											<?php }?>
                                            									</div>
                                        										<button class="btn btn-main-primary btn-block" type="submit" name="register">Register</button>
                                        										</form>
                        										             </div>
                        										             <?php }}?>
                        										    	    <!--<a href="<?php echo base_url();?>/Retailer/registerVLEID" class="btn btn-main-primary btn-block">Register</a>--><br>
                        										    	    <div class="row">	
                                        										<div class="col-md-6 col-lg-6 col-xl-6">
                        										    	            <a href="<?php echo base_url();?>/Retailer/vlestatus" class="btn btn-main-primary btn-block">User Status</a>
                        										    	        </div>	
                        										    	        <div class="col-md-6 col-lg-6 col-xl-6">
                        										    	            <a href="<?php echo base_url();?>/Retailer/passwordresetVLEID" class="btn btn-main-primary btn-block">Reset Password</a><br>
                        										    	        </div>
                        										    	     </div>
                        										    	    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										    	        <div class="main-content-label mg-b-5">Coupon Hostory<br></div>
                        										                    <div class="table-responsive">
                                                    									<table class="table key-buttons text-md-nowrap">
                                                    										<thead>
                                                    											<tr>
                                                    												<th class="border-bottom-0">Date</th>
                                                    												<th class="border-bottom-0">Quntity</th>
                                                    												<th class="border-bottom-0">Action</th>
                                                    											</tr>
                                                    										</thead>
                                                    										<tbody>
                                                    										   <?php foreach($couponhistory as $cob){?>
                                                    										    <tr>
                                                    												<td><?php echo $cob->date?></td>
                                                    												<td><?php echo $cob->qty?></td>
                                                    												<td> <a href="<?php echo base_url();?>/Retailer/coupon_status/<?php echo $cob->order_id;?>" class="btn btn-main-primary btn-block"><i class="fa fa-eye"></i></a></td>
                                                    											</tr>
                                                    											<?php }?>
                                                    										</tbody>
                                                    									</table>
                                                    								</div>
                        										             </div>	
                        										    	    
                        										    	  </div>	
                        										    </div>
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
<script>
$(document).ready(function(){
  $("#couponid").change(function(){
      var coupan=$('#couponid').val();
      var coupon_type=$('#coupon_type').val();
      if(coupon_type=='1'){
          var price=$('#P-Coupon').val();
      }
      else{
          var price=$('#E-Coupon').val();
      }
      var amout=Number(price)*Number(coupan);
      $('#totamount').val(amout);
  });
});

function getcoupon(){
     var coupan=$('#couponid').val();
      var coupon_type=$('#coupon_type').val();
      if(coupon_type=='1'){
          var price=$('#P-Coupon').val();
      }
      else{
          var price=$('#E-Coupon').val();
      }
      var amout=Number(price)*Number(coupan);
      $('#totamount').val(amout);
}
</script>
</html>