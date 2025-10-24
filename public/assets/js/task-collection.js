let t;

$(function () {
    t = $("#task-collection-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: `/jadwal/pertemuan/tugas/${task_id}/pengumpulan`,
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
                data: "Pengumpulan",
                name: "task_submissions.submitted_at",
                searchable: false,
            },
            {
                data: "Nilai",
                name: "task_submissions.score",
                searchable: false,
            },
            {
                data: "Penilaian",
                name: "task_submissions.graded_at",
                searchable: false,
            },
            {
                data: "Penilai",
                name: "graded_by",
                searchable: false,
                orderable: false,
            },
            {
                data: null,
                name: "",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    const html = `
                    <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                        <a class="square-white view rounded-2" href=${
                            "/jadwal/pertemuan/tugas/" +
                            data.task_id +
                            "/penilaian/" +
                            (meta.row + meta.settings._iDisplayStart + 1)
                        }>
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </div>
                    `;
                    return html;
                },
                className: "text-center",
                width: "40px",
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

    $("#displayedValue").on("change", function () {
        const task_id = $(this).data("id");

        $.ajax({
            url: `/jadwal/pertemuan/tugas/${task_id}/tampilkan_nilai`,
            method: "PATCH",
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(res.message);
                    toast.show();
                }
            },
            error: function (xhr) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON?.message);
                toast.show();
            },
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
});
