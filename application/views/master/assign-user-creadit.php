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
						<h4 class="content-title mb-2">Wallet Management</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($this->uri->segment('2')); ?></li>
							</ol>
						</nav>
					</div>

				</div>
				<!-- /breadcrumb -->
									<!-- row -->
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
							    <div class="main-content-label mg-b-5">
									Assign Users Credits
								</div>
								<p class="mg-b-20">Credit Wallet to Existing Member.</p>
								<form action="<?php echo site_url('master/assign_credits') ?>" method="post" data-parsley-validate="">
									<div class="row row-sm">
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Available Credits: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-weather-snow tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" value="<?php foreach($credits as $c){echo $c->available_count;}?>" name="mastercreditbalance" id="mastercredits" placeholder="0" readonly required="" type="text">
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Member: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-user-outline tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<select class="form-control" name="cus_id" id="members" onchange="getMemberBalance(this)">
    												    <option value="">Select Distributor</option>
    												    <?php foreach($dist as $d){?>
    												    <option value="<?php echo $d->cus_id;?>"><?php echo $d->cus_name; ?></option>
    												    <?php }?>
    												</select>
    											</div>
											</div>
									    </div>
									    <div class="col-6">
									        <div class="form-group">
												<label class="form-label">Credits Balance: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-briefcase tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="creditbalance" id="wallet" placeholder="0" readonly required="" type="text">
    											</div>
											</div>
									    </div>
										<div class="col-6">
											<div class="form-group">
												<label class="form-label">Enter Credits: <span class="tx-danger">*</span></label>
												<div class="input-group">
    												<div class="input-group-prepend">
            											<div class="input-group-text">
            												<i class="typcn typcn-database tx-24 lh--9 op-6"></i>
            											</div>
            										</div>
    												<input class="form-control" name="user_count" id="creditsamt"  placeholder="Enter Credit" required="" type="number">
    											</div>
											</div>
										</div>
										
										<div class="col-12" style="margin-top:2%"><button class="btn btn-main-primary pd-x-20 mg-t-10" type="submit">Submit</button></div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>
	
	<script>
	    $("#creditsamt").keyup(function(event) {
	        var credit=$("#creditsamt").val();
	        var mastercredits=$("#mastercredits").val();
	        if(Number(mastercredits)<Number(credit)){
	            alert('PLease Check Available Credits');
	            $("#creditsamt").val('0');
	        }
	        
	    });
	    
	    
	    var getMembers = cus_type =>{
	        var cus_type = cus_type.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url() ?>master/getMembersByType",
	            cache: "false",
	            data: {cus_type:cus_type},
	            success: function(res){
	                $("#members").empty();
	                $("#members").append(res);
	            }
	        });
	    }
	    
	    
	    var getMemberBalance = cus_id =>{
	        var cus_id = cus_id.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>master/getuser_creadit",
	            cache: false,
	            data: {cus_id:cus_id},
	            success: function(res){
	                $("#wallet").val(res);
	            }
	        });
	    }
	</script>

</html>