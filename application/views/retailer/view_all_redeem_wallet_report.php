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
						<h4 class="content-title mb-2">Hi, welcome back  <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Redeem Wallet Report</li>
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
									<h4 class="card-title mg-b-2 mt-2">Redeem Wallet Report</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
							    <div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
										<div class="card card-body pd-20 pd-md-40 border shadow-none">
										    <form action="<?php echo site_url('Retailer/search_redeem_wallet_report'); ?>" method="post">
											<h5 class="card-title mg-b-20">Search By</h5>
										<div class="row"><!--	
										    <div class="col-md-3 col-lg-3 col-xl-3">
    											<div class="form-group">
    												<label class="main-content-label tx-11 tx-medium tx-gray-600">Recharge ID</label>
    												<input class="form-control" placeholder="Recharge ID" name="rec_id" type="text">
    											</div>
											</div>-->
											<div class="col-md-3 col-lg-3 col-xl-3">
    											<div class="form-group">
    												<label class="main-content-label tx-11 tx-medium tx-gray-600">From Date</label> 
    												<div class="input-group ">
                										<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div><input class="form-control fc-datepicker" name="from_dt" placeholder="MM/DD/YYYY" type="date">
                									</div>
    											</div>
											</div>
										    <div class="col-md-3 col-lg-3 col-xl-3">
    											<div class="form-group">
    												<label class="main-content-label tx-11 tx-medium tx-gray-600">To Date</label> 
    												<div class="input-group ">
                										<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div><input class="form-control fc-datepicker" name="to_dt" placeholder="MM/DD/YYYY" type="date">
                									</div>
    											</div>
											</div>
											<div class="col-md-3 col-lg-3 col-xl-3">
    											<div class="form-group">
    												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
    												<button class="btn btn-main-primary btn-block" type="submit" name="search_transaction_report">Search</button>
    											</div>
											</div><!--
											<div class="col-md-3 col-lg-3 col-xl-3">
    											<div class="form-group">
    												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
    												<button class="btn btn-main-primary btn-block">Reset</button>
    											</div>
											</div>-->
										</div>
										</form>
									</div>
								</div>
								</div>
								
								<div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap">
										<thead>
											<tr>
												<th class="border-bottom-0">#</th>
												<th class="border-bottom-0">TXN ID</th>
												<th class="border-bottom-0">Txn Date</th>
												<th class="border-bottom-0">Op.</th>
												<th class="border-bottom-0">Cr.</th>
												<th class="border-bottom-0">Dr.</th>
												<th class="border-bottom-0">Bal</th>
												<th class="border-bottom-0">TXN Type</th>
											</tr>
										</thead>
										<tbody>
										   <?php $credit_bal=0; if($ledger){ $i=0;foreach($ledger as $rec){ $credit_bal=$credit_bal+$rec->txn_crdt;?>
											<tr>
												<td><?php echo ++$i;?></td>
												<td><?php echo $rec->txn_id?></td>
												<td><?php echo $rec->txn_date?></td>
												<td>₹ <?php echo $rec->txn_opbal?></td>
												<td>₹ <?php echo $rec->txn_crdt?></td>
												<td>₹ <?php echo $rec->txn_dbdt?></td>
												<td>₹ <?php echo $rec->txn_clbal?></td>
												<td><?php echo $rec->txn_type?></td>
											
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