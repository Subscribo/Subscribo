{{-- This template is to be included by questionary template --}}
<?php
$items = $questionGroup->getQuestionItems();
$labelClassAdd = '';
if (isset($totalColumns)) {
    if (empty($labelColumns)) {
        $labelColumns = $question->text ? 1 : 0;
    }
    $itemsColumns = $totalColumns - $labelColumns;
    $count = count($items) ?: 1;
    $itemTotalColumns = floor($itemsColumns / $count) ?: 1;
    $itemLabelColumns = round($itemTotalColumns / 3) ?: 1;
    $labelClassAdd = 'col-md-'.$labelColumns.' ';
} else {
    $itemTotalColumns = null;
    $itemLabelColumn = null;}
?>
<div class="form-group">
    @if ($questionGroup->title)
        <label class="{{ $labelClassAdd }} control-label">{{ $questionGroup->title }}</label>
    @endif
    @foreach ($questionGroup->getQuestionItems() as $key => $item)
        @if ($item->type === 'group')
            @include('subscribo::apiclientcommon.questionGroup', ['questionGroup' => $item, 'totalColumns' => $itemTotalColumns, 'labelColumns' => $itemLabelColumns])
        @else
            @include('subscribo::apiclientcommon.question', ['key' => $key, 'question' => $item, 'totalColumns' => $itemTotalColumns, 'labelColumns' => $itemLabelColumns])
        @endif
    @endforeach
</div>
