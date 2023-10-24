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
						<h4 class="content-title mb-2">Operator API Switching</h4>
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

                <!-- start page title -->
<!-- end page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            
                            <div class="card-body">

                                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                                <th class="wd-15p border-bottom-0">ID.</th><!--
												<th class="wd-15p border-bottom-0">OpCODE</th>-->
												<th class="wd-20p border-bottom-0">Operator Name</th>
												<th class="wd-20p border-bottom-0">API Source</th><!--
												<th class="wd-20p border-bottom-0">Updated Date</th>-->
												<!--<th class="wd-20p border-bottom-0">Service Type</th>-->
                                        </tr>
                                    </thead>


                                    <tbody>
                                         <?php if(!empty($priority_api)){$i=1;foreach($priority_api as $op){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td class="text-success"><b><?php echo ucfirst($op->operator_name); ?></b></td>
												<td>
												    <table>
                                                        <?php foreach($api as $ap){ ?>
                                                        <tr><td><?php echo $ap->apisourcename; ?></td></tr>
                                                        <tr><td><input type="text" value="<?php $col_name=$ap->apisourcename; echo $op->$col_name;?>" onchange="api_update(this.value,<?php echo $op->priority_api_id; ?>,'<?php echo $ap->apisourcename;?>')"></td></tr>
                                                        <?php } ?>
												    </table>  
												</td>
											</tr>
											<?php }} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> 
                
                <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        
   </div>
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
	<script type="application/javascript">
        var api_update = (value,id,col_name) =>{
            $.ajax({
				url: '<?php echo base_url(); ?>admin/update_api_priority',
				type: 'POST',
				cache: false,
				data: {value:value,id:id,col_name:col_name},
				success: function(res) 
				{
					alert(res);	
					location.reload();
				}
			});	 				
		}
    </script>		

</html>