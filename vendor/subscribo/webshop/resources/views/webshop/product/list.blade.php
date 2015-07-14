@extends('app')

@section('pageTitle')
    @parent | {{ $localizer->trans('heading') }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading') }}</h1>

    <ul>
    @foreach($products as $product)
        <li>
            {{ $product['name'] }}
            <strong> {{ $localizer->trans('grossPrice') }} {{ $product['price_gross'] }} {{ $product['price_currency_symbol'] }} </strong>
            {{ $localizer->trans('netPrice') }} {{ $product['price_net'] }} {{ $product['price_currency_symbol'] }}
            [ {{ $localizer->trans('tax') }} {{ $product['tax_percent'] }} %
            {{ $product['tax_category_short_name'] }} : {{ $product['tax_category_name'] }} ]
            <a
                href="{{ route('subscribo.webshop.product.getBuy', [$product['id']]) }}"
                class="btn btn-primary btn-xs">
                {{ $localizer->trans('buyLink') }}
            </a>
        </li>
    @endforeach
    </ul>
</div>
@endsection
