// Common avatar change JS

var loadFile = async function (event) {
    var image = document.getElementById("output");
    image.src = URL.createObjectURL(event.target.files[0]);

    const formData = new FormData();
    formData.append("image", event.target.files[0]);

    $.ajax({
        url: `/profil/${username}/image`,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: { "X-HTTP-Method-Override": "PATCH" },
        success: function (res) {
            if (res.success) {
                const toast = new bootstrap.Toast($("#toast-success"));
                $("#toast-success #toast-text").text(res.message);
                toast.show();
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                for (const key in errors) {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(errors[key][0]);
                    toast.show();
                }
            } else {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
            }
        },
    });
};

function handleChangePassword(e) {
    e.preventDefault();

    $(e.target.submit).prop("disabled", true);
    const originalHtml = $(e.target.submit).html();
    $(e.target.submit).html(
        'Loading <i class="fa-solid fa-arrows-rotate fa-spin"></i>'
    );

    const formData = new FormData(e.target);

    $("#change-password")
        .find("input, select, textarea")
        .removeClass("is-invalid");
    $("#change-password").find(".invalid-feedback").text("");

    $.ajax({
        url: "/password",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: { "X-HTTP-Method-Override": "PUT" },
        success: function (res) {
            if (res.success) {
                const toast = new bootstrap.Toast($("#toast-success"));
                $("#toast-success #toast-text").text(res.message);
                toast.show();
                $("#change-password")[0].reset();
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                for (const key in errors) {
                    $("#change-password [name='" + key + "']")
                        .parent()
                        .addClass("is-invalid")
                        .next(".invalid-feedback")
                        .text(errors[key][0]);
                }
            } else {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
            }
        },
        complete: function () {
            $(e.target.submit).prop("disabled", false);
            $(e.target.submit).html(originalHtml);
        },
    });
}

document.getElementById("cancelButton").addEventListener("click", function () {
    var image = document.getElementById("output");
    image.src = "/assets/svg/user-placeholder.svg"; // Reset to the placeholder image
    document.querySelector('input[type="file"]').value = ""; // Clear the file input

    const formData = new FormData();
    formData.append("image", "");

    $.ajax({
        url: `/profil/${username}/image`,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: { "X-HTTP-Method-Override": "PATCH" },
        success: function (res) {
            if (res.success) {
                const toast = new bootstrap.Toast($("#toast-success"));
                $("#toast-success #toast-text").text(res.message);
                toast.show();
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;

                for (const key in errors) {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(errors[key][0]);
                    toast.show();
                }
            } else {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
            }
        },
    });
});
