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
						<h4 class="content-title mb-2">Member Management</h4>
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
									Add New Member
								</div>
								<p class="mg-b-20">Create new Retailer/Distributor/Master/API Clients.</p>
								<form action="<?php echo site_url('admin/add-member-succ'); ?>" method="post">
									<div class="row row-sm">
									    <div class="col-5">
									        <div class="form-group">
												<label class="form-label">Member Type: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" name="cus_type" onchange="getMembers(this)">
    												    <option value="master">Master</option>
    												    <option value="distributor">Distributor</option>
    												    <option value="retailer">Retailer</option>
    												    <option value="api">API Client</option>
    												</select>
    											</div>
											</div>
									    </div>
									    
									    <span class="col-5" id="members"></span>
									    <div class="col-2">
									        <div class="form-group">
												<label class="form-label">Credits: <span class="tx-danger">*</span></label>
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
												<label class="form-label">Package:</label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" name="package_id" id="package">
    												    <option>Select Package</option>
    												    <?php if(is_array($package)){foreach($package as $pack){ ?>
    												        <option value="<?php echo $pack->scheme_id ?>"><?php echo ucfirst($pack->scheme_name) ?></option>
    												    <?php }} ?>
    												</select>
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Name: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-user-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="name" placeholder="Enter Full Name" required="" type="text">
    											</div>
											</div>
									    </div>
										<div class="col-6">
											<div class="form-group">
												<label class="form-label">Mobile: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-phone tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="mobile" placeholder="Enter Contact Number" required="" type="number">
    											</div>
											</div>
										</div>
										<div class="col-6">
											<label class="form-label">Email: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-mail tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
											    <input class="form-control" name="email" placeholder="Enter email" required="" type="email">
											</div>
										</div>
										<div class="col-6">
											<label class="form-label">Outlet Name: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-cog tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
											    <textarea class="form-control" name="outlet" placeholder="Enter Outlet Name" rows="2" required></textarea>
											</div>
										</div>
										<div class="col-6">
											<label class="form-label">Address: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-location-outline tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
											    <textarea class="form-control" name="address" placeholder="Enter Address" rows="2" required></textarea>
											</div>
										</div>
										<div class="col-6">
											<label class="form-label">Referral Id: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-location-outline tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
											    <input class="form-control" name="referral_id" placeholder="Enter Referral Id" required="" type="text">
											</div>
										</div>
										
										<div class="col-12" style="margin-top:2%"><button class="btn btn-main-primary pd-x-20 mg-t-10" type="submit">Add Member</button></div>
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
	            url: "<?php echo base_url() ?>admin/getUplineMembers",
	            cache: "false",
	            data: {cus_type:cus_type},
	            success: function(res){
	                $("#members").empty();
	                $("#members").append(res);
	            }
	        });
	    }
	
	    var getMemberPackage = cus_type =>{
	        var cus_type = cus_type.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>admin/getMemberPackage",
	            cache: false,
	            data: {cus_type:cus_type},
	            success: function(res){
	                $("#package").empty();
	                $("#package").append(res);
	            }
	        });
	    }
	    
	    var getMemberBalance = cus_id =>{
	        var cus_id = cus_id.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>admin/getuser_creadit",
	            cache: false,
	            data: {cus_id:cus_id},
	            success: function(res){
	                $("#wallet").val(res);
	            }
	        });
	    }
	</script>

</html>