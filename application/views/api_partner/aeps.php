<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('retailer/loader'); ?>

		<?php $this->load->view('retailer/header'); ?>
<style>
    body{ 
    margin-top:40px; 
}

.stepwizard-step p {
    margin-top: 10px;
}

.stepwizard-row {
    display: table-row;
}

.stepwizard {
    display: table;
    width: 100%;
    position: relative;
}

.stepwizard-step button[disabled] {
    opacity: 1 !important;
    filter: alpha(opacity=100) !important;
}

.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 100%;
    height: 5px;
    background: linear-gradient(to right, #0fca0f 11%, #ff0000 11%);
    z-order: 0;

}

.stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}

.btn-circle {
  width: 30px;
  height: 30px;
  text-align: center;
  padding: 6px 0;
  font-size: 12px;
  line-height: 1.428571429;
  border-radius: 15px;
}
</style>
    <script>
    function showPosition() {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var positionInfo = "Latitude: " + position.coords.latitude + ", " + "Longitude: " + position.coords.longitude;
                document.getElementById("latitude").value = position.coords.latitude;
                document.getElementById("longitude").value = position.coords.longitude;
            });
        } else {
           document.getElementById("lat").value = "Sorry, your browser does not support geolocation.";
        }
    }
</script>
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
									<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Hi, welcome back!</h4>
						
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">AEPS</li>
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
									AEPS
								</div>
								<?php if($this->session->flashdata('message')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('message');?></strong> 
                                </div>
                                <?php }?>
								<div class="row"><br></div>
								<div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
								        <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a href="#step-1" type="button" class="btn btn-primary btn-circle"></a>
                                                        <p>Step 1</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled></a>
                                                        <p>Step 2</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled></a>
                                                        <p>Step 3</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled></a>
                                                        <p>Step 4</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled></a>
                                                        <p>Step 5</p>
                                                    </div>
                                                </div>
                                            </div>
                        				</div>	
                        			</div>	
                        		</div>	
                        		<div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
								        <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        				    <form role="form" action="<?php echo site_url('Retailer/onboarding') ?>" method="post" enctype="multipart/form-data">
                                                <div class="row setup-content" id="step-1">
                								    <div class="row" style="display: contents;">
                                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                                            <h5 class="card-title mg-b-20">Contact Details:</h5><br>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Name *</label> 
                                								<input class="form-control" name="merchantName" placeholder="Enter Name" type="text" required="required" onfocusout="showPosition()">
                                							     <input type="hidden" id="longitude" class="form-control" name="longitude">
                                							     <input type="hidden" id="latitude" class="form-control" name="latitude">
                                							</div>
                            							</div>
                            							
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Mobile Number *</label> 
                                								<input readonly class="form-control" value="<?php echo $this->session->userdata('cus_mobile');?>" name="merchantPhoneNumber" minlength="10" maxlength="10" placeholder="Enter Mobile Number" type="number" required="required">
                                							</div>
                            							</div>
                            						</div>
                            						<div class="row" style="display: contents;">
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Email ID *</label> 
                                								<input readonly class="form-control" value="<?php echo $this->session->userdata('cus_email');?>" name="emailId" placeholder="Enter Email ID" type="email" required="required">
                                							</div>
                            							</div>
                            						</div>
                            						<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" type="button" style="padding: 8px 27px;font-size: 15px;">Next</button>
                								</div>
                								<div class="row setup-content" id="step-2">
                								    <div class="row" style="display: contents;">
                                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                                            <h5 class="card-title mg-b-20">Address:</h5><br>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Pin Code *</label> 
                                								<input class="form-control" name="merchantPinCode" minlength="6" maxlength="6" placeholder="Enter Pin code" type="text" required="required">
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Tan</label> 
                                								<input class="form-control" name="tan" placeholder="Enter Tan" type="text">
                                							</div>
                            							</div>
                            						</div>
                            						<div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">City *</label> 
                                								<input class="form-control" name="merchantCityName" placeholder="Enter City" type="text" required="required">
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">District *</label> 
                                								<input class="form-control" name="merchantDistrictName" placeholder="Enter District" type="text" required="required">
                                							</div>
                            							</div>
                            						</div>
                            						<div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label"  style="color:red">Address *</label> 
                                								<input class="form-control" name="merchantAddress" placeholder="Enter Address" type="text" required="required">
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                            							    <div class="form-group">
                            									<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">State *</label>
                            									<select class="form-control" name="merchantState" required="">
                            										<option label="Choose one"></option>
                            										<?php if($aeps_state){foreach($aeps_state as $state){ ?>
                                									<option value="<?php echo $state->stateId;?>"><?php echo $state->state;?></option>
                            										<?php }}?>
                            									</select>
                            								</div>
                            							</div>
                            						</div>
                            						<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" type="button" style="padding: 8px 27px;font-size: 15px;">Next</button>
                								</div>
                								<div class="row setup-content" id="step-3">
                								    <div class="row" style="display: contents;">
                                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                                            <h5 class="card-title mg-b-20">KYC Document:</h5><br>
                                                        </div>
                                                    </div>
                								    <div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Pan Number *</label> 
                                								<input class="form-control" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" name="userPan" placeholder="Enter Pan" type="text" required="required">
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Aadhar Number *</label> 
                                								<input class="form-control" name="aadhaarNumber" minlength="12" maxlength="12" placeholder="Enter Aadhar Number" type="text" required="required">
                                							</div>
                            							</div>
                            						</div>
                            						<div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">GST Number</label> 
                                								<input class="form-control" name="gstInNumber" placeholder="Enter GST Number" type="text" >
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Company Pan Number</label> 
                                								<input class="form-control" name="companyOrShopPan"  placeholder="Enter Company Pan Number" type="text">
                                							</div>
                            							</div>
                            						</div>
                            						<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" type="button" style="padding: 8px 27px;font-size: 15px;">Next</button>
                								</div>
                								<div class="row setup-content" id="step-4">
                								    <div class="row" style="display: contents;">
                                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                                            <h5 class="card-title mg-b-20">Bank Details:</h5><br>
                                                        </div>
                                                    </div>
                								    <div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Bank Account Number</label> 
                                								<input class="form-control" name="companyBankAccountNumber" placeholder="Enter Number" type="text" >
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">IFSC Code</label> 
                                								<input class="form-control" name="bankIfscCode" placeholder="Enter IFSC Code" type="text">
                                							</div>
                            							</div>
                            						</div>
                            						<div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Branch Name</label> 
                                								<input class="form-control" name="bankBranchName" placeholder="Enter Branch Name" type="text" >
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Account Name</label> 
                                								<input class="form-control" name="bankAccountName" placeholder="Enter Account Name" type="text">
                                							</div>
                            							</div>
                            						</div>
                            							<div class="row" style="display: contents;">	
                                						    <div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    							    <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Bank Name</label> 
                                    								<input class="form-control" name="companyBankName" placeholder="Enter Company Bank Name" type="text">
                                    							</div>
                                							</div>
                            							</div>
                            							<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" type="button"style="padding: 8px 27px;font-size: 15px;" >Next</button>
                							    </div>
                								<div class="row setup-content" id="step-5">
                                                    <div class="row" style="display: contents;">
                                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                                            <h5 class="card-title mg-b-20">Company Details:</h5><br>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="display: contents;">	
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                							    <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Company Legal Name</label> 
                                								<input class="form-control" name="companyLegalName" placeholder="Enter Company Legal Name" type="text">
                                							</div>
                            							</div>
                            							<div class="col-md-6 col-lg-6 col-xl-6">
                									        <div class="form-group">
                                							    <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Company Marketing Name</label> 
                                								<input class="form-control" name="companyMarketingName" placeholder="Enter Company Marketing Name" type="text">
                                							</div>
                            							</div>
                            							
                            						</div>
                            						<div class="row" style="display: contents;">	
                            							<div class="col-md-4 col-lg-4 col-xl-4">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Upload Passport Size Photo *</label> 
                                								 <input type="file" name="cancellationCheckImages" class="dropify" data-height="100" required="required"/>
                                							</div>
                            							</div>
                            							<div class="col-md-4 col-lg-4 col-xl-4">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Upload Pan Image *</label> 
                                								 <input type="file" name="shopAndPanImage" class="dropify" data-height="100" required="required"/>
                                							</div>
                            							</div>
                            							<div class="col-md-4 col-lg-4 col-xl-4">
                									        <div class="form-group">
                                								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Upload KYC Document *</label> 
                                								 <input type="file" name="ekycDocuments" class="dropify" data-height="100" required="required"/>
                                							</div>
                            							</div>
                            						</div>
                            					    <button class="btn btn-main-primary btn-block btn-lg pull-right" style="padding: 8px 27px;font-size: 15px;" type="submit">Finish!</button>
                							    </div>
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
	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>
	
