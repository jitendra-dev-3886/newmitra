<?php $page = $this->uri->segment('2'); ?>

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
						<h4 class="content-title mb-2">Aeps Transaction</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
							</ol>
						</nav>
					</div><!--
					<div class="d-flex my-auto">
						<div class=" d-flex right-page">
							<div class="d-flex justify-content-center mr-5">
								<div class="">
									<span class="d-block">
										<span class="label ">SUCCESS</span>
									</span>
									<span class="value">
										₹ 0
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar"></span>
								</div>
							</div>
							<div class="d-flex justify-content-center">
								<div class="">
									<span class="d-block">
										<span class="label">FAILED</span>
									</span>
									<span class="value">
										₹ 0
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar31"></span>
								</div>
							</div>
						</div>
					</div>-->
				</div>
				<!-- /breadcrumb -->
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-body">
							    <div class="row">
							        <form action="<?php echo site_url('admin/search_aeps_transaction'); ?>" method="post" style="width: 100%;">
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
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-2p border-bottom-0">#</th>
												<th class="wd-3p border-bottom-0">Trans ID</th>
												<th class="wd-15p border-bottom-0">Member Id</th>
												<th class="wd-15p border-bottom-0">Transaction Id</th>
												<th class="wd-10p border-bottom-0">Amount</th>
												<th class="wd-5p border-bottom-0">Type</th>
												<th class="wd-20p border-bottom-0">Date</th>
												<th class="wd-10p border-bottom-0">Aadhar</th>
												<th class="wd-10p border-bottom-0">UTR</th>
												<th class="wd-35p border-bottom-0">Details</th>
												<th class="wd-10p border-bottom-0">Dispute</th>
												<th class="wd-10p border-bottom-0">Status</th>
												
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($aepsUser)){$i=0;foreach($aepsUser as $rec){ ?>
    											<tr>
    											    <td><?php echo ++$i;?></td>
    												<td><b><?php echo $rec->aeps_id;?></b></td>
    												
    												<td><b><?php echo $rec->apiclid;?></b></td>
    												<td style="width:">
    												    <?php echo $rec->transaction_ref_id;?>
    												</td>
    												<td><b><?php echo $rec->amount; ?></b></td>												
    												<td class="text-primary">
    												    <?php if($rec->transactionType == 'M'){ ?>
    												    <b><?php echo 'Adhar Pay'; ?></b>
    												    <?php } else { ?>
    												    <b><?php echo $rec->transactionType; ?></b>
    												    <?php }?>
    												    </td>
    												<td><b><?php echo $rec->aeps_date_time;?></b></td>
    												<td><b><?php echo $rec->aadhar_number; ?></b></td>
    												<td><b><?php echo $rec->utr; ?></b></td>
    												<td><b>Bank :- <?php echo $rec->bankName;?><br>Device :- Mantra</b></td>
    												<td><b><?php echo $rec->dmt_dispute_status;?></b></td>
    												<td>
    												<?php  
    												    $status=$rec->status;
    												    if($status == 'failed'){ ?>
    												<b Style="color:red">FAILED</b>     
    												<?php 
    												    }if($status == 'successful'){ ?>
    												<b Style="color:green">SUCCESS</b>          
    												<?php 
    												    }if($status == 'pending'){ ?>
    												    <b Style="color:yellow">PENDING</b><br>
    												    <button class="btn-info">Check</button>
    												   
    												 <?php   } ?>
    												 </td> 
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


</html>