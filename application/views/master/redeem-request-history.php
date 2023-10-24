<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('master/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('master/loader'); ?>

		<?php $this->load->view('master/header'); ?>
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Payout to Bank</h4>
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

							     <div class=="row"><br></div>
								<div class="table-responsive"><br>
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">Txn Id</th>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-15p border-bottom-0">Details</th>
												<th class="wd-15p border-bottom-0">Status</th>
												<th class="wd-20p border-bottom-0">Amount</th>
												<th class="wd-10p border-bottom-0">Charges</th>
												<th class="wd-25p border-bottom-0">Total</th>
												<th class="wd-15p border-bottom-0">Req Date</th>
											</tr>
										</thead>
										<tbody>
											<?php if($ledger){ $i= 1; foreach($ledger as $rec){ ?> 
											<tr>
												
												<td><?php echo $i++ ?><br></td>
												<td><b><?php echo ucfirst($rec->cus_name).'('.$rec->cus_id.')' ?> <br><?php echo $this->encryption_model->decode($rec->cus_mobile); ?></b></td>
												<td style="width:250px">Name: <?php echo ucfirst($rec->accountHolderName); ?><br>Bank Name: <?php echo ucfirst($rec->bankName); ?><br>Account Number: <?php echo ucfirst($rec->bankAccount); ?><br>IFSC: <?php echo ucfirst($rec->bankIFSC); ?></b></td>
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
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
</html>