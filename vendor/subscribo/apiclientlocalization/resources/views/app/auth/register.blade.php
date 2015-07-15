<?php
/**
 * Localized version of part of Laravel Auth: Register User content template
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
    @parent | {{ $localizer->trans('register.addToPageTitle') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $localizer->trans('register.heading') }}</div>
                <div class="panel-body">
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        {!! $localizer->trans('register.errorsHeading') !!}<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

					<form class="form-horizontal" role="form" method="POST" action="/auth/register">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
                            @include('auth.forms.register')
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
                                    {{ $localizer->trans('register.form.submit.label') }}
								</button>
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
