<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo strtoupper( $_SERVER['HTTP_HOST']); ?> | LOGIN </title>
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
    <section class="fxt-template-animation fxt-template-layout21">
        <!-- Animation Start Here -->
        <div id="particles-js"></div>
        <!-- Animation End Here --> 
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 col-lg-7 col-sm-12 col-12 fxt-bg-color">
                    <div class="fxt-content">
                        <div class="fxt-header">
                            <a href="" class="fxt-logo"><img src="../../assets/logo.png" alt="Logo" style="width:50%"></a>   
                        </div>                            
                        <div class="fxt-form"> 
                            <form method="POST" action="<?php echo site_url('admin_login/verifyotp'); ?>">
                                <div class="form-group"> 
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                              
                                        <input type="text" id="otp" class="form-control" name="enteredotp" placeholder="Enter OTP Sent on Registered Email Id" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">  
                                        <div class="fxt-checkbox-area">
                                            <label for="checkbox1">Didn't Recieved OTP?</label>
                                            <a href="<?php echo base_url().'admin_login/resendotp'; ?> " class="switcher-text">Resend OTP</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-4">  
                                        <button type="submit" name="verify" class="fxt-btn-fill">Verify</button>
                                    </div>
                                </div>
                            </form>                
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