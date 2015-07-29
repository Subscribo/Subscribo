<div>
    <label>
        {{ $localizer->trans('forms.buy.register.email')}}
        <input type="email" name="email" id="email" value="{{ old('email') }}">
    </label>
</div>

<div>
    <label>
        {{ $localizer->trans('forms.buy.register.password')}}
        <input type="password" name="password" id="password">
    </label>
</div>

<div>
    <label>
        {{ $localizer->trans('forms.buy.register.passwordConfirmation')}}
        <input type="password" name="password_confirmation" id="password_confirmation">
    </label>
</div>
