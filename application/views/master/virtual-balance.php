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
						<h4 class="content-title mb-2">Wallet Management</h4>
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
							<div class="card-body">
							    <form action="<?php echo site_url('master/virtual-balance-success'); ?>" method="post" accept-charset="utf-8">				
    							    <div class="row">
                        				<div class="col-md-6 col-sm-8 col-xs-12">
                            				<div class="input-group">
                            				    <input type="number" class="form-control" name="amount" placeholder="Enter Amount" required/>
                                				<span class="input-group-btn">
                                				    <button type="submit" class="btn btn-main-primary"><i class="fa fa-plus"></i> Add Balance</button>
                                				</span>
                            				</div>
                        				</div>
                    				</div>
                    			</form>
							    <br><br>
							    
							    <div class="row">
							        <form action="<?php echo site_url('master/search-vb-report'); ?>" method="post" style="width: 100%;">
									    <div class="col-md-12 col-lg-12 col-xl-12 mx-auto d-block">
										<div class="card card-body pd-20 pd-md-40 border shadow-none">
										<div class="row">	
										    
											<div class="col-md-2">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="member_type" onchange="changeMembers(this)">
														<option value="all">All</option>
														<option value="retailer">Retailer</option>
														<option value="distributor">Distributor</option>
														<option value="master">Master Distributor</option>
														<option value="api">API Clients</option>
													</select>
    											</div>
											</div>
											
											<div class="col-md-3">
    											<div class="form-group">
    												<select class="form-control select2-no-search" name="member_id" id="members">
														<option label="Select Retailer"></option>
														<?php if($members){foreach($members as $cus){ ?>
														    <option value="<?php echo $cus->cus_id; ?>"><?php echo $cus->cus_name; ?> (<?php echo $cus->cus_id ?>)</option>
														<?php }} ?>
													</select>
    											</div>
											</div>
											
											<div class="col-md-3">
    											<div class="form-group">
    												<div class="input-group ">
                										<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control fc-datepicker" placeholder="MM/DD/YYYY" type="text">
                									</div>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<button class="btn btn-main-primary btn-block">Search</button>
    											</div>
											</div>
											
											<div class="col-md-2">
    											<div class="form-group">
    												<input type="reset" class="btn btn-main-primary btn-block"></button>
    											</div>
											</div>
										</div>
									</div>
								</div>
								    </form>
								</div>
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">Date</th>
												<th class="wd-20p border-bottom-0">Opening</th>
												<th class="wd-20p border-bottom-0">Closing</th>
												<th class="wd-20p border-bottom-0">Credit</th>
												<th class="wd-20p border-bottom-0">Debit</th>
												<th class="wd-20p border-bottom-0">Partner Name</th>
												<th class="wd-20p border-bottom-0">Partner Id</th>
												<th class="wd-20p border-bottom-0">Txn Type</th>
												<th class="wd-20p border-bottom-0">IP</th>
												<th class="wd-20p border-bottom-0">Paid/Due</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($vb)){$i=1;foreach($vb as $vb){  
										        if($vb->vb_agentid != 0){
                                					$cid = "SELECT * FROM customers where cus_id='".$vb->vb_agentid."'";
                                                    $custom =  $this->db->query($cid)->result_array();                                             
                                		            $memN = $custom[0]['cus_name'];
                                		            if($custom==''){$naM='Admin';}else{$naM=$memN;}
                            		            }else{
                            		                $naM='Admin';
                            		            }?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td style="width:250px"><b><?php echo ucfirst($vb->vb_date); ?></b></td>
												<td class="text-warning"><b>₹ <?php echo ucfirst($vb->vb_opbal); ?></b></td>
												<td class="text-info"><b>₹ <?php echo ucfirst($vb->vb_clbal); ?></b></td>
												<td class="text-success"><b>₹ <?php echo $vb->vb_crdt; ?></b></td>
												<td class="text-danger" ><b>₹ <?php echo $vb->vb_dbdt; ?></b></td>
												<td><b><?php echo ucfirst($naM); ?></b></td>
												<td><b><?php echo $vb->vb_agentid; ?></b></td>
												<td style="width:200px"><b><?php echo $vb->vb_type; ?></b></td>
												<td><?php echo $vb->vb_ip; ?></td>
												<td class="text-primary"><b><?php echo $vb->paid_due==1 ? 'PAID' : 'DUE' ; ?></b></td>
												
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
		<!-- /main-content -->
	
	   <?php $this->load->view('master/footer'); ?> 
	   
	   <script>
	    var changeMembers = memberType => {
	        var cus_type = memberType.value;
	        $.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>master/getMembersByType",
                cache: false,               
                data:{cus_type:cus_type,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
                success: function(res){
                    //alert(res);
                    $('#members').empty();
                    $('#members').append(res);
                }
            });
	    }
	</script>

	
	</body>

</html>