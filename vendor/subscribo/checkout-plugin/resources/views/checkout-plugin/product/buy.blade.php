@extends('app')

@section('pageTitle')
@parent | {{ $localizer->trans('heading') }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading') }}</h1>
    @if(Auth::check())
        @include('subscribo::client-checkout-common.forms.buy.foruser')
    @else
        @include('subscribo::client-checkout-common.forms.buy.forguest')
    @endif
    <hr>
    <a href="{{ route('subscribo.checkout.product.list') }}">{{ $localizer->trans('backToListLink') }}</a>
</div>
@endsection
