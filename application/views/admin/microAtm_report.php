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
							    
							    <div class="row">
							        <form action="<?php echo site_url('admin/search-micro-atm'); ?>" method="post" style="width: 100%;">
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
												<th class="wd-05p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">Trans Id</th>
												<th class="wd-15p border-bottom-0">User Id</th>
												<th class="wd-15p border-bottom-0">Ref Number</th>
												<th class="wd-10p border-bottom-0">Amount</th>
												<th class="wd-5p border-bottom-0">Type</th>
												<th class="wd-10p border-bottom-0">Date</th>
												<th class="wd-20p border-bottom-0">Details</th>
												<th class="wd-10p border-bottom-0">Status</th>
												
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($micro)){  $i=0;foreach($micro as $rec){  ?>
    											<tr>
    											    <td>
    											        <?php echo ++$i;?>
    											    </td>
    												<td>
    												    <b><?php echo $rec->ma_id;?></b>
												    </td>
    												
    												<td>
    												    <b><?php echo $rec->cus_id;?><br>
    												        <?php echo $rec->cus_name;?>
												        </b>
											        </td>
    												
    												<td>
    												    <b><?php echo $rec->bankRrn;?></b>
												    </td>
												    
    												<td>
    												    <?php echo $rec->transAmount;?>
												    </td>

    												<td>
    												    <b><?php echo $rec->type;?></b>
												    </td>
    												
    												<td>
    												    <b><?php echo $rec->ma_date_time;?></b>  
												    </td>

    												<td class="text-primary">Bank :- <?php echo $rec->bankNm;?> <br>Card Num :- <?php echo $rec->cardNum; ?><br>card Type :- <?php echo $rec->cardType;?></b></td>
    												
    												<td>
    												    <?php  
        												    $status=$rec->transactionStatus;
        												    if($status == 'FAILED'){ ?>
        												    <b Style="color:red">Failed</b>
        											    	<?php }if($status == 'SUCCESS'){ ?>
        											    	<b Style="color:green">Success</b>
        												    <?php }if($status == 'PENDING'){ ?>
        												    <b Style="color:yellow">Pending</b><br>
        												    <button class="btn-info">Check</button>
        												 <?php }?>
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