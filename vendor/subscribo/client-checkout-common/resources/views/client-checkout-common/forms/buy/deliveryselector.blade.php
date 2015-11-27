<div class="form-group <?php if ($errors->get('delivery_id')) { echo 'has-error';}?>">
    <label class="control-label" for="delivery_id">{{ $localizer->trans('forms.buy.delivery.select.title') }}</label>

    <select class="form-control" name="delivery_id" id="delivery_id" >
    @foreach($deliveries as $delivery)
            <option value="{{ $delivery['id'] }}"
                @if(old('delivery_id') == $delivery['id'])
                    selected="selected"
                @endif
                >
                {{ $localizer->trans('forms.buy.delivery.select.text', [
                    '{start}' => date('j.n.Y', strtotime($delivery['start']))
                ]) }}
            </option>
    @endforeach
    </select>
</div>

<div class="form-group <?php if ($errors->get('subscription_period')) { echo 'has-error';}?>">
    <strong class="control-label">{{ $localizer->trans('forms.buy.subscriptionPeriod.select.title') }}</strong>
    @foreach($subscriptionPeriods as $subscriptionPeriodKey => $subscriptionPeriodText)
    <div class="radio">
        <label class="control-label">
            <input type="radio" name="subscription_period" id="subscription_period_{{ $subscriptionPeriodKey }}" value="{{ $subscriptionPeriodKey }}"
            @if((old('subscription_period') == $subscriptionPeriodKey) or (count($subscriptionPeriods) < 2))
                checked="checked"
            @endif
            >
            {{ $subscriptionPeriodText }}
        </label>
    </div>
    @endforeach
</div>

<div class="form-group <?php if ($errors->get('delivery_window_type_id')) { echo 'has-error';}?>">
    <strong class="control-label">{{ $localizer->trans('forms.buy.deliveryWindowType.select.title') }}</strong>
    @foreach($deliveryWindowTypes as $deliveryWindowTypeKey => $deliveryWindowTypeText)
    <div class="radio">
        <label class="control-label">
            <input type="radio" name="delivery_window_type_id" id="delivery_window_type_id_{{ $deliveryWindowTypeKey }}" value="{{ $deliveryWindowTypeKey }}"
            @if((old('delivery_window_type_id') == $deliveryWindowTypeKey) or (count($deliveryWindowTypes) < 2))
            checked="checked"
            @endif
            >
            {{ $deliveryWindowTypeText }}
        </label>
    </div>
    @endforeach
</div>
