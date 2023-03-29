$(document).ready(function () {
    var csrf;
    $.ajax({
        type: "GET",
        url: "/csrf",
        success: function (response) {
            csrf = JSON.parse(response);
            csrf = csrf.CSRF; 
        }
    });
    
    // response log
    $(document).on('click','.log-response', function (e) { 
        e.preventDefault();
        let uuid = $(this).attr('uuid');
        let data = $(this).attr('response');

        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': csrf},
            url: `/log/response/${uuid}`,
            data: {'status' : data},
            success: function (response) {
                location.reload();
            }
        });
    });
});