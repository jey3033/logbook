<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log View | {{ $log->title }}</title>
    @include('include/head')
</head>
<body>
    @include('include/topbar')
    <div class="container-fluid">
        <div class="card w-100">
            <div class="card-header">
                <h1>{!! $log->title !!}</h1>
            </div>
            <div class="card-body">
                <p>{!! $log->log !!}</p>
            </div>
            @if (Auth::user()->id != $log->user_id)
                <div class="card-footer text-muted">
                    <button type="button" class="btn btn-primary log-response"  uuid="{{ $log->uuid }}" response=1>Accept</button>
                    <button type="button" class="btn btn-danger log-response"  uuid="{{ $log->uuid }}" response=2>Reject</button>
                </div>
            @endif
        </div>
    </div>
</body>
</html>