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
								<div class="row">
							
        							<div class="col-md-6">
        								<div class="box box-primary">
                                            <div class="box-header">
                                                <h3 class="box-title">Customer Details</h3>
                                            </div><!-- /.box-header -->
                                            <div class="box-body">
                                          
            							  <table width="99%" border="1" class="table table-bordered">
                                              <tr>
                                                <td width="200px;">Outlet Name</td>
                                                <td>:</td>
                                                <td width="300px;"><?php echo $r['info']->cus_outlate; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer Name</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_name; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Password</td>
                                                <td>:</td>
                                                <td><?php echo $this->encryption_model->decode($r['info']->cus_pass); ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Pin</td>
                                                <td>:</td>
                                                <td><?php echo $this->encryption_model->decode($r['info']->cus_pin); ?></td>
                                              </tr>
                                              
                                              <tr>
                                                <td>Customer Type</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_type; ?></td>
                                              </tr>
                            
                                              <?php
                                              if($r['info']->cus_type == 'distributor')
                                              {
                                              ?>
                                              <tr>
                                                <td>Master Distributer</td>
                                                <td>:</td>
                                                <td>
                                                <?php 
                                                $id = $r['info']->cus_reffer;
                                                $sql = "select * from customers where cus_id='$id'";
                                                $cd = $this->db_model->getAlldata($sql);
                                                echo '<strong>ID :'.$cd[0]->cus_id.'<br/>Name : '.$cd[0]->cus_name.'</strong>';
                                                ?>
                                                  
                                                </td>
                                              </tr> 
                            
                                              <tr>
                                                <td>Move All Retailer</td>
                                                <td>:</td>
                                                <td>
                                                <?php                    
                                                echo form_open('master/movedistributor');
                                                ?>
                                                <div class="row">
                                                  <div class="col-md-8 col-sm-12">
                                                    <select name="distb" class="form-control">
                                                    <?php 
                                                    $sql = "select * from customers where cus_type='distributor' and avability_status='0'";
                                                    $cd = $this->db_model->getAlldata($sql);
                                                    foreach($cd as $cds)
                                                    {
                                                    ?>
                                                    <option <?php echo ($cds->cus_id == $this->uri->segment(3) ? 'selected' : ''); ?> value="<?php echo $cds->cus_id; ?>"><?php echo $cds->cus_name.'-'.$this->encryption_model->decode($cds->cus_mobile); ?></option>
                                                    <?php  
                                                    }
                                                    ?>   
                                                    </select>
                                                  </div>
                                                  <input type="hidden" name="did" value="<?php echo $this->uri->segment(3);?>">
                                                  <div class="col-md-4 col-sm-12">
                                                    <input type="submit" class="btn btn-info" value="Submit">                   
                                                  </div>
                                                </div>
                                                <?php
                                                echo form_close(); 
                                                ?>
                                                </td>
                                              </tr> 
                            
                                              <?php 
                                              }
                                              elseif($r['info']->cus_type == 'retailer')
                                              {
                                              ?>
                                              <tr>
                                                <td>Move Retailer</td>
                                                <td>:</td>
                                                <td>
                                                <?php                    
                                                echo form_open('master/moveretailer');
                                                ?>
                                                <div class="row">
                                                  <div class="col-md-8 col-sm-12">
                                                    <select name="dist" class="form-control">
                                                    <?php 
                                                    $sql = "select * from customers where cus_type='distributor' and avability_status='0'";
                                                    $cd = $this->db_model->getAlldata($sql);
                                                    foreach($cd as $cds)
                                                    {
                                                    ?>
                                                    <option <?php echo ($cds->cus_id == $r['info']->cus_reffer ? 'selected' : ''); ?> value="<?php echo $cds->cus_id; ?>"><?php echo $cds->cus_name.'-'.$this->encryption_model->decode($cds->cus_mobile); ?></option>
                                                    <?php  
                                                    }
                                                    ?>   
                                                    </select>
                                                  </div>
                                                  <input type="hidden" name="mid" value="<?php echo $this->uri->segment(3);?>">
                                                  <div class="col-md-4 col-sm-12">
                                                    <button class="btn btn-main-primary">Submit</button>            
                                                  </div>
                                                </div>
                                                <?php
                                                echo form_close(); 
                                                ?>
                                                </td>
                                              </tr>
                            
                                              <tr>
                                                <td>Distributer</td>
                                                <td>:</td>
                                                <td>
                                                <?php 
                                                $id = $r['info']->cus_reffer;
                                                $sql = "select * from customers where cus_id='$id'";
                                                $cd = $this->db_model->getAlldata($sql);
                                                $dbid = $cd[0]->cus_reffer;
                                                echo '<strong>ID :'.$cd[0]->cus_id.'<br/>Name : '.$cd[0]->cus_name.'</strong>';
                                                ?>                      
                                                </td>
                                              </tr> 
                            
                                              <tr>
                                                <td>Master Distributer</td>
                                                <td>:</td>
                                                <td>
                                                <?php 
                                                $sql = "select * from customers where cus_id='$dbid'";
                                                $cd = $this->db_model->getAlldata($sql);
                                                echo '<strong>ID :'.$cd[0]->cus_id.'<br/>Name : '.$cd[0]->cus_name.'</strong>';
                                                ?>  
                                                </td>
                                              </tr>
                            
                                              <?php  
                                              }
                                              elseif($r['info']->cus_type == 'master')
                                              {
                                               ?>
                                              <tr>
                                                <td>Move All Distributor</td>
                                                <td>:</td>
                                                <td>
                                                <?php                    
                                                echo form_open('master/movemaster');
                                                ?>
                                                <div class="row">
                                                  <div class="col-md-8 col-sm-12">
                                                    <select name="mast" class="form-control">
                                                    <?php 
                                                    $sql = "select * from customers where cus_type='master' and avability_status='0'";
                                                    $cd = $this->db_model->getAlldata($sql);
                                                    foreach($cd as $cds)
                                                    {
                                                    ?>
                                                    <option <?php echo ($cds->cus_id == $this->uri->segment(3) ? 'selected' : ''); ?> value="<?php echo $cds->cus_id; ?>"><?php echo $cds->cus_name.'-'.$this->encryption_model->decode($cds->cus_mobile); ?></option>
                                                    <?php  
                                                    }
                                                    ?>   
                                                    </select>
                                                  </div>
                                                  <input type="hidden" name="mid" value="<?php echo $this->uri->segment(3);?>">
                                                  <div class="col-md-4 col-sm-12">
                                                    <input type="submit" class="btn btn-sm" value="Submit">                   
                                                  </div>
                                                </div>
                                                <?php
                                                echo form_close(); 
                                                ?>
                                                </td>
                                              </tr>                   
                                              <?php 
                                              }
                                              ?>
                            
                                              
                            
                                              <tr>
                                                <td>Customer Gmail</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_email; ?></td>
                                              </tr>
                                              
                                              <tr>
                                                <td>Customer Address</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_address; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer  Mobile</td>
                                                <td>:</td>
                                                <td><?php echo $this->encryption_model->decode($r['info']->cus_mobile); ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer Date Of Birth</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_dob; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer Sex</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_sex; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer Country</td>
                                                <td>:</td>
                                                <td><?php echo $r['country']; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer State</td>
                                                <td>:</td>
                                                <td><?php echo $r['state']; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer City</td>
                                                <td>:</td>
                                                <td><?php echo $r['city']; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer Pincode</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_pincode; ?></td>
                                              </tr>
                            
                            
                                              <tr>
                                                <td>Account Type</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_account_type; ?></td>
                                              </tr>
                            
                            
                                              <tr>
                                                <td>Account Number</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_account_no; ?></td>
                                              </tr>
                            
                            
                                              <tr>
                                                <td>IFSC Code</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_ifsc; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Pancard Number</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_panno; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Id Proof</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->id_proof; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Id Proof Code</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->id_proof_code; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>Proof Image</td>
                                                <td>:</td>
                                                <td>
                                                <p class="text-danger">(Click on image to download.)</p>
                                                <?php
                                                if(!empty($r['image']))
                                                {
                                                  foreach($r['image'] as $img)
                                                  {
                                                    ?>
                                                    <a download="<?php echo substr(base_url().$img->gallery_path, strrpos(base_url().$img->gallery_path, '/') + 1); ?>" href="<?php echo base_url().$img->gallery_path; ?>" title="ImageName">
                                                      <img alt="ImageName" class="img-thumbnail" src="<?php echo base_url().$img->gallery_path; ?>">
                                                    </a>
                                                    <?php  
                                                  }
                                                }
                                                ?>                    
                                                </td>
                                              </tr>
                            
                                              <tr>
                                                <td>Signature Upload</td>
                                                <td>:</td> 
                                                <td>
                                                <?php
                                                if($r['info']->cus_signature != '')
                                                {
                                                ?>
                                                <p class="text-danger">(Click on image to download.)</p>
                                                <a download="<?php echo substr(base_url().$r['info']->cus_signature, strrpos(base_url().$r['info']->cus_signature, '/') + 1); ?>" href="<?php echo base_url().$r['info']->cus_signature; ?>" title="ImageName">
                                                  <img alt="ImageName" class="img-thumbnail" src="<?php echo base_url().$r['info']->cus_signature; ?>">
                                                </a>                  
                                                <?php  
                                                }
                                                ?>                      
                                                </td>
                                              </tr>
                            
                                              <tr>
                                                <td>Photo</td>
                                                <td>:</td>
                                                <td>
                                                <?php
                                                if($r['info']->profile_img != '')
                                                {
                                                ?>
                                                <p class="text-danger">(Click on image to download.)</p>
                                                  <a download="<?php echo substr(base_url().$r['info']->profile_img, strrpos(base_url().$r['info']->profile_img, '/') + 1); ?>" href="<?php echo base_url().$r['info']->profile_img; ?>" title="ImageName">
                                                  <img alt="ImageName" class="img-thumbnail" src="<?php echo base_url().$r['info']->profile_img; ?>">
                                                  </a>   
                                                <?php
                                                }
                                                ?>              
                                                </td>
                                              </tr>
                            
                                              <tr>
                                                <td>Address Proof Image</td>
                                                <td>:</td>
                                                <td>
                                                <?php
                                                if($r['info']->cus_address_proof != '')
                                                {
                                                ?>
                                                <p class="text-danger">(Click on image to download.)</p>
                                                  <a download="<?php echo substr(base_url().$r['info']->cus_address_proof, strrpos(base_url().$r['info']->cus_address_proof, '/') + 1); ?>" href="<?php echo base_url().$r['info']->cus_address_proof; ?>" title="ImageName">
                                                  <img alt="ImageName" class="img-thumbnail" src="<?php echo base_url().$r['info']->cus_address_proof; ?>">
                                                  </a> 
                                                <?php
                                                }
                                                ?>                   
                                                </td>
                                              </tr>
                            
                                              <tr>
                                                <td>Pancard Copy Image</td>
                                                <td>:</td>
                                                <td>
                                                <?php
                                                if($r['info']->cus_pancard_copy != '')
                                                {
                                                ?>
                                                <p class="text-danger">(Click on image to download.)</p>
                                                  <a download="<?php echo substr(base_url().$r['info']->cus_pancard_copy, strrpos(base_url().$r['info']->cus_pancard_copy, '/') + 1); ?>" href="<?php echo base_url().$r['info']->cus_pancard_copy; ?>" title="ImageName">
                                                  <img alt="ImageName" class="img-thumbnail" src="<?php echo base_url().$r['info']->cus_pancard_copy; ?>">
                                                  </a>   
                                                <?php
                                                }
                                                ?>          
                                                </td>
                                              </tr>
                            
                                              <tr>
                                                <td>Customer Joining Date</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_added_date; ?></td>
                                              </tr>
                                              <tr>
                                                <td>Customer Cut of Amount</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_cutofamt; ?></td>
                                              </tr>
                                              <tr>
                                                <td>Customer IP</td>
                                                <td>:</td>
                                                <td><?php echo $r['info']->cus_ip; ?></td>
                                              </tr>
                            
                                              <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><?php 
                                                if($r['info']->avability_status !='1')
                                                {
                                                  echo "<a href='".base_url().'master/in-active-customer/'.$r['info']->cus_id."' class='btn btn-success btn-flat' >Active</a>";  
                                                }
                                                else
                                                {
                                                  echo "<a href='".base_url().'master/active-customer/'.$r['info']->cus_id."' class='btn btn-danger btn-flat' >In Active</a>";
                                                }
                                                ?></td>
                                              </tr>
                                              
                                            </table>
            
                            							  
                            							  
                            							            
                            										
                                        </div>
        							    </div>
        						    </div>
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
	
	   <?php $this->load->view('master/footer'); ?> 
	
	</body>


</html>