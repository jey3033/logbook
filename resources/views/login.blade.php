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
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).keyup(function (e) { 
                if (e.which == 13){
                    if ($('#email').val() && $('#password').val()) {
                        $('#login-submit').click();
                    }
                }
            });
            $('#login-submit').click(function() {
                $.ajax({
                    type: "POST",
                    url: "/login",
                    data: $('#login-form').serializeArray(),
                    success: function (response) {
                        window.location = "/dashboard";
                    },
                    error: function (response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Error',
                            text: response.Message
                        });
                    }
                });
            })
        })
    </script>
</body>
</html>