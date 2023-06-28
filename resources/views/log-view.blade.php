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
                <h1 class="d-inline">{!! $log->title !!}</h1>
                @if ($log->status == 5)
                    <span class="badge rounded-pill bg-success align-self-end log-badge">Selesai</span>
                @elseif ($log->status == 1 || $log->status == 3 || $log->status == 4)
                    <span class="badge rounded-pill bg-primary align-self-end log-badge">Proses</span>
                @endif
            </div>
            <div class="card-body">
                <span class="text-muted">Dibuat : {{ $log->created_at }}</span><br>
                <span class="text-muted">Terakhir diupdate : {{ $log->updated_at }}</span><br>
                <span class="text-muted">Pemohon : <img src="{{ $log->author()->profile_path }}" class="small-icon" alt=""> {{ $log->author()->name }}</span><br>
                <span class="text-muted">Status : {{ $log->get_status() }} </span>
                <hr>
                <p>{!! $log->log !!}</p>
            </div>
            @if (Auth::user()->id == $log->next_approver)
                <div class="card-footer text-muted align-items-center d-flex">
                    @if ($log->status == 1)
                        <form id="form-date">
                            @csrf
                            <label for="form-worker" class="form-label me-2 pt-2">Worker</label>
                            <select class="form-select w-25 d-inline me-1" name="worker" id="form-worker">
                                <option value=""></option>
                                @foreach ($list_worker as $user)
                                    <option value="{!! $user['uuid'] !!}">{!! $user['name'] !!}</option>
                                @endforeach
                            </select>
                            <label for="form-deadline" class="form-label me-2 pt-2">Target(Jumlah Hari)</label>
                            <input type="number" class="form-control w-25 d-inline me-1" name="date" min="0" id="form-deadline">
                            <input type="hidden" name="status" value="3">
                            <button type="button" class="btn btn-primary me-1" id="log-response-work" uuid="{{ $log->uuid }}" response={{ $log->status+1 }}>Accept</button>
                        </form>
                    @else
                        <button type="button" class="btn btn-primary log-response me-1"  uuid="{{ $log->uuid }}" response={{ $log->status+1 }}>Accept</button>
                    @endif
                    
                    <button type="button" class="btn btn-danger log-response"  uuid="{{ $log->uuid }}" response=2>Reject</button>
                </div>
            @endif
        </div>

        <div class="card w-100">
            <div class="card-header">
                <h1>History Log</h1>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped
                    table-hover	
                    table-borderless
                    table-primary
                    align-middle">
                        <thead class="">
                            <tr>
                                <th>User</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @foreach ($log->history() as $item)
                                    <tr class="table-primary">
                                        <td class="col-name">{{ $item->get_user()->name }}</td>
                                        <td class="col-status">{{ $item->get_status() }}</td>
                                        <td class="col-date">{{ $item->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                
                            </tfoot>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    @include('include/firebase')

    <script>
        $(document).ready(function () {
            $.each($('.col-date'), function (i, e) { 
                let data = $('.col-date').eq(i).text();
                let date = new Date(data);
                let text = date.toLocaleString("id", {
                    day: "2-digit",
                    month: "long",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                });
                text = text.replace("pukul", "");
                text = text.replace(".", ":");text = text.replace(".", ":");
                $('.col-date').eq(i).text(text);
            });

            $('#log-response-work').click(function (e) { 
                e.preventDefault();
                let uuid = $(this).attr("uuid");
                $.ajax({
                    type: "POST",
                    url: `/log/response/${uuid}`,
                    data: $('#form-date').serializeArray(),
                    success: function (response) {
                        location.reload();
                    },
                });
            });
        });
    </script>
</body>
</html>