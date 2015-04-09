{{-- This template is to be included by template containing form --}}
@if( count($errors) )
    <div class="alert alert-danger">
        {{ $errorTitle or '' }}
        <ul>
            @foreach ( $errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
