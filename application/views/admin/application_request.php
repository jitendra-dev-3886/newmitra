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
						<h4 class="content-title mb-2">Application Request Details</h4>
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
									
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
								    <b>Application Request</b>
								    <!--
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add Bank Details</button></div>-->
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">Customer ID</th>
												<th class="wd-15p border-bottom-0">Application Name</th>
												<th class="wd-15p border-bottom-0">Mobile Number</th>
												<th class="wd-15p border-bottom-0">Email Id</th>
												<th class="wd-10p border-bottom-0">Aadhar Number</th>
												<th class="wd-10p border-bottom-0">Pan Number</th>
												<th class="wd-10p border-bottom-0">Amount</th>
												<th class="wd-10p border-bottom-0">Transaction Id</th>
												<th class="wd-10p border-bottom-0">Application Id</th>
												<th class="wd-10p border-bottom-0">Status</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($r)){$i = 1;foreach($r as $v) { ?>     
											<tr>
                                                <td><?php echo $i++;?></td>
                                                <td><b><?php echo ucfirst($v->cus_name); ?><br>
    												    <?php echo $v->cus_id; ?><br></td>
                                                <td><?php echo $name =$v->appname; ?></td>
                                                <td><?php echo $v->mobile; ?></td>
                                                <td><?php echo $v->email; ?></td>
                                                <td><?php echo $v->adhar_number; ?></td>
                                                <td><?php echo $v->pan_number; ?></td>
                                                <td><?php echo $v->amount; ?></td>
                                                <td><?php echo $v->txnId; ?></td>
                                                <td><?php echo $v->application_id; ?></td>
                                                <td class="<?php if($v->status == 'Approved'){ echo 'text-success';}else { echo 'text-warning';} ?>"><b><?php echo $v->status; ?></b></td>
                                                
                                                <td class="text-center">
												    <?php if($v->status == "Pending"){ ?>
<!--    												    <a href="<?php echo base_url()?>admin/fund-request-accept/<?php echo $v->id?>"><i class="fa fa-check text-success"></i></a>&nbsp;&nbsp;&nbsp;
-->    												    <a class='text-primary'onClick="test('<?php echo $v->id;?>','<?php echo $v->cus_id; ?>','<?php echo $name;?>')"><i class="fa fa-edit" data-target="#modaldemo1" data-toggle="modal"></i></a>
												    <?php }else{ ?> 
												            <input type="hidden" value="<?php echo "$v->status ";?>">
<!--												        <span class="text-info"><?php //echo "REQUEST $rec->req_status ";?></span>
-->												    <?php } ?>
												</td>
                                            </tr>
                                      <?php $sl++;} } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--/div-->
					
							<div class="modal" id="modaldemo1">
                        		<div class="modal-dialog" role="document">
                        			<div class="modal-content modal-content-demo">
                        				<form action="<?php echo base_url()?>admin/application_request_approved" method="post">
                            				<div class="modal-header">
                            					<h6 class="modal-title">Approved Application Requst</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                            				</div>
                            				<div class="modal-body">
                            					<input type="hidden"  >
                                   					<div class="form-group">
                                					    <!--<label>Application Status</label>-->
                                					    <!--input type="text" id="here" name="id" class="form-control" readonly required-->
                                					    <input type="hidden" id="cusid" name="cusid" class="form-control" readonly required>
                                					</div>
                                					<div class="form-group">
                                					        <input type="hidden" id="appname" name="appname" class="form-control" readonly required>
                                					</div>
                                					<div class="form-group">
                                					    <label>Application Id</label>
                                					    <input type="text" class="form-control" id="application_id" name="application_id"  required>
                                					</div>
                        					</div>
                            				<div class="modal-footer">
                            					<input class="btn ripple btn-primary" type="submit">
                            					<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                            				</div>
                            			</form>	
                        			</div>
                        		</div>
    	                    </div>
    	
    	<script>
		    function test(id,cusid,appname){
		        //alert(id);
		        //alert(cusid);
		        //alert(appname);
		    $('#here').val(id);
		    $('#cusid').val(cusid);
		    $('#appname').val(appname);
		    }
		</script>
					

				</div>
				<!-- /row -->

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>

</html>