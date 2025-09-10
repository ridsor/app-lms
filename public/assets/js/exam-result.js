let t;

$(function () {
    t = $("#exam-result-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: window.location.pathname,
            pages: 5,
        }),
        columns: [
            {
                data: null,
                name: "No",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                className: "text-center",
                width: "40px",
            },
            {
                data: "Nama",
                name: "students.name",
                searchable: false,
            },
            {
                data: "Pengerjaan",
                name: "task_submissions.submitted_at",
                searchable: false,
            },
            {
                data: "Nilai",
                name: "task_submissions.score",
                searchable: false,
            },
            {
                data: "",
                name: "",
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
            sSearch: "Cari:",
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchDelay: 300,
        order: [],
    });

    $("#globalSearch").on("keyup", function () {
        t.search(this.value).clearPipeline().draw();
    });
});

$(document).ready(function () {
    $("#reset-all").on("click", function () {
        let examId = $(this).data("id");
        const originalHtml = $(this).html();
        const btnSubmit = $(this);

        Swal.fire({
            title: "Reset Semua Hasil Ujian",
            text: "Apakah Anda yakin ingin mereset semua hasil ujian?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Reset Semua!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                btnSubmit
                    .prop("disabled", true)
                    .html(
                        '<i class="fa-solid fa-arrows-rotate fa-spin"></i> Loading...'
                    );
                $.ajax({
                    url: `/ujian/${examId}/hasil/reset`,
                    method: "PATCH",
                    success: function (response) {
                        showToast("success", response.message);
                        t.clearPipeline().draw();
                    },
                    error: function (xhr) {
                        showToast(
                            "error",
                            xhr.responseJSON?.message || "Terjadi kesalahan."
                        );
                    },
                    complete: function () {
                        btnSubmit.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });

    $("#export-excel").on("submit", function (e) {
        e.preventDefault();

        const btnSubmit = $(this).find("button[type='submit']");
        const originalHtml = btnSubmit.html();

        btnSubmit
            .prop("disabled", true)
            .html(
                '<i class="fa-solid fa-arrows-rotate fa-spin"></i> Loading...'
            );

        this.submit();

        setTimeout(function () {
            btnSubmit.prop("disabled", false).html(originalHtml);
        }, 3000);
    });

    $("#exam-result-table").on("click", ".reset-result", function () {
        let id = $(this).data("id");
        let exam_id = $(this).data("exam-id");
        const originalHtml = $(this).html();
        const btnSubmit = $(this);

        Swal.fire({
            title: "Reset Hasil Ujian",
            text: "Apakah Anda yakin ingin mereset hasil ujian?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Reset!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                btnSubmit
                    .prop("disabled", true)
                    .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');
                $.ajax({
                    url: `/ujian/${exam_id}/hasil/${id}/reset`,
                    method: "PATCH",
                    success: function (response) {
                        showToast("success", response.message);
                        t.clearPipeline().draw();
                    },
                    error: function (xhr) {
                        showToast(
                            "error",
                            xhr.responseJSON?.message || "Terjadi kesalahan."
                        );
                    },
                    complete: function () {
                        btnSubmit.prop("disabled", false).html(originalHtml);
                    },
                });
            }
        });
    });
});
