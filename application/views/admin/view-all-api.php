

<!DOCTYPE html>
<html lang="en">
    
    <?php $this->load->view('admin/links'); ?>

	<body class="main-body  app">
		
		<?php $this->load->view('admin/loader'); ?>

		<?php $this->load->view('admin/header'); ?>
		
	<div class="main-content horizontal-content">
	    
	    <div class="container">
		        <div class="breadcrumb-header justify-content-between">
					<div>
						<h4 class="content-title mb-2">All API</h4>
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

                <div class="row">
                    <div class="col-lg-12">
                        <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100" style="background:white">
                            <thead>
                                <tr class="text-center">
                                        <th>#</th>
										<th>API Source</th>
										<th>URL</th>
										<th>Date</th>
										<th>Operator</th>
										<th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(is_array($api)){$i=0;foreach($api as $rec){ $i = ++$i; ?>
										<tr class="text-center">
										    <td><?php echo $i ;?></td>
											<td class="text-primary"><b> <?php echo $rec->apisourcename; ?></b></td>
											<td><b> <?php echo $rec->api_url; ?></b></td>
											<td style=" width: 200px;t;"><b><?php echo $rec->api_added_time; ?></b></td>
											<td><?php if($i >3 ){ ?><b><a href="<?php echo site_url('admin/view-all-api-operator/'.$rec->apisourceid)?>"><i class="fa fa-plus text-danger"></i></a>
											    </b> <?php } ?></td>
										    <td><?php if($i >3 ){ ?><b><a href="<?php echo site_url('admin/update-api/'.$rec->apisourceid)?>"><i class="fa fa-edit text-success"></i></a>
											    </b><?php } ?></td>
										</tr>
									<?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
        <?php include('footer.php');?>
   </div>
	   
	</body>

</html>