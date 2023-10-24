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
						<h4 class="content-title mb-2">Fun Requests</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($this->uri->segment('2')); ?></li>
							</ol>
						</nav>
					</div>
				</div>
				<!-- /breadcrumb -->
				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-body">
							    <div class="row">
							        <form action="<?php echo site_url('admin/search-fund-request'); ?>" method="post" style="width: 100%;">
									    <div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
    										<div class="card card-body pd-20 pd-md-40 border shadow-none">
    										    <div class="row">	
        											<div class="col-md-4">
            											<div class="form-group">
            												<div class="input-group ">
                        										<div class="input-group-prepend">
                        											<div class="input-group-text">
                        												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>&nbsp;FROM
                        											</div>
                        										</div>
                        										<input class="form-control fc-datepicker hasDatepicker" placeholder="MM/DD/YYYY" type="date" id="dp1612569947060" name="from_dt">
                        									</div>
            											</div>
        											</div>
        											
        											<div class="col-md-4">
            											<div class="form-group">
            												<div class="input-group ">
                        										<div class="input-group-prepend">
                        											<div class="input-group-text">
                        												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>&nbsp;TO
                        											</div>
                        										</div>
                        										<input class="form-control fc-datepicker hasDatepicker" placeholder="MM/DD/YYYY" type="date" id="dp1612569947060" name="to_dt">
                        									</div>
            											</div>
        											</div>
        											<div class="col-md-4">
            											<div class="form-group">
            												<button class="btn btn-main-primary btn-block">Search</button>
            											</div>
        											</div>
        										</div>
        									</div>
    								    </div>
								    </form>
								</div>
								<div class="table-responsive">
									<table id="example" class="table key-buttons text-md-nowrap">
										
										<thead>
											<tr>
												<th class="wd-5p border-bottom-0">#</th>
												<th class="wd-15p border-bottom-0">Member</th>
												<th class="wd-15p border-bottom-0">Bank Details</th>
												<th class="wd-15p border-bottom-0">Status</th>
												<th class="wd-20p border-bottom-0">Amount</th>
												<th class="wd-20p border-bottom-0">Payment Mode</th>
												<th class="wd-15p border-bottom-0">Requested Date</th>
												<th class="wd-15p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if($ledger){ $i= 1; foreach($ledger as $rec){ ?> 
											<tr>
												<td><?php echo $i++ ?><br></td>
												<td><b><?php echo ucfirst($rec->cus_name).'('.$rec->cus_id.')' ?> <br><?php echo $this->encryption_model->decode($rec->cus_mobile); ?></b></td>
												<td class="text-center">
												    <?php if($rec->pay_bank) { $bank = $this->db_model->getAlldata("select * from bank_details as bd,fund_request as fr where fr.pay_bank = bd.bank_id ")  ?>
												    <b>
												    <?php echo $rec->bank_name ?><br><?php echo $bank[0]->bank_account ?><br><?php echo $bank[0]->account_id ?><br><?php echo $bank[0]->ifsc_code ?>
												    </b>
												    <?php }else{ ?>
												    <b>0</b>
												    <?php }?>
												</td>
												<td class="<?php if($rec->req_status == 'ACCEPTED'){ echo 'text-success';}else if($rec->req_status == 'PENDING'){ echo 'text-warning';}else{ echo 'text-danger';} ?>"><b><?php echo $rec->req_status; ?></b></td>
												<td class="text-success"><b>₹ <?php echo $rec->pay_amount ?></b></td>
												<td style="width:250px"><b>
                                                    <?php
                                                    switch ($rec->pay_mode) {
                                                        case '1':
                                                            echo '<span>Cash Deposit</span>';
                                                            break;
                                                        case '2':
                                                            echo '<span>Cheque Deposit</span>';
                                                            break;
                                                        case '3':
                                                            echo '<span>Online Transfer (IMPS/NEFT/RTGS)</span>';
                                                            break;
                                                        case '4':
                                                            echo '<spanr>ATM Transfer</span>';
                                                            break;
                                                        default:
                                                            echo 'No Bank selected';
                                                    }
                                                    ?>
                                                </b></td>
												<td style="width:250px"><b><?php echo $rec->req_date; ?></b></td>
												<td class="text-center">
												    <?php if($rec->req_status == "PENDING"){ ?>
    												    <a href="<?php echo base_url()?>admin/fund-request-accept/<?php echo $rec->req_id?>"><i class="fa fa-check text-success"></i></a>&nbsp;&nbsp;&nbsp;
    												    <i onclick="test(<?php echo $rec->req_id;?>,<?php echo $rec->cus_id;?>)" class="fa fa-window-close text-danger"  data-target="#modaldemo1" data-toggle="modal"></i>
												    <?php }else{ ?> 
												        <span class="text-info"><?php echo "REQUEST $rec->req_status ";?></span>
												    <?php } ?>
												</td>
												
											</tr>
											<?php }} ?>
										</tbody>
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
    				<form action="<?php echo base_url()?>admin/fund_request_reject" method="post">
        				<div class="modal-header">
        					<h6 class="modal-title">Reject Request</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
        				</div>
        				<div class="modal-body">
        					<input type="hidden"  >
            					<div class="form-group">
            					    <label>ID</label>
            					    <input type="text" id="here" name="id" class="form-control" readonly required>
            					    <input type="hidden" id="cusid" name="cusid" class="form-control" >
            					</div>
            					<div class="form-group">
            					    <label>Reason</label>
            					    <textarea class="form-control" name="reason"  required></textarea>
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
		    function test(id,cusid){
		    $('#here').val(id);
		    $('#cusid').val(cusid);
		    
		    }
		</script>
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
</html>