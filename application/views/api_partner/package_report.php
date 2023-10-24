<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('api_partner/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('api_partner/loader'); ?>

		<?php $this->load->view('api_partner/header'); ?>

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
								<li class="breadcrumb-item active" aria-current="page">My Package</li>
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
									<h4 class="card-title mg-b-2 mt-2">My Package</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
							    <div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap">
										<thead>
											<tr>
												<th class="border-bottom-0">Sr No.</th>
												<th class="border-bottom-0">Package For</th>
												<th class="border-bottom-0">View</th>
											</tr>
										</thead>
										<tbody>
										   <tr>
										       <td>1</td>
										       <td>Recharge</td>
										       <td><button class="btn btn-info" value="recharge"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
										   <tr>
										       <td>2</td>
										       <td>Aeps</td>
										       <td><button class="btn btn-info" value="aeps"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
										   <tr>
										       <td>3</td>
										       <td>DMT</td>
										       <td><button class="btn btn-info" value="dmt"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
										   <tr>
										       <td>4</td>
										       <td>Adhar Pay</td>
										       <td><button class="btn btn-info" value="adharpay"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
										   <tr>
										       <td>5</td>
										       <td>Micro Atm</td>
										       <td><button class="btn btn-info" value="microatm"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
										   <tr>
										       <td>6</td>
										       <td>Pancard</td>
										       <td><button class="btn btn-info" value="pancard"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
										   <tr>
										       <td>7</td>
										       <td>Payout</td>
										       <td><button class="btn btn-info" value="payout"  onclick="commission_for(this.value)" style="background:#B23E8F"><i class="fa fa-eye"></i></button></td>
										   </tr>
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
		
		
		<div class="modal" id="recModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 800px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<input type="text" class="form-control" id="texth" style="width:30%" readonly> <h6 class="modal-title" id="label"></h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="recharge">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		
		
		<script type="application/javascript">
            function commission_for(type_name)
        	{		
        	   //alert(type_name);
        	   //alert(sid);
        	    
                    $.ajax({
            			url: '<?php echo base_url(); ?>api_partner/getRechargePackage',
            			type: 'POST',
            			cache: false,
            			data: {type:type_name},
            			success: function(res) 
            			{
            			    if(res == 'NF'){
            			        alert("Package Not Assigned .Please Contact Admin.")
            			    }else{
            			        
            			        if(type_name == "recharge")
            			        $('#texth').val('RECHARGE COMMISSION');
            			        else if(type_name == "aeps")
            			        $('#texth').val('AEPS COMMISSION');
            			        else if(type_name == "dmt")
            			        $('#texth').val('DMT COMMISSION');
            			        else if(type_name == "adharpay")
            			        $('#texth').val('ADHARPAY COMMISSION');
            			        else if(type_name == "microatm")
            			        $('#texth').val('MICRO ATM COMMISSION');
            			        else if(type_name == "pancard")
            			        $('#texth').val('PANCARD COMMISSION');
            			        else if(type_name == "payout")
            			        $('#texth').val('PAYOUT COMMISSION');
            			       
            			        
            			        $("#recharge").empty();
            	                $("#recharge").append(res);
                			    $('#recModal').modal('show');
            			    }
        	                		
            			}
            		})
        		
        	}
    	</script>
        		
	
	   <?php $this->load->view('api_partner/footer'); ?> 
	
	</body>

</html>