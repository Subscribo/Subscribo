<h4>{{ $localizer->trans('forms.buy.delivery.select.title') }}</h4>
<select name="delivery_id" id="delivery_id" >
@foreach($deliveries as $delivery)
        <option value="{{ $delivery['id'] }}">
            {{ $localizer->trans('forms.buy.delivery.select.text', ['{start}' => $delivery['start']]) }}
        </option>
@endforeach
</select>
