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
						<h4 class="content-title mb-2">AEPS Ledger</h4>
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
									<span class="d-block">
										<span class="label ">CREDIT</span>
									</span>
									<span class="value">
										$53,000
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar"></span>
								</div>
							</div>
							<div class="d-flex justify-content-center">
								<div class="">
									<span class="d-block">
										<span class="label">DEBIT</span>
									</span>
									<span class="value">
										$34,000
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar31"></span>
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
							<!--<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mg-b-2 mt-2">SIMPLE TABLE</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>-->
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">Txn Id</th>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-20p border-bottom-0">OB.</th>
												<th class="wd-10p border-bottom-0">CR.</th>
												<th class="wd-25p border-bottom-0">DB.</th>
												<th class="wd-25p border-bottom-0">CB.</th>
												<th class="wd-25p border-bottom-0">Txn Type</th>
												<th class="wd-15p border-bottom-0">Txn Date</th>
											</tr>
										</thead>
										<tbody>
										    <?php if($ledger){ $i= 1; foreach($ledger as $rec){ ?> 
											<tr>
												<td><?php echo $i++ ?></td>
												<td><b><?php echo ucfirst($rec->cus_name).'('.$rec->cus_id.')' ?> <br><?php echo $this->encryption_model->decode($rec->cus_mobile); ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->aeps_txn_opbal; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->aeps_txn_crdt; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->aeps_txn_dbdt; ?></b></td>
												<td class="text-danger"><b>₹ <?php echo $rec->aeps_txn_clbal; ?></b></td>
												<td><?php echo ucfirst($rec->aeps_txn_type); ?></td>
												<td style="width:250px"><b><?php echo $rec->aeps_txn_date; ?></b></td>
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

</html>