<script type="text/javascript">
$(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url'],input[type='email'],input[type='file']"),
            isValid = true;
        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }
        
        if (isValid){
            if(curStepBtn=='step-1'){
                 $('head').append('<style>.stepwizard-row:before{background: linear-gradient(to right, #0fca0f 30%, #ff0000 30%) !important;}</style>');
            }
            if(curStepBtn=='step-2'){
                 $('head').append('<style>.stepwizard-row:before{background: linear-gradient(to right, #0fca0f 50%, #ff0000 50%) !important;}</style>');
            }
            if(curStepBtn=='step-3'){
                 $('head').append('<style>.stepwizard-row:before{background: linear-gradient(to right, #0fca0f 70%, #ff0000 70%) !important;}</style>');
            }
            if(curStepBtn=='step-4'){
                 $('head').append('<style>.stepwizard-row:before{background: linear-gradient(to right, #0fca0f 90%, #ff0000 90%) !important;}</style>');
            }
            nextStepWizard.removeAttr('disabled').trigger('click');
        }
            
    });
    
    $('div.setup-panel div a.btn-primary').trigger('click');
});



function resendotp(){
    $.ajax({
        type: "post",
        url: "<?php echo base_url()?>retailer/aepsotp",
        cache: false,
        success: function(res){
        	alert(res);
        }
    });
}
</script>
</html>