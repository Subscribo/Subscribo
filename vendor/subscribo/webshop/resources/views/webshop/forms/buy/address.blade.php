<div>
    {{ $localizer->trans('forms.buy.address.gender.title') }}

    <label>
        <input type="radio" name="gender" id="gender_m" value="man">
        {{ $localizer->trans('forms.buy.address.gender.man') }}
    </label>

    <label>
        <input type="radio" name="gender" id="gender_m" value="woman">
        {{ $localizer->trans('forms.buy.address.gender.woman') }}
    </label>
</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.firstName') }}
        <input name="first_name">
    </label>

    <label>
        {{ $localizer->trans('forms.buy.address.lastName') }}
        <input name="last_name">
    </label>
</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.street') }}
        <input name="street">
    </label>

</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.postCode') }}
        <input name="post_code">
    </label>

    <label>
        {{ $localizer->trans('forms.buy.address.city') }}
        <input name="city">
    </label>
</div>
<div>
    <label>
        {{ $localizer->trans('forms.buy.address.country') }}
        <input name="country">
    </label>
</div>

<div>
    <label>
        {{ $localizer->trans('forms.buy.address.phone') }}
        <input name="phone">
    </label>

</div>

<div>
    <label>
        {{ $localizer->trans('forms.buy.address.deliveryInformation') }}
        <textarea name="delivery_information"></textarea>
    </label>

</div>
