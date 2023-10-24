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
								<li class="breadcrumb-item active" aria-current="page">Dmt Report</li>
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
									<h4 class="card-title mg-b-2 mt-2">Dmt Report</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
							    <div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
    										<div class="card card-body pd-20 pd-md-40 border shadow-none">
    										    <form action="<?php echo site_url('Retailer/search_dmt_report'); ?>" method="post">
    											<h5 class="card-title mg-b-20">Search By</h5>
    										<div class="row">	
    										<div class="col-md-3 col-lg-3 col-xl-3">
        											<div class="form-group">
        												<label class="main-content-label tx-11 tx-medium tx-gray-600">Transaction ID</label>
        												<input class="form-control" placeholder="Transaction ID" name="rec_id" type="text">
        											</div>
    											</div>
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
    											<div class="col-md-3 col-lg-3 col-xl-3">
        											<div class="form-group">
        												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
        												<button class="btn btn-main-primary btn-block" type="submit" name="search_transaction_report">Search</button>
        											</div>
    											</div>
    												<div class="col-md-3 col-lg-3 col-xl-3">
        											<div class="form-group">
        												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
        												<button class="btn btn-main-primary btn-block">Reset</button>
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
												<th class="border-bottom-0">TXN ID</th>
												<th class="border-bottom-0">UTR NO</th>
												<th class="border-bottom-0">Mobile No</th>
												<th class="border-bottom-0">Befe Name</th>
												<th class="border-bottom-0">Amount</th>
												<th class="border-bottom-0">Charges</th>
												<th class="border-bottom-0">Account</th>
												<th class="border-bottom-0">IFSC</th>
												<th class="border-bottom-0">Date</th>
												<th class="border-bottom-0">Status</th>
												<th class="border-bottom-0">Action</th>
												
											</tr>
										</thead>
										<tbody>
										   <?php $credit_bal=0; if($dmt_txn){foreach($dmt_txn as $res){ ?>
											<tr>
												<td><?php echo $res->trans_id?></td>
												<td><?php echo $res->imps_ref_no?></td>
												
												<td><?php echo $res->mobile_no?></td>
												<td><?php echo $res->bene_name?></td>
												<td>₹ <?php echo $res->amount?></td>
												<td>
												    ₹ <?php echo $res->charge?>
											    </td>
												<td> <?php echo $res->account_num?></td>
												<td> <?php echo $res->ifsc?></td>
												<td> <?php echo $res->dmt_trnx_date?></td>
												
												     <?php  
												        $status = $res->status; 
												        if($status == 'SUCCESS'){
												     ?>
												     <td style="color:green">Success</td>
												     <?php }if($status == 'FAILED'){ ?>
												     <td style="color:red">Failed</td>
												     <?php }if($status == 'PENDING'){ ?>
												     <td style="color:yellow">Pending<br><button class="btn-info">check</button></td>
												     
												     <?php }?>
											    <td><a href="<?php echo base_url()?>retailer/receipt/<?php echo $res->dmt_trnx_id;?>"><i class="fa fa-print" class="btn-main-primary"></i></a></td>
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