<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Management - List</title>

    @include('include/head')
    <style>
        .col-action{
            margin-right: 10px;
        }
    </style>
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
            <form id="user-filter-form">
                <div class="row justify-content-center align-items-center g-2">
                    <div class="col-md-4 mb-3">
                      <label for="filter-name" class="form-label">Nama</label>
                      <input type="text"
                        class="form-control" name="users.name" id="filter-name" placeholder="Nama">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="filter-email" class="form-label">Email</label>
                        <input type="text"
                          class="form-control" name="users.email" id="filter-email" placeholder="Email">
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="filter-status" class="form-label">Status</label>
                        <select class="form-select" name="users.activated" id="filter-status">
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
    
    <div class="container" id="user-list">
        <button type="button" class="btn btn-primary btn-lg mb-2" data-bs-toggle="modal" data-bs-target="#new-user-modal">
            <i class="fa-solid fa-user-plus"></i>  Create New
        </button>
        
        <div class="modal fade" id="new-user-modal" tabindex="-1" role="dialog" aria-labelledby="new-user-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="new-user-title">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="new-user-form" method="post">
                            @csrf
                            <div class="mb-3">
                              <label for="new-user-name" class="form-label">Name</label>
                              <input type="text" class="form-control" name="Name" id="new-user-name" placeholder="User's Name">
                            </div>
                            <div class="mb-3">
                                <label for="new-user-email" class="form-label">Email</label>
                                <input type="text" class="form-control" name="Email" id="new-user-email" placeholder="User's Email">
                            </div>
                            <div class="mb-3">
                                <label for="new-user-password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="Password" id="new-user-password" placeholder="User's Password">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="new-user-save" class="btn btn-primary"><i class="fa-solid fa-user-pen"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center align-items-center g-2">
            <div class="col-md-1 fw-bold text-center col-head col-action">Action</div>
            <div class="col-md-3 fw-bold col-head col-user">User</div>
            <div class="col-md-3 fw-bold col-head col-email">Email</div>
            <div class="col-md-3 fw-bold col-head col-status">Status</div>
        </div>
        <div id="user-list-body"></div>
    </div>
    
    <script>
        const newUser = new bootstrap.Modal(document.getElementById('new-user-modal'))
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

            $('#filter-submit').click(function (e) { 
                e.preventDefault();
                param = $('#user-filter-form').serializeArray();

                console.log(param);
                $('#user-list-body').empty();
                loadList();
            });

            function loadList() {
                $.ajax({
                    type: "GET",
                    url: "/listuser",
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
                                if (value['activated'] == 1) {
                                    chgsttsbtn = `<a class="dropdown-item" id="btn-deact-${value['uuid']}" href="/user/${value['uuid']}/deactuser"><i class="fa-solid fa-user-xmark"></i> Deactivate</a`;                      
                                }else if(value['activated'] == 2){
                                    chgsttsbtn = `<a class="dropdown-item" id="btn-act-${value['uuid']}" href="/user/${value['uuid']}/actuser"><i class="fa-solid fa-user-check"></i> Activate</a`;
                                }
                                let button = `<div class="col-md-1 dropdown open col-data col-action">
                                                    <a class="btn btn-primary dropdown-toggle" type="button" id="trigger-dropdown-${value['uuid']}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="trigger-dropdown-${value['uuid']}">
                                                        <a class="dropdown-item" href="/user/${value['uuid']}/edit"><i class="fa-solid fa-eye"></i> View</a>
                                                        ${chgsttsbtn}
                                                    </div>
                                                </div>
                                            </div>`;
                                
                                //content creation
                                let status = `<i class="fa-solid fa-check text-success"></i>`;
                                if (value['activated'] == 2) {
                                    status = `<i class="fa-solid fa-xmark text-danger"></i>`;
                                }
                                let data = `<div class="row justify-content-center align-items-center g-2 my-3">
                                        ${button}
                                        <div class="col-md-3 col-data col-user">${value['name']}</div>
                                        <div class="col-md-3 col-data col-email">${value['email']}</div>
                                        <div class="col-md-3 col-data col-status pl-20">${status}</div>
                                    </div>`;
                                
                                $('#user-list-body').append(data);

                                if (value['activated'] == 1) {
                                    $(`#btn-deact-${value['uuid']}`).click(function (e) { 
                                        e.preventDefault();
                                        swal.fire({
                                            icon: "warning",
                                            title: "Are You Sure ?",
                                            text: "Are you really sure you want to deactivate this user ?",
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

            $('#new-user-save').click(function (e) { 
                e.preventDefault();
                if ($('#new-user-name').val() && $('#new-user-email').val() && $('#new-user-password').val()) {
                    $('#new-user-name').removeClass('is-invalid');
                    $('#new-user-email').removeClass('is-invalid');
                    $('#new-user-password').removeClass('is-invalid');
                    $.ajax({
                        type: "POST",
                        url: "/user/create",
                        data: $('#new-user-form').serializeArray(),
                        success: function (response) {
                            location.reload();
                        }
                    });
                } else {
                    if (!$('#new-user-name').val()) {
                        $('#new-user-name').addClass('is-invalid');
                    }
                    if (!$('#new-user-email').val()) {
                        $('#new-user-email').addClass('is-invalid');
                    }
                    if (!$('#new-user-password').val()) {
                        $('#new-user-password').addClass('is-invalid');
                    }
                }
            });

            $('#new-user-form').submit(function (e) { 
                e.preventDefault();
                $('#new-user-save').click();
            });
        });
    </script>
    @include('include/firebase')
</body>
</html>