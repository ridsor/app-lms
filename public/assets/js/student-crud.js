$(function () {
    var t = $("#student-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/siswa",
            data: function (d) {
                d.class = $("#class-filter").val();
                d.homeroom_teacher = $(
                    "input[name='homeroom-teacher-filter']:checked"
                ).val();
                d.major = $("#major-filter").val();
                d.level = $("#level-filter").val();
                d.status = $("#status-filter").val();
            },
            complete: function () {
                updateDeleteButtonState();
            },
        },
        columns: [
            {
                data: "id",
                name: "id",
                orderable: false,
                searchable: false,
                width: "50px",
                className: "text-center",
            },
            {
                data: "Nama",
                name: "name",
            },
            {
                data: "NIS",
                name: "nis",
            },
            {
                data: "NISN",
                name: "nisn",
            },
            {
                data: "Jurusan",
                name: "major_name",
            },
            {
                data: "Kelas",
                name: "class_name",
            },
            {
                data: "Wali Kelas",
                name: "homeroom_teacher_name",
            },
            {
                data: "Status",
                name: "status",
            },
            {
                data: "Waktu",
                name: "created_at",
            },
            {
                data: "Aksi",
                name: "Aksi",
                orderable: false,
                searchable: false,
            },
            {
                data: "created_at",
                name: "created_at",
                visible: false,
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
        autoWidth: true,
        searchable: true,
        order: [[8, "desc"]],
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
                $.ajax({
                    url: "/siswa/hapus",
                    method: "DELETE",
                    data: {
                        ids: selectedIds,
                    },
                    success: function (res) {
                        t.ajax.reload();
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
                });
            }
        });
    });

    // Event handler untuk filter
    $("#filter-btn").click(function (e) {
        e.preventDefault();
        t.draw();
    });

    $("#student-table").on("click", ".trash", function (e) {
        e.preventDefault();
        var row = $(this).closest("tr");
        var id = row.attr("id");
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
                $.ajax({
                    url: "/siswa/" + id,
                    method: "DELETE",
                    success: function (res) {
                        if (res.success) {
                            t.ajax.reload();
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
                    t.draw();
                    $("#addStudentModal").modal("hide");
                    $("#addStudentForm")[0].reset();
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
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

    function updateDeleteButtonState() {
        var selectedRows = $(
            "#student-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#delete-selected").prop("disabled", selectedRows === 0);
        $("#delete-selected")
            .parent()
            .css("display", selectedRows > 0 ? "block" : "none");
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

                    var classOptions = '<option value="">Pilih Kelas</option>';
                    if (res.data.class.major) {
                        $("#editStudentForm #studentMajor").val(
                            res.data.class.major.id
                        );
                        // class
                        res.data.class.major.classes.forEach(function (cls) {
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

                    console.log(res.data.homeroom_teacher_id);
                    // Set radio button untuk wali kelas berdasarkan ID
                    $(
                        `#editStudentForm input[name="homeroom_teacher_id"][value="${res.data.homeroom_teacher_id}"]`
                    ).prop("checked", true);

                    // Update teks yang ditampilkan di selected-box
                    var selectedTeacherName = $(
                        `#editStudentForm input[name="homeroom_teacher_id"][value="${res.data.homeroom_teacher_id}"]`
                    )
                        .next("label")
                        .find("span")
                        .text();
                    $("#editStudentForm .select-box .selected-box span").text(
                        selectedTeacherName
                    );
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
        console.log(formData.get("homeroom_teacher_id"));
        // return;

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
                    $("#student-table").DataTable().ajax.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);

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

// Custom add search option
$(document).on("click", ".select-box", function () {
    var $selected = $(this).find(".selected-box");
    var $optionsContainer = $(this).find(".options-container");
    var $searchBox = $optionsContainer.find(".search-box input");
    var $optionsList = $optionsContainer.find(".selection-option");

    $optionsContainer.toggleClass("active");

    $searchBox.val("");
    filterList("");

    if ($optionsContainer.hasClass("active")) {
        $searchBox.focus();
    }

    $optionsList.on("click", function () {
        $selected.html($(this).find("label").html());
        $optionsContainer.removeClass("active");
    });

    $searchBox.on("keyup", function (e) {
        filterList(e.target.value);
    });

    function filterList(searchTerm) {
        searchTerm = searchTerm.toLowerCase();
        $optionsList.each(function () {
            var $label = $(this).find("label");
            var labelText = $label.text().toLowerCase();
            if (labelText.indexOf(searchTerm) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});

$(document).on("change", "#studentMajor", function () {
    var majorId = $(this).val();
    var formId = $(this).closest("form").attr("id");

    if (majorId) {
        if (classes.length > 0) {
            var options = '<option value="">Pilih Kelas</option>';
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
    }
});
