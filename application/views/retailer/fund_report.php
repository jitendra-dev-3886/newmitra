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
								<li class="breadcrumb-item active" aria-current="page">Fund Report</li>
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
									<h4 class="card-title mg-b-2 mt-2">Fund Report</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
							    <div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
    										<div class="card card-body pd-20 pd-md-40 border shadow-none">
    										    <form action="<?php echo site_url('Retailer/search_fund_report'); ?>" method="post">
    											<h5 class="card-title mg-b-20">Search By</h5>
    										<div class="row">
                                                <div class="col-md-4 col-lg-4 col-xl-4">
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
    											
    										    <div class="col-md-4 col-lg-4 col-xl-4">
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
    											<div class="col-md-4 col-lg-4 col-xl-4">
        											<div class="form-group">
        												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
        												<button class="btn btn-main-primary btn-block" type="submit" name="search_transaction_report">Search</button>
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
												<th class="border-bottom-0">#</th>
												<th class="border-bottom-0">TXN ID</th>
												<th class="border-bottom-0">Opening Balance</th>
												<th class="border-bottom-0">Credit Balance</th>
												<th class="border-bottom-0">Closing Balance</th>
												<th class="border-bottom-0">Remarks</th>
												<th class="border-bottom-0">Date</th>
											</tr>
										</thead>
										<tbody>
										    <?php $credit_bal=0; if($fund){$i=0;foreach($fund as $fu){ $credit_bal=$credit_bal+$fu->txn_crdt;?>
											<tr>
											    <td><?php echo ++$i;?></td>
												<td><?php echo $fu->txn_id?></td>
												<td>₹ <?php echo $fu->txn_opbal?></td>
												<td>₹ <?php echo $fu->txn_crdt?></td>
												<td>₹ <?php echo $fu->txn_clbal?></td>
												<td><?php echo $fu->txn_comment?></td>
												<td><?php echo $fu->txn_date?></td>
											</tr>
											<?php }}?>
											<tr style="color: #fff;background: linear-gradient(45deg, #e43364, #3858f9) !important;border-color: #2e50fb;">
												<td>Total: </td>
												<td></td>
												<td></td>
												<td>₹ <?php echo $credit_bal;?></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
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