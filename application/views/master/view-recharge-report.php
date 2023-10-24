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
						<h4 class="content-title mb-2">Recharge Report</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-auto">
						<div class=" d-flex right-page">
							<div class="d-flex justify-content-center mr-5">
								<div class="">
									<span class="d-block">
										<span class="label ">SUCCESS</span>
									</span>
									<span class="value">
										₹ <?php if(!empty($success[0]->amt)){echo $success[0]->amt; }else{ echo '0'; }?>
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
										₹ <?php if(!empty($failed[0]->amt)){echo $failed[0]->amt; }else{ echo '0'; }?>
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar31"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /breadcrumb -->
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-body">
							    <div class="row">
							        <form action="<?php echo site_url('master/search-recharge-report'); ?>" method="post">
									    <div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
										<div class="card card-body pd-20 pd-md-40 border shadow-none">
										<div class="row">	
										    <div class="col-md-2">
    											<div class="form-group">
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
											
											<div class="col-md-2">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="operator" id="operators">
														<option label="Select Opertaor"></option>
														<?php if($operator){foreach($operator as $oper){ ?>
														    <option value="<?php echo $oper->opid; ?>"><?php echo $oper->operatorname; ?></option>
														<?php }} ?>
													</select>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="apisource">
														<option label="Select Source"></option>
														<?php if($apisources){foreach($apisources as $api){ ?>
														    <option value="<?php echo $api->apisourceid; ?>"><?php echo $api->apisourcename; ?></option>
														<?php }} ?>
													</select>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="member_type" onchange="changeMembers(this)">
														<option value="all">All</option>
														<option value="retailer">Retailer</option>
														<option value="distributor">Distributor</option>
														<option value="master">Master Distributor</option>
														<option value="api">API Clients</option>
													</select>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="member_id" id="members">
														<option label="Select Retailer"></option>
														<?php if($members){foreach($members as $cus){ ?>
														    <option value="<?php echo $cus->cus_id; ?>"><?php echo $cus->cus_name; ?> (<?php echo $cus->cus_id ?>)</option>
														<?php }} ?>
													</select>
    											</div>
											</div>
											
										    <div class="col-md-2">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="status">
														<option label="Select Status"></option>
														<option value="success">SUCCESS</option>
														<option value="failed">FAILED</option>
														<option value="pending">PENDING</option>
														<option value="refund">REFUND</option>
													</select>
    											</div>
											</div>
										</div>
										
										<div class="row">	
											<div class="col-md-3">
    											<div class="form-group">
    												<input class="form-control pd-r-80" type="text" placeholder="Mobile No." name="mobile_number">
    											</div>
											</div>
											
											<div class="col-md-3">
    											<div class="form-group">
    												<input class="form-control pd-r-80" type="text" placeholder="Recharge ID" name="rec_id">
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<div class="input-group ">
                										<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div><input class="form-control fc-datepicker" placeholder="MM/DD/YYYY" type="text" name="date">
                									</div>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<button class="btn btn-main-primary btn-block">Search</button>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<input type="reset" class="btn btn-main-primary btn-block"></button>
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
												<th class="wd-15p border-bottom-0">Rec Id</th>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-20p border-bottom-0">Mobile</th>
												<th class="wd-10p border-bottom-0">Amount</th>
												<th class="wd-25p border-bottom-0">Status</th>
												<?php if($page == "recharge-report" || $page == "success-recharge-report"){ ?>
												<th class="wd-25p border-bottom-0">Response</th>
												<?php } ?>
												<th class="wd-15p border-bottom-0">Date</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($recharges)){foreach($recharges as $rec){ ?>
    											<tr>
    												<td>
    												    <b><?php echo $rec->recid; ?><br>
    												    <a href="<?php echo site_url('master/update-recharge-status/'.$rec->recid)?>"><i class="typcn typcn-edit text-success"></i></a>
    												    <!--<i class="typcn typcn-book text-success"></i> <br>-->
    												    <?php echo $rec->wellborn_trans_no; ?></b>
    												</td>
    												<td><b><?php echo ucfirst($rec->cus_name); ?><br><?php echo ucfirst($this->encryption_model->decode($rec->cus_mobile)); ?><br>
    												<?php echo $rec->cus_id; ?></b></td>
    												<td><b><?php echo $rec->mobileno; ?></b></td>
    												<td class="text-success"><b>₹ <?php echo $rec->amount; ?></b></td>
    												<td class="<?php if($rec->status == 'SUCCESS'){echo 'text-success';}else if($rec->status == 'FAILED'){echo 'text-danger';}else if($rec->status == 'PENDING'){echo 'text-warning';}else{echo 'text-primary'; }?>">
        												<b><?php echo strtoupper($rec->status); ?></b>
    												</td>
    												<?php if($page == "recharge-report" || $page == "success-recharge-report"){ ?>
    												<td class="text-info"><b><?php echo strtoupper($rec->statusdesc); ?></b></td>
    												<?php } ?>
    												<td style=" width: 200px;t;"><b><?php echo $rec->reqdate; ?></b></td>
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
	
	<script>
	    var changeMembers = memberType => {
	        var cus_type = memberType.value;
	        $.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>master/getMembersByType",
                cache: false,               
                data:{cus_type:cus_type,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
                success: function(res){
                    //alert(res);
                    $('#members').empty();
                    $('#members').append(res);
                }
            });
	    }
	    var getOperators = operatorType =>{
	        var operator_type = operatorType.value;
	        $.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>master/getOperatorsByType",
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