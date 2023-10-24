<?php $page = $this->uri->segment('2'); ?>

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
						<h4 class="content-title mb-2">Service Management</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
							</ol>
						</nav>
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
												<th class="wd-15p border-bottom-0">ID</th>
												<th class="wd-15p border-bottom-0">Service</th>
												<th class="wd-15p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($service)){foreach($service as $ser){ ?>
    											<tr>
    											    <td><?php echo $ser->service_id;?></td>
    											    <td><?php echo $ser->name;?></td>
    												<td>
    												    <?php if($ser->status == 'active'){ ?>
    												    <button class="btn btn-success" id="" onClick="changeStatus('<?php echo $ser->service_id;?>','<?php echo $ser->status;?>')">UP</button></b>
    												    <?php } else{ ?>
    												    <button class="btn btn-danger" id="<?php echo $ser->serives_id;?>" onClick="changeStatus('<?php echo $ser->service_id;?>','<?php echo $ser->status;?>')" >Down</button></b>
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
					<div class="modal" id="modaldemo1">
                		<div class="modal-dialog" role="document">
                			<div class="modal-content modal-content-demo">
                				<form action="<?php echo base_url()?>admin/aepsPackage_assign" method="post">
                    				<div class="modal-header">
                    					<h6 class="modal-title">Assign Package</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    				</div>
                    				<div class="modal-body">
                    					<input type="hidden" id="here" name="cus_id" >
                    					<div class="form-group">
                    					    <label>Select Package</label>
                    					    <select class="form-control" name="pk_id">
                    					        <option value="">select</option>
                    					        <?php if(is_array($package)){ foreach($package as $p) { ?>
                    					        <option value="<?php echo $p->aeps_comm_id;?>"><?php echo $p->slab;?></option>
                    					        <?php }}?>
                    					    </select>
                    					</div>
                					</div>
                    				<div class="modal-footer">
                    					<input class="btn ripple btn-primary" type="submit">
                    					<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                    				</div>
                    			</form>	
                			</div>
                		</div>
                	</div>
				</div>
				<!-- /row -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
		<script>
    	    function changeStatus(id,status){
    	        $.ajax({
    	            type: "post",
    	            url: "<?php echo base_url()?>admin/downService",
    	            cache: false,
    	            data: {status:status,id:id},
    	            success: function(res){
                        location.reload();
    	            }
    	        });
    	    }
    	</script>
	   <?php $this->load->view('admin/footer'); ?> 
	</body>
</html>