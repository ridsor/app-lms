$(function () {
    var t = $("#major-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/jurusan",
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
                data: "Waktu",
                name: "created_at",
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
        autoWidth: true,
        searchable: true,
        order: [[2, "desc"]],
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $('#major-table tbody input[type="checkbox"].select-row:checked').each(
            function () {
                selectedIds.push($(this).val());
            }
        );
        if (selectedIds.length === 0) return;

        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Data jurusan yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/jurusan/hapus",
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

    // Event handler tombol tambah
    $("#addMajorForm").on("submit", function (e) {
        e.preventDefault();
        $("#addMajorForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addMajorForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addMajorSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: "/jurusan",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    t.draw();
                    $("#addMajorModal").modal("hide");
                    $("#addMajorForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#majorName")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#majorName").addClass("is-invalid");
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

    // Event handler tombol edit
    $("#major-table").on("click", ".edit", function (e) {
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
            url: "/jurusan/" + id,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editMajorForm #majorName").val(res.data.name);
                    $("#editMajorForm").attr("data-id", id);
                    $("#editMajorModal").modal("show");
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

    // Submit update jurusan
    $("#editMajorForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var formData = new FormData(this);
        $("#editMajorForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editMajorForm").find(".invalid-feedback").text("");
        const submitBtn = $("#editMajorSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        $.ajax({
            url: "/jurusan/" + id,
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
                    $("#editMajorModal").modal("hide");
                    $("#editMajorForm")[0].reset();
                    $("#major-table").DataTable().ajax.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#editMajorForm #majorName")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#editMajorForm #majorName").addClass("is-invalid");
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

    // Hapus satuan
    $("#major-table").on("click", ".trash", function (e) {
        e.preventDefault();
        const trashBtn = $(this);
        var id = trashBtn.attr("data-id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data jurusan yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/jurusan/" + id,
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

    $("#major-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateDeleteButtonState();
            var totalCheckbox = $(
                "#major-table tbody input[type='checkbox'].select-row"
            ).length;
            var checkedCheckbox = $(
                "#major-table tbody input[type='checkbox'].select-row:checked"
            ).length;
            $("#select-all").prop(
                "checked",
                totalCheckbox > 0 && totalCheckbox === checkedCheckbox
            );
        }
    );

    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#major-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateDeleteButtonState();
    });

    function updateDeleteButtonState() {
        var selectedRows = $(
            "#major-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#delete-selected").prop("disabled", selectedRows === 0);
        $("#delete-selected")
            .parent()
            .css("display", selectedRows > 0 ? "block" : "none");
    }
});
