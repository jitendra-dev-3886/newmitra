
<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('admin/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('admin/loader'); ?>

		<?php $this->load->view('admin/header'); ?>
		
		<!-- main-content opened -->
		<div class="main-content horizontal-content">
		    <div class="container">
		        <div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Add API</h4>
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
			</div>
                
        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <!--<h4 class="mb-sm-0 font-size-18">Add API</h4>-->
                            <div class="page-title-right">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <div class="wrapper row-offcanvas row-offcanvas-left">
                    
                    <section class="content-header">
                    <h1>
                        
                    </h1>
                    <!--<ol class="breadcrumb">-->
                    <!--    <li><a href="<?php echo base_url(); ?>admin/index"><i class="fa fa-dashboard"></i>Home</a></li>-->
                    <!--    <li><a href="#">Examples</a></li>-->
                    <!--    <li class="active">Update Bank Details</li>-->
                    <!--</ol>-->
                </section>
				
                <!-- Main content -->
                <div class="page-content">
                <div class="container-fluid">
                
                <section class="content">
                        
						<div class="row">
							
							<div class="col-md-12">
									
								<div class="box box-primary">
								
                                <div class="box-header">
                                    <h3 class="box-title">Update Bank Details</h3>
                                </div><!-- /.box-header -->
                                   <?php echo form_open_multipart('admin/update_api_succ')?>
                                   
                                   
                                   
								   <div class="col-md-6">
                                    <div class="box-body">
                                       
                                            <?php 
                                            if(is_array($api)){foreach($api as $a){
        												            $apiname = $a->apisourcename;
        												            $bal_api_url = $a->bal_api_url;
        												            $api_url = $a->api_url;
        												            $operator = $a->api_operator;
        												            $mobile = $a->api_mobile;
        												            $amount = $a->api_amount;
        												            $txn_id = $a->api_txn_id;
        												            $optional = $a->api_optional;
        												            $succ_resp = $a->succ_resp;
        												            $fail_resp = $a->fail_resp;
        												            $pending_resp = $a->pending_resp;
        												            $api_resp_type = $a->api_resp_type;
        												            $api_hit_type = $a->api_hit_type; 
        												            $op_txid_resp = $a->op_txid_resp;
        												            $status_resp = $a->status_resp;
        												            $apisourceid = $a->apisourceid; 
        												            
        												        }
    												        }
                                        ?>                          
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">API Source Name</label>
                                      <input type="text" name="apisourcename" class="form-control" id="exampleInputCategory1" placeholder="API Source Name" value="<?php echo $apiname; ?>" required>
                                    </div>  

                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Balance API Url</label>
                                      <input type="text" name="bal_api_url" class="form-control" id="exampleInputCategory1" placeholder="Balance API Url" value="<?php echo $bal_api_url; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">API URL</label>
                                      <input type="text" name="api_url" class="form-control" id="exampleInputCategory1" placeholder="http:google.com/pagename?username=XXXX&password=XXXX"  value="<?php echo $api_url; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Operator Parameter</label>
                                      <input type="text" name="operator" class="form-control" id="exampleInputCategory1" placeholder="Operator Parameter" value="<?php echo $operator; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Mobile Parameter</label>
                                      <input type="text" name="mobile" class="form-control" id="exampleInputCategory1" placeholder="Mobile Parameter" value="<?php echo $mobile; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Amount Parameter</label>
                                      <input type="text" name="amount" class="form-control" id="exampleInputCategory1" placeholder="Amount Parameter" value="<?php echo $amount; ?>" required>
                                    </div>
                                    
                                     <div class="form-group">
                                      <label for="exampleInputCategory1">Txn Id Parameter</label>
                                      <input type="text" name="txn_id" class="form-control" id="exampleInputCategory1" placeholder="Txn Id Parameter" value="<?php echo $txn_id; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Optional Parameter</label>
                                      <input type="text" name="optional" class="form-control" id="exampleInputCategory1" placeholder="Optional Parameter" value="<?php echo $optional; ?>" required>
                                    </div>
                                    </div>
                                    </div>
                                    
                                    <div class="col-md-6"><div class="box-body">
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Success Response</label>
                                      <input type="text" name="succ_resp" class="form-control" id="exampleInputCategory1" placeholder="Success Response" value="<?php echo $succ_resp; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Failure Response</label>
                                      <input type="text" name="fail_resp" class="form-control" id="exampleInputCategory1" placeholder="Failure Response" value="<?php echo $fail_resp; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Pending  Response</label>
                                      <input type="text" name="pending_resp" class="form-control" id="exampleInputCategory1" placeholder="Pending  Response" value="<?php echo $pending_resp; ?>" required>
                                    </div>

                                   <div class="form-group">
                                      <label for="exampleInputCategory1">API Response Type</label>
                                      <select name="api_resp_type" class="form-control"  required>
                  									  	<option value="">Select API Response Type</option>
                  									  	<option value="json"<?php if( $api_resp_type == "json" ) echo 'selected="selected"' ; ?> >JSON</option>
                                        <option value="xml" <?php if( $api_resp_type == "xml" ) echo 'selected="selected"' ; ?> >XML</option>
                                        <option value="string" <?php if( $api_resp_type == "string" ) echo 'selected="selected"' ; ?> >STRING</option>                                   
                  						</select>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">API HIT type</label>
                                      <select name="api_hit_type" class="form-control" required>
                  									  	<option value="">Select API HIT Type</option>
                  									  	<option value="get" <?php if( $api_hit_type == "get" ) echo 'selected="selected"' ; ?> >GET</option>
                                        <option value="post" <?php if( $api_hit_type == "post" ) echo 'selected="selected"' ; ?> >POST</option>                                  
                  						</select>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Operator TXID Response</label>
                                      <input type="text" name="op_txid_resp" class="form-control" id="exampleInputCategory1" value="<?php echo $op_txid_resp; ?>" placeholder="Operator TXID Response" required>
                                    </div>
                                    
                                    <div class="form-group">
                                      <label for="exampleInputCategory1">Status Response</label>
                                      <input type="text" name="status_resp" class="form-control" id="exampleInputCategory1" value="<?php echo $status_resp; ?>" placeholder="Status Response" required>
                                    </div>
                                  </div> 
                                  
                                  <input type="hidden" value="<?php echo $apisourceid;?>" name="apisourceid">

                                    <div class="box-footer">
									<button type="submit" value="Add Section" class="btn btn-primary"><i class="glyphicon glyphicon-user"></i>&nbsp;Update API Details</button>
                                        <a href="<?php echo base_url();?>admin/view-all-api"><button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Discard</button></a>
                       				</div>

                                    <?php echo form_close();?>
                                   </div>
								
							</div>
							
						</div>
                    
                </section><!-- /.content -->
        </div><!-- ./wrapper -->
                </section>
                    
                </div>
                </div>
                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <!-- End Page-content -->
        <?php include('footer.php');?>
   </div>
	
	</body>
    <script>
				$('#datepicker').datepicker({
                    format: "yyyy-mm-dd"
                });				
				</script>
</html>