<div>
    @include('subscribo::client-checkout-common.forms.buy.flashmessage')
    @include('subscribo::apiclientcommon.formerrors')

    <form method="POST">
        {!! csrf_field() !!}
        @include('subscribo::client-checkout-common.forms.buy.cart')
        @include('subscribo::client-checkout-common.forms.buy.deliveryselector')
        @include('subscribo::client-checkout-common.forms.buy.address')
        @include('subscribo::client-checkout-common.forms.buy.billing')
        @include('subscribo::client-checkout-common.forms.buy.register')
        @include('subscribo::client-checkout-common.forms.buy.transactiongatewayselect')
        <button>{{ $localizer->trans('forms.buy.forguest.submitButton') }}</button>
    </form>
</div>
