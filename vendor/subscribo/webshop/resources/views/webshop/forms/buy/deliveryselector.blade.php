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
    <label class="control-label" for="subscription_period">{{ $localizer->trans('forms.buy.subscriptionPeriod.select.title') }}</label>

    <select class="form-control" name="subscription_period" id="subscription_period" >
        @foreach($subscriptionPeriods as $subscriptionPeriodKey => $subscriptionPeriodText)
        <option value="{{ $subscriptionPeriodKey }}"
            @if(old('subscription_period') == $subscriptionPeriodKey)
                selected="selected"
            @endif
            >
            {{ $subscriptionPeriodText }}
        </option>
        @endforeach
    </select>
</div>
