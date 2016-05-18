@extends('subscribo::checkout-plugin.frame')

@section('heading')
    {{ $localizer->trans('heading') }}
@endsection

@section('content')
    @if(Auth::check())
        @include('subscribo::client-checkout-common.forms.buy.foruser')
    @else
        @include('subscribo::client-checkout-common.forms.buy.forguest')
    @endif
    <hr>
    <a href="{{ route('subscribo.plugin.checkout.product.list') }}">{{ $localizer->trans('backToListLink') }}</a>
@endsection
