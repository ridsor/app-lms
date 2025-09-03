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
                data: null,
                name: "",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    const html = `
                    <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                        <a class="square-white view rounded-2" href=${
                            "/jadwal/pertemuan/tugas/1/penilaian/" +
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
});
