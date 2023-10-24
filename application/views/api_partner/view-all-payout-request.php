<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('api_partner/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('api_partner/loader'); ?>

		<?php $this->load->view('api_partner/header'); ?>
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
							    
							    <div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
    										<div class="card card-body pd-20 pd-md-40 border shadow-none">
    										    <form action="<?php echo site_url('api_partner/search_redeem_request'); ?>" method="post">
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
								</div><!--
							    <form action="<?php echo base_url();?>api_partner/redeem_transaction" method="post" enctype="multipart/form-data">-->
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">#</th>    
												<th class="wd-15p border-bottom-0">Txn Id</th>
												<th class="wd-15p border-bottom-0">Details</th>
												<th class="wd-20p border-bottom-0">Amount</th>
												<th class="wd-10p border-bottom-0">Charges</th>
												<th class="wd-25p border-bottom-0">Total</th>
												<th class="wd-15p border-bottom-0">Req Date</th>
												<th class="wd-15p border-bottom-0">Status</th>
											</tr>
										</thead>
										<tbody>
											<?php if($requests){ $i= 1; foreach($requests as $rec){ ?> 
											<tr>
												<td><?php echo $i++ ?></td>
												<td><b><?php echo $rec->pay_req_id; ?></b></td>
												<td style="width:250px"><b>Name: <?php echo ucfirst($rec->accountHolderName); ?><br>Bank Name: <?php echo ucfirst($rec->bankName); ?><br>Account Number: <?php echo ucfirst($rec->bankAccount); ?><br>IFSC: <?php echo ucfirst($rec->bankIFSC); ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->amount; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->charge; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->charge + $rec->amount; ?></b></td>
												<td style="width:250px"><b><?php echo $rec->request_date; ?></b></td>
												<td class="<?php if($rec->status == 'SUCCESS'){echo 'text-success';}else if($rec->status == 'FAILED'){echo 'text-danger';}else if($rec->status == 'PENDING'){echo 'text-warning';}else{echo 'text-primary'; }?>">
    												<b><?php echo strtoupper($rec->status); ?></b>
												</td>
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
	
	   <?php $this->load->view('api_partner/footer'); ?> 
	
	</body>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
</html>