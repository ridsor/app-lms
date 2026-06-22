$(function () {
    const t = $("#ukk-operator-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/operator-ukk",
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
            { data: "Username", name: "username" },
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
        order: [[3, "desc"]],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#ukk-operator-action-buttons").css("display", "none");
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#ukk-operator-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data operator yang terpilih akan dihapus secara permanen.",
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
                const deleteBtn = $("#delete-selected");
                const originalHtml = deleteBtn.html();
                deleteBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"> </span>'
                    );
                $.ajax({
                    url: "/operator-ukk/hapus",
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

    // Tambah operator
    $("#addUkkOperatorForm").on("submit", function (e) {
        e.preventDefault();
        $("#addUkkOperatorForm")
            .find("input")
            .removeClass("is-invalid");
        $("#addUkkOperatorForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addUkkOperatorSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: "/operator-ukk",
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
                    $("#addUkkOperatorModal").modal("hide");
                    $("#addUkkOperatorForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        $("#addUkkOperatorForm [name='" + key + "']")
                            .addClass("is-invalid")
                            .next(".invalid-feedback")
                            .text(errors[key][0]);
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

    // Edit operator
    $("#ukk-operator-table").on("click", ".edit", function (e) {
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
            url: `/operator-ukk/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editUkkOperatorForm [name='name']").val(res.data.name);
                    $("#editUkkOperatorForm [name='username']").val(res.data.username);
                    $("#editUkkOperatorForm [name='password']").val('');
                    $("#editUkkOperatorForm").attr("data-id", id);
                    $("#editUkkOperatorModal").modal("show");
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

    // Submit update operator
    $("#editUkkOperatorForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var formData = new FormData(this);
        $("#editUkkOperatorForm")
            .find("input")
            .removeClass("is-invalid");
        $("#editUkkOperatorForm").find(".invalid-feedback").text("");
        const submitBtn = $("#editUkkOperatorSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        $.ajax({
            url: `/operator-ukk/${id}`,
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
                    $("#editUkkOperatorModal").modal("hide");
                    $("#editUkkOperatorForm")[0].reset();
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        $("#editUkkOperatorForm [name='" + key + "']")
                            .addClass("is-invalid")
                            .next(".invalid-feedback")
                            .text(errors[key][0]);
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

    // Individual delete
    $("#ukk-operator-table").on("click", ".trash", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Operator ini akan dihapus secara permanen.",
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
                $.ajax({
                    url: `/operator-ukk/${id}`,
                    method: "DELETE",
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
                });
            }
        });
    });

    // Select all
    $("#ukk-operator-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateActionState();
        }
    );
    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#ukk-operator-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateActionState();
    });
    function updateActionState() {
        var selectedRows = $(
            "#ukk-operator-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#selected-count").text(selectedRows);
        $("#ukk-operator-action-buttons").css(
            "display",
            selectedRows > 0 ? "flex" : "none"
        );
        var totalCheckbox = $(
            "#ukk-operator-table tbody input[type='checkbox'].select-row"
        ).length;
        $("#select-all").prop("checked", totalCheckbox === selectedRows);
    }
});
