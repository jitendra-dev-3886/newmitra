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
						<h4 class="content-title mb-2">API Commission Management</h4>
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
						<div class="card">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo8">Add New Package</button></div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">API Package Name</th>
												<th class="wd-20p border-bottom-0">API Pakage Created Date</th>
												<th class="wd-20p border-bottom-0">View</th>
												<th class="wd-10p border-bottom-0">Delete</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>1</td>
												<td class=""><b>Test Package</b></td>
												<td><b>16-12-2020</b></td>
												<td class="text-success"><b><i class="typcn typcn-eye-outline tx-24 lh--9 op-6"></i></b></td>
												<td class="text-primary"><b><i class="typcn typcn-delete-outline tx-24 lh--9 op-6"></i></b></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--/div-->
					
					<!-- Modal effects -->
            		<div class="modal" id="modaldemo8">
            			<div class="modal-dialog modal-dialog-centered" role="document">
            				<div class="modal-content modal-content-demo">
            					<div class="modal-header">
            						<h6 class="modal-title">Add New API Package</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            					</div>
            					<div class="modal-body">
            						<form action="" data-parsley-validate="">
									<div class="row row-sm">
									    <div class="col-12">
									        <div class="form-group">
												<label class="form-label">API Source: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control">
    												    <option>Default Success</option>
    												</select>
    											</div>
											</div>
									    </div>
									    <div class="col-12">
									        <div class="form-group">
												<label class="form-label">Package Name: <span class="tx-danger"></span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
            										<input class="form-control" name="" placeholder="Package Name" type="text">
    											</div>
											</div>
									    </div>
										
									</div>
								</form>
            					</div>
            					<div class="modal-footer">
            						<button class="btn ripple btn-primary" type="button">Add</button>
            						<button class="btn ripple btn-danger" data-dismiss="modal" type="button">Discard</button>
            					</div>
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