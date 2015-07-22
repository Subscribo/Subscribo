<div>
    <h3>For User:</h3>
    @include('subscribo::apiclientcommon.formerrors')

    <form method="POST">
        {!! csrf_field() !!}
        @include('subscribo::webshop.forms.buy.product')
        @include('subscribo::webshop.forms.buy.address')
        @include('subscribo::webshop.forms.buy.transactiongatewayselect')
        <button>{{ $localizer->trans('forms.buy.forguest.submitButton') }}</button>
    </form>
</div>
