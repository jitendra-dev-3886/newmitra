<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('admin/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('admin/loader'); ?>

		<?php $this->load->view('admin/header'); ?>
		
		<!-- main-content opened -->
		
		<div class="main-content horizontal-content">
		    <div class="container">
		        <div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Amount Wise API Switching</h4>
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
			</div>

        <div class="page-content">
            <div class="container-fluid">

               <!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
						    
						    <div class="card-header">
            				    <!--<button class="btn btn-info" data-bs-toggle="modal" data-bs-target="bs-example-modal-center"><i class="fa fa-plus"></i> ADD NEW DENOMINATION </button>-->
            				    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>ADD NEW DENOMINATION</button>
						    </div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">Min Amount</th>
												<th class="wd-15p border-bottom-0">Max Amount</th>
												<th class="wd-20p border-bottom-0">Operator Name</th>
												<th class="wd-20p border-bottom-0">API Source</th>
												<th class="wd-20p border-bottom-0">Updated Date</th>
												<th class="wd-20p border-bottom-0">Service Type</th>
												<th class="wd-20p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($amount_apisource)){$i=1;foreach($amount_apisource as $op){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td class="text-info"><b>₹<?php echo ucfirst($op->api_amount_min); ?></b></td>
												<td class="text-info"><b>₹<?php echo ucfirst($op->api_amount_max); ?></b></td>
												<td ><b><?php echo ucfirst($op->operatorname); ?></b></td>
												<td>
												    <select name="api" class="form-control" onchange="api_update(this.value,<?php echo $op->amount_id; ?>)">
                                                        <option >Select API</option>
                                                        <?php foreach($api as $ap){ ?>
                                                            <option value="<?php echo $ap->apisourceid; ?>" <?php echo($op->amount_apisource_id == $ap->apisourceid ? 'selected' : ''); ?>><?php echo $ap->apisourcename; ?></option>
                                                        <?php } ?>
                                                    </select>     
												</td>
												<td class="text-center" style="width:250px"><b><?php echo ucfirst($op->amount_apisource_update_datetime); ?></b></td>
												<td class="text text-primary"><b><?php echo ucfirst($op->opsertype); ?></b></td>
												<td>
												    <a href="<?php echo base_url();?>admin/delete-range-wise-api/<?php echo $op->amount_id; ?>" onclick="return confirm('Are you sure You want to Delete User Id <?php echo $op->amount_id; ?>?')" ><i class="typcn typcn-trash text-danger"></i></a>
												</td>
											</tr>
											<?php }} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--/div-->

				</div>
				<!-- /row -->
				
				<div id="myModal" class="modal fade" role="dialog">
                  <div class="modal-dialog modal-lg">
                
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="post" action="<?php echo site_url('admin/add_new_amount_apisource'); ?>">
                      <div class="modal-header">
                        <h5 class="modal-title">Add Package</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                         <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label">Operator</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="bx bx-add-to-queue"></i></div>
    										<select class="form-control" name="opcodenew" required>
    										    <?php if(!empty($operator)){$i=1;foreach($operator as $op){  ?>
    										    <option value="<?php echo $op->opcodenew; ?>"><?php echo $op->operatorname; ?></option>
    										    <?php }} ?>
    										</select>
                                        </div><br>
                                        <label class="form-label">API Source</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="bx bxs-bar-chart-alt-2"></i></div>
        									<select class="form-control" name="apisourceid" required>
                                                <?php foreach($api as $ap){ ?>
                                                    <option value="<?php echo $ap->apisourceid; ?>" ><?php echo $ap->apisourcename; ?></option>
                                                <?php } ?>
    										</select>
                                        </div><br>
                                        <label class="form-label">Minimum Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="bx bx-list-ul"></i></div>
        									 <input class="form-control" placeholder="Enter Max Amount" type="number" required name="min_amt">
                                        </div><br>
                                        <label class="form-label">Maximum Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="bx bx-list-ul"></i></div>
        									 <input class="form-control" placeholder="Enter Max Amount" type="number" required name="max_amt">
                                        </div>
                                    </div>
                                </div>
                      </div>
                      <div class="modal-footer">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <!--<button class="btn btn-success close">Close</button>-->
                        </div>
                      </div>
                    </div>
                
                  </div>
                </div>
				
				
				<!--<div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-hidden="true">-->
    <!--                <div class="modal-dialog modal-dialog-centered">-->
    <!--                    <div class="modal-content">-->
    <!--                        <form method="post" action="<?php echo site_url('admin/add_new_amount_apisource'); ?>">-->
    <!--                        <div class="modal-header">-->
    <!--                            <h5 class="modal-title">Add Package</h5>-->
    <!--                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
    <!--                        </div>-->
    <!--                        <div class="modal-body">-->
    <!--                            <div class="col-lg-12">-->
    <!--                                <div class="mb-3">-->
    <!--                                    <label class="form-label">Operator</label>-->
    <!--                                    <div class="input-group">-->
    <!--                                        <div class="input-group-text"><i class="bx bx-add-to-queue"></i></div>-->
    <!--										<select class="form-control" name="opcodenew" required>-->
    <!--										    <?php if(!empty($operator)){$i=1;foreach($operator as $op){  ?>-->
    <!--										    <option value="<?php echo $op->opcodenew; ?>"><?php echo $op->operatorname; ?></option>-->
    <!--										    <?php }} ?>-->
    <!--										</select>-->
    <!--                                    </div><br>-->
    <!--                                    <label class="form-label">API Source</label>-->
    <!--                                    <div class="input-group">-->
    <!--                                        <div class="input-group-text"><i class="bx bxs-bar-chart-alt-2"></i></div>-->
    <!--    									<select class="form-control" name="apisourceid" required>-->
    <!--                                            <?php foreach($api as $ap){ ?>-->
    <!--                                                <option value="<?php echo $ap->apisourceid; ?>" ><?php echo $ap->apisourcename; ?></option>-->
    <!--                                            <?php } ?>-->
    <!--										</select>-->
    <!--                                    </div><br>-->
    <!--                                    <label class="form-label">Minimum Amount</label>-->
    <!--                                    <div class="input-group">-->
    <!--                                        <div class="input-group-text"><i class="bx bx-list-ul"></i></div>-->
    <!--    									 <input class="form-control" placeholder="Enter Max Amount" type="number" required name="min_amt">-->
    <!--                                    </div><br>-->
    <!--                                    <label class="form-label">Maximum Amount</label>-->
    <!--                                    <div class="input-group">-->
    <!--                                        <div class="input-group-text"><i class="bx bx-list-ul"></i></div>-->
    <!--    									 <input class="form-control" placeholder="Enter Max Amount" type="number" required name="max_amt">-->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="modal-footer">-->
    <!--                            <div class="col-lg-12">-->
    <!--                                <button type="submit" class="btn btn-success">Submit</button>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        </form>-->
    <!--                    </div><!-- /.modal-content -->-->
    <!--                </div><!-- /.modal-dialog -->-->
    <!--            </div><!-- /.modal -->-->

				
			
            </div> <!-- container-fluid -->
        </div>
    </div>
	
		
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
    <script type="application/javascript">
    var api_update = (api,id) =>{			
        $.ajax({
			url: '<?php echo base_url(); ?>admin/update-amount-api-source',
			type: 'POST',
			cache: false,
			data: {api:api,id:id},
			success: function(res) 
			{
				alert(res);	
				location.reload();
			}
		});	 				
	}
    </script>

</html>