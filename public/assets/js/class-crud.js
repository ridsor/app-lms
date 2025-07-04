$(function () {
    // filter
    const params = getQueryParams();
    if (params.major) {
        $("#major-filter").val(params.major);
    }
    if (params.level) {
        $("#level-filter").val(params.level);
    }

    var t = $("#class-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/kelas",
            pages: 5,
            data: function (d) {
                let filterParams = getQueryParams();
                $.extend(d, filterParams);
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
                data: "Nama",
                name: "name",
            },
            {
                data: "Tingkat",
                name: "level",
                searchable: false,
            },
            {
                data: "Jurusan",
                name: "majors.name",
                searchable: false,
            },
            {
                data: "Wali Kelas",
                name: "teachers.name",
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
        searchDelay: 300,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchable: true,
        order: [],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#delete-selected").prop("disabled", true);
        $("#delete-selected").parent().css("display", "none");
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
                    url: "/kelas/hapus",
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
        if ($("#level-filter").val())
            params.append("level", $("#level-filter").val());

        // Update URL tanpa reload
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);

        // Refresh datatable
        t.clearPipeline().draw();
    });

    $("#class-table").on("click", ".trash", function (e) {
        e.preventDefault();
        const trashBtn = $(this);
        var id = trashBtn.attr("data-id");
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
            imageUrl: "/assets/images/gif/trash.gif",
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
                    url: "/kelas/" + id,
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
                    t.clearPipeline().draw();
                    $("#addClassModal").modal("hide");
                    $("#addClassForm")[0].reset();
                    $("#addClassForm .selectpicker").selectpicker("refresh");
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
                    if (errors.name) {
                        $("#addClassForm [name='name']")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#addClassForm [name='name']").addClass("is-invalid");
                    }
                    if (errors.level) {
                        $("#addClassForm [name='level']")
                            .next(".invalid-feedback")
                            .text(errors.level[0]);
                        $("#addClassForm [name='level']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.major_id) {
                        $("#addClassForm [name='major_id']")
                            .next(".invalid-feedback")
                            .text(errors.major_id[0]);
                        $("#addClassForm [name='major_id']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.homeroom_teacher_id) {
                        $("#addClassForm [name='homeroom_teacher_id']")
                            .parent()
                            .next(".invalid-feedback")
                            .text(errors.homeroom_teacher_id[0]);
                        $("#addClassForm [name='homeroom_teacher_id']")
                            .parent()
                            .addClass("is-invalid");
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
            $("#delete-selected-count").text(checkedCheckbox);
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
        $("#delete-selected-count").text(
            $("#class-table tbody input[type='checkbox'].select-row:checked")
                .length
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
            url: `/kelas/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editClassForm [name='name']").val(res.data.name);
                    $("#editClassForm [name='level']").val(res.data.level);
                    $("#editClassForm [name='major_id']").val(
                        res.data.major_id
                    );
                    $("#editClassForm [name='homeroom_teacher_id']").val(
                        res.data.homeroom_teacher_id
                    );
                    $(
                        "#editClassForm [name='homeroom_teacher_id']"
                    ).selectpicker("refresh");
                    $("#editClassForm").attr("data-id", id);
                    $("#editClassModal").modal("show");
                }
            },
            error: function () {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
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
                    $("#editClassForm .selectpicker").selectpicker("refresh");
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#editClassForm [name='name']")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#editClassForm [name='name']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.level) {
                        $("#editClassForm [name='level']")
                            .next(".invalid-feedback")
                            .text(errors.level[0]);
                        $("#editClassForm [name='level']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.major_id) {
                        $("#editClassForm [name='major_id']")
                            .next(".invalid-feedback")
                            .text(errors.major_id[0]);
                        $("#editClassForm [name='major_id']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.homeroom_teacher_id) {
                        $("#editClassForm [name='homeroom_teacher_id']")
                            .parent()
                            .next(".invalid-feedback")
                            .text(errors.homeroom_teacher_id[0]);
                        $("#editClassForm [name='homeroom_teacher_id']")
                            .parent()
                            .addClass("is-invalid");
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
