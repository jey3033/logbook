<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logbook Login Page</title>

    @include('include/head')
    <style>
        #select2-supervisor-select-container{
            text-align: start !important;
        }
    </style>
</head>
<body>
    @include('include/topbar')
    <div class="container-fluid">
        <div class="card text-center">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <a class="nav-link active" id="profile-tab-btn">Profile</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="password-tab-btn">Password</a>
              </li>
            </ul>
          </div>
          <div class="card-body" id="profile-tab">
            <h4 class="card-title">Profile</h4>
            <form id="profile-form" method="POST" action="/user/setphoto" enctype="multipart/form-data">
                @csrf
                <div class="d-inline">
                    <label for="Image" class="form-label d-block">Profile</label>
                    <img src="{{ $user_data['profile_path'] }}" alt="Profile Photo" class="img-fluid mb-3 prof-photo">
                    <input class="form-control w-93 mb-3 d-inline" name="image" type="file" id="image" onchange="preview()">
                    <button role="button" onclick="clearImage()" class="btn btn-danger" style="margin-top: -5px;"><i class="fa-solid fa-trash-can"></i> Delete</button>
                </div>
                <img id="frame" src="" class="img-fluid mt-3 prev-image" />
                <div class="mb-3">
                  <label for="profile-name" class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" id="profile-name" placeholder="Name" value="{!! $user_data['name'] !!}">
                </div>
                <div class="mb-3">
                  <label for="profile-email" class="form-label">Email</label>
                  <input type="text"
                    class="form-control" name="email" id="profile-email" placeholder="Email" value="{!! $user_data['email'] !!}">
                </div>
                <div class="mb-3" id="supervisor-row">
                  <label for="profile-supervisor" class="form-label">Supervisor</label>
                  <select class="form-control disabled" id="supervisor-select" name="supervisor">
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
                <div class="form-check form-switch">
                    <label class="form-check-label float-start" for="TOTPEnable">Enable TOTP</label>
                    @if ($user_data['TOTPEnable'] == 1)
                        <input class="form-check-input float-end" type="checkbox" name="TOTP" id="TOTPEnable" checked>
                    @else
                        <input class="form-check-input float-end" type="checkbox" name="TOTP" id="TOTPEnable">
                    @endif
                </div>
                <input type="hidden" name="uuid" value="{!! $user_data['uuid'] !!}">
                <button type="submit" class="btn btn-primary" id="update-data"><i class="fa-solid fa-user-pen"></i> Submit</button>
            </form>
          </div>

          <div class="card-body" id="password-tab">
            <h4 class="card-title">Password</h4>
            <form id="password-form" method="post">
                @csrf
                <div class="mb-3">
                  <label for="profile-old-password" class="form-label">Old Password</label>
                  <input type="password" class="form-control" name="oldpass" id="profile-old-password" placeholder="Enter Your Current Password">
                </div>
                <div class="mb-3">
                  <label for="profile-new-password" class="form-label">New Password</label>
                  <input type="password"
                    class="form-control" name="newPass" id="profile-new-password" placeholder="Enter Your New Password">
                    <div id="recommendation" class="d-none alert alert-warning mt-2"></div>
                </div>
                <div class="mb-3">
                  <label for="profile-conf-password" class="form-label">Confirm New Password</label>
                  <input type="password"
                    class="form-control" name="confPass" id="profile-conf-password" placeholder="Re-enter Your New Password">
                </div>

                <button type="button" id="profile-password-change" class="btn btn-primary"><i class="fa-solid fa-lock"></i> Save Changes</button>
            </form>
          </div>
        </div>
    </div>
    <script>
        function preview() {
            frame.src = URL.createObjectURL(event.target.files[0]);
            console.log($('#image').val())
        }
        function clearImage() {
            document.getElementById('image').value = null;
            frame.src = "";
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#password-tab').hide()
            $('#profile-tab-btn').click(function (e) { 
                e.preventDefault();
                
                if ($('#profile-tab-btn').hasClass('active') == false) { $('#profile-tab-btn').addClass('active') }
                if ($('#password-tab-btn').hasClass('active') == true) { $('#password-tab-btn').removeClass('active') }

                $('#profile-tab').show()
                $('#password-tab').hide()
            })

            $('#password-tab-btn').click(function (e) { 
                e.preventDefault();
                
                if ($('#profile-tab-btn').hasClass('active') == true) { $('#profile-tab-btn').removeClass('active') }
                if ($('#password-tab-btn').hasClass('active') == false) { $('#password-tab-btn').addClass('active') }

                $('#profile-tab').hide()
                $('#password-tab').show()
            })

            $('#profile-old-password').blur(function (e) { 
                e.preventDefault();
                $.ajax({
                    type: "GET",
                    url: "/user/checkpassword",
                    data: {'oldpass': $(this).val()},
                    success: function (response) {
                        let dec = JSON.parse(response);
                        let message = dec['Message'];
                        if (message.includes("mismatch")) {
                            $('#profile-old-password').removeClass('is-valid');
                            $('#profile-old-password').addClass('is-invalid');
                            $('#profile-old-password').tooltip({ 'trigger': 'focus', 'title': 'password is not match', 'placement': 'bottom'});
                        }else if(message.includes("match")){
                            $('#profile-old-password').addClass('is-valid');
                            $('#profile-old-password').removeClass('is-invalid');
                            $('#profile-old-password').tooltip('disable');
                        }
                    }
                });

                check_pass();
            });

            $('#profile-new-password').keyup(function (e) { 
                let value = $(this).val();
                let score = 0;
                let message = "";
                if(value.match(/[A-Z]/g) != null) score=score+1;
                else {
                    score=score-1;
                    message += "<br>Capital Alphabet Recommended";
                }
                if(value.match(/[a-z]/g) != null) score=score+1;
                else {
                    score=score-1;
                    message += "<br>Lowercase Alphabet Recommended";
                }
                if(value.match(/[0-9]/g) != null) score=score+1;
                else {
                    score=score-1;
                    message += "<br>Number Recommended";
                }

                if(message){
                    $('#recommendation').removeClass('d-none');
                    $('#recommendation').empty();
                    $('#recommendation').append(`Here's Some Recommendation : ${message}`);
                }
                if(score < 0) {
                    $(this).removeClass("is-valid");
                    $(this).addClass("is-invalid");
                    $(this).tooltip({ 'trigger': 'focus', 'title': 'password baru tidak memenuhi kriteria', 'placement': 'top'});
                }
                if(score > 0) {
                    $(this).addClass("is-valid");
                    $(this).removeClass("is-invalid");
                    $(this).tooltip('disable');
                }
                console.log(score);

                check_pass();
            });

            $('#profile-conf-password').keyup(function (e) { 
                let newPass = $('#profile-new-password').val();
                let confPass = $(this).val();
                if (confPass == newPass) {
                    $(this).removeClass('is-invalid');
                    $(this).addClass('is-valid');
                    $(this).tooltip('disable');
                } else if (confPass != newPass) {
                    $(this).removeClass('is-valid');
                    $(this).addClass('is-invalid');
                    $(this).tooltip({ 'trigger': 'focus', 'title': 'password baru tidak sesuai', 'placement': 'bottom'});
                }

                check_pass();
            });

            $('#profile-password-change').hover(function (e) {
                check_pass();
            })

            $('#profile-password-change').click(function (e) {
                $.ajax({
                    type: "post",
                    url: "/user/password/change",
                    data: $('#password-form').serializeArray(),
                    success: function (response) {
                        swal.fire({
                            icon: 'success',
                            title: "Password Changed"
                        })
                    }
                });
            })

            function check_pass() {
                let found = false;
                $.each($('input[type=password]'), function (index, value) {
                    console.log(value.classList.contains('is-valid'));
                    if(!value.value) {
                        value.classList.add('is-invalid');
                        found = true;
                    } 
                    if(value.classList.contains('is-invalid')) {
                        found = true
                    }
                });
                if (found) {
                    $('#profile-password-change').fadeOut();
                }else{
                    $('#profile-password-change').fadeIn();
                }
            }

            @if (session('status'))
                swal.fire({
                    icon: `{{ session('status') }}`,
                    title: `{{ session('message') }}`
                });
            @endif
        });
    </script>
</body>
</html>