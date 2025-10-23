var addMaterialDescriptionQuill = new Quill("#addMaterialDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#addMaterialMaterialToolbar" },
    placeholder: "Tulis deskripsi",
});
var editMaterialDescriptionQuill = new Quill("#editMaterialDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#editMaterialMaterialToolbar" },
    placeholder: "Tulis deskripsi",
});

function handleChangeFileType(value, file) {
    file.show();
    file.parent().find(".materialLink").hide();

    if (value === "eBook") {
        file.find(".custom-file-upload").css({
            "pointer-events": "auto",
            opacity: 1,
        });
        file.find(".info").show();
        file.find("input").attr(
            "accept",
            ".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
        );
        file.find("input").attr("disabled", false);
        file.next(".materialLink").find("input").attr("disabled", true);
    } else if (value === "Archive") {
        file.find(".custom-file-upload").css({
            "pointer-events": "auto",
            opacity: 1,
        });
        file.find(".info").show();
        file.find("input").attr("accept", ".zip,.rar");
        file.find("input").attr("disabled", false);
        file.next(".materialLink").find("input").attr("disabled", true);
    } else if (value === "Link") {
        file.hide();
        file.parent().find(".materialLink").show();
        file.find("input").attr("disabled", true);
    } else {
        file.find(".custom-file-upload").css({
            "pointer-events": "none",
            opacity: 0.6,
        });
        file.find(".info").hide();
        file.find("input").removeAttr("accept");
        file.find("input").attr("disabled", true);
        file.next(".materialLink").find("input").attr("disabled", true);
    }
}

$(document).ready(function () {
    $(".material-form [name='file_type']").on("change", function () {
        const value = $(this).val();
        const $file = $(this).closest(".material-form").find(".materialFile");
        handleChangeFileType(value, $file);
    });

    $("#addMaterialForm").on("submit", function (e) {
        e.preventDefault();
        const meeting_id = $(this).data("id");

        $("#addMaterialForm").find("input, select").removeClass("is-invalid");
        $("#addMaterialForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: `/jadwal/pertemuan/${meeting_id}/materi`,
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
                    for (const key in errors) {
                        if (
                            $("#addMaterialForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addMaterialForm [name='" + key + "']").hasClass(
                                "quill"
                            )
                        ) {
                            $("#addMaterialForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addMaterialForm [name='" + key + "']")
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

    $("#editMaterialForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editMaterialForm").find("input, select").removeClass("is-invalid");
        $("#editMaterialForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: `/jadwal/pertemuan/materi/${id}`,
            method: "POST",
            headers: { "X-HTTP-Method-Override": "PUT" },
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
                    for (const key in errors) {
                        if (
                            $(
                                "#editMaterialForm [name='" + key + "']"
                            ).hasClass("file_path") ||
                            $(
                                "#editMaterialForm [name='" + key + "']"
                            ).hasClass("quill")
                        ) {
                            $("#editMaterialForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editMaterialForm [name='" + key + "']")
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

    addMaterialDescriptionQuill.on("text-change", function () {
        var value = addMaterialDescriptionQuill.root.innerHTML;
        var descriptionText = addMaterialDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#addMaterialForm [name='description']").val(value);
    });
    editMaterialDescriptionQuill.on("text-change", function () {
        var value = editMaterialDescriptionQuill.root.innerHTML;
        var descriptionText = editMaterialDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editMaterialForm [name='description']").val(value);
    });
});

function handleEditMaterial(e, id) {
    e.preventDefault();

    const editBtn = $(e.currentTarget);
    const originalHtml = editBtn.html();
    editBtn
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/jadwal/pertemuan/materi/${id}`,
        method: "GET",
        success: function (res) {
            if (res.success && res.data) {
                $("#editMaterialForm").data("id", id);
                $("#editMaterialForm [name='title']").val(res.data.title);
                editMaterialDescriptionQuill.setContents(
                    editMaterialDescriptionQuill.clipboard.convert(
                        res.data.description
                    )
                );
                $("#editMaterialForm [name='file_type']").val(
                    res.data.file_type
                );

                $file_path = $('#editMaterialForm [name="file_path"]');
                const $file = $file_path
                    .closest(".material-form")
                    .find(".materialFile");
                handleChangeFileType(res.data.file_type, $file);
                if (res.data.file_type === "Link") {
                    $("#editMaterialForm [name='material_link']").val(
                        res.data.file_path || ""
                    );
                } else {
                    $dropArea = $file_path.prev();
                    $dropArea.hide();
                    const $preview_file = $file_path.next();

                    const elementFile = getElementFile(
                        res.data.file_name,
                        (res.data.file_size / (1024 * 1024)).toFixed(2) + "mb",
                        getFileIcon(res.data.file_name)
                    );
                    $preview_file.html(elementFile);
                    $preview_file
                        .find(".btn-remove-file")
                        .on("click", function () {
                            $('#editMaterialForm [name="deletedFile"]').val(
                                "1"
                            );

                            $(this).parent().parent().remove();
                            if (
                                $preview_file.find(".file-preview-item")
                                    .length === 0
                            ) {
                                $dropArea.show();
                            }
                        });
                }

                $("#editMaterialModal").modal("show");
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

function handleDeleteMaterial(e, id) {
    e.preventDefault();
    const deleteBtn = $(e.currentTarget);
    const originalHtml = deleteBtn.html();

    Swal.fire({
        title: "Hapus Materi",
        text: "Apakah Anda yakin ingin menghapus materi ini?",
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
                url: `/jadwal/pertemuan/materi/${id}`,
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
