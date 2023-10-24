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
						<h4 class="content-title mb-2">Ticket Management</h4>
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
							    
                    <!-- form start -->
                    <div class="col-md-7 table-responsive">      
    
                        <table style="background: #fff;" class="table table-bordered">
                          <tbody>
                          <tr>
                              <td>Name</td> 
                              <td><?php echo $name; ?></td> 
                          </tr>
                          <tr>
                              <td>Email</td> 
                              <td><?php echo $email; ?></td> 
                          </tr>
                          <tr>
                              <td>Mobile</td> 
                              <td><?php echo $this->encryption_model->decode($mobile); ?></td> 
                          </tr>
    
                          <tr>
                              <td>Status</td> 
                              <td>
                              <?php 
                              if($r[0]->status == 0)
                              {
                                echo 'Open';
                              }
                              else
                              {
                                echo 'Closed';
                              }
                              ?></td> 
                          </tr>
    
                          
                             <?php echo form_open_multipart('admin/reply')?> 
    						<tr>
                              <td colspan="2">
                                <textarea rows="4" name="desc" minlength="10" class="form-control" required></textarea>
                                <br/>  
                                <input type="file" name="userfile" class="form-control" /> 
                                <br/>
                                <input type="hidden" name="ticket" id="tick" value="<?php echo $r[0]->ticket_id; ?>" />
                                <input type="hidden" name="recid" id="recid" value="<?php echo $r[0]->rec_id; ?>"/>
                                <input type="hidden" name="cus" value="<?php echo $r[0]->cus_id; ?>" />
                          
                                <button type="submit" class="btn btn-flat btn-success">Reply</button>
    
                                <span id="closed" class="btn btn-flat btn-danger">Closed</span>
                                </td>  
                            </tr>
                          <?php echo form_close();?>
                          </tbody>
                        </table>  
                        
                    </div>
                     <div class="col-md-5 table-responsive">    
                                        
                                <h4 class="text-primary">Previous Discussion</h4>  
                                  <table style="background: #fff;" class="table table-bordered">
                                      <thead>
                                        <tr>
                                          <td><strong>By</strong></td>
                                          <td><strong>Date</strong></td>
                                          <td><strong>Message</strong></td>
                                        </tr>
                                      </thead>  
                                      <tbody>
                                          <?php
                                          if($r)
                                          {  
                                          foreach($r as $dis)
                                          {   
                                          ?>
                                              <tr>
                                                  <td><?php echo $dis->reply_from; ?></td>
                                                  <td><?php echo DATE('d M, Y h:i:s', strtotime($dis->ticket_date)); ?></td>  
                                                  <td>
                                                  <?php echo $dis->message;?>
                                                  <br/>
                                                  <?php
                                                  if($dis->ticket_image != '')
                                                  {
                                                  ?>
                                                  <img src="<?php echo base_url().$dis->ticket_image; ?>" class="img-responsive" />
                                                  <?php  
                                                  }
                                                  ?>
                                                  </td> 
                                                  </tr>
                                                  <?php
                                                  }
                                                  }
                                                  else
                                                  {
                                                  ?>
                                                  <tr>
                                                  <td colspan="2"><?php echo 'No data found..'; ?></td> 
                                              </tr>
                                          <?php 
                                          }
                                          ?>
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
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>
	
	<script type="text/javascript">
        $('#stat').change(function(){
  			var dt = $('#stat').val();
  			var id = $('#id').val();
  			
  			$.ajax({
  				url: '<?php echo base_url(); ?>admin/change_st',
  				type: 'POST',
  				cache: false,
  				data: {dt:dt,id:id},
  				success: function(data){
  					location.reload();
  					}
  				})
  		});
      </script>   
      <script type="text/javascript">
        $(document).ready(function(){
          $('#closed').click(function(){
            var tk = $('#tick').val();
            var recid = $('#recid').val();
            
            $.ajax({
              url: "<?php echo base_url(); ?>admin/closed",
              async: false,
              type: "POST",
            //   data: "tk="+tk,
              data: {tk:tk,recid:recid},
              dataType: "html", 
              success: function(data) {
                location.reload();
              }
            })
          });
        });
      </script>   

</html>