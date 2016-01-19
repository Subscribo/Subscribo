    <ul>
    @foreach($products as $product)
        <li>
            {{ $product['name'] }}
            <small>{{ $product['description'] }}</small>
            <strong> {{ $localizer->trans('grossPrice') }} {{ $product['price_gross'] }} {{ $product['price_currency_symbol'] }} </strong>
            {{ $localizer->trans('netPrice') }} {{ $product['price_net'] }} {{ $product['price_currency_symbol'] }}
            [ {{ $localizer->trans('tax') }} {{ $product['tax_percent'] }} %
            {{ $product['tax_category_short_name'] }} : {{ $product['tax_category_name'] }} ]
            <a
                href="{{ route($buyRoute, [$product['id']]) }}"
                class="btn btn-primary btn-xs">
                {{ $localizer->trans('buyLink') }}
            </a>
        </li>
    @endforeach
    </ul>
