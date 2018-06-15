	<main class="container">
		<div class="space50"></div>
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-centered clearfix">
				<?php 
				echo form_open(
					current_url(),
					array(
						'class' => 'form-horizontal',
						'id' => 'login_form'
					)
				);
				?>
				<div class="form-group">
					<input type="text" class="form-control text-center blackBorder col-centered clearfix" name="username" dir="ltr" placeholder="שם משתמש" value="<?php set_value('username'); ?>" minlength=1 maxlength=72 autofocus required>
				</div>
				<div class="form-group">
					<input type="password" class="form-control text-center blackBorder col-centered clearfix" name="password" dir="ltr" placeholder="סיסמא" minlength=1 maxlength=72 required>
				</div>
				<div class="form-group text-center">
						<input type="submit" class="btn btn-primary btn-lg" value="התחבר">
				</div>
				</form>
			</div><?php echo validation_errors(); // Validation error output ?> 
		</div>
	</main>
