<!DOCTYPE html>
<html lang="en">
    
    <style>
.show {display: block;}
</style>
    
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
								<li class="breadcrumb-item active" aria-current="page">Fastag</li>
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
									Fastag <br>
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
                        						    <!--<div class="col-md-3 col-lg-3 col-xl-3"></div>-->
                        						    <!--<div class="col-md-3 col-lg-3 col-xl-3">-->
                            						<!--					<div class="form-group">-->
                            						<!--						<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>-->
                            						<!--							<input class="form-control pd-r-80" required="" name="mobile" type="text" placeholder="Search Here">-->
                            						<!--					</div>-->
                        										<!--	</div>-->
                        										<!--	<div class="col-md-3 col-lg-3 col-xl-3">-->
                            						<!--					<div class="form-group">-->
                            						<!--						<label class="main-content-label tx-11 tx-medium tx-gray-600"></label>-->
                            						<!--						<button type="submit" name="search_find_report" class="btn btn-main-primary btn-block">Search</button>-->
                            						<!--					</div>-->
                        										<!--	</div>-->
                        										<!--	<div class="col-md-3 col-lg-3 col-xl-3"></div>-->
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            							    <label class="main-content-label tx-11 tx-medium tx-gray-600">Select operator</label>
                            								<select class="form-control select2" name="operator" required="" id="operator">
                        										<option label="Choose one"></option>
                        										<?php if($operator){foreach($operator as $op){ if($op->opsertype=='FASTAG'){?>
                        											<option value="<?php echo $op->opcodenew;?>"><?php echo $op->operatorname;?></option>
                        										<?php }}}?>
                        									</select>
                            							</div>
                        							</div>	
                        							<div class="col-md-6 col-lg-6 col-xl-6">
                            						    <div class="form-group">
                            								<label class="main-content-label tx-11 tx-medium tx-gray-600">Vechicle No.</label> 
                            								<input class="form-control" id="prepaid_mobile" required="" name="number" placeholder="Vechicle No." type="text">
                                    						<input class="form-control" required="" type="hidden" name="type" value="fastag">
                                    						<input class="form-control" required="" type="hidden" name="redirect" value="fastag">
                            							</div>
                        							</div>
                        						</div>
                        						<div class="row">
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                        						        <div class="form-group">
                            								<label class="main-content-label tx-11 tx-medium tx-gray-600">CUSTOMER NAME</label> 
                            								<input class="form-control" id="c_name" required="" name="c_name" placeholder="Customer Name" type="text">
                            							</div>
                        						    </div>
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                        						        <div class="form-group">
                            								<label class="main-content-label tx-11 tx-medium tx-gray-600">CUSTOMER MOBILE NO</label> 
                            								<input class="form-control" id="c_number" required="" name="c_number" placeholder="Cusomer Mobile Number" type="text">
                            							</div>
                        						    </div>
                        						</div>
                        						<div class="row">
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                        						        <div class="form-group">
                            								<label class="main-content-label tx-11 tx-medium tx-gray-600">Optional</label> 
                            								<input class="form-control" id="optional1" name="optional1" placeholder="BU ,city etc." type="text">
                            							</div>
                        						    </div>
                        						    
                        						</div>
                        						<div class="row">
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                        						        <div class="form-group">
                                							<label class="main-content-label tx-11 tx-medium tx-gray-600">Enter Recharge Amount</label>
                                							<div class="pos-relative">
                                								<input class="form-control" required="" type="text" name="amount" placeholder="Amount" id="amount">
                                							</div>
                                						</div>
                        						    </div>
                        						    <div class="col-md-6 col-lg-6 col-xl-6">
                        						        <label class="main-content-label tx-11 tx-medium tx-gray-600"></label> 
    													<a class="btn btn-main-primary btn-block" style="color:white;cursor: pointer;" onclick="showplan_new();">Bill Fetch</a>
                        						    </div>
                        						</div>
                        						
                        						<button type="submit" class="btn btn-main-primary btn-block">Proceed To Pay</button>
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
	   <?php $this->load->view('retailer/footer'); ?> 
	   
<script>
    function showplan_new()
          {
              
              
              	var prepaid_mobile = $('#prepaid_mobile').val();
              	  var opcodeid = $('#operator').val();
              	
            //   	alert(prepaid_mobile);
            //   	alert(opcodeid);
              	
              	if($.isNumeric(opcodeid) && prepaid_mobile !== '')
              	{
              		$.ajax({
                  url: '<?php echo base_url(); ?>Retailer/FastagInfo',
                  type: 'POST',
                  dataType: 'html',        
                  cache: false,       
                  data: {prepaid_mobile:prepaid_mobile,opcodeid:opcodeid},
                  success: function (response) {
                      var data = response.split(',');
                      var text = data[3];
                      var text1 = text.replace(/#/g, ",");
                          $("#c_name").val(data[0]);
                          $("#amount").val(data[1]);
                          $("#plandts").html(data[2]);
                          $("#res").val(text1);
                          
                          $('#modaldemo3').modal('toggle');
                          $('#modaldemo3').modal('show');
                          $("#operator").attr('readonly', true);
                          $("#prepaid_mobile").attr('readonly', true); 
                          $("#c_number").attr('readonly', true);
                          $("#bill_unit").attr('readonly', true);
                          var ss = $("#amt").html();
                        //     //alert(ss);
                             $('#amount').val(ss);
                        
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
	   

	
	</body>

</html>