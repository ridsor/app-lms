$(function () {
    // filter
    const params = getQueryParams();
    if (params.subject) {
        $("#subject-filter").val(params.subject);
    }
    if (params.teacher) {
        $("#teacher-filter").val(params.teacher);
    }
    if (params.room) {
        $("#room-filter").val(params.room);
    }
    if (params.day) {
        $("#day-filter").val(params.day);
    }

    const t = $("#schedule-by-class-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/jadwal/kelas/" + classId,
            pages: 5,
            data: function (d) {
                let filterParams = getQueryParams();
                $.extend(d, filterParams);
            },
        }),
        columns: [
            {
                data: "id",
                orderable: false,
                searchable: false,
                width: "50px",
                className: "text-center",
            },
            {
                data: null,
                name: "No",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center",
                width: "40px",
            },
            { data: "Mata Pelajaran", name: "subjects.name" },
            { data: "Guru", name: "teachers.name" },
            { data: "Ruangan", name: "rooms.name" },
            { data: "Hari", name: "day" },
            { data: "Jam", name: "start_time" },
            { data: "Aksi", name: "Aksi", orderable: false, searchable: false },
        ],
        language: {
            sProcessing: "Sedang memproses...",
            sZeroRecords: "Tidak ditemukan data yang sesuai",
            sInfo: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            sInfoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
            sEmptyTable: "Tidak ada data di tabel",
            sSearch: "Cari:",
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        order: [],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#schedule-action-buttons").css("display", "none");
    });

    // Submit tambah jadwal
    $("#addScheduleForm").on("submit", function (e) {
        e.preventDefault();
        $("#addScheduleForm").find("input, select").removeClass("is-invalid");
        $("#addScheduleForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addScheduleSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = $(this).serialize();
        $.ajax({
            url: "/jadwal",
            method: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    t.clearPipeline().draw();
                    $("#addScheduleModal").modal("hide");
                    $("#addScheduleForm")[0].reset();
                    $("#addScheduleForm .selectpicker").selectpicker("refresh");
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        if (
                            $("#addScheduleForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#addScheduleForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addScheduleForm [name='" + key + "']")
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
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            },
        });
    });

    $("#schedule-by-class-table").on("click", ".edit", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (!id) return;

        const editBtn = $(this);
        const originalHtml = editBtn.html();
        editBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
            );

        $.ajax({
            url: `/jadwal/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editScheduleForm [name='subject_id']").val(
                        res.data.subject_id
                    );
                    $("#editScheduleForm [name='teacher_id']").val(
                        res.data.teacher_id
                    );
                    $("#editScheduleForm [name='room_id']").val(
                        res.data.room_id
                    );
                    $("#editScheduleForm [name='day']").val(res.data.day);
                    $("#editScheduleForm [name='start_time']").val(
                        res.data.start_time
                    );
                    $("#editScheduleForm [name='end_time']").val(
                        res.data.end_time
                    );
                    $("#editScheduleForm [name='meeting_method']").val(
                        res.data.meeting_method
                    );

                    $("#editScheduleForm").attr("data-id", id);
                    $("#editScheduleModal").modal("show");
                    $("#editScheduleForm .selectpicker").selectpicker(
                        "refresh"
                    );
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
    });

    $("#editScheduleForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var formData = new FormData(this);

        $("#editScheduleForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editScheduleForm").find(".invalid-feedback").text("");

        const submitBtn = $("#editScheduleSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        $.ajax({
            url: "/jadwal/" + id,
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
                    $("#editScheduleModal").modal("hide");
                    $("#editScheduleForm")[0].reset();
                    $("#editScheduleForm .selectpicker").selectpicker(
                        "refresh"
                    );
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    for (const key in errors) {
                        if (
                            $("#editScheduleForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#editScheduleForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editScheduleForm [name='" + key + "']")
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
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            },
        });
    });

    $("#schedule-by-class-table").on("click", ".view", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (!id) return;

        const viewBtn = $(this);
        const originalHtml = viewBtn.html();
        viewBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
            );
        // Reset modal
        $("#viewScheduleModal .form-control-plaintext").text("");
        $("#viewScheduleModal").modal("show");
        // Show loading state
        $("#viewScheduleModal .modal-body")
            .addClass("position-relative")
            .append(
                '<div id="viewScheduleLoading" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:10;display:flex;align-items:center;justify-content:center;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );
        $.ajax({
            url: `/jadwal/${id}`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#viewScheduleSubject").text(res.data.subject);
                    $("#viewScheduleTeacher").text(res.data.teacher);
                    $("#viewScheduleRoom").text(res.data.room);
                    $("#viewScheduleMajor").text(
                        res.data.class && res.data.class.major
                            ? res.data.class.major.name
                            : "-"
                    );
                    $("#viewScheduleClass").text(
                        `${res.data.class.name} - ${res.data.class.level}`
                    );
                    $("#viewScheduleDay").text(res.data.day);
                    $("#viewScheduleStartTime").text(res.data.start_time);
                    $("#viewScheduleEndTime").text(res.data.end_time);
                    $("#viewScheduleMethod").text(res.data.meeting_method);
                }
            },
            error: function (xhr) {
                $("#viewScheduleModal").modal("hide");
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON?.message || "Gagal mengambil detail siswa"
                );
                toast.show();
            },
            complete: function () {
                $("#viewScheduleLoading").remove();
                viewBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });
});

$(document).ready(function () {
    flatpickr(".twenty-four-hour", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        allowInput: true,
    });
});
