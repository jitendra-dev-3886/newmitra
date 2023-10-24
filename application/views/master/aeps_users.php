<?php $page = $this->uri->segment('2'); ?>

<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('master/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('master/loader'); ?>

		<?php $this->load->view('master/header'); ?>
		
		
		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
			    
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Aeps Member Management</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">HOME</a></li>
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
							</ol>
						</nav>
					</div><!--
					<div class="d-flex my-auto">
						<div class=" d-flex right-page">
							<div class="d-flex justify-content-center mr-5">
								<div class="">
									<span class="d-block">
										<span class="label ">SUCCESS</span>
									</span>
									<span class="value">
										₹ 0
									</span>
								</div>
								<div class="ml-3 mt-2">
									<span class="sparkline_bar"></span>
								</div>
							</div>
							<div class="d-flex justify-content-center">
								<div class="">
									<span class="d-block">
										<span class="label">FAILED</span>
									</span>
									<span class="value">
										₹ 0
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
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">Name</th>
												<th class="wd-15p border-bottom-0">Contact</th>
												<th class="wd-10p border-bottom-0">Type</th>
												<th class="wd-20p border-bottom-0">Bank Details</th>
												<th class="wd-10p border-bottom-0">Personal Details</th>
												<th class="wd-25p border-bottom-0">Status</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($aepsUser)){foreach($aepsUser as $rec){ ?>
    											<tr>
    												<td style="width:200px">
    												    <b><?php echo ucfirst($rec->cus_name); ?><br>
    												    <?php echo $rec->cus_id; ?><br></b>
    												    
    												   <!-- <span class="text-info"><?php $sql3 = "select * from aeps_commission_slab where aeps_comm_id='".$rec->aeps_comm_id."'";
                                                        	$checkst2 = $this->db->query($sql3)->result_array();                                             
                                                            $pack = $checkst2[0]['slab'];
                                                            $pk= "Package: ";
                                                            if($checkst2){ echo $pk.$pack;}else{ ?>
                                                            <i class="fa fa-edit" value="" onclick="test(<?php echo $rec->cus_id;?>)" class="btn ripple btn-primary" data-target="#modaldemo1" data-toggle="modal"></i>
                                                            <?php }?>
                                                            </span>-->
                                                            
    												</td>
    												
    												<td><b><?php echo $rec->cus_email; ?><br><?php echo ucfirst($this->encryption_model->decode($rec->cus_mobile)); ?></b></td>
    												    												
    												<td class="text-primary"><b><?php echo ucfirst($rec->cus_type); ?></b></td>

    												<td><b><?php echo $rec->bankName;?><br><?php echo $rec->aeps_AccountNumber;?><br><?php echo $rec->aeps_bankIfscCode;?></b></td>
    												
    												<td><b><?php echo "PAN :- $rec->aeps_userPan";?><br><?php echo "Adhar :- $rec->aeps_aadhaarNumber";?></b></td>
    												
    												<td><b><?php echo $rec->aeps_kyc_status;?></b></td>
    											</tr>
											<?php }} ?>
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
                				<form action="<?php echo base_url()?>master/aepsPackage_assign" method="post">
                    				<div class="modal-header">
                    					<h6 class="modal-title">Assign Package</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    				</div>
                    				<div class="modal-body">
                    					<input type="hidden" id="here" name="cus_id" >
                    					<div class="form-group">
                    					    <label>Select Package</label>
                    					    <select class="form-control" name="pk_id">
                    					        <option value="">select</option>
                    					        <?php if(is_array($package)){ foreach($package as $p) { ?>
                    					        <option value="<?php echo $p->aeps_comm_id;?>"><?php echo $p->slab;?></option>
                    					        <?php }}?>
                    					    </select>
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


				</div>
				<!-- /row -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
	    <script>
		    function test(id){
		    $('#here').val(id);
		    }
		</script>
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>


</html>