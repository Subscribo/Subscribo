<?php
/**
 * Localized version of Laravel Auth: Login content template
 * Taken from Laravel Framework (www.laravel.com) and modified
 *
 * @license MIT
 *
 */
/** @var \Subscribo\Localizer $localizer */
$localizer = \Subscribo\Localization::localizer('app', 'auth');
?>
@extends('app')

@section('pageTitle')
    @parent | {{ $localizer->trans('login.addToPageTitle') }}
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">{{ $localizer->trans('login.heading') }}</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
                            {!! $localizer->trans('login.errorsHeading') !!}<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="/auth/login">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">{{ $localizer->trans('login.form.email.label') }}</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ $localizer->trans('login.form.password.label') }}</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember">{{ $localizer->trans('login.form.remember.label') }}
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                                    {{ $localizer->trans('login.form.submit.label') }}
								</button>

								<a href="/password/email">{{ $localizer->trans('login.form.submit.linkPasswordEmail') }}</a>
							</div>
						</div>

                        @include('subscribo::apiclientoauth.loginwithbuttons')

					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
