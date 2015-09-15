<html>
    <head></head>
    <body>
        <p>Dear {{ $salutation }}</p>
        <p>A new sales order have been automatically created from your subscription.</p>
        <p>The order contain following items:</p>
        <table style="border-collapse: collapse">
            <thead>
                <tr>
                    <th style="border:1px solid">Name</th>
                    <th style="border:1px solid">Description</th>
                    <th style="border:1px solid">Price without tax per item</th>
                    <th style="border:1px solid">Tax</th>
                    <th style="border:1px solid">Price per item</th>
                    <th style="border:1px solid">Amount</th>
                    <th style="border:1px solid">Price without tax</th>
                    <th style="border:1px solid">Price</th>
                </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td style="border:1px solid">{{ $item['name'] }}</td>
                    <td style="border:1px solid">{{ $item['description'] }}</td>
                    <td style="border:1px solid">{{ $currencySymbol }}{{ $item['price_net'] }}</td>
                    <td style="border:1px solid">{{ $item['tax_category_short_name'] }} {{ $item['tax_percent'] }}%</td>
                    <td style="border:1px solid">{{ $currencySymbol }}{{ $item['price_gross'] }}</td>
                    <td style="border:1px solid">{{ $item['amount'] }}</td>
                    <td style="border:1px solid">{{ $currencySymbol }}{{ $item['total_price_net'] }}</td>
                    <td style="border:1px solid">{{ $currencySymbol }}{{ $item['total_price_gross'] }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="border:1px solid"><strong>Total</strong></td>
                    <td style="border:1px solid">{{ $currencySymbol }}{{ $totalNetSum }}</td>
                    <td style="border:1px solid"><strong>{{ $currencySymbol }}{{ $totalGrossSum }}</strong></td>
                </tr>
            </tfoot>
        </table>
        @if($anticipatedDeliveryStart and $anticipatedDeliveryEnd)
        <p></p>
        <p>
            @if($anticipatedDeliveryStart->format('Y-m-d') === $anticipatedDeliveryEnd->format('Y-m-d'))
                Your order should be delivered on {{ $anticipatedDeliveryStart->format('l jS \\o\\f F Y') }}
                between {{ $anticipatedDeliveryStart->format('G:i') }} and {{ $anticipatedDeliveryEnd->format('G:i') }}.
            @else
                Your order should be delivered between {{ $anticipatedDeliveryStart->format('l jS \\o\\f F Y G:i') }}
                and {{ $anticipatedDeliveryEnd->format('l jS \\o\\f F Y G:i') }}.
            @endif
        </p>
        @endif
        <p></p>
        <p>Kind regards</p>
        <p>Subscribo</p>
    </body>
</html>
