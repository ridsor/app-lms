$(function () {
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
    ];
    if (hasMajor) {
        columns.push({
            data: "major_name",
            name: "major_name",
            defaultContent: "-",
        });
    }
    columns = columns.concat([
        { data: "name", name: "name" },
        { data: "level", name: "level" },
        { data: "Aksi", name: "Aksi", orderable: false, searchable: false },
    ]);

    // filter
    const params = new URLSearchParams(window.location.search);
    if (params.get("major")) {
        $("#major-filter").val(params.get("major"));
    }
    if (params.get("level")) {
        $("#level-filter").val(params.get("level"));
    }
    if (params.get("class")) {
        $("#class-filter").val(params.get("class"));
    }

    var t = $("#attendance-classlist-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: window.location.pathname,
            pages: 5,
            data: function (d) {
                let filterParams = {};
                if ($("#major-filter").val())
                    filterParams.major = $("#major-filter").val();
                if ($("#level-filter").val())
                    filterParams.level = $("#level-filter").val();
                if ($("#class-filter").val())
                    filterParams.class = $("#class-filter").val();
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
        fixedColumns: {
            leftColumns: 2,
        },
        scrollCollapse: true,
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
        if ($("#major-filter").val())
            params.append("major", $("#major-filter").val());
        if ($("#level-filter").val())
            params.append("level", $("#level-filter").val());
        if ($("#class-filter").val())
            params.append("class", $("#class-filter").val());
        // Update URL tanpa reload
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);
        // Refresh datatable
        t.clearPipeline().draw();
    });
});
