	<main class="container">
		<div class="space50"></div>
			<?php
				foreach($messages as $message){
			?>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-centered text-center clearfix">
						<h2> <?php echo $message; ?></h2>
					</div>
				</div>
			<?php 
				echo "\r\n\t";
				}
			?>
		<div class="space50"></div>
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-centered text-center clearfix">
				<button type="button" class="btn btn-default"><a href="<?php echo base_url() ?>">חזרה לטופס ראשי</a></button>
			</div>
		</div>
	</main>
	<div class="space50"></div>
