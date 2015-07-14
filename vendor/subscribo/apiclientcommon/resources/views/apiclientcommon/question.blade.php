{{-- This template is to be included by questionary template --}}
<div class="form-group">
    <label class="col-md-4 control-label">{{ $question->text }}</label>
    <div class="col-md-6">
        @if ($question->type === 'select')
        <select name="{{ $key }}">
            @foreach ($question->getSelectOptions() as $selectOptionKey => $selectOptionText)
                <option value="{{ $selectOptionKey }}" @if (( ! empty($oldValues[$key])) and ($oldValues[$key] === $selectOptionKey)) selected="selected" @endif >{{ $selectOptionText }}</option>
            @endforeach
        </select>
        @else
        <input type="{{ $question->type }}" class="form-control" name="{{ $key }}" @if ( ! empty($oldValues[$key])) value="{{ $oldValues[$key] }}" @endif >
        @endif
    </div>
</div>
