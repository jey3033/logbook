<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logbook Login Page</title>

    @include('include/head')
</head>
<body>
    <div style="height: 100vh" class="container-fluid d-flex justify-content-center align-items-center bg-pg">
        <div class="card w-25">
            <div class="card-body">
                <h4 class="card-title">Welcome to Log Book</h4>
                <hr>
                <form method="post" id="login-form">
                    @csrf
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="text" class="form-control" name="email" id="email" placeholder="Email eg. johndoe@yopmail.com">
                    </div>
                    <div class="form-group">
                      <label for="password">Password</label>
                      <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                    </div>

                    <div class="mt-1 text-center">
                        <button id="login-submit" type="button" class="btn btn-primary">Submit</button>
                        <!-- tambahkan script di bawah ini untuk membuat tombol signin google -->
                        <a class="btn btn-danger" href="{{ '/auth/redirect'}}">Sign In With Google</a>
                
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
    <div class="modal fade" id="modal-totp" tabindex="-1" role="dialog" aria-labelledby="totp-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="totp-title">Here's Your TOTP</h5>
                    <button type="button" id="close-totp" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="totp-body">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-verify" tabindex="-1" role="dialog" aria-labelledby="verify-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verify-title">Verify Your OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="verification-body">
                    <form id="verification-form" method="post">
                        @csrf
                        <div class="form-group mb-2">
                            <input type="text" name="verification" class="form-control" id="verificationID" placeholder="Masukan Kode OTP anda" autocomplete="off">
                        </div>
                        <button type="button" id="submitVerification" class="btn btn-primary">Verify OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totp = new bootstrap.Modal(document.getElementById('modal-totp'));
        const verify = new bootstrap.Modal(document.getElementById('modal-verify'));
        $(document).ready(function () {
            $(document).keyup(function (e) { 
                if (e.which == 13){
                    if ($('#verificationID').val()) {
                        $('#submitVerification').click();
                    }else 
                    if ($('#email').val() && $('#password').val()) {
                        $('#login-submit').click();
                    }
                }
            });
            $('#login-submit').click(function() {
                $('#email').removeClass('is-invalid');
                $('#password').removeClass('is-invalid');
                if ($('#email').val() && $('#password').val()) {
                    $.ajax({
                        type: "POST",
                        url: "/login",
                        data: $('#login-form').serializeArray(),
                        success: function (response) {
                            let decResult = $.parseJSON(response);
                            $('#totp-body').html("<img src='"+decResult.uri+"'>");
                            totp.toggle();
                            // window.location = "/dashboard";
                        },
                        error: function (response) {
                            let decResult = $.parseJSON(response.responseText);
                            $('#email').addClass('is-invalid');
                            $('#password').addClass('is-invalid');
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Error',
                                text: decResult.message
                            });
                        }
                    });
                }else{
                    if (!$('#email').val()) {
                        $('#email').addClass('is-invalid');
                    }
                    if (!$('#password').val()) {
                        $('#password').addClass('is-invalid');
                    }
                }
            })

            $('#login-form').submit(function (e) { 
                e.preventDefault();
                $('#login-submit').click();
            });

            $('#close-totp').click(function () {
                totp.hide();
                verify.show();
            });

            $('#submitVerification').click(function() {
                $('#verificationID').removeClass('is-invalid');
                if ($('#verificationID').val()) {
                    $.ajax({
                        url: "/user/verifyOTP",
                        type: "POST",
                        data: $('#verification-form').serialize(),
                        async: true,
                        success: function(result) {
                            $('#modal-verify').hide();
                            if (result == 200) {
                                window.location = "/dashboard";
                            }else{
                                $('#verificationID').addClass('is-invalid');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Verifikasi Gagal',
                                    text: "Verifikasi anda gagal, harap periksa kembali kode anda"
                                });
                            }
                        }
                    })
                } else {
                    $('#verificationID').addClass('is-invalid');
                }
            });

            $('#verification-form').submit(function (e) { 
                e.preventDefault();
                $('#submitVerification').click();
            });
        })
    </script>
</body>
</html>