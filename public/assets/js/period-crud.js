$(function () {
    var t = $("#period-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/periode",
            pages: 5,
            data: function (d) {
                d.semester = $("#semester-filter").val();
                d.start_date = $("#start-date-filter").val();
                d.end_date = $("#end-date-filter").val();
            },
        }),
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
                data: "Semester",
                name: "semester",
                searchable: false,
            },
            {
                data: "Tahun Akademik",
                name: "academic_year",
                searchable: false,
            },
            {
                data: "Periode",
                name: "periode",
                orderable: false,
                searchable: false,
            },
            {
                data: "Status",
                name: "status",
                orderable: false,
                searchable: false,
            },
            {
                data: "Waktu",
                name: "created_at",
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
        fixedColumns: {
            leftColumns: 2,
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [5, 10, 50, 100],
        responsive: true,
        autoWidth: false,
        searchable: true,
        searching: false,
        order: [[5, "desc"]],
    });

    t.on("draw", function () {
        updateDeleteButtonState(true);
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $('#period-table tbody input[type="checkbox"].select-row:checked').each(
            function () {
                selectedIds.push($(this).val());
            }
        );
        if (selectedIds.length === 0) return;

        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Data periode yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/periode/hapus",
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

    // Hapus satuan
    $("#period-table").on("click", ".trash", function (e) {
        e.preventDefault();
        const trashBtn = $(this);
        var id = trashBtn.attr("data-id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data periode yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Hapus",
            denyButtonText: `Batal`,
            confirmButtonColor: "#FC4438",
            cancelButtonColor: "#16C7F9",
            imageUrl: "../assets/images/gif/trash.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then(async (result) => {
            if (result.isConfirmed) {
                const originalHtml = trashBtn.html();
                trashBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );
                await $.ajax({
                    url: "/periode/" + id,
                    method: "DELETE",
                    success: async function (res) {
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

    // Tambah periode
    $("#addPeriodForm").on("submit", function (e) {
        e.preventDefault();
        $("#addPeriodForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addPeriodForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addPeriodSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: "/periode",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    t.clearPipeline().draw();
                    $("#addPeriodModal").modal("hide");
                    $("#addPeriodForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.semester) {
                        $("#addSemester")
                            .next(".invalid-feedback")
                            .text(errors.semester[0]);
                        $("#addSemester").addClass("is-invalid");
                    }
                    if (errors.academic_year) {
                        $("#addAcademicYear")
                            .next(".invalid-feedback")
                            .text(errors.academic_year[0]);
                        $("#addAcademicYear").addClass("is-invalid");
                    }
                    if (errors.start_date) {
                        $("#addStartDate")
                            .next(".invalid-feedback")
                            .text(errors.start_date[0]);
                        $("#addStartDate").addClass("is-invalid");
                    }
                    if (errors.end_date) {
                        $("#addEndDate")
                            .next(".invalid-feedback")
                            .text(errors.end_date[0]);
                        $("#addEndDate").addClass("is-invalid");
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

    // Edit periode (show modal dan isi data)
    $("#period-table").on("click", ".edit", function (e) {
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
            url: "/periode/" + id,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editPeriodForm #editPeriodId").val(res.data.id);
                    $("#editPeriodForm #editSemester").val(res.data.semester);
                    $("#editPeriodForm #editAcademicYear").val(
                        res.data.academic_year
                    );
                    $("#editPeriodForm #editStartDate").val(
                        res.data.start_date
                    );
                    $("#editPeriodForm #editEndDate").val(res.data.end_date);
                    $("#editPeriodModal").modal("show");
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

    // Submit update periode
    $("#editPeriodForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).find("#editPeriodId").val();
        var formData = new FormData(this);
        $("#editPeriodForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editPeriodForm").find(".invalid-feedback").text("");
        const submitBtn = $("#editPeriodSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        $.ajax({
            url: "/periode/" + id,
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
                    $("#editPeriodModal").modal("hide");
                    $("#editPeriodForm")[0].reset();
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.semester) {
                        $("#editPeriodForm #editSemester")
                            .next(".invalid-feedback")
                            .text(errors.semester[0]);
                        $("#editPeriodForm #editSemester").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.academic_year) {
                        $("#editPeriodForm #editAcademicYear")
                            .next(".invalid-feedback")
                            .text(errors.academic_year[0]);
                        $("#editPeriodForm #editAcademicYear").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.start_date) {
                        $("#editPeriodForm #editStartDate")
                            .next(".invalid-feedback")
                            .text(errors.start_date[0]);
                        $("#editPeriodForm #editStartDate").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.end_date) {
                        $("#editPeriodForm #editEndDate")
                            .next(".invalid-feedback")
                            .text(errors.end_date[0]);
                        $("#editPeriodForm #editEndDate").addClass(
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

    // Checkbox select all dan update tombol hapus
    $("#period-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateDeleteButtonState();
            var totalCheckbox = $(
                "#period-table tbody input[type='checkbox'].select-row"
            ).length;
            var checkedCheckbox = $(
                "#period-table tbody input[type='checkbox'].select-row:checked"
            ).length;
            $("#select-all").prop(
                "checked",
                totalCheckbox > 0 && totalCheckbox === checkedCheckbox
            );
        }
    );
    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#period-table tbody input[type="checkbox"].select-row').prop(
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

    // Active period
    $(document).on("click", ".period-inactive", function (e) {
        e.preventDefault();
        const periodId = $(this).data("id");
        const periodName = $(this).data("name");

        Swal.fire({
            title: "Apakah anda yakin menjadikan periode ini aktif?",
            text: `Periode: ${periodName}`,
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Batal`,
            confirmButtonText: "Aktifkan",
            confirmButtonColor: "#16C7F9",
            cancelButtonColor: "#FC4438",
            confirmButtonText: "Aktifkan",
            imageUrl: "../assets/images/gif/dashboard-8/successful.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                const activeBtn = $(this);
                activeBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: `/periode/active/${periodId}`,
                    method: "POST",
                    success: async function (response) {
                        if (response.success) {
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(
                                response.message
                            );
                            toast.show();
                            t.clearPipeline().draw();
                        }
                    },
                    error: function (xhr, status, error) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON.message
                        );
                        toast.show();
                    },
                    complete: function () {
                        activeBtn.prop("disabled", false);
                    },
                });
            } else if (result.isDenied) {
            }
        });
    });
});
