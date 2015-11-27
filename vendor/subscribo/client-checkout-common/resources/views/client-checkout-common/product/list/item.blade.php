<? $loc = $localizer->template('messages', 'client-checkout-common')->setPrefix('template.product.list.item') ?>

            {{ $product['name'] }}
            <small>{{ $product['description'] }}</small>
            <strong> {{ $loc->trans('grossPrice') }} {{ $product['price_gross'] }} {{ $product['price_currency_symbol'] }} </strong>
            {{ $loc->trans('netPrice') }} {{ $product['price_net'] }} {{ $product['price_currency_symbol'] }}
            [ {{ $loc->trans('tax') }} {{ $product['tax_percent'] }} %
            {{ $product['tax_category_short_name'] }} : {{ $product['tax_category_name'] }} ]
