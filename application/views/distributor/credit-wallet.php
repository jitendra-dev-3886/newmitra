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
						<h4 class="content-title mb-2">Wallet Management</h4>
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
								<div class="ml-3 mt-2">
									<span class="sparkline_bar"></span>
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
							    <?php if($this->session->flashdata('msg')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('msg');?></strong> 
                                </div>
                                <?php }?>
							    <div class="main-content-label mg-b-5">
									Wallet Topup
								</div>
								<p class="mg-b-20">Credit Wallet to Existing Member.</p>
								<form action="<?php echo site_url('distributor/wallet-topup-success') ?>" method="post" data-parsley-validate="">
									<div class="row row-sm">
									    <div class="col-6">
									        <div class="form-group">
									            <label class="form-label">PIN: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="pin" placeholder="Enter PIN" type="text" id="old_pin">
    												<div class="alert" role="alert" style="display:none;" id="wrong_pin">
                                                        <strong style="color:red">Please Enter Correct Pin...!!!</strong> 
                                                    </div>
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Member: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-user-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" name="cus_id" id="members" onchange="getMemberBalance(this)">
    												    <option>Choose Member</option>
    												    <?php if(is_array($members)){foreach($members as $rec){ ?>
    												        <option value="<?php echo $rec->cus_id; ?>"><?php echo ucfirst($rec->cus_name);?></option>
    												    <?php }}?>
    												</select>
    											</div>
											</div>
									    </div>
									    
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Wallet Balance: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-briefcase tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="" id="wallet" placeholder="0" readonly required="" type="text">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Enter Amount: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-database tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="amount" placeholder="Enter Amount to Credit" required="" type="number">
    											</div>
											</div>
									    </div>
									    <div class="col-12" style="margin-top:2%">
										    <button class="btn btn-main-primary pd-x-20 mg-t-10" id="update_pin" disabled type="submit">Submit</button></div>
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
	
	   <?php $this->load->view('distributor/footer'); ?> 
	
	</body>
	<script>
	
	   var getMemberBalance = cus_id =>{
	        var cus_id = cus_id.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>distributor/getMemberBalance",
	            cache: false,
	            data: {cus_id:cus_id},
	            success: function(res){
	                $("#wallet").val(res);
	            }
	        });
	    }
	    
	     $(document).ready(function(){
          $("#old_pin").focusout(function(){
            var old_pin=$("#old_pin").val();
            $.ajax({
      		        url: "<?php echo site_url();?>distributor/check_pin",
          		    async: false,
          		    type: "POST",
          		    data: {old_pin:old_pin},
          		    dataType: "html",
          			success: function(data) {
          			    if(data==false){
                            $("#old_pin").css("background-color", "#f8d7da");
                            $("#wrong_pin").show();
                            $("#old_pin").val('');
                            $('#update_pin').prop('disabled', true);
                        }
                        else{
                            $("#old_pin").css("background-color", "#fff");
                            $("#wrong_pin").hide();
                            $('#update_pin').prop('disabled', false);
                        }
              		},
      				error: function(xhr, status, error) {
                          alert(error);
                    }
      			}) 
           });
        });
	</script>
</html>