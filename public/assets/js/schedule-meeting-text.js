const toolbarOptions = [
    ["bold", "italic", "underline", "strike"], // toggled buttons
    ["blockquote"],
    ["link"],
    [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
    [{ header: [1, 2, 3, 4, 5, 6, false] }],

    [{ color: [] }, { background: [] }],
    [{ align: [] }],

    ["clean"], // remove formatting button
];

var addMeetingTextQuill = new Quill("#addMeetingTextQuill", {
    theme: "snow",
    modules: { toolbar: toolbarOptions },
    placeholder: "Tulis untuk teks di pertemuan",
});
var editMeetingTextQuill = new Quill("#editMeetingTextQuill", {
    theme: "snow",
    modules: { toolbar: toolbarOptions },
    placeholder: "Tulis untuk teks di pertemuan",
});

$(document).ready(function () {
    $("#addMeetingTextForm").on("submit", function (e) {
        e.preventDefault();
        const meeting_id = $(this).data("id");

        $("#addMeetingTextForm")
            .find("input, select")
            .removeClass("is-invalid");
        $("#addMeetingTextForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: `/jadwal/pertemuan/${meeting_id}/text`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    location.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
                    for (const key in errors) {
                        console.log(
                            $("#addMeetingTextForm [name='" + key + "']")
                        );
                        if (
                            $(
                                "#addMeetingTextForm [name='" + key + "']"
                            ).hasClass("file_path") ||
                            $(
                                "#addMeetingTextForm [name='" + key + "']"
                            ).hasClass("quill")
                        ) {
                            $("#addMeetingTextForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addMeetingTextForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }
                    }
                } else {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                }
                submitBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });

    $("#editMeetingTextForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editMeetingTextForm")
            .find("input, select")
            .removeClass("is-invalid");
        $("#editMeetingTextForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: `/jadwal/pertemuan/text/${id}`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { "X-HTTP-Method-Override": "PUT" },
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    location.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
                    for (const key in errors) {
                        console.log(
                            $("#editMeetingTextForm [name='" + key + "']")
                        );
                        if (
                            $(
                                "#editMeetingTextForm [name='" + key + "']"
                            ).hasClass("file_path") ||
                            $(
                                "#editMeetingTextForm [name='" + key + "']"
                            ).hasClass("quill")
                        ) {
                            $("#editMeetingTextForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editMeetingTextForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }
                    }
                } else {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                }
                submitBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });

    addMeetingTextQuill.on("text-change", function () {
        var value = addMeetingTextQuill.root.innerHTML;
        var descriptionText = addMeetingTextQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#addMeetingTextForm [name='text']").val(value);
    });
    editMeetingTextQuill.on("text-change", function () {
        var value = editMeetingTextQuill.root.innerHTML;
        var descriptionText = editMeetingTextQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editMeetingTextForm [name='text']").val(value);
    });
});

function handleEditMeetingText(e, id) {
    e.preventDefault();

    const editBtn = $(e.currentTarget);
    const originalHtml = editBtn.html();
    editBtn
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/jadwal/pertemuan/text/${id}`,
        method: "GET",
        success: function (res) {
            if (res.success && res.data) {
                $("#editMeetingTextForm").data("id", id);
                editMeetingTextQuill.setContents(
                    editMeetingTextQuill.clipboard.convert(res.data.text)
                );
                $("#editMeetingTextModal").modal("show");
            }
        },
        error: function (xhr) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(xhr.responseJSON.message);
            toast.show();
        },
        complete: function () {
            editBtn.prop("disabled", false).html(originalHtml);
        },
    });
}

function handleDeleteMeetingText(e, id) {
    e.preventDefault();
    const deleteBtn = $(e.currentTarget);
    const originalHtml = deleteBtn.html();

    Swal.fire({
        title: "Hapus Teks Pertemuan",
        text: "Apakah Anda yakin ingin menghapus teks pertemuan ini?",
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: "Hapus",
        denyButtonText: `Batal`,
        confirmButtonColor: "#FC4438",
        imageUrl: "/assets/images/gif/trash.gif",
        imageWidth: 120,
        imageHeight: 120,
    }).then((result) => {
        if (result.isConfirmed) {
            deleteBtn
                .prop("disabled", true)
                .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

            $.ajax({
                url: `/jadwal/pertemuan/text/${id}`,
                method: "DELETE",
                success: function (response) {
                    if (response.success) {
                        const toast = new bootstrap.Toast($("#toast-success"));
                        $("#toast-success #toast-text").text(response.message);
                        toast.show();
                        location.reload();
                    }
                },
                error: function (xhr) {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                    deleteBtn.prop("disabled", false).html(originalHtml);
                },
            });
        }
    });
}
