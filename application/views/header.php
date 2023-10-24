<?php include("links.php")?>
<!--/header -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<header class="nav_w3pvt text-center "><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<header class="nav_w3pvt text-center "><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- nav -->
    <nav class="wthree-w3ls">
        <div id="logo">
            <h1> <a class="navbar-brand px-0 mx-0" href="index.php"><img src = "assets/logo.jpg" style = "height:50px" >
                </a>
            </h1>
        </div>

        <label for="drop" class="toggle">Menu</label>
        <input type="checkbox" id="drop" />
        <ul class="menu mr-auto" style="margin-top:15px">
            <li class=""><a href="index.php">Home</a></li>
            <li><a href="#about">Aboutus</a></li>
            <li><a href="#servicesprovided">Services</a></li>
            <li><a href="<?php echo base_url();?>web/policy">Privacy Policy</a></li>
            <li><a href="#contact">Contactus</a></li>
            <li><a href="https://play.google.com/store/apps/details?id=com.satmatgroup.newmitra" target="/blank">APP LINK</a></li>
            <li><a href="<?php echo base_url();?>web/signup">Signup</a></li>
              <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                LOGIN
               </a>
               <div class="dropdown-menu">
                   <a class="dropdown-item" href="<?php echo base_url();?>Users_login">User Login</a>
                </div>
            </li>
            <!--<li><a href="https://saipay.in/include/apprelease.apk" download>APP DOWNLOAD</a></li>-->
            

            
        </ul>
    </nav>
    <!-- //nav -->
</header>
<!--//header -->