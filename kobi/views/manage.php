<?php
	$config = array(
        'token_name' => $this->security->get_csrf_token_name(),
        'token_hash' => $this->security->get_csrf_hash(),
		'base_url' => base_url()
	);
?>
	<input type="hidden" name="config" value="<?php echo $config['token_name'].",".$config['token_hash'].','.$config['base_url'];?>" />
	<nav class="navbar navbar-inverse navbar-static-top">
		<div class="container-fluid">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>          
		  </button>
			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav nav-pills navbar-right">
					<li class="float_right active"><a href="#request_table" aria-controls="request_table" data-toggle="pill">טבלת בקשות</a></li>
					<li class="float_right"><a href="#service_list" aria-controls="service_list" data-toggle="pill">רשימת שירותים ללקוח</a></li>
				</ul>
			</div>
		</div>
	</nav>
	
	<section class="container">
		<div class="tab-content">
			<div class="tab-pane active fade in" id="request_table">
				<div class="row">
					<div class="col-md-2 col-xs-3 float_right">
						<button class="btn btn-primary btn_responsive" name="editRequest" >אשר שינויים</button>
					</div>
					<div class="col-md-2  col-xs-3 float_right">
						<button class="btn btn-danger btn_responsive" name="removeRequest" data-toggle="modal" data-target="#deleteModal">מחק בקשה</button>
					</div>
				</div>
				<div class="space50"></div>
				<div id="msg_requests" class="row">
					
				</div>
				<div class="row">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed">
							<tr class="info">
								<th class="text-right"><input type="checkbox"></th>
								<th class="text-right">שם לקוח</th>
								<th class="text-right">עיר</th>
								<th class="text-right">כתובת</th>
								<th class="text-right">דוא"ל</th>
								<th class="text-right">טלפון</th>
								<th class="text-right">סוג שירות</th>
								<th class="text-right">תיאור</th>
								<th class="text-right">תמונות</th>
								<th class="text-right">סטטוס בקשה</th>
								<th class="text-right">חתימת זמן בקשה</th>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="service_list">
				<div class="row">
					<div class="col-md-2 col-xs-3 float_right">
						<button class="btn btn-primary btn_responsive" name="editJob" >אשר שינויים</button>
					</div>
					<div class="col-md-2 col-xs-3 float_right">
						<button class="btn btn-primary btn_responsive" name="addJob" data-toggle="modal" data-target="#addModal">הוסף שירות</button>
					</div>
					<div class="col-md-2 col-xs-3 float_right">
						<button class="btn btn-danger btn_responsive" name="removeJob" data-toggle="modal" data-target="#deleteModal">מחק שירות</button>
					</div>
				</div>
				<div class="space50"></div>
				<div id="msg_jobs" class="row">
					
				</div>
				<div class="row">
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<tr class="info">
								<th class="text-right"><input type="checkbox"></th>
								<th class="text-right">תיאור השירות</th>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Image Modal -->
	<div id="imgModal" class="modal fade modal-lg col-centered" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div id="img_carousel" class="carousel slide" data-ride="carousel">
						  <ol class="carousel-indicators">
								
						  </ol>
						  <div class="carousel-inner" role="listbox">
								
						  </div>
						  <a class="left carousel-control" href="#img_carousel" role="button" data-slide="prev">
							<span class="icon-next" aria-hidden="true"></span>
						  </a>
						  <a class="right carousel-control" href="#img_carousel" role="button" data-slide="next">
							<span class="icon-prev" aria-hidden="true"></span>
						  </a>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary btn_responsive" data-dismiss="modal">סגור</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Delete Modal -->
	<div id="deleteModal" class="modal fade modal-sm col-centered" tabindex="-1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">אישור מחיקה</h4>
				</div>
				<div class="modal-body">
					<h5>האם אתה בטוח?</h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn_responsive" data-dismiss="modal">ביטול</button>
					<button type="button" class="btn btn-primary btn_responsive" data-dismiss="modal">אישור</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Add Modal -->
	<div id="addModal" class="modal fade modal-lg col-centered" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<form id="addForm" class="form-horizontal">
						<div class="form-group">
							<label for="job_description" class="control-label">תיאור השירות</label>
							<input type="text" name="description" class="form-control" autofocus minlength=1 maxlength=255 required>
							<label for="job_description" class="control-label errorLabel"></label>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn_responsive" data-dismiss="modal">ביטול</button>
					<button type="button" class="btn btn-primary btn_responsive" data-dismiss="modal">אישור</button>
				</div>
			</div>
		</div>
	</div>