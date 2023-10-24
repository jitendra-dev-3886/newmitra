<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('api_partner/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('api_partner/loader'); ?>

		<?php $this->load->view('api_partner/header'); ?>

		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
									<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<!--<div>
						<h4 class="content-title mb-2">Hi, welcome back <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page"></li>
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
					</div>-->
				</div>
				<div class="row">
				    <div class="col-sm-12">
		         <?php $news=$this->db_model->getAlldata("SELECT * FROM news ORDER BY news_id DESC LIMIT 1");?>
								<marquee style="color:white;font-size:20px"><?php echo $news[0]->news_desc;?></marquee></div>
				</div><br>
				<!-- /breadcrumb -->
					
				<!-- main-content-body -->
				<div class="main-content-body">
					<div class="row row-sm">
						<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
							<div class="card overflow-hidden project-card">
								<div class="card-body">
									<div class="d-flex">
										<div class="my-auto">
											<svg enable-background="new 0 0 477.849 477.849" class="mr-4 ht-60 wd-60 my-auto success" version="1.1" viewBox="0 0 477.85 477.85" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
												<path d="m374.1 385.52c71.682-74.715 69.224-193.39-5.492-265.08-34.974-33.554-81.584-52.26-130.05-52.193-103.54-0.144-187.59 83.676-187.74 187.22-0.067 48.467 18.639 95.077 52.193 130.05l-48.777 65.024c-5.655 7.541-4.127 18.238 3.413 23.893s18.238 4.127 23.893-3.413l47.275-63.044c65.4 47.651 154.08 47.651 219.48 0l47.275 63.044c5.655 7.541 16.353 9.069 23.893 3.413 7.541-5.655 9.069-16.353 3.413-23.893l-48.775-65.024zm-135.54 24.064c-84.792-0.094-153.51-68.808-153.6-153.6 0-84.831 68.769-153.6 153.6-153.6s153.6 68.769 153.6 153.6-68.769 153.6-153.6 153.6z"/>
												<path d="m145.29 24.984c-33.742-32.902-87.767-32.221-120.67 1.521-32.314 33.139-32.318 85.997-8e-3 119.14 6.665 6.663 17.468 6.663 24.132 0l96.546-96.529c6.663-6.665 6.663-17.468 0-24.133zm-106.55 82.398c-12.186-25.516-1.38-56.08 24.136-68.267 13.955-6.665 30.175-6.665 44.131 0l-68.267 68.267z"/>
												<path d="m452.49 24.984c-33.323-33.313-87.339-33.313-120.66 0-6.663 6.665-6.663 17.468 0 24.132l96.529 96.529c6.665 6.663 17.468 6.663 24.132 0 33.313-33.322 33.313-87.338 0-120.66zm-14.08 82.449-68.301-68.301c19.632-9.021 42.79-5.041 58.283 10.018 15.356 15.341 19.371 38.696 10.018 58.283z"/>
												<path d="m238.56 136.52c-9.426 0-17.067 7.641-17.067 17.067v96.717l-47.787 63.71c-5.655 7.541-4.127 18.238 3.413 23.893s18.238 4.127 23.893-3.413l51.2-68.267c2.216-2.954 3.413-6.547 3.413-10.24v-102.4c1e-3 -9.426-7.64-17.067-17.065-17.067z"/>
											</svg>
										</div>
										<div class="project-content">
											<h6>BALANCE</h6>
											<ul>
												<li>
													<strong>Rupees</strong>
													<span><?php if(!empty($data['balance'][0]->amt)){echo $data['balance'][0]->amt; }else{ echo '0'; }?>/-</span>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
							<div class="card overflow-hidden project-card">
								<div class="card-body">
									<div class="d-flex">
										<div class="my-auto">
											<svg enable-background="new 0 0 512 512" class="mr-4 ht-60 wd-60 my-auto warning" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
												<path d="m259.2 317.72h-6.398c-8.174 0-14.824-6.65-14.824-14.824 1e-3 -8.172 6.65-14.822 14.824-14.822h6.398c8.174 0 14.825 6.65 14.825 14.824h29.776c0-20.548-13.972-37.885-32.911-43.035v-33.74h-29.777v33.739c-18.94 5.15-32.911 22.487-32.911 43.036 0 24.593 20.007 44.601 44.601 44.601h6.398c8.174 0 14.825 6.65 14.825 14.824s-6.65 14.824-14.825 14.824h-6.398c-8.174 0-14.824-6.65-14.824-14.824h-29.777c0 20.548 13.972 37.885 32.911 43.036v33.739h29.777v-33.74c18.94-5.15 32.911-22.487 32.911-43.035 0-24.594-20.008-44.603-44.601-44.603z"/>
												<path d="m502.7 432.52c-7.232-60.067-26.092-111.6-57.66-157.56-27.316-39.764-65.182-76.476-115.59-112.06v-46.29l37.89-98.425-21.667-0.017c-6.068-4e-3 -8.259-1.601-13.059-5.101-6.255-4.559-14.821-10.802-30.576-10.814h-0.046c-15.726 0-24.292 6.222-30.546 10.767-4.799 3.487-6.994 5.081-13.041 5.081h-0.027c-6.07-5e-3 -8.261-1.602-13.063-5.101-6.255-4.559-14.821-10.801-30.577-10.814h-0.047c-15.725 0-24.293 6.222-30.548 10.766-4.8 3.487-6.995 5.081-13.044 5.081h-0.027l-21.484-0.017 36.932 98.721v46.117c-51.158 36.047-89.636 72.709-117.47 111.92-33.021 46.517-52.561 98.116-59.74 157.74l-9.304 77.285h512l-9.304-77.284zm-301.06-395.47c4.8-3.487 6.995-5.081 13.045-5.081h0.026c6.07 4e-3 8.261 1.602 13.062 5.101 6.255 4.559 14.821 10.802 30.578 10.814h0.047c15.725 0 24.292-6.222 30.546-10.767 4.799-3.487 6.993-5.081 13.041-5.081h0.026c6.068 5e-3 8.259 1.602 13.059 5.101 2.869 2.09 6.223 4.536 10.535 6.572l-21.349 55.455h-92.526l-20.762-55.5c4.376-2.041 7.773-4.508 10.672-6.614zm98.029 91.89v26.799h-83.375v-26.799h83.375zm-266.09 351.08 5.292-43.947c6.571-54.574 24.383-101.7 54.458-144.07 26.645-37.537 62.54-71.458 112.73-106.5h103.78c101.84 71.198 150.75 146.35 163.29 250.56l5.291 43.948h-444.85z"/>
											</svg>
										</div>
										<div class="project-content">
											<h6>AEPS Total Transactions</h6>
											<ul>
												<li>
													<strong>Rupees</strong>
													<span><?php if(!empty($data['aepsbalance'][0]->amt)){echo $data['aepsbalance'][0]->amt; }else{ echo '0'; }?>/-</span>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
            <!--services section-->
					<div class="row">
						<div class="col-md-12 col-12 text-center">
							<div class="task-box primary mb-0">
								<h3 class="mb-0" style="text-align:left;"><i class="fe fe-codepen"></i>  Services</h3>
							</div>
						</div>
					</div>
					<br>
                    <!-- row -->
				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
						<div class="card">
							<div class="card-body iconfont text-left">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-3">Mobile Recharge</h4>
									<i class="mdi mdi-dots-vertical"></i>
								</div>
								<div class="d-flex mb-0">
									<div class="">
										<h4 class="mb-1 font-weight-bold"><?php if(!empty($data['mobile'][0]->amt)){echo $data['mobile'][0]->amt; }else{ echo '0'; }?>/-<span class="text-success tx-13 ml-2">(₹)</span></h4>
										<p class="mb-2 tx-12 text-muted">Monthly</p>
									</div>
									<div class="card-chart bg-primary-transparent brround ml-auto mt-0">
										<img src="../../assets/img/serviceicon/icons8-android-24.png"/>
									</div>
								</div>

								<div class="progress progress-sm mt-2">
									<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar bg-primary wd-100p" role="progressbar"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
						<div class="card">
							<div class="card-body iconfont text-left">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-3">Data Card Recharge</h4>
									<i class="mdi mdi-dots-vertical"></i>
								</div>
								<div class="d-flex mb-0">
									<div class="">
										<h4 class="mb-1 font-weight-bold"><?php if(!empty($data['datacard'][0]->amt)){echo $data['datacard'][0]->amt; }else{ echo '0'; }?>/-<span class="text-danger tx-13 ml-2">(₹)</span></h4>
										<p class="mb-2 tx-12 text-muted">Monthly</p>
									</div>
									<div class="card-chart bg-pink-transparent brround ml-auto mt-0">
										<img src="../../assets/img/serviceicon/icons8-usb-off-26.png"/>
									</div>
								</div>

								<div class="progress progress-sm mt-2">
									<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar bg-pink wd-100p" role="progressbar"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
						<div class="card">
							<div class="card-body iconfont text-left">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-3">DTH Recharge</h4>
									<i class="mdi mdi-dots-vertical"></i>
								</div>
								<div class="d-flex mb-0">
									<div class="">
										<h4 class="mb-1   font-weight-bold"><?php if(!empty($data['dth'][0]->amt)){echo $data['dth'][0]->amt; }else{ echo '0'; }?>/-<span class="text-success tx-13 ml-2">(₹)</span></h4>
										<p class="mb-2 tx-12 text-muted">Monthly</p>
									</div>
									<div class="card-chart bg-teal-transparent brround ml-auto mt-0">
										<img src="../../assets/img/serviceicon/icons8-gps-antenna-100.png"/>
									</div>
								</div>

								<div class="progress progress-sm mt-2">
									<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar bg-teal wd-100p" role="progressbar"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
						<div class="card">
							<div class="card-body iconfont text-left">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-3">Landline Recharge </h4>
									<i class="mdi mdi-dots-vertical"></i>
								</div>
								<div class="d-flex mb-0">
									<div class="">
										<h4 class="mb-1 font-weight-bold"><?php if(!empty($data['landline'][0]->amt)){echo $data['landline'][0]->amt; }else{ echo '0'; }?>/-<span class="text-success tx-13 ml-2">(₹)</span></h4>
										<p class="mb-2 tx-12 text-muted">Monthly</p>
									</div>
									<div class="card-chart bg-purple-transparent brround ml-auto mt-0">
										<img src="../../assets/img/serviceicon/icons8-phone-24.png"/>
									</div>
								</div>

								<div class="progress progress-sm mt-2">
									<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar bg-purple wd-100p" role="progressbar"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->

				<!-- row -->
				<div class="row row-sm">
					<div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
						<div class="card">
							<div class="card-body">
								<div class="card-order">
									<h6 class="mb-2">Electricity Recharge</h6>
									<h2 class="text-right ">
									    <img class="icon-size float-left text-danger text-danger-shadow" src="../../assets/img/serviceicon/icons8-electricity-24.png"/>
									    <span>₹ <?php if(!empty($data['electricity'][0]->amt)){echo $data['electricity'][0]->amt; }else{ echo '0'; }?>/-</span></h2><!--
									<p class="mb-0">Overview of Last month<span class="float-right">Monthly</span></p>-->
								</div>
							</div>
						</div>
					</div><!-- COL END -->
					<div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
						<div class="card ">
							<div class="card-body">
								<div class="card-widget">
									<h6 class="mb-2">Insurance</h6>
									<h2 class="text-right"><img class="icon-size float-left text-danger text-danger-shadow" src="../../assets/img/serviceicon/icons8-lease-24.png"/>
									<span>₹ <?php if(!empty($data['insurance'][0]->amt)){echo $data['insurance'][0]->amt; }else{ echo '0'; }?>/-</span></h2><!--
									<p class="mb-0">Overview of Last month<span class="float-right">Monthly</span></p>-->
								</div>
							</div>
						</div>
					</div><!-- COL END -->
					<div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
						<div class="card">
							<div class="card-body">
								<div class="card-widget">
									<h6 class="mb-2">Pancard</h6>
									<h2 class="text-right"><img class="icon-size float-left text-danger text-danger-shadow" src="../../assets/img/serviceicon/icons8-gas-bottle-24.png"/>
									<span> <span style="font-size: 0.875rem;">Que</span> <?php if(!empty($data['pan'][0]->amt)){echo $data['pan'][0]->amt; }else{ echo '0'; }?>/-</span></h2><!--
									<p class="mb-0">Overview of Last month<span class="float-right">Monthly</span></p>-->
								</div>
							</div>
						</div>
					</div><!-- COL END -->
					<div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
						<div class="card">
							<div class="card-body">
								<div class="card-widget">
									<h6 class="mb-2">DMT</h6>
									
									<h2  class="text-right"><img class="icon-size float-left text-danger text-danger-shadow" src="../../assets/img/serviceicon/icons8-money-transfer-64.png" style="width:30px;height:30px;"/>
									<span>₹ <?php if(!empty($data['dmt'][0]->amt)){echo $data['dmt'][0]->amt; }else{ echo '0'; }?>/-</span></h2><!--
									<p class="mb-0">Overview of Last month<span class="float-right">Monthly</span></p>-->
								</div>
							</div>
						</div>
					</div><!-- COL END -->
				</div>
				<!-- /row -->
                <!-- row -->
				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body ">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<img src="../../assets/img/serviceicon/icons8-tollbooth-50.png" style="width:30px;height:30px;"/>
								</div>
								<h6 class="mb-1 text-muted">Fastag</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($data['fastag'][0]->amt)){echo $data['fastag'][0]->amt; }else{ echo '0'; }?>/-</h3>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body ">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<img src="../../assets/img/serviceicon/icons8-touch-id-30.png" style="width:30px;height:30px;"/>
								</div>
								<h6 class="mb-1 text-muted">AEPS</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($data['aeps'][0]->amt)){echo $data['aeps'][0]->amt; }else{ echo '0'; }?>/-</h3>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<img src="../../assets/img/serviceicon/icons8-atm-64.png" style="width:30px;height:30px;"/>
								</div>
								<h6 class="mb-1 text-muted">Micro ATM</h6>
								<h3 class="font-weight-semibold">₹ <?php if(!empty($data['microatm'][0]->amt)){echo $data['microatm'][0]->amt; }else{ echo '0'; }?>/-</h3>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-sm-6 col-md-6">
						<div class="card text-center">
							<div class="card-body ">
								<div class="feature widget-2 text-center mt-0 mb-3">
									<img src="../../assets/img/serviceicon/icons8-pay-64.png"/ style="width:30px;height:30px;">
								</div>
								<h6 class="mb-1 text-muted">Aadhar Pay</h6>
								<h3 class="font-weight-semibold"><?php if(!empty($data['adhar'][0]->amt)){echo $data['adhar'][0]->amt; }else{ echo '0'; }?>/-</h3>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->    
					<!--end service section-->
					
					
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('api_partner/footer'); ?> 
	
	</body>

</html>