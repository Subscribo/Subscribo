@if ( ! empty($flashMessageText))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ $ariaLabelClose }}"><span aria-hidden="true">&times;</span></button>
        {{ $flashMessageText }}
    </div>
@endif
