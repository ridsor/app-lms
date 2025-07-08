$(function () {
    const t = $("#teacher-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/guru",
            pages: 5,
            data: function (d) {
                // No filter for now
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
            { data: "Nama", name: "name" },
            { data: "NIP", name: "nip" },
            {
                data: "Spesialisasi",
                name: "specialization",
                searchable: false,
            },
            { data: "Waktu", name: "created_at", searchable: false },
            { data: "Aksi", name: "Aksi", orderable: false, searchable: false },
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
            select: { rows: { _: "%d baris terpilih", 0: "" } },
        },
        scrollCollapse: true,
        pageLength: 10,
        searchDelay: 300,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchable: true,
        order: [[4, "desc"]],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#teacher-action-buttons").css("display", "none");
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#teacher-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;
        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Data guru yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/guru/hapus",
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

    // Tambah guru
    $("#addTeacherForm").on("submit", function (e) {
        e.preventDefault();
        $("#addTeacherForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addTeacherForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addTeacherSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: "/guru",
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
                    $("#addTeacherModal").modal("hide");
                    $("#addTeacherForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $("#addTeacherForm [name='" + key + "']")
                        .addClass("is-invalid")
                        .next(".invalid-feedback")
                        .text(errors[key][0]);
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

    // Edit guru
    $("#teacher-table").on("click", ".edit", function (e) {
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
            url: `/guru/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editTeacherForm [name='name']").val(res.data.name);
                    $("#editTeacherForm [name='nip']").val(res.data.nip);
                    $("#editTeacherForm [name='specialization']").val(
                        res.data.specialization
                    );
                    $("#editTeacherForm [name='date_of_birth']").val(
                        res.data.date_of_birth
                    );
                    $("#editTeacherForm [name='birthplace']").val(
                        res.data.birthplace
                    );
                    $("#editTeacherForm [name='religion']").val(
                        res.data.religion
                    );
                    $("#editTeacherForm [name='gender']").val(res.data.gender);
                    $("#editTeacherForm").attr("data-id", id);
                    $("#editTeacherModal").modal("show");
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

    // Submit update guru
    $("#editTeacherForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var formData = new FormData(this);
        $("#editTeacherForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editTeacherForm").find(".invalid-feedback").text("");
        const submitBtn = $("#editTeacherSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        $.ajax({
            url: `/guru/${id}`,
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
                    $("#editTeacherModal").modal("hide");
                    $("#editTeacherForm")[0].reset();
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $("#editTeacherForm [name='" + key + "']")
                        .addClass("is-invalid")
                        .next(".invalid-feedback")
                        .text(errors[key][0]);
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

    // Bulk Edit
    $("#bulk-edit-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#teacher-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;
        // Reset form
        $("#bulkEditTeacherForm")[0].reset();
        $("#bulkEditTeacherModal").modal("show");
        $("#bulkEditTeacherForm").data("ids", selectedIds);
    });

    // Select all
    $("#teacher-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateActionState();
        }
    );
    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#teacher-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateActionState();
    });
    function updateActionState() {
        var selectedRows = $(
            "#teacher-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#selected-count").text(selectedRows);
        $("#teacher-action-buttons").css(
            "display",
            selectedRows > 0 ? "flex" : "none"
        );
        var totalCheckbox = $(
            "#teacher-table tbody input[type='checkbox'].select-row"
        ).length;
        $("#select-all").prop("checked", totalCheckbox === selectedRows);
    }

    // Event handler tombol view
    $("#teacher-table").on("click", ".view", function (e) {
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
        $("#viewTeacherModal .form-control-plaintext").text("");
        $("#viewTeacherModal").modal("show");
        // Show loading state
        $("#viewTeacherModal .modal-body")
            .addClass("position-relative")
            .append(
                '<div id="viewTeacherLoading" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:10;display:flex;align-items:center;justify-content:center;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );
        $.ajax({
            url: `/guru/${id}`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#viewTeacherName").text(res.data.name || "-");
                    $("#viewTeacherNip").text(res.data.nip || "-");
                    $("#viewTeacherSpecialization").text(
                        res.data.specialization || "-"
                    );
                    $("#viewTeacherBirthplace").text(
                        res.data.birthplace || "-"
                    );
                    $("#viewTeacherDateOfBirth").text(
                        res.data.date_of_birth || "-"
                    );
                    let genderLabel = "-";
                    if (res.data.gender === "M") genderLabel = "Laki-laki";
                    else if (res.data.gender === "F") genderLabel = "Perempuan";
                    $("#viewTeacherGender").text(genderLabel);
                    $("#viewTeacherReligion").text(res.data.religion || "-");
                    $("#viewTeacherCreatedAt").text(res.data.created_at || "-");
                }
            },
            error: function (xhr) {
                $("#viewTeacherModal").modal("hide");
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON?.message || "Gagal mengambil detail guru"
                );
                toast.show();
            },
            complete: function () {
                $("#viewTeacherLoading").remove();
                viewBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });
});
