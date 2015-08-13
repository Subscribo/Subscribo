{{-- This template is to be included by questionarygroup template --}}
<?php $value = array_key_exists($key, $oldValues) ? $oldValues[$key] : $question->defaultValue; ?>
@if ($question->type === 'checkbox')
    <label class="col-md-4 control-label">
        <input type="checkbox" class="form-control" name="{{ $key }}" value="{{ $question->$checkboxValue }}" @if ($value === $question->$checkboxValue) checked="checked" @endif >
        {{ $question->text }}
    </label>
@else
    <label class="col-md-4 control-label">{{ $question->text }}</label>
    <div class="col-md-6">
        @if ($question->type === 'select')
        <select name="{{ $key }}">
            @foreach ($question->getSelectOptions() as $selectOptionKey => $selectOptionText)
                <option value="{{ $selectOptionKey }}" @if ($value === $selectOptionKey)) selected="selected" @endif >{{ $selectOptionText }}</option>
            @endforeach
        </select>
        @else
        <input type="{{ $question->type }}" class="form-control" name="{{ $key }}" @if ($value) value="{{ $oldValues[$key] }}" @endif >
        @endif
    </div>
@endif
