@foreach($oAuthLinks as $url => $label)
<div class="form-group">
    <div class="col-md-6 col-md-offset-4">
        <a href="{{ $url }}" class="btn btn-primary">{{ $label }}</a>
    </div>
</div>
@endforeach
