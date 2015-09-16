<div>
    <label>
        {{ $localizer->trans('forms.buy.address.billingIsSame') }}
        <input type="checkbox" name="billing_is_same" id="billing_is_same" value="1"
               onchange="document.getElementById('billing_address_form_part').style.display = this.checked ? 'none' : 'block'"
            @if(old('billing_is_same') or (empty(old('billing_address_id')) and empty(old('billing_country'))))
                checked="checked"
            @endif
            >
    </label>
</div>
<div id="billing_address_form_part">
@if ($addresses)
    @include('subscribo::webshop.forms.buy.addressselector', ['type' => 'billing'])
@endif
@include('subscribo::webshop.forms.buy.address', ['type' => 'billing'])
</div>
<script>
    document.getElementById('billing_address_form_part').style.display = document.getElementById('billing_is_same').checked ? 'none' : 'block';
</script>
