
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
                    
                                    <!-- Main content -->
                                    <section class="content">
                                        <div class="row">
                                            <div class="col-xs-12 col-md-12">
                    
                                                <!--alert start-->
                                                <div id="resp">
                    
                                                </div>
                                                <!--alert end-->
                    
                                                <div class="box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"></h3>
                                                    </div><!-- /.box-header -->
                    
                    								<div id="finalResult">
                    								
                                                    <div class="box-body table-responsive">								
                                                        <table id="example1" class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Api Source Name</th> 
                    												<th>Operator Name</th>
                    												<th>Operator Type</th>
                                                                    <th>Oprator Code</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                    										
                                                            <tbody>  
                    										 
                    										<?php 
                    										if(!empty($r))
                    										{
                    										    $sl =0;
                    										foreach($r as $pk)   
                    										{
                                                            $apiname = $v[0]->apisourcecode;
                                                            $apisourceid = $v[0]->apisourceid;
                                                            ?>
                    											<tr>
                                                                    <td><?php echo $sl++;?></td>
                                                                    <td><?php echo $apiname ?></td>
                                                                    <td><?php echo $pk->operatorname;?></td>  
                                                                    <td><?php echo $pk->opsertype;?></td> 
                                                                    
                                                                     <td><input type="text" value="<?php echo $pk->$apiname;?>" name="opcode" id="opcode_<?php echo $pk->opid?>" class="text-center"></td> 
                    												
                                                                    
                                                                        <input type="hidden" id="operatorid_<?php echo $pk->opid ?>" name="operatorid" value="<?php echo $pk->opid ?>">
                                                                        <input type="hidden" id="apisourceid_<?php echo $pk->opid?>"  name="apisourceid" value="<?php echo $apiname ?>">
                                                                       
                                                                    
                                                                       <td> <a href="JavaScript:void(0);" class="btn btn-default" onclick="spdate('<?php echo $pk->opid ?>')"><i class="fa fa-edit"></i>&nbsp;Update</a>
                                                                 
                    
                    
                                                                    </td>
                                                          <?php
                    									  }}
                    									  ?>
                                                            </tbody>
                    										<tfoot>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Api Source Name</th> 
                    												<th>Operator Name</th>
                    												<th>Operator Type</th>
                                                                    <th>Oprator Code</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                    									
                                                    </div><!-- /.box-body -->
                    								</div>
                                                </div><!-- /.box -->
                                            </div>
                                        </div>
                    
                                    </section><!-- /.content -->
                            </div><!-- ./wrapper -->
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
	
	<script type="text/javascript">
            $(function() {
				
                
				
				$("#checkall").click(function () {
					$('.icheckbox_minimal').attr('aria-checked','true');
					$('.icheckbox_minimal').attr('class','icheckbox_minimal checked');
					$('.ch').attr('checked','checked');
					});
					
					$("#uncheckall").click(function () {
					$('.icheckbox_minimal').attr('aria-checked','false');
					$('.icheckbox_minimal').attr('class','icheckbox_minimal');
					$('.ch').removeAttr('checked');
					});
					
				
				 
           		});

            function spdate(v) {
                var info = {'operatorid': '', 'apisourceid': '', 'opcode': ''};
                console.log(v);
                info.operatorid = $('#operatorid_' + v).val();
                info.apisourceid = $('#apisourceid_' + v).val();
                info.opcode = $('#opcode_' + v).val();
             //console.log(info);
                $('#resp').html('');
                $('#load').show();

                $.ajax({
                    type: "POST",
                    url:'<?=base_url('admin/apiopcode_update')?>',
                    data:info,
                    dataType:'json'
                }).done(function(data) {
                    console.log(data);
                    $('#resp').html(data['mess']);
                    $('#date_'+v).html(data['date']);
                    $('#load').hide();
                });
            }

        </script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.jclock.js"></script>
        <script type="text/javascript">
        $(function($) {
          var options2 = {
            format: '%a %b %d, %Y %H:%M:%S' // 24-hour
          }
          $('#jclock2').jclock(options2);
        });
        </script>

</html>