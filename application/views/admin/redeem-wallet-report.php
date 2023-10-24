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
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Payout to Wallet</h4>
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
							        <form action="<?php echo site_url('admin/search-redeem-wallet-report'); ?>" method="post" style="width: 100%;">
									    <div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
    										<div class="card card-body pd-20 pd-md-40 border shadow-none">
    										    <div class="row">	
        											<div class="col-md-4">
            											<div class="form-group">
            												<div class="input-group ">
                        										<div class="input-group-prepend">
                        											<div class="input-group-text">
                        												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>&nbsp;FROM
                        											</div>
                        										</div>
                        										<input class="form-control fc-datepicker hasDatepicker" placeholder="MM/DD/YYYY" type="date" id="dp1612569947060" name="from_dt">
                        									</div>
            											</div>
        											</div>
        											
        											<div class="col-md-4">
            											<div class="form-group">
            												<div class="input-group ">
                        										<div class="input-group-prepend">
                        											<div class="input-group-text">
                        												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>&nbsp;TO
                        											</div>
                        										</div>
                        										<input class="form-control fc-datepicker hasDatepicker" placeholder="MM/DD/YYYY" type="date" id="dp1612569947060" name="to_dt">
                        									</div>
            											</div>
        											</div>
        											<div class="col-md-4">
            											<div class="form-group">
            												<button class="btn btn-main-primary btn-block">Search</button>
            											</div>
        											</div>
        										</div>
        									</div>
    								    </div>
								    </form>
								</div>
								<div class="table-responsive"><br>
									<table class="table text-md-nowrap text-center" id="example1">
										
										<thead>
											<tr>
												<th class="wd-5p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">Txn Id</th>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-15p border-bottom-0">Status</th>
												<th class="wd-10p border-bottom-0">Op Bal</th>
												<th class="wd-20p border-bottom-0">Amount/Cr.</th>
												<th class="wd-25p border-bottom-0">Cl Bal</th>
												<th class="wd-15p border-bottom-0">Date</th>
											</tr>
										</thead>
										<tbody>
											<?php if($ledger){ $i= 1; foreach($ledger as $rec){ ?> 
											<tr>
												<td><?php echo $i++ ?><br></td>
												<td><?php echo $rec->txn_id; ?><br></td>	
												<td><b><?php echo ucfirst($rec->cus_name).'('.$rec->cus_id.')' ?> <br><?php echo $this->encryption_model->decode($rec->cus_mobile); ?></b></td>
												<td class="text-success"><b>SUCCESS</b></td>
												<td class="text-info"><b>₹ <?php echo $rec->txn_opbal; ?></b></td>
												<td class="text-info"><b>₹ <?php echo $rec->txn_crdt; ?></b></td>
												<td class="text-info"><b>₹ <?php echo $rec->txn_clbal ?></b></td>
												<td style="width:250px"><b><?php echo $rec->txn_date; ?></b></td>
												
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
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
</html>