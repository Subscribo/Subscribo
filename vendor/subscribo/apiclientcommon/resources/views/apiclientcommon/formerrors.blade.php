{{-- This template is to be included by template containing form --}}
@if( count($errors) )
    <div class="alert alert-danger">
        {{ $errorTitle or '' }}
        <ul>
            @foreach ( array_unique($errors->all()) as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
