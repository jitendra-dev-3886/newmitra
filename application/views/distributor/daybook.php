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
												<th class="border-bottom-0">Recharge Id	</th>
												<th class="border-bottom-0">Txn Date</th>
												<th class="border-bottom-0">Mobile No</th>
												<th class="border-bottom-0">Operator</th>
												<th class="border-bottom-0">Amount</th>
												<th class="border-bottom-0">Status</th>
												<th class="border-bottom-0">Opt ID.</th>
												<th class="border-bottom-0">Medium</th>
											</tr>
										</thead>
										<tbody>
										    <?php if($recharge){$i=1;foreach($recharge as $rec){?>
											<tr>
											<tr>
											    <td><?php echo $i++;?></td>
												<td><?php echo $rec->recid;?></td>
												<td><?php echo $rec->reqdate;?></td>
												<td><?php echo $rec->mobileno;?></td>
												<td><?php echo $rec->operatorname;?></td>
												<td><?php echo $rec->amount;?></td>
												<td class="<?php if($rec->status == 'SUCCESS'){echo 'text-success';}else if($rec->status == 'FAILED'){echo 'text-danger';}else if($rec->status == 'PENDING'){echo 'text-warning';}else{echo 'text-primary'; }?>">
        												<b><?php echo strtoupper($rec->status); ?></b>
    											</td>
												<td><?php echo $rec->statusdesc;?></td>
												<td><?php echo $rec->recmedium;?></td>
											</tr>
											</tr>
											<?php }}?>
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

</html>