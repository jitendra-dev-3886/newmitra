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
								<li class="breadcrumb-item active" aria-current="page">Commission Report</li>
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
									<h4 class="card-title mg-b-2 mt-2">Commission Report</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
							    <div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										    <form action="<?php echo site_url('Retailer/commission_report'); ?>" method="post">
                        											<h5 class="card-title mg-b-20">Search By</h5>
                        										<div class="row">	
                        										    <div class="col-md-3 col-lg-3 col-xl-3">
                            											<div class="form-group">
                            												<label class="main-content-label tx-11 tx-medium tx-gray-600">Select Service</label> 
                            													<select class="form-control select2-no-search" name="operator_type" onchange="getOperators(this)">
                            														<option label="Select Service"></option>
                            														<option value="mobile">Mobile Prepaid</option>
                            														<option value="postpaid">Mobile Postpaid</option>
                            														<option value="dth">DTH</option>
                            														<option value="datacard">Datacard</option>
                            														<option value="electricity">Electricity</option>
                            														<option value="gas">Gas</option>
                            													</select>
                            											</div>
                        											</div>
                        											<div class="col-md-3 col-lg-3 col-xl-3">
                            											<div class="form-group">
                            												<label class="main-content-label tx-11 tx-medium tx-gray-600">Select Operator</label> 
                            												<select class="form-control select2-no-search" name="operator" id="operators">
                        														<option label="Select Opertaor"></option>
                        														<?php if($operator){foreach($operator as $oper){ ?>
                        														    <option value="<?php echo $oper->opid; ?>"><?php echo $oper->operatorname; ?></option>
                        														<?php }} ?>
                        													</select>
                            											</div>
                        											</div>
                        											<div class="col-md-3 col-lg-3 col-xl-3">
                            											<div class="form-group">
                            												<label class="main-content-label tx-11 tx-medium tx-gray-600">Select Date</label> 
                            												<div class="input-group ">
                                        										<div class="input-group-prepend">
                                        											<div class="input-group-text">
                                        												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                        											</div>
                                        										</div><input class="form-control fc-datepicker" name="date" placeholder="MM/DD/YYYY" type="text">
                                        									</div>
                            											</div>
                        											</div>
                        												<div class="col-md-3 col-lg-3 col-xl-3">
                            											<div class="form-group">
                            												<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>
                            												<button type="submit" name="search_commission_report" class="btn btn-main-primary btn-block">Search</button>
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
												<th class="border-bottom-0">Operator Name</th>
												<th class="border-bottom-0">Recharge Amount</th>
												<th class="border-bottom-0">Commission Amount</th>
											</tr>
										</thead>
										<tbody>
										     <?php if($fund){foreach($fund as $fu){?>
											<tr>
												<td><?php echo $fu->operatorname?></td>
												<td>₹ <?php echo $fu->amount?></td>
												<td>₹ <?php echo $fu->com?></td>
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
    <script>
	    
	    var getOperators = operatorType =>{
	        var operator_type = operatorType.value;
	        $.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>Retailer/getOperatorsByType",
                cache: false,               
                data:{operator_type:operator_type,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
                success: function(res){
                    //alert(res);
                    $('#operators').empty();
                    $('#operators').append(res);
                }
            });
	    }
	</script>
</html>