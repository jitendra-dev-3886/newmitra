<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>
    <script src="../../assets/jquery-1.12.4.js"></script>
    <style>
        .nav-tabs .nav-link {
    background: linear-gradient(45deg, #e43364, #3858f9) !important;border-color: #2e50fb;
    border-width: 0;
    border-radius: 0;
    padding: 10px 15px;
    line-height: 1.428;
    color: white!important;
}
.nav-tabs .nav-link.active {
    font-weight: 800;
    width: 35%;
    text-align: center;
    letter-spacing: 0.9px;
    border-radius: .5rem;
}
.nav-tabs .nav-link + .nav-link {
    margin-left: 20px !important;
}
    </style>

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
						<div class="card custom-card" id="tab">
								<div class="card-body">
								<div class="main-content-label mg-b-5">
									AEPS  	
									<!--<?php echo $this->encryption_model->decode('421e44e2e831de39007fc91a99439f0c');?><br>-->
									<!--<?php echo $this->encryption_model->decode('9aaa7196e118852ecd11217921ebd1f7');?>-->
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
									<div class="text-wrap">
										<div class="example">
											<div class="border">
											<form role="form" action="<?php echo site_url('retailer/cashWithdrawal') ?>" method="post" enctype="multipart/form-data">
											    <div class="text-wrap">
											        <div class="example">
											            <div class="row" style="display:none">	
                                    						<div class="col-md-12 col-lg-12 col-xl-12">
                                    							  <div class="form-group">
                                    									<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Captured Data</label>
                                    									<textarea  class="form-control" id="txtPidData" rows="5" readonly name="txtPidData" required="required"></textarea>
                                    								    <textarea id="txtPidOptions" name="PidOptions" readonly style="width: 100%; height: 100px;" class="form-control"> </textarea>
                                    								    <textarea id="txtDeviceInfo" style="width: 100%; height: 160px;" class="form-control"> </textarea>
                                    								</div>
                                    						</div>
                                    					</div>
                                    					<div class="row">
                                    							<div class="col-md-3 col-lg-3 col-xl-3">
                                    							    <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Select Device *</label> 
                                    							    <select class="form-control" name="biomatric_type" >
                                    										<option value="Mantra">Mantra</option>
                                        									<option value="Mantra">Morpho</option>
                                    										
                                    									</select>
                                    							</div>
                                    							</div>
                                    							<div class="col-md-3 col-lg-3 col-xl-3">
                                    							    <div class="form-group">
                                                                        <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red"></label> 
                                    									<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" onclick="discoverAvdm();" type="button" style="padding: 8px 27px;font-size: 15px;margin-top:2%">Discover</button>
                                    								</div>
                                    							</div>
                                    							<div class="col-md-3 col-lg-3 col-xl-3">
                                    							    <div class="form-group">
                                    							        <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red"></label> 
                                    									<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" onclick="deviceInfoAvdm();" type="button" style="padding: 8px 27px;font-size: 15px;margin-top:2%">Device Info</button>
                                    								</div>
                                    							</div>
                                    							<div class="col-md-3 col-lg-3 col-xl-3">
                                    							    <div class="form-group">
                                    							        <label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red"></label> 
                                    									<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" onclick="CaptureAvdm();" type="button" style="padding: 8px 27px;font-size: 15px;margin-top:2%">Capture</button>
                                    								</div>
                                    							</div>
                                    						</div>
                                    				</div>	
                                    			</div>
												<div style="width: 80%;margin-top: 1%;margin-left: 2%;">
													<nav class="nav nav-tabs">
														<a class="nav-link active" data-toggle="tab" href="#tabCont1">Cash Withdrawal</a>
														<a class="nav-link" data-toggle="tab" href="#tabCont3">Balance Check</a>
														<a class="nav-link" data-toggle="tab" href="#tabCont4">Mini Statement</a>
														<a class="nav-link" data-toggle="tab" href="#tabCont2">Aadhar Payment</a>
													</nav>
												</div>
												<div class="card-body tab-content">
												
													<div class="tab-pane active show" id="tabCont1">
													    <div class="row" style="display: contents;">
                                                            <div class="col-md-12 col-lg-12 col-xl-12"><br>
                                                                <h5 class="card-title mg-b-20">Cash Withdrawal:</h5>
                                                            </div>
                                                        </div>
                                                        <div style="display:none;">
                                    						    <input id="txtName" type="text" />
                                    						    
                                    						    <select id="drpMatchValuePI" class="form-control"></select>
                                    						     <input name="RDPI" id="rdExactPI" checked="true" type="radio">Exact</input>
                                                                <input name="RDPI" id="rdPartialPI" type="radio">Partial</input>
                                                                <input name="RDPI" id="rdFuzzyPI" type="radio">Fuzzy</input><input id="txtState" type="text" />
                                                                <input id="txtAge" type="text" /><input id="txtLandMark" type="text" />
                                                                <input id="txtLocalNamePI" type="text" /><input id="txtStreet" type="text" />
                                                                <select id="drpLocalMatchValuePI"></select><input id="txtSubDist" type="text" />
                                                                <input id="txtDOB" type="text" /><input id="txtLocality" type="text" />
                                                                <select id="drpGender" class="form-control"></select><input id="txtDist" type="text" />
                                                                <input id="txtPhone" type="text" /> <input id="txtPOName" type="text" />
                                                                <input id="txtEmail" type="text" /><input id="txtCity" type="text" />
                                                                <select id="drpDOBType" class="form-control"> </select> <input id="txtPinCode" type="text" />
                                                                <select id="drpMatchValuePFA"></select>
                                                                <select id="drpLocalMatchValue"></select>
                                                                <textarea id="txtAddressValue" style="width: 100%; height: 50px;" class="form-control"></textarea>
                                                                <textarea id="txtLocalAddress" style="width: 100%; height: 50px;" class="form-control"></textarea>
                                                                <input id="rdMatchStrategyPA" checked="true" type="radio">Exact</input>
                                                                <input name="RD" id="rdExactPFA" checked="true" type="radio">Exact</input>
                                                                <input name="RD" id="rdPartialPFA" type="radio">Partial </input>
                                                                <input name="RD" id="rdFuzzyPFA" type="radio">Fuzzy</input>
                                                                <input id="txtCareOf" type="text" /><input id="txtBuilding" type="text" />
                                    						    <select id="Timeout" class="form-control"><option>15000</option></select>
                                    						    <select id="Pidver" class="form-control"><option>2.0</option></select>
                                    						    <select id="Env" class="form-control"><option>P</option></select>
                                    						    <select id="Dtype" style="width: 45px;" class="form-control"><option value="0">X</option></select>
                                    						    <select id="Fcount" class="form-control"><option selected="selected">1</option></select>
                                    						    <select id="Ftype" class="form-control"><option value="0">FMR</option></select>
                                    						     <select id="Icount" class="form-control"><option>0</option></select>
                                    						     <select id="Itype" class="form-control"><option>ISO</option></select>
                                    						     <select id="Pcount" class="form-control"><option>0</option></select>
                                    						      <select id="Ptype" class="form-control"><option>SELECT</option></select>
                                    						      <select id="ddlAVDM" class="form-control" style="width: 100%;"><option></option></select>
                                    						      <textarea id="txtWadh" style="width: 100%; height: 50px;" class="form-control"> </textarea>
                                    						</div>
                                                        
                                                            <div class="row">	
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Aadhar Number *</label> 
                                    								<input class="form-control" name="cwadhaarNumber" minlength="12" maxlength="12" placeholder="Enter Aadhar Number" type="text" >
                                    							</div>
                                							</div>
                                							
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Select Bank *</label> 
                                    							    <select class="form-control" name="cwnationalBankIdenticationNumber" >
                                    										<option label="Choose one"></option>
                                    										<?php if($aeps_bank){foreach($aeps_bank as $bank){ ?>
                                        									<option value="<?php echo $bank->iinno;?>"><?php echo $bank->bankName;?></option>
                                    										<?php }}?>
                                    									</select>
                                    							</div>
                                							</div>
                                						</div>
                                                        	<div class="row">	
                                    							<div class="col-md-6 col-lg-6 col-xl-6">
                        									        <div class="form-group">
                                        								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label"  style="color:red">Mobile Number *</label> 
                                        								<input class="form-control" name="cwmobileNumber" minlength="10" maxlength="10" placeholder="Enter Mobile Number" type="text" >
                                        							</div>
                                    							</div>
                                    							<div class="col-md-6 col-lg-6 col-xl-6">
                                    							    <div class="form-group">
                                    									<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Amount *</label>
                                    									<input class="form-control" name="cwtransactionAmount" placeholder="Enter Amount" type="text" >
                                    								</div>
                                    							</div>
                                    						</div>
                                    						<div class="row">	
                                    							<div class="col-md-12 col-lg-12 col-xl-12">
                                    							    <div class="form-group">
                                    									<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Remark</label>
                                    									<textarea class="form-control" name="cwrequestRemarks"></textarea>
                                    								</div>
                                    							</div>
                                    						</div>
                                    					<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" name="cashwithdrawal" type="submit" style="padding: 8px 27px;font-size: 15px;">Submit</button>
                        							</div>
													<div class="tab-pane" id="tabCont2">
													    <div class="row" style="display: contents;">
                                                            <div class="col-md-12 col-lg-12 col-xl-12"><br>
                                                                <h5 class="card-title mg-b-20">Aadhar Payment:</h5>
                                                            </div>
                                                        </div>
                                                            <div class="row">	
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Aadhar Number *</label> 
                                    								<input class="form-control" name="apadhaarNumber" minlength="12" maxlength="12" placeholder="Enter Aadhar Number" type="text" >
                                    							</div>
                                							</div>
                                							
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Select Bank *</label> 
                                    							    <select class="form-control" name="apnationalBankIdenticationNumber" >
                                    										<option label="Choose one"></option>
                                    										<?php if($aeps_bank){foreach($aeps_bank as $bank){ ?>
                                        									<option value="<?php echo $bank->iinno;?>"><?php echo $bank->bankName;?></option>
                                    										<?php }}?>
                                    									</select>
                                    							</div>
                                							</div>
                                						</div>
                                                        	<div class="row">	
                                    							<div class="col-md-6 col-lg-6 col-xl-6">
                        									        <div class="form-group">
                                        								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label"  style="color:red">Mobile Number *</label> 
                                        								<input class="form-control" name="apmobileNumber" minlength="10" maxlength="10" placeholder="Enter Mobile Number" type="text" >
                                        							</div>
                                    							</div>
                                    							<div class="col-md-6 col-lg-6 col-xl-6">
                                    							    <div class="form-group">
                                    									<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Amount *</label>
                                    									<input class="form-control" name="aptransactionAmount" placeholder="Enter Amount" type="text" >
                                    								</div>
                                    							</div>
                                    						</div>
                                    						<div class="row">	
                                    							<div class="col-md-12 col-lg-12 col-xl-12">
                                    							    <div class="form-group">
                                    									<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label">Remark</label>
                                    									<textarea class="form-control" name="aprequestRemarks"></textarea>
                                    								</div>
                                    							</div>
                                    						</div>
                                    						<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" name="adharpayment" type="submit" style="padding: 8px 27px;font-size: 15px;">Submit</button>
                        							</div>
													<div class="tab-pane" id="tabCont3">
													    <div class="row" style="display: contents;">
                                                            <div class="col-md-12 col-lg-12 col-xl-12"><br>
                                                                <h5 class="card-title mg-b-20">Balance Check:</h5>
                                                            </div>
                                                        </div>
                                                            <div class="row">	
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Aadhar Number *</label> 
                                    								<input class="form-control" name="bcadhaarNumber" minlength="12" maxlength="12" placeholder="Enter Aadhar Number" type="text" >
                                    							</div>
                                							</div>
                                							
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Select Bank *</label> 
                                    							    <select class="form-control" name="bcnationalBankIdenticationNumber" >
                                    										<option label="Choose one"></option>
                                    										<?php if($aeps_bank){foreach($aeps_bank as $bank){ ?>
                                        									<option value="<?php echo $bank->iinno;?>"><?php echo $bank->bankName;?></option>
                                    										<?php }}?>
                                    									</select>
                                    							</div>
                                							</div>
                                						</div>
                                                        	<div class="row">	
                                    							<div class="col-md-6 col-lg-6 col-xl-6">
                        									        <div class="form-group">
                                        								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label"  style="color:red">Mobile Number *</label> 
                                        								<input class="form-control" name="bcmobileNumber" minlength="10" maxlength="10" placeholder="Enter Mobile Number" type="text" >
                                        							</div>
                                    							</div>
                                    						</div>
                                    						<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" name="balancecheck" type="submit" style="padding: 8px 27px;font-size: 15px;">Submit</button>
                        							</div>
													<div class="tab-pane" id="tabCont4">
													    <div class="row" style="display: contents;">
                                                            <div class="col-md-12 col-lg-12 col-xl-12"><br>
                                                                <h5 class="card-title mg-b-20">Mini Statement:</h5>
                                                            </div>
                                                        </div>
                                                           <div class="row">	
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Aadhar Number *</label> 
                                    								<input class="form-control" name="msadhaarNumber" minlength="12" maxlength="12" placeholder="Enter Aadhar Number" type="text" >
                                    							</div>
                                							</div>
                                							
                                							<div class="col-md-6 col-lg-6 col-xl-6">
                    									        <div class="form-group">
                                    								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label" style="color:red">Select Bank *</label> 
                                    							    <select class="form-control" name="msnationalBankIdenticationNumber" >
                                    										<option label="Choose one"></option>
                                    										<?php if($aeps_bank){foreach($aeps_bank as $bank){ ?>
                                        									<option value="<?php echo $bank->iinno;?>"><?php echo $bank->bankName;?></option>
                                    										<?php }}?>
                                    									</select>
                                    							</div>
                                							</div>
                                						</div>
                                                        	<div class="row">	
                                    							<div class="col-md-6 col-lg-6 col-xl-6">
                        									        <div class="form-group">
                                        								<label class="main-content-label tx-11 tx-medium tx-gray-600 control-label"  style="color:red">Mobile Number *</label> 
                                        								<input class="form-control" name="msmobileNumber" minlength="10" maxlength="10" placeholder="Enter Mobile Number" type="text" >
                                        							</div>
                                    							</div>
                                    						</div>
                                    					<button class="btn btn-main-primary btn-block nextBtn btn-lg pull-right" name="ministetement" type="submit" style="padding: 8px 27px;font-size: 15px;">Submit</button>
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
				<!-- / main-content-body -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
		
	<!--<script src="aeps_script.js"></script>	-->
	<script language="javascript" type="text/javascript">

	   var GetPIString='';
    	var GetPAString='';
    	var GetPFAString='';
    	var DemoFinalString='';
    	var select = '';
    	select += '<option val=0>Select</option>';
    	for (i=1;i<=100;i++){
    		select += '<option val=' + i + '>' + i + '</option>';
    	}
    	$('#drpMatchValuePI').html(select);
    	$('#drpMatchValuePFA').html(select);
    	$('#drpLocalMatchValue').html(select);
    	$('#drpLocalMatchValuePI').html(select);
    
    	var finalUrl="";
    	var MethodInfo="";
    	var MethodCapture="";
    	var OldPort=false;
    
    
    	function test()
    	{
    		alert("I am calling..");
    	}
    
    	function reset()
    	{
    		$('#txtWadh').val('');
    	    $('#txtDeviceInfo').val('');
    		$('#txtPidOptions').val('');
    		$('#txtPidData').val('');
    	    $("select#ddlAVDM").prop('selectedIndex', 0);
    	    $("select#Timeout").prop('selectedIndex', 0);
    		$("select#Icount").prop('selectedIndex', 0);
    		$("select#Fcount").prop('selectedIndex', 0);
    		$("select#Icount").prop('selectedIndex', 0);
    		$("select#Itype").prop('selectedIndex', 0);
    		$("select#Ptype").prop('selectedIndex', 0);
    		$("select#Ftype").prop('selectedIndex', 0);
    		$("select#Dtype").prop('selectedIndex', 0);
    	}
    	// All New Function
    
    	function Demo()
    	{
    
    	var GetPIStringstr='';
    	var GetPAStringstr='';
    	var GetPFAStringstr='';
    
    		if(GetPI()==true)
    		{
    			GetPIStringstr ='<Pi '+GetPIString+' />';
    			//alert(GetPIStringstr);
    		}
    		else
    		{
    			GetPIString='';
    		}
    
    		if(GetPA()==true)
    		{
    			GetPAStringstr ='<Pa '+GetPAString+' />';
    			//alert(GetPAStringstr);
    		}
    		else
    		{
    			GetPAString='';
    		}
    
    		if(GetPFA()==true)
    		{
    			GetPFAStringstr ='<Pfa '+GetPFAString+' />';
    			//alert(GetPFAStringstr);
    		}
    		else
    		{
    			GetPFAString='';
    		}
    
    		if(GetPI()==false && GetPA()==false && GetPFA()==false)
    		{
    			//alert("Fill Data!");
    			DemoFinalString='';
    		}
    		else
    		{
    			DemoFinalString = '<Demo>'+ GetPIStringstr +' ' + GetPAStringstr + ' ' + GetPFAStringstr + ' </Demo>';
    			//alert(DemoFinalString)
    		}
    	}
    
    	function GetPI()
    	{
    		var Flag=false;
    		GetPIString='';
    
    		 if ($("#txtName").val().trim().length > 0)
            {
                Flag = true;
                GetPIString += "name="+ "\""+$("#txtName").val().trim()+"\"";
            }
    
            if ($("#drpMatchValuePI").val() > 0 && Flag)
            {
                Flag = true;
    			GetPIString += " mv="+ "\""+$("#drpMatchValuePI").val().trim()+"\"";
            }
    
    		if ($('#rdExactPI').is(':checked') && Flag)
            {
                Flag = true;
                GetPIString += " ms="+ "\"E\"";
            }
            else if ($('#rdPartialPI').is(':checked') && Flag)
            {
                Flag = true;
               GetPIString += " ms="+ "\"P\"";
            }
            else if ($('#rdFuzzyPI').is(':checked') && Flag)
            {
                Flag = true;
                GetPIString += " ms="+ "\"F\"";
            }
    		if ($("#txtLocalNamePI").val().trim().length > 0)
            {
    			Flag = true;
                GetPIString += " lname="+ "\""+$("#txtLocalNamePI").val().trim()+"\"";
            }
    
    		if ($("#txtLocalNamePI").val().trim().length > 0 && $("#drpLocalMatchValuePI").val() > 0)
            {
    			Flag = true;
    			GetPIString += " lmv="+ "\""+$("#drpLocalMatchValuePI").val().trim()+"\"";
            }
    
    		//alert(GetPIString);
    		return Flag;
    	}
    
    
    	function GetPA()
    	{
    		var Flag=false;
    		GetPAString='';
    
    		if ($("#txtCareOf").val().trim().length > 0)
            {
    			Flag = true;
                GetPAString += "co="+ "\""+$("#txtCareOf").val().trim()+"\"";
            }
            if ($("#txtLandMark").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " lm="+ "\""+$("#txtLandMark").val().trim()+"\"";
            }
            if ($("#txtLocality").val().trim().length > 0 )
            {
               Flag = true;
                GetPAString += " loc="+ "\""+$("#txtLocality").val().trim()+"\"";
            }
            if ($("#txtCity").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " vtc="+ "\""+$("#txtCity").val().trim()+"\"";
            }
            if ($("#txtDist").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " dist="+ "\""+$("#txtDist").val().trim()+"\"";
            }
            if ($("#txtPinCode").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " pc="+ "\""+$("#txtPinCode").val().trim()+"\"";
            }
            if ($("#txtBuilding").val().trim().length > 0 )
            {
                 Flag = true;
                GetPAString += " house="+ "\""+$("#txtBuilding").val().trim()+"\"";
            }
            if ($("#txtStreet").val().trim().length > 0 )
            {
                 Flag = true;
                GetPAString += " street="+ "\""+$("#txtStreet").val().trim()+"\"";
            }
            if ($("#txtPOName").val().trim().length > 0 )
            {
                 Flag = true;
                GetPAString += " po="+ "\""+$("#txtPOName").val().trim()+"\"";
            }
            if ($("#txtSubDist").val().trim().length > 0 )
            {
                  Flag = true;
                GetPAString += " subdist="+ "\""+$("#txtSubDist").val().trim()+"\"";
            }
            if ($("#txtState").val().trim().length > 0)
            {
                 Flag = true;
                GetPAString += " state="+ "\""+$("#txtState").val().trim()+"\"";
            }
            if ( $('#rdMatchStrategyPA').is(':checked') && Flag)
            {
                Flag = true;
                GetPAString += " ms="+ "\"E\"";
            }
    		//alert(GetPIString);
    		return Flag;
    	}
    
    	function GetPFA()
    	{
    		var Flag=false;
    		GetPFAString='';
    
    		if ($("#txtAddressValue").val().trim().length > 0)
            {
    			Flag = true;
                GetPFAString += "av="+ "\""+$("#txtAddressValue").val().trim()+"\"";
            }
    
    		if ($("#drpMatchValuePFA").val() > 0 && $("#txtAddressValue").val().trim().length > 0)
            {
                Flag = true;
    			GetPFAString += " mv="+ "\""+$("#drpMatchValuePFA").val().trim()+"\"";
            }
    
    		if ($('#rdExactPFA').is(':checked') && Flag)
            {
                Flag = true;
                GetPFAString += " ms="+ "\"E\"";
            }
            else if ($('#rdPartialPFA').is(':checked') && Flag)
            {
                Flag = true;
               GetPFAString += " ms="+ "\"P\"";
            }
            else if ($('#rdFuzzyPFA').is(':checked') && Flag)
            {
                Flag = true;
                GetPFAString += " ms="+ "\"F\"";
            }
    
    		if ($("#txtLocalAddress").val().trim().length > 0)
            {
    			Flag = true;
                GetPFAString += " lav="+ "\""+$("#txtLocalAddress").val().trim()+"\"";
            }
    
    		if ($("#drpLocalMatchValue").val() > 0 && $("#txtLocalAddress").val().trim().length > 0)
            {
                Flag = true;
    			GetPFAString += " lmv="+ "\""+$("#drpLocalMatchValue").val().trim()+"\"";
            }
    		//alert(GetPIString);
    		return Flag;
    	}
    
    	$( "#ddlAVDM" ).change(function() {
    	//alert($("#ddlAVDM").val());
    	discoverAvdmFirstNode($("#ddlAVDM").val());
    	});
    
    
    	$( "#chkHttpsPort" ).change(function() {
    	    if($("#chkHttpsPort").prop('checked')==true)
    	    {
    	        OldPort=true;
    	    }
    	    else
    	    {
    	        OldPort=false;
    	    }
    
    	});
    
    	function discoverAvdmFirstNode(PortNo)
    	{
    
    		$('#txtWadh').val('');
    	    $('#txtDeviceInfo').val('');
    		$('#txtPidOptions').val('');
    		$('#txtPidData').val('');
    
    	    //alert(PortNo);
    
    	    var primaryUrl = "http://127.0.0.1:";
            url = "";
    	    var verb = "RDSERVICE";
            var err = "";
    		var res;
    		$.support.cors = true;
    		var httpStaus = false;
    		var jsonstr="";
    		var data = new Object();
    		var obj = new Object();
    
    		$.ajax({
    			type: "RDSERVICE",
    			async: false,
    			crossDomain: true,
    			url: primaryUrl + PortNo,
    			contentType: "text/xml; charset=utf-8",
    			processData: false,
    			cache: false,
    			async:false,
    			crossDomain:true,
    			success: function (data) {
    				httpStaus = true;
    				res = { httpStaus: httpStaus, data: data };
    			    //alert(data);
    
    				//debugger;
    
    				 $("#txtDeviceInfo").val(data);
    
    				var $doc = $.parseXML(data);
    
    				//alert($($doc).find('Interface').eq(1).attr('path'));
    
    
    				if($($doc).find('Interface').eq(0).attr('path')=="/rd/capture")
    
    				{
    				  MethodCapture=$($doc).find('Interface').eq(0).attr('path');
    				}
    				if($($doc).find('Interface').eq(1).attr('path')=="/rd/capture")
    
    				{
    				  MethodCapture=$($doc).find('Interface').eq(1).attr('path');
    				}
    
    				if($($doc).find('Interface').eq(0).attr('path')=="/rd/info")
    
    				{
    				  MethodInfo=$($doc).find('Interface').eq(0).attr('path');
    				}
    				if($($doc).find('Interface').eq(1).attr('path')=="/rd/info")
    
    				{
    				  MethodInfo=$($doc).find('Interface').eq(1).attr('path');
    				}
    			
    				 alert("RDSERVICE Discover Successfully");
    			},
    			error: function (jqXHR, ajaxOptions, thrownError) {
    			$('#txtDeviceInfo').val("");
    			//alert(thrownError);
    				res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
    			},
    		});
    
    		return res;
    	}
    
    
    	function discoverAvdm()
    	{
    
    		openNav();
    		
    		$('#txtWadh').val('');
    	    $('#txtDeviceInfo').val('');
    		$('#txtPidOptions').val('');
    		$('#txtPidData').val('');
    
    		var SuccessFlag=0;
            var primaryUrl = "http://127.0.0.1:";
    
    		try {
    			 var protocol = window.location.href;
    			 if (protocol.indexOf("https") >= 0) {
    				primaryUrl = "http://127.0.0.1:";
    			}
    		 } catch (e){ }
    
            url = "";
    		$("#ddlAVDM").empty();
    		
    		//alert("Please wait while discovering port from 11100 to 11120.\nThis will take some time.");
    	    for (var i = 11100; i <= 11120; i++)
            {
    			if(primaryUrl=="https://127.0.0.1:" && OldPort==true)
    			{
    			   i="8005";
    			}
    		    $("#lblStatus1").text("Discovering RD service on port : " + i.toString());
    
    				var verb = "RDSERVICE";
                    var err = "";
    				SuccessFlag=0;
    				var res;
    				$.support.cors = true;
    				var httpStaus = false;
    				var jsonstr="";
    				var data = new Object();
    				var obj = new Object();
    
    				$.ajax({
    
    				type: "RDSERVICE",
    				async: false,
    				crossDomain: true,
    				url: primaryUrl + i.toString(),
    				contentType: "text/xml; charset=utf-8",
    				processData: false,
    				cache: false,
    				crossDomain:true,
    
    				success: function (data) {
    
    					httpStaus = true;
    					res = { httpStaus: httpStaus, data: data };
    				    //alert(data);
    					finalUrl = primaryUrl + i.toString();
    					var $doc = $.parseXML(data);
    					var CmbData1 =  $($doc).find('RDService').attr('status');
    					var CmbData2 =  $($doc).find('RDService').attr('info');
    					if(RegExp('\\b'+ 'Mantra' +'\\b').test(CmbData2)==true)
    					{
    					    closeNav();
    						$("#txtDeviceInfo").val(data);
    
    						if($($doc).find('Interface').eq(0).attr('path')=="/rd/capture")
    						{
    						  MethodCapture=$($doc).find('Interface').eq(0).attr('path');
    						}
    						if($($doc).find('Interface').eq(1).attr('path')=="/rd/capture")
    						{
    						  MethodCapture=$($doc).find('Interface').eq(1).attr('path');
    						}
    						if($($doc).find('Interface').eq(0).attr('path')=="/rd/info")
    						{
    						  MethodInfo=$($doc).find('Interface').eq(0).attr('path');
    						}
    						if($($doc).find('Interface').eq(1).attr('path')=="/rd/info")
    						{
    						  MethodInfo=$($doc).find('Interface').eq(1).attr('path');
    						}
    
    						$("#ddlAVDM").append('<option value='+i.toString()+'>(' + CmbData1 +')'+CmbData2+'</option>')
    						SuccessFlag=1;
    						alert("RDSERVICE Discover Successfully");
    						return;
    
    					}
    
    					//alert(CmbData1);
    					//alert(CmbData2);
    
    				},
    				error: function (jqXHR, ajaxOptions, thrownError) {
    				if(i=="8005" && OldPort==true)
    				{
    					OldPort=false;
    					i="11099";
    				}
    				$('#txtDeviceInfo').val("");
    				//alert(thrownError);
    
    					//res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
    				},
    
    			});
    
    			if(SuccessFlag==1)
    			{
    			  break;
    			}
    
    			//$("#ddlAVDM").val("0");
    
            }
    
    		if(SuccessFlag==0)
    		{
    		 alert("Connection failed Please try again.");
    		}
    
    		$("select#ddlAVDM").prop('selectedIndex', 0);
    
    		//$('#txtDeviceInfo').val(DataXML);
    
    		closeNav();
    		return res;
    	}
    
    
    	function openNav() {
    	}
    
    	function closeNav() {
    	}
    
    	function deviceInfoAvdm()
    	{
    		//alert($("#ddlAVDM").val());
            url = "";
    
    		//Dynamic URL
    
    		finalUrl = "https://127.0.0.1:" + $("#ddlAVDM").val();
    // 		alert(finalUrl);
    
    		try {
    			var protocol = window.location.href;
    			if (protocol.indexOf("https") >= 0) {
    				finalUrl = "https://127.0.0.1:" + $("#ddlAVDM").val();
    			}
    		} catch (e) { }
    
    				
    	    var verb = "DEVICEINFO";
            //alert(finalUrl);
            var err = "";
    		var res;
    		$.support.cors = true;
    		var httpStaus = false;
    		var jsonstr="";
    					
    		$.ajax({
    
    			type: "DEVICEINFO",
    			async: false,
    			crossDomain: true,
    			url: finalUrl+MethodInfo,
    			contentType: "text/xml; charset=utf-8",
    			processData: false,
    			success: function (data) {
    			//console.log(data);
    				httpStaus = true;
    				res = { httpStaus: httpStaus, data: data };
    
    				$('#txtDeviceInfo').val(data);
    			},
    			error: function (jqXHR, ajaxOptions, thrownError) {
    			alert(thrownError);
    				res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
    			},
    		});
    
    		return res;
    
    	}
    
    	function CaptureAvdm()
    	{
            // discoverAvdm();
            // deviceInfoAvdm();
    	   Demo();
    	   if($("#txtWadh").val().trim()!="")
    	   {
    		var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions ver="1.0"> <Opts fCount="'+$("#Fcount").val()+'" fType="'+$("#Ftype").val()+'" iCount="'+$("#Icount").val()+'" pCount="'+$("#Pcount").val()+'" format="0"   pidVer="'+$("#Pidver").val()+'" timeout="'+$("#Timeout").val()+'" wadh="'+$("#txtWadh").val()+'" posh="UNKNOWN" env="'+$("#Env").val()+'" /> '+DemoFinalString+'<CustOpts><Param name="mantrakey" value="'+$("#txtCK").val()+'" /></CustOpts> </PidOptions>';
    	   }
    	   else
    	   {
    		var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions ver="1.0"> <Opts fCount="'+$("#Fcount").val()+'" fType="'+$("#Ftype").val()+'" iCount="'+$("#Icount").val()+'" pCount="'+$("#Pcount").val()+'" format="0"   pidVer="'+$("#Pidver").val()+'" timeout="'+$("#Timeout").val()+'" posh="UNKNOWN" env="'+$("#Env").val()+'" /> '+DemoFinalString+'<CustOpts><Param name="mantrakey" value="'+$("#txtCK").val()+'" /></CustOpts> </PidOptions>';
    	   }
            //alert(XML);
    
    		var verb = "CAPTURE";
            var err = "";
    		var res;
    		$.support.cors = true;
    		var httpStaus = false;
    		var jsonstr="";
    
    		$.ajax({
    
    			type: "CAPTURE",
    			async: false,
    			crossDomain: true,
    			url: finalUrl+MethodCapture,
    			data:XML,
    			contentType: "text/xml; charset=utf-8",
    			processData: false,
    			success: function (data) {
    			//alert(data);
    				httpStaus = true;
    				res = { httpStaus: httpStaus, data: data };
    
    				$('#txtPidData').val(data);
    				$('#txtPidOptions').val(XML);
    
    				var $doc = $.parseXML(data);
    				var Message =  $($doc).find('Resp').attr('errInfo');
    
    				alert(Message);
    
    			},
    			error: function (jqXHR, ajaxOptions, thrownError) {
    			//$('#txtPidOptions').val(XML);
    			alert(thrownError);
    				res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
    			},
    		});
    
    		return res;
    	}
    		
    	function getHttpError(jqXHR) {
    	    var err = "Unhandled Exception";
    	    if (jqXHR.status === 0) {
    	        err = 'Service Unavailable';
    	    } else if (jqXHR.status == 404) {
    	        err = 'Requested page not found';
    	    } else if (jqXHR.status == 500) {
    	        err = 'Internal Server Error';
    	    } else if (thrownError === 'parsererror') {
    	        err = 'Requested JSON parse failed';
    	    } else if (thrownError === 'timeout') {
    	        err = 'Time out error';
    	    } else if (thrownError === 'abort') {
    	        err = 'Ajax request aborted';
    	    } else {
    	        err = 'Unhandled Error';
    	    }
    	    return err;
    	}
    </script>
	   <?php $this->load->view('retailer/footer'); ?> 
	</body>
	
</html>