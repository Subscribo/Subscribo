<?php
/**
 * Localized version of Laravel home content template
 * Taken from Laravel Framework (www.laravel.com) and modified
 *
 * @license MIT
 *
 */
/** @var \Subscribo\Localizer $localizer */
$localizer = \Subscribo\Localization::localizer('app', 'main');
?>
@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">{{ $localizer->trans('home.heading') }}</div>

				<div class="panel-body">
                    {{ $localizer->trans('home.content') }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
