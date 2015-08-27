<div class="form-group <?php if ($errors->get('transaction_gateway')) { echo 'has-error';}?>">
    <div>
        {{ $localizer->trans('forms.buy.transactiongatewayselect.title') }}
    </div>
    @foreach($transactionGateways as $transactionGateway)
    <div class="radio">
        <label class="label-control">
            <input type="radio" name="transaction_gateway" value="{{$transactionGateway['id']}}"
                    id="transaction_gateway_{{$transactionGateway['id']}}"
                    {{ (old('transaction_gateway') == $transactionGateway['id']) ? 'checked="checked"' : '' }} >
            <strong>{{ $transactionGateway['name'] }}</strong>
            {{$transactionGateway['description']}}

        </label>
    </div>
    @endforeach
</div>
