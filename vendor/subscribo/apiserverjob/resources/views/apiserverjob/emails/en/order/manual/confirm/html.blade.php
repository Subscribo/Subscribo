<html>
    <head></head>
    <body>
    <p>Dear {{ $salutation }}</p>
    <p>Your order have been received.</p>
    <p>You have ordered following items:</p>
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
            <td colspan="6"  style="border:1px solid"><strong>Total</strong></td>
            <td style="border:1px solid">{{ $currencySymbol }}{{ $totalNetSum }}</td>
            <td style="border:1px solid"><strong>{{ $currencySymbol }}{{ $totalGrossSum }}</strong></td>
        </tr>
        </tfoot>
    </table>
    <p></p>
    <p>Kind regards</p>
    <p>Subscribo</p>
    </body>
</html>
