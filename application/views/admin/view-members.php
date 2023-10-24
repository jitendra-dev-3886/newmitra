<?php $page = $this->uri->segment('2'); ?>

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
								<li class="breadcrumb-item active" aria-current="page"><?php echo strtoupper($page); ?></li>
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
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example">
										<thead>
											<tr>
											    
												<th class="wd-5p border-bottom-0">#</th>
												<th class="wd-5p border-bottom-0">Name</th>
												<th class="wd-5p border-bottom-0">Contact</th>
												<th class="wd-5p border-bottom-0">Type</th>
												<th class="wd-10p border-bottom-0">Sponsor</th>
												<th class="wd-10p border-bottom-0">Status</th>
												<th class="wd-10p border-bottom-0">1rs</th>
												<th class="wd-10p border-bottom-0">App Login Status</th>
												<th class="wd-10p border-bottom-0">Web Login Status</th>
												<th class="wd-10p border-bottom-0">Balance</th>
												<th class="wd-10p border-bottom-0">Aeps Wallet</th>
												<th class="wd-10p border-bottom-0">Date of Join</th>
												<th class="wd-10p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(is_array($members)){$i=0;foreach($members as $rec){ ?>
    											<tr>
    											    <td><?php echo ++$i; ?></td>
    												<td style="width:200px">
    												    <b><?php echo ucfirst($rec->cus_name); ?><br>
    												    <?php echo $rec->cus_id; ?><br>
    												    <span class="text-info"><?php $sql3 = "select * from commission_scheme where scheme_id='".$rec->scheme_id."'";
                                                        	$checkst2 = $this->db->query($sql3)->result_array();                                             
                                                            $pack = $checkst2[0]['scheme_name'];
                                                            $pk= "Package: ";
                                                            if($checkst2) echo $pk.$pack; ?></span>
                                                        </b>                 
    												</td>
    												
    												<td><b><?php echo $rec->cus_email; ?><br><?php echo ucfirst($this->encryption_model->decode($rec->cus_mobile)); ?></b></td>
    												    												
    												<td class="text-primary"><b><?php echo ucfirst($rec->cus_type); ?></b></td>

    												<td><b><?php
        												if($rec->cus_reffer != 0){
                                                            $sql2 = "select * from customers where cus_id='".$rec->cus_reffer."'";
                                                            $checkst = $this->db->query($sql2)->result_array();     
                                                            if($checkst){
                                                                $dname= $checkst[0]['cus_name'];
                                                                $custy= $checkst[0]['cus_type'];
    										                }
                                                            if(($rec->cus_type=='retailer' || $rec->cus_type=='api' || $rec->cus_type=='distributor') && ($dname != NULL)){
                                                                echo ucfirst($dname) ." "."(".ucfirst($custy).")"; }
                                                            elseif($rec->cus_type=='master') {echo "Admin"; }
                                                            elseif($rec->cus_type=='customer') { echo "Online"; }
                                                            else{echo "Admin";}
        												}else{
        												    echo "Admin";
        												}
                                                        ?></b></td>
    												
    												<?php if($rec->avability_status == '0'){ ?>
    												    <td class="text-success"><b>Active</b></td>
        											<?php }else{ ?>
    												    <td class="text-danger"><b>Inactive</b></td>
    												<?php } ?>
    												
    												<td>
    												    <?php if($rec->recharge_rs_1=='1'){?>
    												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="0" checked onchange="recharge1_update(this.value,<?php echo $rec->cus_id; ?>,'recharge_rs_1')">
    												    <?php }else{?>
    												    <input type="checkbox" class="form-control" style="height: 20px;" name="permission" value="1" onchange="recharge1_update(this.value,<?php echo $rec->cus_id; ?>,'recharge_rs_1')">
    												    <?php }?>
												    </td>
    												
    												<?php if($rec->login_status == 'loggedin'){ ?>
    												    <td class="text-success"><b>Logged In</b>
    												    <a href="<?php echo base_url(); ?>admin/logout-member-app/<?php echo $rec->cus_id; ?>" onclick="return confirm('Are you sure You want to Logout This User?')" > <i class="typcn typcn-power-outline text-danger"></i> </a>
        											    </td>
        											<?php }else{ ?>
    												    <td class="text-danger"><b>Logged Out</b></td>
    												<?php } ?>
    												
    												<?php if($rec->web_login_status == 'loggedin'){ ?>
    												    <td class="text-success"><b>Logged In</b>
    												    <a href="<?php echo base_url(); ?>admin/logout-member-web/<?php echo $rec->cus_id; ?>" onclick="return confirm('Are you sure You want to Logout This User?')" > <i class="typcn typcn-power-outline text-danger"></i> </a>
        										        </td>
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
                                                    <td class="text-info"><b>₹ 
                                                        
                                                        <?php
                                                            $sqlaeps = "SELECT * FROM `aeps_exr_trnx` WHERE aeps_txn_agentid='".$rec->cus_id."' ORDER BY aeps_txn_id DESC LIMIT 1";
                                                            $aepswallet = $this->db_model->getAlldata($sqlaeps);
                                                            //echo $this->db->last_query();
                                                            if($aepswallet)
                                                            {
                                                                echo $data['aepswallet'] = $aepswallet[0]->aeps_txn_clbal;
                                                            }
                                                            else
                                                            {
                                                                echo $data['aepswallet'] = 0;    
                                                            }
                                                            ?>
                                                    </b></td>
    												
    												<td style="width:200px"><b><?php echo $rec->cus_added_date; ?></b></td>
    												
    												<td style=" width: 200px;"><b>
    					
    												    <a href="<?php echo base_url(); ?>admin/resend-pass/<?php echo $rec->cus_id; ?>"><i class="typcn typcn-mail text-success"></i></a>
    												    <a href="<?php echo base_url(); ?>admin/member-details/<?php echo $rec->cus_id; ?>"><i class="typcn typcn-eye text-info"></i></a>
    												    <a href="<?php echo base_url(); ?>admin/edit-member/<?php echo $rec->cus_id; ?>"><i class="typcn typcn-edit text-success"></i></a>
    												   <!-- <i class="typcn typcn-arrow-repeat text-primary"></i>-->
    												    <a href="<?php echo base_url();?>admin/delete-account/<?php echo $rec->cus_id; ?>" onclick="return confirm('Are you sure You want to Delete User Id <?php echo $rec->cus_id; ?>?')" ><i class="typcn typcn-trash text-danger"></i></a> 
    												    <a href="<?php echo base_url()?>admin/switch-account/<?php echo $rec->cus_id; ?>"><i class="typcn typcn-weather-snow text-warning"></i></a><!--
    												    <a href="<?php echo base_url(); ?>admin/logout-member/<?php echo $rec->cus_id; ?>" onclick="return confirm('Are you sure You want to Logout This User?')" > <i class="typcn typcn-power-outline text-danger"></i> </a>-->
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
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
    <script type="application/javascript">
        var recharge1_update = (per,id,column) =>{
            $.ajax({
				url: '<?php echo base_url(); ?>admin/update_recharge1',
				type: 'POST',
				cache: false,
				data: {per:per,id:id,column:column},
				success: function(res) 
				{
					alert(res);	
					location.reload();
				}
			});	 				
		}
    </script>

</html>