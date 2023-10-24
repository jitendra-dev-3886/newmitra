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
						<h4 class="content-title mb-2">Bank Details</h4>
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
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add Bank Details</button></div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-15p border-bottom-0">Holder Name</th>
												<th class="wd-15p border-bottom-0">Bank Name</th>
												<th class="wd-20p border-bottom-0">Branch Name</th>
												<th class="wd-15p border-bottom-0">Account Number</th>
												<th class="wd-20p border-bottom-0">IFSC Code</th>
												<th class="wd-20p border-bottom-0">Status</th>
												<th class="wd-10p border-bottom-0">Delete</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($r)){$sl = 1;foreach($r as $v) { ?>     
											<tr>
                                                <td><?php echo ($v->type != '' ? $v->type[0]->cus_type : 'Admin'); ?></td>
                                                <td><?php echo $v->bank_account; ?></td>
                                                <td><?php echo $v->bank_name; ?></td>
                                                <td><?php echo $v->branch_name; ?></td>
                                                <td><?php echo $v->account_id; ?></td>
                                                <td><?php echo $v->ifsc_code; ?></td>
                                                <td><b>
                                                <?php if($v->account_status== 1){ ?>
                                                <label class="text-success">ACTIVE</label>
                                                <?php }else{ ?>
                                                <label class="text-danger">INACTIVE</label>
                                                <?php } ?>
                                                </b></td>    
                                                <td align="center">
                                                    <a href="<?php echo base_url();?>admin/update-bank-details/<?php echo $v->bank_id ?>" class="text-info" title="edit"><i class="fa fa-edit"></i></a>
                                                    &nbsp;&nbsp;
                                                    <a href="<?php echo base_url();?>admin/delete-bank-details/<?php echo $v->bank_id ?>" onclick="return confirm('Are you sure?')" class="text-danger" title="edit"><i class="fa fa-trash"></i></a>                                             
                                                </td>                                                                   
                                            </tr>
                                      <?php $sl++;} } ?>
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
            					<form action="<?php echo site_url('admin/add-bank-details'); ?>" data-parsley-validate="" method="post">
                					<div class="modal-body">
        								<div class="row row-sm">
        								    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Holder Name: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-user tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" placeholder="Enter Holder Name" type="text" required name="holder">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Bank Name: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" placeholder="Enter Bank Name" type="text" required name="bank">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Branch: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-spiral tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" placeholder="Enter Branch Name" type="text" required name="branch">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Account Number: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" placeholder="Enter Account Number" type="text" required name="account">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">IFSC Code: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-link tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<input class="form-control" placeholder="Enter IFSC Code" type="text" required name="ifsc">
        											</div>
    											</div>
    									    </div>
    									    
    										
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
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>

</html>