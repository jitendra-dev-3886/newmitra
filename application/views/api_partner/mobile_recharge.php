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
								<li class="breadcrumb-item active" aria-current="page">Mobile Recharge</li>
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
				    <div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="main-content-label mg-b-5">
									Mobile Recharge
								</div>
								<p class="mg-b-20">Select Your Service.</p>
								<?php if($this->session->flashdata('message')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('message');?></strong> 
                                </div>
                                <?php }?>
								<div class="example">
										<div class="panel panel-primary tabs-style-1">
											<div class=" tab-menu-heading">
												<div class="tabs-menu1">
													<!-- Tabs -->
													<ul class="nav panel-tabs main-nav-line">
														<li><a href="#tab1" class="nav-link active" data-toggle="tab">Prepaid</a></li>
														<li><a href="#tab2" class="nav-link" data-toggle="tab">Postpaid</a></li>
													</ul>
												</div>
											</div>
											<div class="panel-body tabs-menu-body main-content-body-right border">
												<div class="tab-content">
													<div class="tab-pane active" id="tab1">
													    <form action="<?php echo base_url();?>/Retailer/recharge_ot" method="post">
														    <div class="row">
									                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        											<h5 class="card-title mg-b-20">Your Prepaid Details</h5>
                        											<div class="row">
                            											<div class="col-md-12 col-lg-12 col-xl-12">
                                											<div class="form-group">
                                											    <label class="main-content-label tx-11 tx-medium tx-gray-600">Select Operator</label><br>
                                											    <div class="row">
                                											        <?php if($operator){foreach($operator as $op){ if($op->opsertype=='mobile'){?>
                                											        <div class="col-md-2 col-lg-2 col-xl-2">
                                        											    <label class="rdiobox"><input type="radio" name="operator" value="<?php echo $op->opcodenew;?>" required=""> 
                                        											    <span>
                                        											        <div for="Airtel" class="main-profile-menu" style="width: 50px;height: 50px;border-radius: 50%;">
                                        											            <img alt="" src="<?php echo base_url().'/assets/'.$op->operator_image;?>">
                                        											            <label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $op->operatorname;?></label><br>
                                        											        </div>
                                        											    </span>
                                        											    </label>
                                    											    </div>
                                    											    <?php }}}?>
                                											    </div>
                                											</div>
                            											</div>
                                									</div>
                        											<div class="row">
                            											<div class="col-md-12 col-lg-12 col-xl-12">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Prepaid Mobile Number</label> 
                                												<input class="form-control" id="prepaid_mobile" required="" name="number" minlength="10" maxlength="10" placeholder="Mobile Number" type="text">
                                												<input class="form-control" required="" type="hidden" name="type" value="prepaid">
                                												<input class="form-control" required="" type="hidden" name="redirect" value="mobile_recharge">
                                											</div>
                            											</div>
                            										</div>
                        											<div class="form-group">
                        												<div class="row row-sm">
                        													<!--<div class="col-sm-9">-->
                        													    <div class="col-sm-9 mg-t-10 mg-sm-t-0">
                        														<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Recharge Amount</label>
                        														<input class="form-control" required="" type="text" name="amount" id="amount" placeholder="Amount">
                        													</div>
                        													<div class="col-sm-3 mg-t-20 mg-sm-t-0">
                        														<label class="main-content-label tx-11 tx-medium tx-gray-600"></label> 
                        														<a class="btn btn-main-primary btn-block" style="color:white;cursor: pointer;" onclick="showplan();">Plan</a>
                        														<!--<a class="modal-effect btn btn-main-primary btn-block" data-effect="effect-scale" data-toggle="modal" href="#modaldemo8">Plan</a>-->
                        													</div>
                        												</div>
                        											</div>
                        											<button type="submit" class="btn btn-main-primary btn-block">Proceed To Recharge</button>
                        										</div>
                        									</div>
								                         </div>
								                         </form>
													</div>
													<div class="tab-pane" id="tab2">
													    <form action="<?php echo base_url();?>/Retailer/recharge_ot" method="post">
														    <div class="row">
									                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        											<h5 class="card-title mg-b-20">Your Postpaid Details</h5>
                        											<div class="row">
                            											<div class="col-md-12 col-lg-12 col-xl-12">
                                											<div class="form-group">
                                											    <label class="main-content-label tx-11 tx-medium tx-gray-600">Select Operator</label><br>
                                											    <div class="row">
                                											        <?php if($operator){foreach($operator as $op){ if($op->opsertype=='postpaid'){?>
                                											        <div class="col-md-2 col-lg-2 col-xl-2">
                                        											    <label class="rdiobox"><input type="radio" name="operator" value="<?php echo $op->opcodenew;?>" required=""> 
                                        											    <span>
                                        											        <div for="Airtel" class="main-profile-menu" style="width: 50px;height: 50px;border-radius: 50%;">
                                        											            <img alt="" src="<?php echo base_url().'/assets/'.$op->operator_image;?>">
                                        											            <label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $op->operatorname;?></label><br>
                                        											        </div>
                                        											    </span>
                                        											    </label>
                                    											    </div>
                                    											    <?php }}}?>
                                											    </div>
                                											</div>
                            											</div>
                                									</div>
                        											<div class="row">
                            											<div class="col-md-12 col-lg-12 col-xl-12">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Postpaid Mobile Number</label> 
                                												<input class="form-control" id="prepaid_mobile" required="" minlength="10" name="number" maxlength="10" placeholder="Mobile Number" type="text">
                                												<input class="form-control" required="" type="hidden" name="type" value="postpaid">
                                												<input class="form-control" required="" type="hidden" name="redirect" value="mobile_recharge">
                                											</div>
                            											</div>
                            											</div>
                        											<div class="form-group">
                        												<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Recharge Amount</label>
                        												<div class="pos-relative">
                        													<input class="form-control pd-r-80" required="" name="amount" type="text" placeholder="Amount">
                        												</div>
                        											</div>
                        											<button type="submit" class="btn btn-main-primary btn-block">Proceed To Recharge</button>
                        										</div>
                        									</div>
								                         </div>
								                         </form>
													</div>
												</div>
											</div>
										</div>
									</div>
							</div>
						</div>
					</div>	
				</div>
				<!-- / main-content-body -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->

		<div id="plandts"></div>
 		
		<script>
		    function showplan()
              {
              	var prepaid_mobile = $('#prepaid_mobile').val();
              	var ele = document.getElementsByName('operator'); 
                var opcodeid;
                for(i = 0; i < ele.length; i++) { if(ele[i].checked) opcodeid=ele[i].value; }
                        
                if($.isNumeric(opcodeid) && prepaid_mobile !== '')
              	{
              		$.ajax({
                      url: '<?php echo base_url(); ?>retailer/roffer',
                      type: 'POST',
                      dataType: 'html',        
                      cache: false,       
                      data: {prepaid_mobile:prepaid_mobile,opcodeid:opcodeid},
                      success: function (response) {
                          $("#plandts").html(response);
                          $('#modaldemo3').modal('toggle');
                              
                            //     $('#modaldemo3').modal('show');
                            },
                    error: function (error_) {
                                MYLOG(error_);
                            }
                      
                    });	
              	}
              	else
              	{
              		alert('Please choose operator / Enter Mobile Number');
              	}
              }
		</script>
		<script type="text/javascript">  
            function MyAlert(id)  
            {        
              var text = id;
              $('#myModal').modal('toggle');
              $("#amount").val( text );
              $('#myModal').hide();
            }  
        </script> 
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>

</html>