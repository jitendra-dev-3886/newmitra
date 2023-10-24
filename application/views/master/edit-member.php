<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('master/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('master/loader'); ?>

		<?php $this->load->view('master/header'); ?>
		
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
									Update Member
								</div>
								<p class="mg-b-20">Update Existing Retailer/Distributor/Master/API Client Details.</p>
								<form action="<?php echo site_url('master/edit-member-succ'); ?>" method="post">
									<div class="row row-sm">
									    
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
    												    <?php if(is_array($package)){foreach($package as $pack){ ?>
    												        <option value="<?php echo $pack->package_id ?>"><?php echo ucfirst($pack->package_name) ?></option>
    												    <?php }} ?>
    												</select>
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">DMT Package:</label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-link tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" name="dmt_package_id" id="dmt_package">
    												    <?php if(is_array($dmt_package)){foreach($dmt_package as $dmt_pack){ ?>
    												        <option value="<?php echo $dmt_pack->dmt_comm_id ?>"><?php echo ucfirst($dmt_pack->slab) ?> (<?php echo ucfirst($dmt_pack->member_type); ?>)</option>
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
    												<input class="form-control" name="name" placeholder="Enter Full Name" value="<?php echo $r['info']->cus_name; ?>" required="" type="text">
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
    												<input class="form-control" name="mobile" placeholder="Enter Contact Number" value="<?php echo $this->encryption_model->decode($r['info']->cus_mobile); ?>" required="" type="number">
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
											    <input class="form-control" name="email" placeholder="Enter email" required="" value="<?php echo $r['info']->cus_email; ?>" type="email">
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
											    <textarea class="form-control" name="outlet" placeholder="Enter Outlet Name" rows="2" required><?php echo $r['info']->cus_outlate; ?></textarea>
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
											    <textarea class="form-control" name="address" placeholder="Enter Address" rows="2" required><?php echo $r['info']->cus_address; ?></textarea>
											</div>
										</div>
										
										<input type="hidden" value="<?php echo $r['info']->cus_id; ?>" name="cus_id">
										
										<div class="col-12" style="margin-top:2%"><button class="btn btn-main-primary pd-x-20 mg-t-10" type="submit">Update Member</button></div>
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
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>

</html>