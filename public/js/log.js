$(document).ready(function () {
    var csrf;
    $.ajax({
        type: "GET",
        url: "/csrf",
        success: function (response) {
            csrf = JSON.parse(response);
            csrf = csrf.CSRF;
        },
    });

    // response log
    $(document).on("click", ".log-response", function (e) {
        e.preventDefault();
        let uuid = $(this).attr("uuid");
        let data = $(this).attr("response");

        $.ajax({
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrf },
            url: `/log/response/${uuid}`,
            data: { status: data },
            success: function (response) {
                location.reload();
            },
        });
    });

    // Animate Font-Awesome
    $(".nav-link").hover(
        function () {
            // over
            $(this).find(".fa-solid").addClass("fa-beat");
        },
        function () {
            // out
            $(this).find(".fa-solid").removeClass("fa-beat");
        }
    );

    $(".dropdown-item").hover(
        function () {
            // over
            $(this).find(".fa-solid").addClass("fa-beat");
        },
        function () {
            $(this).find(".fa-solid").removeClass("fa-beat");
        }
    );

    $("button").hover(
        function () {
            // over
            $(this).find(".fa-solid").addClass("fa-beat");
        },
        function () {
            // out
            $(this).find(".fa-solid").removeClass("fa-beat");
        }
    );

    // Initialization Select2
    $("#supervisor-select").select2({
        allowClear: true,
        placeholder: "Pilih Supervisor",
    });
});
