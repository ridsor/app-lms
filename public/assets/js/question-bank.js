let t;

$(function () {
    if ($("#question-bank-table").length) {
        let columns = [
            {
                data: "id",
                name: "question-banks.id",
                orderable: false,
                searchable: false,
                width: "50px",
                className: "text-center",
            },
            { data: "Judul", name: "title" },
            { data: "Mata Pelajaran", name: "subject_name", searchable: false },
            {
                data: "Soal",
                name: "question",
                searchable: false,
                orderable: false,
            },
            { data: "Waktu", name: "created_at", searchable: false },
            { data: "", name: "", orderable: false, searchable: false },
        ];

        // filter
        const params = new URLSearchParams(window.location.search);
        if (params.get("mata_pelajaran")) {
            $("#subject-filter").val(params.get("mata_pelajaran"));
        }

        t = $("#question-bank-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: window.location.pathname,
                pages: 5,
                data: function (d) {
                    let filterParams = getQueryParams();
                    $.extend(d, filterParams);
                },
            }),
            columns: columns,
            language: {
                sProcessing: "Sedang memproses...",
                sZeroRecords: "Tidak ditemukan data yang sesuai",
                sInfo: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                sInfoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                sEmptyTable: "Tidak ada data di tabel",
                sSearch: "Cari:",
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            autoWidth: false,
            searchable: true,
            searching: true,
            searchDelay: 300,
            order: [],
        });
    }
});

$(document).ready(function () {
    $("#filter-btn").click(function (e) {
        e.preventDefault();
        // Buat query string
        const params = new URLSearchParams();
        if ($("#subject-filter").val())
            params.append("mata_pelajaran", $("#subject-filter").val());
        // Update URL tanpa reload
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);
        // Refresh datatable
        t.clearPipeline().draw();
    });

    t.on("draw", function () {
        $("#select-all").prop("checked", false);
        $("#question-bank-action-buttons").css("display", "none");
    });

    $("#addQuestionBankForm").on("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $("#addQuestionBankForm")
            .find("input, select, textarea")
            .removeClass("is-invalid");
        $("#addQuestionBankForm").find(".invalid-feedback").text("");

        const submitBtn = $("#addStudentSubmitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        $.ajax({
            url: "/bank-soal",
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
                    $("#addQuestionBankModal").modal("hide");
                    $("#addQuestionBankForm")[0].reset();
                    $("#addQuestionBankForm .selectpicker").selectpicker(
                        "refresh"
                    );
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        if (
                            $(
                                "#addQuestionBankForm [name='" + key + "']"
                            ).hasClass("selectpicker")
                        ) {
                            $("#addQuestionBankForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addQuestionBankForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }
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

    $("#question-bank-table").on("click", ".edit", function (e) {
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
            url: `/bank-soal/${id}/edit`,
            method: "GET",
            success: function (res) {
                if (res.success && res.data) {
                    $("#editQuestionBankForm").attr("data-id", id);
                    $("#editQuestionBankModal").modal("show");

                    $("#editQuestionBankForm [name='title']").val(
                        res.data.title
                    );
                    $("#editQuestionBankForm [name='subject_id']").val(
                        res.data.subject_id
                    );
                    $("#editQuestionBankForm [name='description']").val(
                        res.data.description
                    );
                    $("#editQuestionBankForm .selectpicker").selectpicker(
                        "refresh"
                    );
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

    $("#question-bank-table").on("click", ".trash", function (e) {
        e.preventDefault();
        const trashBtn = $(this);
        var id = trashBtn.attr("data-id");
        if (!id) return;
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Tindakan ini tidak dapat dibatalkan.",
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
                const originalHtml = trashBtn.html();
                trashBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );
                $.ajax({
                    url: "/bank-soal/" + id,
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
});
