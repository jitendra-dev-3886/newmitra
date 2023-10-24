<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('retailer/loader'); ?>

		<?php $this->load->view('retailer/header'); ?>
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Redeem Request</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($this->uri->segment('2')); ?></li>
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
							    <form action="<?php echo base_url();?>retailer/redeem_transaction" method="post" enctype="multipart/form-data">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">Txn Id</th>
												<th class="wd-15p border-bottom-0">Details</th>
												<th class="wd-15p border-bottom-0">Status</th>
												<th class="wd-20p border-bottom-0">Amount</th>
												<th class="wd-10p border-bottom-0">Charges</th>
												<th class="wd-25p border-bottom-0">Total</th>
												<th class="wd-15p border-bottom-0">Req Date</th>
											</tr>
										</thead>
										<tbody>
											<?php if($requests){ $i= 1; foreach($requests as $rec){ ?> 
											<tr>
												<td><?php echo $i++ ?><br></td>
												<td style="width:250px"><b>Name: <?php echo ucfirst($rec->accountHolderName); ?><br>Bank Name: <?php echo ucfirst($rec->bankName); ?><br>Account Number: <?php echo ucfirst($rec->bankAccount); ?><br>IFSC: <?php echo ucfirst($rec->bankIFSC); ?></b></td>
												<td class="<?php if($rec->status == 'SUCCESS'){echo 'text-success';}else if($rec->status == 'FAILED'){echo 'text-danger';}else if($rec->status == 'PENDING'){echo 'text-warning';}else{echo 'text-primary'; }?>">
    												<b><?php echo strtoupper($rec->status); ?></b>
												</td>
												<td class="text-success"><b>₹ <?php echo $rec->amount; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->charge; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->charge + $rec->amount; ?></b></td>
												<td style="width:250px"><b><?php echo $rec->request_date; ?></b></td>
											</tr>
											<?php }} ?>
										</tbody>
									</table>
								</div>
							</form>
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
	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
</html>