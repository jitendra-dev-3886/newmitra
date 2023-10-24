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
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">Package Name</th>
												<th class="wd-20p border-bottom-0">Operator Name</th>
												<th class="wd-20p border-bottom-0">Service Type</th>
												<th class="wd-20p border-bottom-0">Commission</th>
												<th class="wd-20p border-bottom-0">Updated Date</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($package)){$i=1;foreach($package as $pack){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><?php echo ucfirst($pack->package_name); ?></td>
												<td class="text-success"><b><?php echo ucfirst($pack->operatorname); ?></b></td>
												<td ><b><?php echo ucfirst($pack->opsertype); ?></b></td>
												<td>
												    <input type="text" class="form-control" value="<?php echo $pack->packcomm_comm; ?>" name="new_commission" onchange="updateCommission(this,<?php echo $pack->packcomm_comm; ?>,<?php echo $pack->packcomm_id; ?>)">
												</td>
												<td class="text-center" style="width:250px"><b><?php echo ucfirst($pack->package_date); ?></b></td>
												
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
	
	<script>
	    
	    var updateCommission = (newCommission,oldCommission,packcommId) =>{
	        var new_comm = newCommission.value;
	        var old_comm = oldCommission;
	        var packcomm_id = packcommId;
	        $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>admin/package-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,old_comm:old_comm,packcomm_id:packcomm_id},
	            success: function(res){
	                alert(res);
	            }
	        });
	    }
	    
	</script>

</html>