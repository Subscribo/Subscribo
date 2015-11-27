@extends('app')

@section('pageTitle')
@parent | {{ $localizer->trans('heading') }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading') }}</h1>
    @if(Auth::check())
        @include('subscribo::webshop.forms.buy.foruser', ['localizer' => $localizer->template()])
    @else
        @include('subscribo::webshop.forms.buy.forguest', ['localizer' => $localizer->template()])
    @endif
    <hr>
    <a href="{{ route('subscribo.webshop.product.list') }}">{{ $localizer->trans('backToListLink') }}</a>
</div>
@endsection
