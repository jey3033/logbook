<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create New Division</title>
    @include('include/head')
</head>
<body>
    @include('include/topbar')
    <div class="container-fluid">
        <div class="card text-center">
          <div class="card-header">
            Divisi Baru
          </div>
          <div class="card-body" id="division-tab">
            <h4 class="card-title">Division</h4>
            <form id="division-form" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="division-name" class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" id="division-name" placeholder="Name">
                </div>
                <div class="mb-3" id="supervisor-row">
                  <label for="division-supervisor" class="form-label">Supervisor</label>
                  <select class="form-control" id="division-supervisor" name="supervisor">
                    <option value=""></option>
                    @foreach ($userlist as $user)
                        <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-check form-switch">
                    <label class="form-check-label float-start" for="division-status">Status</label>
                    @if ($user_data['Status'] == 1)
                        <input class="form-check-input float-end" type="checkbox" name="status" id="division-status" checked>
                    @else
                        <input class="form-check-input float-end" type="checkbox" name="status" id="division-status">
                    @endif
                </div>
                <div class="mb-3">
                    <label for="division-member" class="form-label">Division's Member</label>
                    <button type="button" class="btn btn-primary">Add Member</button>
                    <div id="division-member-list"></div>
                </div>
                <button type="button" class="btn btn-primary" id="division-create"><i class="fa-solid fa-users-rectangle"></i> Submit</button>
            </form>
          </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            
        });
    </script>
</body>
</html>