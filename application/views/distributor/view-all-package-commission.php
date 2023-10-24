<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('distributor/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('distributor/loader'); ?>

		<?php $this->load->view('distributor/header'); ?>
		
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
												<th class="wd-20p border-bottom-0">Assign Commission</th>
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
												 <?php if(!empty($assignpackage)){foreach($assignpackage as $asspack){  if($asspack->packcomm_opcode==$pack->packcomm_opcode){ $despackcomm=$asspack->packcomm_comm; }}}?>
												<td>
												    <input type="text" class="form-control" value="<?php echo $pack->packcomm_comm; ?>" name="new_commission" onchange="updateCommission(this,<?php echo $pack->packcomm_comm; ?>,<?php echo $pack->packcomm_id; ?>,<?php echo $despackcomm; ?>)">
												</td>
												<td class="text-center" style="width:250px">
												   <b><?php echo $despackcomm; ?></b>
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
	
	   <?php $this->load->view('distributor/footer'); ?> 
	
	</body>
	
	<script>
	    
	    var updateCommission = (newCommission,oldCommission,packcommId,dispackcomm) =>{
	        var new_comm = newCommission.value;
	        var old_comm = oldCommission;
	        var packcomm_id = packcommId;
	         var dispackcomm = dispackcomm;
	         if(Number(dispackcomm) < Number(new_comm)){
	             alert('Please enter commission less then or equal to assign commission');
	         }
	         else{
    	        $.ajax({
    	            type: 'POST',
    	            url: "<?php echo base_url()?>distributor/package-commission-update",
    	            cache: 'false',
    	            data: {new_comm:new_comm,old_comm:old_comm,packcomm_id:packcomm_id},
    	            success: function(res){
    	                alert(res);
    	            }
    	        });
	         }
	    }
	    
	</script>

</html>