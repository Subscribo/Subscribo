<html>
    <head></head>
    <body>
        <p>{{ $heading or ''}}</p>
        @foreach($paragraphs as $paragraph)
        <p>{{ $paragraph }}</p>
        @endforeach
        <p>{{ $ending or ''}}</p>

        Subscribo

    </body>
</html>
