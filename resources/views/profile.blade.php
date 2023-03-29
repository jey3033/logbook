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
                <a class="nav-link active" id="profile-btn" aria-current="true" href="#">Profile</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="password-btn" href="#">Password</a>
              </li>
            </ul>
          </div>
          <div class="card-body" id="profile-tab">
            <h4 class="card-title">Profile</h4>
            <form id="profile-form" method="POST" action="/user/setphoto" enctype="multipart/form-data">
                @csrf
                <div class="d-inline">
                    <label for="Image" class="form-label d-block">Profile</label>
                    <img src="{{ $user_data['profile_path'] }}" alt="Profile Photo" class="img-fluid mb-3 prof-photo square-300">
                    <input class="form-control w-94 mb-3 d-inline" name="image" type="file" id="image" onchange="preview()">
                    <button role="button" onclick="clearImage()" class="btn btn-primary" style="margin-top: -5px;">Delete</button>
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
                <input type="hidden" name="uuid" value="{!! $user_data['uuid'] !!}">
                <button type="submit" class="btn btn-primary" id="update-data">Submit</button>
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

                <button type="button" class="btn btn-primary">Save Changes</button>
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
            $('#supervisor-select').select2({
                allowClear: true,
                placeholder: "Pilih Supervisor"
            });

            $('#password-tab').hide()
            $('#profile-btn').click(function (e) { 
                e.preventDefault();
                
                if (!$('#profile-btn').hasClass('active')) { $('#profile-btn').addClass('active') }
                if ($('#password-btn').hasClass('active')) { $('#password-btn').removeClass('active') }

                $('#profile-tab').show()
                $('#password-tab').hide()
            })

            $('#password-btn').click(function (e) { 
                e.preventDefault();
                
                if ($('#profile-btn').hasClass('active')) { $('#profile-btn').removeClass('active') }
                if (!$('#password-btn').hasClass('active')) { $('#password-btn').addClass('active') }

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
                        }else if(message.includes("match")){
                            $('#profile-old-password').addClass('is-valid');
                            $('#profile-old-password').removeClass('is-invalid');
                        }
                    }
                });
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
                }
                if(score > 0) {
                    $(this).addClass("is-valid");
                    $(this).removeClass("is-invalid");
                }
                console.log(score);
            });

            $('#profile-conf-password').keyup(function (e) { 
                let newPass = $('#profile-new-password').val();
                let confPass = $(this).val();
                if (confPass == newPass) {
                    $(this).removeClass('is-invalid');
                    $(this).addClass('is-valid');
                } else if (confPass != newPass) {
                    $(this).removeClass('is-valid');
                    $(this).addClass('is-invalid');
                }
            });
        });
    </script>
</body>
</html>