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
    <div class="container">
        <div class="row justify-content-center align-items-center g-2 mb-3">
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
                        accbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-success log-response" response=1><i class="fa-solid fa-file-circle-check"></i> Accept</button>`
                        rejbtn = `<button type="button" uuid=${value['uuid']} class="btn btn-danger log-response" response=2><i class="fa-solid fa-file-circle-xmark"></i> Reject</button>`
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
                                        <h4 class="card-title text-start">${value['title']}</h4>
                                        <h6 class="card-subtitle text-muted text-start"> created by : ${image} ${value['name']}</h6>
                                        <h6 class="card-subtitle text-muted text-start"> last updated : ${value['updated_at']}</h6>
                                    </div>
                                    <hr style="margin:0;">
                                    <div class="card-body">
                                        <p class="card-text text-start">${value['log']}</p>
                                    </div>
                                    <div class="card-footer">
                                        <a class="btn btn-primary me-1" href="/log/${value['uuid']}" role="button"><i class="fa-solid fa-eye"></i> View</a>
                                        ${accbtn}
                                        ${rejbtn}
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
</body>
</html>