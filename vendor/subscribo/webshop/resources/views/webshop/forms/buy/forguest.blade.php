<div>
    @include('subscribo::apiclientcommon.formerrors')

    <form method="POST">
        {!! csrf_field() !!}
        @include('subscribo::webshop.forms.buy.cart')
        @include('subscribo::webshop.forms.buy.deliveryselector')
        @include('subscribo::webshop.forms.buy.address')
        @include('subscribo::webshop.forms.buy.billing')
        @include('subscribo::webshop.forms.buy.register')
        @include('subscribo::webshop.forms.buy.transactiongatewayselect')
        <button>{{ $localizer->trans('forms.buy.forguest.submitButton') }}</button>
    </form>
</div>
