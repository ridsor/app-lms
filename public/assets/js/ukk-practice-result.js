$(document).ready(function () {
    const table = $("#ukk-practice-result-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            data: function (d) {
                d.search = $('#globalSearch').val();
            }
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                className: "text-center",
                width: "40px",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            { data: "Nama", name: "students.name" },
            { data: "Pengumpulan", name: "submitted_at" },
            { data: "Nilai", name: "score" },
            { data: "Penilaian", name: "graded_at" },
            { data: "Penilai", name: "grader.name" },
            {
                data: null,
                name: "",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    const html = `
                    <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                        <a class="square-white view rounded-2" href=${"/ukk/" +
                        data.ukk_id +
                        "/praktik/evaluasi/" +
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
        table.search(this.value).draw();
    });
});
