// quill
var meetingDescriptionQuill = new Quill("#meetingDescriptionQuill", {
    theme: "snow",
    modules: { toolbar: "#toolbar9" },
    placeholder: "Masukan deskripsi pertemuan",
});

const subSubjectMatterQuill = new Quill("#subSubjectMatterQuill", {
    theme: "snow",
    modules: { toolbar: "#toolbarsubSubjectMatter" },
    placeholder: "Masukan sub pokok pembahasan",
});
const additionalNoteQuill = new Quill("#additionalNoteQuill", {
    theme: "snow",
    modules: { toolbar: "#toolbaradditionalNote" },
    placeholder: "Masukan catatan tambahan",
});

$(function () {
    meetingDescriptionQuill.root.innerHTML = defaultDescription;
    $("#meetingDescription").val(defaultDescription);
    subSubjectMatterQuill.root.innerHTML = defaultSubSubjectMatter;
    $("#subSubjectMatter").val(defaultSubSubjectMatter);
    additionalNoteQuill.root.innerHTML = defaultAdditionalNote;
    $("#additionalNote").val(defaultAdditionalNote);

    meetingDescriptionQuill.on("text-change", function () {
        var value = meetingDescriptionQuill.root.innerHTML;
        var descriptionText = meetingDescriptionQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editMeetingForm [name='description']").val(value);
    });

    subSubjectMatterQuill.on("text-change", function () {
        var value = subSubjectMatterQuill.root.innerHTML;
        var subSubjectMatterText = subSubjectMatterQuill.getText().trim();
        if (subSubjectMatterText === "" || subSubjectMatterText === "\n") {
            value = "";
        }
        $("#fillRealizationForm [name='sub_subject_matter']").val(value);
    });

    additionalNoteQuill.on("text-change", function () {
        var value = additionalNoteQuill.root.innerHTML;
        var additionalNoteText = additionalNoteQuill.getText().trim();
        if (additionalNoteText === "" || additionalNoteText === "\n") {
            value = "";
        }
        $("#fillRealizationForm [name='additional_note']").val(value);
    });
});

$(document).ready(function () {
    statuses.forEach(function (value) {
        $(`.status-all-${value}`).on("click", function () {
            $(`.status-input`).each(function () {
                $(this)
                    .find(`.status-value[value="${value}"]`)
                    .prop("checked", true);
            });
        });
    });

    function checkStatusAll() {
        let statuses = [];
        $("#fill_attendance .status-value:checked").each(function () {
            statuses.push($(this).val());
        });
        let statusIsSame =
            statuses.length > 0 &&
            statuses.every(function (s) {
                return s === statuses[0];
            });
        if (statusIsSame) {
            $(`[name='status-all'][value='${statuses[0]}']`).prop(
                "checked",
                true
            );
        } else {
            $(`[name='status-all']:checked`).prop("checked", false);
        }
    }

    checkStatusAll();

    $("#fill_attendance .status-value").on("change", function (e) {
        checkStatusAll();
    });

    $("#editMeetingForm").on("submit", function (e) {
        e.preventDefault();
        const meeting_id = $(this).data("id");
        const code = $(this).data("code");

        $("#editMeetingForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editMeetingForm").find(".invalid-feedback").text("");

        const submitBtn = $(this).find("#submit");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                'Loading... <i class="fa-solid fa-arrows-rotate fa-spin"></i>'
            );

        const formData = new FormData(this);

        $.ajax({
            url: `/jadwal/${code}/pertemuan/${meeting_id}`,
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

                    for (const key in errors) {
                        if (
                            $("#editMeetingForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            ) ||
                            $("#editMeetingForm [name='" + key + "']").hasClass(
                                "quill"
                            )
                        ) {
                            $("#editMeetingForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editMeetingForm [name='" + key + "']")
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

    $("#fillRealizationForm").on("submit", function (e) {
        e.preventDefault();
        const meeting_id = $(this).data("id");

        $("#fillRealizationForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#fillRealizationForm").find(".invalid-feedback").text("");

        const submitBtn = $(this).find("#submit");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                'Loading... <i class="fa-solid fa-arrows-rotate fa-spin"></i>'
            );

        const formData = new FormData(this);

        $.ajax({
            url: `/jadwal/pertemuan/${meeting_id}/jurnal`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    $("#fillRealizationModal").modal("hide");
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    for (const key in errors) {
                        if (
                            $(
                                "#fillRealizationForm [name='" + key + "']"
                            ).hasClass("selectpicker") ||
                            $(
                                "#fillRealizationForm [name='" + key + "']"
                            ).hasClass("quill")
                        ) {
                            $("#fillRealizationForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#fillRealizationForm [name='" + key + "']")
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

    $("#save_attendance").on("click", function (e) {
        const button = $(this);
        const originalHtml = button.html();
        button
            .prop("disabled", true)
            .html(
                'Loading... <i class="fa-solid fa-arrows-rotate fa-spin"></i>'
            );

        const meeting_id = $(this).data("meeting-id");
        const attendances = [];
        $("#fill_attendance .status-input").each(function () {
            if ($(this).data("user-id")) {
                const status =
                    $(this).find(".status-value:checked").val() || null;
                attendances.push({
                    user_id: $(this).data("user-id"),
                    status: status,
                });
            }
        });

        const data = {
            attendances,
        };

        $.ajax({
            url: `/jadwal/pertemuan/${meeting_id}/kehadiran`,
            method: "PATCH",
            data: JSON.stringify(data),
            processData: false,
            contentType: false,
            contentType: "application/json",
            success: function (res) {
                if (res.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(res.message);
                    toast.show();
                    $("#fillAttendanceModal").modal("hide");
                    $("#fillRealizationModal").modal("show");
                }
            },
            error: function (xhr) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON?.message || "Gagal menyimpan kehadiran"
                );
                toast.show();
            },
            complete: function () {
                button.prop("disabled", false).html(originalHtml);
            },
        });
    });

    $("#start_learning").on("click", function () {
        const title = $(this).data("title");

        Swal.fire({
            title: "Apakah anda yakin memulai pertemuan?",
            text: `${title}`,
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Batal`,
            confirmButtonText: "Ya",
            confirmButtonColor: "#16C7F9",
            cancelButtonColor: "#FC4438",
            imageUrl: "/assets/images/gif/dashboard-8/successful.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $(this);
                const originalHtml = button.html();
                button
                    .prop("disabled", true)
                    .html(
                        'Loading... <i class="fa-solid fa-arrows-rotate fa-spin"></i>'
                    );

                const meeting_id = $(this).data("meeting-id");

                $.ajax({
                    url: `/jadwal/pertemuan/${meeting_id}/mulai-belajar`,
                    method: "PATCH",
                    success: function (res) {
                        if (res.success) {
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(res.message);
                            toast.show();

                            $("#fillRealizationModal #date").text(
                                res.data.date
                            );
                            $("#fillRealizationModal #start_time").text(
                                res.data.formatted_started_at
                            );
                            $("#btn_fill_realization").attr("disabled", false);

                            $(
                                `#meeting-sidebar [data-meeting-id='${meeting_id}']`
                            )
                                .find("#status")
                                .text(res.data.status);

                            button.prop("disabled", true).html(originalHtml);

                            $("#fillRealizationModal").modal("show");
                        }
                    },
                    error: function (xhr) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON?.message ||
                                "Gagal menyimpan kehadiran"
                        );
                        toast.show();
                        button.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });
});
