<?php $page = $this->uri->segment('2'); ?>

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
						<h4 class="content-title mb-2">Pan Coupon Report</h4>
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
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">User Id</th>
												<th class="wd-15p border-bottom-0">Order Id</th>
												<th class="wd-10p border-bottom-0">Date</th>
												<th class="wd-5p border-bottom-0">Qty</th>
												<th class="wd-5p border-bottom-0">Rate</th>
												<th class="wd-10p border-bottom-0">Amount</th>
												<th class="wd-10p border-bottom-0">Type</th>
												<th class="wd-30p border-bottom-0">Details</th>
												<th class="wd-10p border-bottom-0">Status</th>
												
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($coupons)){foreach($coupons as $rec){ ?>
    											<tr>
    												<td><b><?php echo $rec->coupon_buy_id;?></b></td>
    												
    												<td><b><?php echo $rec->vle_id;?><br><?php echo $rec->cus_name;?></b></td>
    												
    												<td><b><?php echo $rec->order_id;?></b></td>
    												<td style="width:"><?php echo $rec->date;?></td>
    												
    												<td><b><?php echo $rec->qty; ?></b></td>												
    												<td><?php echo $rec->rate; ?></b></td>

    												<td><b><?php echo $rec->amount;?></b></td>
    												
    												<td><b><?php echo $rec->coupon_type;?><b></td>

    												<td class="text-primary"><b>Old Bal :- <?php echo $rec->old_bal;?> <br> New Bal :- <?php echo $rec->new_bal;?></b></td>
    												
    												<?php  
    												    $status=$rec->status;
    												    if($status == 'failed'){ ?>
    												    
    												<td><b Style="color:red">Failed</b></td>        
    												        
    												<?php 
    												    }if($status == 'SUCCESS'){ ?>
    												
    												<td><b Style="color:green">Success</b></td>              
    												        
    												<?php 
    												    }if($status == 'pending'){ ?>
    												        
    												<td>
    												    <b Style="color:yellow">Pending</b><br>
    												    <button class="btn-info">Check</button>
    												</td>    
    												 <?php   } ?>
    												
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