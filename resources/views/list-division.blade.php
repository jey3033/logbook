<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Division Management - List</title>

    @include('include/head')
</head>
<body id="parent">
    @include('include/topbar')
    @include('include/loader')

    <div class="accordion mb-3" id="filter-panel">
      <div class="accordion-item">
        <h2 class="accordion-header" id="filter-head">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filter-body" aria-expanded="true" aria-controls="filter-body">
            Filter
          </button>
        </h2>
        <div id="filter-body" class="accordion-collapse collapse show" aria-labelledby="filter-head" data-bs-parent="#filter-panel">
          <div class="accordion-body">
            <form id="division-filter-form">
                <div class="row justify-content-center align-items-center g-2">
                    <div class="col-md-4 mb-3">
                      <label for="filter-name" class="form-label">Nama</label>
                      <input type="text"
                        class="form-control" name="divisions.name" id="filter-name" placeholder="Nama">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="filter-supervisor" class="form-label">Supervisor</label>
                        <select class="form-select" name="users.uuid" id="filter-supervisor">
                            <option value=""></option>
                            @foreach ($supervisorlist as $supervisor)
                                <option value="{!! $supervisor->uuid !!}">{!! $supervisor->name !!}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="filter-status" class="form-label">Status</label>
                        <select class="form-select" name="divisions.activated" id="filter-status">
                            <option value=""></option>
                            <option value="1">Active</option>
                            <option value="2">Non-Active</option>
                        </select>
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
    
    <div class="container" id="division-list">
        <button type="button" class="btn btn-primary btn-lg mb-2" id="new-division-btn">
            <i class="fa-solid fa-users"></i>  Create New
        </button>

        <div class="row justify-content-center align-items-center g-2">
            <div class="col-md-1 fw-bold text-center">Action</div>
            <div class="col-md-3 fw-bold">Name</div>
            <div class="col-md-3 fw-bold">Supervisor</div>
            <div class="col-md-3 fw-bold">Status</div>
        </div>
        <div id="division-list-body"></div>
    </div>
    
    <script>
        // const newUser = new bootstrap.Modal(document.getElementById('new-division-modal'))
        $(document).ready(function () {
            var param;
            loadList();
            $('.table-responsive').on('show.bs.dropdown', function () {
                $('.table-responsive').css( "overflow", "inherit" );
            });

            $('.table-responsive').on('hide.bs.dropdown', function () {
                $('.table-responsive').css( "overflow", "auto" );
            })

            $('#filter-status').select2({
                placeholder: "Pilih Status",
                minimumResultsForSearch: Infinity,
                allowClear: true
            })

            $('#filter-supervisor').select2({
                placeholder: "Pilih Supervisor",
                allowClear: true
            })

            $('#filter-submit').click(function (e) { 
                e.preventDefault();
                param = $('#user-filter-form').serializeArray();

                console.log(param);
                $('#division-list-body').empty();
                loadList();
            });

            $('#new-division-btn').click(function (e) { 
                e.preventDefault();
                location.href = "/division/create"
            });

            function loadList() {
                $.ajax({
                    type: "GET",
                    url: "/division/list",
                    data: {filter: param},
                    statusCode:{
                        204: function(response) {
                            let message = JSON.parse(response)['Message'];
                            let html = `<tr>
                                    <td colspan=4>${message}</td>
                                </tr>`;
                        },
                        200: function(response) {
                            let data = JSON.parse(response)['Data'];
                            $.each(data, function (index, value) { 
                                let supervisor = value['supervisor'];
                                //button creation
                                let chgsttsbtn = "";
                                if (value['active'] == 1) {
                                    chgsttsbtn = `<a class="dropdown-item" id="btn-deact-${value['uuid']}" href="/division/${value['uuid']}/deactivate"><i class="fa-solid fa-user-xmark"></i> Deactivate</a>`;                      
                                }else if(value['active'] == 2){
                                    chgsttsbtn = `<a class="dropdown-item" id="btn-act-${value['uuid']}" href="/division/${value['uuid']}/activate"><i class="fa-solid fa-user-check"></i> Activate</a>`;
                                }
                                let button = `<div class="col-md-1 dropdown open">
                                                    <a class="btn btn-primary dropdown-toggle" type="button" id="trigger-dropdown-${value['uuid']}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="trigger-dropdown-${value['uuid']}">
                                                        <a class="dropdown-item" href="/division/${value['uuid']}/edit"><i class="fa-solid fa-eye"></i> View</a>
                                                        ${chgsttsbtn}
                                                    </div>
                                                </div>`;
                                
                                //content creation
                                let status = `<i class="fa-solid fa-check text-success"></i>`;
                                if (value['active'] == 2) {
                                    status = `<i class="fa-solid fa-xmark text-danger"></i>`;
                                }
                                let data = `<div class="row justify-content-center align-items-center g-2 my-3">
                                        ${button}
                                        <div class="col-md-3">${value['name']}</div>
                                        <div class="col-md-3">${value['supervisor']}</div>
                                        <div class="col-md-3 pl-20">${status}</div>
                                    </div>`;
                                
                                $('#division-list-body').append(data);

                                if (value['activated'] == 1) {
                                    $(`#btn-deact-${value['uuid']}`).click(function (e) { 
                                        e.preventDefault();
                                        swal.fire({
                                            icon: "warning",
                                            title: "Are You Sure ?",
                                            text: "Are you really sure you want to deactivate this division ? User under this division wouldn't be able to login",
                                            showCancelButton: true,
                                            confirmButtonText: 'Yes, deactivate it!',
                                            cancelButtonText: 'No, cancel!',
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                location.href = $(`#btn-deact-${value['uuid']}`).attr('href');
                                            }
                                        })
                                    });
                                } else {
                                    $(`#btn-act-${value['uuid']}`).click(function (e) { 
                                        e.preventDefault();
                                        swal.fire({
                                            icon: "warning",
                                            title: "Are You Sure ?",
                                            text: "Are you really sure you want to activate this user ?",
                                            showCancelButton: true,
                                            confirmButtonText: 'Yes, activate it!',
                                            cancelButtonText: 'No, cancel!',
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                location.href = $(`#btn-act-${value['uuid']}`).attr('href');
                                            }
                                        })
                                    });
                                }
                            });
                        }
                    },
                });
            }
        });
    </script>
    @include('include/firebase')
</body>
</html>