<?php
/**
 * Localized version of part of form of Laravel Auth: Register User content template
 * Taken from Laravel Framework (www.laravel.com) and modified
 *
 * @license MIT
 *
 */
?>
						<div class="form-group">
							<label class="col-md-4 control-label">{{ $localizer->trans('register.form.name.label') }}</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="name" value="{{ old('name') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ $localizer->trans('register.form.email.label') }}</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ $localizer->trans('register.form.password.label') }}</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ $localizer->trans('register.form.confirmation.label') }}</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password_confirmation">
							</div>
						</div>
