var addExamDescriptionQuill = new Quill("#addExamDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#addExamToolbar" },
    placeholder: "Tulis deskripsi",
});
var editExamDescriptionQuill = new Quill("#editExamDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#editExamToolbar" },
    placeholder: "Tulis deskripsi",
});

const formatTime = (date) => {
    const hours = date.getHours().toString().padStart(2, "0");
    const minutes = date.getMinutes().toString().padStart(2, "0");
    return `${hours}:${minutes}`;
};

const add_start_time = flatpickr("#addExamStartTime", {
    defaultDate: new Date(),
    minDate: "today",
    minTime: formatTime(new Date()),
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
    onChange: function (selectedDates) {
        if (selectedDates.length > 0) {
            const selectedDate = selectedDates[0];
            add_end_time.set({
                minDate: selectedDate,
                minTime: formatTime(selectedDate),
            });
            add_end_time.setDate(selectedDate);
        }
    },
});

const add_end_time = flatpickr("#addExamEndTime", {
    defaultDate: new Date(),
    minTime: formatTime(new Date()),
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
});

const edit_start_time = flatpickr("#editExamStartTime", {
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minTime: formatTime(new Date()),
    locale: flatpickrLocationID,
    onChange: function (selectedDates) {
        if (selectedDates.length > 0) {
            const selectedDate = selectedDates[0];
            edit_end_time.set({
                minDate: selectedDate,
                minTime: formatTime(selectedDate),
            });
            edit_end_time.setDate(selectedDate);
        }
    },
});

const edit_end_time = flatpickr("#editExamEndTime", {
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
});

$(document).ready(function () {
    $("#addExamForm").on("submit", function (e) {
        e.preventDefault();

        $("#addExamForm").find("input, select").removeClass("is-invalid");
        $("#addExamForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);

        $.ajax({
            url: `/ujian`,
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
                            $("#addExamForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addExamForm [name='" + key + "']").hasClass(
                                "quill"
                            ) ||
                            $("#addExamForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#addExamForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#addExamForm [name='" + key + "']").hasClass(
                                "radio"
                            )
                        ) {
                            $("#addExamForm [name='" + key + "']")
                                .parent()
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addExamForm [name='" + key + "']")
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

    $("#editExamForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editExamForm").find("input, select").removeClass("is-invalid");
        $("#editExamForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);

        $.ajax({
            url: `/ujian/${id}`,
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
                            $("#editExamForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#editExamForm [name='" + key + "']").hasClass(
                                "quill"
                            ) ||
                            $("#editExamForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#editExamForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#editExamForm [name='" + key + "']").hasClass(
                                "radio"
                            )
                        ) {
                            $("#editExamForm [name='" + key + "']")
                                .parent()
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editExamForm [name='" + key + "']")
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

    editExamDescriptionQuill.on("text-change", function () {
        var value = editExamDescriptionQuill.root.innerHTML;
        var descriptionText = editExamDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editExamForm [name='description']").val(value);
    });
    addExamDescriptionQuill.on("text-change", function () {
        var value = addExamDescriptionQuill.root.innerHTML;
        var descriptionText = addExamDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#addExamForm [name='description']").val(value);
    });
});

function handleEditExam(e, id) {
    e.preventDefault();

    const editBtn = $(e.currentTarget);
    const originalHtml = editBtn.html();
    editBtn
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/ujian/${id}/edit`,
        method: "GET",
        success: function (res) {
            if (res.success && res.data) {
                $("#editExamForm").data("id", id);

                $("#editExamForm [name='title']").val(res.data.title);
                $("#editExamForm [name='schedule_id']").val(
                    res.data.schedule_id
                );
                editExamDescriptionQuill.setContents(
                    editExamDescriptionQuill.clipboard.convert(
                        res.data.description
                    )
                );
                $("#editExamForm [name='type']").val(res.data.type);
                edit_start_time.setDate(new Date(res.data.start_time));
                edit_end_time.setDate(new Date(res.data.end_time));
                edit_end_time.set("minTime", new Date(res.data.start_time));
                edit_end_time.set("minDate", new Date(res.data.start_time));

                $(
                    `#editExamForm [name='exam_mode'][value='${res.data.exam_mode}']`
                ).prop("checked", true);
                $(
                    `#editExamForm [name='is_shuffle_questions'][value='${res.data.is_shuffle_questions}']`
                ).prop("checked", true);

                $("#editExamModal").modal("show");
                $("#editExamModal .selectpicker").selectpicker("refresh");
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

function handleDeleteExam(e, id) {
    e.preventDefault();
    const deleteBtn = $(e.currentTarget);
    const redirectWhenSuccess = deleteBtn.data("redirect");
    const originalHtml = deleteBtn.html();

    Swal.fire({
        title: "Hapus Ujian",
        text: "Apakah Anda yakin ingin menghapus ujian ini?",
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
                url: `/ujian/${id}`,
                method: "DELETE",
                success: function (response) {
                    if (response.success) {
                        const toast = new bootstrap.Toast($("#toast-success"));
                        $("#toast-success #toast-text").text(response.message);
                        toast.show();
                        if (redirectWhenSuccess) {
                            window.location.href = redirectWhenSuccess;
                        } else {
                            location.reload();
                        }
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
