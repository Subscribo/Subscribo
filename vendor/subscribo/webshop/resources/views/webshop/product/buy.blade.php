@extends('app')

@section('pageTitle')
@parent | {{ $localizer->trans('heading', ['{productName}' => $product['name']]) }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading', ['{productName}' => $product['name']]) }}</h1>
    <p>{{ $product['description'] }}</p>
    <h2> {{ $localizer->trans('grossPrice') }} {{ $product['price_gross'] }} {{ $product['price_currency_symbol'] }} </h2>
    <h3>{{ $localizer->trans('netPrice') }} {{ $product['price_net'] }} {{ $product['price_currency_symbol'] }}</h3>
    <h3> {{ $localizer->trans('tax') }} {{ $product['tax_percent'] }} %
    [ {{ $product['tax_category_short_name'] }}: {{ $product['tax_category_name'] }} ]</h3>
    @if(Auth::check())
        @include('subscribo::webshop.forms.buy.foruser', ['localizer' => $localizer->template()])
    @else
        @include('subscribo::webshop.forms.buy.forguest', ['localizer' => $localizer->template()])
    @endif
    <hr>
    <a href="{{ route('subscribo.webshop.product.list') }}">{{ $localizer->trans('backToListLink') }}</a>
</div>
@endsection
