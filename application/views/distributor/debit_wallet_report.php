<?php $page = $this->uri->segment('2'); ?>

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
						<h4 class="content-title mb-2">Debit Wallet Report</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-auto">
						<div class=" d-flex right-page">
							<div class="d-flex justify-content-center mr-5">
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
												<th class="wd-15p border-bottom-0">TXN ID</th>
												<th class="wd-15p border-bottom-0">Transfer To</th>
												<th class="wd-20p border-bottom-0">User Name</th>
												<th class="wd-10p border-bottom-0">TXN Date</th>
												<th class="wd-25p border-bottom-0">Op.</th>
												<th class="wd-15p border-bottom-0">Cr.</th>
												<th class="wd-15p border-bottom-0">Dr.</th>
												<th class="wd-15p border-bottom-0">Bal</th>
												<th class="wd-15p border-bottom-0">TXN Type</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($credit)){foreach($credit as $rec){ ?>
    											<tr>
    												<td><b><?php echo $rec->txn_id; ?><br></td>
    												<td><b><?php echo $rec->cus_id; ?><br></td>
    												<td><b><?php echo $rec->cus_name; ?><br></td>
    												<td><b><?php echo date("d-m-Y h:i:s a", $rec->txn_time); ?><br></td>
    												<td><b><?php echo $rec->txn_opbal; ?><br></td>
    												<td><b><?php echo $rec->txn_crdt; ?><br></td>
    												<td><b><?php echo $rec->txn_dbdt; ?><br></td>
    												<td><b><?php echo $rec->txn_clbal; ?><br></td>
    												<td><b><?php echo $rec->txn_type; ?><br></td>
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
		<!-- main-content closed -->
	
	   <?php $this->load->view('distributor/footer'); ?> 
	
	</body>
	
	<script>
	    var getOperators = operatorType =>{
	        var operator_type = operatorType.value;
	        $.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>admin/getOperatorsByType",
                cache: false,               
                data:{operator_type:operator_type,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
                success: function(res){
                    //alert(res);
                    $('#operators').empty();
                    $('#operators').append(res);
                }
            });
	    }
	</script>

</html>