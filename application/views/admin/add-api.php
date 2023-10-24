
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

                                <h4 class="card-title">Add New API</h4>
                                <form method="post" action="<?php echo base_url()?>admin/add_api_succ">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label">API Source Name</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-magic"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" onkeypress=return/[a-zA-z]/i.test(event.key) placeholder="Enter API Source Name" name="apisourcename" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Balance API Url</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-cog"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Balance API Url" name="bal_api_url" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">API Url</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-list"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="http:google.com/pagename?username=XXXX&password=XXXX" name="api_url" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Operator Parameter</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-asterisk"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Operator Parameter" name="operator" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Mobile Parameter</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-mobile"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" onkeypress="return/[a-zA-Z]/i.test(event.key)" placeholder="Enter Mobile Parameter" name="mobile" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Amount Parameter</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-credit-card "></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" onkeypress="return/[a-zA-Z]/i.test(event.key)" placeholder="Enter Amount Parameter" name="amount" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Txn Id Parameter</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-circle"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" onkeypress="return/[a-zA-Z]/i.test(event.key)" placeholder="Enter Txn Id Parameter" name="txn_id" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Authentication Parameter(Note :- please enter all authorization parameter seperated by '&' along with value using '=')</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-circle"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" onkeypress="return/[a-zA-Z0-9=&]/i.test(event.key)" placeholder="Enter Authentication Parameters" name="authenticate" required>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    

                                        <div class="col-lg-6">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Optional Parameter</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-magic"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="eg : format=1&feild1=" name="optional" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Success Response</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-check"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Success Response" name="succ_resp" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Failed Response</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-list"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Failed Response" name="fail_resp" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Pending  Response</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-asterisk"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Pending  Response" name="pending_resp" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">API Response Type</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-mobile"></i></div>
                                                    <select name="api_resp_type" class="form-control" required>
                  									  	<option value="">Select API Response Type</option>
                  									  	<option value="json">JSON</option>
                                                        <option value="xml">XML</option>
                                                        <option value="string">STRING</option>                                   
                              						</select>                                               
                                  				</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">API HIT type</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-mobile"></i></div>
                                                    <select name="api_hit_type" class="form-control" required>
                  									  	<option value="">Select API HIT Type</option>
                  									  	<option value="get">GET</option>
                                                        <option value="post">POST</option>                                  
                              						</select>                                               
                                  				</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Operator TXID Response</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-list"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Operator TXID Response" name="op_txid_resp" required>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Status Response</label>
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="fas fa-asterisk"></i></div>
                                                    <input type="text" class="form-control" id="autoSizingInputGroup" placeholder="Enter Status Response" name="status_resp" required>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <input type="submit" class="btn btn-primary w-md">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- end select2 -->

                    </div>
<!--                                    <div class="col-xl-3 col-lg-4 col-sm-6 mb-2">
                                        <div class="p-3">
                                            <p>A success message!</p>
                                            <button type="button" class="btn btn-primary waves-effect waves-light" id="sa-success">Click me</button>
                                        </div>
                                    </div>-->

                </div>
                <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
        <?php include('footer.php');?>
   </div>
	
	</body>
	
<script>
	
	    var getMembers = cus_type =>{
	        var cus_type = cus_type.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url() ?>admin/getUplineMembers",
	            cache: "false",
	            data: {cus_type:cus_type},
	            success: function(res){
	                $("#members").empty();
	                $("#members").append(res);
	            }
	        });
	    }
	
	    var getMemberPackage = cus_type =>{
	        var cus_type = cus_type.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>admin/getMemberPackage",
	            cache: false,
	            data: {cus_type:cus_type},
	            success: function(res){
	                $("#package").empty();
	                $("#package").append(res);
	            }
	        });
	    }
	    
	    var getMemberBalance = cus_id =>{
	        var cus_id = cus_id.value;
	        $.ajax({
	            type: "post",
	            url: "<?php echo base_url()?>admin/getuser_creadit",
	            cache: false,
	            data: {cus_id:cus_id},
	            success: function(res){
	                $("#wallet").val(res);
	            }
	        });
	    }
	</script>

</html>