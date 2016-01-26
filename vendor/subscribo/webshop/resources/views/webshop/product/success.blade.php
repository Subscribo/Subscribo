@extends('app')

@section('pageTitle')
@parent | {{ $localizer->trans('heading') }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading') }}</h1>
    @include('subscribo::client-checkout-common.product.success')
</div>
@endsection
