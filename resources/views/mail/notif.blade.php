<!DOCTYPE html>
<html>
    <head>
        <title>There is New Log Uploaded</title>
    </head>
    <body>
        <h2>{{ $log->title }} uploaded</h2>
        <p>{{ Str::words(strip_tags($log->log), 10, '...') }}</p>

        Please see complete url in <a href="{{"localhost:8000/log/".$log->uuid}}">Here</a>
    </body>
</html> 