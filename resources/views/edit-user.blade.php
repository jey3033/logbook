<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User View | {{ $user_data['name'] }}</title>
    @include('include/head')
</head>
<body>
    @include('include/topbar')
    <div class="container-fluid">
        <div class="card w-100">
            <div class="card-header">
                <h1><img src="{!! $user_data->profile_path !!}" class="round-prof"> {{ $user_data->name }}</h1>
            </div>
            <div class="card-body">
                <form id="edit-user-form" method="post">
                    @csrf
                    <div class="mb-3">
                      <label for="edit-user-name" class="form-label">Name</label>
                      <input type="hidden" name="UUID" value="{!! $user_data['uuid'] !!}">
                      <input type="text" class="form-control" name="Name" id="edit-user-name" placeholder="Nama User" value="{!! $user_data['name'] !!}">
                    </div>
                    <div class="mb-3">
                        <label for="edit-user-email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="Email" id="edit-user-email" placeholder="Email User" value="{!! $user_data['email'] !!}">
                      </div>
                      <div class="mb-3">
                        <label for="edit-user-password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="Password" id="edit-user-password" placeholder="Password User">
                      </div>
                      <div class="mb-3">
                        <label for="edit-user-supervisor" class="form-label">Supervisor</label>
                        <select class="form-control" id="supervisor-select" name="supervisor">
                            <option value=""></option>
                            @foreach ($list_user as $user)
                                @if ($user['id'] == $user_data['supervisor'])
                                    <option value="{!! $user['id'] !!}" selected>{!! $user['name'] !!}</option>
                                @else
                                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                                @endif
                            @endforeach
                        </select>
                      </div>
                </form>
            </div>
            <div class="card-footer text-muted">
                <button type="button" id="edit-user-save" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#edit-user-save').click(function (e) { 
                e.preventDefault();
                $('#edit-user-name').removeClass('is-invalid');
                $('#edit-user-email').removeClass('is-invalid');
                if ($('#edit-user-name').val() && $('#edit-user-email').val()) {
                    $.ajax({
                        type: "POST",
                        url: "/user/update",
                        data: $('#edit-user-form').serializeArray(),
                        success: function (response) {
                            swal.fire({
                                icon: 'success',
                                title: 'User Edited'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }
                    });
                }else{
                    if (!$('#edit-user-name').val()) {
                        $('#edit-user-name').addClass('is-invalid');
                    }
                    if (!$('#edit-user-email').val()) {
                        $('#edit-user-email').addClass('is-invalid');
                    }
                }
            });
        });
    </script>
</body>
</html>