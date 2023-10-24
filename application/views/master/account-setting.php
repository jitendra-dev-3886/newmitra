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
						<h4 class="content-title mb-2">ACCOUNT SETTING</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page">CHANGE PASSWORD / CHANGE PIN</li>
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
				
                <div class="row row-sm">
                    <div class="col-lg-6">
                        
						<div class="card">
							<div class="card-body">
								<form action="<?php echo base_url();?>master/password_and_pin" method="post" enctype="multipart/form-data">
            					   	<div class="row">	
            						    <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                							    <div class="mb-4 main-content-label"> Change Password </div>
                						    </div>
            							</div>
            						</div>
            						<div class="row">	
            						     <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                						        <label class="main-content-label tx-11 tx-medium tx-gray-600">Current Password</label> 
                							    <input class="form-control" required="" name="old_password" id="old_password" placeholder="Current Password"  type="text">
                                        		<div class="alert" role="alert" style="display:none;" id="wrong_password">
                                                    <strong style="color:red">Please Enter Correct Password...!!!</strong> 
                                                </div>			
                        					</div>
            							</div>
            						</div>
            						<div class="row">	
            						     <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                						        <label class="main-content-label tx-11 tx-medium tx-gray-600">New Password</label> 
            									<input class="form-control" required="" disabled name="new_password" id="new_password" placeholder="New Password" type="text">
                        					</div>
            							</div>
            						</div>
            						<div class="row">	
            						     <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                						        <label class="main-content-label tx-11 tx-medium tx-gray-600">Confirm Password</label> 
                							    <input class="form-control" required="" disabled name="confirm_password" id="confirm_password" placeholder="Confirm Password" type="text">
                        					</div>
            							</div>
            						</div>
            						<button type="submit" name="update_password" id="update_password" disabled  class="btn btn-main-primary btn-block">Change Password</button>
            				</form>
							</div>
						</div>
                    </div>
					<!-- Col -->
					<div class="col-lg-6">
						<div class="card">
							<div class="card-body">
								<form action="<?php echo base_url();?>master/password_and_pin" method="post" enctype="multipart/form-data">
            					   	<div class="row">	
            						    <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                							    <div class="mb-4 main-content-label"> Change Pin </div>
                						    </div>
            							</div>
            						</div>
            						<div class="row">	
            						     <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                						        <label class="main-content-label tx-11 tx-medium tx-gray-600">Current Pin</label> 
                							    <input class="form-control" required="" name="old_pin" id="old_pin" placeholder="Current Pin"  type="text">
                							    <div class="alert" role="alert" style="display:none;" id="wrong_pin">
                                                    <strong style="color:red">Please Enter Correct Pin...!!!</strong> 
                                                </div>
                        					</div>
            							</div>
            						</div>
            						<div class="row">	
            						     <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                						        <label class="main-content-label tx-11 tx-medium tx-gray-600">New Pin</label> 
            									<input class="form-control" required="" disabled name="new_pin" id="new_pin" placeholder="New Pin" type="text">
                        					</div>
            							</div>
            						</div>
            						<div class="row">	
            						     <div class="col-md-12 col-lg-12 col-xl-12">
                						    <div class="form-group">
                						        <label class="main-content-label tx-11 tx-medium tx-gray-600">Confirm Pin</label> 
                							    <input class="form-control" required="" disabled name="confirm_pin" id="confirm_pin" placeholder="Confirm Pin" type="text">
                        					</div>
            							</div>
            						</div>
            						<button type="submit" name="update_pin" id="update_pin" disabled class="btn btn-main-primary btn-block">Change Pin</button>
            					</form>

							</div>
						</div>
					</div>
					<!-- /Col -->
					
					
				</div>

			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>
	
		<script>
        $(document).ready(function(){
          $("#old_password").focusout(function(){
            var old_password=$("#old_password").val();
            $.ajax({
      		        url: "<?php echo site_url();?>master/check_password",
          		    async: false,
          		    type: "POST",
          		    data: {old_password:old_password},
          		    dataType: "html",
          			success: function(data) {
          			    if(data==false){
                            $("#old_password").css("background-color", "#f8d7da");
                            $("#wrong_password").show();
                            $("#old_password").val('');
                            $('#new_password').prop('disabled', true);
                            $('#confirm_password').prop('disabled', true);
                        }
                        else{
                            $("#old_password").css("background-color", "#fff");
                            $("#wrong_password").hide();
                            $('#new_password').prop('disabled', false);
                            $('#confirm_password').prop('disabled', false);
                        }
              		},
      				error: function(xhr, status, error) {
                          alert(error);
                    }
      			}) 
           });
        });
        
        
        $(document).ready(function(){
          $("#new_password").focusout(function(){
              var new_password=$("#new_password").val();
              var confirm_password=$("#confirm_password").val();
              if(confirm_password!=''){
                  if(new_password!=confirm_password){
                      $("#new_password").css("background-color", "#f8d7da");
                      $('#update_password').prop('disabled', true);
                  }
                  else{
                      $("#new_password").css("background-color", "#fff");
                      $("#confirm_password").css("background-color", "#fff");
                      $('#update_password').prop('disabled', false);
                  }
              }
          });
        });
        
        $(document).ready(function(){
          $("#confirm_password").focusout(function(){
              var new_password=$("#new_password").val();
              var confirm_password=$("#confirm_password").val();
              if(new_password!=confirm_password){
                      $("#confirm_password").css("background-color", "#f8d7da");
                      $('#update_password').prop('disabled', true);
                  }
                  else{
                      $("#confirm_password").css("background-color", "#fff");
                      $("#new_password").css("background-color", "#fff");
                      $('#update_password').prop('disabled', false);
                  }
          });
        });
    </script>
    	<script>
        $(document).ready(function(){
          $("#old_pin").focusout(function(){
            var old_pin=$("#old_pin").val();
            $.ajax({
      		        url: "<?php echo site_url();?>master/check_pin",
          		    async: false,
          		    type: "POST",
          		    data: {old_pin:old_pin},
          		    dataType: "html",
          			success: function(data) {
          			    if(data==false){
                            $("#old_pin").css("background-color", "#f8d7da");
                            $("#wrong_pin").show();
                            $("#old_pin").val('');
                            $('#new_pin').prop('disabled', true);
                            $('#confirm_pin').prop('disabled', true);
                        }
                        else{
                            $("#old_pin").css("background-color", "#fff");
                            $("#wrong_pin").hide();
                            $('#new_pin').prop('disabled', false);
                            $('#confirm_pin').prop('disabled', false);
                        }
              		},
      				error: function(xhr, status, error) {
                          alert(error);
                    }
      			}) 
           });
        });
        
        
        $(document).ready(function(){
          $("#new_pin").focusout(function(){
              var new_pin=$("#new_pin").val();
              var confirm_pin=$("#confirm_pin").val();
              if(confirm_pin!=''){
                  if(new_pin!=confirm_pin){
                      $("#new_pin").css("background-color", "#f8d7da");
                      $("#confirm_pin").css("background-color", "#f8d7da");
                      $('#update_pin').prop('disabled', true);
                  }
                  else{
                      $("#new_pin").css("background-color", "#fff");
                      $("#confirm_pin").css("background-color", "#fff");
                      $('#update_pin').prop('disabled', false);
                  }
              }
          });
        });
        
        $(document).ready(function(){
          $("#confirm_pin").focusout(function(){
              var new_pin=$("#new_pin").val();
              var confirm_pin=$("#confirm_pin").val();
              if(new_pin!=confirm_pin){
                      $("#new_pin").css("background-color", "#f8d7da");
                      $("#confirm_pin").css("background-color", "#f8d7da");
                      $('#update_pin').prop('disabled', true);
                  }
                  else{
                      $("#new_pin").css("background-color", "#fff");
                      $("#confirm_pin").css("background-color", "#fff");
                      $('#update_pin').prop('disabled', false);
                  }
          });
        });
    </script>

</html>