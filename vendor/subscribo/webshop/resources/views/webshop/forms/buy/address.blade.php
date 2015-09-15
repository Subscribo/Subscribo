<?php
$type = empty($type) ? '' : $type;
$prefix = ($type ? ($type.'_') : '');
?>

<fieldset id="{{ $prefix.'address_fieldset'}}">

    <div class="form-group <?php if ($errors->get('gender')) { echo 'has-error';}?>">
        <strong class="control-label">{{ $localizer->trans('forms.buy.address.gender.title') }}</strong>

        <label class="control-label" for="{{ $prefix.'gender_man' }}">
            <input type="radio" name="{{ $prefix.'gender' }}" id="{{ $prefix.'gender_man' }}" value="man"
            {{ (old($prefix.'gender') === 'man') ? 'checked="checked"' : '' }}>
            {{ $localizer->trans('forms.buy.address.gender.man') }}
        </label>

        <label class="control-label" for="{{ $prefix.'gender_woman' }}">
            <input type="radio" name="{{ $prefix.'gender' }}" id="{{ $prefix.'gender_woman' }}" value="woman"
            {{ (old($prefix.'gender') === 'woman') ? 'checked="checked"' : '' }}>
            {{ $localizer->trans('forms.buy.address.gender.woman') }}
        </label>
    </div>
    <div>
        <span class="form-group <?php if ($errors->get('first_name')) { echo 'has-error';}?>">
            <label class="control-label" for="{{ $prefix.'first_name' }}">
                {{ $localizer->trans('forms.buy.address.firstName') }}
                <input class="form-control" name="{{ $prefix.'first_name' }}" id="{{ $prefix.'first_name' }}" value="{{ old($prefix.'first_name') }}">
            </label>
        </span>
        <span class="form-group <?php if ($errors->get('last_name')) { echo 'has-error';}?>">
            <label class="control-label" for="{{ $prefix.'last_name' }}">
                {{ $localizer->trans('forms.buy.address.lastName') }}
                <input class="form-control" name="{{ $prefix.'last_name' }}" id="{{ $prefix.'last_name' }}" value="{{ old($prefix.'last_name') }}">
            </label>
        </span>
    </div>
    <div class="form-group <?php if ($errors->get('street')) { echo 'has-error';}?>">
        <label class="control-label" for="{{ $prefix.'street' }}">
            {{ $localizer->trans('forms.buy.address.street') }}
            <input class="form-control" name="{{ $prefix.'street' }}" id="{{ $prefix.'street' }}" value="{{ old($prefix.'street') }}">
        </label>
    </div>
    <div>
        <span class="form-group <?php if ($errors->get('post_code')) { echo 'has-error';}?>">
            <label class="control-label" for="{{ $prefix.'post_code' }}">
                {{ $localizer->trans('forms.buy.address.postCode') }}
                <input class="form-control" name="{{ $prefix.'post_code' }}" id="{{ $prefix.'post_code' }}" value="{{ old($prefix.'post_code') }}">
            </label>
        </span>
        <span class="form-group <?php if ($errors->get('city')) { echo 'has-error';}?>">
            <label class="control-label" for="{{ $prefix.'city' }}">
                {{ $localizer->trans('forms.buy.address.city') }}
                <input class="form-control" name="{{ $prefix.'city' }}" id="{{ $prefix.'city' }}" value="{{ old($prefix.'city') }}">
            </label>
        </span>

    </div>
    <div class="form-group <?php if ($errors->get('country')) { echo 'has-error';}?>">
        <label class="control-label" for="{{ $prefix.'country' }}">
            {{ $localizer->trans('forms.buy.address.country') }}
            <input class="form-control" name="{{ $prefix.'country' }}" id="{{ $prefix.'country' }}" value="{{ old($prefix.'country') }}">
        </label>
    </div>
    @if ($type != 'billing')

    <div class="form-group <?php if ($errors->get('phone')) { echo 'has-error';}?>">
        <label class="control-label" for="{{ $prefix.'phone' }}">
            {{ $localizer->trans('forms.buy.address.phone') }}
            <input class="form-control" name="{{ $prefix.'phone' }}" id="{{ $prefix.'phone' }}" value="{{ old($prefix.'phone') }}">
        </label>

    </div>

    <div class="form-group <?php if ($errors->get('delivery_information')) { echo 'has-error';}?>">
        <label class="control-label" for="{{ $prefix.'delivery_information' }}">
            {{ $localizer->trans('forms.buy.address.deliveryInformation') }}
            <textarea class="form-control" name="{{ $prefix.'delivery_information' }}" id="{{ $prefix.'delivery_information' }}">{{ old($prefix.'delivery_information') }}</textarea>
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
