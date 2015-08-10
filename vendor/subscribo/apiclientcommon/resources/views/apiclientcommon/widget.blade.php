@extends('app')

@if ( ! empty($heading))
    @section('pageTitle')
        @parent
        | {{ $heading }}
    @endsection
@endif

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                @if ( ! empty($heading))
                <div class="panel-heading">{{ $heading }}</div>
                @endif
                <div class="panel-body">
                    {!! $widget->content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
