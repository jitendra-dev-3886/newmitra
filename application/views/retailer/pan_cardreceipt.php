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
									Pan Card Receipt<br>
								</div>
								<div class="row"><br></div>
								<div class="row" >
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
                            					    <b>Vle ID :- </b><?php echo $dmt_txn['0']->vle_id;?><br>
                            					</div>
                        					</div>
                        				</div>
                        			    <div class="card card-body pd-20 pd-md-40 border shadow-none">
                        			        
                        			        <div class="row">
                        			            <div class="col-md-12 col-lg-8 d-block">
                        			                <b style="font-size:20px">Pan Card Receipt</b>
                        			            </div>
                        			        </div><br>
                        			        <div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Order ID  :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->order_id;?> </label>
                                					 </div>
                            					 </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Date :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->date;?> </label>	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Quantity :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->qty;?> </label>
                                					 </div>
                            					 </div>
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Rate :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->rate;?> </label>	
                                					</div>
                            					</div>
                        					</div>
                        					<div class="row">	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Coupon Price :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->coupon_price;?> </label>
                                					 </div>
                            					 </div>	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Amount :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->amount;?> </label>
                                					 </div>
                            					 </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Counpon Type :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600"><?php echo $dmt_txn['0']->coupon_type;?> </label>
                                					 </div>
                            					 </div>	
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Old Balance :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600" id="charges"><?php echo $dmt_txn['0']->old_bal;?> </label>
                                					 </div>
                            					 </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">New Balance :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600" id="charges"><?php echo $dmt_txn['0']->new_bal;?> </label>
                                					</div>
                            					 </div>
                            					 <div class="col-md-6 col-lg-6 col-xl-6">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Status :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600" id="charges"><?php echo $dmt_txn['0']->status;?> </label>
                                					</div>
                            					 </div>
                        					</div>
                        					<div class="row">
                            					<div class="col-md-12 col-lg-12 col-xl-12">
                                					<div class="form-group">
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600">Message :- </label>
                                						<label class="main-content-label tx-11 tx-medium tx-gray-600" id="charges"><?php echo $dmt_txn['0']->message;?> </label>
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


