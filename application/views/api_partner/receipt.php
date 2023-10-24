<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('retailer/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('retailer/loader'); ?>

		<?php $this->load->view('retailer/header'); ?>

		<!-- main-content opened -->
		<div class="main-content horizontal-content">

			<!-- container opened -->
			<div class="container">
									<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">Hi, welcome back  <?php echo $this->session->userdata('cus_name');?>!!!</h4>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
								<li class="breadcrumb-item active" aria-current="page">Dmt Report</li>
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
				<!-- main-content-body -->
				<div class="main-content-body">
				    <div class="col-lg-12 col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="main-content-label mg-b-5">
									Dmt Receipt<br>
								</div>
								<div class="row"><br></div>
								<div class="row" >
									<div class="col-md-8 col-lg-8 col-xl-8 mx-auto d-block">
                        			    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                            					    <div class="form-group">
                                    					<label class="main-content-label tx-11 tx-medium tx-gray-600">Add Charges&nbsp;<span style="color:red">*</span></label>
                                    					<input class="form-control"  placeholder="Amt" type="text" onkeypress="return/[a-z0-9]/i.test(event.key)" onchage="alert('hi)" id="amt" name="ifsc_code" required>
                            					    </div>
                        					    </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					 <br>
                        					        <button class="btn btn-info" onclick="change()">ADD</button>
                            					</div>
                        					</div>
                        				</div>
                        			</div>

                        			<!--<div class="col-md-8 col-lg-8 col-xl-8 mx-auto d-block" >
                        			    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<div class="row">
                            					<div class="col-md-4 col-lg-4 col-xl-4">
                            					    <img src="../../assets/logo.png" class=" ">
                        					    </div>
                            					<div class="col-md-4 col-lg-4 col-xl-4">
                            					    
                            					</div>
                            					<div class="col-md-4 col-lg-4 col-xl-4">
                            					    <b>Name :- </b><?php echo $this->session->userdata('cus_name');?><br>
                            					    <b>Mobile :- </b><?php echo $this->session->userdata('cus_mobile');?><br>
                            					    <b>Type :- </b><?php echo $this->session->userdata('cus_type');?><br>
                            					</div>
                        					</div>
                        				</div>
                        			</div>-->
									<div class="col-md-8 col-lg-8 col-xl-8 mx-auto d-block" id="dvforprint">
									    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        					<div class="row">
                            					<div class="col-md-4 col-lg-4 col-xl-4">
                            					    <img src="../../assets/logo.png" class=" ">
                        					    </div>
                            					<div class="col-md-4 col-lg-4 col-xl-4">
                            					    <br><br>
                            					</div>
                            					<div class="col-md-4 col-lg-4 col-xl-4">
                            					    <b>Name &nbsp;:- </b><?php echo $this->session->userdata('cus_name');?><br>
                            					    <b>Mobile :- </b><?php echo $this->session->userdata('cus_mobile');?><br>
                            					    <b>Type &nbsp;&nbsp;&nbsp; :- </b><?php echo ucfirst($this->session->userdata('cus_type'));?><br>
                            					</div>
                        					</div>
                        				</div>
                        			    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        			        
                        			        <div class="row">
                        			            <div class="col-md-12 col-lg-8 d-block">
                        			                <b style="font-size:20px">Money Transfer Receipt</b>
                        			            </div>
                        			        </div><br>
                        			        <div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Name  :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->bene_name;?> </label>
                                					 </div>
                            					 </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Reference No :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->imps_ref_no;?> </label>	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Transaction No :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->trans_id;?> </label>
                                					 </div>
                            					 </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Status :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->status;?> </label>	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Bank Name :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->bene_name;?> </label>
                                					 </div>
                            					 </div>	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Mobile No :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->mobile_no;?> </label>
                                					 </div>
                            					 </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Amount :- </label>
                                						<input type="text" class="tx-11 tx-medium tx-gray-600" style="border:none" id="dmtAmt" value="<?php echo $dmt_txn['0']->amount;?>"><!--
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->amount;?> </label>-->
                                					 </div>
                            					 </div>	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Charges :- </label><!--
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600" id="charges"><?php echo $dmt_txn['0']->charge;?> </label>-->
                                						<input type="text" value="0"class="tx-11 tx-medium tx-gray-600" id="charges" style="border:none">
                                					 </div>
                            					 </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Total :- </label><!--
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600" id="charges"><?php echo $dmt_txn['0']->charge;?> </label>-->
                                						<input type="text" value="<?php echo $dmt_txn['0']->amount;?>" class="tx-11 tx-medium tx-gray-600" id="total" style="border:none">
                                					 </div>
                            					 </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                        					        <button class="btn btn-main-primary" onclick="onClickprint()">Print</button>
                            					</div>
                        					</div>
                        				</div>
                        			</div>
								</div>
							</div>
						</div>
					</div>	
				</div>
				<!-- / main-content-body -->
			</div>
			<!-- /container -->
		</div>
		<!-- /main-content -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
<script>
  function onClickprint() {
    html2canvas(document.getElementById('dvforprint'), {
      onrendered: function(canvas) {
        var img = canvas.toDataURL("image/png");
        var doc = new jsPDF();
        doc.addImage(img, 'JPEG', 5, 20);
        doc.autoPrint(); 
        window.open(doc.output('bloburl'))
      }
    });
  };
   /* var element = document.getElementById("btnPrint");
  element.addEventListener("click", onClickprint);*/
</script>
<script>
    function change(){
        const amt = $('#amt').val();
        const dmt = $('#dmtAmt').val();
        const total = parseInt(amt) + parseInt(dmt);
        $('#charges').val(amt);
        $('#total').val(total);
    }
</script>
	   <?php $this->load->view('retailer/footer'); ?> 
	
	</body>

</html>


