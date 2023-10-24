<?php $page = $this->uri->segment('2'); ?>

<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('distributor/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('distributor/loader'); ?>

		<?php $this->load->view('distributor/header'); ?>
		
		
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
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
							</ol>
						</nav>
					</div>
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
					</div>
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
												<th class="wd-20p border-bottom-0">Sponsor</th>
												<th class="wd-10p border-bottom-0">Status</th>
												<th class="wd-10p border-bottom-0">App Login Status</th>
												<th class="wd-25p border-bottom-0">Balance</th>
												<th class="wd-15p border-bottom-0">Date of Join</th>
												<th class="wd-25p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($members)){foreach($members as $rec){ ?>
    											<tr>
    												<td>
    												    <b><?php echo ucfirst($rec->cus_name); ?><br>
    												    <?php echo $rec->cus_id; ?></b>
    												</td>
    												
    												<td><b><?php echo $rec->cus_email; ?><br><?php echo ucfirst($this->encryption_model->decode($rec->cus_mobile)); ?></b></td>
    												    												
    												<td class="text-primary"><b><?php echo ucfirst($rec->cus_type); ?></b></td>

    												<td><b><?php
                                                        $sql2 = "select * from customers where cus_id='".$rec->cus_reffer."'";
                                                        $checkst = $this->db->query($sql2)->result_array();     
                                                        if($checkst){
                                                            $dname= $checkst[0]['cus_name'];
                                                            $custy= $checkst[0]['cus_type'];
										                }
                                                        if($rec->cus_type=='retailer' || $rec->cus_type=='api' || $rec->cus_type=='distributor'){
                                                            echo ucfirst($dname) ." "."(".ucfirst($custy).")"; }
                                                        elseif($rec->cus_type=='master') {echo "Company"; }
                                                        elseif($rec->cus_type=='customer') { echo "Online"; }
                                                        ?></b></td>
    												
    												<?php if($rec->avability_status == '0'){ ?>
    												    <td class="text-success"><b>Active</b></td>
        											<?php }else{ ?>
    												    <td class="text-danger"><b>Inactive</b></td>
    												<?php } ?>
    												
    												<?php if($rec->login_status == 'loggedin'){ ?>
    												    <td class="text-success"><b>Logged In</b></td>
        											<?php }else{ ?>
    												    <td class="text-danger"><b>Logged Out</b></td>
    												<?php } ?>
    												
    												<td class="text-info"><b>₹ <?php 
                                                    	$sql9 = "select * from exr_trnx where txn_agentid=".$rec->cus_id." order by txn_id desc limit 1";
                                                        $ty = $this->db_model->getAlldata($sql9);
                                                        if($ty)
                                                        {
                                                            echo $data['wallet'] = $ty[0]->txn_clbal;
                                                        }
                                                        else
                                                        {
                                                            echo $data['wallet'] = 0;    
                                                        }
                                                    ?></b></td>
    												
    												<td style="width:200px"><b><?php echo $rec->cus_added_date; ?></b></td>
    												
    												<td style=" width: 200px;"><b>
    												    <a href="<?php echo base_url(); ?>distributor/member-details/<?php echo $rec->cus_id; ?>"><i class="typcn typcn-eye text-info"></i></a>
    												    <a href="<?php echo base_url(); ?>distributor/edit-member/<?php echo $rec->cus_id; ?>"><i class="typcn typcn-edit text-success"></i></a>
    												    <i class="typcn typcn-arrow-repeat text-primary"></i>
    												    <a href="<?php echo base_url();?>distributor/delete-account/<?php echo $rec->cus_id; ?>" onclick="return confirm('Are you sure You want to Delete User Id <?php echo $rec->cus_id; ?>?')" ><i class="typcn typcn-trash text-danger"></i></a> 
    												   <a href="<?php echo base_url(); ?>distributor/logout-member/<?php echo $rec->cus_id; ?>" onclick="return confirm('Are you sure You want to Logout This User?')" > <i class="typcn typcn-power-outline text-danger"></i> </a>
    												</b></td>
    												
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
	
	   <?php $this->load->view('distributor/footer'); ?> 
	
	</body>


</html>