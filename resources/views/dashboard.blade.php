<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome {!! $username !!}</title>

    @include('include/head')
</head>
<body>
    @include('include/topbar')
    @include('include/loader')
    <div class="container">
        <div class="row justify-content-center align-items-center g-2 pt-1 mt-1 mb-3">
            {!! $quote !!}
        </div>
        <div class="card text-center">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
              <li class="nav-item">
                <a class="nav-link active" aria-current="true" href="#outstanding-log" id="outstanding-log-button">Outstanding</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#personal-log" id="personal-log-button">Your Log</a>
              </li>
            </ul>
          </div>
          <div class="card-body" id="outstanding-log">
            <h4 class="card-title">Your Outstanding Log</h4>
            <div id="ol-content"></div>
          </div>
          <div class="card-body" id="personal-log">
            <h4 class="card-title">Your Personal Log</h4>
            <div id="pl-content"></div>
          </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $('#personal-log').hide();
            showOutstanding();
            $('#personal-log-button').click(function (e) { 
                e.preventDefault();
                $('#personal-log').show();
                $('#outstanding-log').hide();

                if (!$('#personal-log-button').hasClass('active')) $('#personal-log-button').addClass('active');
                if ($('#outstanding-log-button').hasClass('active')) $('#outstanding-log-button').removeClass('active');
                showPersonal();
            });

            $('#outstanding-log-button').click(function (e) { 
                e.preventDefault();
                $('#personal-log').hide();
                $('#outstanding-log').show();

                if ($('#personal-log-button').hasClass('active')) $('#personal-log-button').removeClass('active');
                if (!$('#outstanding-log-button').hasClass('active')) $('#outstanding-log-button').addClass('active');
                showOutstanding();

            });

            function createGridLog(response, parent) {
                rowid = 0;
                let resp_data = JSON.parse(response)
                $.each(resp_data.Data, function (index, value) {
                    // generate rows 
                    if (index % 3 == 0) {
                        rowid++
                        $(`#${parent}`).append(`<div class="row" id="${parent}-row-${rowid}">`)
                    }

                    //generate accept button
                    var accbtn = ""
                    var rejbtn = ""
                    if (value['name'] != `{!! $username !!}`) {
                        let nextStatus = value['status']+1;
                        if (value['status'] == 1) {
                            nextStatus = nextStatus+1;
                        }
                        accbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-success log-response float-end" response=${nextStatus}><i class="fa-solid fa-file-circle-check"></i> Terima</button>`
                        rejbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-danger log-response float-end mt-1" response=2><i class="fa-solid fa-file-circle-xmark"></i> Tolak</button>`
                        if (nextStatus == 3) {
                            accbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-success log-response float-end" disabled response=${nextStatus}><i class="fa-solid fa-file-circle-check"></i> Terima</button>`
                        }else if (nextStatus == 4) {
                            accbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-success log-response float-end" response=${nextStatus}><i class="fa-solid fa-file-circle-check"></i> Selesai</button>`
                            rejbtn = '' 
                        }
                        
                    }

                    // generate badge
                    var badge = "";
                    if (value['status'] == 0) {
                        badge = `<span class="badge rounded-pill bg-warning align-self-end w-25 position-relative log-badge">Pending</span>`
                    }else if ($.inArray(value['status'], [1, 4]) >= 0) {
                        badge = `<span class="badge rounded-pill bg-primary align-self-end w-25 position-relative log-badge">Proses</span>`
                    }else if (value['status'] == 2) {
                        badge = `<span class="badge rounded-pill bg-danger align-self-end w-25 position-relative log-badge">Ditolak</span>`
                    }else if (value['status'] == 5) {
                        badge = `<span class="badge rounded-pill bg-success align-self-end w-25 position-relative log-badge">Selesai</span>`                        
                    }else if (value['status'] == 3) {
                        let cur = new Date()
                        let duedate = new Date(value['due_date'])
                        let diffTime = Math.abs(duedate - cur);
                        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 

                        // console.log(diffDays);
                        if (diffDays > 0) {
                            badge = `<span class="badge rounded-pill bg-warning align-self-end w-25 position-relative log-badge">${diffDays} Hari</span>`    
                        } else if(diffDays == 0) {
                            badge = `<span class="badge rounded-pill bg-danger align-self-end w-25 position-relative log-badge">target Hari ini</span>`
                        } else {
                            diffDays = diffDays * -1;
                            badge = `<span class="badge rounded-pill bg-danger align-self-end w-25 position-relative log-badge">terlewat ${diffDays} Hari</span>`
                        }
                        
                    }

                    //Detail Status Generation
                    let status = '';
                    let statusinfo = "";
                    if (value['status'] == 0) {
                        status = 'Menunggu approval head departemen';
                    } else if (value['status'] == 1) {
                        status = 'Menunggu approval head departemen tujuan';
                        statusinfo = `<span class='float-start text-start w-px-200'>harap melihat detail pengerjaan sebelum melanjutkan request</span>`;
                    } else if (value['status'] == 2) {
                        status = 'Ditolak';
                    } else if (value['status'] == 3) {
                        status = 'Dalam pengerjaan departemen tujuan';
                    } else if (value['status'] == 4) {
                        status = 'Hasil dalam review head departemen';
                    } else if (value['status'] == 5) {
                        status = 'Selesai';
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
                                        <h4 class="card-title text-start">${value['title']}</h4>
                                        <h6 class="card-subtitle text-muted text-start"> created by : ${image} ${value['name']}</h6>
                                        <h6 class="card-subtitle text-muted text-start"> status : ${status}</h6>
                                        <h6 class="card-subtitle text-muted text-start"> last updated : ${value['updated_at']}</h6>
                                    </div>
                                    <hr style="margin:0;">
                                    <div class="card-body">
                                        <p class="card-text text-start">${value['shortenlog']}</p>
                                    </div>
                                    <div class="card-footer d-grid">
                                        <div>
                                            <a class="btn btn-primary float-start" href="/log/${value['uuid']}" role="button"><i class="fa-solid fa-eye"></i> View</a>
                                            ${accbtn}
                                        </div>
                                        <div class=''>
                                            ${statusinfo}
                                            ${rejbtn}
                                        </div>
                                    </div>
                                </div>`;
                    $(`#${parent}-row-${rowid}`).append(html)
                });
            }

            function showOutstanding() {
                $(`#ol-content`).empty();
                $.ajax({
                    type: "GET",
                    url: "/get-outstanding-log",
                    statusCode: {
                        204: function (response) {
                            $('#ol-content').append("No Outstanding Data, Good Job");
                        },
                        200: function (response) {
                            createGridLog(response, 'ol-content');
                        }
                    }
                });
            }

            function showPersonal() {
                $(`#pl-content`).empty();
                $.ajax({
                    type: "GET",
                    url: "/get-personal-log",
                    statusCode: {
                        204: function (response) {
                            $('#pl-content').append("No Data to show");
                        }, 
                        200: function (response) {
                            createGridLog(response, 'pl-content');
                        }
                    }
                });
            }
        })
    </script>
    @include('include/firebase')
</body>
</html>