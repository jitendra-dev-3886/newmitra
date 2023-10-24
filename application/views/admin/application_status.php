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
						<h4 class="content-title mb-2">Application Status Details</h4>
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
								    <b>Application Status</b>
								    <!--
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add Bank Details</button></div>-->
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-20p border-bottom-0">Customer Name</th>
												<th class="wd-20p border-bottom-0">bank_of_india_csp</th>
												<th class="wd-20p border-bottom-0">gst_reg</th>
												<th class="wd-20p border-bottom-0">fino_csp</th>
												<th class="wd-20p border-bottom-0">udyam_registration</th>
												<th class="wd-20p border-bottom-0">itr_tds_return</th>
												<th class="wd-20p border-bottom-0">driving_license</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($members)){$i=1;foreach($members as $mbr){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><b><?php echo ucfirst($mbr->cus_name); ?>(<?php echo $mbr->cus_id; ?>)</b></td>
												<td>
												    <?php if($mbr->bank_of_india_csp=='1'){?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="api_update(this.value,<?php echo $mbr->id; ?>,'bank_of_india_csp')">
												    <?php }else{?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="api_update(this.value,<?php echo $mbr->id; ?>,'bank_of_india_csp')">
												    <?php }?>
												</td>
											    <td>
											        <?php if($mbr->gst_reg=='1'){?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="api_update(this.value,<?php echo $mbr->id; ?>,'gst_reg')">
												    <?php }else{?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="api_update(this.value,<?php echo $mbr->id; ?>,'gst_reg')">
												    <?php }?>
												 <td>
												    <?php if($mbr->fino_csp=='1'){?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="api_update(this.value,<?php echo $mbr->id; ?>,'fino_csp')">
												    <?php }else{?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="api_update(this.value,<?php echo $mbr->id; ?>,'fino_csp')">
												    <?php }?>
												</td>
										        <td>
										            <?php if($mbr->udyam_registration=='1'){?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="api_update(this.value,<?php echo $mbr->id; ?>,'udyam_registration')">
												    <?php }else{?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="api_update(this.value,<?php echo $mbr->id; ?>,'udyam_registration')">
												    <?php }?>
										        </td>
										        <td>
										            <?php if($mbr->itr_tds_return=='1'){?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="api_update(this.value,<?php echo $mbr->id; ?>,'itr_tds_return')">
												    <?php }else{?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="api_update(this.value,<?php echo $mbr->id; ?>,'itr_tds_return')">
												    <?php }?>
										        </td>
										        <td>
										            <?php if($mbr->driving_license=='1'){?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="api_update(this.value,<?php echo $mbr->id; ?>,'driving_license')">
												    <?php }else{?>
												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="api_update(this.value,<?php echo $mbr->id; ?>,'driving_license')">
												    <?php }?>
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

			</div>
			<!-- Container closed -->
		
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
	<script type="application/javascript">
        var api_update = (per,id,column) =>{
            $.ajax({
				url: '<?php echo base_url(); ?>admin/update_emp_permission',
				type: 'POST',
				cache: false,
				data: {per:per,id:id,column:column},
				success: function(res) 
				{
					alert(res);	
					location.reload();
				}
			});	 				
		}
    </script>		

</html>