$(function () {
    if ($("#journal-schedule-by-class-table").length) {
        let columns = [
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
            { data: "Mata Pelajaran", name: "subject_name" },
            { data: "Pengajar", name: "teacher_name", searchable: false },
            { data: "", name: "", orderable: false, searchable: false },
        ];

        // filter
        const params = new URLSearchParams(window.location.search);
        if (params.get("guru")) {
            $("#teacher-filter").val(params.get("guru"));
        }
        if (params.get("mata_pelajaran")) {
            $("#subject-filter").val(params.get("mata_pelajaran"));
        }

        var t = $("#journal-schedule-by-class-table").DataTable({
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

        $("#filter-btn").click(function (e) {
            e.preventDefault();
            // Buat query string
            const params = new URLSearchParams();
            if ($("#teacher-filter").val())
                params.append("guru", $("#teacher-filter").val());
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
    }
});
