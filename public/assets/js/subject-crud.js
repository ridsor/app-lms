$(function () {
    const t = $("#subject-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: `/kurikulum/${curriculumId}/mata-pelajaran`,
            pages: 5,
            data: function (d) {},
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
                data: "Kode",
                name: "code",
            },
            {
                data: "Nama",
                name: "name",
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
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchDelay: 300,
        searchable: true,
        order: [[3, "desc"]],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#subject-action-buttons").css("display", "none");
    });

    // Tambah subject
    $("#addSubjectForm").on("submit", function (e) {
        e.preventDefault();
        $("#addSubjectForm").find("input, select").removeClass("is-invalid");
        $("#addSubjectForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addSubjectSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = $(this).serialize();
        $.ajax({
            url: `/kurikulum/${curriculumId}/mata-pelajaran`,
            method: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    t.clearPipeline().draw();
                    $("#addSubjectModal").modal("hide");
                    $("#addSubjectForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#addSubjectForm input[name='name']")
                            .addClass("is-invalid")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                    }
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            },
        });
    });

    // Edit subject (show modal)
    $("#subject-table").on("click", ".edit", function (e) {
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
            url: `/kurikulum/${curriculumId}/mata-pelajaran/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    console.log(res.data.name);
                    $("#editSubjectForm [name='name']").val(res.data.name);
                    $("#editSubjectForm").attr("data-id", id);
                    $("#editSubjectModal").modal("show");
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

    // Submit update subject
    $("#editSubjectForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        $("#editSubjectForm").find("input, select").removeClass("is-invalid");
        $("#editSubjectForm").find(".invalid-feedback").text("");
        const submitBtn = $("#editSubjectSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = $(this).serialize();
        $.ajax({
            url: `/kurikulum/${curriculumId}/mata-pelajaran/${id}`,
            method: "POST",
            data: formData,
            headers: { "X-HTTP-Method-Override": "PUT" },
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    t.clearPipeline().draw();
                    $("#editSubjectModal").modal("hide");
                    $("#editSubjectForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#editSubjectForm input[name='name']")
                            .addClass("is-invalid")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                    }
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalText);
            },
        });
    });

    // Hapus subject
    $("#subject-table").on("click", ".trash", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (!id) return;

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Mata pelajaran yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                const trashBtn = $(this);
                const originalHtml = trashBtn.html();
                trashBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: `/kurikulum/${curriculumId}/mata-pelajaran/${id}`,
                    method: "DELETE",
                    success: function (res) {
                        if (res.success) {
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(
                                "Mata pelajaran berhasil dihapus"
                            );
                            toast.show();
                            t.clearPipeline().draw();
                        }
                    },
                    complete: function () {
                        trashBtn.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });

    // Bulk Delete
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#subject-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;
        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Mata pelajaran yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                const deleteBtn = $(this);
                const originalHtml = deleteBtn.html();
                deleteBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: `/kurikulum/${curriculumId}/mata-pelajaran/hapus`,
                    method: "DELETE",
                    data: { ids: selectedIds },
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

    // Checkbox & Action State
    $("#subject-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateActionState();
        }
    );
    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#subject-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateActionState();
    });
    function updateActionState() {
        var selectedRows = $(
            "#subject-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#selected-count").text(selectedRows);
        $("#subject-action-buttons").css(
            "display",
            selectedRows > 0 ? "flex" : "none"
        );

        var totalCheckbox = $(
            "#subject-table tbody input[type='checkbox'].select-row"
        ).length;

        $("#select-all").prop("checked", totalCheckbox === selectedRows);
    }
});
