{{ $localizer->trans('forms.buy.paymentmethod.selectTitle') }}
@foreach($paymentMethods as $paymentMethod)
<div class="radio">
    <label>
        <input type="radio" name="payment_method" value="{{$paymentMethod['id']}}"
               id="payment_method_{{$paymentMethod['id']}}" >
        <strong>{{ $paymentMethod['title'] }}</strong>
        {{$paymentMethod['description']}}

    </label>
</div>

@endforeach
