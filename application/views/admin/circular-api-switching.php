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
						<h4 class="content-title mb-2">Circle Wise API Switching</h4>
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
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">Circle</th>
												<th class="wd-20p border-bottom-0">API Source</th>
												<th class="wd-20p border-bottom-0">Updated Date</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($circle_apisource)){$i=1;foreach($circle_apisource as $op){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td class="text-info"><b><?php echo ucfirst($op->circle_name); ?></b></td>
												<td>
												    <select name="api" class="form-control" onchange="api_update(this.value,<?php echo $op->circle_id; ?>)">
                                                        <option >Select API</option>
                                                        <?php foreach($api as $ap){ ?>
                                                            <option value="<?php echo $ap->apisourceid; ?>" <?php echo($op->circle_api_source_id == $ap->apisourceid ? 'selected' : ''); ?>><?php echo $ap->apisourcename; ?></option>
                                                        <?php } ?>
                                                    </select>     
												</td>
												<td class="text-center" style="width:250px"><b><?php echo ucfirst($op->circle_apisource_update_datetime); ?></b></td>

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
    
			
            </div> <!-- container-fluid -->
        </div>
    </div>
		
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
    <script type="application/javascript">
        var api_update = (api,id) =>{			
            $.ajax({
    			url: '<?php echo base_url(); ?>admin/update-circle-api-source',
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