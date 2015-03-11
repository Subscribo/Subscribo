@extends('app')

@if( ! empty($heading))
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
                @if ($heading)
                <div class="panel-heading">{{ $heading }}</div>
                @endif
                <div class="panel-body">
                    @include('subscribo::apiclientcommon.formerrors', ['errorTitle' => $errorTitle])
                    <form method="POST" class="form-horizontal" role="form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @foreach ($questions as $key => $question)
                            @include('subscribo::apiclientcommon.question', ['key' => $key, 'question' => $question])
                        @endforeach
                        @if ($submit)
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" style="margin-right: 15px;">
                                    {{$submit}}
                                </button>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
