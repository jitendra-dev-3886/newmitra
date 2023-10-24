<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo strtoupper( $_SERVER['HTTP_HOST']); ?> | LOGIN </title>
    
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


    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../loginassets/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="../../loginassets/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="../../loginassets/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../loginassets/style.css">
    
</head>

<body>
   <section class="fxt-template-animation fxt-template-layout21" style="background: linear-gradient(to bottom left, #ffff00 10%, #ff0000 121%);">
        <!-- Animation Start Here -->
        <div id="particles-js"></div>
        <!-- Animation End Here --> 
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-12 col-lg-12 col-sm-12 col-12 fxt-bg-color">
                    <div class="row">
                    <div class="col-xl-5 col-lg-5 col-sm-5 col-12" style="padding-right: 0px;">
                        
                        <img src="http://kashishrecharge.in/images/rec3.jpg" alt="Logo" style="width: 100%;height: 100%;">
                    </div>
                    <div class="col-xl-7 col-lg-7 col-sm-7 col-12" style="padding-left: 0px;">
                            <div class="fxt-content" style="background-color: rgba(255, 255, 255, 0.39);">
                            <div class="fxt-form"> 
                            <h5><?php echo $this->session->flashdata('msg'); ?></h5>
                            <form method="POST">
                                <div class="form-group">
                                    <div class="row"> 
                                        <div class="col-md-3 col-lg-3 col-sm-3"> 
                                            <label class="rdiobox">
                                                <input id="Master" type="radio" name="logintype">
                                                <span style="color: #a4a4a4;font-size: 17px;">Master</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-sm-3"> 
                                            <label class="rdiobox">
                                                <input id="Distributor" type="radio" name="logintype">
                                                <span style="color: #a4a4a4;font-size: 17px;">Distributor</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-sm-3"> 
                                            <label class="rdiobox">
                                                <input id="Retailer" type="radio" name="logintype">
                                                <span style="color: #a4a4a4;font-size: 17px;">Retailer</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-sm-3">     
                                            <label class="rdiobox">
                                                <input id="API Client" type="radio" name="logintype">
                                                <span style="color: #a4a4a4;font-size: 17px;">API Client</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group"> 
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                              
                                        <input type="text" class="form-control" name="mobile" placeholder="Mobile" required="required" onfocusout="showPosition()">
                                    </div>
                                </div>
                                <div class="form-group">  
                                    <div class="fxt-transformY-50 fxt-transition-delay-2">                                              
                                        <input id="password" type="password" class="form-control" name="password" placeholder="********" required="required">
                                        <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon"></i>
                                    </div>
                                </div>
                                
                                <div class="form-group"> 
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                              
                                        <input type="hidden" id="lat" class="form-control" name="location">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">  
                                        <div class="fxt-checkbox-area">
                                            <div class="checkbox">
                                                <input id="checkbox1" type="checkbox">
                                                <label for="checkbox1">Keep me logged in</label>
                                            </div>
                                            <!--<a href="forgot-password-21.html" class="switcher-text">Forgot Password</a>-->
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-4">  
                                        <button type="submit" name="login" class="fxt-btn-fill">Log in</button>
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
    </section>    
    
    
    <!-- jquery-->
    <script src="../../loginassets/js/jquery-3.5.0.min.js"></script>
    <!-- Popper js -->
    <script src="../../loginassets/js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="../../loginassets/js/bootstrap.min.js"></script>
    <!-- Imagesloaded js -->
    <script src="../../loginassets/js/imagesloaded.pkgd.min.js"></script>
    <!-- Particles js -->
    <script src="../../loginassets/js/particles.min.js"></script>
    <script src="../../loginassets/js/particles-1.js"></script>
    <!-- Validator js -->
    <script src="../../loginassets/js/validator.min.js"></script>
    <!-- Custom Js -->
    <script src="../../loginassets/js/main.js"></script>
    
 </body>

</html>