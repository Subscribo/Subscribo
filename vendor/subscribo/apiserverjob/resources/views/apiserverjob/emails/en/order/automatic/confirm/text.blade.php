Dear {{ $salutation }}

A new sales order have been automatically created from your subscription.

The order contain following items:
@foreach($items as $item)
- {{ $item['name'] }} {{ $item['description'] ? "(".$item['description'].") " : "" }}- Price per item: {!! $currencySymbol !!}{{ $item['price_net'] }} (without tax) / {!! $currencySymbol !!}{{ $item['price_gross'] }} (with tax) - Tax: {{ $item['tax_percent'] }}% {{ $item['tax_category_short_name'] ? "(".$item['tax_category_short_name'].") " : "" }}- Amount: {{ $item['amount'] }} - Price: {!! $currencySymbol !!}{{ $item['total_price_net'] }} (without tax) / {!! $currencySymbol !!}{{ $item['total_price_gross'] }} (with tax)
@endforeach

Total price: {!! $currencySymbol !!}{{ $totalNetSum }} (without tax) / {!! $currencySymbol !!}{{ $totalGrossSum }} (with tax)
@if($anticipatedDeliveryStart and $anticipatedDeliveryEnd)

Your order should be delivered between {{ $anticipatedDeliveryStart->format('l jS \\o\\f F Y G:i') }} and {{ $anticipatedDeliveryEnd->format('l jS \\o\\f F Y G:i') }}.
@endif

Kind regards

Subscribo

