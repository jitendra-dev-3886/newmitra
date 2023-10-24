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
				<!-- row -->
				<div class="row">
					<div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
							    <div class="main-content-label mg-b-5">
									Recharge Details
								</div><br>
								<div class="row">


								<div class="col-md-6">
                                <div class="box-body">								
                                      <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Recharge Id</td>
                                                <td><?php echo $v[0][0]->recid; ?></td>
                                            </tr> 
                                            <tr>
                                                <td>User Id</td>
                                                <td><?php echo $v[0][0]->apiclid; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Txn Date</td>
                                                <td><?php echo date("d-m-Y h:i:s a", $v[0][0]->restime);?></td>
                                            </tr>
                                            <tr>
                                                <td>Mobile No</td>
                                                <td><b style="font-size: 18px;"><?php echo $v[0][0]->mobileno;?></b></td>
                                            </tr> 
                                            <tr>
                                                <td>Operator</td>
                                                <td><?php echo $v[1][0]->operatorname;?></td>
                                            </tr> 
                                            <tr>
                                                <td>Amount</td>
                                                <td><b style="font-size: 18px;"><?php echo $v[0][0]->amount;?></b></td>
                                            </tr> 
                                            <tr>
                                                <td>Status</td>
                                                <td><?php echo $v[0][0]->status;?></td>
                                            </tr>
                                            <tr>
                                                <td>Opt ID</td>
                                                <td><?php echo $v[0][0]->statusdesc;?></td>
                                            </tr>
                                            <tr>
                                                <td>Medium</td>
                                                <td><?php echo $v[0][0]->recmedium;?></td>
                                            </tr> 
                                            <tr>
                                                <td>API Source</td>
                                                <td><?php 
                                                $sql1 = "select * from apisource where apisourceid='".$v[0][0]->apisourceid."'";
                                                $api = $this->db->query($sql1)->result_array();                                             
                                                echo $api[0]['apisourcecode'];
                                                 ?></td>
                                            </tr>
                                            <tr>
                                                <td>Response</td>
                                                <td><?php echo $v[0][0]->api_msg;?></td>
                                            </tr>
                                        </tbody>										
                                    </table>
									</div>
                                </div><!-- /.box-body -->

                                <div class="col-md-6">   
                                    <?php
                                    if($v[0][0]->status != 'Null')
                                    {
                                    ?>
                                    <div class="main-content-label mg-b-5">
    									Update Recharge Status
    								</div>
                                    <hr/>
                                    <div class="box-body">                              
                                        <div class="col-xs-12 col-md-12">  
                                            <?php 
                                            echo $this->session->flashdata('msg');
                                            $data = array('id'=>'trans');
                                            echo form_open('admin/update-recharge-status-success',$data);
                                            ?>
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group">
                                                        <select class="form-control" name="status" required="">
                                                            <option value="">Status</option>
                                                            <option value="1">Success</option>
                                                            <option value="2">Failed</option>
                                                        </select>             
                                                    </div>      
                                                </div>

                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group">
                                                        <input type="text" name="transaction" id="transaction" class="form-control" placeholder="Operator txn id">            
                                                    </div>      
                                                </div>

                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group">
                                                        <input type="hidden" name="api" id="apiid" class="form-control" value="<?php echo $v[0][0]->apiclid; ?>">
                                                    </div>      
                                                </div>

                                                <input type="hidden" value="<?php echo $v[0][0]->recid; ?>" name="id">

                                                <div class="col-xs-12 col-md-12">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-main-primary btn-flat btn-block" >UPDATE</button>      
                                                    </div>      
                                                </div>

                                            <?php
                                            echo form_close();
                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                </div><!-- /.box-body -->
							
                                </div>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>

</html>