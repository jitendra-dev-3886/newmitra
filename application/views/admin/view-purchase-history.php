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
						<h4 class="content-title mb-2">Admin Ledger</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($this->uri->segment('2')); ?></li>
							</ol>
						</nav>
					</div>
				<!--	<div class="d-flex my-auto">
						<div class=" d-flex right-page">
							<div class="d-flex justify-content-center mr-5">
								<div class="">
									<span class="d-block">
										<span class="label ">CREDIT</span>
									</span>
									<span class="value">
										$53,000
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar"></span>
								</div>
							</div>
							<div class="d-flex justify-content-center">
								<div class="">
									<span class="d-block">
										<span class="label">DEBIT</span>
									</span>
									<span class="value">
										$34,000
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar31"></span>
								</div>
							</div>
						</div>
					</div>-->
				</div>
				<!-- /breadcrumb -->
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-body">
							    <div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap">
										<thead>
											<tr>
												<th class="wd-10p border-bottom-0">#</th>
												<th class="wd-5p border-bottom-0">Purchase Id</th>
												<th class="wd-5p border-bottom-0">Offer Details</th>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-10p border-bottom-0">Purchase Amount</th>
												<th class="wd-25p border-bottom-0">Status</th>
												<th class="wd-10p border-bottom-0">Payment Details</th>
												<th class="wd-10p border-bottom-0">Distributor</th>
												<th class="wd-10p border-bottom-0">Master</th>
												<th class="wd-15p border-bottom-0">Address</th>
												<th class="wd-15p border-bottom-0">Date</th>
												<th class="wd-15p border-bottom-0">Receiver Name</th>
												<th class="wd-15p border-bottom-0">Delivery Pin</th>
												<th class="wd-15p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($orders)){$i=0;$amt='0';foreach($orders as $rec){ ?>
    											<tr>
        											<?php
    											        $amt+=$rec->amount;
    											    ?>
    											   
    											    <td><?php echo ++$i;?></td>
    												<td>
    												    <b><?php echo $rec->purchase_id; ?><br>
    												    <!--<i class="typcn typcn-book text-success"></i> <br>-->
    												    <?php echo $rec->transaction_id; ?></b>
    												</td>
    												<td>
    												    <b><?php echo $rec->product_name; ?>(<?php echo $rec->offer_id; ?>)</b>
    												</td>
    												<td><b><?php echo ucfirst($rec->cus_name); ?><br><?php echo ucfirst($this->encryption_model->decode($rec->cus_mobile)); ?>(<?php echo $rec->cus_id; ?>)</b></td>
    												<td ><b class="text-primary">₹ <?php echo $rec->amount; ?></b></td>
    												<td class="text-center <?php if($rec->purchase_status == 'ORDER PLACED'){echo 'text-success';}else if($rec->purchase_status == 'FAILED'){echo 'text-danger';}else if($rec->purchase_status == 'DELIVERED') {echo 'text-primary';} else{echo 'text-warning';}?>">
        												<b><?php if($rec->purchase_status != 'REJECTED'){echo strtoupper($rec->purchase_status);} ?></b>
        												<?php if($rec->purchase_status == 'ORDER PLACED'){?>
        												    <br><a href="" onclick="test(<?php echo $rec->purchase_id;?>)" class="btn btn-success" data-target="#modaldemo2" data-toggle="modal">APPROVE</a>
        												<?php } ?>
        												<?php if($rec->purchase_status == 'APPROVED'){?>
        												    <br><a onclick="test1(<?php echo $rec->purchase_id;?>)" class="btn btn-primary" data-target="#modaldemo1" data-toggle="modal">DELIVERED</a>
        												<?php } ?>
        												<?php if($rec->purchase_status == 'DELIVERED'){?>
        												    <br><span class="text-primary"><?php echo $rec->delivered_to;?></span>
        												<?php }if($rec->purchase_status == 'REJECTED'){ ?>
        												    <a class="btn btn-danger">REJECTED</a>
        												<?php }?>
    												</td>
    												<td class="text-center"><b class="text-danger">STATUS : <?php echo strtoupper($rec->payment_status); ?></b><br><b class="text-primary">TYPE : <?php echo strtoupper($rec->payment_type);?></b></</td>
    												<td class="text-info"><b>₹ <?php echo $rec->dis_comm; ?></b></td>
    												<td class="text-info"><b>₹ <?php echo $rec->master_comm; ?></b></td>
    												<td style=" width: 200px;t;"><b><?php echo $rec->delivery_address; ?></b></td>
    												<td style=" width: 200px;t;"><b><?php echo $rec->purchase_date; ?></b></td>
    												<td style=" width: 200px;t;"><b><?php echo $rec->receiver_name; ?></b></td>
    												<td style=" width: 200px;t;"><b><?php echo $rec->delivery_pin; ?></b></td>
                                                    <td><a onclick="refundAmt(<?php echo $rec->purchase_id;?>,<?php echo $rec->cus_id;?>,<?php echo $rec->amount;?>,<?php echo $rec->dis_comm;?>,<?php echo $rec->master_comm;?>)" class="btn btn-danger" data-target="#modaldemo3" data-toggle="modal">Reject</a></td>
    											</tr>
    											
    											
											<?php }} ?>
										</tbody>
										<tbfooter>
										    <tr style="background-color:#1b4863;color:white">
										        <td colspan='4'>Total</td>
										        <td><b><?php echo $amt;?></b></td>
										        <td colspan='2'></td>
										        <td><b><?php echo '0';?></b></</td>
										        <td><b><?php echo '0';?></b></</td>
										        <td></td>
										        <td></td>
										    </tr>
										</tbfooter>
									</table>
								</div>
								
							</div>
						</div>
					</div>
					<!--/div-->


				</div>
				<!-- /row -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
		
		<div class="modal" id="modaldemo1">
                		<div class="modal-dialog" role="document">
                			<div class="modal-content modal-content-demo">
                				<form action="<?php echo base_url()?>admin/deliver_order" method="post">
                    				<div class="modal-header">
                    					<h6 class="modal-title">Change Order to Deivered</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    				</div>
                    				<div class="modal-body">
                    					<input type="hidden"  >
                        					<div class="form-group">
                        					    <label>ID</label>
                        					    <input type="text" id="here1" name="id" class="form-control" readonly required>
                        					</div>
                        					<div class="form-group">
                        					    <label>Date</label>
                                                <input type="date" name="date" class="form-control" required>
                                            </div>
                					</div>
                    				<div class="modal-footer">
                    					<input class="btn btn-primary" type="submit">
                    					<button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                    				</div>
                    			</form>	
                			</div>
                		</div>
                	</div>
                
                	<div class="modal" id="modaldemo2">
                		<div class="modal-dialog" role="document">
                			<div class="modal-content modal-content-demo">
                				<form action="<?php echo base_url()?>admin/approveorder" method="post">
                				    <input type="hidden" id="here" name="id">
                    				<div class="modal-header">
                    					<h6 class="modal-title">Update Delivery Date</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    				</div>
                    				<div class="modal-body">
                    						<div class="form-group">
                        					    <label>Date</label>
                                                <input type="date" name="date" class="form-control" required>
                                            </div>
                					</div>
                    				<div class="modal-footer">
                    					<input class="btn btn-primary" type="submit">
                    					<button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                    				</div>
                    			</form>	
                			</div>
                		</div>
                	</div>
                	
                	<div class="modal" id="modaldemo3">
                		<div class="modal-dialog" role="document">
                			<div class="modal-content modal-content-demo">
                				<form action="<?php echo base_url()?>admin/RefundOfferAmount" method="post">
                				    <input type="hidden" id="pid" name="id">
                				    <input type="hidden" id="cusid" name="cusid">
                				    <input type="hidden" id="amt" name="amt">
                				    <input type="hidden" id="dis_comm" name="dis_comm">
                				    <input type="hidden" id="mas_comm" name="mas_comm">
                    				<div class="modal-header">
                    					<h6 class="modal-title">Reject Purchase Offer</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    				</div>
                    				<div class="modal-body">
                    						<div class="form-group">
                        					    <label>Reason</label>
                                                <textarea type="text" name="reason" class="form-control" required></textarea>
                                            </div>
                					</div>
                    				<div class="modal-footer">
                    					<input class="btn btn-primary" type="submit">
                    					<button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                    				</div>
                    			</form>	
                			</div>
                		</div>
                	</div>
                
                	
                	<script>
		    function test(id,cusid){
		    $('#here').val(id);

		    }
		    function test1(id,cusid){
		    $('#here1').val(id);

		    }
		    function refundAmt(id,cusid,amt,dis_comm,mas_comm){
		    $('#pid').val(id);
            $('#cusid').val(cusid);
            $('#amt').val(amt);
            $('#dis_comm').val(dis_comm);
            $('#mas_comm').val(mas_comm);
		    }
		</script>
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>

</html>