<?php $page = $this->uri->segment('2'); ?>

<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('master/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('master/loader'); ?>

		<?php $this->load->view('master/header'); ?>
		
		
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
			    
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Micro Atm Report</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
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
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">User Id</th>
												<th class="wd-15p border-bottom-0">Ref Number</th>
												<th class="wd-10p border-bottom-0">Amount</th>
												<th class="wd-5p border-bottom-0">Type</th>
												<th class="wd-10p border-bottom-0">Date</th>
												<th class="wd-40p border-bottom-0">Details</th>
												<th class="wd-10p border-bottom-0">Status</th>
												
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($micro)){foreach($micro as $rec){ ?>
    											<tr>
    												<td><b><?php echo $rec->cus_id;?></b></td>
    												
    												<td><b><?php echo $rec->cus_id;?><br><?php echo $rec->cus_name;?></b></td>
    												
    												<td><b><?php echo $rec->bankRrn;?></b></td>
    												<td style="width:"><?php echo $rec->transAmount;?></td>

    												<td><b><?php echo $rec->type;?></b></td>
    												
    												<td><b><?php echo $rec->ma_date_time;?><b></td>

    												<td class="text-primary">Bank :- <?php echo $rec->bankNm;?> <br>Card Num :- <?php echo $rec->cardNum; ?><br>card Type :- <?php echo $rec->cardType;?></b></td>
    												
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
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>


</html>