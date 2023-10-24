<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('admin/links'); ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <style>
         
        @media (min-width: 992px)
        .modal-lg, .modal-xl {
            max-width: 1100px; !important
        }
    </style>
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
						<h4 class="content-title mb-2">Commission Management</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($this->uri->segment('2')); ?></li>
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
									
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add New Package</button></div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">Package Name</th>
												<th class="wd-20p border-bottom-0">Status</th>
												<th class="wd-50p border-bottom-0" colspan ="">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($package)){$i=1;foreach($package as $pack){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><?php echo ucfirst($pack->scheme_name); ?></td>
												<td><?php if($pack->scheme_status=='active'){ ?>
												    <button class="btn btn-success" value="<?php echo $pack->scheme_status?>" onclick="updateStatus(this.value,<?php echo $pack->scheme_id ?>)"><?php echo ucfirst($pack->scheme_status); ?></button>
												    <?php }else{ ?>
												    <button class="btn btn-danger" value="<?php echo $pack->scheme_status?>" onclick="updateStatus(this.value,<?php echo $pack->scheme_id ?>)"><?php echo ucfirst($pack->scheme_status); ?></button>
												    <?php } ?>
												</td>
												<!--<td>
												<button class="btn btn-info"><i class="fa fa-edit"></i>&nbsp;EDIT</button></td>
												<td><button class="btn btn-info">&nbsp;View Commission</button></td>-->
												 <td><?php if($pack->scheme_status=='active') { ?>
												 
												    <select name="select" class="form-control" onchange="commission_for(this.value,<?php echo $pack->scheme_id ?>)">
												        <option value="">Select Type</option>
												        <option value="Recharge">Mobile Recharge</option>
												        <option value="Aeps">Aeps</option>
												        <!--<option value="Dmt">Dmt</option>-->
												        <!--<option value="PanCard">PanCard</option>-->
												        <!--<option value="MicroAtm">MicroAtm</option>-->
												        <option value="AdharPay">AdharPay</option>
												        <option value="Payout">Payout</option>
												        
												    </select>
												    
												    <?php }else{ ?>
												    <b>Not Available</b>
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
		<!-- /main-content -->
		
		<!-- Modal effects -->
		<div class="modal" id="modaldemo7">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">Add New Package</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<form action="<?php echo site_url('admin/add_commission_scheme'); ?>" data-parsley-validate="" method="post">
    					<div class="modal-body">
							<div class="row row-sm">
								    <div class="col-12">
								        <div class="form-group">
											<label class="form-label">PACKAGE NAME: <span class="tx-danger"></span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
        										<input class="form-control" placeholder="ENTER PACAKAGE NAME" type="text" required name="package_name">
											</div>
										</div>
								    </div>
								</div>
    					</div>
    					<div class="modal-footer">
    						<button type="submit" class="btn ripple btn-primary">Add</button>
    						<button class="btn ripple btn-danger" data-dismiss="modal" type="button">Discard</button>
    					</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="modal" id="RechargeModal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">Recharge Package</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<form action="<?php echo site_url('admin/add_commission_scheme'); ?>" data-parsley-validate="" method="post">
    					<div class="modal-body">
							<div class="row row-sm">
								    <div class="col-12">
								        <div class="form-group">
											<label class="form-label">PACKAGE NAME: <span class="tx-danger"></span></label>
											<div class="input-group">
												<div class="input-group-prepend">
        											<div class="input-group-text">
        												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
        											</div>
        										</div>
        										<input class="form-control" placeholder="ENTER PACAKAGE NAME" type="text" required name="package_name">
											</div>
										</div>
								    </div>
									
								</div>
    					</div>
    					<div class="modal-footer">
    						<button type="submit" class="btn ripple btn-primary">Add</button>
    						<button class="btn ripple btn-danger" data-dismiss="modal" type="button">Discard</button>
    					</div>
					</form>
				</div>
			</div>
		</div>
		
		
	    <div class="modal" id="recModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">RECHARGE COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
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
		
		
		<div class="modal" id="dmtModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">DMT COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="dmthere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal" id="aepsModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">AEPS COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="aepshere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="modal" id="pancardModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">PANCARD COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="pancardhere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal" id="microAtmModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">MICRO ATM COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="microAtmhere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="modal" id="adharPayModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">ADHAR PAY COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="adharPayhere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="modal" id="adharPayModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">ADHAR PAY COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="adharPayhere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="modal" id="payoutModal">
			<div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
				<div class="modal-content modal-content-demo">
					<div class="modal-header">
						<h6 class="modal-title">PAYOUT COMMISSION</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div id="payouthere">
						    
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- End Modal effects-->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
	<script type="application/javascript">
        function commission_for(type_name,sid)
    	{		
    	   //alert(type_name);
    	   //alert(sid);
    	   
    	   if(type_name == 'Recharge'){
                $.ajax({
        			url: '<?php echo base_url(); ?>master/getOperator',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#recharge").empty();
    	                $("#recharge").append(res);
        			    $('#recModal').modal('show');		
        			}
        		})
        		
    	   }else if(type_name == 'Aeps'){
    	       
    	       $.ajax({
        			url: '<?php echo base_url(); ?>master/getAepsComm',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#aepshere").empty();
    	                $("#aepshere").append(res);
        			    $('#aepsModal').modal('show');		
        			}
        		})
    	   }else if(type_name == 'Dmt'){
    	       
    	       $.ajax({
        			url: '<?php echo base_url(); ?>master/getDmtComm',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#dmthere").empty();
    	                $("#dmthere").append(res);
        			    $('#dmtModal').modal('show');		
        			}
        		})
    	   }else if(type_name == 'PanCard'){
    	       
    	       $.ajax({
        			url: '<?php echo base_url(); ?>master/getPancardComm',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#pancardhere").empty();
    	                $("#pancardhere").append(res);
        			    $('#pancardModal').modal('show');		
        			}
        		})
    	       
    	   }else if(type_name == 'MicroAtm'){
    	       
    	       $.ajax({
        			url: '<?php echo base_url(); ?>master/getMicroATMComm',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#microAtmhere").empty();
    	                $("#microAtmhere").append(res);
        			    $('#microAtmModal').modal('show');		
        			}
        		})
    	   }else if(type_name == 'AdharPay'){
    	       
    	       $.ajax({
        			url: '<?php echo base_url(); ?>master/getAdharPayComm',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#adharPayhere").empty();
    	                $("#adharPayhere").append(res);
        			    $('#adharPayModal').modal('show');		
        			}
        		})
    	   }else if(type_name == 'Payout'){
    	       
    	       $.ajax({
        			url: '<?php echo base_url(); ?>master/getPayoutComm',
        			type: 'POST',
        			cache: false,
        			data: {sid:sid},
        			success: function(res) 
        			{
    	                $("#payouthere").empty();
    	                $("#payouthere").append(res);
        			    $('#payoutModal').modal('show');		
        			}
        		})
    	   }	
    	}
    	
    	
    	function change(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/recharge-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	
    	function changeDmtComm(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/dmt-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	
    	function changeAepsComm(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/aeps-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	function changePancardComm(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/pancard-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	function changeMicroAtmComm(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/microatm-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	function changeAdharPayComm(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/adharpay-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	function changePayoutComm(new_comm,type,id){
    	   /*alert(type);
    	   alert(new_comm);
    	   alert(id);*/
    	   $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/payout-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,type:type,comm_id:id},
	            success: function(res){
	               alert('Updated Successfully');
	            }
	        });
    	}
    	
    	
    	
    </script>		
	
	<script>
	    
	    var updateCommission = (newCommission,oldCommission,packcommId) =>{
	        var new_comm = newCommission.value;
	        var old_comm = oldCommission;
	        var packcomm_id = packcommId;
	        $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>admin/package-commission-update",
	            cache: 'false',
	            data: {new_comm:new_comm,old_comm:old_comm,packcomm_id:packcomm_id},
	            success: function(res){
	                alert(res);
	            }
	        });
	    }
	    
	    
	    $(document).ready(function() {
            $('#example').DataTable();
        } );



	    $(document).ready(function() {
            $('#next').DataTable();
        } );
	</script>
	
	<script>
	   
	   function updateStatus(status,id){
	       /*
	       alert(id);
	       alert(status);*/
	       $.ajax({
	            type: 'POST',
	            url: "<?php echo base_url()?>master/updateStatus",
	            cache: 'false',
	            data: {id:id,status:status},
	            success: function(res){
	                window.location.reload();
	            }
	        });
	       
	   }
	    
	</script>

</html>