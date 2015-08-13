{{-- This template is to be included by questionary template --}}
<div class="form-group">
    @foreach ($questionGroup->getQuestions() as $key => $item)
        @if ($item->type === 'group')
            @include('subscribo::apiclientcommon.questionGroup', ['questionGroup' => $item])
        @elsif
            @include('subscribo::apiclientcommon.question', ['key' => $key, 'question' => $item])
        @endif
    @endforeach
</div>
