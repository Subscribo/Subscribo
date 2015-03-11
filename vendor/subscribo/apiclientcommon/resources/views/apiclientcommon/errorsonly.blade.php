@extends('app')

@if( ! empty($heading))
@section('pageTitle')
@parent
| {{ $heading }}
@endsection
@endif

@section('content')

<div class="alert alert-danger">
    <ul>
        @foreach($errorList as $error)
            <li> {{ $error }} </li>
        @endforeach
    </ul>
</div>

@endsection
