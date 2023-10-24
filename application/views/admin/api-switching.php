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
						<h4 class="content-title mb-2">API Management</h4>
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
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">OpCODE</th>
												<th class="wd-20p border-bottom-0">Operator Name</th>
												<th class="wd-20p border-bottom-0">API Source</th>
												<th class="wd-20p border-bottom-0">Updated Date</th>
												<th class="wd-20p border-bottom-0">Service Type</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($operator)){$i=1;foreach($operator as $op){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><?php echo ucfirst($op->opcodenew); ?></td>
												<td class="text-success"><b><?php echo ucfirst($op->operatorname); ?></b></td>
												<td>
												    <select name="api" class="form-control" onchange="api_update(this.value,<?php echo $op->opid; ?>)">
                                                        <option >Select API</option>
                                                        <?php foreach($api as $ap){ ?>
                                                            <option value="<?php echo $ap->apisourceid; ?>" <?php echo($op->apisource == $ap->apisourceid ? 'selected' : ''); ?>><?php echo $ap->apisourcename; ?></option>
                                                        <?php } ?>
                                                    </select>     
												</td>
												<td class="text-center" style="width:250px"><b><?php echo ucfirst($op->api_update_time); ?></b></td>
												<td class="text text-primary"><b><?php echo ucfirst($op->opsertype); ?></b></td>
												
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

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
	<script type="application/javascript">
        var api_update = (api,id) =>{			
            $.ajax({
				url: '<?php echo base_url(); ?>admin/update-api-source',
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