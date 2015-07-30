<?php
$type = empty($type) ? '' : $type;
$prefix = ($type ? ($type.'_') : '');
?>

<fieldset id="{{ $prefix.'address_fieldset'}}">

    <div>
        {{ $localizer->trans('forms.buy.address.gender.title') }}

        <label>
            <input type="radio" name="{{ $prefix.'gender' }}" id="{{ $prefix.'gender_man' }}" value="man"
            {{ (old($prefix.'gender') === 'man') ? 'checked="checked"' : '' }}>
            {{ $localizer->trans('forms.buy.address.gender.man') }}
        </label>

        <label>
            <input type="radio" name="{{ $prefix.'gender' }}" id="{{ $prefix.'gender_woman' }}" value="woman"
            {{ (old($prefix.'gender') === 'woman') ? 'checked="checked"' : '' }}>
            {{ $localizer->trans('forms.buy.address.gender.woman') }}
        </label>
    </div>
    <div>
        <label>
            {{ $localizer->trans('forms.buy.address.firstName') }}
            <input name="{{ $prefix.'first_name' }}" id="{{ $prefix.'first_name' }}" value="{{ old($prefix.'first_name') }}">
        </label>

        <label>
            {{ $localizer->trans('forms.buy.address.lastName') }}
            <input name="{{ $prefix.'last_name' }}" id="{{ $prefix.'last_name' }}" value="{{ old($prefix.'last_name') }}">
        </label>
    </div>
    <div>
        <label>
            {{ $localizer->trans('forms.buy.address.street') }}
            <input name="{{ $prefix.'street' }}" id="{{ $prefix.'street' }}" value="{{ old($prefix.'street') }}">
        </label>

    </div>
    <div>
        <label>
            {{ $localizer->trans('forms.buy.address.postCode') }}
            <input name="{{ $prefix.'post_code' }}" id="{{ $prefix.'post_code' }}" value="{{ old($prefix.'post_code') }}">
        </label>

        <label>
            {{ $localizer->trans('forms.buy.address.city') }}
            <input name="{{ $prefix.'city' }}" id="{{ $prefix.'city' }}" value="{{ old($prefix.'city') }}">
        </label>
    </div>
    <div>
        <label>
            {{ $localizer->trans('forms.buy.address.country') }}
            <input name="{{ $prefix.'country' }}" id="{{ $prefix.'country' }}" value="{{ old($prefix.'country') }}">
        </label>
    </div>
    @if ($type != 'billing')

    <div>
        <label>
            {{ $localizer->trans('forms.buy.address.phone') }}
            <input name="{{ $prefix.'phone' }}" id="{{ $prefix.'phone' }}" value="{{ old($prefix.'phone') }}">
        </label>

    </div>

    <div>
        <label>
            {{ $localizer->trans('forms.buy.address.deliveryInformation') }}
            <textarea name="{{ $prefix.'delivery_information' }}" id="{{ $prefix.'delivery_information' }}">{{ old($prefix.'delivery_information') }}</textarea>
        </label>

    </div>
    @endif

</fieldset>
<script>
    (function (){
        var addressSelect = document.getElementById('{{ $prefix.'address_id'}}');
        if (addressSelect) {
            document.getElementById('{{ $prefix.'address_fieldset'}}').style.display = addressSelect.options[addressSelect.selectedIndex].value ? 'none' : 'block';
        }
    })();
</script>
