<!DOCTYPE html>
<html>
    <head>
        <title>Log 
            @if ($log->status == 1)
                Diterima
            @elseif ($log->status == 2)
                Ditolak
            @endif
        </title>
    </head>
    <body>
        <h2>
            {{ $log->title }} 
            @if ($log->status == 1)
                Diterima
            @elseif ($log->status == 2)
                Ditolak
            @endif
        </h2>
    </body>
</html> 