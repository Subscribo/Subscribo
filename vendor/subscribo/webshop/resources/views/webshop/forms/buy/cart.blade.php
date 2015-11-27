<table class="table">
    <thead>
        <tr>
            <th>{{ $localizer->trans('forms.buy.cart.name') }}</th>
            <th></th>
            <th>{{ $localizer->trans('forms.buy.cart.grossPrice') }}</th>
            <th>{{ $localizer->trans('forms.buy.cart.netPrice') }}</th>
            <th>{{ $localizer->trans('forms.buy.cart.tax') }}</th>
            <th><span class="<?php if ($errors->get('cart')) { echo 'has-error';}?>"><span class="control-label">{{ $localizer->trans('forms.buy.cart.amount') }}</span></span></th>
        </tr>

    </thead>
    <tbody>
    @foreach($products as $product)
        <tr class="form-group <?php if ($errors->get('item.'.$product['price_id'])) { echo 'has-error';}?>">
            <td><label class="control-label" for="item[{{ $product['price_id'] }}]">{{ $product['name'] }}</label></td>
            <td><small>{{ $product['description'] }}</small></td>
            <td><strong>{{ $product['price_gross'] }} {{ $product['price_currency_symbol'] }}</strong></td>
            <td>{{ $product['price_net'] }} {{ $product['price_currency_symbol'] }}</td>
            <td>{{ $product['tax_percent'] }} % [ {{ $product['tax_category_short_name'] }} : {{ $product['tax_category_name'] }} ]</td>
            <td>
                <input class="form-control" name="item[{{ $product['price_id'] }}]"
                       id="item[{{ $product['price_id'] }}]" value="{{ $oldItems[$product['price_id']] or '' }}">
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
