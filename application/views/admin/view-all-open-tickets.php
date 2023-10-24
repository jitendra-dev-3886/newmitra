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
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
									    <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>TicketID</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Rec Id</th>
                                                <th>Issue</th>
                                                <th>Subject</th>
                                                <th>priority</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th style="text-align: center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										
										
										<?php 
										if(!empty($r))
										{
										foreach($r as $i=>$v) 
										{		
                                            $mobile = '';	
                                            $rec = '';
                                            if($v->rec_id)
                                            {
                                                $recdt = $this->db_model->get_newdata('exr_rechrgreqexr_rechrgreq_fch',array('recid'=>$v->rec_id),'mobileno,recid');
                                                $mobile = $recdt[0]->mobileno;   
                                                $rec = $recdt[0]->recid;
                                            }
										    ?>										
                                            <tr>
                                                <td><?php echo $v->cus_id; ?></td>
                                                <td><strong><?php echo $v->ticket_id; ?></strong></td>  
                                                <td><?php echo $v->cus_name; ?></td>
												<td><?php echo $mobile; ?></td>
                                                <td><?php echo $rec; ?></td>
                                                <td><?php echo $v->issue; ?></td>
                                                <td><?php echo $v->subject; ?></td>
                                                <td><?php echo $v->priority; ?></td>
                                                <td><?php 
                                                if($v->status == 0)
                                                {
                                                    echo 'Open';
                                                }
                                                else
                                                {
                                                    echo 'Close';
                                                }
                                                ?></td>
                                                <td><?php echo DATE('d M, Y', strtotime($v->ticket_date)); ?></td>
                                                <td align="center">
                                                <a href="<?php echo base_url();?>admin/ticket/<?php echo $v->ticket_id; ?>" title="view" class="label label-success" ><i class="fa fa-eye"></i></a>
												</td>
                                            </tr> 
										
										<?php 
										}	
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

</html>