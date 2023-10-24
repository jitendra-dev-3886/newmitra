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
						<h4 class="content-title mb-2">Banner Management</h4>
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
					<div class="col-lg-6 col-md-6">
						<div class="card">
							<div class="card-body">
							    <div class="main-content-label mg-b-5">
									Update Banner
								</div>
								<form action="<?php echo site_url('admin/banner_update'); ?>" method="post" enctype="multipart/form-data">
								    <div>
								        <label class="form-label">Current Banner Image:</label>
								        <img src="<?php echo $banner[0]->image; ?>" style="width:350px;height:250px">
								    </div><hr/><br>
							        <div class="form-group">
										<label class="form-label">New Banner Image: <span class="tx-danger">*</span></label>
										<div class="input-group">
											<div class="input-group-prepend">
    											<div class="input-group-text">
    												<i class="typcn typcn-link tx-24 lh--9 op-6"></i>
    											</div>
    										</div>
											<input class="form-control" name="userfile" required="" type="file">
										</div>
									</div>
									<input type="hidden" name="id" value="<?php echo $banner[0]->bid; ?>">
									
									<div style="margin-top:2%"><button class="btn btn-main-primary btn-block pd-x-20 mg-t-10" type="submit">Update</button></div>
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
	</script>

</html>