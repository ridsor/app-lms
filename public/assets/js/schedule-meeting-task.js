var addTaskDescriptionQuill = new Quill("#addTaskDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#addTaskToolbar" },
    placeholder: "Tulis deskripsi",
});
var editTaskDescriptionQuill = new Quill("#editTaskDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#editTaskToolbar" },
    placeholder: "Tulis deskripsi",
});

$(document).ready(function () {
    $("[name='allow_late_submission']").on("change", function () {
        const value = $(this).is(":checked");

        const late_submission_time = $(this)
            .closest("form")
            .find("[name='late_submission_time']");
        const late_submission_time_form_input = $(this)
            .closest("form")
            .find(".lateSubmission");
        if (value) {
            late_submission_time.prop("disabled", false);
            late_submission_time_form_input.show();
        } else {
            late_submission_time.prop("disabled", true);
            late_submission_time_form_input.hide();
        }
    });

    $("#addTaskForm").on("submit", function (e) {
        e.preventDefault();
        const meeting_id = $(this).data("id");

        $("#addTaskForm").find("input, select").removeClass("is-invalid");
        $("#addTaskForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $(this)
            .find("input")
            .each(function () {
                const name = $(this).attr("name");
                const value =
                    $(this).is(":checkbox") || $(this).is(":radio")
                        ? $(this).is(":checked")
                            ? 1
                            : 0
                        : $(this).val();
                if (name && value) {
                    formData.append(name, value);
                }
            });

        $.ajax({
            url: `/jadwal/pertemuan/${meeting_id}/tugas`,
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
                        console.log($("#addTaskForm [name='" + key + "']"));
                        if (
                            $("#addTaskForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addTaskForm [name='" + key + "']").hasClass(
                                "quill"
                            ) ||
                            $("#addTaskForm [name='" + key + "']").hasClass(
                                "flatpicker"
                            )
                        ) {
                            $("#addTaskForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addTaskForm [name='" + key + "']")
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

    $("#editTaskForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editTaskForm").find("input, select").removeClass("is-invalid");
        $("#editTaskForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $(this)
            .find("input")
            .each(function () {
                const name = $(this).attr("name");
                const value =
                    $(this).is(":checkbox") || $(this).is(":radio")
                        ? $(this).is(":checked")
                            ? 1
                            : 0
                        : $(this).val();
                if (name) {
                    formData.append(name, value);
                }
            });

        $.ajax({
            url: `/jadwal/pertemuan/tugas/${id}`,
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
                            $("#editTaskForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#editTaskForm [name='" + key + "']").hasClass(
                                "quill"
                            ) ||
                            $("#addTaskForm [name='" + key + "']").hasClass(
                                "flatpicker"
                            )
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

    addTaskDescriptionQuill.on("text-change", function () {
        var value = addTaskDescriptionQuill.root.innerHTML;
        var descriptionText = addTaskDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#addTaskForm [name='description']").val(value);
    });
    editTaskDescriptionQuill.on("text-change", function () {
        var value = editTaskDescriptionQuill.root.innerHTML;
        var descriptionText = editTaskDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editTaskForm [name='description']").val(value);
    });
});

function handleEditTask(e, id) {
    e.preventDefault();

    const editBtn = $(e.currentTarget);
    const originalHtml = editBtn.html();
    editBtn
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/jadwal/pertemuan/tugas/${id}`,
        method: "GET",
        success: function (res) {
            if (res.success && res.data) {
                $("#editTaskForm").data("id", id);
                $("#editTaskForm [name='title']").val(res.data.title);
                editTaskDescriptionQuill.setContents(
                    editTaskDescriptionQuill.clipboard.convert(
                        res.data.description
                    )
                );
                $("#editTaskForm [name='type']").val(res.data.type);
                $("#editTaskForm [name='start_time']").val(res.data.start_time);
                $("#editTaskForm [name='end_time']").val(res.data.end_time);
                $("#editTaskForm [name='late_submission_time']").val(
                    res.data.late_submission_time
                );
                $("#editTaskForm [name='allow_late_submission']").prop(
                    "checked",
                    res.data.allow_late_submission
                );
                if (res.data.allow_late_submission) {
                    $("#editTaskForm .lateSubmission").show();
                }

                $("#editTaskModal").modal("show");

                $file_path = $("#editTaskForm [name='file_path']");
                if (res.data.file_path) {
                    $dropArea = $file_path.prev();
                    $dropArea.hide();
                    const $preview_file = $file_path.next();
                    const elemmentFile = getElementFile(
                        res.data.file_name,
                        (res.data.file_size / (1024 * 1024)).toFixed(2) + "mb",
                        getFileIcon(res.data.file_name)
                    );
                    $preview_file.html(elemmentFile);

                    $preview_file
                        .find(".btn-remove-file")
                        .on("click", function () {
                            $('#editTaskForm [name="deletedFile"]').val("1");

                            $(this).parent().parent().remove();
                            if (
                                $preview_file.find(".file-preview-item")
                                    .length === 0
                            ) {
                                $dropArea.show();
                            }
                        });
                }

                // Inisialisasi flatpickr untuk tanggal
                const late_submission_time = flatpickr(
                    "#editLateSubmissionTime",
                    {
                        defaultDate: res.data.late_submission_time
                            ? new Date(res.data.late_submission_time)
                            : null,
                        static: true,
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true,
                        locale: "id",
                        appendTo: document.getElementById("editTaskModal"),
                        minDate: "today",
                        locale: {
                            firstDayOfWeek: 1, // Mulai dari Senin
                            weekdays: {
                                shorthand: [
                                    "Min",
                                    "Sen",
                                    "Sel",
                                    "Rab",
                                    "Kam",
                                    "Jum",
                                    "Sab",
                                ],
                                longhand: [
                                    "Minggu",
                                    "Senin",
                                    "Selasa",
                                    "Rabu",
                                    "Kamis",
                                    "Jumat",
                                    "Sabtu",
                                ],
                            },
                            months: {
                                shorthand: [
                                    "Jan",
                                    "Feb",
                                    "Mar",
                                    "Apr",
                                    "Mei",
                                    "Jun",
                                    "Jul",
                                    "Agu",
                                    "Sep",
                                    "Okt",
                                    "Nov",
                                    "Des",
                                ],
                                longhand: [
                                    "Januari",
                                    "Februari",
                                    "Maret",
                                    "April",
                                    "Mei",
                                    "Juni",
                                    "Juli",
                                    "Agustus",
                                    "September",
                                    "Oktober",
                                    "November",
                                    "Desember",
                                ],
                            },
                        },
                    }
                );
                const end_time = flatpickr("#editTaskEndTime", {
                    defaultDate: new Date(res.data.end_time),
                    static: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    locale: "id",
                    appendTo: document.getElementById("editTaskModal"),
                    minDate: "today",
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            const selectedDate = selectedDates[0];
                            late_submission_time.set("minDate", selectedDate);
                            const selectedTime =
                                selectedDate.getHours() +
                                ":" +
                                selectedDate.getMinutes();
                            late_submission_time.set("minTime", selectedTime);
                        }
                    },
                    locale: {
                        firstDayOfWeek: 1, // Mulai dari Senin
                        weekdays: {
                            shorthand: [
                                "Min",
                                "Sen",
                                "Sel",
                                "Rab",
                                "Kam",
                                "Jum",
                                "Sab",
                            ],
                            longhand: [
                                "Minggu",
                                "Senin",
                                "Selasa",
                                "Rabu",
                                "Kamis",
                                "Jumat",
                                "Sabtu",
                            ],
                        },
                        months: {
                            shorthand: [
                                "Jan",
                                "Feb",
                                "Mar",
                                "Apr",
                                "Mei",
                                "Jun",
                                "Jul",
                                "Agu",
                                "Sep",
                                "Okt",
                                "Nov",
                                "Des",
                            ],
                            longhand: [
                                "Januari",
                                "Februari",
                                "Maret",
                                "April",
                                "Mei",
                                "Juni",
                                "Juli",
                                "Agustus",
                                "September",
                                "Oktober",
                                "November",
                                "Desember",
                            ],
                        },
                    },
                });
                const start_time = flatpickr("#editTaskStartTime", {
                    defaultDate: new Date(res.data.start_time),
                    static: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    locale: "id",
                    minDate: "today",
                    minTime: "00:00",
                    appendTo: document.getElementById("editTaskModal"),
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            const selectedDate = selectedDates[0];
                            end_time.set("minDate", selectedDate);
                            const selectedTime =
                                selectedDate.getHours() +
                                ":" +
                                selectedDate.getMinutes();
                            end_time.set("minTime", selectedTime);
                        }
                    },
                    locale: {
                        firstDayOfWeek: 1, // Mulai dari Senin
                        weekdays: {
                            shorthand: [
                                "Min",
                                "Sen",
                                "Sel",
                                "Rab",
                                "Kam",
                                "Jum",
                                "Sab",
                            ],
                            longhand: [
                                "Minggu",
                                "Senin",
                                "Selasa",
                                "Rabu",
                                "Kamis",
                                "Jumat",
                                "Sabtu",
                            ],
                        },
                        months: {
                            shorthand: [
                                "Jan",
                                "Feb",
                                "Mar",
                                "Apr",
                                "Mei",
                                "Jun",
                                "Jul",
                                "Agu",
                                "Sep",
                                "Okt",
                                "Nov",
                                "Des",
                            ],
                            longhand: [
                                "Januari",
                                "Februari",
                                "Maret",
                                "April",
                                "Mei",
                                "Juni",
                                "Juli",
                                "Agustus",
                                "September",
                                "Oktober",
                                "November",
                                "Desember",
                            ],
                        },
                    },
                });
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

function handleDeleteTask(e, id) {
    e.preventDefault();
    const deleteBtn = $(e.currentTarget);
    const originalHtml = deleteBtn.html();

    Swal.fire({
        title: "Hapus Tugas",
        text: "Apakah Anda yakin ingin menghapus tugas ini?",
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
                url: `/jadwal/pertemuan/tugas/${id}`,
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

$("#addTaskModal").on("shown.bs.modal", function () {
    const late_submission_time = flatpickr("#addLateSubmissionTime", {
        static: true,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "id",
        appendTo: document.getElementById("addTaskModal"),
        minDate: "today",
        locale: {
            firstDayOfWeek: 1, // Mulai dari Senin
            weekdays: {
                shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                longhand: [
                    "Minggu",
                    "Senin",
                    "Selasa",
                    "Rabu",
                    "Kamis",
                    "Jumat",
                    "Sabtu",
                ],
            },
            months: {
                shorthand: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "Mei",
                    "Jun",
                    "Jul",
                    "Agu",
                    "Sep",
                    "Okt",
                    "Nov",
                    "Des",
                ],
                longhand: [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember",
                ],
            },
        },
    });
    const end_time = flatpickr("#addTaskEndTime", {
        static: true,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "id",
        appendTo: document.getElementById("addTaskModal"),
        minDate: "today",
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const selectedDate = selectedDates[0];
                late_submission_time.set("minDate", selectedDate);
                const selectedTime =
                    selectedDate.getHours() + ":" + selectedDate.getMinutes();
                late_submission_time.set("minTime", selectedTime);
            }
        },
        locale: {
            firstDayOfWeek: 1, // Mulai dari Senin
            weekdays: {
                shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                longhand: [
                    "Minggu",
                    "Senin",
                    "Selasa",
                    "Rabu",
                    "Kamis",
                    "Jumat",
                    "Sabtu",
                ],
            },
            months: {
                shorthand: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "Mei",
                    "Jun",
                    "Jul",
                    "Agu",
                    "Sep",
                    "Okt",
                    "Nov",
                    "Des",
                ],
                longhand: [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember",
                ],
            },
        },
    });
    const start_time = flatpickr("#addTaskStartTime", {
        defaultDate: new Date(),
        static: true,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: "id",
        minDate: "today",
        minTime: "00:00",
        appendTo: document.getElementById("addTaskModal"),
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const selectedDate = selectedDates[0];
                end_time.set("minDate", selectedDate);
                const selectedTime =
                    selectedDate.getHours() + ":" + selectedDate.getMinutes();
                end_time.set("minTime", selectedTime);
            }
        },
        locale: {
            firstDayOfWeek: 1, // Mulai dari Senin
            weekdays: {
                shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                longhand: [
                    "Minggu",
                    "Senin",
                    "Selasa",
                    "Rabu",
                    "Kamis",
                    "Jumat",
                    "Sabtu",
                ],
            },
            months: {
                shorthand: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "Mei",
                    "Jun",
                    "Jul",
                    "Agu",
                    "Sep",
                    "Okt",
                    "Nov",
                    "Des",
                ],
                longhand: [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember",
                ],
            },
        },
    });
});
