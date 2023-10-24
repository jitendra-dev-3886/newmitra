<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('retailer/loader'); ?>

		<?php $this->load->view('retailer/header'); ?>

		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
									<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Hi, welcome back <?php echo $this->session->userdata('cus_name');?> !!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Support</li>
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
					
				<!-- main-content-body -->
				<div class="main-content-body">
				    <div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="main-content-label mg-b-5">
									Support<br>
								</div>
								<?php if($this->session->flashdata('message')){?>
								<div class="alert alert-info" role="alert">
                                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                	   <span aria-hidden="true">&times;</span>
                                  </button>
                                    <strong><?php echo $this->session->flashdata('message');?></strong> 
                                </div>
                                <?php }?>
								<!--<p class="mg-b-20">Select Your Service.</p>-->
								<div class="row"><br></div>
								<div class="row">
									<div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
								        <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<h5 class="card-title mg-b-20">For any Support Related Queries Kindly Mail us at: <span style="color:green;">sales@edigitalvillage.com</sapn></h5>
                        					<p>Our Technical Support Executives will Revert you back within 48 working hrs of your mail, and provide the schedule for the same.</p>
                        				    <br><h5 class="card-title mg-b-20">Support Time:</h5>
                        				    <p>Monday to Friday - 10:00 AM to 07:00 PM.<br>
                        				    Saturday - 10:00 AM to 04:00 PM. <br>
                        				    Sunday Will be Closed. </p>
                        				    <h6 style="text-align:center;color:red">!! WE WILL BE HAPPY TO SERVE YOU !!</h6>
                        				</div>	
                        			</div>	
                        		</div>	
								<div class="row">
									                           <div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
                        										<div class="card card-body pd-20 pd-md-40 border shadow-none">
                        										    <form action="<?php echo base_url();?>/Retailer/send_support" method="post" enctype="multipart/form-data">
                        											<h5 class="card-title mg-b-20">Specify Your Problem:</h5>
                        										    <div class="row">	
                            										    <div class="col-md-6 col-lg-6 col-xl-6">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Issue</label>
                                													<select name="type" required="" class="form-control select2-no-search">
                                														<option label="Choose one"></option>
                                														<option value="">Your issue is regarding</option>
                                                                                		<option value="Recharge Failed">Recharge Failed</option>
                                                                                		<option value="Recharge Pending">Recharge Pending</option>
                                                                                		<option value="Payment Failed">Payment Failed</option>
                                                                                		<option value="Recharge Successful but Service not received">Recharge Successful but Service not received</option>
                                                                                		<option value="Cash back/ Promocode issue">Cash back/ Promocode issue</option>
                                                                                		<option value="Refund/Payment issue">Refund/Payment issue</option>
                                                                                		<option value="Other Issues">Other Issues</option>
                                													</select>
                                											</div>
                            											</div>
                            										    <div class="col-md-6 col-lg-6 col-xl-6">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Recharge Id</label> 
                                												<input class="form-control" name="txnid" placeholder="Enter Recharge Id" type="text">
                                											</div>
                            											</div>
                        											</div>
                        											<div class="row">	
                            										    <div class="col-md-6 col-lg-6 col-xl-6">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Subject</label>
                                												<input class="form-control" required="" name="subject" placeholder="Enter Subject" type="text">
                                											</div>
                            											</div>
                            										    <div class="col-md-6 col-lg-6 col-xl-6">
                                											<div class="form-group">
                                											    <label class="main-content-label tx-11 tx-medium tx-gray-600">Priority</label>
                                													<select class="form-control select2-no-search" required="" name="priority">
                                														<option label="Choose one"></option>
                                														<option value="Urgent">Urgent</option>
                                														<option value="Regular">Regular</option>
                                													</select>
                                											</div>
                            											</div>
                        											</div>
                        											<div class="row">	
                            										    <div class="col-md-6 col-lg-6 col-xl-6">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Message</label> 
                                												<textarea class="form-control" required="" name="desc" placeholder="Message" rows="9"></textarea>
                                											</div>
                            											</div>
                            										    <div class="col-md-6 col-lg-6 col-xl-6">
                                											<div class="form-group">
                                												<label class="main-content-label tx-11 tx-medium tx-gray-600">Attachment</label> 
                                											    <input type="file" name="userfile" class="dropify" data-height="200" />
                                											</div>
                            											</div>
                        											</div>
                        											<button type="submit" class="btn btn-main-primary btn-block">send</button>
                        										</form>
                        										</div>
                        									</div>
								                         </div>
								<div class="row">
									<div class="col-md-10 col-lg-10 col-xl-10 mx-auto d-block">
								        <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap">
										<thead>
											<tr>
												<th class="border-bottom-0">Sr. No.</th>
												<th class="border-bottom-0">Rec ID</th>
												<th class="border-bottom-0">TicketID</th>
												<th class="border-bottom-0">Issue Type</th>
												<th class="border-bottom-0">Subject</th>
												<th class="border-bottom-0">Priority</th>
												<th class="border-bottom-0">Status</th>
												<th class="border-bottom-0">Date</th>
												<th class="border-bottom-0">View</th>
											</tr>
										</thead>
										<tbody>
										    <?php $i=1; if($ticket){ foreach($ticket as $tick){?>
											<tr>
												<td><?php echo $i++;?></td>
												<td><?php echo $tick->rec_id;?></td>
												<td><?php echo $tick->ticket_id;?></td>
												<td><?php echo $tick->issue;?></td>
												<td><?php echo $tick->subject;?></td>
												<td><?php echo $tick->priority;?></td>
												<td><?php echo $tick->status;?></td>
												<td><?php echo $tick->ticket_date;?></td>
												<td><a href="<?php echo base_url();?>Retailer/ticket/<?php echo $tick->ticket_id;?>" class="btn btn-main-primary btn-block"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
											</tr>
											<?php }}?>
										</tbody>
									</table>
								</div>
                        				</div>	
                        			</div>	
                        		</div>	
							</div>
						</div>
					</div>	
				</div>
				<!-- / main-content-body -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>

</html>