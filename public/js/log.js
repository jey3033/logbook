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
        var cont = 1;
        console.log();
        if (data == 2) {
            swal.fire({
                icon: "warning",
                title: "Are You Sure ?",
                text: "Are you really sure you want to reject this log ?",
                showCancelButton: true,
                confirmButtonText: "Yes, reject it!",
                cancelButtonText: "No, cancel!",
            }).then((result) => {
                if (result.isDismissed) {
                    cont = 2;
                }
                $("loader").addClass("d-flex");
                $("loader").removeClass("d-none");
            });
        }
        if (cont == 1) {
            $.ajax({
                type: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
                url: `/log/response/${uuid}`,
                data: { status: data },
                success: function (response) {
                    location.reload();
                },
            });
        }
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

    $(document).on("mouseenter mouseleave", ".dropdown-item", function (e) {
        $(this).find(".fa-solid").toggleClass("fa-beat");
    });

    $(document).on("mouseenter mouseleave", ".btn", function (e) {
        $(this).find(".fa-solid").toggleClass("fa-beat");
    });

    // Initialization Select2
    $("#supervisor-select").select2({
        allowClear: true,
        placeholder: "Pilih Supervisor",
    });

    $("#division-select").select2({
        allowClear: true,
        placeholder: "Pilih Divisi",
    });

    $(document).ajaxComplete(function (event, xhr, settings) {
        $("#loader").addClass("d-none");
        $("#loader").removeClass("d-flex");
    });
});
