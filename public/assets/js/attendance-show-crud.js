$(function () {
    if ($("#attendance-show-table").length) {
        let scheduleId = window.location.pathname.split("/").pop();
        let columns = [
            { data: "student_name", name: "student_name" },
            { data: "status_label", name: "status_label" },
            { data: "Aksi", name: "Aksi", orderable: false, searchable: false },
        ];

        // filter
        const params = new URLSearchParams(window.location.search);
        if (params.get("student_name")) {
            $("#student-filter").val(params.get("student_name"));
        }
        if (params.get("status")) {
            $("#status-filter").val(params.get("status"));
        }

        var t = $("#attendance-show-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: window.location.pathname,
                pages: 5,
                data: function (d) {
                    let filterParams = {};
                    if ($("#student-filter").val())
                        filterParams.student_name = $("#student-filter").val();
                    if ($("#status-filter").val())
                        filterParams.status = $("#status-filter").val();
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
            searchable: false,
            searching: false,
            searchDelay: 300,
            order: [],
        });

        $("#filter-btn").click(function (e) {
            e.preventDefault();
            // Buat query string
            const params = new URLSearchParams();
            if ($("#student-filter").val())
                params.append("student_name", $("#student-filter").val());
            if ($("#status-filter").val())
                params.append("status", $("#status-filter").val());
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
