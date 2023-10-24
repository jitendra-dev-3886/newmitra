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
						<h4 class="content-title mb-2">Coupon Price</h4>
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
												<th class="wd-5p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">Coupon Id</th>
												<th class="wd-10p border-bottom-0">Price</th>
												<th class="wd-10p border-bottom-0">Distributor Commission</th>
												<th class="wd-10p border-bottom-0">Master Commission</th>
												<th class="wd-10p border-bottom-0">Type</th>
												<th class="wd-10p border-bottom-0">Action</th>
												
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($coupon)){ $i=0;foreach($coupon as $rec){ ?>
    											<tr>
    											    <td><b><?php echo ++$i;?></b></td>
    												<td><b><?php echo $rec->coupon_price_id;?></b></td>
    												
    												<td class="text-success"><b>₹ <?php echo $rec->coupon_price;?><br></b></td>
    												
    												<td><b>₹ <?php echo $rec->dist_commision;?><br></b></td>
    												
    												<td><b>₹ <?php echo $rec->master_commision;?><br></b></td>
    												
    												<td><b><?php echo $rec->coupon_type;?></b></td>

    												<td><i class="fa fa-edit" value="" onclick="test(<?php echo $rec->coupon_price_id;?>)" class="btn ripple btn-primary" data-target="#modaldemo1" data-toggle="modal"></i></td>
    												
    											</tr>
											<?php }} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					
					<div class="modal" id="modaldemo1">
                		<div class="modal-dialog" role="document">
                			<div class="modal-content modal-content-demo">
                				<form action="<?php echo base_url()?>admin/changeCoupon_price" method="post">
                    				<div class="modal-header">
                    					<h6 class="modal-title">UPDATE PAN COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    				</div>
                    				<div class="modal-body">
                    					<input type="hidden" id="here" name="id" >
                        					<div class="form-group">
                        					    <label>Coupan Charge</label>
                        					    <input type="text" placeholder="Enter Coupan Charge" class="form-control" name="price" required>
                        					</div>
                        					<div class="form-group">
                        					    <label>Distributor Commission</label>
                        					    <input type="text" class="form-control" name="dist_commision" placeholder="Enter Distributor Commission" required>
                        					</div>
                        					<div class="form-group">
                        					    <label>Master Commission</label>
                        					    <input type="text" class="form-control" placeholder="Enter Master Commission" name="master_commision" required>
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
		    function test(id){
		    $('#here').val(id);
		    }
		</script>
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>


</html>