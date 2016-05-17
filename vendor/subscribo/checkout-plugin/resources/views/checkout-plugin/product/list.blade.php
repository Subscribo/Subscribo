@extends('subscribo::checkout-plugin.frame')

@section('heading')
{{ $localizer->trans('heading') }}
@endsection

@section('content')
    @include('subscribo::client-checkout-common.product.list', ['buyRoute' => 'subscribo.plugin.checkout.product.getBuy'])
@endsection
