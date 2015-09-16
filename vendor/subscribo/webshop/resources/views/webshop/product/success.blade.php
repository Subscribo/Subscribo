@extends('app')

@section('pageTitle')
@parent | {{ $localizer->trans('heading') }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading') }}</h1>
    <p>{{ $localizer->trans('text') }}</p>
</div>
@endsection
