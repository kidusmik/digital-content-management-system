<div class="col-lg-6 col-lg-offset-3 middle-body">
	<div class="shadow-box sign-up-box well">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2">
				<?php
					echo validation_errors();
					echo form_open_multipart('sign_up/user');
				?>
					<div class="form-group">
					<span class="glyphicon glyphicon-user"></span>insert profile image
					<input name="profileimage" type="file" accept="image/jpg" class="form-control" id="input_file">
					</div>
					<div class="form-group">
					<input name="username" type="text" class="form-control" id="username" placeholder="username" value="<?php echo set_value('username'); ?>">
					</div>
					<div class="form-group">
					<input name="firstname" type="text" class="form-control" id="firstname" placeholder="first name" value="<?php echo set_value('firstname'); ?>">
					</div>
					<div class="form-group">
					<input name="lastname" type="text" class="form-control" id="lastname" placeholder="last name" value="<?php echo set_value('lastname'); ?>">
					</div>
					<div class="form-group">
					<input name="email" type="email" class="form-control" id="email" placeholder="email" value="<?php echo set_value('email'); ?>">
					</div>
					<div class="form-group">
					<input name="phonenumber" type="number" class="form-control" id="phonenumber" placeholder="phone number { 09xxxxxxxx }" value="<?php echo set_value('phonenumber'); ?>">
					</div>
					<div class="form-group">
					<select name="preference" class="form-control" id="preference">
						<option value="general">general</option>
						<option value="education">education</option>
						<option value="entertainment">entertainment</option>
					</select>
					</div>
					<div class="form-group">
					<input name="deposit" type="number" min="50" max="6000" class="form-control" id="deposit" placeholder="deposit amount" value="<?php echo set_value('deposit'); ?>">
					</div>
					<div class="form-group">
					<input name="password" type="password" class="form-control" id="password" placeholder="password">	
					</div>
					<div class="form-group">
					<input name="confirm_password" type="password" class="form-control" id="confirm_password" placeholder="confirm password" >
					</div>
					<button name="signup" type="submit" class="btn btn-default">sign up</button>
				</form>
			</div>
		</div>
	</div>
</div>