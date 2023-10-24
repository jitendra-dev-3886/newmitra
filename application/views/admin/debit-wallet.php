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
									<span class="d-block">
										<span class="label ">TOTAL MEMBER BALANCE</span>
									</span>
									<span class="value">
										$53,000
									</span>
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
							    <div class="main-content-label mg-b-5">
									Wallet Pullout
								</div>
								<p class="mg-b-20">Debit Wallet of Existing Member.</p>
								<form action="<?php echo site_url('admin/wallet-pullout-success') ?>" method="post"  data-parsley-validate="">
									<div class="row row-sm">
									    <div class="col-3">
									        <div class="form-group">
												<label class="form-label">Member Type: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" id="cus_type" onchange="getMembers(this)">
    												    <option>Member Type</option>
    												    <option value="master">Master</option>
    												    <option value="distributor">Distributor</option>
    												    <option value="retailer">Retailer</option>
    												    <option value="api">API Client</option>
    												</select>
    											</div>
											</div>
									    </div>
									    <div class="col-3">
									        <div class="form-group">
												<label class="form-label">Member: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-user-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" name="cus_id" id="members" onchange="getMemberBalance(this)">
    												</select>
    											</div>
											</div>
									    </div>
									    <div class="col-6">
											<label class="form-label">Remarks: <span class="tx-danger"></span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-location-outline tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
											    <textarea class="form-control" placeholder="Remarks" rows="1"></textarea>
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
    												<input class="form-control" name="pullout_bal" placeholder="Enter Amount to Debit" required="" type="number">
    											</div>
											</div>
										</div>
										
										<div class="col-12" style="margin-top:2%"><button class="btn btn-main-primary pd-x-20 mg-t-10" type="submit">Submit</button></div>
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

	<script>
	
	    var getMembers = cus_type =>{
	        var cus_type = cus_type.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url() ?>admin/getMembersByType",
	            cache: "false",
	            data: {cus_type:cus_type},
	            success: function(res){
	                $("#members").empty();
	                $("#members").append(res);
	            }
	        });
	    }
	
	    var getMemberBalance = cus_id =>{
	        var cus_id = cus_id.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>admin/getMemberBalance",
	            cache: false,
	            data: {cus_id:cus_id},
	            success: function(res){
	                $("#wallet").val(res);
	            }
	        });
	    }
	</script>
</html>