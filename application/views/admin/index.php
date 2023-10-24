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
                            <a href="" class="fxt-logo"><img src="<?php echo base_url()?>assets/logo.jpg" alt="Logo" style="width:50%"></a>   
                        </div>                            
                        <div class="fxt-form"> 
                            <?php $seckey = $_GET['seckey'];?>
                            <form method="POST" action="<?php echo site_url('admin_login/index?seckey='.$seckey); ?>">
                                <div class="form-group"> 
                                    <div class="fxt-transformY-50 fxt-transition-delay-1">                                              
                                        <input type="text" id="username" class="form-control" name="username" placeholder="Username" required="required">
                                    </div>
                                </div>
                                <div class="form-group">  
                                    <div class="fxt-transformY-50 fxt-transition-delay-2">                                              
                                        <input id="password" type="password" class="form-control" name="password" placeholder="********" required="required">
                                        <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-3">  
                                        <div class="fxt-checkbox-area">
                                            <div class="checkbox">
                                                <input id="checkbox1" type="checkbox">
                                                <label for="checkbox1">Keep me logged in</label>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-transformY-50 fxt-transition-delay-4">  
                                        <button type="submit" class="fxt-btn-fill">Log in</button>
                                    </div>
                                </div>
                            </form>                
                        </div> 
                        <!--<div class="fxt-style-line"> 
                            <div class="fxt-transformY-50 fxt-transition-delay-5">                                
                                <h3>Or Login With</h3> 
                            </div>
                        </div>
                        <ul class="fxt-socials">
                            <li class="fxt-google">
                                <div class="fxt-transformY-50 fxt-transition-delay-6">  
                                <a href="#" title="google"><i class="fab fa-google-plus-g"></i><span>Google +</span></a>
                                </div>
                            </li>                                    
                            <li class="fxt-twitter"><div class="fxt-transformY-50 fxt-transition-delay-7">  
                                <a href="#" title="twitter"><i class="fab fa-twitter"></i><span>Twitter</span></a>
                                </div>
                            </li>
                            <li class="fxt-facebook"><div class="fxt-transformY-50 fxt-transition-delay-8">  
                                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i><span>Facebook</span></a>
                                </div>
                            </li>                                    
                        </ul>-->
                        
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


</body>

</html>