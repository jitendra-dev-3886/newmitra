<!DOCTYPE html>
<html lang="en">
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
		<meta name="Author" content="Spruko Technologies Private Limited">
		<meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>
		<!-- Title --> 
<title><?php echo strtoupper( $_SERVER['HTTP_HOST']); ?></title>
		<!--- Icons css -->
		<link href="../../assets/css/icons.css" rel="stylesheet">

		
		<!--- Right-sidemenu css -->
		<link href="../../assets/plugins/sidebar/sidebar.css" rel="stylesheet">

		<!--- Custom Scroll bar -->
		<link href="../../assets/plugins/mscrollbar/jquery.mCustomScrollbar.css" rel="stylesheet"/>

		<!--- Style css -->
		<link href="../../assets/css/style.css" rel="stylesheet">
		<link href="../../assets/css/skin-modes.css" rel="stylesheet">

		<!--- Animations css -->
		<link href="../../assets/css/animate.css" rel="stylesheet">
		
		<!--- Switcher css -->
		<link href="../../assets/switcher/css/switcher.css" rel="stylesheet">
		<link href="../../assets/switcher/demo.css" rel="stylesheet">
	   <script>
    function showPosition() {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var positionInfo = "Latitude: " + position.coords.latitude + ", " + "Longitude: " + position.coords.longitude;
                document.getElementById("lat").value = positionInfo;
            });
        } else {
           document.getElementById("lat").value = "Sorry, your browser does not support geolocation.";
        }
    }
</script>	
	</head>
	
	<body class="main-body">
	    <!-- Modal effects -->
            <div class="modal" id="successModal">
            	<div class="modal-dialog modal-dialog-centered" role="document">
            		<div class="modal-content tx-size-sm">
            			<div class="modal-body tx-center pd-y-20 pd-x-20">
            				<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button> <i class="icon ion-ios-checkmark-circle-outline tx-100 tx-success lh-1 mg-t-20 d-inline-block"></i>
            				<h4 class="tx-success tx-semibold mg-b-20">Success!</h4>
            				<p class="mg-b-20 mg-x-20"><?php echo $this->session->flashdata('success'); ?>.</p><button aria-label="Close" class="btn ripple btn-success pd-x-25" data-dismiss="modal" type="button">OK</button>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="modal" id="errorModal">
            	<div class="modal-dialog modal-dialog-centered" role="document">
            		<div class="modal-content tx-size-sm">
            			<div class="modal-body tx-center pd-y-20 pd-x-20">
            				<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button> <i class="icon icon ion-ios-close-circle-outline tx-100 tx-danger lh-1 mg-t-20 d-inline-block"></i>
            				<h4 class="tx-danger mg-b-20">Error!</h4>
            				<p class="mg-b-20 mg-x-20"><?php echo $this->session->flashdata('error'); ?></p><button aria-label="Close" class="btn ripple btn-danger pd-x-25" data-dismiss="modal" type="button">OK</button>
            			</div>
            		</div>
            	</div>
            </div>
            <?php 
            $successStatus = false;
            if(!empty($this->session->flashdata('success'))){
                $successStatus = true;
            }
            if($successStatus == true){ ?>
            
            <script type="text/javascript">
                window.onload = function() {
                    $('#successModal').modal('show');
                };
            </script>
            
            <?php } ?>
            <?php 
            $errorStatus = false;
            if(!empty($this->session->flashdata('error'))){
                $errorStatus=true;
            }
            if($errorStatus == true){ ?>
            
                <script type="text/javascript">
                    window.onload = function() {
                        $('#errorModal').modal('show');
                    };
                </script>
            
            <?php } ?>
	    <!-- Modal effects end-->
        <?php $this->load->view('retailer/loader'); ?>
   
    <!-- main-signin-wrapper -->
		<div class="my-auto page page-h">
			<div class="main-signin-wrapper" style="padding: 0px 20px;">
				<div class="main-card-signin d-md-flex wd-100p" style="max-width: 100% !important;">
				<div class="wd-md-50p login d-none d-md-block text-white" style="width: 65% !important;">
					<div class="my-auto authentication-pages">
						<div>
						    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
      <?php if($banner){$i=0;foreach($banner as $op){if($i==0){$i++;?>
    <div class="carousel-item active">
      <img class="d-block w-100" src="<?php echo $op->image;?>" alt="Second slide">
    </div>
    <?php }else{ ?>
    <div class="carousel-item">
      <img class="d-block w-100" src="<?php echo $op->image;?>" alt="Second slide">
    </div>
    <?php }}}?>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
						</div>
					</div>
				</div>
				<div class="p-5 wd-md-50p" style="width: 35% !important;padding: 70px 3rem !important;">
					<div class="main-signin-header">
						<img src="<?php base_url();?>/assets/logo.jpg?<?php echo rand(00000,99999); ?>" alt="Logo" style="padding-left: 20%;width: 60%;">
						<form method="post" action ="<?php echo base_url()?>api_partner/otp">
						    <div class="form-group">
								<label>Enter Otp</label><input class="form-control" name="otp" placeholder="OTP" required="required">
								
							</div>
							<div class="fxt-checkbox-area">
                                            <label for="checkbox1">Didn't Recieved OTP?</label>&nbsp;&nbsp;
                                            <a href="<?php echo base_url().'api_partner/resendotp'; ?> " class="switcher-text">Resend OTP</a>
                                        </div>
							<button class="btn btn-main-primary btn-block" type="submit" name="checkotp">Verify OTP</button>
						</form>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="row">
		    <div class="col-md-2 col-lg-2 col-sm-2" style="padding-left: 7%;"><h5 style="margin-top: 12%;">Powerd By :</h5></div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="<?php echo base_url();?>assets/icici_logo.png" alt="Logo" style="width: 90%;margin-top: 3%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="<?php echo base_url();?>assets/Kotak_logo.png" alt="Logo" style="width: 90%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="<?php echo base_url();?>assets/Indusind-logo.png" alt="Logo" style="width: 90%;margin-top: 10%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="<?php echo base_url();?>assets/NPCI-Logo.png" alt="Logo" style="width: 90%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="<?php echo base_url();?>assets/UPI-Logo.png" alt="Logo" style="width: 60%;margin-top: 7%;">
		    </div>
		</div>
	
    <?php $this->load->view('retailer/footer'); ?> 
	
    </body>

</html>