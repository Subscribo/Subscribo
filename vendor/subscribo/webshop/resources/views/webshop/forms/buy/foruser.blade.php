<div>
    @include('subscribo::apiclientcommon.formerrors')

    <form method="POST">
        {!! csrf_field() !!}
        @include('subscribo::webshop.forms.buy.product')
        @include('subscribo::webshop.forms.buy.deliveryselector')
        @include('subscribo::webshop.forms.buy.addressselector', ['type' => ''])
        @include('subscribo::webshop.forms.buy.address', ['type' => ''])
        @include('subscribo::webshop.forms.buy.billing')
        @include('subscribo::webshop.forms.buy.transactiongatewayselect')
        <button>{{ $localizer->trans('forms.buy.foruser.submitButton') }}</button>
    </form>
</div>
