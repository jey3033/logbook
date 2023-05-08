<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Global Setting</title>
    @include('include/head')
</head>
<body>
    @include('include/topbar')
    <div class="container-fluid">
        <div class="card text-center">
          <div class="card-header">
            Global Setting
          </div>
          <div class="card-body" id="setting-tab">
            <h4 class="card-title">Global Setting</h4>
            <form id="setting-form" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="seting-default-date-acceptance0" class="form-label">Name</label>
                  <input type="number" class="form-control" name="default-date-acceptance" id="division-default-date-acceptance" placeholder="batas waktu penerimaan"
                    value="{!! $setting->default_date_acceptance !!}"
                  >
                </div>
                <button type="button" class="btn btn-primary" id="setting-update"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            </form>
          </div>
        </div>
    </div>
    <script>
        $('#setting-update').click(function (e) { 
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: " /setting/store",
                data: $('#setting-form').serializeArray(),
                success: function (response) {
                    swal.fire({
                        icon: 'success',
                        title: 'Setting Saved'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
    @include('firebase')
</body>
</html>