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
						<h4 class="content-title mb-2">News Management</h4>
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
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add News</button></div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<th class="wd-15p border-bottom-0">ID.</th>
												<th class="wd-15p border-bottom-0">News Description</th>
												<th class="wd-15p border-bottom-0">News Type</th>
												<th class="wd-15p border-bottom-0">News Date</th>
												<th class="wd-20p border-bottom-0">Status</th>
												<th class="wd-10p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($news)){$i=1;foreach($news as $news){  ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td class=""><b><?php echo ucfirst($news->news_desc); ?></b></td>
												<td class="text-danger"><b><?php echo ucfirst($news->news_type); ?></b></td>
												<td class="text-info"><b><?php echo ucfirst($news->news_date); ?></b></td>
												<td class="<?php if($news->news_status == '1'){ echo 'text-success'; $status='ACTIVE'; }else{ echo 'text-danger'; $status='INACTIVE'; } ?>"><b><?php echo $status; ?></b></td>
												<td class="text-primary"><a href="<?php echo base_url(); ?>admin/delete-news/<?php echo $news->news_id; ?>" onclick="return confirm('Delete News Id <?php echo ucfirst($i); ?> ? ')" ><b><i class="typcn typcn-delete-outline tx-24 lh--9 op-6"></i></b></a></td>
											</tr>
											<?php }} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--/div-->
					
					<!-- Modal effects -->
            		<div class="modal" id="modaldemo7">
            			<div class="modal-dialog modal-dialog-centered" role="document">
            				<div class="modal-content modal-content-demo">
            					<div class="modal-header">
            						<h6 class="modal-title">Add News</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            					</div>
            					<form action="<?php echo site_url('admin/add_news'); ?>" data-parsley-validate="" method="post">
                					<div class="modal-body">
        								<div class="row row-sm">
        								    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Status: <span class="tx-danger">*</span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-user tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
        												<select class="form-control" required name="status">
        												    <option value="1">Active</option>
        												    <option value="0">Inactive</option>
        												</select>
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Description: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" placeholder="Description" type="text" required name="desc">
        											</div>
    											</div>
    									    </div>
    									    
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">News For: <span class="tx-danger">*</span></label>
    												<!--<div class="input-group">-->
                                                        <input type="checkbox" name="newstype[]" value="retailer" >&nbsp;&nbsp;Retailers&nbsp;&nbsp;
                                                        <input type="checkbox" name="newstype[]" value="distributor">&nbsp;&nbsp;Distributors&nbsp;&nbsp;
                                                        <input type="checkbox" name="newstype[]" value="master">&nbsp;&nbsp;Masters&nbsp;&nbsp;
                                                        <input type="checkbox" name="newstype[]" value="api">&nbsp;&nbsp;Api Clients&nbsp;&nbsp;     
                                                    <!--</div>-->
    											</div>
    									    </div>
    										
    									</div>
                					</div>
                					<div class="modal-footer">
                						<button type="submit" class="btn ripple btn-primary">Add</button>
                						<button class="btn ripple btn-danger" data-dismiss="modal" type="button">Discard</button>
                					</div>
    							</form>
            				</div>
            			</div>
            		</div>
            		<!-- End Modal effects-->


				</div>
				<!-- /row -->

			</div>
			<!-- Container closed -->
		</div>
		<!-- /main-content -->
	
	   <?php $this->load->view('admin/footer'); ?> 
	
	</body>

</html>