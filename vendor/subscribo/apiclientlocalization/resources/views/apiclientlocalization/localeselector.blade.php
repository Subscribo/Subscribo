@foreach($localeLinks as $localeUrl => $localeLabel)
<li><a href="{{ $localeUrl }}">{{ $localeLabel}}</a></li>
@endforeach
