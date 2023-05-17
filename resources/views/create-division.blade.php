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
                        <option value="{!! $user['uuid'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                  </select>
                </div>
                @if (Auth::user()->id == 5)
                <div class="mb-3">
                  <label for="division-due-date" class="form-label">Due Date</label>
                  <input type="text" class="form-control" name="due_date" id="division-name" placeholder="Target Penyelesaian Request">
                </div>
                @endif
                <div class="form-check form-switch ps-0">
                    <label class="form-check-label float-start" for="division-status">Status</label>
                    <input class="form-check-input float-end" type="checkbox" name="status" id="division-status" checked>
                </div>
                <div class="mb-3">
                    <label for="division-member" class="form-label">Anggota Divisi</label>
                    <button type="button" class="btn btn-primary" id="add-new-member">Add Member</button>
                    <div id="division-member-list" class="row"></div>
                </div>
                <button type="button" class="btn btn-primary" id="division-create"><i class="fa-solid fa-users-rectangle"></i> Submit</button>
            </form>
          </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var rowid = 0;
            var cek = false;
            $('#add-new-member').click(function (e) { 
              e.preventDefault();
              let html = `<div class="col-md-3">
                  <div class="round-300 mb-3" id="img-prof-${rowid}"></div>
                  <div>
                    <select class="form-control member-list" id="division-member-${rowid}" name="member[]">
                      <option value=""></option>
                      @foreach ($userlist as $user)
                          <option value="{!! $user['uuid'] !!}">{!! $user['name'] !!}</option>
                      @endforeach
                    </select>
                  </div>
                </div>`;

              $('#division-member-list').append(html);
              $(`#division-member-${rowid}`).change(function (e) { 
                e.preventDefault();
                let img = $(this).parent().prev();
                $.ajax({
                  type: "get",
                  url: "/user/getprofpict",
                  data: {
                    uuid: $(this).val()
                  },
                  success: function (response) {
                    img.empty();
                    img.append(`<img class="round-300" src=${response}>`);
                    cek = checkDuplicates();
                    if (cek == true) {
                      swal.fire({
                        icon: "warning",
                        title: "Duplicate Member",
                        text: "Duplicate Member will not be added twice in the group",
                      })
                    }
                  }
                });
              });

              rowid++;
            });

            $('#division-create').click(function (e) { 
              e.preventDefault();
              $.ajax({
                type: "POST",
                url: "/division/store",
                data: $('#division-form').serializeArray(),
                success: function (response) {
                  location.href = "/division";
                }
              });
            });
        });
        
        function checkDuplicates() {
          let arr = [];
          let i = 0;
          $.each($('.member-list'), function (index, value) { 
             arr[i] = value.value;
             i++;
          });
          if (arr.length !== new Set(arr).size) {
            return true
          }
          return false;
        }
    </script>
    @include('include/firebase')
</body>
</html>