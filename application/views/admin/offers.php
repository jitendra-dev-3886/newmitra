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
									<div class="col-sm-6 col-md-3"><button class="modal-effect btn btn-danger btn-rounded btn-block"  data-effect="effect-slide-in-right" data-toggle="modal" href="#modaldemo7">Add Offers</button></div>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap text-center" id="example1">
										<thead>
											<tr>
												<!--<th class="wd-15p border-bottom-0">ID.</th>-->
												<th class="wd-15p border-bottom-0">Offer ID</th>
												<th class="wd-15p border-bottom-0">Offer Title</th>
												<th class="wd-15p border-bottom-0">Offer Weblink</th>
												<th class="wd-15p border-bottom-0">Offer Description</th>
												<th class="wd-15p border-bottom-0">Offer Image</th>
												<th class="wd-15p border-bottom-0">Offer Date</th>
												<th class="wd-20p border-bottom-0">Amount</th>
												<th class="wd-10p border-bottom-0">Action</th>
											</tr>
										</thead>
										<tbody>
										    <?php if(!empty($offer)){$i=1;foreach($offer as $offer){  ?>
											<tr>
												<td class=""><b><?php echo ucfirst($offer->offer_id); ?></b></td>
												<td class=""><b><?php echo ucfirst($offer->title); ?></b></td>
												<td class=""><?php echo $offer->weblink; ?></td>
												<td class=""><?php echo ucfirst($offer->description); ?></td>
												<td class=""><img src="<?php echo $offer->banner_image; ?>"></td>
												<td class="text-info"><b><?php echo ucfirst($offer->datetime); ?></b></td>
												<td class=""><b><?php echo $offer->amount; ?></b></td>
												<!--<td class="<?php if($offer->news_status == '1'){ echo 'text-success'; $status='ACTIVE'; }else{ echo 'text-danger'; $status='INACTIVE'; } ?>"><b><?php echo $status; ?></b></td>-->
												<td class="text-primary">
												    <a href="<?php echo base_url(); ?>admin/update_offer/<?php echo $offer->offer_id; ?>" ><b><i class="typcn typcn-edit tx-24 lh--9 op-6"></i></b></a>
												    <a href="<?php echo base_url(); ?>admin/delete_offer/<?php echo $offer->offer_id; ?>" onclick="return confirm('Delete Offer Id <?php echo ucfirst($offer->offer_id); ?> ? ')" ><b><i class="typcn typcn-delete-outline tx-24 lh--9 op-6"></i></b></a>
												    </td>
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
            						<h6 class="modal-title">Add Offer</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            					</div>
            					<form action="<?php echo site_url('admin/add_offer'); ?>" data-parsley-validate="" method="post" enctype="multipart/form-data">
                					<div class="modal-body">
        								<div class="row row-sm">
        							<!--	    <div class="col-12">-->
    									  <!--      <div class="form-group">-->
    											<!--	<label class="form-label">Status: <span class="tx-danger">*</span></label>-->
    											<!--	<div class="input-group">-->
        							<!--					<div class="input-group-prepend">-->
               <!-- 											<div class="input-group-text">-->
               <!-- 												<i class="typcn typcn-user tx-24 lh--9 op-6"></i>-->
               <!-- 											</div>-->
               <!-- 										</div>-->
        							<!--					<select class="form-control"  name="status">-->
        							<!--					    <option value="1">Active</option>-->
        							<!--					    <option value="0">Inactive</option>-->
        							<!--					</select>-->
        							<!--				</div>-->
    											<!--</div>-->
    									  <!--  </div>-->
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Offer Title: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" placeholder="Offer Title" type="text"  name="offer_title">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Offer Weblink: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" placeholder="Offer Weblink" type="text"  name="offer_weblink">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Offer Description: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" placeholder="Description" type="text"  name="description">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Offer Amount: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" placeholder="Amount" type="text"  name="amount">
        											</div>
    											</div>
    									    </div>
    									    <div class="col-12">
    									        <div class="form-group">
    												<label class="form-label">Image: <span class="tx-danger"></span></label>
    												<div class="input-group">
        												<div class="input-group-prepend">
                											<div class="input-group-text">
                												<i class="typcn typcn-infinity-outline tx-24 lh--9 op-6"></i>
                											</div>
                										</div>
                										<input class="form-control" type="file"  name="banner_image">
        											</div>
    											</div>
    									    </div>
    									  <!--  <div class="col-12">-->
    									  <!--      <div class="form-group">-->
    											<!--	<label class="form-label">News For: <span class="tx-danger">*</span></label>-->
    												<!--<div class="input-group">-->
               <!--                                         <input type="checkbox" name="newstype[]" value="retailer" >&nbsp;&nbsp;Merchant&nbsp;&nbsp;-->
               <!--                                         <input type="checkbox" name="newstype[]" value="distributor">&nbsp;&nbsp;Sales Manager&nbsp;&nbsp;-->
               <!--                                         <input type="checkbox" name="newstype[]" value="master">&nbsp;&nbsp;President&nbsp;&nbsp;-->
               <!--                                         <input type="checkbox" name="newstype[]" value="api">&nbsp;&nbsp;Api Clients&nbsp;&nbsp;     -->
                                                    <!--</div>-->
    											<!--</div>-->
    									  <!--  </div>-->
    										
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