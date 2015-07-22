<div>
    <div>
        {{ $localizer->trans('forms.buy.transactiongatewayselect.title') }}
    </div>
    @foreach($transactionGateways as $transactionGateway)
    <div class="radio">
        <label>
            <input type="radio" name="transaction_gateway" value="{{$transactionGateway['id']}}"
                   id="transaction_gateway_{{$transactionGateway['id']}}" {{ $transactionGateway['is_default'] ? 'selected' : '' }} >
            <strong>{{ $transactionGateway['name'] }}</strong>
            {{$transactionGateway['description']}}

        </label>
    </div>
    @endforeach
</div>
