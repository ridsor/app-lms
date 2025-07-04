$(function () {
    // filter
    const params = getQueryParams();
    if (params.major) {
        $("#major-filter").val(params.major);
    }
    if (params.class) {
        $("#class-filter").val(params.class);
    }
    if (params.level) {
        $("#level-filter").val(params.level);
    }
    if (params.status) {
        $("#status-filter").val(params.status);
    }
    if (params.homeroom_teacher) {
        $("#teacher-filter").val(params.homeroom_teacher);
    }
    $(".filter").selectpicker("refresh");

    const t = $("#student-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/siswa",
            pages: 5,
            data: function (d) {
                let filterParams = getQueryParams();
                $.extend(d, filterParams);
            },
        }),
        columns: [
            {
                data: "id",
                name: "students.id",
                orderable: false,
                searchable: false,
                width: "50px",
                className: "text-center",
            },
            {
                data: "Nama",
                name: "students.name",
            },
            {
                data: "NIS",
                name: "students.nis",
            },
            {
                data: "NISN",
                name: "students.nisn",
            },
            {
                data: "Jurusan",
                name: "majors.name",
                searchable: false,
            },
            {
                data: "Kelas",
                name: "classes.name",
                searchable: false,
            },
            {
                data: "Wali Kelas",
                name: "homeroom_teacher_name",
                searchable: false,
            },
            {
                data: "Status",
                name: "students.status",
                searchable: false,
            },
            {
                data: "Waktu",
                name: "students.created_at",
                searchable: false,
            },
            {
                data: "Aksi",
                name: "Aksi",
                orderable: false,
                searchable: false,
            },
        ],
        language: {
            sProcessing: "Sedang memproses...",
            sZeroRecords: "Tidak ditemukan data yang sesuai",
            sInfo: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            sInfoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
            sEmptyTable: "Tidak ada data di tabel",
            sInfoPostFix: "",
            sSearch: "Cari:",
            sUrl: "",
            select: {
                rows: {
                    _: "%d baris terpilih",
                    0: "",
                },
            },
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        searchDelay: 300,
        responsive: true,
        autoWidth: false,
        searchable: true,
        order: [[8, "desc"]],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#student-action-buttons").css("display", "none");
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#student-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;

        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Data siswa yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Hapus",
            denyButtonText: `Batal`,
            confirmButtonColor: "#FC4438",
            cancelButtonColor: "#16C7F9",
            imageUrl: "/assets/images/gif/trash.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                const deleteBtn = $("#delete-selected");
                const originalHtml = deleteBtn.html();
                deleteBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"> </span>'
                    );

                $.ajax({
                    url: "/siswa/hapus",
                    method: "DELETE",
                    data: {
                        ids: selectedIds,
                    },
                    success: function (res) {
                        t.clearPipeline().draw();
                        const toast = new bootstrap.Toast($("#toast-success"));
                        $("#toast-success #toast-text").text(res.message);
                        toast.show();
                    },
                    error: function (xhr) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON.message
                        );
                        toast.show();
                    },
                    complete: function () {
                        deleteBtn.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });

    // Event handler untuk filter
    $("#filter-btn").click(function (e) {
        e.preventDefault();

        // Buat query string
        const params = new URLSearchParams();
        if ($("#major-filter").val())
            params.append("major", $("#major-filter").val());
        if ($("#class-filter").val())
            params.append("class", $("#class-filter").val());
        if ($("#level-filter").val())
            params.append("level", $("#level-filter").val());
        if ($("#teacher-filter").val())
            params.append("homeroom_teacher", $("#teacher-filter").val());
        if ($("#status-filter").val())
            params.append("status", $("#status-filter").val());

        // Update URL tanpa reload
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);

        // Refresh datatable
        t.clearPipeline().draw();
    });

    $("#student-table").on("click", ".trash", function (e) {
        e.preventDefault();
        const trashBtn = $(this);
        var id = trashBtn.attr("data-id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data siswa yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Hapus",
            denyButtonText: `Batal`,
            confirmButtonColor: "#FC4438",
            cancelButtonColor: "#16C7F9",
            imageUrl: "/assets/images/gif/trash.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                const originalHtml = trashBtn.html();
                trashBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );
                $.ajax({
                    url: "/siswa/" + id,
                    method: "DELETE",
                    success: function (res) {
                        if (res.success) {
                            t.clearPipeline().draw();
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(res.message);
                            toast.show();
                        } else {
                            const toast = new bootstrap.Toast(
                                $("#toast-error")
                            );
                            $("#toast-error #toast-text").text(res.message);
                            toast.show();
                        }
                    },
                    error: function (xhr) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON.message
                        );
                        toast.show();
                    },
                    complete: function () {
                        trashBtn.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });

    $("#addStudentForm").on("submit", function (e) {
        e.preventDefault();

        $("#addStudentForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addStudentForm").find(".invalid-feedback").text("");

        const submitBtn = $("#addStudentSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        const formData = new FormData(this);

        $.ajax({
            url: "/siswa",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: async function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    t.clearPipeline().draw();
                    $("#addStudentModal").modal("hide");
                    $("#addStudentForm")[0].reset();
                    $("#addStudentForm .selectpicker").selectpicker("refresh");
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        if (
                            $("#addStudentForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#addStudentForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addStudentForm [name='" + key + "']")
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

    // Submit update siswa
    $("#editStudentForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var formData = new FormData(this);

        $("#editStudentForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editStudentForm").find(".invalid-feedback").text("");

        const submitBtn = $("#editStudentSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        $.ajax({
            url: "/siswa/" + id,
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
                    $("#editStudentModal").modal("hide");
                    $("#editStudentForm")[0].reset();
                    $("#editStudentForm .selectpicker").selectpicker("refresh");
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    for (const key in errors) {
                        if (
                            $("#editStudentForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#editStudentForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editStudentForm [name='" + key + "']")
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

    $("#bulkEditStudentForm").on("submit", function (e) {
        e.preventDefault();
        var ids = $(this).data("ids") || [];

        var data = {
            ids: ids,
            class_id: $("#bulkEditStudentForm select[name='class_id']").val(),
            status: $("#bulkEditStudentForm select[name='status']").val(),
            homeroom_teacher_id: $(
                "#bulkEditStudentForm select[name='homeroom_teacher_id']"
            ).val(),
        };

        var submitBtn = $("#bulkEditStudentSubmitBtn");
        var originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        $.ajax({
            url: "/siswa/edit",
            method: "PATCH",
            data: JSON.stringify(data),
            contentType: "application/json",
            success: function (res) {
                if (res.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(res.message);
                    toast.show();
                    $("#bulkEditStudentModal").modal("hide");
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    for (const key in errors) {
                        if (
                            $(
                                "#bulkEditStudentForm [name='" + key + "']"
                            ).hasClass("selectpicker")
                        ) {
                            $("#bulkEditStudentForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#bulkEditStudentForm [name='" + key + "']")
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
});

$(document).ready(function () {
    // Event handler tombol edit
    $("#student-table").on("click", ".edit", function (e) {
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
            url: `/siswa/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editStudentForm [name='name']").val(res.data.name);
                    $("#editStudentForm [name='nis']").val(res.data.nis);
                    $("#editStudentForm [name='nisn']").val(res.data.nisn);

                    let classOptions = '<option value="">Pilih Kelas</option>';
                    if (res.data.class?.major_id) {
                        $("#editStudentForm [name='major_id']").val(
                            res.data.class.major_id
                        );
                        // class
                        majors
                            .find(function (major) {
                                return major.id == res.data.class.major_id;
                            })
                            .classes.forEach(function (cls) {
                                classOptions +=
                                    "<option " +
                                    (cls.id === res.data.class_id
                                        ? "selected"
                                        : "") +
                                    ' value="' +
                                    cls.id +
                                    '">' +
                                    cls.name +
                                    " - " +
                                    cls.level +
                                    "</option>";
                            });
                    } else {
                        classes.forEach(function (cls) {
                            classOptions +=
                                "<option " +
                                (cls.id === res.data.class_id
                                    ? "selected"
                                    : "") +
                                ' value="' +
                                cls.id +
                                '">' +
                                cls.name +
                                " - " +
                                cls.level +
                                "</option>";
                        });
                    }

                    $("#editStudentForm [name='class_id']")
                        .html(classOptions)
                        .attr("disabled", false);
                    $("#editStudentForm [name='homeroom_teacher_id']").val(
                        res.data.homeroom_teacher_id
                    );

                    $("#editStudentForm [name='date_of_birth']").val(
                        res.data.date_of_birth
                    );
                    $("#editStudentForm [name='birthplace']").val(
                        res.data.birthplace
                    );
                    $("#editStudentForm [name='gender']").val(res.data.gender);
                    $("#editStudentForm [name='religion']").val(
                        res.data.religion
                    );
                    $("#editStudentForm [name='admission_year']").val(
                        res.data.admission_year
                    );
                    $("#editStudentForm [name='status']").val(res.data.status);

                    $("#editStudentForm").attr("data-id", id);
                    $("#editStudentModal").modal("show");

                    $("#editStudentForm .selectpicker").selectpicker("refresh");
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

    // Event handler tombol view
    $("#student-table").on("click", ".view", function (e) {
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
        $("#viewStudentModal .form-control-plaintext").text("");
        $("#viewStudentModal").modal("show");
        // Show loading state
        $("#viewStudentModal .modal-body")
            .addClass("position-relative")
            .append(
                '<div id="viewStudentLoading" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:10;display:flex;align-items:center;justify-content:center;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );
        $.ajax({
            url: `/siswa/${id}`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#viewStudentName").text(res.data.name || "-");
                    $("#viewStudentNis").text(res.data.nis || "-");
                    $("#viewStudentNisn").text(res.data.nisn || "-");
                    $("#viewStudentMajor").text(
                        res.data.class && res.data.class.major
                            ? res.data.class.major
                            : "-"
                    );
                    $("#viewStudentClass").text(
                        res.data.class
                            ? `${res.data.class.name} - ${res.data.class.level}`
                            : "-"
                    );
                    $("#viewStudentHomeroomTeacher").text(
                        res.data.homeroom_teacher
                            ? res.data.homeroom_teacher.name
                            : "-"
                    );
                    $("#viewStudentBirthplace").text(
                        res.data.birthplace || "-"
                    );
                    $("#viewStudentDateOfBirth").text(
                        res.data.date_of_birth || "-"
                    );
                    $("#viewStudentGender").text(
                        res.data.gender === "M"
                            ? "Laki-laki"
                            : res.data.gender === "F"
                            ? "Perempuan"
                            : "-"
                    );
                    $("#viewStudentReligion").text(res.data.religion || "-");
                    $("#viewStudentAdmissionYear").text(
                        res.data.admission_year || "-"
                    );
                    let statusLabel = "-";
                    switch (res.data.status) {
                        case "active":
                            statusLabel = "Aktif";
                            break;
                        case "transferred":
                            statusLabel = "Pindah";
                            break;
                        case "graduated":
                            statusLabel = "Lulus";
                            break;
                        case "dropout":
                            statusLabel = "Keluar";
                            break;
                    }
                    $("#viewStudentStatus").text(statusLabel);
                    $("#viewStudentCreatedAt").text(res.data.created_at || "-");
                }
            },
            error: function (xhr) {
                $("#viewStudentModal").modal("hide");
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON?.message || "Gagal mengambil detail siswa"
                );
                toast.show();
            },
            complete: function () {
                $("#viewStudentLoading").remove();
                viewBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });

    $(document).on("change", "select[name='major_id']", function () {
        var majorId = $(this).val();
        var formId = $(this).closest("form").attr("id");
        let options = '<option value="">Pilih Kelas</option>';

        if (majorId) {
            if (majors.length > 0) {
                majors
                    .find(function (major) {
                        return major.id == majorId;
                    })
                    .classes.forEach(function (cls) {
                        options +=
                            '<option  value="' +
                            cls.id +
                            '">' +
                            cls.name +
                            " - " +
                            cls.level +
                            "</option>";
                    });

                if (formId === "addStudentForm") {
                    $("#addStudentForm select[name='class_id']")
                        .html(options)
                        .attr("disabled", false);
                } else if (formId === "editStudentForm") {
                    $("#editStudentForm select[name='class_id']")
                        .html(options)
                        .attr("disabled", false);
                } else if (formId === "bulkEditStudentForm") {
                    $("#bulkEditStudentForm select[name='class_id']")
                        .html(options)
                        .attr("disabled", false);
                }
            }
        } else {
            if (formId === "addStudentForm") {
                $("#addStudentForm select[name='class_id']")
                    .html(options)
                    .attr("disabled", true);
            } else if (formId === "editStudentForm") {
                $("#editStudentForm select[name='class_id']")
                    .html(options)
                    .attr("disabled", true);
            } else if (formId === "bulkEditStudentForm") {
                $("#bulkEditStudentForm select[name='class_id']")
                    .html(options)
                    .attr("disabled", true);
            }
        }
        $(".selectpicker").selectpicker("refresh");
    });

    // Export akun siswa
    $("#export-student-account-form").on("submit", function (e) {
        e.preventDefault();

        const exportBtn = $("#export-student-account-btn");
        const originalHtml = exportBtn.html();

        // Validasi minimal satu filter dipilih
        const major = $(
            "#export-student-account-form select[name='major']"
        ).val();
        const classFilter = $(
            "#export-student-account-form select[name='class']"
        ).val();
        const level = $(
            "#export-student-account-form select[name='level']"
        ).val();

        if (!major && !classFilter && !level) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(
                "Pilih minimal satu filter untuk export data"
            );
            toast.show();
            return;
        }

        exportBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Exporting...'
            );

        // Submit form
        this.submit();

        // Reset tombol setelah 3 detik
        setTimeout(function () {
            exportBtn.prop("disabled", false).html(originalHtml);
        }, 3000);
    });

    // Export akun orang tua siswa
    $("#export-parent-account-form").on("submit", function (e) {
        e.preventDefault();

        const exportBtn = $("#export-parent-account-btn");
        const originalHtml = exportBtn.html();

        // Validasi minimal satu filter dipilih
        const major = $(
            "#export-parent-account-form select[name='major']"
        ).val();
        const classFilter = $(
            "#export-parent-account-form select[name='class']"
        ).val();
        const level = $(
            "#export-parent-account-form select[name='level']"
        ).val();

        if (!major && !classFilter && !level) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(
                "Pilih minimal satu filter untuk export data"
            );
            toast.show();
            return;
        }

        exportBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Exporting...'
            );

        // Submit form
        this.submit();

        // Reset tombol setelah 3 detik
        setTimeout(function () {
            exportBtn.prop("disabled", false).html(originalHtml);
        }, 3000);
    });

    // Bulk Edit
    $("#bulk-edit-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#student-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;
        // Reset form
        $("#bulkEditStudentForm")[0].reset();

        $("#bulkEditStudentModal").modal("show");
        $("#bulkEditStudentForm").data("ids", selectedIds);
    });

    $("#student-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateActionState();
        }
    );

    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#student-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateActionState();
    });

    function updateActionState() {
        var selectedRows = $(
            "#student-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#selected-count").text(selectedRows);
        $("#student-action-buttons").css(
            "display",
            selectedRows > 0 ? "flex" : "none"
        );

        var totalCheckbox = $(
            "#student-table tbody input[type='checkbox'].select-row"
        ).length;

        $("#select-all").prop("checked", totalCheckbox === selectedRows);
    }
});
