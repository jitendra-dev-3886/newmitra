<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>
    <style>
        
        .btn-icon {
            
            width: 175px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
    </style>
        
	<body class="main-body  app">
		
		
		<?php $this->load->view('retailer/loader'); ?>

		<?php $this->load->view('retailer/header'); ?>
		
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Hi, welcome back <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Advanced ui</a></li>
								<li class="breadcrumb-item active" aria-current="page"> Beneficiary List</li>
							</ol>
						</nav>
					</div>
					<div style="float:right">
					    <a href="<?php echo site_url('Retailer/addbeneficary')?>"><button class="btn btn-main-primary">ADD NEW BENEFICIARY</button></a>
					</div>
				</div>
				<div class="row">
				    <?php if(is_array($beneficary->Beneficiary)){foreach($beneficary->Beneficiary as $bene){ ?>
					<div class="col-sm-12 col-md-4">
						<div class="card custom-card">
							<div class="card-body text-center">
								<h5 class="mb-1 mt-3 card-title"><?php echo $bene->BeneficiaryName; echo ' ('.$bene->BeneficiaryCode.')'; ?></h5>
								<div class="d-lg-flex mt-2 align-items-center justify-content-center text-center">
									<p class="mb-2 mr-3"><i class="icon ion-ios-list-box"></i>&nbsp;<?php echo $bene->AccountNumber; ?></p>
									<p class="mb-2"><i class="icon ion-md-pricetags"></i>&nbsp;<?php echo $bene->IFSC; ?></p>
								</div>
								<div class="justify-content-center text-center mt-3 d-flex">
								    <?php 
								        $det =$bene->BeneficiaryCode.';'.$bene->AccountNumber.';'.$bene->IFSC;
								        $details = $this->encryption_model->encode($det);
								    ?>
									<a href="<?php echo site_url('Retailer/moneytransfer/'.$details) ?>" class="btn ripple btn-primary btn-icon mr-3">
										<i class="fe fe-credit-card"></i>&nbsp;Send Money
									</a>
									<?php $bene_code = $bene->BeneficiaryCode; ?>
									<a href="<?php echo site_url('Retailer/deleteBeneficiary/'.$bene_code) ?>" class="btn ripple btn-danger btn-icon">
										<i class="fe fe-trash-2"></i>&nbsp;Delete Beneficiary
									</a>
								</div>
							</div>
						</div>
					</div>
					<?php }} ?>
				</div>
				<!-- row closed -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
		
	   <?php $this->load->view('retailer/footer'); ?> 
	</body>
</html>