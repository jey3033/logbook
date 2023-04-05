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
                      <button type="button" id="filter-submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Submit</button>
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
                <i class="fa-solid fa-file-circle-plus"></i> Create New
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
                        <button type="button" id="NewLog-Save" class="btn btn-primary"><i class="fa-solid fa-file-pen"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="log-list-content"></div>
    </div>

    <script>
        const newlog = new bootstrap.Modal(document.getElementById('NewLog'));
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
                                var accbtn = "";
                                var rejbtn = "";
                                var delbtn = "";
                                var edtbtn = "";
                                if (value['name'] != `{!! $username !!}`) {
                                    accbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-success log-response" response=1><i class="fa-solid fa-file-circle-check"></i> Accept</button>`
                                    rejbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-danger log-response" response=2><i class="fa-solid fa-file-circle-xmark"></i> Reject</button>`
                                }else if(value['status'] == 0){
                                    delbtn = `<a href="/log/${value['uuid']}/delete" type="button" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i> Delete</a>`
                                    edtbtn = `<a type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#edit-modal-${value['uuid']}"><i class="fa-solid fa-file-pen"></i> Edit</a>`
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
                                                    <a class="btn btn-primary me-1" href="/log/${value['uuid']}" role="button"><i class="fa-solid fa-eye"></i> View</a>
                                                    ${edtbtn} ${accbtn}
                                                    ${rejbtn} ${delbtn}
                                                </div>
                                            </div>`;
                                $(`#row-${rowid}`).append(html)
                                
                                // generate modal
                                let modal = `
                                <div class="modal fade" id="edit-modal-${value['uuid']}" tabindex="-1" role="dialog" aria-labelledby="edit-modal-title-${value['uuid']}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="edit-modal-title-${value['uuid']}">Modal title</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container-fluid">
                                                    <form id="Log-${value['uuid']}-Form" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                        <label for="Log-${value['uuid']}-Title" class="form-label">Log Title</label>
                                                        <input type="text" name="title" id="Log-${value['uuid']}-Title" class="form-control" placeholder="Log Title" value="${value['title']}">
                                                        </div>
                                                        <div class="mb-3">
                                                        <label for="Log-${value['uuid']}-log" class="form-label">Log Content</label>
                                                        <textarea class="form-control" name="log" id="Log-${value['uuid']}-log" rows="3">${value['log']}</textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-form="${value['uuid']}" id="Log-${value['uuid']}-Edit">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                `

                                $('#log-list').append(modal);

                                $(`#Log-${value['uuid']}-Edit`).click(function() {
                                    if($(`#Log-${value['uuid']}-Title`).val() && $(`#Log-${value['uuid']}-log`).val()){
                                        $(`#Log-${value['uuid']}-Title`).removeClass('is-invalid');
                                        $(`#Log-${value['uuid']}-log`).removeClass('is-invalid');
                                        let formnumber = $(this).data('form');
                                        let ser_data = $(`#Log-${formnumber}-Form`).serializeArray();
                                        $.ajax({
                                            type: "POST",
                                            url: "/log/update/"+formnumber,
                                            data: ser_data,
                                            success: function (response) {
                                                swal.fire({
                                                    icon: 'success',
                                                    title: 'Log Updated'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        location.reload();
                                                    }
                                                });
                                            }
                                        });
                                    }else{
                                        if (!$(`#Log-${value['uuid']}-Title`).val()) {
                                            $(`#Log-${value['uuid']}-Title`).addClass('is-invalid');
                                        }
                                        if (!$(`#Log-${value['uuid']}-log`).val()) {
                                            $(`#Log-${value['uuid']}-log`).addClass('is-invalid');
                                        }
                                    }
                                })

                                var edit = {};
                                var uuid = value['uuid'];
                                edit[uuid] = new bootstrap.Modal(document.getElementById('NewLog'));

                                $(`#Log-${value['uuid']}-Form`).submit(function() {
                                    $(`#Log-${value['uuid']}-Edit`).click();
                                })
                            });
                        } 
                    }
                });
            } 

            // save new log
            $(document).keydown(function (e) { 
                if (e.which == 13){
                    e.preventDefault();
                    $('#NewLog-Save').click();
                }
            });

            $('#NewLog-Save').click(function (e) { 
                e.preventDefault();
                if ($('#NewLog-Title').val() && $('#NewLog-log').val()) {
                    $('#NewLog-Title').removeClass('is-invalid');
                    $('#NewLog-log').removeClass('is-invalid');
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
                } else {
                    if (!$('#NewLog-Title').val()) {
                        $('#NewLog-Title').addClass('is-invalid');
                    }
                    if (!$('#NewLog-log').val()) {
                        $('#NewLog-log').addClass('is-invalid');
                    }
                }
            });
        });
    </script>
</body>
</html>