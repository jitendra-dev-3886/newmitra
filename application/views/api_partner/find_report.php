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
						<h4 class="content-title mb-2">Hi, welcome back <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Mobile Number Report</li>
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
					
				<!-- main-content-body -->
				<div class="main-content-body">
				    <!--div-->
					<div class="col-xl-12">
						<div class="card mg-b-20">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mg-b-2 mt-2">Mobile Number Report</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
							    <div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										    <form action="<?php echo site_url('Retailer/find_report'); ?>" method="post">
                        											<h5 class="card-title mg-b-20">Search By</h5>
                        										<div class="row">
                        											<div class="col-md-3 col-lg-3 col-xl-3">
                            											<div class="form-group">
                            												<label class="main-content-label tx-11 tx-medium tx-gray-600">Mobile No.</label>
                            													<input class="form-control pd-r-80" required="" name="mobile" type="text" placeholder="Mobile No.">
                            											</div>
                        											</div>
                        											<div class="col-md-3 col-lg-3 col-xl-3">
                            											<div class="form-group">
                            												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
                            												<button type="submit" name="search_find_report" class="btn btn-main-primary btn-block">Search</button>
                            											</div>
                        											</div>
                        										</div>
                        										</form>
                        									</div>
                        								</div>
								</div>
								
								<div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap">
										<thead>
											<tr>
												<th class="border-bottom-0">Recharge Id	</th>
												<th class="border-bottom-0">Txn Date</th>
												<th class="border-bottom-0">Mobile No</th>
												<th class="border-bottom-0">Operator</th>
												<th class="border-bottom-0">Amount</th>
												<th class="border-bottom-0">Status</th>
												<th class="border-bottom-0">Opt ID.</th>
												<th class="border-bottom-0">Medium</th>
												<!--<th class="border-bottom-0">Action</th>-->
											</tr>
										</thead>
										<tbody>
										    <?php if($fund){foreach($fund as $rec){?>
											<tr>
												<td><?php echo $rec->recid;?></td>
												<td><?php echo $rec->reqdate;?></td>
												<td><?php echo $rec->mobileno;?></td>
												<td><?php echo $rec->operatorname;?></td>
												<td><?php echo $rec->amount;?></td>
												<td><?php echo $rec->status;?></td>
												<td><?php echo $rec->operator;?></td>
												<td><?php echo $rec->recmedium;?></td>
												<!--<td></td>-->
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
				<!-- / main-content-body -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>

</html>