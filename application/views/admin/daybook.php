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
			
				<!-- row -->
				<div class="row row-sm" style="margin-top:3%">
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body ">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<i class="ti-bar-chart project bg-primary-transparent mx-auto text-primary "></i>
								</div>
								<h6 class="mb-1 text-muted">Success Recharge</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($success[0]->amt)){echo $success[0]->amt;}else{echo '0';} ?></h3>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body ">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<i class="ti-pie-chart project bg-pink-transparent mx-auto text-pink "></i>
								</div>
								<h6 class="mb-1 text-muted">Failed Recharge</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($failed[0]->amt)){echo $failed[0]->amt;}else{echo '0';} ?></h3>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body">
								<div class="feature widget-2 text-center mt-0 mb-3">
								    <i class="ti-stats-up project bg-success-transparent mx-auto text-success "></i>
								</div>
								<h6 class="mb-1 text-muted">Fund Credited</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($fundCredit[0]->amt)){echo $fundCredit[0]->amt;}else{echo '0';} ?></h3>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body ">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<i class="ti-pulse  project bg-teal-transparent mx-auto text-teal "></i>
								</div>
								<h6 class="mb-1 text-muted">Fund Debited</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($fundDebit[0]->amt)){echo $fundDebit[0]->amt;}else{echo '0';} ?></h3>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->
				
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
												<th class="wd-15p border-bottom-0">Sr No.</th>
												<th class="wd-15p border-bottom-0">Operator</th>
												<th class="wd-20p border-bottom-0">Success Amount</th>
												<th class="wd-20p border-bottom-0">Success Hits</th>
												<th class="wd-10p border-bottom-0">Pending Amount</th>
												<th class="wd-10p border-bottom-0">Pending Hits</th>
												<th class="wd-25p border-bottom-0">Failed Amount</th>
												<th class="wd-10p border-bottom-0">Failed Hits</th>
												<th class="wd-25p border-bottom-0">Total Hits</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>1</td>
												<td class="text-info"><b>Airtel</b></td>
												<td class="text-success"><b>₹ 0</b></td>
												<td class="text-success"><b>0</b></td>
												<td class="text-warning"><b>₹ 0</b></td>
												<td class="text-warning"><b>0</b></td>
												<td class="text-danger"><b>₹ 1</b></td>
												<td class="text-danger"><b>1</b></td>
												<td class="text-primary"><b>1</b></td>
											</tr>
										</tbody>
										<tfoot>
										        <td></td>
												<td class="text-info"><b>Total</b></td>
												<td class="text-success"><b>₹ 0</b></td>
												<td class="text-success"><b>0</b></td>
												<td class="text-warning"><b>₹ 0</b></td>
												<td class="text-warning"><b>0</b></td>
												<td class="text-danger"><b>₹ 1</b></td>
												<td class="text-danger"><b>1</b></td>
												<td class="text-primary"><b>1</b></td>
										</tfoot>
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

</html>