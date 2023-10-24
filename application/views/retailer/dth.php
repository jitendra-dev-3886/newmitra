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
								<li class="breadcrumb-item active" aria-current="page">DTH Recharge</li>
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
									DTH Recharge <br>
								</div>
								<?php if($this->session->flashdata('message')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('message');?></strong> 
                                </div>
                                <?php }?>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row"><br></div>
								<div class="row">
									<div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        	            <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        	                <form action="<?php echo base_url();?>/Retailer/recharge_ot" method="post">
                            				    <div class="row">
                                					<div class="col-md-12 col-lg-12 col-xl-12">
                                    				    <div class="form-group">
                                    						<label class="main-content-label tx-11 tx-medium tx-gray-600">Select Operator</label><br>
                                    						<div class="row">
                                								<?php if($operator){foreach($operator as $op){ if($op->opsertype=='dth'){?>
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
                            								<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter DTH Number</label> 
                            								<input class="form-control" id="prepaid_mobile" required="" name="number" placeholder="DTH Number" type="text">
                                							<input class="form-control" required="" type="hidden" name="type" value="dth">
                                							<input class="form-control" required="" type="hidden" name="redirect" value="dth">
                            							</div>
                            						</div>
                            					</div>
                            					<div class="form-group">
                            					    <div class="row row-sm">
                            					        <div class="col-sm-9 mg-t-10 mg-sm-t-0">
                                    						<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Recharge Amount</label>
                                    						<div class="pos-relative">
                                    							<input class="form-control" required="" type="text" name="amount" placeholder="Amount">
                                    						</div>
                                    					</div>
                                    					<div class="col-sm-3 mg-t-20 mg-sm-t-0">
    														<label class="main-content-label tx-11 tx-medium tx-gray-600"></label> 
    														<a class="btn btn-main-primary btn-block" style="color:white;cursor: pointer;" onclick="showplan();">DTH INFO</a>
    														<!--<a class="modal-effect btn btn-main-primary btn-block" data-effect="effect-scale" data-toggle="modal" href="#modaldemo8">Plan</a>-->
    													</div>
    												</div>
                            					</div>
                            					<button type="submit" class="btn btn-main-primary btn-block">Proceed To Recharge</button>
                            				</form>
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
                      url: '<?php echo base_url(); ?>retailer/dthInfo',
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
	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>

</html>