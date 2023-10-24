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
						<h4 class="content-title mb-2">Commission Management</h4>
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
									
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card"><!--
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add New Package</button></div>
								</div>
							</div>-->
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">Package Name</th>
												<th class="wd-20p border-bottom-0">Pakage Type</th>
												<th class="wd-20p border-bottom-0">Member Type</th>
												<th class="wd-20p border-bottom-0">Commission</th>
												<th class="wd-20p border-bottom-0">Pakage Created Date</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($package)){$i=1;foreach($package as $pack){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td class=""><b><?php echo ucfirst($pack->slab); ?></b></td>
												<td class="text-danger"><b><?php echo ucfirst($pack->type); ?></b></td>
												<td class="text-primary"><b><?php echo ucfirst($pack->member_type); ?></b></td>
												<td class="text-success"><b>₹<?php echo ucfirst($pack->amount); ?></b></td>
												<td style="width:200px"><b><?php echo ucfirst($pack->dmt_update_time); ?></b></td>
												<!--<td class="text-primary"><a href="<?php echo base_url(); ?>master/dmt_delete-package/<?php echo $pack->dmt_comm_id; ?>" onclick="return confirm('Delete Package <?php echo ucfirst($pack->package_name); ?> ? ')" ><b><i class="typcn typcn-delete-outline tx-24 lh--9 op-6"></i></b></a></td>
											--></tr>
											<?php }} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--/div-->
					
					<!-- Modal effects -->
            		<div class="modal" id="modaldemo7">
            			<div class="modal-dialog modal-dialog-centered" role="document">
            				<div class="modal-content modal-content-demo">
            					<div class="modal-header">
            						<h6 class="modal-title">Add New Package</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            					</div>
            					<form action="<?php echo site_url('master/dmt-package-add'); ?>" data-parsley-validate="" method="post">
                					<div class="modal-body">
        								<div class="row row-sm">
        									    <div class="col-12">
        									        <div class="form-group">
        												<label class="form-label">Member Type: <span class="tx-danger">*</span></label>
        												<div class="input-group">
            												<div class="input-group-prepend">
                    											<div class="input-group-text">
                    												<i class="typcn typcn-user tx-24 lh--9 op-6"></i>
                    											</div>
                    										</div>
            												<select class="form-control" required name="member_type">
            												    <option value="retailer">Retailer</option>
            												    <option value="distributor">Distributor</option>
            												    <option value="master">Master</option>
            												    <option value="api">API Client</option>
            												</select>
            											</div>
        											</div>
        									    </div>
        									    <!--<div class="col-12">
        									        <div class="form-group">
        												<label class="form-label">Commission Type: <span class="tx-danger">*</span></label>
        												<div class="input-group">
            												<div class="input-group-prepend">
                    											<div class="input-group-text">
                    												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
                    											</div>
                    										</div>
            												<select class="form-control" required name="package_type">
            												    <option value="flat">Flat</option>
            												</select>
            											</div>
        											</div>
        									    </div>-->
        									    <div class="col-12">
        									        <div class="form-group">
        												<label class="form-label">Package Amount Range: <span class="tx-danger"></span></label>
        												<div class="input-group">
            												<div class="input-group-prepend">
                    											<div class="input-group-text">
                    												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                    											</div>
                    										</div>
                    										<input class="form-control" placeholder="Package Amount Range" type="text" required name="slab">
            											</div>
        											</div>
        									    </div>
        									    <div class="col-12">
        									        <div class="form-group">
        												<label class="form-label">Amount: <span class="tx-danger"></span></label>
        												<div class="input-group">
            												<div class="input-group-prepend">
                    											<div class="input-group-text">
                    												<i class="typcn typcn-bookmark tx-24 lh--9 op-6"></i>
                    											</div>
                    										</div>
                    										<input class="form-control" placeholder="Enter Commission Amount in Flat" type="text" required name="commission">
            											</div>
        											</div>
        									    </div>
        									    <!--<div class="col-12">
        									        <div class="form-group">
        												<label class="form-label">Maximum Amount: <span class="tx-danger"></span></label>
        												<div class="input-group">
            												<div class="input-group-prepend">
                    											<div class="input-group-text">
                    												<i class="typcn typcn-link tx-24 lh--9 op-6"></i>
                    											</div>
                    										</div>
                    										<input class="form-control" placeholder="Enter Maximum Commission Amount " type="text" required name="max_commission">
            											</div>
        											</div>
        									    </div>-->
        										
        									</div>
                					</div>
                					<div class="modal-footer">
                						<button type="submit" class="btn ripple btn-primary">Add</button>
                						<button class="btn ripple btn-danger" data-dismiss="modal" type="button">Discard</button>
                					</div>
    							</form>
            				</div>
            			</div>
            		</div>
            		<!-- End Modal effects-->


				</div>
				<!-- /row -->

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>

</html>