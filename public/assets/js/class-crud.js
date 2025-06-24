$(function () {
    var t = $("#class-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/kelas",
            data: function (d) {
                d.level = $("#level-filter").val();
                d.major = $("#major-filter").val();
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
                data: "Tingkat",
                name: "level",
            },
            {
                data: "Jurusan",
                name: "major",
            },
            {
                data: "Kapasitas",
                name: "capacity",
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
        // columnDefs: [
        //     {
        //         orderable: false,
        //         render: $.fn.dataTable.render.select(),
        //         targets: 0,
        //     },
        // ],
        // select: {
        //     style: "multi",
        //     selector: "td:first-child",
        // },
        fixedColumns: {
            leftColumns: 2,
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [5, 10, 50, 100],
        responsive: true,
        autoWidth: true,
        searchable: true,
        order: [[6, "desc"]],
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $('#class-table tbody input[type="checkbox"].select-row:checked').each(
            function () {
                selectedIds.push($(this).val());
            }
        );
        if (selectedIds.length === 0) return;

        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Data kelas yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/kelas/hapus",
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

    $("#class-table").on("click", ".trash", function (e) {
        e.preventDefault();
        var row = $(this).closest("tr");
        var id = row.attr("id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data kelas yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/kelas/" + id,
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
                });
            }
        });
    });

    $("#addClassForm").on("submit", function (e) {
        e.preventDefault();

        $("#addClassForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addClassForm").find(".invalid-feedback").text("");

        const submitBtn = $("#addClassSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        const formData = new FormData(this);

        $.ajax({
            url: "/kelas",
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
                    $("#addClassModal").modal("hide");
                    $("#addClassForm")[0].reset();
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
                    if (errors.name) {
                        $("#className")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#className").addClass("is-invalid");
                    }
                    if (errors.level) {
                        $("#classLevel")
                            .next(".invalid-feedback")
                            .text(errors.level[0]);
                        $("#classLevel").addClass("is-invalid");
                    }
                    if (errors.major_id) {
                        $("#classMajor")
                            .next(".invalid-feedback")
                            .text(errors.major_id[0]);
                        $("#classMajor").addClass("is-invalid");
                    }
                    if (errors.capacity) {
                        $("#classCapacity")
                            .next(".invalid-feedback")
                            .text(errors.capacity[0]);
                        $("#classCapacity").addClass("is-invalid");
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

    $("#class-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateDeleteButtonState();

            // Hitung jumlah checkbox baris yang dicentang
            var totalCheckbox = $(
                "#class-table tbody input[type='checkbox'].select-row"
            ).length;
            var checkedCheckbox = $(
                "#class-table tbody input[type='checkbox'].select-row:checked"
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
        $('#class-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        updateDeleteButtonState();
    });

    function updateDeleteButtonState() {
        var selectedRows = $(
            "#class-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#delete-selected").prop("disabled", selectedRows === 0);
        $("#delete-selected")
            .parent()
            .css("display", selectedRows > 0 ? "block" : "none");
    }

    // Event handler tombol edit
    $("#class-table").on("click", ".edit", function (e) {
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
            url: "/kelas/" + id,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editClassForm #className").val(res.data.name);
                    $("#editClassForm #classLevel").val(res.data.level);
                    $("#editClassForm #classMajor").val(res.data.major_id);
                    $("#editClassForm #classCapacity").val(res.data.capacity);
                    $("#editClassForm").attr("data-id", id);
                    $("#editClassModal").modal("show");
                }
            },
            error: function () {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(
                    xhr.responseJSON.message
                );
                toast.show();
            },
            complete: function () {
                editBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });

    // Submit update kelas
    $("#editClassForm").on("submit", function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var formData = new FormData(this);

        $("#editClassForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editClassForm").find(".invalid-feedback").text("");

        const submitBtn = $("#editClassSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        $.ajax({
            url: "/kelas/" + id,
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
                    $("#editClassModal").modal("hide");
                    $("#editClassForm")[0].reset();
                    $("#class-table").DataTable().ajax.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#editClassForm #className")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#editClassForm #className").addClass("is-invalid");
                    }
                    if (errors.level) {
                        $("#editClassForm #classLevel")
                            .next(".invalid-feedback")
                            .text(errors.level[0]);
                        $("#editClassForm #classLevel").addClass("is-invalid");
                    }
                    if (errors.major_id) {
                        $("#editClassForm #classMajor")
                            .next(".invalid-feedback")
                            .text(errors.major_id[0]);
                        $("#editClassForm #classMajor").addClass("is-invalid");
                    }
                    if (errors.capacity) {
                        $("#editClassForm #classCapacity")
                            .next(".invalid-feedback")
                            .text(errors.capacity[0]);
                        $("#editClassForm #classCapacity").addClass(
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
