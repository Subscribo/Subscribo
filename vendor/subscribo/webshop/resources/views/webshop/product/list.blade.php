@extends('app')

@section('pageTitle')
    @parent | {{ $localizer->trans('heading') }}
@endsection

@section('content')
<div class="container-fluid">
    <h1>{{ $localizer->trans('heading') }}</h1>
    <ul>
        @foreach($plans as $plan)
        <?php if (empty($plan['billing_plan']['period_denotation'])):
            $periodDenotation = '';
        else:
            $periodDenotation = ' ('.$plan['billing_plan']['period_denotation'].') ';
        endif; ?>
        <li>
            <h2>{{ $plan['name'] or $plan['identifier'] }}</h2>
            <h4>{{ $plan['description'] or '' }}</h4>
        </li>
        <ul>
            @foreach($plan['products'] as $product)
            <li>
                {{ $product['name'] or $product['identifier'] }}
                <small>{{ $product['description'] }}</small>
                <strong>
                    {{ $localizer->trans('grossPrice') }}
                    {{ $product['price_gross'] }}
                    {{ $product['price_currency_symbol'] }}
                    {{ $periodDenotation }}
                </strong>
                {{ $localizer->trans('netPrice') }}
                {{ $product['price_net'] }}
                {{ $product['price_currency_symbol'] }}
                {{ $periodDenotation }}
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
        @endforeach
    </ul>
</div>
@endsection
