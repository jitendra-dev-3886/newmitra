<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('admin/links'); ?>

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
						<h4 class="content-title mb-2">Member Management</h4>
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
									<!-- row -->
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
							    <div class="main-content-label mg-b-5">
									Switch Account
								</div><br>
								<form action="<?php echo site_url('admin/switch-success'); ?>" method="post">
								    
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
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
	<script type="text/javascript">
       $(document).ready(function(){
            $('input[type="radio"]').change(function() {
                var custype = $(this).val();
                var id = $('#id').val();
                //alert(id);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/findparent',
                    type: 'POST',
                    dataType: 'HTML',
                    data : {custype:custype,id:id},
                    success: function(res) {
                        console.log(res);
                        $('#result').html(res);
                    }
                })
            });
        });
    </script>

</html>