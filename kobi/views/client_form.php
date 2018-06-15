	<main class="container">
		<div class="space50"></div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-centered clearfix">
			<?php
			echo form_open_multipart(
				current_url(),
				array(
					'class' => 'form-horizontal',
					'id' => 'client_form'
				)
			);
			?>
				<div class="form-group">
					 <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="fileupload" />
				</div>
				<div class="form-group">
					<label for="firstname" class="control-label">שם פרטי</label>
					<input type="text" class="form-control blackBorder col-centered clearfix" name="firstname" value="<?php set_value('firstname') ?>" minlength=1 maxlength=40 autofocus required>
					<label for="firstname" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="lastname" class="control-label">שם משפחה</label>
					<input  type="text" class="form-control blackBorder col-centered clearfix" name="lastname" value="<?php set_value('lastname') ?>" minlength=1 maxlength=40 required>
					<label for="lastname" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="city" class="control-label">עיר</label>
					<input  type="text" class="form-control blackBorder col-centered clearfix" name="city" value="<?php set_value('city') ?>" minlength=1 maxlength=40 required>
					<label for="city" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="address" class="control-label">כתובת</label>
					<input  type="text" class="form-control blackBorder col-centered clearfix" name="address" value="<?php set_value('address') ?>" minlength=1 maxlength=60 required>
					<label for="address" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="email" class="control-label">דוא"ל (אימייל)</label>
					<input  type="email" class="form-control blackBorder col-centered clearfix" name="email" value="<?php set_value('email') ?>" minlength=1 maxlength=100 required>
					<label for="email" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="phone_number" class="control-label">מספר ליצירת קשר</label>
					<input  type="text" class="form-control blackBorder col-centered clearfix" name="phone_number" value="<?php set_value('phone_number') ?>" minlength=1 maxlength=10 required>
					<label for="phone_number" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="job_type" class="control-label">סוג השירות המבוקש</label>
					<select  class="form-control blackBorder col-centered clearfix" name='job_type' required>
					<?php
						foreach($jobs as $job){
					?>
						<option><?php echo $job['description'] ?></option>
					<?php
						}
					?>
					</select>
				</div>
				<div class="form-group">
					<label for="description" class="control-label">תיאור הבעיה (עד 255 תווים)</label>
					<textarea class="form-control blackBorder col-centered clearfix" name="description" value="<?php set_value('description') ?>" rows=4 minlength=1 maxlength=255 required></textarea>
					<label for="description" class="control-label errorLabel"></label>
				</div>
				<div class="form-group">
					<label for="images[]" class="control-label">העלה תמונות (עד 3, מקסימום גודל 2 מגה לכל תמונה)</label>
					<div class="input-group">
						<label class="input-group-btn">
							<div class="btn btn-primary">עיין&hellip;<input id="images[]" type="file" style="display: none;" name="images[]" accept="image/*" multiple>
							</div>
						</label>
						<input type="text" name="image_info"class="form-control" readonly>
					</div>
					<label for="images[]" class="control-label errorLabel"></label>
				</div>
				<div class="form-group text-center">
						<input type="submit" class="btn btn-primary btn-lg" value="שלח טופס">
				</div>
			</form>
		</div><?php echo validation_errors() ?>
	</main>