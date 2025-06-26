$(function () {
    const t = $("#student-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/siswa",
            pages: 5,
            data: function (d) {
                d.class = $("#class-filter").val();
                d.homeroom_teacher = $(
                    "input[name='homeroom-teacher-filter']:checked"
                ).val();
                d.major = $("#major-filter").val();
                d.level = $("#level-filter").val();
                d.status = $("#status-filter").val();
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
                searchable: false,
            },
            {
                data: "NISN",
                name: "students.nisn",
                searchable: false,
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
                name: "teachers.name",
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
        lengthMenu: [5, 10, 50, 100],
        responsive: true,
        autoWidth: false,
        searchable: true,
        order: [[8, "desc"]],
    });

    t.on("draw", function () {
        updateDeleteButtonState(true);
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
            imageUrl: "../assets/images/gif/trash.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                const deleteBtn = $("#delete-selected");
                const originalHtml = deleteBtn.html();
                deleteBtn
                    .prop("disabled", true)
                    .html(
                        '<div class="d-flex align-items-center gap-2"><span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"> </span> Loading...</div>'
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
            imageUrl: "../assets/images/gif/trash.gif",
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
                        alert("Terjadi kesalahan saat menghapus siswa.");
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
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#addStudentForm#studentName")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#addStudentForm #studentName").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.nis) {
                        $("#addStudentForm #studentNis")
                            .next(".invalid-feedback")
                            .text(errors.nis[0]);
                        $("#addStudentForm #studentNis").addClass("is-invalid");
                    }
                    if (errors.nisn) {
                        $("#addStudentForm #studentNisn")
                            .next(".invalid-feedback")
                            .text(errors.nisn[0]);
                        $("#addStudentForm #studentNisn").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.major_id) {
                        $("#addStudentForm #studentMajor")
                            .next(".invalid-feedback")
                            .text(errors.major_id[0]);
                        $("#addStudentForm #studentMajor").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.class_id) {
                        $("#addStudentForm #studentClass")
                            .next(".invalid-feedback")
                            .text(errors.class_id[0]);
                        $("#addStudentForm #studentClass").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.homeroom_teacher_id) {
                        $("#addStudentForm #studentHomeroomTeacher")
                            .next(".invalid-feedback")
                            .text(errors.homeroom_teacher_id[0]);
                        $("#addStudentForm #studentHomeroomTeacher").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.date_of_birth) {
                        $("#addStudentForm #studentDateOfBirth")
                            .next(".invalid-feedback")
                            .text(errors.date_of_birth[0]);
                        $("#addStudentForm #studentDateOfBirth").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.birthplace) {
                        $("#addStudentForm #studentBirthplace")
                            .next(".invalid-feedback")
                            .text(errors.birthplace[0]);
                        $("#addStudentForm #studentBirthplace").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.gender) {
                        $("#addStudentForm #studentGender")
                            .next(".invalid-feedback")
                            .text(errors.gender[0]);
                        $("#addStudentForm #studentGender").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.religion) {
                        $("#addStudentForm #studentReligion")
                            .next(".invalid-feedback")
                            .text(errors.religion[0]);
                        $("#addStudentForm #studentReligion").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.admission_year) {
                        $("#addStudentForm #studentAdmissionYear")
                            .next(".invalid-feedback")
                            .text(errors.admission_year[0]);
                        $("#addStudentForm #studentAdmissionYear").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.status) {
                        $("#addStudentForm #studentStatus")
                            .next(".invalid-feedback")
                            .text(errors.status[0]);
                        $("#addStudentForm #studentStatus").addClass(
                            "is-invalid"
                        );
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

    $("#student-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateDeleteButtonState();

            // Hitung jumlah checkbox baris yang dicentang
            var totalCheckbox = $(
                "#student-table tbody input[type='checkbox'].select-row"
            ).length;
            var checkedCheckbox = $(
                "#student-table tbody input[type='checkbox'].select-row:checked"
            ).length;

            // Jika semua dicentang, centang juga #select-all
            $("#select-all").prop(
                "checked",
                totalCheckbox > 0 && totalCheckbox === checkedCheckbox
            );
        }
    );

    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#student-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateDeleteButtonState();
    });

    function updateDeleteButtonState(reload) {
        if (!reload) {
            var selectedRows = $(
                "#student-table tbody input[type='checkbox'].select-row:checked"
            ).length;
            $("#delete-selected").prop("disabled", selectedRows === 0);
            $("#delete-selected")
                .parent()
                .css("display", selectedRows > 0 ? "block" : "none");
        } else {
            $("#select-all").prop("checked", false);
            $("#delete-selected").prop("disabled", true);
            $("#delete-selected").parent().css("display", "none");
        }
    }

    // Event handler tombol edit
    $("#student-table").on("click", ".edit", function (e) {
        e.preventDefault();
        var id = $(this).data("id");

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
                    $("#editStudentForm #studentName").val(res.data.name);
                    $("#editStudentForm #studentNis").val(res.data.nis);
                    $("#editStudentForm #studentNisn").val(res.data.nisn);

                    let classOptions = '<option value="">Pilih Kelas</option>';
                    if (res.data.class?.major_id) {
                        $("#editStudentForm #studentMajor").val(
                            res.data.class.major_id
                        );
                        // class
                        classes
                            .filter(function (cls) {
                                return cls.major_id == res.data.class.major_id;
                            })
                            .forEach(function (cls) {
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

                    $("#editStudentForm #studentClass").html(classOptions);

                    if (res.data.homeroom_teacher_id) {
                        $(
                            `#editStudentForm input[name="homeroom_teacher_id"][value="${res.data.homeroom_teacher_id}"]`
                        ).prop("checked", true);

                        var selectedTeacherName = $(
                            `#editStudentForm input[name="homeroom_teacher_id"][value="${res.data.homeroom_teacher_id}"]`
                        )
                            .next("label")
                            .find("span")
                            .text();
                        $(
                            "#editStudentForm .select-box .selected-box span"
                        ).text(selectedTeacherName);
                    } else {
                        $(
                            "#editStudentForm .select-box .selected-box span"
                        ).text("Pilih Wali Kelas");
                    }

                    $("#editStudentForm #studentDateOfBirth").val(
                        res.data.date_of_birth
                    );
                    $("#editStudentForm #studentBirthplace").val(
                        res.data.birthplace
                    );
                    $("#editStudentForm #studentGender").val(res.data.gender);
                    $("#editStudentForm #studentReligion").val(
                        res.data.religion
                    );
                    $("#editStudentForm #studentAdmissionYear").val(
                        res.data.admission_year
                    );
                    $("#editStudentForm #studentStatus").val(res.data.status);
                    $("#editStudentForm").attr("data-id", id);
                    $("#editStudentModal").modal("show");
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
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    if (errors.name) {
                        $("#editStudentForm #studentName")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#editStudentForm #studentName").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.nis) {
                        $("#editStudentForm #studentNis")
                            .next(".invalid-feedback")
                            .text(errors.nis[0]);
                        $("#editStudentForm #studentNis").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.nisn) {
                        $("#editStudentForm #studentNisn")
                            .next(".invalid-feedback")
                            .text(errors.nisn[0]);
                        $("#editStudentForm #studentNisn").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.major_id) {
                        $("#editStudentForm #studentMajor")
                            .next(".invalid-feedback")
                            .text(errors.major_id[0]);
                        $("#editStudentForm #studentMajor").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.class_id) {
                        $("#editStudentForm #studentClass")
                            .next(".invalid-feedback")
                            .text(errors.class_id[0]);
                        $("#editStudentForm #studentClass").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.homeroom_teacher_id) {
                        $("#editStudentForm #studentHomeroomTeacher")
                            .next(".invalid-feedback")
                            .text(errors.homeroom_teacher_id[0]);
                        $("#editStudentForm #studentHomeroomTeacher").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.date_of_birth) {
                        $("#editStudentForm #studentDateOfBirth")
                            .next(".invalid-feedback")
                            .text(errors.date_of_birth[0]);
                        $("#editStudentForm #studentDateOfBirth").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.birthplace) {
                        $("#editStudentForm #studentBirthplace")
                            .next(".invalid-feedback")
                            .text(errors.birthplace[0]);
                        $("#editStudentForm #studentBirthplace").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.gender) {
                        $("#editStudentForm #studentGender")
                            .next(".invalid-feedback")
                            .text(errors.gender[0]);
                        $("#editStudentForm #studentGender").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.religion) {
                        $("#editStudentForm #studentReligion")
                            .next(".invalid-feedback")
                            .text(errors.religion[0]);
                        $("#editStudentForm #studentReligion").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.admission_year) {
                        $("#editStudentForm #studentAdmissionYear")
                            .next(".invalid-feedback")
                            .text(errors.admission_year[0]);
                        $("#editStudentForm #studentAdmissionYear").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.status) {
                        $("#editStudentForm #studentStatus")
                            .next(".invalid-feedback")
                            .text(errors.status[0]);
                        $("#editStudentForm #studentStatus").addClass(
                            "is-invalid"
                        );
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

$(document).on("change", "#studentMajor", function () {
    var majorId = $(this).val();
    var formId = $(this).closest("form").attr("id");

    if (majorId) {
        if (classes.length > 0) {
            let options = '<option value="">Pilih Kelas</option>';
            classes
                .filter(function (cls) {
                    return cls.major_id == majorId;
                })
                .forEach(function (cls) {
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
                $("#addStudentForm #studentClass").html(options);
            } else if (formId === "editStudentForm") {
                $("#editStudentForm #studentClass").html(options);
            }
        }
    } else {
        let options = '<option value="">Pilih Kelas</option>';
        classes.forEach(function (cls) {
            options +=
                "<option value='" +
                cls.id +
                "'>" +
                cls.name +
                " - " +
                cls.level +
                "</option>";
        });

        if (formId === "addStudentForm") {
            $("#addStudentForm #studentClass").html(options);
        } else if (formId === "editStudentForm") {
            $("#editStudentForm #studentClass").html(options);
        }
    }
});
