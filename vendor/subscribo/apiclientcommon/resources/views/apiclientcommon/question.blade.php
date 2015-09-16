{{-- This template is to be included by questionarygroup template --}}
<?php
use \Subscribo\RestCommon\Question;
/** @var \Subscribo\RestCommon\Question $question */
$value = array_key_exists($key, $oldValues) ? $oldValues[$key] : $question->defaultValue;
$type = $question->type;
$min = $question->getMinimumValue();
$max = $question->getMaximumValue();
$selectOptions = $question->getSelectOptions();

switch ($question->type) {
    case Question::TYPE_NUMBER_SELECT:
    case Question::TYPE_DAY:
    case Question::TYPE_MONTH:
    case Question::TYPE_YEAR:
        if (isset($min) and isset($max)) {
            $type = Question::TYPE_NUMBER_SELECT;
        } else {
            $type = Question::TYPE_NUMBER;
        }
}
if (Question::TYPE_NUMBER_SELECT === $type) {
    $type = Question::TYPE_SELECT;
    if (empty($value) and ( ! array_key_exists('', $selectOptions))) {
        $selectOptions[''] = '';
    }
    $step = $question->incrementStep ?: 1;
    if ($step > 0) {
        for ($i = $min; $i <= $max; ) {
            $selectOptions[strval($i)] = strval($i);
            $i = $i + $step;
        }
    } else {
        for ($i = $max; $i >= $min; ) {
            $selectOptions[strval($i)] = strval($i);
            $i = $i + $step;
        }
    }
}
$checkboxLabelClassAdd = '';
$labelClassAdd = '';
$inputClassAdd = '';
if (isset($totalColumns)) {
    if (empty($labelColumns)) {
        $labelColumns = $question->text ? 1 : 0;
    }
    $inputColumns = $totalColumns - $labelColumns;
    $inputColumns = $inputColumns ?: 1;
    $checkboxLabelClassAdd = 'col-md-'.$totalColumns.' ';
    $labelClassAdd = 'col-md-'.$labelColumns.' ';
    $inputClassAdd = 'col-md-'.$inputColumns.' ';
}
?>
@if ($type === 'checkbox')
    <label class="{{ $checkboxLabelClassAdd }} control-label">
        <input type="checkbox" class="form-control" name="{{ $key }}" value="{{ $question->$checkboxValue }}" @if ($value === $question->$checkboxValue) checked="checked" @endif >
        {{ $question->text }}
    </label>
@elseif ($type === 'radio')
    @if (isset($question->text))
        <label class="{{ $labelClassAdd }} control-label">{{ $question->text }}</label>
    @endif
    <div class="{{ $inputClassAdd }}">
        @foreach ($selectOptions as $selectOptionKey => $selectOptionText)
            <div class="radio">
                <label>
                    <input type="radio" name="{{ $key }}" value="{{ $selectOptionKey }}" @if ($value === strval($selectOptionKey))) checked="checked" @endif  >
                    {{ $selectOptionText }}
                </label>
            </div>
        @endforeach
    </div>
@else
    @if (isset($question->text))
        <label class="{{ $labelClassAdd }} control-label">{{ $question->text }}</label>
    @endif
    <div class="{{ $inputClassAdd }}">
        @if ($type === 'select')
        <select name="{{ $key }}" class="form-control">
            @foreach ($selectOptions as $selectOptionKey => $selectOptionText)
                <option value="{{ $selectOptionKey }}" @if ($value === strval($selectOptionKey))) selected="selected" @endif >{{ $selectOptionText }}</option>
            @endforeach
        </select>
        @else
        <input type="{{ $type }}" class="form-control" name="{{ $key }}" @if ($value) value="{{ $oldValues[$key] }}" @endif >
        @endif
    </div>
@endif
