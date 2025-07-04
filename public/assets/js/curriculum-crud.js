$(function () {
    // Inisialisasi Quill untuk tambah
    var addQuill = new Quill("#addDescriptionEditor", {
        theme: "snow",
        modules: { toolbar: "#toolbar9" },
        placeholder: "Deskripsi Kurikulum",
    });

    // Inisialisasi Quill untuk edit
    var editQuill = new Quill("#editDescriptionEditor", {
        theme: "snow",
        modules: { toolbar: "#toolbar10" },
        placeholder: "Deskripsi Kurikulum",
    });

    var t = $("#curriculum-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/kurikulum",
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
                data: "Nama",
                name: "name",
                searchable: false,
            },
            {
                data: "Deskripsi",
                name: "description",
                searchable: false,
            },
            {
                data: "Mata Pelajaran",
                name: "subjects_count",
                searchable: false,
                orderable: false,
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
        searchDelay: 300,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchable: true,
        searching: true,
        order: [[5, "desc"]],
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#delete-selected").prop("disabled", true);
        $("#delete-selected").parent().css("display", "none");
    });

    // Hapus banyak
    $("#delete-selected").on("click", function () {
        var selectedIds = [];
        $(
            '#curriculum-table tbody input[type="checkbox"].select-row:checked'
        ).each(function () {
            selectedIds.push($(this).val());
        });
        if (selectedIds.length === 0) return;

        Swal.fire({
            title: "Apakah Anda yakin ingin?",
            text: "Data kurikulum yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/kurikulum/hapus",
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
        const params = new URLSearchParams();
        if ($("#search-curriculum").val())
            params.append("search", $("#search-curriculum").val());
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);
        t.clearPipeline().draw();
    });

    // Hapus satuan
    $("#curriculum-table").on("click", ".trash", function (e) {
        e.preventDefault();
        const trashBtn = $(this);
        var id = trashBtn.attr("data-id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data kurikulum yang terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.",
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
                    url: "/kurikulum/" + id,
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

    // Set value Quill ke input hidden sebelum submit tambah
    $("#addCurriculumForm").on("submit", function (e) {
        e.preventDefault();

        var descriptionHTML = addQuill.root.innerHTML;
        var descriptionText = addQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            descriptionHTML = "";
        }
        console.log(descriptionHTML);
        $("#addCurriculumForm [name='description']").val(descriptionHTML);

        $("#addCurriculumForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addCurriculumForm").find(".invalid-feedback").text("");
        const submitBtn = $("#addCurriculumSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: "/kurikulum",
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
                    $("#addCurriculumModal").modal("hide");
                    addQuill.root.innerHTML = "";
                    $("#addCurriculumForm")[0].reset();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#addCurriculumForm [name='name']")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#addCurriculumForm [name='name']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.description) {
                        $("#addCurriculumForm [name='description']")
                            .parent()
                            .next(".invalid-feedback")
                            .text(errors.description[0]);
                        $("#addCurriculumForm [name='description']")
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

    // Edit kurikulum (show modal dan isi data)
    $("#curriculum-table").on("click", ".edit", function (e) {
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
            url: `/kurikulum/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editCurriculumForm #editCurriculumId").val(res.data.id);
                    $("#editCurriculumForm #editName").val(res.data.name);
                    editQuill.root.innerHTML = res.data.description || "";
                    console.log(res.data.description);
                    $("#editCurriculumModal").modal("show");
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

    // Submit update kurikulum
    $("#editCurriculumForm").on("submit", function (e) {
        e.preventDefault();

        var descriptionHTML = editQuill.root.innerHTML;
        var descriptionText = editQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            descriptionHTML = "";
        }
        $("#editCurriculumForm [name='description']").val(descriptionHTML);

        var id = $(this).find("#editCurriculumId").val();
        var formData = new FormData(this);
        $("#editCurriculumForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#editCurriculumForm").find(".invalid-feedback").text("");
        const submitBtn = $("#editCurriculumSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        $.ajax({
            url: "/kurikulum/" + id,
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
                    $("#editCurriculumModal").modal("hide");
                    editQuill.root.innerHTML = "";
                    $("#editCurriculumForm")[0].reset();
                    t.clearPipeline().draw();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#editCurriculumForm [name='name']")
                            .next(".invalid-feedback")
                            .text(errors.name[0]);
                        $("#editCurriculumForm [name='name']").addClass(
                            "is-invalid"
                        );
                    }
                    if (errors.description) {
                        $("#editCurriculumForm [name='description']")
                            .parent()
                            .next(".invalid-feedback")
                            .text(errors.description[0]);
                        $("#editCurriculumForm [name='description']")
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

    // Checkbox select all dan update tombol hapus
    $("#curriculum-table tbody").on(
        "change",
        'input[type="checkbox"].select-row',
        function () {
            updateDeleteButtonState();
            var totalCheckbox = $(
                "#curriculum-table tbody input[type='checkbox'].select-row"
            ).length;
            var checkedCheckbox = $(
                "#curriculum-table tbody input[type='checkbox'].select-row:checked"
            ).length;
            $("#delete-selected-count").text(checkedCheckbox);
            $("#select-all").prop(
                "checked",
                totalCheckbox > 0 && totalCheckbox === checkedCheckbox
            );
        }
    );
    $("#select-all").on("click", function () {
        var checked = this.checked;
        $('#curriculum-table tbody input[type="checkbox"].select-row').prop(
            "checked",
            checked
        );
        $("#delete-selected-count").text(
            $(
                "#curriculum-table tbody input[type='checkbox'].select-row:checked"
            ).length
        );
        updateDeleteButtonState();
    });

    function updateDeleteButtonState() {
        var selectedRows = $(
            "#curriculum-table tbody input[type='checkbox'].select-row:checked"
        ).length;
        $("#delete-selected").prop("disabled", selectedRows === 0);
        $("#delete-selected")
            .parent()
            .css("display", selectedRows > 0 ? "block" : "none");
    }

    // Active curriculum
    $(document).on("click", ".curriculum-inactive", function (e) {
        e.preventDefault();
        const curriculumId = $(this).data("id");
        const curriculumName = $(this).data("name");

        Swal.fire({
            title: "Apakah anda yakin menjadikan kurikulum ini aktif?",
            text: `Kurikulum: ${curriculumName}`,
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
                    url: `/kurikulum/active/${curriculumId}`,
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
    $(document).on("click", ".curriculum-active", function (e) {
        e.preventDefault();
        const curriculumId = $(this).data("id");
        const curriculumName = $(this).data("name");

        Swal.fire({
            title: "Apakah anda yakin menjadikan kurikulum ini tidak aktif?",
            text: `Kurikulum: ${curriculumName}`,
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Batal`,
            confirmButtonText: "Tidak Aktifkan",
            confirmButtonColor: "#16C7F9",
            cancelButtonColor: "#FC4438",
            confirmButtonText: "Tidak Aktifkan",
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
                    url: `/kurikulum/active/${curriculumId}`,
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
