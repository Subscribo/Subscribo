<div class="row">
    <form method="POST">
        @include('auth.forms.register', ['localizer' => $localizer->duplicate('auth', 'app')])
        @include('subscribo::webshop.forms.buy.paymentmethod')
        <button>{{ $localizer->trans('forms.buy.forguest.submitButton') }}</button>
    </form>

</div>
