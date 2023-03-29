<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>List Log</title>

    @include('include/head')
</head>
<body>
    @include('include/topbar')

    <div class="accordion mb-3" id="filter-panel">
        <div class="accordion-item">
          <h2 class="accordion-header" id="filter-head">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filter-body" aria-expanded="true" aria-controls="filter-body">
              Filter
            </button>
          </h2>
          <div id="filter-body" class="accordion-collapse collapse show" aria-labelledby="filter-head" data-bs-parent="#filter-panel">
            <div class="accordion-body">
              <form id="log-filter-form">
                    <div class="row justify-content-center align-items-center g-2">
                        <div class="col-md-4 mb-3">
                            <label for="filter-tile" class="form-label">Log Title</label>
                            <input type="text"
                            class="form-control" name="logs.title" id="filter-title" placeholder="Judul Log">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="filter-log" class="form-label">Logs Description</label>
                            <input type="text"
                                class="form-control" name="logs.log" id="filter-log" placeholder="Isi Log">
                            </div>
                        <div class="col-md-4 mb-3">
                            <label for="filter-status" class="form-label">Status</label>
                            <select class="form-select" name="users.activated" id="filter-status">
                                <option value=""></option>
                                <option value="0">Not Accepted</option>
                                <option value="1">Accepted</option>
                                <option value="2">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center align-items-center g-2">
                        <div class="col-md-4 mb-3">
                            <label for="filter-user" class="form-label">User</label>
                            <select class="form-select" name="users.id" id="filter-user">
                                <option value=""></option>
                                @foreach ($list_user as $user)
                                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row col-md-8 mb-3">
                            <label for="filter-tgl" class="form-label text-center">Tanggal Update</label>
                            <div class="col-md-5 text-center">
                                <input type="date" class="form-control" name="tgl-update-min" id="tgl-min">
                            </div>
                            <div class="col-md-2 text-center align-self-center">
                                Sampai Dengan
                            </div>
                            <div class="col-md-5 text-center">
                                <input type="date" class="form-control" name="tgl-update-max" id="tgl-max">
                            </div>
                        </div>
                    </div>
                  <div class="row justify-content-center align-items-center g-2">
                      <button type="button" id="filter-submit" class="btn btn-primary">Submit</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    
    <div class="container" id="log-list">
        <!-- Button trigger modal -->
        <div class="mb-2">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#NewLog">
                Create New
            </button>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="NewLog" tabindex="-1" role="dialog" aria-labelledby="NewLogId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title" id="NewLogId">New Log</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <form id="NewLog-Form">
                                @csrf
                                <div class="mb-3">
                                  <label for="NewLog-Title" class="form-label">Log Title</label>
                                  <input type="text" name="title" id="NewLog-Title" class="form-control" placeholder="Log Title">
                                </div>
                                <div class="mb-3">
                                  <label for="NewLog-log" class="form-label">Log Content</label>
                                  <textarea class="form-control" name="log" id="NewLog-log" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="NewLog-Save" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="log-list-content"></div>
    </div>

    <script>
        $(document).ready(function () {
            var param;
            loadList();
            $('#filter-status').select2({
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
                allowClear: true
            })

            $('#filter-submit').click(function (e) { 
                e.preventDefault();
                param = $('#log-filter-form').serializeArray();

                console.log(param);
                $('#log-list-content').empty();
                loadList();
            });

            function loadList() {
                $.ajax({
                    type: "GET",
                    url: "/log",
                    data: {filter: param},
                    statusCode: {
                        204: function (response) {
                            $('#log-list').append("No Data to show");
                        },
                        200: function (response) {
                            rowid = 0;
                            let resp_data = JSON.parse(response)
                            $.each(resp_data.Data, function (index, value) {
                                // generate rows 
                                if (index % 3 == 0) {
                                    rowid++
                                    $('#log-list-content').append(`<div class="row" id="row-${rowid}">`)
                                }

                                //generate accept button
                                var accbtn = ""
                                var rejbtn = ""
                                if (value['name'] != `{!! $username !!}`) {
                                    accbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-success log-response" response=1>Accept</button>`
                                    rejbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-danger log-response" response=2>Reject</button>`
                                }

                                // generate badge
                                var badge = "";
                                if (value['status'] == 0) {
                                    badge = `<span class="badge rounded-pill bg-warning align-self-end w-25 position-relative log-badge">Not Accepted</span>`
                                }else if (value['status'] == 1) {
                                    badge = `<span class="badge rounded-pill bg-success align-self-end w-25 position-relative log-badge">Accepted</span>`
                                }else if (value['status'] == 2) {
                                    badge = `<span class="badge rounded-pill bg-danger align-self-end w-25 position-relative log-badge">Rejected</span>`
                                }

                                // image generate
                                let image = "";
                                if (value['profile_path']) {
                                    image = `<img src="${value['profile_path']}" alt="Profile Photo" class="small-icon">`;
                                }

                                // generate content
                                let html = `<div class="col-md-3 col-w-4 mx-2 my-2 card">
                                                ${badge}
                                                <div class="card-body">
                                                    <h4 class="card-title">${value['title']}</h4>
                                                    <h6 class="card-subtitle text-muted"> created by : ${image} ${value['name']}</h6>
                                                    <h6 class="card-subtitle text-muted"> last updated : ${value['updated_at']}</h6>
                                                </div>
                                                <hr style="margin:0;">
                                                <div class="card-body">
                                                    <p class="card-text">${value['log']}</p>
                                                </div>
                                                <div class="card-footer">
                                                    <a class="btn btn-primary me-1" href="/log/view/${value['uuid']}" role="button">View</a>
                                                    ${accbtn}
                                                    ${rejbtn}
                                                </div>
                                            </div>`;
                                $(`#row-${rowid}`).append(html)
                            });
                        } 
                    }
                });
            } 

            // save new log
            $('#NewLog-Save').click(function (e) { 
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "/log/store",
                    data: $('#NewLog-Form').serializeArray(),
                    success: function (response) {
                        swal.fire({
                            icon: 'success',
                            title: 'Log Created'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>