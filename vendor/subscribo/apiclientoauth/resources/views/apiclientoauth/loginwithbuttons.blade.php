@foreach($providers as $url => $name)
<div class="form-group">
    <div class="col-md-6 col-md-offset-4">
        <a href="{{ $url }}" class="btn btn-primary">Login with {{ $name }}</a>
    </div>
</div>
@endforeach
