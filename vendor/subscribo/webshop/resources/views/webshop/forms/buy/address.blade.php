<div>
    {{ $localizer->trans('forms.buy.address.gender.title') }}

    <label>
        <input type="radio" name="gender" id="gender_m" value="man" {{ (old('gender') === 'man') ? 'checked="checked"' : '' }}>
        {{ $localizer->trans('forms.buy.address.gender.man') }}
    </label>

    <label>
        <input type="radio" name="gender" id="gender_m" value="woman" {{ (old('gender') === 'woman') ? 'checked="checked"' : '' }}>
        {{ $localizer->trans('forms.buy.address.gender.woman') }}
    </label>
</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.firstName') }}
        <input name="first_name" id="first_name" value="{{ old('first_name') }}">
    </label>

    <label>
        {{ $localizer->trans('forms.buy.address.lastName') }}
        <input name="last_name" id="last_name" value="{{ old('last_name') }}">
    </label>
</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.street') }}
        <input name="street" id="street" value="{{ old('street') }}">
    </label>

</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.postCode') }}
        <input name="post_code" id="post_code" value="{{ old('post_code') }}">
    </label>

    <label>
        {{ $localizer->trans('forms.buy.address.city') }}
        <input name="city" id="city" value="{{ old('city') }}">
    </label>
</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.country') }}
        <input name="country" id="country" value="{{ old('country') }}">
    </label>
</div>

<div>
    <label>
        {{ $localizer->trans('forms.buy.address.phone') }}
        <input name="phone" id="phone" value="{{ old('phone') }}">
    </label>

</div>

<div>
    <label>
        {{ $localizer->trans('forms.buy.address.deliveryInformation') }}
        <textarea name="delivery_information" id="delivery_information">{{ old('delivery_information') }}</textarea>
    </label>

</div>
