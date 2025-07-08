$(function () {
    if ($("#attendance-schedule-by-class-table").length) {
        let columns = [
            { data: "Mata Pelajaran", name: "subject_name" },
            { data: "Guru Pengajar", name: "teacher_name", searchable: false },
            { data: "Aksi", name: "Aksi", orderable: false, searchable: false },
        ];

        // filter
        const params = new URLSearchParams(window.location.search);
        if (params.get("guru")) {
            $("#teacher-filter").val(params.get("guru"));
        }

        var t = $("#attendance-schedule-by-class-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: "/kehadiran/kelas/" + window.classId,
                pages: 5,
                data: function (d) {
                    let filterParams = {};
                    if ($("#teacher-filter").val())
                        filterParams.guru = $("#teacher-filter").val();
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

        $("#filter-btn").click(function (e) {
            e.preventDefault();
            // Buat query string
            const params = new URLSearchParams();
            if ($("#teacher-filter").val())
                params.append("guru", $("#teacher-filter").val());
            // Update URL tanpa reload
            const newUrl =
                window.location.pathname +
                (params.toString() ? "?" + params.toString() : "");
            window.history.replaceState({}, "", newUrl);
            // Refresh datatable
            t.clearPipeline().draw();
        });
    }
});
