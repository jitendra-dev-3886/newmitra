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
    <!--<div class="carousel-item active">-->
    <!--  <img class="d-block w-100" src="http://kashishrecharge.in/images/rec3.jpg" alt="First slide">-->
    <!--</div>-->
    <!--<div class="carousel-item">-->
    <!--  <img class="d-block w-100" src="http://kashishrecharge.in/images/rec3.jpg" alt="Second slide">-->
    <!--</div>-->
    <!--<div class="carousel-item">-->
    <!--  <img class="d-block w-100" src="http://kashishrecharge.in/images/rec3.jpg" alt="Third slide">-->
    <!--</div>-->
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
							<!--<img src="http://kashishrecharge.in/images/rec3.jpg" alt="Logo">-->
						</div>
					</div>
				</div>
				<div class="p-5 wd-md-50p" style="width: 35% !important;padding: 15px 3rem !important;">
					<div class="main-signin-header">
						<img src="https://edigitalvillage.net/assets/logo.png" alt="Logo" style="padding-left: 20%;width: 60%;">
						<h5><?php echo $this->session->flashdata('msg'); ?></h5>
						<form action="https://laravel.spruko.com/azira/horizontal_light/index">
						    <div class="form-group">
								<label>User</label> 
								<select class="form-control" name="user_type" >
                                    <option>Choose User</option>
                                    <option value="Master">Master</option>
                                    <option value="Distributor">Distributor</option>
                                    <option value="Retailer">Retailer</option>
                                    <option value="API Client">API Client</option>
                                </select>
							</div>
							<div class="form-group">
								<label>Username</label><input class="form-control" placeholder="Enter your mobile" type="text">
							</div>
							<div class="form-group">
								<label>Password</label> <input class="form-control" placeholder="Enter your password" type="password">
							</div>
							
							<button class="btn btn-main-primary btn-block">Sign In</button>
						</form>
					</div>
					<div class="main-signin-footer mt-3 mg-t-5">
						<p><a href="#">Forgot password?</a></p>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="row">
		    <div class="col-md-2 col-lg-2 col-sm-2" style="padding-left: 7%;"><h5 style="margin-top: 12%;">Powerd By :</h5></div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="https://logos-download.com/wp-content/uploads/2016/10/icici_bank_logo_symbol.png" alt="Logo" style="width: 90%;margin-top: 3%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="https://logos-download.com/wp-content/uploads/2016/06/Kotak_Mahindra_Bank_logo.png" alt="Logo" style="width: 90%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="https://cdn.freelogovectors.net/wp-content/uploads/2019/01/Indusind-bank-logo.png" alt="Logo" style="width: 90%;margin-top: 10%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="https://i0.wp.com/www.jobsgama.com/wp-content/uploads/2018/07/NPCI-Logo.png?fit=400%2C150&ssl=1" alt="Logo" style="width: 90%;">
		    </div>
		    <div class="col-md-2 col-lg-2 col-sm-2">
		        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/UPI-Logo-vector.svg/1024px-UPI-Logo-vector.svg.png" alt="Logo" style="width: 60%;margin-top: 7%;">
		    </div>
		</div>
		<!-- /main-signin-wrapper -->
   <!--<section class="fxt-template-animation fxt-template-layout21" style="background: linear-gradient(to bottom left, #ffff00 10%, #ff0000 121%);">-->
   <!--      Animation Start Here -->
   <!--     <div id="particles-js"></div>-->
   <!--      Animation End Here -->
   <!--     <div class="container">-->
   <!--         <div class="row align-items-center justify-content-center">-->
   <!--             <div class="col-xl-12 col-lg-12 col-sm-12 col-12 fxt-bg-color">-->
   <!--                 <div class="row">-->
   <!--                 <div class="col-xl-5 col-lg-5 col-sm-5 col-12" style="padding-right: 0px;">-->
                        
   <!--                     <img src="http://kashishrecharge.in/images/rec3.jpg" alt="Logo" style="width: 100%;height: 100%;">-->
   <!--                 </div>-->
   <!--                 <div class="col-xl-7 col-lg-7 col-sm-7 col-12" style="padding-left: 0px;">-->
   <!--                         <div class="fxt-content" style="background-color: rgba(255, 255, 255, 0.39);">-->
   <!--                         <div class="fxt-form"> -->
   <!--                         <h5><?php echo $this->session->flashdata('msg'); ?></h5>-->
   <!--                         <form method="POST">-->
   <!--                             <div class="form-group">-->
   <!--                                 <div class="row"> -->
   <!--                                     <div class="col-md-3 col-lg-3 col-sm-3"> -->
   <!--                                         <label class="rdiobox">-->
   <!--                                             <input id="Master" type="radio" name="logintype">-->
   <!--                                             <span style="color: #a4a4a4;font-size: 17px;">Master</span>-->
   <!--                                         </label>-->
   <!--                                     </div>-->
   <!--                                     <div class="col-md-3 col-lg-3 col-sm-3"> -->
   <!--                                         <label class="rdiobox">-->
   <!--                                             <input id="Distributor" type="radio" name="logintype">-->
   <!--                                             <span style="color: #a4a4a4;font-size: 17px;">Distributor</span>-->
   <!--                                         </label>-->
   <!--                                     </div>-->
   <!--                                     <div class="col-md-3 col-lg-3 col-sm-3"> -->
   <!--                                         <label class="rdiobox">-->
   <!--                                             <input id="Retailer" type="radio" name="logintype">-->
   <!--                                             <span style="color: #a4a4a4;font-size: 17px;">Retailer</span>-->
   <!--                                         </label>-->
   <!--                                     </div>-->
   <!--                                     <div class="col-md-3 col-lg-3 col-sm-3">     -->
   <!--                                         <label class="rdiobox">-->
   <!--                                             <input id="API Client" type="radio" name="logintype">-->
   <!--                                             <span style="color: #a4a4a4;font-size: 17px;">API Client</span>-->
   <!--                                         </label>-->
   <!--                                     </div>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                             <div class="form-group"> -->
   <!--                                 <div class="fxt-transformY-50 fxt-transition-delay-1">                                              -->
   <!--                                     <input type="text" class="form-control" name="mobile" placeholder="Mobile" required="required" onfocusout="showPosition()">-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                             <div class="form-group">  -->
   <!--                                 <div class="fxt-transformY-50 fxt-transition-delay-2">                                              -->
   <!--                                     <input id="password" type="password" class="form-control" name="password" placeholder="********" required="required">-->
   <!--                                     <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon"></i>-->
   <!--                                 </div>-->
   <!--                             </div>-->
                                
   <!--                             <div class="form-group"> -->
   <!--                                 <div class="fxt-transformY-50 fxt-transition-delay-1">                                              -->
   <!--                                     <input type="hidden" id="lat" class="form-control" name="location">-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                             <div class="form-group">-->
   <!--                                 <div class="fxt-transformY-50 fxt-transition-delay-3">  -->
   <!--                                     <div class="fxt-checkbox-area">-->
   <!--                                         <div class="checkbox">-->
   <!--                                             <input id="checkbox1" type="checkbox">-->
   <!--                                             <label for="checkbox1">Keep me logged in</label>-->
   <!--                                         </div>-->
   <!--                                         <a href="forgot-password-21.html" class="switcher-text">Forgot Password</a>-->
   <!--                                     </div>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                             <div class="form-group">-->
   <!--                                 <div class="fxt-transformY-50 fxt-transition-delay-4">  -->
   <!--                                     <button type="submit" name="login" class="fxt-btn-fill">Log in</button>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                         </form>                -->
   <!--                     </div> -->
   <!--                     </div> -->
   <!--                 </div> -->
   <!--                 </div>-->
   <!--             </div>                    -->
   <!--         </div>-->
   <!--     </div>-->
   <!-- </section>    -->
    
    
    <?php $this->load->view('retailer/footer'); ?> 
	
    </body>

</html>