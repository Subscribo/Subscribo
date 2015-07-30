<?php
$type = empty($type) ? '' : $type;
$fieldName = ($type ? ($type.'_') : '').'address_id';
$addressFieldSet = ($type ? ($type.'_') : '').'address_fieldset';
?>
<h4>{{ $localizer->trans('forms.buy.address.select.title.'.($type ?: 'default')) }}</h4>
<select name="{{ $fieldName }}" id="{{ $fieldName }}" onchange="document.getElementById('{{ $addressFieldSet }}').style.display = value ? 'none' : 'block'">
@foreach($addresses as $address)
        <option value="{{ $address['id'] }}"
    @if((old($fieldName) == $address['id'])
    or ((is_null(old($fieldName))) and ( ! empty($address['is_default_'.($type ?: 'shipping')]))))
            selected="selected"
    @endif
            >{{ $address['descriptor'] }}
        </option>
@endforeach
    <option value=""
    @if(old($fieldName) === "")
    selected="selected"
    @endif
        >{{ $localizer->trans('forms.buy.address.select.add.'.($type ?: 'default')) }}</option>
</select>
